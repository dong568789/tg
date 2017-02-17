<?php

class CommonModel extends Model
{
    public $tgdomain;
    public $admindomain;
    public $domainhost;
    public $apkstoreurl;
    public $apkdownloadurl;
    public $texturedownloadurl;
    public $iconurl;
    public $packageStoreFolder;
    public $downloadStoreFolder;
    public $textureStoreFolder;
    public $iconStoreFolder;

    public function __construct(){
        parent::__construct();
		$this->tgdomain = "http://tg.yxgames.com";
        $this->admindomain = "http://tgadmin.yxgames.com";
		$this->domainhost = "http://www.yxgames.com";

        $this->apkdownloadcdnurl = "http://downloadcdn.yxgames.com/dataGames/apk/upfiles/downloadpackage/"; //注意:cdn分包，只能是线上测试
        $this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/"; //图标单独上传到admin/upfiles

        // 测试服务器上使用
        $this->apkstoreurl = "http://tgadmin.yxgames.com/DataGames/upfiles/basicpackage/"; //母包
        $this->apkdownloadurl = "http://tgadmin.yxgames.com/DataGames/upfiles/downloadpackage/"; //分包
        $this->texturedownloadurl = "http://tgadmin.yxgames.com/DataGames/upfiles/texture/"; //素材包
        $this->gamebgurl = "http://tgadmin.yxgames.com/DataGames/upfiles/gamebg/"; //游戏背景
        $this->screenshoturl = "http://tgadmin.yxgames.com/DataGames/upfiles/screenshot/"; //游戏截图

        // 正式服务器上使用
        // $this->apkstoreurl = "http://download.yxgames.com/dataGames/apk/upfiles/basicpackage/"; //母包
        // $this->apkdownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/downloadpackage/"; //分包
        // $this->texturedownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/texture/"; //素材包
        // $this->gamebgurl = "http://img.yxgames.com/images/upfiles/gamebg/"; //游戏背景
        // $this->screenshoturl = "http://img.yxgames.com/images/upfiles/screenshot/"; //游戏截图

        $this->packageStoreFolder = "../admin/DataGames/upfiles/basicpackage/";
        $this->downloadStoreFolder = "../admin/DataGames/upfiles/downloadpackage/";
        $this->textureStoreFolder = "../admin/DataGames/upfiles/texture/";
        $this->iconStoreFolder = "../admin/upfiles/gameicon/";
        $this->gamebgStoreFolder = "../admin/DataGames/upfiles/gamebg/";
        $this->screenshotStoreFolder = "../admin/DataGames/upfiles/screenshot/";
    }

}
?>
