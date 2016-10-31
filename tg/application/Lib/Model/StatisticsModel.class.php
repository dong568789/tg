<?php

class StatisticsModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $userid = $_SESSION['userid'];
        $dailymodel = M('tg_dailyaccount');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
		$startdate = date("Y-m-d",strtotime("-30 day"));
		$enddate = date("Y-m-d");
		$map["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        $map['D.userid'] =$userid;
        $map["D.activeflag"] = 1;
        $daily = $dailymodel->alias("D")->join(C('DB_PREFIX')."tg_game G on D.gameid = G.gameid", "LEFT")->join(C('DB_PREFIX')."tg_channel C on D.channelid = C.channelid", "LEFT")->where($map)->order("D.createtime desc")->select();
		if ($daily) {
            foreach ($daily as $k => $v) {
               $daily[$k]["date"] = date("Y年m月d日",strtotime($v["date"]." 12:00:00"));
            }
        }
        return $daily;
    }

	public function getTodayData($userid, $channelid, $gameid){
		// 32服务器
		//$SDKdbhost = '192.168.1.32';
		//$SDKdbuser = 'root';
		//$SDKdbpw   = 'hy123456';
		//$SDKdbname = 'db_youxia_new';

		// 正式服务器
		 $SDKdbhost = '10.13.58.56';
         $SDKdbuser = 'User_youxia_tg';
         $SDKdbpw   = 'YOUxiadb@2016';
         $SDKdbname = 'db_youxia_new';

		mysql_connect($SDKdbhost,$SDKdbuser,$SDKdbpw);
		mysql_select_db($SDKdbname);

		mysql_query("SET NAMES 'utf8'");

		$starttime = strtotime(date('Y-m-d 00:00:00')); 
		$endtime = time(); 

		// 找出 昨天 支付成功的 注册渠道
		// group by 去重复，只取出一条
		$channelquery = "select * from yx_all_pay where create_time >= '".$starttime."' and create_time <= '".$endtime."' and status = 1 group by regagent order by id desc";

        $result = mysql_query($channelquery);
		while ($row = mysql_fetch_assoc($result)) {
			$channellist[]=$row["regagent"];
		}
		// 找出 昨天 支付成功的 支付
		$dataquery = "select * from yx_all_pay where create_time >= '".$starttime."' and create_time <= '".$endtime."' and status = 1 order by id desc";
		$result = mysql_query($dataquery);
		while ($row = mysql_fetch_assoc($result)) {
			$data[]=$row;
		}
		// 找出 昨天 登录sdk的 渠道
		$activechannelquery = "select * from yx_sdk_logininfo where login_time >= '".$starttime."' and login_time <= '".$endtime."' group by agent order by id desc";
		$result = mysql_query($activechannelquery);
		while ($row = mysql_fetch_assoc($result)) {
			$channellist[]=$row["agent"];
		}
		// 找出 昨天 登录的 sdk
		$activequery = "select * from yx_sdk_logininfo where login_time >= '".$starttime."' and login_time <= '".$endtime."' order by id desc";
		$result = mysql_query($activequery);
		while ($row = mysql_fetch_assoc($result)) {
			$active[]=$row;
		}
		// 找出统计之前的所有登录信息
		// group by user,agent 可以去重
		$before_login_query = "select * from yx_sdk_logininfo where login_time < '".$starttime."' group by userid,agent order by id desc";
		$before_login_result = mysql_query($before_login_query);
		while ($row = mysql_fetch_assoc($before_login_result)) {
			$before_login[]=$row;
		}

		// 找出 昨天 注册玩家的 渠道
		$userchannelquery = "select * from yx_all_user where reg_time >= '".$starttime."' and reg_time <= '".$endtime."' group by agent order by id desc";
		$result = mysql_query($userchannelquery);
		while ($row = mysql_fetch_assoc($result)) {
			$channellist[]=$row["agent"];
		}
		// 找出 昨天 注册的 玩家
		$userquery = "select * from yx_all_user where reg_time >= '".$starttime."' and reg_time <= '".$endtime."' order by id desc";
		$result = mysql_query($userquery);
		while ($row = mysql_fetch_assoc($result)) {
				$user[]=$row;
		}
		// 找出 昨天 支付成功的\注册玩家的\登录的 渠道
		$channellist = array_unique($channellist);

		$conditionstr = "where S.activeflag = 1 and G.activeflag = 1 and G.isonstack = 0 ";
		if ($userid > 0) {
			$conditionstr .= "and S.userid = ".$userid." ";
		}
		if ($channelid > 0) {
			$conditionstr .= "and S.channelid = ".$channelid." ";
		}
		if ($gameid > 0) {
			$conditionstr .= "and S.gameid = ".$gameid." ";
		}
		// 取出所有渠道（用户，游戏，渠道）
		$sourcequery = "select * from yx_tg_source S left join yx_tg_game G on S.gameid = G.gameid left join yx_tg_channel C on S.channelid = C.channelid ".$conditionstr." order by S.id desc";
        $result = mysql_query($sourcequery);
		while ($row = mysql_fetch_assoc($result)) {
			$source[]=$row;
		}

		// 获取当前的所有渠道
		// 对于实时今日统计需要
		$allsourcequery = "select * from yx_tg_source S left join yx_tg_game G on S.gameid = G.gameid where S.activeflag = 1 and G.activeflag = 1 and G.isonstack = 0 order by S.id desc";
        $result = mysql_query($allsourcequery);
		while ($row = mysql_fetch_assoc($result)) {
			$allsource[]=$row;
		}

		$todaydata=array();
		for ($i=0;$i<sizeof($source);$i++) {
			$sourcerow = $source[$i];
			// 取出当前渠道，该用户的所有渠道
			// 不能使用source，使用allsource
			$sourcelist = array();
			for ($j=0;$j<sizeof($allsource);$j++) {
				if ($sourcerow["userid"] == $allsource[$j]["userid"]) {
					$sourcelist[] = $allsource[$j]["sourcesn"];
				}
			}
		
			if (isset($sourcerow["sourcesharerate"]) && $sourcerow["sourcesharerate"] >= 0 && $sourcerow["sourcesharerate"] <= 1) {
				$sharerate = $sourcerow["sourcesharerate"];
			} else {
				$sharerate = 0;
			}
			if (isset($sourcerow["sourcechannelrate"]) && $sourcerow["sourcechannelrate"] >= 0 && $sourcerow["sourcechannelrate"] <= 1) {
				$channelrate = $sourcerow["sourcechannelrate"];
			} else {
				$channelrate = 0;
			}
			// $exsitsn，如果当前渠道没有人玩游戏，为空。如果有人玩游戏，则为原来的值
			$exsitsn = "";
			foreach ($channellist as $k => $v) {
				if ($sourcerow["sourcesn"] == $channellist[$k]) {
					$exsitsn = $sourcerow["sourcesn"];
					break;
				} else {
					continue;
				}
			}
			$dailyjournal = 0;
			$dailyactive = 0;
			$newpeople = 0;
			$paypeople = 0;
			$payrate = 0;
			$dailyincome = 0;
			$voucherje=0;
			if (isset($exsitsn) && $exsitsn != "") {
				$datauser = array();
				$activeuser = array();
				$newuser = array();
				$before_login_user = array();

				// 以前在该渠道上登录的用户
				foreach ($before_login as $key => $value) {
					if($value['agent']==$exsitsn && !in_array($value['userid'],$before_login_user)){
						$before_login_user[]=$value['userid'];
					}
				}
			
				// 统计 昨天 当前渠道中 总流水、总支付人数。（昨天支付成功的订单的 注册渠道 等于 当前渠道的 渠道 ，且 昨天支付成功的订单的 支付渠道 等于 当前渠道的 渠道）
				// $row["regagent"] == $exsitsn，因为要计算当前渠道的流水
				for ($j=0;$j<sizeof($data);$j++) {
					$row = $data[$j];
					if (($row["agent"] == $exsitsn) && (in_array($row["regagent"],$sourcelist))) { 
						$dailyjournal += $row["amount"];
						$dailyjournal -= $row["voucherje"]; //减去代金券金额
						$voucherje += $row["voucherje"]; //代金券统计

						if (!in_array($row["username"],$datauser)) {
							$paypeople += 1;
							$datauser[] = $row["username"];
						}
					}
				}
				// 统计 昨天 当前渠道中 每日活跃用户。（昨天登录的sdk的 渠道 等于 当前渠道的 渠道）
				for ($j=0;$j<sizeof($active);$j++) {
					$row = $active[$j];
					if ($row["agent"] == $exsitsn) {
						if (!in_array($row["userid"],$activeuser)) {
							$dailyactive += 1;
							$activeuser[] = $row["userid"];
						}
					}
				}
				// 统计 昨天 当前渠道中 新增用户数。（昨天注册的玩家的 渠道 等于 当前渠道的 渠道）
				// for ($j=0;$j<sizeof($user);$j++) {
				// 	$row = $user[$j];
				// 	if ($row["agent"] == $exsitsn) {
				// 		if (!in_array($row["id"],$newuser)) {
				// 			$newpeople += 1;
				// 			$newuser[] = $row["id"];
				// 		}
				// 	}
				// }
				// 统计 昨天 当前渠道中 新增用户数。（昨天注册的玩家的 渠道 等于 当前渠道的 渠道）
				for ($j=0;$j<sizeof($active);$j++) {
					$row = $active[$j];
					if ($row["agent"] == $exsitsn) {
						if (!in_array($row["userid"],$before_login_user)) {
							$newpeople += 1;
							$before_login_user[] = $row["userid"];
						}
					}
				}
				$cacheincome = $dailyjournal * $sharerate * (1 - $channelrate);
				$dailyjournal = str_replace(",", "", number_format($dailyjournal, 2));//总流水
				$dailyincome =  str_replace(",", "", number_format($cacheincome, 2));//总收入
				if ($paypeople != 0 && $dailyactive != 0 ) {
					$payrate = floor($paypeople * 100 / $dailyactive);//消费率
				}
			}
			$todaydata[$i]["date"] = date("Y年m月d日");
			$todaydata[$i]["dailyactive"] = $dailyactive;
			$todaydata[$i]["newpeople"] = $newpeople;
			$todaydata[$i]["paypeople"] = $paypeople;
			$todaydata[$i]["payrate"] = $payrate;
			$todaydata[$i]["gamename"] = $sourcerow["gamename"];
			$todaydata[$i]["channelname"] = $sourcerow["channelname"];
			$todaydata[$i]["dailyjournal"] = $dailyjournal;
			$todaydata[$i]["dailyincome"] = $dailyincome;
		}
		return $todaydata;
	}

    public function channel(){
        $userid = $_SESSION['userid'];
        $channelmodel = M('tg_channel');
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
//                 $log_content=date('Y-m-d H:i:s')."\n";
// $log_content.='exsitsn2：'.print_r($channel,1)."\n";
// $log_content.='sql：'.$channelmodel->getlastsql()."\n";
// error_log($log_content, 3, 'test.log');

//         $userModel = M('tg_user');
//         $user = $userModel->where('account="app123"')->find();
//         $log_content=date('Y-m-d H:i:s')."\n";
// $log_content.='exsitsn3：'.print_r($user,1)."\n";
// $log_content.='sql：'.$userModel->getlastsql()."\n";
// error_log($log_content, 3, 'test.log');


        return $channel;
    }
}
?>
