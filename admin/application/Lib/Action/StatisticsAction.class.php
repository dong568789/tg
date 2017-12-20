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

    public $cooperative;

    public function __construct()
    {
        parent::__construct();
        $this->menucheck();
        $this->cooperative = $this->getCooperative();

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
        $this->assign('games', $games);
        $this->display();
    }

    public function recharge()
    {
        $games = $this->getGames();
        $this->assign('games', $games);
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

        $tgUser = $this->getUserIdByDep();
        if(!empty($tgUser)){
            $condition['S.userid'] = array('in', array_keys($tgUser));
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

    protected function parseRechargeWhere()
    {
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

        $tgUser = $this->getUserIdByDep();
        if(!empty($tgUser)){
            $condition['S.userid'] = array('in', array_keys($tgUser));
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
        $condition['_logic'] = 'AND';
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
        $payModel = M("all_pay");
        $count = $payModel->alias("D")
            ->join(C('DB_PREFIX')."tg_source S on D.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT")
            ->where($condition)
            ->field('count(*) as count,sum(amount) as allmoney')
            ->find();

        $cpsPay = $payModel->alias("D");
        $cpsPay->join(C('DB_PREFIX')."tg_source S on D.agent = S.sourcesn", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT");
        $cpsPay->join(C('DB_PREFIX')."dic_paytype P on P.paytype = D.paytype", "LEFT");
        $cpsPay->field('D.orderid,D.regagent,D.agent,D.username,D.amount,D.status,D.serverid,D.create_time,C.channelname,G.gamename,P.payname');
        $cpsPay->where($condition);
        $cpsPay->order($order);
        ($current > 0 && $rowCount > 0) && $cpsPay->page($current, $rowCount);

        $payList = $cpsPay->select();

        //echo $cpsPay->_sql();exit;
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


    protected function getUserIdByDep()
    {
        $cooperative = $this->getCooperative();

        $userids = array();
        if(!empty($cooperative)){
            $tgUserModel = M('tg_user');
            $users = $tgUserModel->where(array('cooperative' => $cooperative))->field('userid,account')->select();

            foreach($users as $v){
                $userids[$v['userid']] = $v;
            }

        }
        return $userids;
    }

    protected function getGames()
    {
        $gameModel = M('tg_game');

        $where['activeflag'] = 1;
        $where['isonstack'] = 0;

        $games = $gameModel->where($where)->field('gameid,gamename')->select();

        return $games;
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