<?php
class RegistrationAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
        $Index = D('Registration');
        //$registration = $Index->index();
        //$this->assign('registration',$registration);
        $this->assign('channel',$Index->channel());
        $this->display();
    }


    public function refresh(){
        $this->logincheck();
        $userid = $_SESSION['userid'];
        $account = $_POST["username"];
        $channelid = $_POST["channelid"];
        $gameid = $_POST["gameid"];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];
        $ischannel = $_POST["ischannel"];
        $model = M("all_user");
        $gameresult = array();
        $condition["S.userid"] = $userid;
        $where = ' AND 1=1';
        if (isset($channelid) && $channelid > 0) {
            $condition["S.channelid"] = $channelid;
            $condition["S.activeflag"] = 1;
            $where.= " AND b.channelid = '$channelid' AND b.activeflag = '1'";

            $model = M("all_user");
            if ($ischannel == 1) {
                $sourcemodel = M("tg_source");
                $sourcecondition["S.userid"] = $userid;
                $sourcecondition["S.channelid"] = $channelid;
                $sourcecondition["S.activeflag"] = 1;
                $sourcecondition["G.activeflag"] = 1;
                $sourcecondition["G.isonstack"] = 0;
                $gamelist = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();
                if ($gamelist) {
                    foreach ($gamelist as $k => $v) {
                        $gameresult[] = "<option value=".$v["gameid"].">".$v["gamename"]."</option>";
                    }
                    array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
                } else {
                    array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
                }
            }
        } else {
            array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
        }
        if (isset($gameid) && $gameid > 0) {
            $gamemodel = M('tg_game');
            $onegame = $gamemodel->where("gameid = '$gameid'")->find();
            $condition["D.gameid"] = $onegame['sdkgameid'];
            $sdkgameid = $onegame['sdkgameid'];

            $where.= " AND a.gameid = '$sdkgameid'";
        }
        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $newstart = strtotime($startdate);
            $newend = strtotime($enddate);
            $strat = strtotime(date('Y-m-d 00:00:00', $newstart));
            $end = strtotime(date('Y-m-d 23:59:59', $newend));
            $condition["D.reg_time"]  = array(array('egt',$strat),array('elt',$end),'and');

            $where.= " AND a.reg_time >= '$strat' AND a.reg_time <= '$end'";
        }

        if ((isset($account) && $account != "" && $account != null)) {
            $userone = $model->where("username = '$account' OR email='$account' OR mobile = '$account'")->find();
            $condition["D.username"] = $userone['username'];
            $username = $userone['username'];
            $where.= " AND a.username = '$username'";
        }
        $condition['_logic'] = 'AND';
       

        $usersql ="select a.id, username,reg_time,agent,a.gameid from yx_all_user  as a LEFT JOIN yx_tg_source as b on a.agent=b.sourcesn where b.userid='$userid'". $where." ORDER BY a.reg_time desc";
        $user = $model->query($usersql);
        
        $channelsql ="select a.sourcesn, b.channelname from yx_tg_source as a LEFT JOIN yx_tg_channel b on a.channelid = b.channelid where a.userid='$userid'";
        $channelmodel = M('tg_channel');
        $channel = $channelmodel->query($channelsql);
        
        $gamesql ="select sdkgameid, gamename from yx_tg_game";
        $gamemodel = M('tg_game');
        $game = $gamemodel->query($gamesql);
       
        $result = array();
        foreach($user as $k => $v){
            $v['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
            //获取渠道名
            foreach($channel as $k1 => $v1){
                if($v['agent'] == $v1['sourcesn']){
                    $v['channelname'] = $v1['channelname'];
                    break;
                }
            }
            //获取游戏名
            foreach($game as $k2 => $v2){
                if($v['gameid'] == $v2['sdkgameid']){
                    $v['gamename'] = $v2['gamename'];
                    break;
                }
            }
            //获取登陆时间
            $lastid = $v['id'];
            $loginsql = "select userid, login_time from yx_sdk_logininfo where userid ='$lastid' ORDER BY login_time DESC LIMIT 1";
            $loginmodel = M('sdk_logininfo');
            $login = $loginmodel->query($loginsql);
            if($login[0]['login_time'] == ''){
                $v['login_time'] = '';
            }else{
                $v['login_time'] = date('Y-m-d H:i',$login[0]['login_time']);
            }
            $data[] = $v;

        }

        $result = $data;
        $result["game"] = $gameresult;

        if ($user) {
            $result["userall"] = $data;

            $this->ajaxReturn($result,'success',1);
            exit();
        } else {
            $this->ajaxReturn($result,'fail',0);
            exit();
        }
    }






















}
?>