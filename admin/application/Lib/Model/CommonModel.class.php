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
		$this->tgdomain = C('tgdomain');
        $this->admindomain = C('admindomain');
		$this->domainhost = C('domainhost');

        $this->apkdownloadcdnurl = C('apkdownloadcdnurl'); //注意:cdn分包，只能是线上测试
        $this->iconurl = C('iconurl'); //图标单独上传到admin/upfiles

        // 测试服务器上使用
        $this->apkstoreurl = C('apkstoreurl'); //母包
        $this->apkdownloadurl = C('apkdownloadurl'); //分包
        $this->texturedownloadurl = C('texturedownloadurl'); //素材包
        $this->gamebgurl = C('gamebgurl'); //游戏背景
        $this->screenshoturl = C('screenshoturl'); //游戏截图

        $this->packageStoreFolder = C('packageStoreFolder');
        $this->downloadStoreFolder = C('downloadStoreFolder');
        $this->textureStoreFolder = C('textureStoreFolder');
        $this->iconStoreFolder = C('iconStoreFolder');
        $this->gamebgStoreFolder = C('gamebgStoreFolder');
        $this->screenshotStoreFolder = C('screenshotStoreFolder');
    }
}
?>
