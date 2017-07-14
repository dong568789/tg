<?php

class SourceModel extends CommonModel
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $userid = $_SESSION['userid'];
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
    public function indexson(){
        $userid = $_SESSION['userid'];

        $usermodel = M("tg_user");

        $where = array('userid' => $userid);
        $channelid = $usermodel->field('channelid')->where($where)->find();
        $channelid = $channelid['channelid'];

        $game['sourcestr'] = $this->selectSource($channelid);
        return $game;
    }

    //游戏分类筛选
    public function selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid,$order,$order_hot='',$source_type=''){
        $userid = $_SESSION['userid'];
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

        if(!empty($source_type)){
            $map['guard'] = array('like', "%,{$source_type},%");
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

        $cpsGamemodel = M("cps_game");
        $cpsGames = $cpsGamemodel->alias("G")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->field('G.*,C.categoryname,T.tagname')
            ->where($map)
            ->order($ordrestr)
            ->select();

        $games = array_merge($games, (array)$cpsGames);

        $sourcecondition["userid"] = $userid;
		$sourcecondition["channelid"] = $channelid;

        $csourceModel = M('cps_source');
        $cpsSql = $csourceModel->where($sourcecondition)->field('gameid')->buildSql();
        $source = $sourcemodel->where($sourcecondition)
            ->union($cpsSql)
            ->field('gameid')
            ->select();
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
    public function searchGame($content,$channelid,$source_type=''){
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

        if(!empty($source_type)){
            $where .= ' AND G.guard like "%,'.$source_type.',%"';
        }

        $cgamemodel = M("cps_game");

        $cgameSql = $cgamemodel->alias("G")
            ->where($where)
            ->field('G.gamecategory,G.gametag,G.gameicon,G.gamename,G.gameversion,G.publishtime,G.gameauthority,G.gamesize,G
            .sharerate,G
            .isonstack,G.gameid')
            ->buildSql();

        $gameSql = $gamemodel->alias("G")
            ->where($where)
            ->field('G.gamecategory,G.gametag,G.gameicon,G.gamename,G.gameversion,G.publishtime,G.gameauthority,G.gamesize,G
            .sharerate,G
            .isonstack,G.gameid')
            ->union($cgameSql)
            ->buildSql();

        $sql = M('')->table($gameSql.' G')
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->order("gameauthority desc")
            ->field("G.*,C.categoryname,T.tagname")
            ->buildSql();
        $games=  M('')->query(str_replace('`','',$sql));
        $sourcecondition["userid"] = $userid;
		$sourcecondition["channelid"] = $channelid;
        $csourceModel = M('cps_source');
        $cpsSql = $csourceModel->where($sourcecondition)->field('gameid')->buildSql();
		$source = $sourcemodel->where($sourcecondition)
            ->union($cpsSql)
            ->field('gameid')
            ->select();
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
            ->field('*,S.id as sourceid,S.createtime as stime')
            ->order("S.id desc")
            ->select();

        $cpsSourceModel = M("cps_source");
        $cgames = $cpsSourceModel->alias("S")
            ->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->where($map)
            ->field('*,S.id as sourceid,S.createtime as stime')
            ->order("S.id desc")
            ->select();

        $games = array_merge((array)$games,(array)$cgames);

        $item = array();
        foreach($games as $key=>$value){
            $item[$key] = $value['stime'];
        }

        array_multisort($item,SORT_DESC,$games);

        $sourcestr = $this->createGameStr($games,"my");
        return $sourcestr;
    }

    //搜索资源
    public function searchSource ($content,$channelid) {
        $sourcemodel = M("tg_source");
        $where = '';
        $where.= "G.activeflag = '1' AND S.activeflag = '1' AND S.channelid = '$channelid' AND(";
        $where.= "G.gamename like '%".$content."%'";
        $where.= "OR G.gamepinyin like'%".$content."%'";
        $where.= "OR G.gametype like '%".$content."%'";
        $where.= "OR G.gamecategory like '%".$content."%'";
        $where.= "OR G.gametag like '%".$content."%'";
        $where.= "OR G.gamesize like '%".$content."%'";
        $where.= "OR G.sharetype like '%".$content."%')";

        $games = $sourcemodel->alias("S")
            ->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->where($where)
            ->field('G.gamecategory,G.gametag,G,gameicon,G.gamename,G.gameversion,G.publishtime,G.gameauthority,G.gamesize,G.sharerate,G.isonstack,G.gameid,C.categoryname,T.tagname,S.id as sourceid')
            ->order("G.gameauthority desc")
            ->select();

        $cpsSourceModel = M("cps_source");
        $cgames = $cpsSourceModel->alias("S")
            ->join(C('DB_PREFIX')."cps_game G on G.gameid = S.gameid", "LEFT")
            ->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
            ->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
            ->where($where)
            ->field('G.gamecategory,G.gametag,G.gameicon,G.gamename,G.gameversion,G.publishtime,G.gameauthority,G.gamesize,G.sharerate,G.isonstack,G.gameid,C.categoryname,T.tagname,S.id as sourceid')
            ->order("G.gameauthority desc")
            ->select();

        $games = array_merge((array)$games,(array)$cgames);

        $item = array();
        foreach($games as $key=>$value){
            $item[$key] = $value['gameauthority'];
        }

        array_multisort($item,SORT_DESC,$games);

        $sourcestr = $this->createGameStr($games,"my");

        return $sourcestr;
    }

	public function createGameStr($games,$tab){
		if ($tab == "all") {
			$gamestr = "";
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
						$gamestr .= "<td><button class='btn btn-primary app-apply' data-gameid='".$v["gameid"]."'>申请</button></td>";
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
				$gamestr .= "<td>".$v["gameauthority"]."</td>";
				$gamestr .= "<td>".($v["gamesize"] <= 0 ? 0 : $v["gamesize"])." MB</td>";
				/*$gamestr .= "<td>".$v["sharetype"]."</td>";*/

                
                if(isset($_SESSION['userpid']) && $_SESSION['userpid']>0){
                    $gamestr .= "<td>".$v["sub_share_rate"]."</td>";
                }else{
                    $gamestr .= "<td>".$v["sourcesharerate"]."</td>";
                    $gamestr .= "<td>".$v["sub_share_rate"]."</td>";
                }
				
				if ($v["isonstack"] == 0) {
					$gamestr .= "<td><a href='javascript:void(0);' onclick='downloadUrl(\"".$v["sourceid"]."\");'>下载APK包</a>&nbsp;&nbsp;";
					$gamestr .= "<a style='margin-top:3px;' href='javascript:void(0);' onclick='downloadTextture(\"".$v["sourcesn"]."\");'>下载素材包</a>&nbsp;&nbsp;";

                    if(isset($_SESSION['userpid']) && $_SESSION['userpid']==0){
                        $gamestr .= "<a style='margin-top:3px;' id='link' href='/definerate/".$v['sourceid']."/'>自定义子账号资源费率</a>&nbsp;&nbsp;";
                    }
                    
                    $gamestr .= "<a style='margin-top:3px;' id='link' href='/material/".$v['sourceid']."/'>获取推广素材</a></td>";

                } else if ($v["isonstack"] == 1) {
					$gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>未上架</button></td>";
				} else if ($v["isonstack"] == 2) {
					$gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>已下架</button></td>";
				}else if ($v["isonstack"] == '-1') {
                    $gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>待上架</button></td>";
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
        $games = $sourcemodel->field('sourcesn,apkurl')->where($where)->find();

        if(empty($games)){
            $sourcemodel = M("cps_source");
            $games = $sourcemodel->field('sourcesn,apkurl')->where($where)->find();
        }
       // print_r($games);exit;
        if(strpos($games['apkurl'],'http') !== false){
            $url = $games['apkurl'];
        }else{
            $url = $this->tgdomain."/publicdownload/".$games["sourcesn"];
        }
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




        $apkdownloadurl = $this->apkdownloadurl;
        //开启新分包
        $checkNewPackage = $this->checkNewPackage($source['sourcesn'], $game['sdkgameid']);
        $packStr = '';
        if($checkNewPackage === true){
            $packStr = '-merged-';
            $apkdownloadurl = C('mountedFolder');
        }

        if ($game["gameversion"] != "") {
            $newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
        } else {
            $newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
        }
        if($checkNewPackage === true){
            $this->createTgApp($packagename, $source['id'], $source['sourcesn'], $newgamename);
            //mountedfiles
            $result['code'] = 1;
        }else{
            $result = $this->subpackage($packagename,$newgamename,$sourcesn);
        }

        if ($result['code'] == 1) {
            $data["isupload"] = 1;
            $data["apkurl"] = $newgamename;
            $upload = $sourcemodel->where($map)->save($data);

            // 第一次分包的时候cdn提交
            $this->cdnsubmit($sourcesn,$newgamename,false);

            // 生成强更包等一系列操作
            $forceReturn =$this->createForcePackage($source['id']);
            if(!$forceReturn['code']){
                return array('code' => 0, 'msg' => $forceReturn['msg'], 'data' => '' );
            }

            return array('code' => 1, 'msg' => '生成资源包成功。', 'data' => $apkdownloadurl.$newgamename );
        } else {
            return array('code' => 0, 'msg' => (!empty($result['msg']) ? $result['msg'] : '生成资源包失败。'));
        }



    }

    // -------------资源包下载-------------------------
    // 生成资源包
    public function cpsCreateSourePackage($sourcesn){
        $sourcemodel = M('cps_source');
        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();

        $gamemodel = M('cps_game');
        $game = $gamemodel->find($source["gameid"]);
        $packagename = $game["packagename"];


        $apkdownloadurl = $this->apkdownloadurl;
        //开启新分包
        $checkNewPackage = $this->checkNewPackage($source['sourcesn'], $game['sdkgameid']);
        $packStr = '';
        if($checkNewPackage === true){
            $packStr = '-merged-';
            $apkdownloadurl = C('mountedFolder');
        }

        if ($game["gameversion"] != "") {
            $newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
        } else {
            $newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
        }
        if($checkNewPackage === true){
            $this->createTgApp($packagename, $source['id'], $source['sourcesn'], $newgamename);
            //mountedfiles
            $result['code'] = 1;
        }else{
            $result = $this->subpackage($packagename,$newgamename,$sourcesn,'cps_source');
        }

        if ($result['code'] == 1) {
            $data["isupload"] = 1;
            $data["apkurl"] = $newgamename;
            $upload = $sourcemodel->where($map)->save($data);

            // 第一次分包的时候cdn提交
            $this->cdnsubmit($sourcesn,$newgamename,false,'cps_source');

            // 生成强更包等一系列操作
            $forceReturn =$this->cpsCreateForcePackage($source['id']);
            if(!$forceReturn['code']){
                return array('code' => 0, 'msg' => $forceReturn['msg'], 'data' => '' );
            }

            return array('code' => 1, 'msg' => '生成资源包成功。', 'data' => $apkdownloadurl.$newgamename );
        } else {
            return array('code' => 0, 'msg' => (!empty($result['msg']) ? $result['msg'] : '生成资源包失败。'));
        }



    }

    // 生成强更包等一系列操作
    public function createForcePackage($sourceid){
        $sourcemodel = M("tg_source");
        $sourcecondition = array('id'=>$sourceid);
        $source = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->find();

        $packageModel = M('tg_package');
        $packagecondition["gameid"] = $source["gameid"];
        $packagecondition["activeflag"] = 1;
        $packagecondition["isnowactive"] = 1;
        $packagecondition["isforcepackage"] = 1;
        $packagecondition["isforced"] = 0; // 没有强更过
        $exsitpackage = $packageModel->where($packagecondition)->order("packageid desc")->find();

        // 如果存在  该游戏 没有强更过 的强更包
        // 在提前生成新包之后，强更时间点到了之前。中间这个时间范围内生成包，同时生成新包等操作（和Admin/Game/createForcePackage操作一样）
        $nowTime = time();
        if ($exsitpackage  && $nowTime>strtotime($exsitpackage['createtime']) && $nowTime<strtotime($exsitpackage['forcetime']) )  {
            $forcepackageModel = M('tg_forcepackage');
            $forcecondition["userid"] = $source["userid"];
            $forcecondition["channelid"] = $source["channelid"];
            $forcecondition["gameid"] = $source["gameid"];
            $forcecondition["isforce"] = 0; // 增加这个条件，解决以前第二次强更不会强更
            $isexsit = $forcepackageModel->where($forcecondition)->find();
            if(!$isexsit){

                $checkNewPackage = $this->checkNewPackage($source['sourcesn'], $source['sdkgameid']);
                $packStr = '';
                if($checkNewPackage === true){
                    $packStr = '-merged-';
                }
                // 不存在强更包，则生成生成强更包文件
                if ($source["gameversion"] != "") {
                    $newgamename = $source["gamepinyin"]."_".$exsitpackage["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
                } else {
                    $newgamename = $source["gamepinyin"]."_".$source["channelid"].$packStr.".apk";
                }

                $result = $this->subpackage($exsitpackage["packagename"],$newgamename,$source["sourcesn"]);
                if ($result['code'] == 1) {
                    // 添加记录到tg_forcepackage表中，记录该资源强更包的游戏下载链接
                    $data["userid"] = $source["userid"];
                    $data["channelid"] = $source["channelid"];
                    $data["gameid"] = $source["gameid"];
                    $data["apkurl"] = $newgamename;
                    $data["isforce"] = 0;
                    $data["isdelete"] = 0;
                    $data["activeflag"] = 1;
                    $data['createtime'] = date('Y-m-d H:i:s',time());
                    $data['createuser'] = "Admin";
                    $forcepackage = $forcepackageModel->add($data);

                    if ($forcepackage) {
                        // 第一次分包的时候cdn提交
                        $this->cdnsubmit($source["sourcesn"],$newgamename,true);

                        // sdk_agentlist增加资源的强更链接
                        $agentModel = M('sdk_agentlist');
                        $agentcondition["agent"] = $source["sourcesn"];
                        $agentdata["upurl"] = $this->apkdownloadurl.$newgamename;
                        $agent = $agentModel->where($agentcondition)->save($agentdata);

                        if ($agent) {
                            return array('code' => 1, 'msg' => '生成生成强更包成功。', 'data' => '' );
                        } else {
                            return array('code' => 0, 'msg' => '分包失败，未能更新强更链接', 'data' => '' );
                        }           
                    } else {
                        return array('code' => 0, 'msg' => '分包失败，未能新增强更包信息', 'data' => '' );
                    }
                }else{
                    return array('code' => 0, 'msg' => '生成强更分包失败。', 'data' => '' );
                }
            }
        }

        return array('code' => 1, 'msg' => '生成生成强更包成功。', 'data' => '' );
    }

    // 生成强更包等一系列操作
    public function cpsCreateForcePackage($sourceid){
        $sourcemodel = M("cps_source");
        $gameModel = M('cps_game');
        $sourcecondition = array('id'=>$sourceid);
        $source = $sourcemodel->alias("S")->join(C('DB_PREFIX')."cps_game G on S.gameid = G.gameid", "LEFT")->where
        ($sourcecondition)->find();

        $packageModel = M('cps_package');
        $packagecondition["gameid"] = $source["gameid"];
        $packagecondition["activeflag"] = 1;
        $packagecondition["isnowactive"] = 1;
        $packagecondition["isforcepackage"] = 1;
        $packagecondition["isforced"] = 0; // 没有强更过
        $exsitpackage = $packageModel->where($packagecondition)->order("packageid desc")->find();

        // 如果存在  该游戏 没有强更过 的强更包
        // 在提前生成新包之后，强更时间点到了之前。中间这个时间范围内生成包，同时生成新包等操作（和Admin/Game/createForcePackage操作一样）
        $nowTime = time();
        if ($exsitpackage  && $nowTime>strtotime($exsitpackage['createtime']))  {
            $forcepackageModel = M('cps_forcepackage');
            $forcecondition["userid"] = $source["userid"];
            $forcecondition["channelid"] = $source["channelid"];
            $forcecondition["gameid"] = $source["gameid"];
            $forcecondition["isforce"] = 0; // 增加这个条件，解决以前第二次强更不会强更
            $isexsit = $forcepackageModel->where($forcecondition)->find();
            if(!$isexsit){

                $checkNewPackage = $this->checkNewPackage($source['sourcesn'], $source['sdkgameid']);
                $packStr = '';
                if($checkNewPackage === true){
                    $packStr = '-merged-';
                }
                // 不存在强更包，则生成生成强更包文件
                if ($source["gameversion"] != "") {
                    $newgamename = $source["gamepinyin"]."_".$exsitpackage["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).$packStr.".apk";
                } else {
                    $newgamename = $source["gamepinyin"]."_".$source["channelid"].$packStr.".apk";
                }

                $result = $this->subpackage($exsitpackage["packagename"],$newgamename,$source["sourcesn"],'cps_source');
                if ($result['code'] == 1) {
                    // 添加记录到tg_forcepackage表中，记录该资源强更包的游戏下载链接
                    $data["userid"] = $source["userid"];
                    $data["channelid"] = $source["channelid"];
                    $data["gameid"] = $source["gameid"];
                    $data["apkurl"] = $newgamename;
                    $data["isforce"] = 0;
                    $data["isdelete"] = 0;
                    $data["activeflag"] = 1;
                    $data['createtime'] = date('Y-m-d H:i:s',time());
                    $data['createuser'] = "Admin";
                    $forcepackage = $forcepackageModel->add($data);

                    if ($forcepackage) {
                        // 第一次分包的时候cdn提交
                        $this->cdnsubmit($source["sourcesn"],$newgamename,true,'cps_source');

                        // sdk_agentlist增加资源的强更链接
                        $agentcondition["gameid"] = $source["gameid"];
                        $agentdata["upurl"] = $this->apkstoreurl.$exsitpackage['packagename'];

                        $agent = $gameModel->where($agentcondition)->save($agentdata);

                        if ($agent) {
                            return array('code' => 1, 'msg' => '生成生成强更包成功。', 'data' => '' );
                        } else {
                            return array('code' => 0, 'msg' => '分包失败，未能更新强更链接', 'data' => '' );
                        }
                    } else {
                        return array('code' => 0, 'msg' => '分包失败，未能新增强更包信息', 'data' => '' );
                    }
                }else{
                    return array('code' => 0, 'msg' => '生成强更分包失败。', 'data' => '' );
                }
            }
        }

        return array('code' => 1, 'msg' => '生成生成强更包成功。', 'data' => '' );
    }

    // 生成包
    public function subpackage($packagename,$newgamename,$sourcesn,$model='tg_source'){
        $sourcemodel = M($model);
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

    // cdn提交接口
    public function cdnsubmit($sourcesn,$newgamename,$isforce,$model='tg_source'){
        // 允许用户提交cdn才提交cdn
        $sourceModel = M($model);
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


    public function createTgSource($sourceid, $source)
    {
        $path = C('tgPackageFolder').$sourceid.'.txt';
        !file_exists($path) && file_put_contents($path, $source);
        return $path;
    }

    public function createTgApp($basicName, $sourceid, $source, $filename)
    {
        $this->createTgSource($sourceid, $source);
        $path = C('downloadStoreFolder').$filename;
        //文件不正确
        if (strpos($filename, '-merged-') === false)
            return false;

        $data = array(
            array(
                'path' => '../basicpackage/'.$basicName,
                'replaces' => array(
                    //防止串包
                    array(
                        'offset' => 10,
                        'length' => 4,
                        'content' => base64_encode(pack('V', timestamp2dos($sourceid * 2 + mktime(0, 0, 0, 1, 1, 2000)))) ,
                    ),
                    //结尾Comment长度
                    array(
                        'offset' => -2,
                        'length' => 2,
                        'content' => base64_encode(pack('v', strlen($source))),
                    ),
                ),
            ),
            array(
                'path' => '../tgpackage/'.$sourceid.'.txt',
            )
        );
        file_put_contents($path, json_encode($data));
        return true;
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
}
?>
