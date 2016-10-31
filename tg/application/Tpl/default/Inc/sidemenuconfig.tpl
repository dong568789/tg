<?php
$profile_nav = array(
	"用户资料" => array(
		"title" => "用户资料",
		"url" => "/profile/",
		"icon" => "account"
	),
	"收件箱" => array(
		"title" => "收件箱",
		"url" => "/message/",
		"icon" => "email"
	),
	"账户管理" => array(
		"title" => "账户管理",
		"url" => "/account/",
		"icon" => "folder-person"
	),
	"安全设置" => array(
		"title" => "安全设置",
		"url" => "/settings/",
		"icon" => "settings"
	)
);

$page_nav = array(
	"渠道管理" => array(
		"title" => "渠道管理",
		"url" => "/channel/",
		"icon" => "view-compact"
	),
	"推广资源" => array(
		"title" => "推广资源",
		"url" => "/source/",
		"icon" => "layers"
	),
	"数据统计" => array(
		"title" => "数据统计",
		"url" => "/statistics/",
		"icon" => "widgets"
	),
	"结算中心" => array(
		"title" => "结算中心",
		"url" => "/balance/",
		"icon" => "money-box"
	)
);

$page_nav["用户查询"]["title"] = "用户查询";
$page_nav["用户查询"]["icon"] = "account";
$page_nav["用户查询"]["sub"]["充值查询"]["title"] = "充值查询";
$page_nav["用户查询"]["sub"]["充值查询"]["url"] = "/recharge/";
$page_nav["用户查询"]["sub"]["注册查询"]["title"] = "注册查询";
$page_nav["用户查询"]["sub"]["注册查询"]["url"] = "/registration/";

$page_nav["用户充值"]["title"] = "用户充值";
$page_nav["用户充值"]["icon"] = "paypal";
$page_nav["用户充值"]["sub"]["游侠币管理"]["title"] = "游侠币管理";
$page_nav["用户充值"]["sub"]["游侠币管理"]["url"] = "/coin/";
$page_nav["用户充值"]["sub"]["代金券管理"]["title"] = "代金券管理";
$page_nav["用户充值"]["sub"]["代金券管理"]["url"] = "/voucher/";


$page_nav['操作指南']=array(
	"title" => "操作指南",
	"url" => "/guide/",
	"icon" => "lamp"
);
$page_nav['公告中心']=array(
	"title" => "公告中心",
	"url" => "/announce/",
	"icon" => "notifications"
);

?>