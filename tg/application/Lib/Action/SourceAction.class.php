<?php
class SourceAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    // 资源列表
    public function index(){
        $this->logincheck();

        $Index = D("Source");
        if(isset($this->userpid) && $this->userpid>0){ //子账号
        	$game = $Index->indexson();
	        $this->assign('sourcestr',$game['sourcestr']);//我的推广
	        $this->assign('userchannelid',$this->userchannelid);//子账号的channelid 	

        	$this->display('indexson');
        }else{
	        $game = $Index->index();
	        $this->assign('category',$game['category']);
	        $this->assign('tag',$game['tag']);
	        $this->assign('channel',$game['channel']);
	        $this->assign('gamestr',$game['gamestr']);
	        $this->assign('sourcestr',$game['sourcestr']);//我的推广
	        $this->display();
        }
    }

	//申请
    public function applyGame(){
		$this->logincheck();
		if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}

		$gameid = $_POST["game"];
		$channelid = $_POST["channel"];
        $userid = $_SESSION['userid'];

        $gamemodel = M('tg_game');
		$checkgame = $gamemodel->where(array('gameid' => $gameid))->find();
		if(empty($checkgame)){
			$this->applyCgame($gameid,$channelid,$userid);
		}
		$usermodel = M('tg_user');
		$sourcemodel = M('tg_source');
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
			$newgamename = createstr(30);
			$sourcesn = "tg_".$newgamename;
			$newgamename = $newgamename.".apk";
			$texturename = $game["texturename"];
			$data['sourcesn'] = $sourcesn;

			$data['sourcesharerate'] = !empty($user["default_sharerate"]) ? $user["default_sharerate"] : 0;
			$data['sourcechannelrate'] = !empty($user["default_channelrate"]) ? $user["default_channelrate"] : 0;
			$data['textureurl'] = $texturename;
			$data['isupload'] = 0;
			$data['createuser'] = $user["realname"];
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
				//李梦君
				if($_SESSION['userid'] == 73){
					file_get_contents(C('admindomain')."/game/syncGameInfo?gameid=".$gameid);
				}
                $this->insertLog($_SESSION['account'],'申请资源', 'SourceAction.class.php', 'applyGame', $time, "用户" .$_SESSION['account']."在“".$channel["channelname"]."”渠道下申请了“".$game['gamename']."”游戏",$sourceid);
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


	protected function applyCgame($gameid,$channelid,$userid)
	{
		$usermodel = M('tg_user');
		$sourcemodel = M('cps_source');
		$channelmodel = M('tg_channel');
		$gamemodel = M('cps_game');
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
			$data['createtime'] = date('Y-m-d : H-i-s', time());
			$newgamename = createstr(30);
			$sourcesn = "cp_" . $newgamename;
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
			$sourceid = $sourcemodel->add($data);
			$time = date('Y-m-d H:i:s', time());
			if ($sourceid) {
				$inccondition["channelid"] = $channelid;
				$channelmodel->where($inccondition)->setInc('gamecount');
				$channel = $channelmodel->find($channelid);
				$this->insertLog($_SESSION['account'], '申请资源', 'SourceAction.class.php', 'applyCgame', $time, "用户" . $_SESSION['account'] . "在“" . $channel["channelname"] . "”渠道下申请了“" . $game['gamename'] . "”游戏",$sourceid);
				$this->ajaxReturn('success', $data, 1);
				exit();
			} else {
				$this->ajaxReturn('fail', "失败，请联系管理员。", 0);
				exit();
			}
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
		if(empty($source)){
			$this->cpsDownTextture($sourcesn);
		}
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

	public function cpsDownTextture($sourcesn)
	{
		$sourcemodel = M('cps_source');
		$gamemodel = M('cps_game');
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

    //游戏分类筛选，以及渠道筛选
    public function selectGame(){
        $this->logincheck();
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $gametype = $_POST['gametype'];
        $gamecategory = $_POST['gamecategory'];
        $gamesize = $_POST['gamesize'];
        $gametag = $_POST['gametag'];
        $channelid = $_POST['gamechannel'];
        $order = $_POST['order'];
        $order_hot = $_POST['order_hot'];
        $Index = D("Source");
        $gamestr = $Index->selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid,$order,$order_hot,$this->sourcetype);
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
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $content = $_POST['content'];
		$channelid = $_POST['channelid'];
        $Index = D("Source");
        $gamestr = $Index->searchGame($content,$channelid,$this->sourcetype);
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
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
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
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $channelid = isset($_POST['channelid'])? (int)$_POST['channelid'] : 0;

		if($channelid <= 0){
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
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

	// --------------推广----------------------------------------------
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

		if(empty($game)){
			$sql="SELECT
        		b.gameid,
                b.gamepinyin
				FROM {$prefix}cps_source a
				LEFT JOIN {$prefix}cps_game b ON a.gameid=b.gameid
				WHERE 1 ".$where;
			$result=M()->query($sql);
			$game=$result[0];
		}
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

	/**
	 * 获取游戏推广链接
	 */
	public function getGameDowUrl()
	{
		$this->logincheck();

		$sourceid = isset($_POST['sourceid']) ? trim($_POST['sourceid']) : 0;
		$Source = D('Source');
		$data['long_url'] = $Source->getDownloadURL($sourceid);
		$data['short_url'] = $Source->shortenSinaUrl($data['long_url']);
		$data['status'] = 1;

		$this->ajaxReturn($data, 'JSON');
	}

    // 用户-资源-推广链接-手机页面
    public function page(){
    	$sourceid = $_GET['sourceid'];

		//选择模板
		$tpl = $this->selectTemplate($sourceid);

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

		if(empty($result)){
			$sql="SELECT
                b.*,
                c.categoryname
			FROM {$prefix}cps_source a
			LEFT JOIN {$prefix}cps_game b ON b.gameid=a.gameid
			LEFT JOIN {$prefix}tg_gamecategory c ON b.gamecategory=c.id
			WHERE 1 ".$where;
			$result=M()->query($sql);
		}
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
        $long_url .= '/'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $this->assign("long_url",$long_url);

        $this->assign("sourceid",$sourceid);

        $this->display($tpl);
    }


	private function selectTemplate($sourceid)
	{
		$gamePage = array(
			316 => 'jlb'
		);
		$sourceMode = M('tg_source');
		$source = $sourceMode->find($sourceid);


		if(array_key_exists($source['gameid'], $gamePage)){


			return $gamePage[$source['gameid']];
		}
		return 'page';
	}

    public function llq(){
        $this->display();
    }

    //推广链接，下载资源包
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

			//如果cdn已经提交成功，并且cdn文件存在，读取cdn。
			if($source["is_cdn_submit"] == 1 ){
				if ($source["isupload"] == 1 && $source["apkurl"] != "") {
					$cndurl = $this->apkdownloadcdnurl.$source["apkurl"];
					Header("Location: ".$cndurl);
					exit();
				}else{
					$sourceModel = D('Source');
					$return = $sourceModel->createSourePackage($sourcesn);
                    if($return['code']==1){
                        Header("Location: ".$return['data']);
                        exit();
                    }else{
                        echo '发生错误：'.$return['msg'];
                    }
				}
			}

			if ($source["isupload"] == 1 && $source["apkurl"] != "") {
				$apkdownloadurl = $this->apkdownloadurl;
				//开启新分包
				$gamemodel = M('tg_game');
				$game = $gamemodel->find($source["gameid"]);
				$sourceModel = D('Source');
				$checkNewPackage = $sourceModel->checkNewPackage($source['sourcesn'], $game['sdkgameid']);
				if($checkNewPackage === true){
					$apkdownloadurl = C('mountedFolder');
				}
				Header("Location: ".$apkdownloadurl.$source["apkurl"]." ");
				exit();
			} else {
				$sourceModel = D('Source');
				$return = $sourceModel->createSourePackage($sourcesn);
                if($return['code']==1){
                    Header("Location: ".$return['data']);
                    exit();
                }else{
                    echo '发生错误：'.$return['msg'];
                }
			}
		} else {

			$this->cpsDownload($sourcesn);
//			echo "Can't find APK package.";
		}
	}

	public function cpsDownload($sourcesn)
	{
		$sourcemodel = M('cps_source');
		$map["sourcesn"] = $sourcesn;
		$source = $sourcemodel->where($map)->find();

		if ($source) {

			//如果cdn已经提交成功，并且cdn文件存在，读取cdn。
			if($source["is_cdn_submit"] == 1 ){
				if ($source["isupload"] == 1 && $source["apkurl"] != "") {
					$cndurl = $this->apkdownloadcdnurl.$source["apkurl"];
					Header("Location: ".$cndurl);
					exit();
				}else{
					$sourceModel = D('Source');
					$return = $sourceModel->cpsCreateSourePackage($sourcesn);
					if($return['code']==1){
						Header("Location: ".$return['data']);
						exit();
					}else{
						echo '发生错误：'.$return['msg'];
					}
				}
			}

			if ($source["isupload"] == 1 && $source["apkurl"] != "") {
				$apkdownloadurl = $this->apkdownloadurl;
				//开启新分包
				$gamemodel = M('cps_game');
				$game = $gamemodel->find($source["gameid"]);
				$sourceModel = D('Source');
				$checkNewPackage = $sourceModel->checkNewPackage($source['sourcesn'], $game['sdkgameid']);
				if($checkNewPackage === true){
					$apkdownloadurl = C('mountedFolder');
				}
				Header("Location: ".$apkdownloadurl.$source["apkurl"]." ");
				exit();
			} else {
				$sourceModel = D('Source');
				$return = $sourceModel->cpsCreateSourePackage($sourcesn);
				if($return['code']==1){
					Header("Location: ".$return['data']);
					exit();
				}else{
					echo '发生错误：'.$return['msg'];
				}
			}
		} else {
			echo "Can't find APK package.";
		}
	}

	// ---------自定义子账号的费率--------------------------------------
    // 自定义子账号的费率 视图
    public function defineRate(){
    	$this->logincheck();

    	// 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }

    	$sourceid = $_GET['sourceid'];

    	$sourceModel = M('tg_source');
    	//获取该资源的相关信息
    	$where = array('id' => $sourceid );
    	$source = $sourceModel->alias('S')
    			->join(C('DB_PREFIX').'tg_channel as C on S.channelid=C.channelid','left')
    			->join(C('DB_PREFIX').'tg_game as G on S.gameid=G.gameid','left')
    			->join(C('DB_PREFIX').'tg_user as U on S.channelid=U.channelid','left')
    			->field('S.id,S.sourcesharerate,S.sourcechannelrate,S.sub_share_rate,S.sub_channel_rate,C.channelname,G.gamename,U.account')
    			->where($where)
    			->find();
		if(empty($source)){
			$source = $this->cpsDefineRate($sourceid);
		}

    	$this->assign('source',$source);
    	$this->display();
    }

	public function cpsDefineRate($sourceid)
	{
		$sourceModel = M('cps_source');
		//获取该资源的相关信息
		$where = array('id' => $sourceid );
		$source = $sourceModel->alias('S')
			->join(C('DB_PREFIX').'tg_channel as C on S.channelid=C.channelid','left')
			->join(C('DB_PREFIX').'cps_game as G on S.gameid=G.gameid','left')
			->join(C('DB_PREFIX').'tg_user as U on S.channelid=U.channelid','left')
			->field('S.id,S.sourcesharerate,S.sourcechannelrate,S.sub_share_rate,S.sub_channel_rate,C.channelname,G.gamename,U.account')
			->where($where)
			->find();

		//print_r($source);exit;
		return $source;
	}

    // 自定义子账号的费率 处理
    public function defineRateHandle(){
    	if (!$this->isAjax()){
    		$this->ajaxReturn("fail",'非法访问',0);
    	}

		$sourceid = $_POST['sourceid'];
		$sub_share_rate = trim($_POST['sub_share_rate']);
		$sub_channel_rate = trim($_POST['sub_channel_rate']);

		$sourceModel= M('tg_source');
		$gameModel = M('tg_game');

		if(!isset($sub_channel_rate)){
            $this->ajaxReturn("fail",'分成比例不能为空',0);
        }
        if(!isset($sub_channel_rate)){
            $this->ajaxReturn("fail",'渠道费不能为空',0);
        }

       	$reg = '/^0|([0-9]+.?[0-9]*)$/';
       	if(!preg_match($reg,$sub_channel_rate)){
       		$this->ajaxReturn("fail",'分成比例必须为大于等于0小于1的小数',0);
       	}
       	if(!preg_match($reg,$sub_channel_rate)){
       		$this->ajaxReturn("fail",'渠道费必须为大于等于0小于1的小数',0);
        }

        //获取原来的资源信息
        $where = array('id'=>$sourceid);
        $oldsource = $sourceModel->field('sourcesharerate,sourcechannelrate,sub_share_rate,sub_channel_rate,channelid,gameid')->where($where)->find();

		if(empty($oldsource)){
			$sourceModel= M('cps_source');
			$gameModel= M('cps_game');
			$oldsource = $sourceModel->field('sourcesharerate,sourcechannelrate,sub_share_rate,sub_channel_rate,channelid,gameid')->where($where)->find();
		}

        //分成比例不能母账号的大
        if($sub_share_rate > $oldsource['sourcesharerate']){
        	$this->ajaxReturn("fail",'子账号的分成比例不能大于等于母账号的分成比例',0);
        }	
        //渠道费必须大于等于母账号的
        if($sub_channel_rate < $oldsource['sourcechannelrate']){
        	$this->ajaxReturn("fail",'子账号的渠道费不能小于母账号的渠道费',0);
        }	

        // 保存子账号资源费率
     	$data = array();
		$data["sub_share_rate"] = $sub_share_rate;
		$data["sub_channel_rate"] = $sub_channel_rate;
		$source = $sourceModel->where($where)->save($data);

		if ($source!==false) {
	        $channelid = $oldsource['channelid'];
	        $gameid = $oldsource['gameid'];

	        $channelmodel =  M('tg_channel');
	        $channel = $channelmodel->alias('C')
	        		->join(C('DB_PREFIX').'tg_user as U on U.channelid=C.channelid','left')
	        		->field('C.channelname,U.account')
	        		->where("C.channelid = '$channelid'")
	        		->find();

	        $game = $gameModel->field('gamename')->where("gameid = '$gameid'")->find();

            $this->insertLog($_SESSION['account'],'自定义子账号资源费率', 'SourceAction.class.php', 'defineRateHandle', $time, $_SESSION['account']."编辑了子账户“".$channel['account']."”的渠道名为“".$channel['channelname']."”游戏名为“".$game['gamename']."”，分成比例由“".$oldsource['sub_share_rate']."变为".$data["sub_share_rate"] ."”，通道费由“".$oldsource['sub_channel_rate']."变为".$data['sub_channel_rate']."”");
            $this->ajaxReturn('success',"成功。",1);
		} else {
			$this->ajaxReturn('fail','出现一个错误，请联系管理员。',0);
		}
    }

	public function cpsDefineRateHandle()
	{

	}


}
?>