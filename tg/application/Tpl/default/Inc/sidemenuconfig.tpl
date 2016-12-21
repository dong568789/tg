<?php
if($userpid==0){
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
}else{
	$profile_nav = array();
}

if($userpid==0){
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
		),
		"用户查询" => array(
			"title" => "用户查询",
			"icon" => "account",
			"sub" => array(
				'充值查询' => array(
					'title' => '充值查询',
					'url' => "/recharge/",
				),
				'注册查询' => array(
					'title' => '注册查询',
					'url' => "/registration/",
				),
			),
		),
		"用户充值" => array(
			"title" => "用户充值",
			"icon" => "paypal",
			"sub" => array(
				'游侠币管理' => array(
					'title' => '游侠币管理',
					'url' => "/coin/",
				),
				'代金券管理' => array(
					'title' => '代金券管理',
					'url' => "/voucher/",
				),
			)
		),
		"操作指南" => array(
			"title" => "操作指南",
			"url" => "/guide/",
			"icon" => "lamp"
		),
		"公告中心" => array(
			"title" => "公告中心",
			"url" => "/announce/",
			"icon" => "notifications"
		),
	);
}else{
	$page_nav = array(
		"推广资源" => array(
			"title" => "我的推广",
			"url" => "/source/",
			"icon" => "layers"
		),
		"数据统计" => array(
			"title" => "数据统计",
			"url" => "/statistics/",
			"icon" => "widgets"
		),
		"用户查询" => array(
			"title" => "用户查询",
			"icon" => "account",
			"sub" => array(
				'充值查询' => array(
					'title' => '充值查询',
					'url' => "/recharge/",
				),
				'注册查询' => array(
					'title' => '注册查询',
					'url' => "/registration/",
				),
			),
		),
	);
}

?>