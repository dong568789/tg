<?php

header("Content-type: text/html; charset=utf-8");

// // 每日流水相关
// // 当天的资源数
// SELECT * FROM yx_tg_source S left join yx_tg_game G on S.gameid = G.gameid where S.activeflag = 1 and G.activeflag = 1 and G.isonstack = 0  and S.createtime<='2016-07-07 23:59:59' order by S.id desc;

// // 当天的每日统计
// SELECT * FROM yx_tg_dailyaccount_copy WHERE date='2016-07-08' ORDER BY sourceid asc;

// // 当天的有效每日统计
// SELECT * FROM yx_tg_dailyaccount_copy WHERE date='2016-07-08' and (dailyjournal!=0 or dailyactive!=0 or newpeople!=0 or paypeople!=0 or payrate!=0 or dailyincome!=0 or valpeople!=0) ORDER BY sourceid asc;

// // 由于游戏下架
// SELECT * FROM yx_tg_dailyaccount D LEFT JOIN yx_tg_game G ON G.gameid=D.gameid WHERE D.date='2016-07-07' and G.isonstack!=0;


// -- 如：今天是 2016-07-29 13:00:00
// set @mycurdate:=curdate(); -- 今天年月日2016-07-29
// -- SELECT date_format(@mycurdate,'%Y-%m-%d %H:%i:%s'); 

// set @yesdate:=date_sub(@mycurdate, interval 1 day);	-- 昨天年月日2016-07-28
// -- SELECT date_format(@yesdate,'%Y-%m-%d %H:%i:%s'); 

// set @yesdatenig:=date_sub(@mycurdate, interval 1 second);	-- 昨天晚上2016-07-28 23:59:59
// -- SELECT date_format(@yesdatenig,'%Y-%m-%d %H:%i:%s');

// set @yesdatemortime:=unix_timestamp(@yesdate);	-- 昨天早上2016-07-28 00:00:00的时间戳1469635200
// -- SELECT @yesdatemortime,from_unixtime(@yesdatemortime);

// set @yesdatenigtime:=unix_timestamp(curdate())-1;	-- 昨天晚上2016-07-28 23:59:59的时间戳1469721599
// -- SELECT @yesdatenigtime,from_unixtime(@yesdatenigtime);


// SELECT SUM(ttb) from yx_coin_use WHERE create_time>=@yesdatemortime AND create_time<=@yesdatenigtime

// SELECT * FROM yx_tg_coinlog as a LEFT JOIN yx_tg_user as b ON a.userid = b.userid  
// WHERE a.createtime >=@yesdate AND a.createtime<=@yesdatenig ORDER BY a.amount DESC LIMIT 10

// SELECT U.account,C.channelname,sourceid,S.sourcesn,max(dailyjournal),dailyincome 
// FROM yx_tg_dailyaccount D 
// LEFT JOIN yx_tg_user U ON U.userid=D.userid
// LEFT JOIN yx_tg_channel C ON C.channelid=D.channelid
// LEFT JOIN yx_tg_source S ON S.id=D.sourceid
// WHERE date= @yesdate;



// 连接数据库
// 测试服务器
// $SDKdbhost = '192.168.1.32';
// $SDKdbuser = 'root';
// $SDKdbpw   = 'hy123456';
// $SDKdbname = 'db_youxia_new';

// //正式服务器
$SDKdbhost = '10.13.58.56';
$SDKdbuser = 'User_youxia_tg';
$SDKdbpw   = 'YOUxiadb@2016';
$SDKdbname = 'db_youxia_new';

//取1天内所有的数据，不可循环取多天
mysql_connect($SDKdbhost,$SDKdbuser,$SDKdbpw);
mysql_select_db($SDKdbname);
 

$query0 = 'set names "utf8"';
mysql_query($query0);

$count = 0;

echo '<table>';
echo '<tr>';
echo '<td width="50">序号</td>';
echo '<td width="50">ID</td>';
echo '<td width="200">账号</td>';
echo '<td width="200">真实姓名</td>';
echo '<td width="300">公司</td>';
echo '<td width="200">电话</td>';
echo '<td width="200">邮箱</td>';
echo '</tr>';

$query = 'SELECT * from yx_tg_user WHERE activeflag=1';
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
	$flag = 0; //没有流水
	$query1 = 'SELECT * from yx_tg_dailyaccount WHERE userid="'.$row['userid'].'" and date>="2016-06-20" ';
	$result1 = mysql_query($query1);
	while ($row1 = mysql_fetch_assoc($result1)) {
		if($row1['dailyjournal']>0 || $row1['dailyactive']>0){
			$flag = 1;
			break;
		}	
	}

	if($flag == 0){
		$count = $count+1;
		echo '<tr>';
		echo '<td>'.$count.'</td>';
		echo '<td>'.$row['userid'].'</td>';
		echo '<td>'.$row['account'].'</td>';
		echo '<td>'.$row['realname'].'</td>';
		echo '<td>'.$row['companyname'].'</td>';
		echo '<td>'.$row['contactmobile'].'</td>';
		echo '<td>'.$row['contactemail'].'</td>';
		echo '</tr>';
	}
}

echo '</table>';


// 删除用户


?>
