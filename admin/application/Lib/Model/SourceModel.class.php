<?php

class SourceModel extends CommonModel
{
    public function __construct(){
        parent::__construct();
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


    // -------------资源包下载-------------------------
    // 生成资源包
    public function createSourePackage($sourcesn){
        $sourcemodel = M('tg_source');
        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();

        $gamemodel = M('tg_game');
        $game = $gamemodel->find($source["gameid"]);
        $packagename = $game["packagename"];
        if ($game["gameversion"] != "") {
            $newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
        } else {
            $newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
        }
        $result = $this->subpackage($packagename,$newgamename,$sourcesn);
        if ($result['code'] == 1) {
            $data["isupload"] = 1;
            $data["apkurl"] = $newgamename;
            $upload = $sourcemodel->where($map)->save($data);

            // 第一次分包的时候cdn提交
            $this->cdnsubmit($sourcesn,$newgamename);

            return array('code' => 1, 'msg' => '生成资源包成功。', 'data' => $this->apkdownloadurl.$newgamename );
        } else {
            return array('code' => 0, 'msg' => '生成资源包失败。');
        }
    }

    // 生成包
    public function subpackage($packagename,$newgamename,$sourcesn){
        $sourfile = $this->packageStoreFolder.$packagename;      
        $newfile = $this->downloadStoreFolder.$newgamename;
        if(!file_exists($sourfile)){
            return array('code' => 0, 'msg' => '母包不存在。');
        }
        if (!copy($sourfile, $newfile)) {
            return array('code' => 0, 'msg' => '无法创建文件，打包失败。');
        }
        $channelfile=$url."gamechannel";
        fopen($channelfile, "w");
        try{
            $zip = new ZipArchive;
            if ($zip->open($newfile) === TRUE) {
                $zip->addFile($url.'gamechannel','META-INF/gamechannel_'.$sourcesn);
                $zip->close();

                return array('code' => 1, 'msg' => '生成包成功');
            } else {
                return array('code' => 0, 'msg' => '生成包失败。');
            }
        }catch(Exception $e){
            return array('code' => 0, 'msg' => '生成包过程中发生异常');
        }
    }

    // cdn提交接口
    public function cdnsubmit($sourcesn,$newgamename,$isforce){
        // 允许用户提交cdn才提交cdn
        $sourceModel = M('tg_source');
        $where = array('sourcesn'=>$sourcesn);
        $is_allow_cdn = $sourceModel->alias('S')
                    ->field('U.is_allow_cdn')
                    ->join(C('DB_PREFIX').'tg_user U on U.userid=S.userid')
                    ->where($where)
                    ->find();
        if($is_allow_cdn['is_allow_cdn'] == '1'){
            if ($isforce) {
                // 输出日志
                $log_file = $_SERVER['DOCUMENT_ROOT'].'/../tg/log/cdn/'.date('Y-m-d').'-sub.log';
                $log_content=date('Y-m-d H:i:s')."\n";
                $log_content.="强更cdn提交：\n";
                $log_content.="sourcesn：".$sourcesn."\n";
                $log_content.="newgamename：".$newgamename."\n";
                error_log($log_content, 3, $log_file);

                /*************CDN*******************/
                $Url = 'http://c.yxgames.com/api/cdn';
                $Callback = $this->admindomain.'/?m=game&a=forcecdncallback&sourcesn='.$sourcesn;
                $packageurl  = $this->apkdownloadcdnurl.$newgamename;
                $Params = array(
                    'url'=>$packageurl,
                    'callback'=>$Callback,
                    'of'=>'json',
                );
                $rs = httpreqCommon($Url, http_build_query($Params),'post');
            /****************CDN*******************/
            }else{
                // 输出日志
                $log_file = $_SERVER['DOCUMENT_ROOT'].'/../tg/log/cdn/'.date('Y-m-d').'-sub.log';
                $log_content=date('Y-m-d H:i:s')."\n";
                $log_content.="下载包cdn提交：\n";
                $log_content.="sourcesn：".$sourcesn."\n";
                $log_content.="newgamename：".$newgamename."\n";
                error_log($log_content, 3, $log_file);

                /*************CDN*******************/
                $Url = 'http://c.yxgames.com/api/cdn';
                $Callback = $this->admindomain.'/?m=game&a=cdncallback&sourcesn='.$sourcesn;
                $packageurl  = $this->apkdownloadcdnurl.$newgamename;
                $Params = array(
                    'url'=>$packageurl,
                    'callback'=>$Callback,
                    'of'=>'json',
                );
                $rs = httpreqCommon($Url, http_build_query($Params),'post');
                /****************CDN*******************/
            }
           
        }
    }

}
?>
