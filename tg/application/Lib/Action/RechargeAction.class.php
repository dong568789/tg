<?php
class RechargeAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
        $Index = D('Recharge');   //默认显示该推广用户充值情况
        $this->assign('channel',$Index->channel());
        $this->display();
    }

    // 所有筛选，搜索
    public function search(){
        $this->logincheck();

        $userid = $_SESSION['userid'];
        $account = $_POST["username"];
        $channelid = $_POST["channelid"];
        $gameid = $_POST["gameid"];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];

        $allusermodel = M("all_user");
        $paymodel = M("all_pay");
        $sourcemodel = M("tg_source");

        $condition["S.userid"] = $userid;

        //根据渠道的 游戏列表
        $gameresult = array();
        if (isset($channelid) && $channelid > 0) {
            $condition["S.channelid"] = $channelid;
            $condition["S.activeflag"] = 1;
            
            $sourcecondition["S.userid"] = $userid;
            $sourcecondition["S.channelid"] = $channelid;
            $sourcecondition["S.activeflag"] = 1;
            $sourcecondition["G.activeflag"] = 1;
            $sourcecondition["G.isonstack"] = 0;
            $gamelist = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();
            if ($gamelist) {
                foreach ($gamelist as $k => $v) {
                    $checkstate='';
                    if (isset($gameid) && $gameid > 0) { //如果有选择游戏
                        if($gameid==$v["gameid"]){
                            $checkstate=' selected="selected" ';
                        }
                    }
                    $gameresult[] = "<option value=".$v["gameid"].$checkstate.">".$v["gamename"]."</option>";
                }
                array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
            } else {
                array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
            }
        } else {
            array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
        }

        if (isset($gameid) && $gameid > 0) {
            $gamemodel = M('tg_game');
            $onegame = $gamemodel->where("gameid = '$gameid'")->find();
            $condition["D.gameid"] = $onegame['sdkgameid'];
        }

        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $newstart = strtotime($startdate);
            $newend = strtotime($enddate);
            $strat = strtotime(date('Y-m-d 00:00:00', $newstart));
            $end = strtotime(date('Y-m-d 23:59:59', $newend));
            $condition["D.create_time"]  = array(array('egt',$strat),array('elt',$end),'and');
        }

        if ((isset($account) && $account != "" && $account != null)) {
            // 支持模糊搜索
            $userall = $allusermodel->field('username')->where('username like "%'.$account.'%" OR email="'.$account.'" OR mobile = "'.$account.'"')->select();
            $userall_arr=array();
            foreach ($userall as $key => $value) {
                $userall_arr[]=$value['username'];
            }
            $userall_atr=implode(',', $userall_arr);

            $condition["D.username"] = array('in',$userall_atr);
        }
        $condition['D.status'] = 1;
        $condition['_logic'] = 'AND';

        // 根据 用户的消费渠道 提取相关数据
        $pay = $paymodel->alias("D")
                    ->join(C('DB_PREFIX')."tg_source S on D.agent = S.sourcesn", "LEFT")
                    ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
                    ->join(C('DB_PREFIX')."tg_game G on G.sdkgameid = D.gameid AND G.gameid = S.gameid", "LEFT")
                    ->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT")
                    ->field('D.orderid,D.regagent,D.agent,D.username,D.amount,D.status,D.serverid,D.create_time,C.channelname,G.gamename,P.payname')
                    ->where($condition)
                    ->select();

        $source = $sourcemodel->where("userid = '$userid'")->select();
        $sourcelist = array();
        foreach($source as $k => $v){
            $sourcelist[] = $v["sourcesn"];
        }
        // 并且 用户的注册渠道 也是当前用户的渠道
        foreach($pay as $k1 => $v1){
            if((in_array($v1['agent'],$sourcelist)) && (in_array($v1['regagent'],$sourcelist))){
                $v = $v1;
                $newpay[] = $v;
            }
        }
        
        $result = array();
        $result["game"] = $gameresult;//根据渠道的 游戏列表
        $result["allmoney"] = 0; //总金额
        $result["getmoney"] = array();  //支付列表
        if ($newpay) {
            $data = array();
            foreach($newpay as $k => $v){
                $newpay[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
                if ($v['status'] == 1) {
                    $newpay[$k]['statusStr'] = "<span style='color:#F00'>成功</span>";
                } else if ($v['status'] == 2) {
                    $newpay[$k]['statusStr'] = "<span style='color:#F00'>失败</span>";
                }  else if ($v['status'] == 0) {
                    $newpay[$k]['statusStr'] = "待支付";
                }

                $result['allmoney'] += $v["amount"];
            }

            $result["getmoney"] = $newpay;

            $this->ajaxReturn($result,'success',1);
            exit();
        } else {
            $this->ajaxReturn($result,'fail',0);
            exit();
        }
    }

    // 导出
    public function export(){
        $this->logincheck();

        //-----获取数据
        $data = $_POST["search_data"];

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
        if($data['getmoney']){
            foreach ($data['getmoney'] as $key => $value) {
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