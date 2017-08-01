<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/27
 * Time: 11:25
 */
class FinancialAction extends CommonAction
{

    public function index()
    {
        $this->logincheck();
        $this->menucheck();
        $this->display('Statistics:financial');
    }

    public function ajaxData()
    {
        $financial = $this->getData();


        echo json_encode(array(
            'rows' => $financial,
            'total' =>count($financial)
        ));
    }

    public function export()
    {
        $financial = $this->getData();
        if(empty($financial)){
            $this->error('数据获取失败，没有符合条件的数据');
        }
        $item = array();
        foreach($financial as $key=>$value){
            $item[$key] = $value['time'];
        }

        array_multisort($item,SORT_ASC,$financial);
        $title['time'] = '日期';
        $title['amount'] = '游戏直充';
        $title['buy_coin'] = '买币直充';
        $title['app'] = 'APP活动';
        $title['cash_over'] = '渠道分成';
        $title['buy_voucher'] = '买券';
        $title['offline_coin'] = '线下买币';
        $title['cp_into'] = '分成';
        $title['agent_coin'] = '渠道币';
        $title['game_coin'] = '玩家币';
        $title['voucher'] = '代金券总额';
        $title['earning'] = '收入';
        $title['expend'] = '支出';
        $title['expend_qz'] = '潜在支出';
        $title['earning_qz'] = '预估收入';
       
        $this->exportFile($title, $financial);
    }
    
    public function getData()
    {
        $financialModel = M('static_financial');
        $condition = $this->parseWhere();
        $sort = $this->parseOrder();

        $financial = $financialModel
            ->where($condition)
            ->order($sort)
            ->select();

        $sumEarning = $sumExpend = $sumExpend_qz = $smEarning_qz = 0;
        foreach($financial as &$value){
            $value['earning'] = $value['amount'] + $value['buy_coin'] + $value['app'] + $value['buy_voucher'] +
                $value['offline_coin'];
            $value['expend'] = $value['cash_over'] + $value['cp_into'];


            $value['expend_qz'] = $value['agent_coin']/10 + $value['game_coin']/10 +
                $value['voucher'];
            $value['earning_qz'] = $value['earning'] - $value['expend'];

            $sumEarning += $value['earning'];
            $sumExpend += $value['expend'];
            $sumExpend_qz += $value['expend_qz'];
            $smEarning_qz += $value['earning_qz'];


        }

        array_unshift($financial,array(
            'time' => '统计',
            'amount'=>'-',
            'buy_coin'=>'-',
            'app'=>'-',
            'cash_over'=>'-',
            'buy_voucher'=>'-',
            'offline_coin'=>'-',
            'cp_into'=>'-',
            'agent_coin'=>'-',
            'game_coin'=>'-',
            'voucher'=>'-',
            'earning' => $sumEarning,
            'expend' => $sumExpend,
            'expend_qz' => $sumExpend_qz,
            'earning_qz' => $smEarning_qz
        ));

        return $financial;
    }

    public function editOfflineCoin()
    {
        $id = isset($_POST["id"]) ? (int)$_POST["id"] : '';
        $money = isset($_POST["offline_coin"]) ? $_POST["offline_coin"] : 0;

        if(empty($id)){
            $this->ajaxReturn('参数错误','error',0);
        }

        $modal = M('static_financial');

        $condition["id"] = $id;

        $financial = $modal->where($condition)->find();
        $data["offline_coin"] = $money;
        $result = $modal->where($condition)->save($data);

        $this->insertLog($_SESSION['adminname'],'修改买币金额', 'FinancialAction.class.php', 'editOfflineCoin',
            $data['updatetime'],
            $_SESSION['adminname']."重置【".$financial['time']."】买币金额,时间【".$financial["createtime"]."】,原始金额：" .$financial["cash_over"].",修改金额:".$money);
        if($result){
            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('未能更新成功','fail',0);
            exit();
        }
    }

    public function parseWhere()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $where = array();
        if(empty($startTime) || empty($endTime)){
            $startTime =date('Y-m-01');
            $endTime = date('Y-m-d');
        }

        if(strtotime($endTime)  >= mktime(0,0,0)){
            $endTime = date('Y-m-d', strtotime('-1 day', mktime(0,0,0)));
        }

        if($startTime > $endTime){
            $itemTime = $startTime;
            $startTime = $endTime;
            $endTime = $itemTime;
        }

       $where['time'] = array(array('EGT', $startTime), array('ELT', $endTime));

        return $where;
    }

    public function parseOrder()
    {
        return "time desc";
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