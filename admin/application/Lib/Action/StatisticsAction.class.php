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

    public $sourceType = array(
        1 => '公会',
        2 => '买量',
        3 => '平台',
        4 => 'CPS',
        5 => '应用商店',
        6 => '其它'
    );

    public function __construct()
    {
        parent::__construct();
        $this->menucheck();

    }

    public function index()
    {


        $games = $this->getGames();
        $users = $this->getUsers();
       // print_r($users);exit;
        $this->assign('games', $games);
        $this->assign('users', $users);
        $this->display('index');
    }

    public function register()
    {

        $games = $this->getGames();
        $users = $this->getUsers();

        $this->assign('games', $games);
        $this->assign('users', $users);
        $this->display();
    }

    public function recharge()
    {
        $games = $this->getGames();
        $users = $this->getUsers();

        $this->assign('games', $games);
        $this->assign('users', $users);
        $this->display();
    }


    public function ajaxData()
    {
        $result = $this->getData();

        if(count($result["daily"] ) <= 0){
            $this->ajaxReturn($result, 'error', 0);
        }
        $this->ajaxReturn($result, 'success', 1);
    }

    public function getData()
    {
        $result = array();

        $verify = $this->verifyParam();
        if($verify != 'success'){
            return $verify;
        }

        $where = $this->parseWhere();

        $daily = M('')->table(C('DB_PREFIX').'tg_dailyaccount as D')
            ->where($where)
            ->field('D.date,sum(D.newpeople) as newpeople,sum(D.dailyactive) as dailyactive,sum(D.paypeople) as paypeople,sum(D.dailyjournal) as dailyjournal, sum(D.dailyincome) as dailyincome, sum(D.voucherje) as voucherje')
            ->group('date')
            ->order('date desc')
            ->select();

        if(!empty($daily)){
            foreach ($daily as $k => $v) {
                $daily[$k]["date"] = date("Y-m-d",strtotime($v["date"]));
                $daily[$k]["datestr"] = date("Y年m月d日",strtotime($v["date"]));
                $daily[$k]["dailyjournal"] = round($v['dailyjournal'],2);
                $daily[$k]["dailyincome"] = round($v['dailyincome'],2);
                $daily[$k]["sub_dailyincome"] = round($v['sub_dailyincome'],2);
            }

            // 汇总数据
            $dataall["datestr"] = "数据汇总";
            $dataall["dailyactive"] = 0;
            $dataall["newpeople"] = 0;
            $dataall["paypeople"] = 0;
            $dataall["dailyjournal"] = 0;
            $dataall["dailyincome"] = 0;
            $dataall["sub_dailyincome"] = 0;


            foreach ($daily as $k => $v) {
                $dataall["dailyactive"] += $v["dailyactive"];
                $dataall["newpeople"] += $v["newpeople"];
                $dataall["paypeople"] += $v["paypeople"];
                $dataall["dailyjournal"] += $v["dailyjournal"];
                $dataall["dailyincome"] += $v["dailyincome"];
                $dataall["voucherje"] += $v["voucherje"];

                $daily[$k]['action'] = '<a href="'.U('statisticstg/detail', array('date' => $v["date"],'uid'=>$this->tguserid)).'">查看详情</a>';
            }

            array_unshift($daily,$dataall);

            $result["daily"] = $daily;
        }

        return $result;
    }

    protected function verifyParam()
    {
        $stardate = isset($_POST['startdate']) ? trim($_POST['startdate']) : '';
        $enddate = isset($_POST['enddate']) ? trim($_POST['enddate']) : '';

        if(!empty($stardate) && !empty($enddate)){
            $dateTime1 = new DateTime($stardate);
            $dateTime2 = new DateTime($enddate);

            $interval = $dateTime1->diff($dateTime2);

          if($interval->days > 31){
              return '搜索时间区间不能大于31天';
          }

        }
        return 'success';
    }

    protected function parseWhere()
    {
        $startTime = isset($_POST['startdate']) ? trim($_POST['startdate']) : '';
        $enddate = isset($_POST['enddate']) ? trim($_POST['enddate']) : '';
        $gameid = isset($_POST['gameid']) ? intval($_POST['gameid']) : '';
        $userid = isset($_POST['userid']) ? intval($_POST['userid']) : '';

        empty($userid) && $userid = array_keys($this->getUserIdByDep());

        if(!empty($userid)){
            $where['userid'] = array('in', (array)$userid);
        }

        if(!empty($gameid)){
            $where['gameid'] = $gameid;
        }

        if(!empty($startTime) && !empty($enddate)){
            $where['date'] = array(array('egt', $startTime), array('elt', $enddate));
        }else{
            $where['date'] = array(array('egt', date('Y-m-01')), array('elt', date('Y-m-d')));
        }

        return $where;
    }

    // 所有筛选，搜索
    public function ajaxRegister(){

        $account    = isset($_POST["username"]) ? $_POST["username"] : '';
        $gameid     = isset($_POST["gameid"]) ? (int)$_POST["gameid"] : 0;
        $userid     = isset($_POST["userid"]) ? (int)$_POST["userid"] : 0;
        $startdate  = isset($_POST["startdate"]) ? $_POST["startdate"] : '';
        $enddate    = isset($_POST["enddate"]) ? $_POST["enddate"] : '';
        $current    = isset($_POST['current']) ? (int)$_POST['current'] : 1;
        $rowCount   = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;
        $sort = $this->parseOrder();

        if(strpos($sort, 'login_time') !== false){
            unset($sort);
        }

        $userModel = M('all_user');

        $condition = array(); //条件

        //根据渠道的 游戏列表
        // 渠道条件
        $gameresult = array(); //游戏列表

        // 游戏条件
        if (isset($gameid) && $gameid > 0) {
            $condition["S.gameid"] = $gameid;
        }

        // 时间条件
        if ($startdate != "" && $enddate != "") {
            $strat = strtotime($startdate.' 00:00:00');
            $end = strtotime($enddate.' 23:59:59');
            $condition["AU.reg_time"]  = array(array('egt',$strat),array('elt',$end),'and');
        }

        if(empty($userid)){
            $tgUser = $this->getUserIdByDep();
            $userid = array_keys($tgUser);
        }
        if(!empty($userid)){
            $condition['S.userid'] = array('in', (array)$userid);
        }

        // 充值用户条件
        if ((isset($account) && $account != "" && $account != null)) {
            // 支持模糊搜索
            $complex = array();
            $complex["AU.username"] = array('like','%'.$account.'%');
            $complex["AU.email"] = array('like','%'.$account.'%');
            $complex["AU.mobile"] = array('like','%'.$account.'%');
            $complex['_logic'] = 'OR';

            $condition['_complex'] = $complex;
        }
        $condition['_logic'] = 'AND';
        // 根据筛选条件，读取相关信息，关联表都是显示时候的提取数据
        $count = $userModel->alias("AU")
            ->join(C('DB_PREFIX')."tg_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->where($condition)
            ->count();


        $user = $userModel->alias("AU")
            ->join(C('DB_PREFIX')."tg_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->field('AU.id,AU.username,AU.reg_time,AU.agent,AU.gameid,AU.agent,AU.mobile,AU.ip,C.channelname,G
            .gamename')
            ->where($condition)
            ->order($sort)
            ->page($current, $rowCount)
            ->select();
        $result = array(); //返回结果
        $result["game"] = $gameresult;//根据渠道的 游戏列表
        $result["userall"] = array(); //注册列表

        empty($user) && $user = array();
        foreach($user as $k => $v){
            $user[$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
        }

        echo json_encode(array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $user,
            'total' => $count
        ));
    }

    // 所有筛选，搜索
    public function ajaxRecharge(){
        $current    = isset($_POST['current']) ? (int)$_POST['current'] : 1;
        $rowCount   = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;

        // 没有搜索用户的情况，不需要关连all_user表，所以放在前面的条件判断中
        // 根据筛选条件，读取相关信息，关联表都是显示时候的提取数据
        $condition = $this->parseRechargeWhere();
        $order = $this->parseRechargeOrder();
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

    // 导出
    public function export(){

        if(!IS_POST){
            $this->ajaxReturn(array('status' => 0, 'info' => 'error'),'JSON');
        }
        //-----获取数据
        $condition = $this->parseRechargeWhere();
        $order = $this->parseRechargeOrder();
        $data = $this->getUserRecharge($condition, $order);

        if($data['count'] <= 0 && $data['count'] > 5000){
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

    protected function parseRechargeWhere()
    {
        $account    = isset($_POST["username"]) ? $_POST["username"] : '';
        $gameid     = isset($_POST["gameid"]) ? (int)$_POST["gameid"] : 0;
        $userid     = isset($_POST["userid"]) ? (int)$_POST["userid"] : 0;
        $startdate  = isset($_POST["startdate"]) ? $_POST["startdate"] : '';
        $enddate    = isset($_POST["enddate"]) ? $_POST["enddate"] : '';

        $condition = array(); //条件
        // 游戏条件
        if ($gameid > 0) {
            $condition["D.gameid"] = $gameid;
        }

        if(empty($userid)){
            $tgUser = $this->getUserIdByDep();
            $userid = array_keys($tgUser);
        }
        if(!empty($userid)){
            $condition['S.userid'] = array('in', (array)$userid);
        }


        // 时间条件
        if (!empty($startdate) && !empty($enddate)) {
            $strat = strtotime($startdate.' 00:00:00');
            $end = strtotime($enddate.' 23:59:59');
            $condition["D.create_time"]  = array(array('egt',$strat),array('elt',$end),'and');
        }

        // 充值用户条件
        if (!empty($account)) {
            // 支持模糊搜索
            $condition["D.username"] = array('like','%'.$account.'%');
        }

        $condition['D.status'] = 1;
        return $condition;
    }


    protected function parseRechargeOrder()
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
        if(!empty($condition['S.userid'])){
            $sourceModel = M('tg_source');
            $where['S.userid'] = $condition['S.userid'];
            unset($condition['S.userid']);
            $sources = $sourceModel->alias("S")
                ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
                ->where($where)
                ->field('S.sourcesn,C.channelname')
                ->select();

            $itemSource = array();
            foreach($sources as $item){
                $itemSource[$item['sourcesn']] = $item;
            }

            unset($sources);

            $condition['D.agent'] = array('in', array_keys($itemSource));
        }
        $payModel = M("all_pay");
        $count = $payModel->alias("D")
            ->where($condition)
            ->field('count(*) as count,sum(amount) as allmoney')
            ->find();

        $cpsPay = $payModel->alias("D");
        $cpsPay->join(C('DB_PREFIX')."tg_game G on G.sdkgameid = D.gameid", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT");
        $cpsPay->field('D.orderid,D.regagent,D.agent,D.username,D.amount,D.status,D.serverid,D.create_time,G.gamename,P.payname');
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

            if(isset($itemSource[$v['agent']])){
                $payList[$k]['channelname'] = $itemSource[$v['agent']]['channelname'];
            }
        }


        return array('list' => $payList, 'count' => (int)$count['count'], 'allmoney' =>
            (int)$count['allmoney']);
    }


    protected function parseOrder()
    {
        $sort = isset($_POST['sort']) ? $_POST['sort'] : '';

        if(empty($sort)){
            $order = "reg_time desc";
        }else{
            foreach($sort as $k => $v){
                $order = "{$k} {$v}";
            }
        }

        return $order;
    }

    /**
     * 获取合作用户
     * @return array
     */
    protected function getUsers()
    {

        $users = $this->getUserIdByDep();

        return $users;
    }

    protected function getGames()
    {
        $gameModel = M('tg_game');

        $where['activeflag'] = 1;
        $where['isonstack'] = 0;

        $games = $gameModel->where($where)->field('gameid,gamename,sdkgameid')->select();

        return $games;
    }
}