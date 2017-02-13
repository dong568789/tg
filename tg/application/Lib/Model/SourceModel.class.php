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
		$this->apkdownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/downloadpackage/";
		$this->texturedownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/texture/";
		$this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/";
		$this->packageStoreFolder = "../admin/DataGames/upfiles/basicpackage/";
		$this->downloadStoreFolder = "../admin/DataGames/upfiles/downloadpackage/";
		$this->textureStoreFolder = "../admin/DataGames/upfiles/texture/";
		$this->iconStoreFolder = "../admin/upfiles/gameicon/";
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
    public function selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid,$order,$order_hot=''){
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
            ->order("G.gameauthority desc")
            ->select();
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

        $games = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")->where($where)->order("G.gameauthority desc")->select();
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
                $gamestr .= "<td>".$v["createtime"]."</td>";
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
                        $sourcemodel = M("tg_source");
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
					/* 下载时分包
					$gamestr .= "<td><a class='btn btn-default btn-icon-text' href='".$this->apkdownloadurl.$v["apkurl"]."'><i class='zmdi zmdi-android'></i> 下载APK包</a>";
					*/
					$gamestr .= "<td><a href='javascript:void(0);' onclick='downloadApk(\"".$v["sourcesn"]."\");'>下载APK包</a>&nbsp;&nbsp;";
					$gamestr .= "<a style='margin-top:3px;' href='javascript:void(0);' onclick='downloadTextture(\"".$v["sourcesn"]."\");'>下载素材包</a>&nbsp;&nbsp;";
                    $currentSource=$sourcemodel->field('id')->where('sourcesn="'.$v['sourcesn'].'"')->find();

                    if(isset($_SESSION['userpid']) && $_SESSION['userpid']==0){
                        $gamestr .= "<a style='margin-top:3px;' id='link' href='/definerate/".$currentSource['id']."/'>自定义子账号资源费率</a>&nbsp;&nbsp;";
                    }
                    
                    $gamestr .= "<a style='margin-top:3px;' id='link' href='/material/".$currentSource['id']."/'>获取推广素材</a></td>";

                } else if ($v["isonstack"] == 1) {
					$gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>未上架</button></td>";
				} else if ($v["isonstack"] == 2) {
					$gamestr .= "<td><button class='btn btn-gray app-apply' style='color: #999;' data-gameid='".$v["gameid"]."' disabled>已下架</button></td>";
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











}
?>
