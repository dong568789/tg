<?php
// 连接数据库
// 测试服务器
$SDKdbhost = '192.168.1.32';
$SDKdbuser = 'root';
$SDKdbpw   = 'hy123456';
$SDKdbname = 'db_youxia_new';

// 测试服务器暂时不使用IP判定
// $outIP = "106.75.141.169";
// $inIP = "10.13.14.83";

//取1天内所有的数据，不可循环取多天
mysql_connect($SDKdbhost,$SDKdbuser,$SDKdbpw);
mysql_select_db($SDKdbname);


// sdk3.0之前，没有增加玩家的有效注册数

// 这里改日期
$nowday = date("Y-m-d H:i:s");
// $nowday = '2016-07-08 00:30:00';
//下面不动
$nowdate = date('Y-m-d',strtotime($nowday.'-1 day')); //统计当天
$starttime_date = date('Y-m-d 00:00:00',strtotime($nowdate));	//统计当天的0点
$endtime_date = date('Y-m-d 23:59:59',strtotime($nowdate));	//统计当天的23:59:59
$starttime = strtotime($starttime_date); //统计当天的0点的时间戳
$endtime = strtotime($endtime_date); //统计当天的23:59:59的时间戳

$channellist=array();
$data=array();
$active=array();
$user=array();
$valuser_imeil = array();
$source=array();

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
$new_channellist=array();

// 取出所有渠道（用户，游戏，渠道）
$sourcequery = "select * from yx_tg_source S left join yx_tg_game G on S.gameid = G.gameid where S.activeflag = 1 and G.activeflag = 1 and G.isonstack = 0  and S.createtime<='".$endtime_date."' order by S.id desc";
$result = mysql_query($sourcequery);
$source_num = mysql_num_rows($result);
$itemSource = array();
while ($row = mysql_fetch_assoc($result)) {
	$source[]=$row;
	$itemSource[] = $row['sourcesn'];
}

// 找出统计之前的所有登录信息
// group by user,agent 可以去重
$before_login = array();
if(!empty($itemSource)){
	$strSource = '\''.str_replace(',', '\',\'', implode(',', $itemSource)).'\'';
	$before_login_query = "select  userid,agent from yx_sdk_logininfo where login_time < '".$starttime."' AND agent in({$strSource})  group by userid,agent order by id desc";
	$before_login_result = mysql_query($before_login_query);
	while ($row = mysql_fetch_assoc($before_login_result)) {
		$before_login[]=$row;
	}
}

for ($i=0;$i<sizeof($source);$i++) {
	$sourcerow = $source[$i];

	// 取出当前渠道，该用户的所有渠道
	$sourcelist = array();
	for ($j=0;$j<sizeof($source);$j++) {
		if ($sourcerow["userid"] == $source[$j]["userid"]) {
			$sourcelist[] = $source[$j]["sourcesn"];
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
			$new_channellist[]=$sourcerow['id'];
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
	$valpeople = 0;
	$getcoin = 0;
	$getcoin_ischeck = 0;
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
		$dailyincome =  str_replace(",", "", number_format($cacheincome, 2)); //总收入
		if ($paypeople != 0 && $dailyactive != 0 ) {
			$payrate = floor($paypeople * 100 / $dailyactive); //消费率
		}
	}

	$query = "insert into yx_tg_dailyaccount (
					date,
					sourceid,
					userid,
					channelid,
					gameid,
					channelrate,
					sharerate,
					dailyjournal,
					dailyactive,
					valpeople,
					newpeople,
					paypeople,
					payrate,
					dailyincome,
					activeflag,
					createtime,
					createuser,
					getcoin,
					getcoin_ischeck,
					voucherje
			)values( 
					'".$nowdate."', 
					'".$sourcerow["id"]."', 
					'".$sourcerow["userid"]."', 
					'".$sourcerow["channelid"]."',
					'".$sourcerow["gameid"]."', 
					'".$channelrate."', 
					'".$sharerate."', 
					'".$dailyjournal."', 
					'".$dailyactive."', 
					'".$valpeople."', 
					'".$newpeople."', 
					'".$paypeople."', 
					'".$payrate."', 
					'".$dailyincome."', 
					'1', 
					now(), 
					'admin',
					'".$getcoin."',
					'".$getcoin_ischeck."',
					'".$voucherje."')";
	$result = mysql_query($query);
}

	mysql_close();
	
	// 输出日志
	$log_file='/var/www/tg/log/tgbatch/'.date('Y-m-d').'.log';
	$log_content=date('Y-m-d H:i:s')."\n";
	$log_content.="统计当天：".$nowdate."\n";
	$log_content.="渠道个数（即dailyaccount的个数）：".$source_num."\n";
	$log_content.="渠道sql：\n".print_r($sourcequery,1)."\n";

$log_content.="活跃渠道个数：".count($channellist)."\n";
$log_content.="活跃渠道array：\n".print_r($channellist,1)."\n";

$log_content.="有效渠道个数：".count($new_channellist)."\n";
$log_content.="有效渠道array：\n".print_r($new_channellist,1)."\n";

error_log($log_content, 3, $log_file);

// 页面输出结果
// echo str_replace("\n","<br/>",$log_content);
?>
