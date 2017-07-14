<?php
class RegistrationAction extends CommonAction {
    public function __construct(){
        parent::__construct();

        // cps用户没有进入这里的权限
        if($this->sourcetype == 4){
            Header("Location: /source/ ");
            exit();
        }
    }

    public function index(){
        $this->logincheck();

        if($this->userpid=='0'){
            $channelmodel = M('tg_channel');

            // 母账号,获取渠道列表
            $userid = $_SESSION['userid'];
            $map = array();
            $map['userid'] =$userid;
            $map["activeflag"] = 1;
            $channel = $channelmodel->where($map)->select();
            $this->assign('channel',$channel);
        }
       
        $this->display();
    }

    // 所有筛选，搜索
    public function search(){
        $this->logincheck();

        $userid = $_SESSION['userid'];
        $account    = isset($_POST["username"]) ? $_POST["username"] : '';
        $channelid  = isset($_POST["channelid"]) ? (int)$_POST["channelid"] : 0;
        $gameid     = isset($_POST["gameid"]) ? (int)$_POST["gameid"] : 0;
        $startdate  = isset($_POST["startdate"]) ? $_POST["startdate"] : '';
        $enddate    = isset($_POST["enddate"]) ? $_POST["enddate"] : '';
        $current    = isset($_POST['current']) ? (int)$_POST['current'] : 1;
        $rowCount   = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;
        $sort = $this->parseOrder();

        if(strpos($sort, 'login_time') !== false){
            unset($sort);
        }

        $allusermodel = M("all_user");
        $logininfomodel = M("sdk_logininfo");
        
        $condition = array(); //条件

        //根据渠道的 游戏列表
        // 渠道条件
        $gameresult = array(); //游戏列表
        if (isset($channelid) && $channelid > 0) {
            $condition["S.channelid"] = $channelid;
            $condition["S.activeflag"] = 1;
        } else {
            $condition["S.userid"] = $userid;
        }
        
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
        $count = $allusermodel->alias("AU")
            ->join(C('DB_PREFIX')."tg_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->where($condition)
            ->count();

        $cpsUserModel = M('cps_user');
        $cpsUserSql = $cpsUserModel->alias("AU")
            ->join(C('DB_PREFIX')."cps_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT")
            ->field('AU.id,AU.username,AU.reg_time,AU.agent,AU.gameid,C.channelname,G.gamename')
            ->where($condition)
            ->buildSql();

        $userSql = $allusermodel->alias("AU")
            ->join(C('DB_PREFIX')."tg_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->field('AU.id,AU.username,AU.reg_time,AU.agent,AU.gameid,C.channelname,G.gamename')
            ->where($condition)
            ->union($cpsUserSql)
            ->buildSql();

        $user = M('')->table($userSql.' a')
            ->order($sort)
            ->page($current, $rowCount)
            ->select();
        $cpsCount = $cpsUserModel->alias("AU")
            ->join(C('DB_PREFIX')."cps_source S on AU.agent = S.sourcesn", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT")
            ->where($condition)
            ->count();


        $count += (int)$cpsCount;

        $result = array(); //返回结果
        $result["game"] = $gameresult;//根据渠道的 游戏列表
        $result["userall"] = array(); //注册列表 

        empty($user) && $user = array();
        foreach($user as $k => $v){
            $user[$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);

            //获取登陆时间
            $lastid = $v['id'];
            $loginsql = "select userid, login_time from yx_sdk_logininfo where userid ='$lastid' ORDER BY login_time DESC LIMIT 1";
            $login = $logininfomodel->query($loginsql);
            if($login[0]['login_time'] == ''){
                $user[$k]['login_time'] = $user[$k]['reg_time'];
            }else{
                $user[$k]['login_time'] = date('Y-m-d H:i',$login[0]['login_time']);
            }
        }

        echo json_encode(array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $user,
            'total' => $count
        ));
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




















}
?>