<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/18
 * Time: 11:33
 */
class StatisticsAction extends CommonAction
{
    const HIDE_DEP = 31;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->menucheck();

        $this->assign('hideDep', $this->checkUserDep());
        $this->display('index');
    }


    public function ajaxData()
    {
        $dailCount = $this->getData();

        $this->totalData($dailCount);

        sort($dailCount);

        if(count($dailCount) <= 0){
            $this->ajaxReturn($dailCount,'error',0);
            exit;
        }
        $this->ajaxReturn($dailCount,'success',1);
        exit;
    }

    public function getData()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $channel = I('request.channel','','intval');
        $realname = I('request.account','','trim');


        if(empty($startTime) || empty($endTime)){
            $startTime =date('Y-m-01');
            $endTime = date('Y-m-d');
        }

        if(strtotime($endTime)  >= mktime(0,0,0)){
            $endTime = date('Y-m-d', strtotime('-1 day', mktime(0,0,0)));
        }

        $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));

        !empty($channel) && $where['a.channelid'] = $channel;

        if(!empty($realname)){
            $where['b.channelbusiness'] = array(array('like', '%'.$realname.'%'));
            //$complex['b.realname'] = array(array('like', '%'.$realname.'%'));
            //$complex['_logic'] = 'OR';
            //$where['_complex'] = $complex;
        }

        //每个渠道流水
        $dailyaccountModel = M('TgDailyaccount');
        $dailCount = $dailyaccountModel->alias('a')
            ->join('left join ' . C('DB_PREFIX') . 'tg_user as b on a.userid=b.userid')
            ->where($where)
            ->field('a.userid,a.channelid,sum(a.dailyjournal) as sum_dailyjournal,b.realname,sum(a.newpeople) as sum_newpeople,sum(a.dailyincome) as sum_dailyincome,b.channelbusiness')
            ->group('a.userid')
            ->select();

        $where = array(
            'a.status' => 1,
            'a.create_time' =>  array(array('EGT',strtotime($startTime.' 00:00:00')), array('ELT', strtotime($endTime.' 23:59:59')))
        );

        //推广用户总流水
        !empty($channel) && $where['b.channelid'] = $channel;
        $data = M('')->table(C('DB_PREFIX') . 'all_pay as a')
            ->join("left join " . C('DB_PREFIX') . "tg_source as b on b.sourcesn=a.agent ")
            ->join("left join " . C('DB_PREFIX') . "tg_game as c on a.gameid=c.sdkgameid ")
            ->where($where)
            ->field('b.userid,
                    sum(
                        CASE
                        WHEN a.voucherje > 0 THEN
                            a.amount - a.voucherje
                        ELSE
                            a.amount
                        END
                    ) AS sum_amount,
                    sum(a.amount) as sum_all_amount,
                    sum(a.voucherje) AS sum_voucherje,
                    sum(
                        CASE
                        WHEN a.amount > 0 THEN
                            a.amount * ((1 - c.channelrate) * (1 - c.joinsharerate))
                        ELSE
                            0
                        END
                    ) AS cpamount')
            ->group('b.userid')
            ->select();
        $where = array(
            'a.create_time' =>  array(array('EGT',strtotime($startTime.' 00:00:00')), array('ELT', strtotime($endTime.' 23:59:59'))),
            'a.status' => 1,
        );
        $voucher = M('voucher_buy')->alias('a')
            ->join(C('DB_PREFIX')."tg_user b on a.buyer = b.account", "LEFT")
            ->where($where)->group('a.buyer')
            ->field('b.userid,a.buyer,sum(a.amount) as sum_amount')
            ->select();
        $itemVoucher = array();
        foreach($voucher as $v){
            $itemVoucher[$v['userid']] = $v;
        }
        $itemData = $arrUserid = array();
        foreach($data as $v){
            $itemData[$v['userid']] = $v;
        }

        foreach($dailCount as $v){
            $arrUserid[] = $v['userid'];
        }

       // $balancemodel = D('Balance');
        empty($dailCount) && $dailCount = array();
        foreach($dailCount as $key => &$value){
            $sumAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['sum_amount'] : 0;
            $cpAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['cpamount'] : 0;

            $yx_amount = (int)($sumAmount - $value['sum_dailyjournal']);
            $value['yx_amount'] = intval($yx_amount);
            $value['sum_voucherje'] = intval($itemData[$value['userid']]['sum_voucherje']);
            $value['sum_amount'] =  intval($itemData[$value['userid']]['sum_all_amount']);
            $value['timeZone'] = "{$startTime}至{$endTime}";
            //推广用户未提现金额
            /*$balance = $balancemodel->money($value['userid']);
            $value['unwithdraw'] = (int)$balance['unwithdraw'];*/
            $value['sum_dailyjournal'] = intval($value['sum_dailyjournal']);
            $value['sum_newpeople'] = (int)$value['sum_newpeople'];
            $value['sum_dailyincome'] = (int)$value['sum_dailyincome'];
            $value['sum_cpamount'] = (int)$cpAmount;
            $value['rate_amount'] = ($value['sum_amount'] * 0.025);//通道费
            $value['yx_earnings'] = (int)($value['sum_amount'] - $value['sum_cpamount'] - $value['sum_dailyincome'] - $value['sum_voucherje'] + $itemVoucher[$value['userid']]['sum_amount']);
            $value['buyer_voucher'] = isset($itemVoucher[$value['userid']]['sum_amount']) ? (int)$itemVoucher[$value['userid']]['sum_amount'] : 0;

            if($value['sum_amount'] <= 0 && $value['sum_newpeople'] <= 0){
                unset($dailCount[$key]);
            }
            //$value['buyer_voucher'] = isset($itemVoucher[$value['userid']]) ? (int)$itemVoucher[$value['userid']]['sum_amount'] : 0;
            //sum(b.dailyjournal*(1-a.channelrate)*(1-a.sharerate)) as sum_cpamount
            /*if($value['sum_dailyjournal'] <= 0 && $value['yx_amount'] <= 0){
                unset($dailCount[$key]);
            }*/

            //合度
        }

        return $dailCount;
    }


    public function export()
    {
        $dailCount = $this->getData();
        if(empty($dailCount)){
           $this->error('数据获取失败，没有符合条件的数据');
        }

        $this->totalData($dailCount);

        $check = false;
        if($this->checkUserDep()){
            $check = true;
        }
        $title['timeZone'] = '日期';
        $title['channelbusiness'] = '部门';
        $title['realname'] = '客户名称';
        $title['sum_newpeople'] = '注册数';
        $check && $title['sum_cpamount'] = 'CP结算';
        $title['sum_voucherje'] = '优惠券';
        $title['sum_dailyincome'] = '渠道收益';
        $title['buyer_voucher'] = '购买代金券';
        $check && $title['yx_amount'] = '官方流水';
        $title['sum_amount'] = '总充值';
        $check && $title['yx_earnings'] = '收益';
        $this->exportFile($title, $dailCount);
    }

    /**
     * @param $dailCount
     * @return array
     */
    protected function totalData(&$dailCount)
    {
        $sum_newpeople = $sum_cpamount = $sum_voucherje = $sum_dailyincome = $yx_amount = $sum_amount = $yx_earnings = $buyer_voucher =  0;
        foreach($dailCount as $v3){
            $sum_newpeople += $v3['sum_newpeople'];
            $sum_cpamount += $v3['sum_cpamount'];
            $sum_voucherje += $v3['sum_voucherje'];
            $sum_dailyincome += $v3['sum_dailyincome'];
            $yx_amount += $v3['yx_amount'];
            $sum_amount += $v3['sum_amount'];
            $yx_earnings += $v3['yx_earnings'];
            $buyer_voucher += $v3['buyer_voucher'];
        }
        $dailCount[] = array(
            'timeZone' => '总计：',
            'realname' => '',
            'sum_newpeople' => $sum_newpeople,
            'sum_cpamount' => $sum_cpamount,
            'sum_voucherje' => $sum_voucherje,
            'sum_dailyincome' => $sum_dailyincome,
            'yx_amount' => $yx_amount,
            'sum_amount' => $sum_amount,
            'yx_earnings' => $yx_earnings,
            'buyer_voucher' => $buyer_voucher
        );
    }


    /**
     * 导出数据
     * @param array $title 第一行显示的数据 array('pid'==>'父id','sex'=>'性别');
     * @param array $data
     * @param string $outputFileName 要保存的文件名
     * @param string $sheetTitle 工作表名称
     */
    public function exportFile(array $title, array $data,$outputFileName='')
    {
        ob_clean();
        if (empty($outputFileName)){
            $outputFileName = date('Y-m-d').'-'.md5(time()).'.xls';
        }
        include_once dirname(__FILE__) . "/../../ORG/PHPExcel.php";
        include_once dirname(__FILE__) . "/../../ORG/PHPExcel/IOFactory.php";
        $objExcel = new PHPExcel();
        $path = pathinfo($outputFileName);
        if($path['extension'] == 'xlsx'){
            Vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
        }else{
            Vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
        }

        //设置活动的工作表
        $objExcel->setActiveSheetIndex(0);
        //获取活动的表对象
        $objActSheet = $objExcel->getActiveSheet();
        //设置工作表的名称
        $objActSheet->setTitle('sheet0');
        //为第一行赋值
        $i = 0;
        foreach ($title as $key=>$value)
        {
            $index = 65+$i;
            $objActSheet->setCellValue(chr($index).'1', $value);
            $i++;
        }
        $arrKeys = array_keys($title);
        $data = array_values($data);
        foreach ($data as $key=>$value)
        {
            for ($m=0;$m<=$i;$m++)
            {
                $objActSheet->setCellValueExplicit(chr($m+65).($key+2),$value[$arrKeys[$m]],\PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
        $today = date('Y/m/d/');
        $save_path = 'upfiles/exportBalance/' . $today;
        Dir::create_dir_auto($save_path);
        // 文件名
        $file_name = md5(time());
        $file_all_path = $save_path . $file_name . '.xlsx';

        $objWriter->save($file_all_path);

        $return['status'] = 1;
        $return['info'] = 'success';
        $return['url'] = $file_all_path;
        $return['msg'] = '导出成功';
        echo json_encode($return, true);
    }

    /**
     * is hide dep
     * @return bool
     */
    protected function checkUserDep()
    {
        $usermodel = M('sys_admin');
        $user = $usermodel->where("id = '{$_SESSION['adminid']}'")->find();   //用户
        if($user['department_id'] == self::HIDE_DEP){
            return false;
        }
        return true;
    }

}

class Dir{

    //判断目录是否存在
    static public function is_dir($dir){
        if (is_dir($dir)) {
            return true;
        }else{
            return false;
        }
    }

    //递归创建目录
    static public function mkdir_digui($dir, $mode = 0777){
        if (is_dir($dir) || @mkdir($dir,$mode)) {
            return true;
        }
        if (!self::mkdir_digui(dirname($dir),$mode)) {
            return false;
        }
        return @mkdir($dir,$mode);
    }

    //自动创建目录
    static public function create_dir_auto ($dir) {
        //判断有没有主文件夹，如果没有则创建
        if (!self::is_dir($dir)) {
            //递归创建目录
            self::mkdir_digui($dir);
        }
    }

}