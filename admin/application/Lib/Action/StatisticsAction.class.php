<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/18
 * Time: 11:33
 */
class StatisticsAction extends CommonAction
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->menucheck();
        $this->display('index');
    }


    public function ajaxData()
    {
        $dailCount = $this->getData();
        sort($dailCount);
        $this->ajaxReturn($dailCount,'success',1);
        exit;
    }


    public function getData()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $channel = I('request.channel','','intval');
        $realname = I('request.searchPhrase');


        if(empty($startTime) || empty($endTime)){
            $startTime =date('Y-m-01');
            $endTime = date('Y-m-d');
        }

        if(strtotime($endTime)  >= mktime(0,0,0)){
            $endTime = date('Y-m-d', strtotime('-1 day', mktime(0,0,0)));
        }

        $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));

        !empty($channel) && $where['a.channelid'] = $channel;

        !empty($realname) && $where['b.realname'] = array('like', '%'.$realname.'%');
        //每个渠道流水
        $dailyaccountModel = M('TgDailyaccount');
        $dailCount = $dailyaccountModel->alias('a')
            ->join('left join ' . C('DB_PREFIX') . 'tg_user as b on a.userid=b.userid')
            ->where($where)
            ->field('a.userid,a.channelid,sum(a.dailyjournal) as sum_dailyjournal,b.realname,sum(a.newpeople) as sum_newpeople,sum(a.dailyincome) as sum_dailyincome,b.channelbusiness')
            ->group('a.userid')
            ->select();

        $where = array(
            'b.status' => 1,
            'b.create_time' =>  array(array('EGT',strtotime($startTime.' 00:00:00')), array('ELT', strtotime($endTime.' 23:59:59')))
        );

        //推广用户总流水
        !empty($channel) && $where['b.channelid'] = $channel;
        $data = M('')->table(C('DB_PREFIX') . 'tg_source as a')
            ->join("left join " . C('DB_PREFIX') . "all_pay as b on a.sourcesn=b.agent ")
            ->join("left join " . C('DB_PREFIX') . "tg_game as c on a.gameid=c.gameid ")
            ->where($where)
            ->field('a.userid,
                    a.channelid,
                    sum(
                        CASE
                        WHEN b.voucherje > 0 THEN
                            b.amount - b.voucherje
                        ELSE
                            b.amount
                        END
                    ) AS sum_amount,
                    sum(b.voucherje) AS sum_voucherje,
                    sum(
                        CASE
                        WHEN b.amount > 0 THEN
                            b.amount * ((1 - c.channelrate) * (1 - c.joinsharerate))
                        ELSE
                            0
                        END
                    ) AS cpamount')
            ->group('a.userid')
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
        foreach($dailCount as $key => &$value){
            $sumAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['sum_amount'] : 0;
            $cpAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['cpamount'] : 0;

            $yx_amount = (int)($sumAmount - $value['sum_dailyjournal']);
            $value['yx_amount'] = intval($yx_amount);
            $value['sum_voucherje'] = intval($itemData[$value['userid']]['sum_voucherje']);
            $value['yx_countamount'] =  intval($value['sum_dailyjournal'] + $yx_amount);
            $value['timeZone'] = "{$startTime}至{$endTime}";
            //推广用户未提现金额
            /*$balance = $balancemodel->money($value['userid']);
            $value['unwithdraw'] = (int)$balance['unwithdraw'];*/
            $value['sum_dailyjournal'] = intval($value['sum_dailyjournal']);
            $value['sum_newpeople'] = (int)$value['sum_newpeople'];
            $value['sum_dailyincome'] = (int)$value['sum_dailyincome'];
            $value['sum_cpamount'] = (int)$cpAmount;
            $value['buyer_voucher'] = isset($itemVoucher[$value['userid']]) ? (int)$itemVoucher[$value['userid']]['sum_amount'] : 0;
            //sum(b.dailyjournal*(1-a.channelrate)*(1-a.sharerate)) as sum_cpamount
            /*if($value['sum_dailyjournal'] <= 0 && $value['yx_amount'] <= 0){
                unset($dailCount[$key]);
            }*/
        }

        return $dailCount;
    }


    public function export()
    {
        $dailCount = $this->getData();
        if(empty($dailCount)){
           $this->error('数据获取失败，没有符合条件的数据');
        }

        $sum_newpeople = $sum_dailyjournal = $yx_amount = $yx_countamount = $sum_voucherje = $unwithdraw = 0;
        foreach($dailCount as $v3){
            $sum_newpeople += $v3['sum_newpeople'];
            $sum_dailyjournal += $v3['sum_dailyjournal'];
            $yx_amount += $v3['yx_amount'];
            $yx_countamount += $v3['yx_countamount'];
            $sum_voucherje += $v3['sum_voucherje'];
            $unwithdraw += $v3['unwithdraw'];
        }
        $dailCount[] = array(
            'timeZone' => '总计：',
            'realname' => '',
            'sum_newpeople' => $sum_newpeople,
            'sum_dailyjournal' => $sum_dailyjournal,
            'yx_amount' => $yx_amount,
            'yx_countamount' => $yx_countamount,
            'sum_voucherje' => $sum_voucherje,
            'unwithdraw' => number_format($unwithdraw, 0, '', ''),

        );
        $title = array('timeZone' => '日期','channelbusiness' => '维护人' ,'realname' => '用户名', 'sum_newpeople' => '新增注册数', 'sum_dailyjournal' => '渠道流水', 'yx_amount' => '平台流水', 'yx_countamount' => '总流水', 'sum_voucherje' => '优惠金额', 'unwithdraw' => '未提现金额');
        $this->exportFile($title, $dailCount);
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