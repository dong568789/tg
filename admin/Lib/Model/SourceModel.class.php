<?php

class SourceModel extends Model
{
    public $gametype;
    public $category;

    public function __construct(){
        parent::__construct();
        $this->gametype = array("网络","单机");
		$this->tgdomain = "http://tg.yxgames.com";
        $this->admindomain = "http://tgadmin.yxgames.com";
		$this->domainhost = "http://www.yxgames.com";
		$this->apkstoreurl = "http://tgadmin.yxgames.com/DataGames/upfiles/basicpackage/";
		$this->apkdownloadurl = "http://tgadmin.yxgames.com/DataGames/upfiles/downloadpackage/";
		$this->texturedownloadurl = "http://tgadmin.yxgames.com/DataGames/apk/upfiles/texture/";
		$this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/";
		$this->packageStoreFolder = "../admin/DataGames/upfiles/basicpackage/";
		$this->downloadStoreFolder = "../admin/DataGames/upfiles/downloadpackage/";
		$this->textureStoreFolder = "../admin/DataGames/upfiles/texture/";
		$this->iconStoreFolder = "../admin/upfiles/gameicon/";
    }


 //推广链接长
    public function getDownloadURL($sourceid){
        $sourcemodel = M("tg_source");
        $where = array('activeflag' =>1);
        $where = array('id' =>$sourceid);
        $games = $sourcemodel->field('sourcesn')->where($where)->find();
        $url = $this->tgdomain."/publicdownload/".$games["sourcesn"];
        return $url;
    }

    //短连接生成请求接口
    function shortenSinaUrl($long_url){
        $apiKey='209678993';//这里是你申请的应用的API KEY，随便写个应用名就会自动分配给你
        $apiUrl='http://api.t.sina.com.cn/short_url/shorten.json?source='.$apiKey.'&url_long='.$long_url;
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        $response = curl_exec($curlObj);
        curl_close($curlObj);
        $json = json_decode($response);
        return $json[0]->url_short;
    }

    //二维码
    function create_erweima($long_url,$gamepinyin) {
        include_once('../Third/qrcode/phpqrcode.php');
        $QRcode = new QRcode();
        // 二维码数据
        $data = $long_url;
        // 生成的文件名
        $path = "upfiles/QRCode/";
        $filename = $path.'tg_'.$gamepinyin.'.png';

        // 纠错级别：L、M、Q、H
        $errorCorrectionLevel = 'L';
        // 点的大小：1到10
        $matrixPointSize = 4;
        //创建一个二维码文件
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        //输入二维码到浏览器
       // QRcode::png($data);
    }
// 生成相应资源的游戏包
    public function createBao($outerConfig){
        $innerConfig=array(
            'userid'=>'',
            'gameid'=>'',
            'channelid'=>'',
        );
        $config=array_merge($innerConfig,$outerConfig);

        $condition=array();
        if($config['userid']){
            $condition['userid'] = $config['userid'];
        }
        if($config['gameid']){
            $condition['gameid'] = $config['gameid'];
        }
        if($config['channelid']){
            $condition['channelid'] = $config['channelid'];
        }
        $condition['activeflag'] = 1;

        $where['isupload'] = 0;
        $where['apkurl'] = '';
        $where['_logic'] = 'or';
        $condition['_complex']=$where;

        // 找出所有没有上传的
        $sourceRecords = $this->where($condition)->select();

        foreach ($sourceRecords as $key => $value) {
            $gameModel = M('tg_game');
            $game = $gameModel->find($value["gameid"]);
            $packagename = $game["packagename"];
            if ($game["gameversion"] != "") {
                $newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$value["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
            } else {
                $newgamename = $game["gamepinyin"]."_".$value["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
            }
            $result = $this->subpackage($packagename,$newgamename,$value['sourcesn']);
            if ($result == "true") {
                $where=array();
                $where["id"] = $value['id'];
                $data=array();
                $data["isupload"] = 1;
                $data["apkurl"] = $newgamename;
                $upload = $this->where($where)->save($data);
            } else {
                echo "系统错误，请重试";
            }
        }
    }

    //分包
    public function subpackage($packagename,$newgamename,$sourcesn){
        $sourfile = $this->packageStoreFolder.$packagename;
        //chmod($sourfile, 0777);       
        $newfile = $this->downloadStoreFolder.$newgamename;
        if(!file_exists($sourfile)){
            $this->ajaxReturn('fail',"母包不存在。",0);
            exit();
        }
        if (!copy($sourfile, $newfile)) {
            $this->ajaxReturn('fail',"无法创建文件，打包失败。",0);
            exit();
        }
        $channelfile=$url."gamechannel";
        fopen($channelfile, "w");
        $zip = new ZipArchive;
        if ($zip->open($newfile) === TRUE) {
            $zip->addFile($url.'gamechannel','META-INF/gamechannel_'.$sourcesn);
            $zip->close();
            return "true";
        } else {
            return "false";
        }
        $this->ajaxReturn('fail',"无法创建文件，打包失败。",0);
        exit();
    }
}
?>
