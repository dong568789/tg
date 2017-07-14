<?php
//连接数据库
//正式服务器

$dbconfig = include dirname(__FILE__).'/tg/application/Conf/config_db.php';
$SDKdbhost = $dbconfig['DB_HOST'];
$SDKdbuser = $dbconfig['DB_USER'];
$SDKdbpw   = $dbconfig['DB_PWD'];
$SDKdbname = $dbconfig['DB_NAME'];


mysql_connect($SDKdbhost,$SDKdbuser,$SDKdbpw);
mysql_select_db($SDKdbname);

$forcequery = "select * from yx_cps_package where isforcepackage = 1 and isforced = 0 order by packageid desc";
$result = mysql_query($forcequery);
while ($row = mysql_fetch_assoc($result)) {
	$force[]=$row;
}
$nowtime = time();
$anhourlater = strtotime("-1 hour");
foreach ($force as $k => $v) {
	if (strtotime($v["forcetime"]) >= $anhourlater && strtotime($v["forcetime"]) < $nowtime) {

		$gamequery = "update yx_cps_game set gamesize = ".$v["gamesize"].", packagename = '".$v["packagename"]."', gameversion ='".$v["gameversion"]."', packageversion = '".$v["packageversion"]."' where gameid = ".$v["gameid"];
		mysql_query($gamequery);
		$forceupdatequery = "update yx_cps_package set isforced = 1 where packageid = ".$v["packageid"];
		mysql_query($forceupdatequery);
		$forcepackagequery = "select * from yx_cps_forcepackage where isforce = 0 and gameid = ".$v["gameid"];
		$forcepackageresult = mysql_query($forcepackagequery);
		while ($row = mysql_fetch_assoc($forcepackageresult)) {
			$forcepackage[]=$row;
		}
		foreach ($forcepackage as $k1 => $v1) {
			$sourcequery = "update yx_cps_source set isupload = 1, is_cdn_submit = '".$v1["is_cdn_submit"]."', apkurl = '' where userid = ".$v1["userid"]." and channelid = ".$v1["channelid"]." and gameid = ".$v1["gameid"];
			mysql_query($sourcequery);
			$forcepackageupdatequery = "update yx_cps_forcepackage set isforce = 1 where id = ".$v1["id"];
			mysql_query($forcepackageupdatequery);
		}
	} else {
		continue;
	}
}

?>
