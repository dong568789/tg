<?php
class RechargeAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();

        $gameModel = M('cps_game');
        $map["isonstack"] = 0;
        $map["activeflag"] = 1;
        $game = $gameModel->where($map)->select();
        $this->assign('gameall',$game);
        $this->menucheck();
        $this->display();
    }

    /**
     * 通过渠道id获取游戏列表
     */
    public function ajaxGame()
    {
        $channelid = isset($_POST['channelid']) ? (int)$_POST['channelid'] : 1;

        $sourcecondition = array();
        $sourcecondition["S.channelid"] = $channelid;
        $sourcecondition["S.activeflag"] = 1;
        $sourcecondition["G.activeflag"] = 1;
        $sourcecondition["G.isonstack"] = 0;

        $sourcemodel = M("tg_source");
        $gamelist = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "JOIN")->where($sourcecondition)->field('G.gameid,G.gamename')->order("S.createtime desc")->select();

        $cpsSourcemodel = M("cps_source");
        $cpsgamelist = $cpsSourcemodel->alias("S")->join(C('DB_PREFIX')."cps_game G on S.gameid = G.gameid", "JOIN")
            ->where($sourcecondition)->field('G.gameid,G.gamename')->order("S.createtime desc")->select();

        $gamelist = array_merge((array)$gamelist,(array)$cpsgamelist);
        $data = array(
            'data' => $gamelist,
            'status' => 1
        );
        $this->ajaxReturn($data, 'JSON');
    }

    // 所有筛选，搜索
    public function search(){
        $this->logincheck();
        $current    = isset($_POST['current']) ? (int)$_POST['current'] : 1;
        $rowCount   = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;

        // 没有搜索用户的情况，不需要关连all_user表，所以放在前面的条件判断中
        // 根据筛选条件，读取相关信息，关联表都是显示时候的提取数据
        $condition = $this->parseWhere();
        $order = $this->parseOrder();
        $data = $this->getUserRecharge($condition, $order, $current, $rowCount);



        echo json_encode(array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $data['list'],
            'allmoney' => $data['allmoney'],
            'total' => $data['count']
        ));

        exit();
    }

    protected function parseWhere()
    {
        $userid     = $_SESSION['userid'];
        $account    = isset($_POST["username"]) ? $_POST["username"] : '';
        $channelid  = isset($_POST["channelid"]) ? (int)$_POST["channelid"] : 0;
        $gameid     = isset($_POST["gameid"]) ? (int)$_POST["gameid"] : 0;
        $startdate  = isset($_POST["startdate"]) ? $_POST["startdate"] : '';
        $enddate    = isset($_POST["enddate"]) ? $_POST["enddate"] : '';

        $condition = array(); //条件
        // 游戏条件
        if ($gameid > 0) {
            $condition["S.gameid"] = $gameid;
        }

        // 时间条件
        if ($startdate != "" && $enddate != "") {
            $strat = strtotime($startdate.' 00:00:00');
            $end = strtotime($enddate.' 23:59:59');
            $condition["D.create_time"]  = array(array('egt',$strat),array('elt',$end),'and');
        }
        // 充值用户条件
        if ($account != "" && $account != null) {
            // 支持模糊搜索
            $complex = array();
            $complex["U.username"] = array('like','%'.$account.'%');
            $complex["U.email"] = array('like','%'.$account.'%');
            $complex["U.mobile"] = array('like','%'.$account.'%');
            $complex['_logic'] = 'OR';

            $condition['_complex'] = $complex;
        }

        // 并且 用户的注册渠道 也是当前用户的渠道
        if ($channelid > 0) {
            $cpssourcemodel = M("cps_source");
            // 获取该子账号渠道的的资源
            $cpssource = $cpssourcemodel->where(" channelid='$channelid' ")->select();

            $sourcelist = array();
            foreach($cpssource as $k => $v){
                $sourcelist[] = $v["sourcesn"];
            }
            $condition['D.regagent'] = array('in',$sourcelist);
        }


        $condition['D.status'] = 1;
        $condition['_logic'] = 'AND';
        return $condition;
    }


    protected function parseOrder()
    {
        $sort = isset($_POST['sort']) ? $_POST['sort'] : '';

        if(empty($sort)){
            $order = "create_time desc";
        }else{
            foreach($sort as $k => $v){
                $order = "{$k} {$v}";
            }
        }

        return $order;
    }

    protected function getUserRecharge($condition, $order, $current='', $rowCount='')
    {
        $cpsPayModel = M("cps_pay");
        $count = $cpsPayModel->alias("D")
            ->join(C('DB_PREFIX')."cps_source S on D.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT")
            ->where($condition)
            ->field('count(*) as count,sum(amount) as allmoney')
            ->find();

        $cpsPay = $cpsPayModel->alias("D");
        $cpsPay->join(C('DB_PREFIX')."cps_source S on D.agent = S.sourcesn", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT");
        $cpsPay->field('D.orderid,D.regagent,D.agent,D.username,D.amount,D.status,D.serverid,D.create_time,C.channelname,G.gamename,P.payname');
        $cpsPay->where($condition);
        $cpsPay->order($order);

        ($current > 0 && $rowCount > 0) && $cpsPay->page($current, $rowCount);

        $payList = $cpsPay->select();
        empty($payList) && $payList = array();
        foreach($payList as $k => $v){
            $payList[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
            if ($v['status'] == 1) {
                $payList[$k]['status'] = "<span style='color:#F00'>成功</span>";
            } else if ($v['status'] == 2) {
                $payList[$k]['status'] = "<span style='color:#F00'>失败</span>";
            }  else if ($v['status'] == 0) {
                $payList[$k]['status'] = "待支付";
            }
        }
        return array('list' => $payList, 'count' => (int)$count['count'], 'allmoney' =>
            (int)$count['allmoney']);
    }

    // 导出
    public function export(){
        $this->logincheck();

        //-----获取数据
        $condition = $this->parseWhere();
        $order = $this->parseOrder();
        $data = $this->getUserRecharge($condition, $order);

        if($data['count'] <= 0){
            $this->ajaxReturn(array('status' => 0, 'info' => 'error'),'JSON');
        }

        //-----引入PHPExcel类
        include_once '../Third/PHPExcel/Classes/PHPExcel.php';
            
        //------写内容
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);// 设置当前的sheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');// 设置sheet的name

        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', '宋体'));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

        //设置样式
        $centerStyle = array(
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $boldStyle = array(
            'font' => array(
               'bold' => true,
            ),
            'alignment' => array(
               'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $centerBoldStyle = array(
            'font' => array(
               'bold' => true,
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $centerBoldSmallStyle = array(
            'font' => array(
               'bold' => true,
               'size'=>10,
            ),
            'alignment' => array(
               'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
               'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $boldSmallStyle = array(
            'font' => array(
               'bold' => true,
               'size'=>10,
            ),
            'alignment' => array(
               'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        // 设置单元格的值
        $titleContent = '充值查询';
        $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $titleContent);
        // 设置样式
        $objPHPExcel->getActiveSheet()->getStyle('A1' )->applyFromArray($centerBoldStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);

        $typeDateContent='打印日期：'.date('Y年m月d日',time());
        $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', $typeDateContent);
        // 设置样式
        $objPHPExcel->getActiveSheet()->getStyle('A2' )->applyFromArray($boldStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(25);
        
        $objPHPExcel->getActiveSheet()->setCellValue('A3', '游戏');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', '渠道');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', '账号');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', '金额（汇总：'.$data['allmoney'].'）');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', '状态');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', '游戏区服');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', '时间');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', '充值方式');
        // 设置样式
        $objPHPExcel->getActiveSheet()->getStyle('A3:H3' )->applyFromArray($centerBoldStyle);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(25);

        $current_line=4;
        // 根据游戏，资源统计出来
        if($data['list']){
            foreach ($data['list'] as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $value['gamename']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$current_line, $value['channelname']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$current_line, $value['username']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$current_line, $value['amount']);

                $statusStr='成功';
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$current_line, $statusStr);

                $objPHPExcel->getActiveSheet()->setCellValue('F'.$current_line, $value['serverid']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$current_line, $value['create_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$current_line, $value['payname']);

                //设置样式
                $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line.':C'.$current_line)->applyFromArray($centerStyle);
                $objPHPExcel->getActiveSheet()->getStyle('E'.$current_line.':H'.$current_line)->applyFromArray($centerStyle);
                $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(25);

                $current_line++;
            }
        }

        //设置宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        // 设置高度
        $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(180);

        //----------保存excel—2007格式
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        //或者$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); 非2007格式

        // 生成文件目录
        include_once '../Share/classes/DirClass.php';
        $today=date('Y/m/d/');
        $save_path='upfiles/export_recharge/'.$today;
        Dir::create_dir_auto($save_path);
        // 文件名
        $file_name=md5(time());
        $file_all_path=$save_path.$file_name.'.xlsx';

        // 生成excel
        $objWriter->save($file_all_path);

        $data['status'] = 1;
        $data['info'] = 'success';
        $data['url'] = $file_all_path;
        $this->ajaxReturn($data,'JSON');
        exit();
    }
}
?>