<?php

return array(
        'sign' => "admin",  //用于密码加密
        'AUTH_KEY' => '9e13yK8RN2M0lKP8CLRLhGs468d1WMaSlbDeCcI_1tsdk@you@sdk@2015',
        'LOGIN_KEY' => '5dfb49dkm1c25n7cgh6s_tg',
        'appkeysign' => "chuyou_sdk_2014",
        'tgdomain' => "http://tg.yxgames.com",
        'admindomain' => "http://tgadmin.yxgames.com",
        'domainhost' => "http://www.yxgames.com",
        'iconurl' => "http://tgadmin.yxgames.com/upfiles/gameicon/", //图标单独上传到admin/upfiles
        'apkdownloadcdnurl' => "http://downloadcdn.yxgames.com/dataGames/apk/upfiles/downloadpackage/", //注意:cdn分包，只能是线上测试

        // 测试服务器上使用
         'apkstoreurl' => "http://tgadmin.yxgames.com/DataGames/upfiles/basicpackage/", //母包
         'apkdownloadurl' => "http://tgadmin.yxgames.com/DataGames/upfiles/downloadpackage/", //分包
         'texturedownloadurl' => "http://tgadmin.yxgames.com/DataGames/upfiles/texture/", //素材包
         'gamebgurl' => "http://tgadmin.yxgames.com/DataGames/upfiles/gamebg/",  //游戏背景
         'screenshoturl' => "http://tgadmin.yxgames.com/DataGames/upfiles/screenshot/", //游戏截图
         'diylogourl' => "http://tgadmin.yxgames.com/DataGames/upfiles/diylogo/", //自定义合作会员logo
         'mountedFolder' => 'http://tgadmin.yxgames.com/DataGames/mountedfiles/downloadpackage/',

        // 正式服务器上使用
//        'apkstoreurl' => "http://download.yxgames.com/dataGames/apk/upfiles/basicpackage/", //母包
//        'apkdownloadurl' => "http://download.yxgames.com/dataGames/apk/upfiles/downloadpackage/", //分包
//        'texturedownloadurl' => "http://download.yxgames.com/dataGames/apk/upfiles/texture/", //素材包
//        'gamebgurl' => "http://downloadcdn.yxgames.com/images/upfiles/gamebg/", //游戏背景
//        'screenshoturl' => "http://downloadcdn.yxgames.com/images/upfiles/screenshot/", //游戏截图
//        'diylogourl' => "http://img.yxgames.com/DataGames/upfiles/diylogo/", //自定义合作会员logo
        'packageStoreFolder' => "../admin/DataGames/upfiles/basicpackage/",
        'downloadStoreFolder' => "../admin/DataGames/upfiles/downloadpackage/",
        'textureStoreFolder' => "../admin/DataGames/upfiles/texture/",
        'iconStoreFolder' => "../admin/upfiles/gameicon/",
        'gamebgStoreFolder' => "../admin/DataGames/upfiles/gamebg/",
        'diylogoStoreFolder' => "../admin/DataGames/upfiles/diylogo/",
        'screenshotStoreFolder' => "../admin/DataGames/upfiles/screenshot/",
        'tgPackageFolder' => '../admin/DataGames/upfiles/tgpackage/',

        'signCheckFolder' => "/var/www/admin/DataGames/upfiles/basicpackage/",
        'sourceType' => array(
                1 => '公会',
                3 => '平台YXGAMES',
                4 => 'CPS',
                5 => '应用商店'
        )
);