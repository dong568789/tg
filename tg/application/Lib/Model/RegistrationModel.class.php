<?php

class RegistrationModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $userid = $_SESSION["userid"];

        $usermodel = M("all_user");
        //$sql = "select * from yx_all_user where agent in ( select sourcesn from yx_tg_source where userid='$userid')";
        $sql = "select a.id, username,reg_time,agent,a.gameid from yx_all_user  as a LEFT JOIN yx_tg_source as b on a.agent=b.sourcesn where b.userid='$userid' ORDER BY a.reg_time DESC";
        $users = $usermodel->query($sql);
        $channelsql ="select a.sourcesn, b.channelname from yx_tg_source as a LEFT JOIN yx_tg_channel b on a.channelid = b.channelid where a.userid='$userid'";
        $channelmodel = M('tg_channel');
        $channel = $channelmodel->query($channelsql);

        $gamesql ="select sdkgameid, gamename from yx_tg_game";
        $gamemodel = M('tg_game');
        $game = $gamemodel->query($gamesql);

        foreach($users as $k => $v){
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
            //获取最近登录时间
            $lastid = $v['id'];
            $loginsql = "select userid, login_time from yx_sdk_logininfo where userid ='$lastid' ORDER BY login_time DESC LIMIT 1";
            $loginmodel = M('sdk_logininfo');
            $login = $loginmodel->query($loginsql);
            if($login[0]['login_time'] == ''){
                $v['login_time'] = '';
            }else{
                $v['login_time'] = date('Y-m-d H:i',$login[0]['login_time']);
            }
            $result[] = $v;
        }
        return $result;

    }

    public function channel(){
        $userid = $_SESSION['userid'];
        $channelmodel = M('tg_channel');
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
        return $channel;
    }














}
?>