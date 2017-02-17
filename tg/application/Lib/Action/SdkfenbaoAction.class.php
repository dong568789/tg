<?php
class SdkfenbaoAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
		$username = $_POST['username'];
		$sdkgid = $_POST['gameid'];

		//生成sdk推广信息
		$this->applyGame($username,$sdkgid);



    }

	 //信息生成
    public function applyGame($username,$sdkgid){
    	$time =  date('Y-m-d : H-i-s',time());
    	$gamemodel = M('tg_game');
    	$usermodel = M('tg_user');
    	$channelmodel = M('tg_channel');
    	$sourcemodel = M('tg_source');
    	$sdkuserbodel = M('all_user');
    	$sdkagent = M('sdk_agentlist');
    	$sdkuser = $sdkuserbodel->field('password')->where("username='{$username}'")->find();
    	$game = $gamemodel->where("sdkgameid={$sdkgid}")->find();
    	if(empty($game)){
    		echo '游戏不存在';
    		exit();
    	}
    	// 这样就减少导致和用户重复的机会，用户重复会对推广系统有影响
    	$new_username='sdk_'.$username; // 默认用户前面加sdk_
    	$user = $usermodel->where("account='{$new_username}' AND issdkuser=1")->find();
    	//判断该SDK推广用户是否存在
    	if(!empty($user)){
	    	$map['userid'] = $user['userid'];
	    	$map['gameid'] = $game['gameid'];
	    	$source = $sourcemodel->where($map)->find();
	    	$channel = $channelmodel->where("userid='{$user['userid']}'")->find();
	    	if(empty($source)){
	    		$data['activeflag'] = 1;
				$data['userid'] = $user['userid'];
				$data['gameid'] = $game['gameid'];
				$data['channelid'] = $channel['channelid'];
				$data['createtime'] = $time;
				$packagename = $game["packagename"];
				$newgamename = createstr(30);
				$sourcesn = "tg_".$newgamename;
				$newgamename = $newgamename.".apk";
				$texturename = $game["texturename"];
				$data['sourcesn'] = $sourcesn;
				$data['sourcesharerate'] = $game["sharerate"];
				$data['sourcechannelrate'] = $game["channelrate"];
				$data['textureurl'] = $texturename;
				$data['isupload'] = 0;
				$data['createuser'] = 'sdk';
				$sourceid = $sourcemodel->add($data);
				$this->downloadapk($sourcesn);
	    	}else{
	    		$this->downloadapk($source['sourcesn']);
	    	}
    	}else{
    		//user 用户信息
    		$data["account"] = $new_username;
			$data["usertype"] = 1;
			$data["gender"] = 0;
			$data["invoicetype"] = 0;
			$data["withdrawlimit"] = 100;
			$data['activeflag'] = 1;
			$data['createtime'] = $time;
			$data['isverified'] = 1;//默认审核用户
			$data['issdkuser'] = 1;
			$user = $usermodel->add($data);

			//channel 渠道名称
			$data = array();
			$data["userid"] = $user;
			$data["channelname"] = 'SDK推广';
			$data["gender"] = 0;
			$data["channeltype"] = 'sdk';
			$data["withdrawlimit"] = 100;
			$data['activeflag'] = 1;
			$data['createtime'] = $time;
			$data['createuser'] = 'sdk';
			$channel = $channelmodel->add($data);

			//source 渠道号
			$data['activeflag'] = 1;
			$data['userid'] = $user;
			$data['gameid'] = $game['gameid'];
			$data['channelid'] = $channel;
			$data['createtime'] = $time;
			$packagename = $game["packagename"];
			$newgamename = createstr(30);
			$sourcesn = "tg_".$newgamename;
			$newgamename = $newgamename.".apk";
			$texturename = $game["texturename"];
			$data['sourcesn'] = $sourcesn;
			$data['sourcesharerate'] = $game["sharerate"];
			$data['sourcechannelrate'] = $game["channelrate"];
			$data['textureurl'] = $texturename;
			$data['isupload'] = 0;
			$data['createuser'] = 'sdk';
			$sourceid = $sourcemodel->add($data);
			$data = array();
			$data['gameid'] = $sdkgid;
			$data['agent'] = $sourcesn;
			$data['agentname'] = $new_username.'_SDK推广';
			$data['departmentid'] = 20;
			$data['owner'] = 'admin';
			$data['username'] = 'admin';
			$data['cpa_price'] = 0;
			$data['rate'] = $game["sharerate"];
			$data['create_time'] = time();
			$data['supcard'] = 0;
			$sdkagent->add($data);
			$this->downloadapk($sourcesn);
    	}
    }

	public function downloadapk($sourcesn){
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
			if ($package) {
				if (strtotime($package["forcetime"]) > time()) {
					$oldgamename = $source["apkurl"];
					if ($source["isupload"] == 1) {
						$oldapkurl = $this->apkdownloadurl.$oldgamename;
					} else {
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $game["packagename"];
						if ($game["gameversion"] != "") {
							$oldgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
						} else {
							$oldgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
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
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $package["packagename"];
						if ($package["gameversion"] != "") {
							$newgamename = $game["gamepinyin"]."_".$package["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
						} else {
							$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
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
									echo json_encode(array('code'=>'fail','msg'=>'失败，未能更新强更链接'));
									exit();
								}			
							} else {
								echo json_encode(array('code'=>'fail','msg'=>'失败，未能新增强更包信息'));
							}
						} else {
							echo json_encode(array('code'=>'fail','msg'=>'分包失败'));
							exit();
						}
					}
					$info["oldapkurl"] = $oldapkurl;
					$info["newapkurl"] = $newapkurl;
					$info["forcetime"] = date("m月d日 h时i分",strtotime($package["forcetime"]));
					print_r($info);
					exit();
				} else {
					$newgamename = $source["apkurl"];
					if ($source["isupload"] == 1) {
                        $time = date('Y-m-d H:i:s',time());
                        // $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                        echo json_encode(array('code'=>'success','apkurl'=>$this->apkdownloadurl.$newgamename,'sourcesn'=>$sourcesn));
						exit();
					} else {
						$gamemodel = M('tg_game');
						$game = $gamemodel->find($source["gameid"]);
						$packagename = $game["packagename"];
						if ($game["gameversion"] != "") {
							$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
						} else {
							$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
						}
						$result = $this->subpackage($packagename,$newgamename,$sourcesn);
						if ($result == "true") {
							$data["isupload"] = 1;
							$data["apkurl"] = $newgamename;
							$upload = $sourcemodel->where($map)->save($data);
                            $time = date('Y-m-d H:i:s',time());
                            // $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                            echo json_encode(array('code'=>'success','apkurl'=>$this->apkdownloadurl.$newgamename,'sourcesn'=>$sourcesn));
							exit();
						}
					}
				}
			} else {
				$newgamename = $source["apkurl"];
				if ($source["isupload"] == 1) {
                    $time = date('Y-m-d H:i:s',time());
                    // $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                    echo json_encode(array('code'=>'success','apkurl'=>$this->apkdownloadurl.$newgamename,'sourcesn'=>$sourcesn));
					exit();
				} else {
					$gamemodel = M('tg_game');
					$game = $gamemodel->find($source["gameid"]);
					$packagename = $game["packagename"];
					if ($game["gameversion"] != "") {
						$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
					} else {
						$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
					}
					$result = $this->subpackage($packagename,$newgamename,$sourcesn);
					if ($result == "true") {
						$data["isupload"] = 1;
						$data["apkurl"] = $newgamename;
						$upload = $sourcemodel->where($map)->save($data);
                        $time = date('Y-m-d H:i:s',time());
                        // $this->insertLog($_SESSION['account'],'下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”游戏包");
                        echo json_encode(array('code'=>'success','apkurl'=>$this->apkdownloadurl.$newgamename,'sourcesn'=>$sourcesn));
						exit();
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
        error_log(date('Y-m-d H:i:s') .'-----------sql----------> '.M()->getlastsql()."\n", 3, '/data/a.log');
		if ($source) {
			if ($source["isupload"] == 1 && $source["apkurl"] != "") {
				$time = date('Y-m-d H:i:s',time());
				$this->insertLog('推广链接','下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户通过推广链接在ID为“".$source["channelid"]."”渠道下下载了ID为“".$source['gameid']."”游戏包");
				Header("Location: ".$this->apkdownloadurl.$source["apkurl"]." ");
				exit();
			} else {
				$gamemodel = M('tg_game');
				$game = $gamemodel->find($source["gameid"]);
				$packagename = $game["packagename"];
				if ($game["gameversion"] != "") {
					$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
				} else {
					$newgamename = $game["gamepinyin"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
				}
				$result = $this->subpackage($packagename,$newgamename,$sourcesn);
				if ($result == "true") {
					$data["isupload"] = 1;
					$data["apkurl"] = $newgamename;
					$upload = $sourcemodel->where($map)->save($data);
					$time = date('Y-m-d H:i:s',time());
					$this->insertLog('推广链接','下载APK包', 'SourceAction.class.php', 'downloadapk', $time, "用户通过推广链接在ID为“".$source["channelid"]."”渠道下下载了ID为“".$source['gameid']."”游戏包");
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
    public function downloadTextture(){
        $sourcesn = $_POST["source"];
        $sourcemodel = M('tg_source');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();
        $texturename = $source['textureurl'];
        $gameid = $source['gameid'];
        $channelid = $source['channelid'];
        $oldgame = $gamemodel->where("gameid = '$gameid'")->find();
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
			echo '母包不存在';
			exit();
		}
		if (!copy($sourfile, $newfile)) {
			echo '无法创建文件，打包失败。';
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
    	echo '无法创建文件，打包失败。';
		exit();
	}

    // 用户-资源-推广链接-手机页面
    public function page(){
    	$sourceid = $_GET['sourceid'];
	
        $prefix = C('DB_PREFIX');
        $where=' and a.id="'.$sourceid.'"';
        $sql="SELECT b.*,c.categoryname
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