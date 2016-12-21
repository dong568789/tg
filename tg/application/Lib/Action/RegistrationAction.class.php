<?php
class RegistrationAction extends CommonAction {
    public function __construct(){
        parent::__construct();
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
        $account = $_POST["username"];
        $channelid = $_POST["channelid"];
        $gameid = $_POST["gameid"];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];

        $allusermodel = M("all_user");
        $sourcemodel = M("tg_source");
        $logininfomodel = M("sdk_logininfo");
        
        $condition = array(); //条件

        //根据渠道的 游戏列表
        // 渠道条件
        $gameresult = array(); //游戏列表
        if (isset($channelid) && $channelid > 0) {
            $condition["S.channelid"] = $channelid;
            $condition["S.activeflag"] = 1;
            
            $sourcecondition = array();
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
            $condition["S.userid"] = $userid;
            array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
        }
        
        // 游戏条件
        if (isset($gameid) && $gameid > 0) {
            $condition["S.gameid"] = $gameid;
        }

        // 时间条件
        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $newstart = strtotime($startdate);
            $newend = strtotime($enddate);
            $strat = strtotime(date('Y-m-d 00:00:00', $newstart));
            $end = strtotime(date('Y-m-d 23:59:59', $newend));
            $condition["AU.reg_time"]  = array(array('egt',$strat),array('elt',$end),'and');
        }

        // 充值用户条件
        if ((isset($account) && $account != "" && $account != null)) {
            // 支持模糊搜索
            $condition["AU.username"] = array('like','%'.$account.'%');
            $condition["AU.email"] = array('like','%'.$account.'%');
            $condition["AU.mobile"] = array('like','%'.$account.'%');
        }
        $condition['_logic'] = 'AND';
       
        // 根据筛选条件，读取相关信息，关联表都是显示时候的提取数据
        $user = $allusermodel->alias("AU")
                    ->join(C('DB_PREFIX')."tg_source S on AU.agent = S.sourcesn", "LEFT")
                    ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
                    ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
                    ->field('AU.id,AU.username,AU.reg_time,AU.agent,AU.gameid,C.channelname,G.gamename')
                    ->where($condition)
                    ->order('AU.reg_time desc')
                    ->select();
                    // vde($allusermodel->getLastSql());
        $result = array(); //返回结果
        $result["game"] = $gameresult;//根据渠道的 游戏列表
        $result["userall"] = array(); //注册列表 
    	if($user){
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
            $result["userall"] = $user;

            $this->ajaxReturn($result,'success',1);
            exit();
    	}else{
            $this->ajaxReturn($result,'fail',0);
            exit();
        }
    }






















}
?>