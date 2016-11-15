<?php
class SourceAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
        $Index = D("Source");
        $game = $Index->index();
        $this->assign('category',$game['category']);
        $this->assign('tag',$game['tag']);
        $this->assign('channel',$game['channel']);
        $this->assign('gamestr',$game['gamestr']);
        $this->assign('sourcestr',$game['sourcestr']);//我的推广
        $this->display();
    }

	 //申请
    public function applyGame(){
		$this->logincheck();
		$gameid = $_POST["game"];
		$channelid = $_POST["channel"];
        $userid = $_SESSION['userid'];
		$usermodel = M('tg_user');
        $sourcemodel = M('tg_source');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
		$agentmodel = M('sdk_agentlist');
		$condition['userid'] = $userid;
        $condition['gameid'] = $gameid;
        $condition['channelid'] = $channelid;
		$condition['activeflag'] = 1;
		$source = $sourcemodel->where($condition)->find();
		if($source) {
			$this->ajaxReturn('fail',"您已经申请过该资源。",0);
			exit();
		} else {
			$game = $gamemodel->find($gameid);
			$user = $usermodel->find($userid);
			$data['activeflag'] = 1;
			$data['userid'] = $userid;
			$data['gameid'] = $gameid;
			$data['channelid'] = $channelid;
			$data['createtime'] = date('Y-m-d : H-i-s',time());
			$packagename = $game["packagename"];
			$newgamename = $this->makeStr(30);
			$sourcesn = "tg_".$newgamename;
			$newgamename = $newgamename.".apk";
			$texturename = $game["texturename"];
			$data['sourcesn'] = $sourcesn;
			if ($game["sharerate"] != "") {
				$data['sourcesharerate'] = $game["sharerate"];
			}
			if ($game["channelrate"] != "") {
				$data['sourcechannelrate'] = $game["channelrate"];
			}
			$data['textureurl'] = $texturename;
			$data['isupload'] = 0;
			$data['createuser'] = $user["realname"];
			/* 分包改为下载时
			$result = $this->subpackage($packagename,$newgamename,$sourcesn); 
			if ($result == "true") {
			*/
			$sourceid = $sourcemodel->add($data);
			$agentdata["gameid"] = $game["sdkgameid"];
			$agentdata["agent"] = $sourcesn;
			$channel = $channelmodel->find($channelid);
			$agentdata["agentname"] = $user["account"]."_".$channel["channelname"];
			$agentdata["departmentid"] = 20;
			$agentdata["owner"] = "Admin";
			$agentdata["username"] = "Admin";
			$agentdata["cpa_price"] = 0;
			$agentdata["rate"] = $game["sharerate"];
			$agentdata["create_time"] = time();
			$agentid = $agentmodel->add($agentdata);
            $time = date('Y-m-d H:i:s',time());
			if ($sourceid && $agentid) {
				$inccondition["channelid"] = $channelid;
				$channelmodel->where($inccondition)->setInc('gamecount');
                $this->insertLog($_SESSION['account'],'申请资源', 'SourceAction.class.php', 'applyGame', $time, "用户".$_SESSION['account']."在“".$channel["channelname"]."”渠道下申请了“".$game['gamename']."”游戏");
                $this->ajaxReturn('success',$data,1);
				exit();
			} else {
				$this->ajaxReturn('fail',"失败，请联系管理员。",0);
				exit();
			}
		}
        
		/*
		} else {
			$this->ajaxReturn('fail',"无法创建文件，打包失败。",0);
			exit();
		}
		*/
    }

	public function downloadapk(){
		$userid = $_SESSION["userid"];
		if (isset($userid) && $userid > 0) {
			$sourcesn = $_POST["source"];
			$sourcemodel = M('tg_source');
            $gamemodel = M('tg_game');
            $channelmodel = M('tg_channel');
			$map["sourcesn"] = $sourcesn;
			$source = $sourcemodel->where($map)->find();
            $gameid = $source['gameid'];
            $channelid = $source['channelid'];
            $oldgame = $gamemodel->where("gameid = '$gameid'")->find();
            $oldchannel = $channelmodel->where("channelid = '$channelid'")->find();

			$packagemodel = M('tg_package');
			$packagemap["activeflag"] = 1;
			$packagemap["isnowactive"] = 1;
			$packagemap["isforcepackage"] = 1;
			$packagemap["gameid"] = $source["gameid"];
			$package = $packagemodel->where($packagemap)->find();
			// 是否存在强更包
			if ($package) {
				if (strtotime($package["forcetime"]) > time()) {
					// 如果强更时间没到
					$oldgamename = $source["apkurl"];

					// 获取$oldapkurl
					if ($source["isupload"] == 1) {
						$oldapkurl = $this->apkdownloadurl.$oldgamename;
					} else {
						// 如果不存在，根据游戏的母包重新生成
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $game["packagename"];
						if ($game["gameversion"] != "") {
							$oldgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						} else {
							$oldgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						}
						$result = $this->subpackage($packagename,$oldgamename,$sourcesn);
						if ($result == "true") {
							$data["isupload"] = 1;
							$data["apkurl"] = $oldgamename;
							$upload = $sourcemodel->where($map)->save($data);
							$oldapkurl = $this->apkdownloadurl.$oldgamename;
						}
					}

					$forcepackagemodel = M('tg_forcepackage');
					$forcepackagemap["activeflag"] = 1;
					$forcepackagemap["isforce"] = 0;
					$forcepackagemap["userid"] = $source["userid"];
					$forcepackagemap["channelid"] = $source["channelid"];
					$forcepackagemap["gameid"] = $source["gameid"];
					$forcepackage = $forcepackagemodel->where($forcepackagemap)->find();
					if ($forcepackage) {
						$newapkurl = $this->apkdownloadurl.$forcepackage["apkurl"];
					} else {
						// 如果不存在，根据强更包的母包重新生成
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $package["packagename"];
						if ($package["gameversion"] != "") {
							$newgamename = $game["gamepinyin"]."_".$package["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						} else {
							$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						}
						$result = $this->subpackage($packagename,$newgamename,$sourcesn);
						if ($result == "true") {
							$forcepackagedata["userid"] = $source["userid"];
							$forcepackagedata["channelid"] = $source["channelid"];
							$forcepackagedata["gameid"] = $source["gameid"];
							$forcepackagedata["apkurl"] = $newgamename;
							$forcepackagedata["isforce"] = 0;
							$forcepackagedata["isdelete"] = 0;
							$forcepackagedata["activeflag"] = 1;
							$forcepackagedata['createtime'] = date('Y-m-d H:i:s',time());
							$forcepackagedata['createuser'] = "Admin";
							$newforcepackage = $forcepackagemodel->add($forcepackagedata);
							if ($newforcepackage) {
								$agentModel = M('sdk_agentlist');
								$agentcondition["agent"] = $source["sourcesn"];
								$agentdata["upurl"] = $this->apkdownloadurl.$newgamename;
								$agent = $agentModel->where($agentcondition)->save($agentdata);
								if ($agent) {
									$newapkurl = $this->apkdownloadurl.$newgamename;
								} else {
									$this->ajaxReturn('fail','失败，未能更新强更链接.',0);
									exit();
								}			
							} else {
								$this->ajaxReturn('fail','失败，未能新增强更包信息.',0);
								exit();
							}
						} else {
							$this->ajaxReturn('fail','分包失败.',0);
							exit();
						}
					}
					$info["oldapkurl"] = $oldapkurl;
					$info["newapkurl"] = $newapkurl;
					$info["forcetime"] = date("m月d日 H时i分",strtotime($package["forcetime"]));
					$this->ajaxReturn('force',$info,1);
					exit();
				} else {
					// 过了强更时间
					$newgamename = $source["apkurl"];
					if ($source["isupload"] == 1) {
                        $time = date('Y-m-d H:i:s',time());
                        $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                        $this->ajaxReturn('success',$this->apkdownloadurl.$newgamename,1);
						exit();
					} else {
						// 如果不存在，根据游戏的母包，生成
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $game["packagename"];
						if ($game["gameversion"] != "") {
							$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						} else {
							$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
						}
						$result = $this->subpackage($packagename,$newgamename,$sourcesn);
						if ($result == "true") {
							$data["isupload"] = 1;
							$data["apkurl"] = $newgamename;
							$upload = $sourcemodel->where($map)->save($data);
                            $time = date('Y-m-d H:i:s',time());
                            $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                            $this->ajaxReturn('success',$this->apkdownloadurl.$newgamename,1);
							exit();
						}
					}
				}
			} else {
				$newgamename = $source["apkurl"];
				if ($source["isupload"] == 1) {
                    $time = date('Y-m-d H:i:s',time());
                    $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                    $this->ajaxReturn('success',$this->apkdownloadurl.$newgamename,1);
					exit();
				} else {
					// 没有上传，（当更新包时候，会清空资源的游戏下载链接）
					// 重新分包
					$gamemodel = M('tg_game');
					$game = $gamemodel->find($source["gameid"]);
					$packagename = $game["packagename"];
					if ($game["gameversion"] != "") {
						$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
					} else {
						$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
					}
					$result = $this->subpackage($packagename,$newgamename,$sourcesn);
					if ($result == "true") {
						$data["isupload"] = 1;
						$data["apkurl"] = $newgamename;
						$upload = $sourcemodel->where($map)->save($data);
                        $time = date('Y-m-d H:i:s',time());
                        $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                        $this->ajaxReturn('success',$this->apkdownloadurl.$newgamename,1);
						exit();
					}
				}
			}
		}
	}
	
	//推广链接
	public function publicdownload() {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_pc = (stripos($agent, 'windows nt')) ? true : false;
        $is_iphone = (stripos($agent, 'iphone')) ? true : false;
        $is_ipad = (stripos($agent, 'ipad')) ? true : false;
        $is_android = (stripos($agent, 'android')) ? true : false;
        $is_weixin = (stripos($agent, 'MicroMessenger')) ? true : false;
        $is_qq = (stripos($agent, 'QQ')) ? true : false;
        $is_qqbrowser = (stripos($agent, 'QQBrowser')) ? true : false;
        $is_weibo = (stripos($agent, 'weibo')) ? true : false;

        if($is_weixin || $is_weibo || ($is_qq ^ $is_qqbrowser)){
            $this->display("llq");
            exit();
        } else if($is_iphone){
            echo ("暂不支持苹果iOS下载，请关注我们更多游戏。");
            exit();
        } else if($is_ipad){
            echo ("暂不支持苹果iOS下载，请关注我们更多游戏。");
            exit();
        }

        $sourcesn = $_GET["sourcesn"];
		$sourcemodel = M('tg_source');
		$map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();
		if ($source) {
			if ($source["isupload"] == 1 && $source["apkurl"] != "") {
				$time = date('Y-m-d H:i:s',time());
				// $this->insertLog('推广链接','下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户通过推广链接在ID为“".$source["channelid"]."”渠道下下载了ID为“".$source['gameid']."”游戏包");
				Header("Location: ".$this->apkdownloadurl.$source["apkurl"]." ");
				exit();
			} else {
				$gamemodel = M('tg_game');
				$game = $gamemodel->find($source["gameid"]);
				$packagename = $game["packagename"];
				if ($game["gameversion"] != "") {
					$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
				} else {
					$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
				}
				$result = $this->subpackage($packagename,$newgamename,$sourcesn);
				if ($result == "true") {
					$data["isupload"] = 1;
					$data["apkurl"] = $newgamename;
					$upload = $sourcemodel->where($map)->save($data);
					$time = date('Y-m-d H:i:s',time());
					// $this->insertLog('推广链接','下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户通过推广链接在ID为“".$source["channelid"]."”渠道下下载了ID为“".$source['gameid']."”游戏包");
					Header("Location: ".$this->apkdownloadurl.$newgamename." ");
					exit();
				} else {
					echo "System Error.";
				}
			}
		} else {
			echo "Can't find APK package.";
		}
	}

    //下载素材包
    // public function downloadTextture(){
    //     $sourcesn = $_POST["source"];
    //     $sourcemodel = M('tg_source');
    //     $gamemodel = M('tg_game');
    //     $channelmodel = M('tg_channel');
    //     $map["sourcesn"] = $sourcesn;
    //     $source = $sourcemodel->where($map)->find();
    //     $texturename = $source['textureurl'];
    //     $gameid = $source['gameid'];
    //     $channelid = $source['channelid'];
    //     $oldgame = $gamemodel->where("gameid = '$gameid'")->find();
    //     $oldchannel = $channelmodel->where("channelid = '$channelid'")->find();
    //     $time = date('Y-m-d H:i:s',time());
    //     if($texturename){
    //         $this->insertLog($_SESSION['account'],'下载素材包', 'SourceAction.class.php', 'downloadTextture', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”素材包");
    //         $this->ajaxReturn('success',$this->texturedownloadurl.$texturename,1);
    //         exit();
    //     } else{
    //         $this->ajaxReturn('fail','该素材包不存在',0);
    //         exit();
    //     }
    // }

    //下载素材包
    public function downloadTextture(){
        $sourcesn = $_POST["source"];
        $sourcemodel = M('tg_source');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
        
        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();
        // $texturename = $source['textureurl'];
        $gameid = $source['gameid'];
        $channelid = $source['channelid'];

        // 改成获取游戏的素材包
        $oldgame = $gamemodel->where("gameid = '$gameid'")->find();
        $texturename = $oldgame['texturename'];

        $oldchannel = $channelmodel->where("channelid = '$channelid'")->find();

        $time = date('Y-m-d H:i:s',time());
        if($texturename){
            $this->insertLog($_SESSION['account'],'下载素材包', 'SourceAction.class.php', 'downloadTextture', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”素材包");
            $this->ajaxReturn('success',$this->texturedownloadurl.$texturename,1);
            exit();
        } else{
            $this->ajaxReturn('fail','该素材包不存在',0);
            exit();
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

    //游戏分类筛选，以及渠道筛选
    public function selectGame(){
        $this->logincheck();
        $gametype = $_POST['gametype'];
        $gamecategory = $_POST['gamecategory'];
        $gamesize = $_POST['gamesize'];
        $gametag = $_POST['gametag'];
        $channelid = $_POST['gamechannel'];
        $Index = D("Source");
        $gamestr = $Index->selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
    }

    //搜索游戏
    public function searchGame(){
        $this->logincheck();
        $content = $_POST['content'];
		$channelid = $_POST['channelid'];
        $Index = D("Source");
        $gamestr = $Index->searchGame($content,$channelid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}

    }

    //搜索资源
    public function searchMygame(){
        $this->logincheck();
        $content = $_POST['content'];
        $channelid = $_POST['channelid'];
        $Index = D("Source");
        $sourcestr = $Index->searchSource($content,$channelid);
        if($sourcestr){
            $this->ajaxReturn('success',$sourcestr,1);
            exit();
        }else{
            $this->ajaxReturn('fail','没有数据。',0);
            exit();
        }

    }


	//tab2选择渠道进行搜索
    public function selectSource(){
        $this->logincheck();
        $channelid = $_POST['channelid'];
        $Index = D("Source");
        $gamestr = $Index->selectSource($channelid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}

    }

	public function makeStr($length) { 
		$possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"; 
		$str = ""; 
		while(strlen($str) < $length) {
			$str .= substr($possible, (rand() % strlen($possible)), 1);
		}
		return($str); 
	}

    // 用户-资源-推广链接
    public function material(){
    	$this->logincheck();
  
    	$sourceid = $_GET['sourceid'];

    	$prefix = C('DB_PREFIX');
		$where=' and a.id="'.$sourceid.'"';
        $sql="SELECT
        		b.gameid,
                b.gamepinyin
        FROM {$prefix}tg_source a
        LEFT JOIN {$prefix}tg_game b ON a.gameid=b.gameid
        WHERE 1 ".$where;
        $result=M()->query($sql);
        $game=$result[0];

        $Source = D('Source');
    	$long_url = $Source->getDownloadURL($sourceid);
    	$short_url = $Source->shortenSinaUrl($long_url);

    	$image = $Source->create_erweima($long_url,$game['gamepinyin']);

    	$this->assign("long_url",$long_url);
        $this->assign("short_url",$short_url);
        $this->assign("image",$image);

        $this->assign("gamepinyin",$game['gamepinyin']);
        $this->assign("sourceid",$sourceid);

        $this->display();
    }

    // 用户-资源-推广链接-手机页面
    public function page(){
    	$sourceid = $_GET['sourceid'];
	
        $prefix = C('DB_PREFIX');
        $where=' and a.id="'.$sourceid.'"';
        $sql="SELECT
                b.*,
                c.categoryname
        FROM {$prefix}tg_source a
        LEFT JOIN {$prefix}tg_game b ON b.gameid=a.gameid
        LEFT JOIN {$prefix}tg_gamecategory c ON b.gamecategory=c.id
        WHERE 1 ".$where;
        $result=M()->query($sql);
        $game=$result[0];
        $yel_num=intval($game['score']/2);//黄星星个数
        $half_num=$game['score']%2;//半星星个数
        $grey_num=5-$yel_num-$half_num;//灰星星个数
        $this->assign("game",$game);
        $this->assign("yel_num",$yel_num);
        $this->assign("half_num",$half_num);
        $this->assign("grey_num",$grey_num);

        $Source = D('Source');
        $long_url = $Source->getDownloadURL($sourceid);
        $this->assign("long_url",$long_url);
        $this->assign("short_url",$Source->shortenSinaUrl($long_url));

        $this->assign("sourceid",$sourceid);

        $this->display();
    }

    public function llq(){
        $this->display();
    }




}
?>