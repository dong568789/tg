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
        $current = I('request.current', 1, 'intval');
        $rowCount = I('request.rowCount', 50, 'intval');
        $dailCount = $this->getData();
        /*$sort = I('request.sort');
        foreach($sort as $k => $v){
            if($v == 'asc'){
                $asc = SORT_ASC;
            }else{
                $asc = SORT_DESC;
            }
            $sort = $k;
        }

        if($sort){
            $item = array();
            foreach($dailCount as $k2=>$v2){
                $item[$k2] = $v2[$sort];
            }
            array_multisort($item, $asc, $dailCount);
        }


        $current <= 0 && $current = 1;

        $offset  = ($current-1) * $rowCount;

        $itmeDailCount = array_slice($dailCount, $offset, $rowCount);*/
        sort($dailCount);
       $this->ajaxReturn($dailCount,'success',1);
       // sort($itmeDailCount);
       /* $datas['current'] = $current;
        $datas['rowCount'] = $rowCount;
        $datas['rows'] = !empty($itmeDailCount) ? $itmeDailCount : array();
        $datas['total'] = count($dailCount);
        echo json_encode($datas, true);*/

        exit;
    }


    public function getData()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $channel = I('request.channel','','intval');
        $realname = I('request.searchPhrase');

        if(!empty($startTime) && !empty($endTime)){
            $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));
        }elseif(!empty($startTime) && empty($endTime)){
            $where['a.date'] = array('EGT', $startTime);
        }elseif(empty($startTime) && !empty($endTime)){
            $where['a.date'] = array('ELT', $endTime);
        }else{
            $startTime =date('Y-m-1');
            $endTime = date('Y-m-d');

            $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));
        }

        if(strtotime($endTime)  >= mktime(0,0,0)){

            $endTime = date('Y-m-d', strtotime('-1 day', mktime(0,0,0)));
        }

        !empty($channel) && $where['a.channelid'] = $channel;

        !empty($realname) && $where['b.realname'] = array('like', '%'.$realname.'%');

        //每个渠道流水
        $dailyaccountModel = M('TgDailyaccount');
        $dailCount = $dailyaccountModel->alias('a')
            ->join('left join ' . C('DB_PREFIX') . 'tg_user as b on a.userid=b.userid')
            ->where($where)
            ->field('a.userid,a.channelid,sum(dailyjournal) as sum_dailyjournal,b.realname,sum(newpeople) as sum_newpeople,b.channelbusiness')
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
            ->where($where)
            ->field('a.userid,a.channelid,sum(case WHEN b.voucherje > 0 THEN b.amount-b.voucherje ELSE b.amount END) as sum_amount,sum(b.voucherje) as sum_voucherje')
            ->group('a.userid')
            ->select();
        $itemData = $arrUserid = array();
        foreach($data as $v){
            $itemData[$v['userid']] = $v;
        }

        foreach($dailCount as $v){
            $arrUserid[] = $v['userid'];
        }

        $balancemodel = D('Balance');
        foreach($dailCount as $key => &$value){
            $sumAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['sum_amount'] : 0;

            $yx_amount = (int)($sumAmount - $value['sum_dailyjournal']);
            $value['yx_amount'] = intval(number_format($yx_amount,0, '', ''));
            $value['sum_voucherje'] = intval(number_format($itemData[$value['userid']]['sum_voucherje'], 0, '', ''));
            $value['yx_countamount'] =  intval(number_format($value['sum_dailyjournal'] + $yx_amount, 0, '', ''));
            $value['timeZone'] = "{$startTime}至{$endTime}";
            //推广用户未提现金额
            $balance = $balancemodel->money($value['userid']);
            $value['unwithdraw'] = (int)$balance['unwithdraw'];
            $value['sum_dailyjournal'] = intval(number_format($value['sum_dailyjournal'], 0, '', ''));
            $value['sum_newpeople'] = (int)$value['sum_newpeople'];
            if($value['sum_dailyjournal'] <= 0 && $value['yx_amount'] <= 0){
                unset($dailCount[$key]);
            }

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