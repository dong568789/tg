<?php
$page_nav = array();

if ($checkgame == 'ok') {
    $page_nav["游戏管理"]["title"] = "游戏管理";
    $page_nav["游戏管理"]["icon"] = "gamepad";
    if ($checknewgame == 'ok') {
        $page_nav["游戏管理"]["sub"]["新增游戏"]["title"] = "新增游戏";
        $page_nav["游戏管理"]["sub"]["新增游戏"]["url"] = "/newgame/";

    }
    if ($checkgameall == 'ok') {
        $page_nav["游戏管理"]["sub"]["所有游戏"]["title"] = "所有游戏";
        $page_nav["游戏管理"]["sub"]["所有游戏"]["url"] = "/gameall/";

    }
}

if ($checkbalance == 'ok') {
    $page_nav["财务管理"]["title"] = "财务管理";
    $page_nav["财务管理"]["icon"] = "money-box";
    if ($checkbalanceall == 'ok') {
        $page_nav["财务管理"]["sub"]["所有结算单"]["title"] = "所有结算单";
        $page_nav["财务管理"]["sub"]["所有结算单"]["url"] = "/balanceall/";

    }
    if ($checkChannel == 'ok') {
        $page_nav["财务管理"]["sub"]["渠道流水汇总"]["title"] = "渠道流水汇总";
        $page_nav["财务管理"]["sub"]["渠道流水汇总"]["url"] = U('/Statistics/index');

    }
}

if ($checkuser == 'ok') {
    $page_nav["用户管理"]["title"] = "用户管理";
    $page_nav["用户管理"]["icon"] = "account-box";
    if ($newuser == 'ok') {
        $page_nav["用户管理"]["sub"]["新增用户"]["title"] = "新增用户";
        $page_nav["用户管理"]["sub"]["新增用户"]["url"] = "/newuser/";

    }
    if ($checkuserall == 'ok') {
        $page_nav["用户管理"]["sub"]["所有用户"]["title"] = "所有用户";
        $page_nav["用户管理"]["sub"]["所有用户"]["url"] = "/userall/";

    }
     if ($checksendmail == 'ok') {
        $page_nav["用户管理"]["sub"]["发送信息"]["title"] = "发送信息";
        $page_nav["用户管理"]["sub"]["发送信息"]["url"] = "/sendmail/";

    }
}

if ($checkannounce == 'ok') {
    $page_nav["公告管理"]["title"] = "公告管理";
    $page_nav["公告管理"]["icon"] = "notifications";
    if ($announceall == 'ok') {
        $page_nav["公告管理"]["sub"]["所有公告"]["title"] = "所有公告";
        $page_nav["公告管理"]["sub"]["所有公告"]["url"] = "/announceall/";

    }
     if ($newannounce == 'ok') {
        $page_nav["公告管理"]["sub"]["新增公告"]["title"] = "新增公告";
        $page_nav["公告管理"]["sub"]["新增公告"]["url"] = "/newannounce/";

    }
     if ($announcetype == 'ok') {
        $page_nav["公告管理"]["sub"]["新增公告类型"]["title"] = "新增公告类型";
        $page_nav["公告管理"]["sub"]["新增公告类型"]["url"] = "/announcetype/";

    }


}


if ($checkguide == 'ok') {
    $page_nav["操作指南管理"]["title"] = "操作指南管理";
    $page_nav["操作指南管理"]["icon"] = "help";
    if ($newguide == 'ok') {
        $page_nav["操作指南管理"]["sub"]["新增操作指南"]["title"] = "新增操作指南";
        $page_nav["操作指南管理"]["sub"]["新增操作指南"]["url"] = "/newguide/";

    }
    if ($checkguidedetail == 'ok') {
        $page_nav["操作指南管理"]["sub"]["操作指南详情"]["title"] = "操作指南详情";
        $page_nav["操作指南管理"]["sub"]["操作指南详情"]["url"] = "/guidedetail/";

    }
}

if ($checkother == 'ok') {
    $page_nav["其他项目管理"]["title"] = "其他项目管理";
    $page_nav["其他项目管理"]["icon"] = "settings";
    if ($checkgamecategory == 'ok') {
        $page_nav["其他项目管理"]["sub"]["游戏类型"]["title"] = "游戏类型";
        $page_nav["其他项目管理"]["sub"]["游戏类型"]["url"] = "/gamecategory/";

    }
    if ($checkgametag == 'ok') {
        $page_nav["其他项目管理"]["sub"]["游戏标签"]["title"] = "游戏标签";
        $page_nav["其他项目管理"]["sub"]["游戏标签"]["url"] = "/gametag/";

    }
}


if ($checklog == 'ok') {
   $page_nav["日志管理"]["title"] = "日志管理";
    $page_nav["日志管理"]["icon"] = "file-text";
    if ($log == 'ok') {
        $page_nav["日志管理"]["sub"]["登录日志"]["title"] = "登录日志";
        $page_nav["日志管理"]["sub"]["登录日志"]["url"] = "/log/";

    }
    if ($checkoperate == 'ok') {
         $page_nav["日志管理"]["sub"]["操作日志"]["title"] = "操作日志";
         $page_nav["日志管理"]["sub"]["操作日志"]["url"] = "/operate/";

    }
}


?>