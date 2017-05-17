<?php

class SourceModel extends CommonModel
{
    public function __construct(){
        parent::__construct();
    }

    public function index($userid){
        $categorymodel = M("tg_gamecategory");
        $tagmodel = M("tg_gametag");
        $channelmodel = M("tg_channel");
        $usermodel = M("tg_user");
        $game['category'] = $categorymodel->where('activeflag = 1')->order("createtime desc")->select();
        $game['tag'] = $tagmodel->where('activeflag = 1')->order("createtime desc")->select();

        $game['channel'] = $channelmodel->where("activeflag = 1 and userid = $userid")->order("createtime desc")->select();
        $channelid = $game['channel'][0]["channelid"];

        $game['gamestr'] = $this->selectGame('全部',0,'全部',0,$channelid,'asc');
        $game['sourcestr'] = $this->selectSource($channelid);
        return $game;
    }

    // 子账号首页
    public function indexson($userid){

        $usermodel = M("tg_user");

        $where = array('userid' => $userid);
        $channelid = $usermodel->field('channelid')->where($where)->find();
        $channelid = $channelid['channelid'];

        $game['sourcestr'] = $this->selectSource($channelid);
        return $game;
    }

    //游戏分类筛选
    public function selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid,$order,$order_hot='',$userid){
        $gamemodel = M("tg_game");
        $sourcemodel = M("tg_source");
        if($gametype !== '全部'){
            $map['gametype'] = $gametype;
        }
        if($gamecategory != 0){
            $map['gamecategory'] = $gamecategory;
        }

        if($gamesize == '0-10M'){
            $map['gamesize'] = array(array('egt',0),array('elt',10)) ;
        }elseif($gamesize == '10-30M'){
            $map['gamesize'] = array(array('egt',10),array('elt',30)) ;
        }elseif($gamesize == '30-50M'){
            $map['gamesize'] = array(array('egt',30),array('elt',50)) ;
        }elseif($gamesize == '50-100M'){
            $map['gamesize'] = array(array('egt',50),array('elt',100)) ;
        }elseif($gamesize == '大于100M'){
            $map['gamesize'] = array('gt',100) ;
        }

        if($gametag != 0){
            $map['gametag'] = $gametag;
        }

        if(!empty($order_hot)){
            $ordrestr = ($order_hot == 'asc' ? 'G.gameauthority ASC' : 'G.gameauthority DESC');

        }
        $map['G.activeflag'] = 1;
        $games = $gamemodel->alias("G")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->field('G.*,C.categoryname,T.tagname')
            ->where($map)
            ->order($ordrestr)
            ->select();

        $sourcecondition["userid"] = $userid;
        $sourcecondition["channelid"] = $channelid;
        $source = $sourcemodel->where($sourcecondition)->order("id desc")->select();
        $sourceItem = array();
        foreach($source as $value){
            $sourceItem[] = $value['gameid'];
        }
        foreach($games as $k1 =>$v1){
            $games[$k1]['isapply'] = 0;
            if(in_array($v1['gameid'], $sourceItem)){
                $games[$k1]['isapply'] = 1;
            }

            $isonstack[$k1] = $v1["isonstack"];
            //$gameauthority[$k1] = $v1["gameauthority"];
            $createtime[$k1] = $v1["createtime"];
            $isapply[$k1] = $games[$k1]["isapply"]; //$v1在循环之前已经确定。$v1["isapply"]!=$games[$k1]['isapply']
        }
        if(empty($order_hot)) {
            if ($order == 'asc') {
                array_multisort($isonstack, SORT_ASC, $isapply, SORT_ASC, $createtime, SORT_DESC, $games);
            } else {
                array_multisort($isonstack, SORT_DESC, $isapply, SORT_ASC, $createtime, SORT_DESC, $games);
            }
        }

        $gamestr = $this->createGameStr($games,"all");
        return $gamestr;
    }
    //搜索游戏
    public function searchGame($content,$channelid){
        $userid = $_SESSION['userid'];
        $gamemodel = M("tg_game");
        $sourcemodel = M("tg_source");
        $where = '';
        $where.= "G.activeflag = '1' AND(";
        $where.= "gamename like '%".$content."%'";
        $where.= "OR gamepinyin like'%".$content."%'";
        $where.= "OR gametype like '%".$content."%'";
        $where.= "OR gamecategory like '%".$content."%'";
        $where.= "OR gametag like '%".$content."%'";
        $where.= "OR gamesize like '%".$content."%'";
        $where.= "OR sharetype like '%".$content."%')";
        $games = $gamemodel->alias("G")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->where($where)
            ->field('G.*,C.categoryname,T.tagname')
            ->order("G.gameauthority desc")
            ->select();
        $sourcecondition["userid"] = $userid;
        $sourcecondition["channelid"] = $channelid;
        $source = $sourcemodel->where($sourcecondition)->order("id desc")->select();
        foreach($games as $k1 =>$v1){
            $games[$k1]['isapply'] = 0;
            foreach($source as $k2 =>$v2){
                if($v1['gameid'] == $v2['gameid']){
                    $games[$k1]['isapply'] = 1;
                }
            }
            $isonstack[$k1] = $v1["isonstack"];
            $gameauthority[$k1] = $v1["gameauthority"];
            $isapply[$k1] = $games[$k1]["isapply"]; //$v1在循环之前已经确定。$v1["isapply"]!=$games[$k1]['isapply']
        }
        array_multisort($isonstack, SORT_ASC, $isapply, SORT_ASC, $gameauthority, SORT_DESC, $games);
        $gamestr = $this->createGameStr($games,"all");
        return $gamestr;
    }
    //TAB2渠道搜索
    public function selectSource ($channelid) {
        $sourcemodel = M("tg_source");
        $map['S.channelid'] = $channelid;
        $map['S.activeflag'] = 1;
        $map['G.activeflag'] = 1;

        $games = $sourcemodel->alias("S")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->where($map)
            ->field('*,S.id as sourceid')
            ->order("G.gameauthority desc")
            ->select();
        $sourcestr = $this->createGameStr($games,"my");
        return $sourcestr;
    }

    //搜索资源
    public function searchSource ($content,$channelid) {
        $sourcemodel = M("tg_source");
        $where['S.channelid'] = $channelid;
        $where["S.activeflag"] = 1;
        $where["G.activeflag"] = 1;

        if(!empty($content)){
            $condition['G.gamename'] = array("like", "%{$content}%");
            $condition['G.gamepinyin'] = array("like", "%{$content}%");
            $condition['G.gametype'] = array("like", "%{$content}%");
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }

        $source = $sourcemodel
            ->field("S.id as sourceid,G.gameicon,G.gamename,G.gameversion,C.channelname,S.sourcesn,S.sourcesharerate,S.sourcechannelrate,S.sub_share_rate,S.sub_channel_rate")
            ->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")
            ->where($where)
            ->order("G.gameauthority desc")
            ->select();
        $sourcestr = $this->createGameStr($source,"my");
        return $sourcestr;
    }

    public function createGameStr($games,$tab){
        if ($tab == "all") {
            $gamestr = "";

            $commonAction = new CommonAction();
            $applySourceRight = $commonAction->authoritycheck(10225);//申请资源
            foreach($games as $k =>$v){
                $gamestr .= "<tr>";
                $gamestr .= "<td>";
                $gamestr .= "<a class='gameinfo'>";
                $gamestr .= "<img src=".$this->admindomain."/upfiles/gameicon/".$v["gameicon"].">";
                $gamestr .= "<div>";
                $gamestr .= "<span href='javascript:void(0)' class='gamename'>".$v["gamename"]."</span>";
                $gamestr .= "<span href='javascript:void(0)' class='gameversion'>".$v["gameversion"]."</span>";
                $gamestr .= "</div>";
                $gamestr .= "</a>";
                $gamestr .= "</td>";
                $gamestr .= "<td>".$v["categoryname"]."</td>";
                $gamestr .= "<td>".$v["tagname"]."</td>";
                $gamestr .= "<td>".$v["publishtime"]."</td>";
                $gamestr .= "<td>".$v["gameauthority"]."</td>";
                /*if($v["isonstack"]  == -1 ){
                    $gamestr .= "<td></td>";
                    $gamestr .= "<td></td>";
                    $gamestr .= "<td></td>";
                }else{*/
                $gamestr .= "<td>".($v["gamesize"] <= 0 ? "0" : $v["gamesize"])." MB</td>";
                /*                    $gamestr .= "<td>".$v["sharetype"]."</td>";*/
                $gamestr .= "<td style='position: relative;top: -1px;'>".$v["sharerate"]."</td>";
                //}

                if ($v["isonstack"] == 0) {
                    if ($v["isapply"] == 1) {
                        $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>已申请</button></td>";
                    } else {
                        if($applySourceRight == 'ok'){
                            $gamestr .= "<td><button class='btn btn-primary app-apply' data-gameid='".$v["gameid"]."'>申请</button></td>";
                        }else{
                            $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>未申请</button></td>";
                        }
                    }
                } else if ($v["isonstack"] == 1) {
                    $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>未上架</button></td>";
                } else if ($v["isonstack"] == 2) {
                    $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>已下架</button></td>";
                } else if ($v["isonstack"] == -1) {
                    $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>待上架</button></td>";
                }
                $gamestr .= "</tr>";
            }
            return $gamestr;
        } else if ($tab == "my") {
            $gamestr = "";
            $commonAction = new CommonAction();
            $customRateRight = $commonAction->authoritycheck(10136);//自定义资源费率
            $seeDevelopRight = $commonAction->authoritycheck(10138);//查看推广
            $childRateRight = $commonAction->authoritycheck(10224);//自定义子账号费率

            foreach($games as $k =>$v){
                $gamestr .= "<tr>";
                $gamestr .= "<td>";
                $gamestr .= "<a class='gameinfo'>";
                $gamestr .= "<img src=".$this->admindomain."/upfiles/gameicon/".$v["gameicon"].">";
                $gamestr .= "<div>";
                $gamestr .= "<span href='javascript:void(0)' class='gamename'>".$v["gamename"]."</span>";
                $gamestr .= "<span href='javascript:void(0)' class='gameversion'>".$v["gameversion"]."</span>";
                $gamestr .= "</div>";
                $gamestr .= "</a>";
                $gamestr .= "</td>";
                $gamestr .= "<td>".$v["channelname"]."</td>";
                $gamestr .= "<td>".$v["sourcesn"]."</td>";
                $gamestr .= "<td class=\"text-right\">".$v["isfixrate"]."</td>";
                $gamestr .= "<td class=\"text-right\">".$v["sourcesharerate"]."</td>";
                $gamestr .= "<td class=\"text-right\">".$v["sourcechannelrate"]."</td>";
                $gamestr .= "<td class=\"text-right\">".$v["sub_share_rate"]."</td>";
                $gamestr .= "<td class=\"text-right\">".$v["sub_channel_rate"]."</td>";

                if($customRateRight == 'ok'){
                    $gamestr .= "<td class=\"text-center\"><a style='margin-top:3px;' id='link' href='/userrate/".$v['sourceid']."/'>修改</a></td>";
                }

                if($childRateRight == 'ok'){
                    $gamestr .= "<td class=\"text-center\"><a style='margin-top:3px;' id='link' href='/definerate/".$v['sourceid']."/'>修改</a></td>";
                }

                if($seeDevelopRight == 'ok') {
                    $gamestr .= "<td class=\"text-center\"><a style='margin-top:3px;' id='link' href='/material/" . $v['sourceid'] . "/'>查看</a></td>";
                }
                $gamestr .= "</tr>";
            }
            return $gamestr;
        }
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
    // 生成包
    public function subpackage($packagename,$newgamename,$sourcesn){
        $sourcemodel = M('tg_source');
        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->field('id')->where($map)->find();

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

                //分包完成后，修改文件一处特征值，避免QQ离线重复上传
                app_channel($this->downloadStoreFolder.$newgamename,$source['id']);

                return array('code' => 1, 'msg' => '生成包成功');
            } else {
                return array('code' => 0, 'msg' => '生成包失败。');
            }
        }catch(Exception $e){
            return array('code' => 0, 'msg' => '生成包过程中发生异常');
        }
    }

    public function checkNewPackage($source, $gameid)
    {
        $allGameModel = M('all_game');
        $allGame = $allGameModel->where(array('id' => $gameid))->find();
        $sdkversion = $allGame['sdkversion'];

        if(C('IS_NEW_PACKAGE') === true && (!empty($sdkversion) && strcasecmp($sdkversion, '3.0') > 0) && strpos($source, 'gr_') !== false){
            return true;
        }
        return false;
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
                $Callback = $this->admindomain.'/?m=game&a=forcecdncallback&sourcesn='.$sourcesn.'&newgamename='.$newgamename;
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
                $Callback = $this->admindomain.'/?m=game&a=cdncallback&sourcesn='.$sourcesn.'&newgamename='.$newgamename;
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
