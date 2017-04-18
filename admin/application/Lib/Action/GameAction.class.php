<?php
class GameAction extends CommonAction {
	public function __construct(){
    	parent::__construct();


    }

	//游戏列表
	public function gameall(){
		$this->logincheck();
        $this->authoritycheck(10103);
        $gamemodel = M("tg_game");
        $gamecondition["G.activeflag"] = 1;
        $gamelist = $gamemodel->alias("G")
        				->join(C('DB_PREFIX')."tg_gamecategory C on G.gamecategory = C.id", "LEFT")
        				->join(C('DB_PREFIX')."tg_gametag T on G.gametag = T.id", "LEFT")
        				->join(C('DB_PREFIX')."tg_package P on G.gameid = P.gameid and P.isnowactive = 1 ", "LEFT")
        				->field('G.gameid,G.gamename,G.gamepinyin,G.gametype,G.gamesize,G.gameauthority,G.sharerate,G.channelrate,G.isonstack,C.categoryname,T.tagname,P.packageversion')
        				->where($gamecondition)
        				->order("G.gameid desc")
        				->select();
        				
        $this->assign('gamelist',$gamelist);
        $this->assign("editgame",$this->authoritycheck(10127));
        $this->assign("deletegame",$this->authoritycheck(10128));
        $this->menucheck();
        $this->display();
    }
	
	//新增游戏 视图
    public function newgame(){
		$this->logincheck();
        $this->authoritycheck(10092);

        $prefix = C('DB_PREFIX');
        $where=" and a.isdelete=0 and not exists(SELECT b.gameid FROM {$prefix}tg_game b WHERE b.sdkgameid=a.id) ";
        $sql="SELECT
                a.*
        FROM {$prefix}all_game a
        WHERE 1 ".$where;
        $sdkgamelist=M()->query($sql);

        $gamecategorymodel= M('tg_gamecategory');
        $condition["activeflag"] = 1;
        $gamecategory = $gamecategorymodel->where($condition)->order("id desc")->select();
        $gametagmodel= M('tg_gametag');
        $condition["activeflag"] = 1;
        $gametag = $gametagmodel->where($condition)->order("id desc")->select();
        $this->assign('sdkgamelist',$sdkgamelist);
        $this->assign('gamecategory',$gamecategory);
        $this->assign('gametag',$gametag);
        $this->assign('sourceType',C('sourceType'));
        $this->menucheck();
        $this->display();
    }
	
	//新增游戏 处理
	public function addgame(){
		$this->logincheck();
		$max_file_size = '1000000000'; //文件小于1GB
		$max_image_size = '5000000'; //图片小于5MB
		$gameicon = ""; //游戏图标
		$gamebg = ""; //游戏背景
		$screenshot1 = ""; //游戏截图1
		$screenshot2 = ""; //游戏截图2
		$screenshot3 = ""; //游戏截图3
		$screenshot4 = ""; //游戏截图4
		$screenshot5 = ""; //游戏截图5
		$packagename = ""; //游戏白包名
		$texturename = ""; //游戏素材名
		$packagesize = 0;
		$bao_extension_list = array("apk", "ipa", "rar", "zip");
		$img_extension_list = array("jpg", "jpeg", "gif", "png");

		// 上传图片
		if (!empty($_FILES)) {
			if (is_uploaded_file($_FILES["gamepackage"]["tmp_name"])) {
				$tempFile = $_FILES["gamepackage"]["tmp_name"];
				$targetPath = $this->packageStoreFolder;
				$filesize = $_FILES["gamepackage"]["size"];
				$packagesize = floor($filesize/1000000);
				if ($filesize > $max_file_size) {
					$this->ajaxReturn('fail',"上传游戏包不能大于1GB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gamepackage"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$bao_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏包格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$packageFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$packageFile)) {  
					$this->ajaxReturn('fail',"上传游戏包失败。",0);
					exit();
				}
				$packagename = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["texturepackage"]["tmp_name"])) {
				$tempFile = $_FILES["texturepackage"]["tmp_name"];
				$targetPath = $this->textureStoreFolder;
				$filesize = $_FILES["texturepackage"]["size"];
				if ($filesize > $max_file_size) {
					$this->ajaxReturn('fail',"上传素材包不能大于1GB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["texturepackage"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$bao_extension_list)) {
					$this->ajaxReturn('fail',"上传素材包格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$textureFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$textureFile)) {  
					$this->ajaxReturn('fail',"上传素材包失败。",0);
					exit();
				}
				$texturename = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["gameicon"]["tmp_name"])) {
				$tempFile = $_FILES["gameicon"]["tmp_name"];
				$targetPath = $this->iconStoreFolder;
				$filesize = $_FILES["gameicon"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏图标不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gameicon"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏图标格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$gameiconFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$gameiconFile)) {  
					$this->ajaxReturn('fail',"上传游戏图标失败。",0);
					exit();
				}
				$gameicon = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["gamebg"]["tmp_name"])) {
				$tempFile = $_FILES["gamebg"]["tmp_name"];
				$targetPath = $this->gamebgStoreFolder;
				$filesize = $_FILES["gamebg"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏背景不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gamebg"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏背景格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$gamebgFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$gamebgFile)) {  
					$this->ajaxReturn('fail',"上传游戏背景失败。",0);
					exit();
				}
				$gamebg = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot1"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot1"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot1"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图1不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot1"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图1格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot1File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot1File)) {  
					$this->ajaxReturn('fail',"上传游戏截图1失败。",0);
					exit();
				}
				$screenshot1 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot2"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot2"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot2"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图2不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot2"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图2格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot2File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot2File)) {  
					$this->ajaxReturn('fail',"上传游戏截图2失败。",0);
					exit();
				}
				$screenshot2 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot3"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot3"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot3"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图3不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot3"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图3格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot3File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot3File)) {  
					$this->ajaxReturn('fail',"上传游戏截图3失败。",0);
					exit();
				}
				$screenshot3 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot4"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot4"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot4"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图4不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot4"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图4格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot4File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot4File)) {  
					$this->ajaxReturn('fail',"上传游戏截图4失败。",0);
					exit();
				}
				$screenshot4 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot5"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot5"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot5"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图5不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot5"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图5格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot5File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot5File)) {  
					$this->ajaxReturn('fail',"上传游戏截图5失败。",0);
					exit();
				}
				$screenshot5 = $cacheFileName;
			}
		}

		$data['guard'] = !empty($_POST['guard']) ? ','.implode(',',$_POST['guard']).',' : '';
		$data['sdkgameid'] = $_POST['sdkgameid'];
        $data['gamename'] = $_POST['gamename'];
        $data['gamepinyin'] = $_POST['gamepinyin'];
		$data['gametype'] = $_POST['gametype'];
		$data['gamecategory'] = $_POST['gamecategory'];
		$data['gametag'] = $_POST['gametag'];
        $data['sharetype'] = "CPS";
        $data['gameauthority'] = $_POST['gameauthority'];
        $data['sharerate'] = $_POST['sharerate'];
        $data['joinsharerate'] = $_POST['joinsharerate'];
		$data['beizhumessage'] = $_POST['beizhumessage'];
		$data['channelrate'] = $_POST['channelrate'];
		$data['score'] = $_POST['score'];
		if (isset($_POST['publishtime']) && $_POST['publishtime'] != "") {
			$data['publishtime'] = $_POST['publishtime']." 00:00:00";
		} else {
			$data['publishtime'] = date("Y-m-d H:i:s",time());
		}
		if ($packagename != "") {
			$data['packagename'] = $packagename;
			$data['gamesize'] = $packagesize;
			require_once "apk.class.php";
			$apkobj = new apk();
			$apkobj->open($packageFile);
			$data['gameversion'] = $apkobj->getVersionName();
			$data['packageversion'] = $apkobj->getPackage();
			$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
			if (!stripos($newsign, 'yxgames')) {
				$this->ajaxReturn('fail',"游戏签名有错误.",0);
				exit();
			}
		}
		$data['isonstack'] = $_POST['isonstack'];
		$data['isusedvoucher'] = $_POST['isusedvoucher'];
		if ($gameicon != "") {
			$data['gameicon'] = $gameicon;
		}
		if ($gamebg != "") {
			$data['gamebg'] = $gamebg;
		}
		if ($screenshot1 != "") {
			$data['screenshot1'] = $screenshot1;
		}
		if ($screenshot2 != "") {
			$data['screenshot2'] = $screenshot2;
		}
		if ($screenshot3 != "") {
			$data['screenshot3'] = $screenshot3;
		}
		if ($screenshot4 != "") {
			$data['screenshot4'] = $screenshot4;
		}
		if ($screenshot5 != "") {
			$data['screenshot5'] = $screenshot5;
		}
		if ($texturename != "") {
			$data['texturename'] = $texturename;
		}
		$data['description'] = $_POST['description'];
		$data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
		$data['createuser'] = "Admin";
		$data['updatetime'] = date('Y-m-d H:i:s',time());
		$data['updateuser'] = "Admin";
		$model = M('tg_game');
		$map['gamename'] = $_POST['gamename'];
		$map['activeflag'] = 1;
		$gamename = $model->where($map)->find();
		if($gamename){
			$this->ajaxReturn('fail','该游戏已存在',0);
			exit();
		} else{
			$game = $model->add($data);
			$addgameError=$model->getDbError();
			// 添加游戏写日志
			if($addgameError){
				$log_content=date('Y-m-d H:i:s')."\n";
				$log_content.='游戏新增error：'.print_r($addgameError,1)."\n";
				$log_content.='sql：'.$model->getlastsql()."\n";
				error_log($log_content, 3, 'test.log');
			}else {
				$log_content=date('Y-m-d H:i:s')."\n";
				$log_content.='游戏新增sql：'.$model->getlastsql()."\n";
				error_log($log_content, 3, 'test.log');
			}

			if ($game) {
				$packageModel = M('tg_package');
				$packagedata['gameid'] = $game;
				$packagedata['gamename'] = $data['gamename'];
				$packagedata['gameversion'] = $data['gameversion'];
				$packagedata['gamesize'] = $data['gamesize'];
				$packagedata['packagename'] = $data['packagename'];
				$packagedata['packageversion'] = $data['packageversion'];
				$packagedata['viewname'] = $data["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
				$packagedata['activeflag'] = 1;
				$packagedata['createtime'] = date('Y-m-d H:i:s',time());
				$packagedata['createuser'] = "Admin";
				$packagedata['isnowactive'] = 1;
				$packageModel->add($packagedata);
                $this->insertLog($_SESSION['adminname'],'新增游戏', 'GameAction.class.php', 'addgame',  $data['createtime'], $_SESSION['adminname']."添加游戏名为：“".$data['gamename']."”游戏");
                $this->ajaxReturn('success','游戏上传成功。',1);
				exit();
			} else {
				$this->ajaxReturn('fail','上传游戏失败，请检查项目是否填写正确。',0);
				exit();
			}
		}  
    }

	//编辑游戏 视图
    public function gamedetail(){
		$this->logincheck();
        $gameid = $_GET['gameid'];
		if ($gameid == 0) {
			Header("Location: /gameall/ ");
			exit();
		} else {
			$model= M('tg_game');
			$game = $model->find($gameid);
			$sdkgamemodel= M('all_game');
			$sdkcondition["isdelete"] = 0;
			$sdkgamelist = $sdkgamemodel->where($sdkcondition)->order("id desc")->select();
			$gamecategorymodel= M('tg_gamecategory');
			$condition["activeflag"] = 1;
			$gamecategory = $gamecategorymodel->where($condition)->order("id desc")->select();
			$gametagmodel= M('tg_gametag');
			$condition["activeflag"] = 1;
			$gametag = $gametagmodel->where($condition)->order("id desc")->select();
			$packagemodel= M('tg_package');
			$packagecondition["gameid"] = $gameid;
			$packagecondition["activeflag"] = 1;
			$packagelist = $packagemodel->where($packagecondition)->order("packageid asc")->select();
			$latestpackage = $packagemodel->where($packagecondition)->order("packageid desc")->find();
			$versionstr = "";
			foreach ($packagelist as $k => $v) {
				$versionstr .= $v["gameversion"].",";
			}
			$game['guardArr'] = explode(',',trim($game['guard'],','));

			$this->assign('game',$game);
			$this->assign('sdkgamelist',$sdkgamelist);
			$this->assign('gamecategory',$gamecategory);
			$this->assign('gametag',$gametag);
			$this->assign('packagelist',$packagelist);
			$this->assign('latestpackage',$latestpackage);
			$this->assign('versionstr',$versionstr);
			$this->assign('sourceType',C('sourceType'));
            $this->menucheck();
			$this->display();
		}
    }

	//编辑游戏时 基本资料处理
	public function editgame(){
		$this->logincheck();
		$gameid = $_POST['gameid'];
		$data['gametype'] = $_POST['gametype'];
		$data['gamecategory'] = $_POST['gamecategory'];
		$data['gametag'] = $_POST['gametag'];
        $data['gameauthority'] = $_POST['gameauthority'];
        $data['sharerate'] = $_POST['sharerate'];
        $data['joinsharerate'] = $_POST['joinsharerate'];

        // 如果设置的分成比例 小于（针对没有自定义分成比例的）子账号的分成比例
        $big_subuser = array();
        $big_subuser_str = '子账号：';
        $source_model = M('tg_source');
        $source_where = array('gameid'=>$gameid);
        $source_where['isfixrate'] = 0; // 增加没有自定义分成比例
        $source = $source_model->alias('S')
        			->join(C('DB_PREFIX').'tg_user U on S.channelid=U.channelid','left')
        			->join(C('DB_PREFIX').'tg_user U1 on U.pid=U1.userid','left')
        			->field('S.sub_share_rate,S.sub_channel_rate,U.account,U1.account as paccpunt')
        			->where($source_where)
        			->select();
        foreach ($source as $key => $value) {
        	if($_POST['sharerate'] < $value['sub_share_rate'] ){
        		$big_subuser[] = $value;
        	}
        }
        foreach ($big_subuser as $key => $value) {
        	$big_subuser_str .= $value['account'].'(母：'.$value['paccpunt'].')、';
        }
        if($big_subuser){
        	$this->ajaxReturn('fail',$big_subuser_str.'比母账号的分成比例大，联系母账号修改。',0);
        }
        
		$data['beizhumessage'] = $_POST['beizhumessage'];
		$data['channelrate'] = $_POST['channelrate'];
		$data['score'] = $_POST['score'];
		if (isset($_POST['publishtime']) && $_POST['publishtime'] != "") {
			$data['publishtime'] = $_POST['publishtime']." 00:00:00";
		} else {
			$data['publishtime'] = date("Y-m-d H:i:s",time());
		}
		$data['isonstack'] = $_POST['isonstack'];
		$data['isusedvoucher'] = $_POST['isusedvoucher'];
		$data['updatetime'] = date('Y-m-d H:i:s',time());
		$data['updateuser'] = "Admin";
		$model = M('tg_game');
        $condition["gameid"] = $gameid;
        $oldgame = $model->where($condition)->find();
        if($oldgame['isonstack'] == 0){
            $oldgame['stackname'] = "正常";
        } elseif($oldgame['isonstack'] == 1){
            $oldgame['stackname'] = "未上架";
        } else{
            $oldgame['stackname'] = "已下架";
        }
        if($oldgame['isusedvoucher'] == 0){
            $oldgame['isusedvouchername'] = "不能";
        } elseif($oldgame['isusedvoucher'] == 1){
            $oldgame['isusedvouchername'] = "能";
        }
        if($data['isonstack'] == 0){
            $data['stackname'] = "正常";
        } elseif($data['isonstack'] == 1){
            $data['stackname'] = "未上架";
        } else{
            $data['stackname'] = "已下架";
        }
        if($data['isusedvoucher'] == 0){
            $data['isusedvouchername'] = "不能";
        } elseif($data['isusedvoucher'] == 1){
            $data['isusedvouchername'] = "能";
        }

		$data['guard'] = !empty($_POST['guard']) ? ','.implode(',',$_POST['guard']).',' : '';
        $game = $model->where($condition)->save($data);

		if ($game) {
            $this->insertLog($_SESSION['adminname'],'编辑游戏', 'GameAction.class.php', 'editgame',  $data['updatetime'], $_SESSION['adminname']."编辑了游戏“".$oldgame['gamename']."”，权重由“".$oldgame['gameauthority']."变为".$data["gameauthority"] ."”，分成比例由“".$oldgame['sharerate']."变为".$data['sharerate']."”通道费由“".$oldgame['channelrate']."”变为“".$data['channelrate']."”上架状态由“".$oldgame['stackname']."”变为“".$data['stackname']."”"."”是否能使用代金券由“".$oldgame['isusedvouchername']."”变为“".$data['isusedvouchername']."”");

	        // 并把所有渠道中 没有固定分成比例的 分成比例改成最新的
	        $sourceModel=M('tg_source');
	        $sourceWhere=array(
	        	'gameid'=>$gameid,
	        	'isfixrate'=>0,
	        );
	        $sourceData=array(
	        	'sourcesharerate'=>$_POST['sharerate'],
	        	'sourcechannelrate'=>$_POST['channelrate'],
	        );
	        $soureResult=$sourceModel->where($sourceWhere)->save($sourceData);

            $this->ajaxReturn('success','游戏信息修改成功。',1);
			exit();
		} else {
			$this->ajaxReturn('fail','修改游戏失败，请检查项目是否填写正确。',0);
			exit();
		}
    }

	//删除游戏
    public function deleteGame() {
		$this->logincheck();
        $gameid = $_POST['gameid'];
        $model = M('tg_game');
		$data["activeflag"] = 0;
		$condition["gameid"] = $gameid;
		$deletegame = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $game = $model->where($condition)->find();
        if($deletegame){
            $this->insertLog($_SESSION['adminname'],'删除游戏', 'GameAction.class.php', 'deleteGame',  $time, $_SESSION['adminname']."删除游戏名为：“".$game['gamename']."”游戏");
            $this->ajaxReturn('success','游戏删除成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','游戏删除失败。',0);
			exit();
		}
    }


    // 更新游戏包
    // 普通包，覆盖包，都是以下几个步骤
    // 1、tg_package添加游戏包信息
    // 2、更新该游戏的所有包状态，以前包设置为不是当前包，刚更新的包为最新的包
    // 3、更新该游戏的所有资源，把资源的游戏包下载链接放在tg_oldpackage表里面，把资源表里面的游戏包下载链接清空，设置为没有上传
    // 4、更新tg_game游戏基本信信息表和包信息
    
    // 强更包
    // 强更包是为了解决：
    // 1、提交录入将要更新包信息，过段时间更新。
    // 2、提交生成临时的资源包，不用强更点所有用户蜂拥下载
    // 流程：
    // 1、tg_package添加游戏包信息
    // 2、更新该游戏的所有包状态，以前包设置为不是当前包，刚更新的包为最新的包
    // 3、更新该游戏的所有资源，把资源的游戏包下载链接放在tg_oldpackage表里面，生成临时的资源包（forcepackage）
    // 4、更新tg_game游戏基本信息表
    // 5、更新all_game强更信息
    // 6、强更点到了:a、更新游戏的包信息；b、把资源表里面的游戏包下载切换成新的下载链接；c、包表为强更过。d、强更表设置为强更过
    

	// 编辑游戏时，上传游戏包
	public function uploadpackage(){
		$this->logincheck();
		$gameid = $_POST['uploadgameid'];

		$max_file_size = '1000000000'; //文件小于1GB
		$max_image_size = '5000000'; //图片小于5MB
		$packagename = ""; //游戏白包名
		$gameicon = ""; //游戏图标
		$gamebg = ""; //游戏图标
		$screenshot1 = ""; //游戏截图1
		$screenshot2 = ""; //游戏截图2
		$screenshot3 = ""; //游戏截图3
		$screenshot4 = ""; //游戏截图4
		$screenshot5 = ""; //游戏截图5
		$texturename = ""; //游戏素材名
		$bao_extension_list = array("apk", "ipa", "rar", "zip");
		$img_extension_list = array("jpg", "jpeg", "gif", "png");

		$data = array(); //包相关信息
		$infodata = array(); //除包之外的其他基本相关信息

		if (!empty($_FILES)) {
			if (is_uploaded_file($_FILES["gamepackage"]["tmp_name"])) {
				$tempFile = $_FILES["gamepackage"]["tmp_name"];
				$targetPath = $this->packageStoreFolder;
				$filesize = $_FILES["gamepackage"]["size"];
				$packagesize = floor($filesize/1000000);
				if ($filesize > $max_file_size) {
					$this->ajaxReturn('fail',"上传游戏不能大于1GB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gamepackage"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$bao_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$packageFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$packageFile)) {  
					$this->ajaxReturn('fail',"上传游戏失败。",0);
					exit();
				}
				$packagename = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["texturepackage"]["tmp_name"])) {
				$tempFile = $_FILES["texturepackage"]["tmp_name"];
				$targetPath = $this->textureStoreFolder;
				$filesize = $_FILES["texturepackage"]["size"];
				if ($filesize > $max_file_size) {
					$this->ajaxReturn('fail',"上传素材不能大于1GB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["texturepackage"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$bao_extension_list)) {
					$this->ajaxReturn('fail',"上传素材格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$textureFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$textureFile)) {  
					$this->ajaxReturn('fail',"上传素材失败。",0);
					exit();
				}
				$texturename = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["gameicon"]["tmp_name"])) {
				$tempFile = $_FILES["gameicon"]["tmp_name"];
				$targetPath = $this->iconStoreFolder;
				$filesize = $_FILES["gameicon"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏图标不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gameicon"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏图标格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$gameiconFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$gameiconFile)) {  
					$this->ajaxReturn('fail',"上传游戏图标失败。",0);
					exit();
				}
				$gameicon = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["gamebg"]["tmp_name"])) {
				$tempFile = $_FILES["gamebg"]["tmp_name"];
				$targetPath = $this->gamebgStoreFolder;
				$filesize = $_FILES["gamebg"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏图标不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["gamebg"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏图标格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$gamebgFile = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$gamebgFile)) {  
					$this->ajaxReturn('fail',"上传游戏图标失败。",0);
					exit();
				}
				$gamebg = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot1"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot1"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot1"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图1不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot1"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图1格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot1File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot1File)) {  
					$this->ajaxReturn('fail',"上传游戏截图1失败。",0);
					exit();
				}
				$screenshot1 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot2"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot2"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot2"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图2不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot2"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图2格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot2File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot2File)) {  
					$this->ajaxReturn('fail',"上传游戏截图2失败。",0);
					exit();
				}
				$screenshot2 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot3"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot3"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot3"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图3不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot3"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图3格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot3File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot3File)) {  
					$this->ajaxReturn('fail',"上传游戏截图3失败。",0);
					exit();
				}
				$screenshot3 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot4"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot4"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot4"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图4不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot4"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图4格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot4File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot4File)) {  
					$this->ajaxReturn('fail',"上传游戏截图4失败。",0);
					exit();
				}
				$screenshot4 = $cacheFileName;
			}
			if (is_uploaded_file($_FILES["screenshot5"]["tmp_name"])) {
				$tempFile = $_FILES["screenshot5"]["tmp_name"];
				$targetPath = $this->screenshotStoreFolder;
				$filesize = $_FILES["screenshot5"]["size"];
				if ($filesize > $max_image_size) {
					$this->ajaxReturn('fail',"上传游戏截图5不能大于5MB。",0);
					exit();
				}
				$ftypearr = explode('.',$_FILES["screenshot5"]["name"]);
				$cacheindex = sizeof($ftypearr) - 1;
				if ($cacheindex >= 0) {
					$ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
				}
				if (!in_array($ftype,$img_extension_list)) {
					$this->ajaxReturn('fail',"上传游戏截图5格式不正确。",0);
					exit();
				}
				$cacheFileName = createstr(30).".".$ftype;
				$screenshot5File = $targetPath.$cacheFileName;
				if (!move_uploaded_file($tempFile,$screenshot5File)) {  
					$this->ajaxReturn('fail',"上传游戏截图5失败。",0);
					exit();
				}
				$screenshot5 = $cacheFileName;
			}
		}
		
		if ($packagename != "") {
			$data['gamesize'] = $packagesize;
			$data['packagename'] = $packagename;
			require_once "apk.class.php";
			$apkobj = new apk();
			$apkobj->open($packageFile);
			$data['gameversion'] = $apkobj->getVersionName();
			$data['packageversion'] = $apkobj->getPackage();
		}
		if ($gameicon != "") {
			$infodata['gameicon'] = $gameicon;
		}
		if ($gamebg != "") {
			$infodata['gamebg'] = $gamebg;
		}
		if ($screenshot1 != "") {
			$infodata['screenshot1'] = $screenshot1;
		}
		if ($screenshot2 != "") {
			$infodata['screenshot2'] = $screenshot2;
		}
		if ($screenshot3 != "") {
			$infodata['screenshot3'] = $screenshot3;
		}
		if ($screenshot4 != "") {
			$infodata['screenshot4'] = $screenshot4;
		}
		if ($screenshot5 != "") {
			$infodata['screenshot5'] = $screenshot5;
		}
		if ($texturename != "") {
			$infodata['texturename'] = $texturename;
		}
		$infodata['description'] = $_POST['description'];
		$gameModel = M('tg_game');
		$game = $gameModel->find($gameid);
		if ($packagename != "") {
			$packageModel = M('tg_package');
			$packagecondition["gameid"] = $gameid;
			$packagecondition["activeflag"] = 1;
			$packagecondition["isnowactive"] = 1;
			$exsitpackage = $packageModel->where($packagecondition)->order("packageid desc")->find();
			if ($exsitpackage) {
				// 游戏版本比较，1.9.0 应该 > 1.10.0
				if (strnatcmp($data['gameversion'],$exsitpackage["gameversion"])>0) {
					if ($_POST["isforcepackage"] == 1) {
						if (!empty($exsitpackage["gameversion"]) && $exsitpackage["packageversion"] != $data['packageversion']) {
							$this->ajaxReturn('fail',"游戏包名不相同.",0);
							exit();
						}
						$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
						if (!stripos($newsign, 'yxgames')) {
							$this->ajaxReturn('fail',"游戏签名有错误.",0);
							exit();
						}
						$historyversion = $_POST["historyversion"];
						$forcetime = $_POST["forcetime"];
						$latestversion = $data['gameversion'];
						if ($forcetime != "") {
							$packagedata['gameid'] = $gameid;
							$packagedata['gamename'] = $game['gamename'];
							$packagedata['gameversion'] = $data['gameversion'];
							$packagedata['gamesize'] = $data['gamesize'];
							$packagedata['packagename'] = $data['packagename'];
							$packagedata['packageversion'] = $data['packageversion'];
							$packagedata['viewname'] = $game["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
							$packagedata['activeflag'] = 1;
							$packagedata['createtime'] = date('Y-m-d H:i:s',time());
							$packagedata['createuser'] = "Admin";
							$packagedata['isnowactive'] = 1;
							$packagedata['isforcepackage'] = 1;
							$packagedata['isforced'] = 0;
							$packagedata['forcetime'] = $forcetime.":00";
							$packageresult = $packageModel->add($packagedata);

							if ($packageresult) {
								$packageid = $packageresult;
								$package = $packageModel->find($packageid);

								// 该游戏的 将以前所有的游戏包，设置为不是当前包
								$oldactivedata["isnowactive"] = 0;
								$oldactivecondition["gameid"] = $package["gameid"];
								$oldactiveresult = $packageModel->where($oldactivecondition)->save($oldactivedata);

								// 将刚才新增的游戏包，设置为当前包
								$activedata["isnowactive"] = 1;
								$activecondition["packageid"] = $packageid;
								$activeresult = $packageModel->where($activecondition)->save($activedata);

								// 更新该游戏的所有资源，把资源的游戏包下载链接放在tg_oldpackage表里面，强更batch中才把所有资源的强更成新的下载链接
								$sourceModel = M('tg_source');
								$sourcecondition["gameid"] = $package["gameid"];
								$sourcecondition["activeflag"] = 1;
								$sourcelist = $sourceModel->where($sourcecondition)->order("id desc")->select();
								$oldModel = M('tg_oldpackage');
								$olddata = array();
								foreach ($sourcelist as $k => $v) {
									$olddata[$k]["userid"] = $v["userid"];
									$olddata[$k]["channelid"] = $v["channelid"];
									$olddata[$k]["gameid"] = $v["gameid"];
									$olddata[$k]["apkurl"] = $v["apkurl"];
									$olddata[$k]["isdelete"] = 0;
									$olddata[$k]['activeflag'] = 1;
									$olddata[$k]['createtime'] = date('Y-m-d H:i:s',time());
									$olddata[$k]['createuser'] = "Admin";
								}
								if (sizeof($olddata) > 0) {
									$oldresult = $oldModel->addAll($olddata);
								}
								$gamecondition["gameid"] = $package["gameid"];
								//强更信息登陆时不更新游戏信息,只更新icon和素材，强更batch中才更新包信息
								$gameresult = $gameModel->where($gamecondition)->save($infodata);
								$forceModel = M('all_game');
								$forcedata["upversions"] = $historyversion;
								$forcedata["uptime"] = strtotime($forcetime.":00");
								$forcedata["lastupver"] = $latestversion;
								$forcecondition["id"] = $game["sdkgameid"];
								$forceresult = $forceModel->where($forcecondition)->save($forcedata);
								$this->insertLog($_SESSION['adminname'],'上传游戏包', 'GameAction.class.php', 'uploadpackage',  $packagedata['createtime'], $_SESSION['adminname']."上传了游戏“".$packagedata['gamename']."”，".$forceModel->getLastSql().",data:".json_encode($forcedata));

								if ($forceresult && $oldactiveresult && $activeresult && ($sourceresult || $sourceresult == 0) && ($gameresult || $gameresult == 0)) {
									$this->ajaxReturn('force','force',1);
									exit();
								} else {
									$this->ajaxReturn('fail',"强更包和信息上传失败.",0);
									exit();
								}
							} else {
								$this->ajaxReturn('fail',"强更包和信息上传失败.",0);
								exit();
							}
						} else {
							$this->ajaxReturn('fail',"强更时间不允许为空.",0);
							exit();
						}
					} else { 
						// 上传普通包
						if (!empty($exsitpackage["gameversion"]) && $exsitpackage["packageversion"] != $data['packageversion']) {
							$this->ajaxReturn('fail',"游戏包名不相同.",0);
							exit();
						}
						$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
						if (!stripos($newsign, 'yxgames')) {
							$this->ajaxReturn('fail',"游戏签名有错误.",0);
							exit();
						}
						$packagedata['gameid'] = $gameid;
						$packagedata['gamename'] = $game['gamename'];
						$packagedata['gameversion'] = $data['gameversion'];
						$packagedata['gamesize'] = $data['gamesize'];
						$packagedata['packagename'] = $data['packagename'];
						$packagedata['packageversion'] = $data['packageversion'];
						$packagedata['viewname'] = $game["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
						$packagedata['activeflag'] = 1;
						$packagedata['createtime'] = date('Y-m-d H:i:s',time());
						$packagedata['createuser'] = "Admin";
						$packagedata['isnowactive'] = 1;
						$packagedata['isforcepackage'] = 0;
						$packagedata['isforced'] = 0;
						$packageresult = $packageModel->add($packagedata);
						if ($packageresult) {
							$packageid = $packageresult;
							$package = $packageModel->find($packageid);
							// 该游戏的 所有的游戏包，设置为不是当前包
							$oldactivedata["isnowactive"] = 0;
							$oldactivecondition["gameid"] = $package["gameid"];
							$oldactiveresult = $packageModel->where($oldactivecondition)->save($oldactivedata);

							// 将刚才新增的游戏包，设置为当前包
							$activedata["isnowactive"] = 1;
							$activecondition["packageid"] = $packageid;
							$activeresult = $packageModel->where($activecondition)->save($activedata);

							// 更新该游戏的所有资源，把资源的游戏包下载链接放在tg_oldpackage表里面，把资源表里面的游戏包下载链接清空
							$sourceModel = M('tg_source');
							$sourcecondition["gameid"] = $package["gameid"];
							$sourcecondition["activeflag"] = 1;
							$sourcelist = $sourceModel->where($sourcecondition)->order("id desc")->select();

							$sourcedata["isupload"] = 0;
							$sourcedata["is_cdn_submit"] = -1;
							$sourcedata["apkurl"] = "";
							$sourceresult = $sourceModel->where($sourcecondition)->save($sourcedata);

							$oldModel = M('tg_oldpackage');
							$olddata = array();
							foreach ($sourcelist as $k => $v) {
								$olddata[$k]["userid"] = $v["userid"];
								$olddata[$k]["channelid"] = $v["channelid"];
								$olddata[$k]["gameid"] = $v["gameid"];
								$olddata[$k]["apkurl"] = $v["apkurl"];
								$olddata[$k]["isdelete"] = 0;
								$olddata[$k]['activeflag'] = 1;
								$olddata[$k]['createtime'] = date('Y-m-d H:i:s',time());
								$olddata[$k]['createuser'] = "Admin";
							}
							if (sizeof($olddata) > 0) {
								$oldresult = $oldModel->addAll($olddata);
							}
							// 更新tg_game游戏信息表
							$gamecondition["gameid"] = $package["gameid"];
							$gameresult = $gameModel->where($gamecondition)->save($data);
							$gameresult = $gameModel->where($gamecondition)->save($infodata);
							if ($oldactiveresult && $activeresult && ($sourceresult || $sourceresult == 0) && ($gameresult || $gameresult == 0)) {
								$this->ajaxReturn('success','success',1);
								exit();
							} else {
								$this->ajaxReturn('fail',"激活游戏包失败。",0);
								exit();
							}
						} else {
							$this->ajaxReturn('fail',"上传游戏包失败。",0);
							exit();
						}
					}
				} else if (strnatcmp($data['gameversion'],$exsitpackage["gameversion"])==0) {
					if ($_POST["isforcepackage"] == 1) {
						$this->ajaxReturn('fail',"强更不允许上传相同版本的包，请检查。",0);
						exit();
					} else {
						if ($_POST['isconfirmed'] == 0) {
							// 先上传包信息，如果想覆盖coverPackage方法覆盖
							if ($exsitpackage["packageversion"] != $data['packageversion']) {
								$this->ajaxReturn('fail',"游戏包名不相同.",0);
								exit();
							}
							$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
							if (!stripos($newsign, 'yxgames')) {
								$this->ajaxReturn('fail',"游戏签名有错误.",0);
								exit();
							}
							$packagedata['gameid'] = $gameid;
							$packagedata['gamename'] = $game['gamename'];
							$packagedata['gameversion'] = $data['gameversion'];
							$packagedata['gamesize'] = $data['gamesize'];
							$packagedata['packagename'] = $data['packagename'];
							$packagedata['packageversion'] = $data['packageversion'];
							$packagedata['viewname'] = $game["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
							$packagedata['activeflag'] = 0;
							$packagedata['createtime'] = date('Y-m-d H:i:s',time());
							$packagedata['createuser'] = "Admin";
							$packagedata['isnowactive'] = 0;
							$packagedata['isforcepackage'] = 0;
							$packagedata['isforced'] = 0;
							$package = $packageModel->add($packagedata);
							if ($package) {
								$this->ajaxReturn('warning',$package,0);
								exit();
							} else {
								$this->ajaxReturn('fail',"上传游戏包失败。",0);
								exit();
							}
						} else {
							$this->ajaxReturn('fail',"已经上传一个相同版本的包并覆盖原包，不允许再次上传相同的包。",0);
							exit();
						}
					}
				} else {
					$this->ajaxReturn('fail',"上传游戏包版本过低。",0);
					exit();
				}
			} else {
				// 该游戏不存在当前包，如果以前程序正确，一般不存在这种情况
				if ($_POST["isforcepackage"] == 1) {
					// 上传强更包
					$historyversion = $_POST["historyversion"];
					$forcetime = $_POST["forcetime"];
					$latestversion = $data['gameversion'];
					if ($forcetime != "") {
						$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
						if (!stripos($newsign, 'yxgames')) {
							$this->ajaxReturn('fail',"游戏签名有错误.",0);
							exit();
						}
						$packagedata['gameid'] = $gameid;
						$packagedata['gamename'] = $game['gamename'];
						$packagedata['gameversion'] = $data['gameversion'];
						$packagedata['gamesize'] = $data['gamesize'];
						$packagedata['packagename'] = $data['packagename'];
						$packagedata['packageversion'] = $data['packageversion'];
						$packagedata['viewname'] = $game["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
						$packagedata['activeflag'] = 1;
						$packagedata['createtime'] = date('Y-m-d H:i:s',time());
						$packagedata['createuser'] = "Admin";
						$packagedata['isnowactive'] = 1;
						$packagedata['isforcepackage'] = 1;
						$packagedata['isforced'] = 0;
						$packagedata['forcetime'] = $forcetime.":00";
						$packageresult = $packageModel->add($packagedata);

						if ($packageresult) {
							$packageid = $packageresult;
							$package = $packageModel->find($packageid);

							// 该游戏的 所有的游戏包，设置为不是当前包
							$oldactivedata["isnowactive"] = 0;
							$oldactivecondition["gameid"] = $package["gameid"];
							$oldactiveresult = $packageModel->where($oldactivecondition)->save($oldactivedata);

							// 将刚才新增的游戏包，设置为当前包
							$activedata["isnowactive"] = 1;
							$activecondition["packageid"] = $packageid;
							$activeresult = $packageModel->where($activecondition)->save($activedata);

							// 上传强更包；把更新资源的游戏包下载链接放在了批量处理，而不再这里，因为有个强更时间点
							$sourceModel = M('tg_source');
							$sourcecondition["gameid"] = $package["gameid"];
							$sourcecondition["activeflag"] = 1;
							$sourcelist = $sourceModel->where($sourcecondition)->order("id desc")->select();
							$oldModel = M('tg_oldpackage');
							$olddata = array();
							foreach ($sourcelist as $k => $v) {
								$olddata[$k]["userid"] = $v["userid"];
								$olddata[$k]["channelid"] = $v["channelid"];
								$olddata[$k]["gameid"] = $v["gameid"];
								$olddata[$k]["apkurl"] = $v["apkurl"];
								$olddata[$k]["isdelete"] = 0;
								$olddata[$k]['activeflag'] = 1;
								$olddata[$k]['createtime'] = date('Y-m-d H:i:s',time());
								$olddata[$k]['createuser'] = "Admin";
							}
							if (sizeof($olddata) > 0) {
								$oldresult = $oldModel->addAll($olddata);
							}
							$gamecondition["gameid"] = $package["gameid"];
							//强更信息登陆时不更新游戏信息,只更新icon和素材
							$gameresult = $gameModel->where($gamecondition)->save($infodata);

							// 更新all_game的游戏信息
							$forceModel = M('all_game');
							$forcedata["upversions"] = $historyversion;
							$forcedata["uptime"] = strtotime($forcetime.":00");
							$forcedata["lastupver"] = $latestversion;
							$forcecondition["id"] = $game["sdkgameid"];
							$forceresult = $forceModel->where($forcecondition)->save($forcedata);
							$this->insertLog($_SESSION['adminname'],'上传游戏包', 'GameAction.class.php', 'uploadpackage',  $packagedata['createtime'], $_SESSION['adminname']."上传了游戏“".$packagedata['gamename']."”，".$forceModel->getLastSql().",data:".json_encode($forcedata));

							if ($forceresult && $oldactiveresult && $activeresult && ($sourceresult || $sourceresult == 0) && ($gameresult || $gameresult == 0)) {
								$this->ajaxReturn('force','force',1);
								exit();
							} else {
								$this->ajaxReturn('fail',"强更包和信息上传失败.",0);
								exit();
							}
						} else {
							$this->ajaxReturn('fail',"强更包和信息上传失败.",0);
							exit();
						}
					} else {
						$this->ajaxReturn('fail',"强更时间不允许为空.",0);
						exit();
					}
				} else {
					// 该游戏，上传普通包，设置tg_package的最新包，更新资源的包下载链接（下面的操作）
					$newsign = shell_exec("/usr/java/jdk1.7.0_79/bin/jarsigner -verify -verbose -certs ".$this->signCheckFolder.$packagename." | grep YXGames");
					if (!stripos($newsign, 'yxgames')) {
						$this->ajaxReturn('fail',"游戏签名有错误.",0);
						exit();
					}
					$packagedata['gameid'] = $gameid;
					$packagedata['gamename'] = $game['gamename'];
					$packagedata['gameversion'] = $data['gameversion'];
					$packagedata['gamesize'] = $data['gamesize'];
					$packagedata['packagename'] = $data['packagename'];
					$packagedata['packageversion'] = $data['packageversion'];
					$packagedata['viewname'] = $game["gamepinyin"]."_".$data["gameversion"]."_".date("md")."_".createstr(4).".apk";
					$packagedata['activeflag'] = 1;
					$packagedata['createtime'] = date('Y-m-d H:i:s',time());
					$packagedata['createuser'] = "Admin";
					$packagedata['isnowactive'] = 1;
					$packagedata['isforcepackage'] = 0;
					$packagedata['isforced'] = 0;
					// 添加一个包信息，而不是编辑
					$packageresult = $packageModel->add($packagedata);
					if ($packageresult) {
						$packageid = $packageresult;
						$package = $packageModel->find($packageid);

						// 该游戏的 将以前所有的游戏包，设置为不是当前包
						$oldactivedata["isnowactive"] = 0;
						$oldactivecondition["gameid"] = $package["gameid"];
						$oldactiveresult = $packageModel->where($oldactivecondition)->save($oldactivedata);

						// 将刚才新增的游戏包，设置为当前包
						$activedata["isnowactive"] = 1;
						$activecondition["packageid"] = $packageid;
						$activeresult = $packageModel->where($activecondition)->save($activedata);

						// 下面两步，是更新资源游戏包的下载链接，将旧的下载链接放在tg_oldpackage表里，资源表里面的下载链接去掉，目的：前台下载时候可以重新下载，下载最新的
						// 该游戏的 所有的资源的包下载地址改为空，是否上传改成否
						$sourceModel = M('tg_source');
						$sourcecondition["gameid"] = $package["gameid"];
						$sourcecondition["activeflag"] = 1;
						$sourcelist = $sourceModel->where($sourcecondition)->order("id desc")->select();
						$sourcedata["isupload"] = 0;
						$sourcedata["is_cdn_submit"] = -1;
						$sourcedata["apkurl"] = "";
						$sourceresult = $sourceModel->where($sourcecondition)->save($sourcedata);

						// 将该游戏 所有的资源的游戏包的下载链接，放在tg_oldpackage表中
						$oldModel = M('tg_oldpackage');
						$olddata = array();
						foreach ($sourcelist as $k => $v) {
							$olddata[$k]["userid"] = $v["userid"];
							$olddata[$k]["channelid"] = $v["channelid"];
							$olddata[$k]["gameid"] = $v["gameid"];
							$olddata[$k]["apkurl"] = $v["apkurl"];
							$olddata[$k]["isdelete"] = 0;
							$olddata[$k]['activeflag'] = 1;
							$olddata[$k]['createtime'] = date('Y-m-d H:i:s',time());
							$olddata[$k]['createuser'] = "Admin";
						}
						if (sizeof($olddata) > 0) {
							$oldresult = $oldModel->addAll($olddata);
						}

						// 更新游戏信息表
						$gamecondition["gameid"] = $package["gameid"];
						$gameresult = $gameModel->where($gamecondition)->save($data);
						$gameresult = $gameModel->where($gamecondition)->save($infodata);
                        if($packagedata['isforcepackage'] = 1){
                            $packagedata['forcename'] = '强更';
                        } elseif($packagedata['isforcepackage'] = 0){
                            $packagedata['forcename'] = '不是强更';
                        }
						if ($oldactiveresult && $activeresult && ($sourceresult || $sourceresult == 0) && ($gameresult || $gameresult == 0)) {
                            $this->insertLog($_SESSION['adminname'],'上传游戏包', 'GameAction.class.php', 'uploadpackage',  $packagedata['createtime'], $_SESSION['adminname']."上传了游戏“".$packagedata['gamename']."”，包名为“".$packagedata['packagename']."”，版本号为“".$packagedata['gameversion']."”强更时间为“".$packagedata['forcetime']."，”".$packagedata['forcename']);
                            $this->ajaxReturn('success','success',1);
							exit();
						} else {
							$this->ajaxReturn('fail',"激活游戏包失败。",0);
							exit();
						}
					} else {
						$this->ajaxReturn('fail',"上传游戏包失败。",0);
						exit();
					}
				}
			}
		} else {
			// 如果不更新游戏包，只是游戏其他资料的修改
			$gamecondition["gameid"] = $game["gameid"];
			$gameresult = $gameModel->where($gamecondition)->save($infodata);
			if ($gameresult) {
				$this->ajaxReturn('success',"上传素材和游戏图标成功。",1);
				exit();
			} else {
				$this->ajaxReturn('fail',"上传素材和游戏图标失败。",0);
				exit();
			}
		}
	}

	// 当更新包信息的时候，出现相同包的时候，选择覆盖的时候，覆盖游戏包
	public function coverPackage(){
		$this->logincheck();
		$packageid = $_POST['packageid'];
		$packageModel = M('tg_package');
		$package = $packageModel->find($packageid);

		// 该游戏的 所有的游戏包，设置为不是当前包
		$packagedata["isnowactive"] = 0;
		$packagecondition["gameid"] = $package["gameid"];
		$packageresult = $packageModel->where($packagecondition)->save($packagedata);

		// 将该游戏 和该包重复版本号，都设置为失效
		$versiondata["activeflag"] = 0;
		$versioncondition["gameversion"] = $package["gameversion"];
		$versioncondition["gameid"] = $package["gameid"];
		$versionresult = $packageModel->where($versioncondition)->save($versiondata);

		// 将刚才新增的游戏包，设置为当前包
		$condition["packageid"] = $packageid;
		$data["isnowactive"] = 1;
		$data["activeflag"] = 1;
		$cover = $packageModel->where($condition)->save($data);
		if ($cover) {
			$sourceModel = M('tg_source');
			$sourcecondition["gameid"] = $package["gameid"];
			$sourcecondition["activeflag"] = 1;
			$sourcelist = $sourceModel->where($sourcecondition)->order("id desc")->select();
			$sourcedata["isupload"] = 0;
			$sourcedata["is_cdn_submit"] = -1;
			$sourcedata["apkurl"] = "";
			$sourceresult = $sourceModel->where($sourcecondition)->save($sourcedata);
			// 将该游戏 所有的资源的游戏包的下载链接，放在tg_oldpackage表中
			$oldModel = M('tg_oldpackage');
			$olddata = array();
			foreach ($sourcelist as $k => $v) {
				$olddata[$k]["userid"] = $v["userid"];
				$olddata[$k]["channelid"] = $v["channelid"];
				$olddata[$k]["gameid"] = $v["gameid"];
				$olddata[$k]["apkurl"] = $v["apkurl"];
				$olddata[$k]["isdelete"] = 0;
				$olddata[$k]['activeflag'] = 1;
				$olddata[$k]['createtime'] = date('Y-m-d H:i:s',time());
				$olddata[$k]['createuser'] = "Admin";
			}
			if (sizeof($olddata) > 0) {
				$oldresult = $oldModel->addAll($olddata);
			}
			$gameModel = M('tg_game');
			$gamedata["gamesize"] = $package["gamesize"];
			$gamedata["gameversion"] = $package["gameversion"];
			$gamedata["packagename"] = $package["packagename"];
			$gamedata["packageversion"] = $package["packageversion"];
			$gamecondition["gameid"] = $package["gameid"];
			$gameresult = $gameModel->where($gamecondition)->save($gamedata);
			if (($sourceresult || $sourceresult == 0) && ($gameresult || $gameresult == 0)) {
				$this->ajaxReturn('success','success',1);
				exit();
			} else {
				$this->ajaxReturn('fail',"激活游戏包失败。",0);
				exit();
			}
		} else {
			$this->ajaxReturn('fail',"上传游戏包失败。",0);
			exit();
		}
	}

	
	// ------录入强更包，点击继续生成强更渠道包时候----------------------------------------------
	// 录入强更包之后，点击继续生成强更渠道包时候，检查
	public function forceUpdateCheck(){
		$this->logincheck();
		$packageid = $_POST['packageid'];
		$packageModel = M('tg_package');
		$package = $packageModel->find($packageid);
		if (($package["activeflag"] == 1) && ($package["isforcepackage"] == 1) && ($package["isnowactive"] == 1) && ($package["isforced"] == 0)) {
			$this->ajaxReturn('success',$package["gameid"],1);
		} else {
			$this->ajaxReturn('fail','生成渠道包失败，所选择游戏包不符合要求.',0);
			exit();
		}
	}

	// 录入强更包之后，检查完之后，生成强更渠道包
	public function createForcePackage(){
		// 循环在客户端，服务器端处理一条
		$this->logincheck();
		$gameid = $_POST['gameid'];
		$now = $_POST['now'];
		$sourceModel = M('tg_source');
		$sourcecondition["S.id"] = array('gt',$now);
		$sourcecondition["S.apkurl"] = array('gt',''); //对于没有生成下载包的，强更时候不生成渠道包
		$sourcecondition["S.gameid"] = $gameid;
		$sourcecondition["S.activeflag"] = 1;
		$sourcecondition["G.activeflag"] = 1;
		$sourcecondition["G.isonstack"] = 0;
		// 获取当前资源
		$source = $sourceModel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->find();
		if ($source) {
			$forcepackageModel = M('tg_forcepackage');
			$forcecondition["userid"] = $source["userid"];
			$forcecondition["channelid"] = $source["channelid"];
			$forcecondition["gameid"] = $source["gameid"];
			$forcecondition["isforce"] = 0; // 增加这个条件，解决以前第二次强更不会强更
			$isexsit = $forcepackageModel->where($forcecondition)->find();
			if ($isexsit) {
				// 如果已经在tg_forcepackage表中存放了资源的游戏下载链接
				$this->ajaxReturn('success',$source["id"],1);
				exit();
			} else {
				$packageModel = M('tg_package');
				$packagecondition["gameid"] = $gameid;
				$packagecondition["activeflag"] = 1;
				$packagecondition["isnowactive"] = 1;
				$packagecondition["isforcepackage"] = 1;
				$exsitpackage = $packageModel->where($packagecondition)->order("packageid desc")->find();
				// 是否存在强更包
				if ($exsitpackage) {
					// 生成强更包文件
					if ($source["gameversion"] != "") {
						$newgamename = $source["gamepinyin"]."_".$exsitpackage["gameversion"]."_".$source["channelid"]."_".date("md")."_".createstr(4).".apk";
					} else {
						$newgamename = $source["gamepinyin"]."_".$source["channelid"].".apk";
					}

					$sourceModel = D('Source');
					$result = $sourceModel->subpackage($exsitpackage["packagename"],$newgamename,$source["sourcesn"]);
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
							$soureceModel = D('Source');
							$soureceModel->cdnsubmit($source["sourcesn"],$newgamename,true);

							// sdk_agentlist增加资源的强更链接
							$agentModel = M('sdk_agentlist');
							$agentcondition["agent"] = $source["sourcesn"];
							$agentdata["upurl"] = $this->apkdownloadurl.$newgamename;
							$agent = $agentModel->where($agentcondition)->save($agentdata);

							if ($agent) {
								$this->ajaxReturn('success',$source["id"],1);
								exit();
							} else {
								$this->ajaxReturn('fail','分包失败，未能更新强更链接.',0);
								exit();
							}			
						} else {
							$this->ajaxReturn('fail','分包失败，未能新增强更包信息.',0);
							exit();
						}
					}else{
						$this->ajaxReturn('fail','生成分包失败。',0);
					}
				} else {
					$this->ajaxReturn('fail','分包失败，未找到合适的强更包.',0);
					exit();
				}
			}
		} else {
			$this->ajaxReturn('finish',"分包已完成。",1);
			exit();
		}
	}

	// 前台下载的时候cdn回调函数
	public function cdncallback(){
		$sourcesn = $_GET['sourcesn'];
		$newgamename = $_GET['newgamename'];

		$sourceModel = M('tg_source');
		$where = array('sourcesn'=>$sourcesn);
		$where['apkurl'] = $newgamename;
		$data = array('is_cdn_submit'=>1);
		$result = $sourceModel->where($where)->save($data);

		$log_file = $_SERVER['DOCUMENT_ROOT'].'/../tg/log/cdn/'.date('Y-m-d').'-call.log';
		if( empty($result) ){
			// 输出日志
			$log_content=date('Y-m-d H:i:s')."\n";
			$log_content.="【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】下载包cdn回调，修改source表的is_cdn_submit失败。原因是该包已经被新的替换。\n";
			error_log($log_content, 3, $log_file);

			echo "【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】下载包cdn回调，修改source表的is_cdn_submit失败。原因是该包已经被新的替换。";
		}else{
			// 输出日志
			$log_content=date('Y-m-d H:i:s')."\n";
			$log_content.="【sourcesn='".$sourcesn."'】下载包cdn回调，修改source表的is_cdn_submit成功。\n";
			error_log($log_content, 3, $log_file);

			echo "【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】下载包cdn回调，修改source表的is_cdn_submit成功。";;
		}
	}
	// 强更的时候cdn回调函数
	public function forcecdncallback(){
		$sourcesn = $_GET['sourcesn'];
		$newgamename = $_GET['newgamename'];

		$sourceModel = M('tg_source');
		$where = array('sourcesn'=>$sourcesn);
		$source = $sourceModel->field('userid,gameid,channelid,apkurl')->where($where)->find();
	
		// cdn回调，可能是强更时间点到了之后，才回调成功。
		$forcepackageModel = M('tg_forcepackage');
		$where = array();
		$where['userid'] = $source['userid'];
		$where['gameid'] = $source['gameid'];
		$where['channelid'] = $source['channelid'];
		$where['apkurl'] = $newgamename;
		$data = array('is_cdn_submit'=>1);
		$result = $forcepackageModel->where($where)->save($data);

		// 更新tgsource表
		$data = array('is_cdn_submit'=>1);
		$result1 = M('tg_source')->where($where)->save($data);

		$log_file = $_SERVER['DOCUMENT_ROOT'].'/../tg/log/cdn/'.date('Y-m-d').'-call.log';
		if( empty($result) ){
			// 输出日志
			$log_content=date('Y-m-d H:i:s')."\n";
			$log_content.="【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】强更包cdn回调，修改forcepackage表的is_cdn_submit失败。\n";
			error_log($log_content, 3, $log_file);

			echo "【sourcesn='".$sourcesn."',newgamename='".$newgamename."']强更包cdn回调，修改forcepackage表的is_cdn_submit失败。";
		}else{
			$forcepackage = $forcepackageModel->where($where)->find();

			$agentModel = M('sdk_agentlist');
			$agentcondition = array();
			$agentcondition["agent"] = $sourcesn;
			$agent = $agentModel->where($agentcondition)->find();
			$agentUpurlArr = explode('/',$agent['upurl']);
			$agentUpurl = $agentUpurlArr[7];
			if($agentUpurl ==  $newgamename ){
				$agentdata = array();
				$agentdata["upurl"] = $this->apkdownloadcdnurl.$forcepackage['apkurl'];
				$result = $agentModel->where($agentcondition)->save($agentdata);
				if( empty($result) ){
					// 输出日志
					$log_content=date('Y-m-d H:i:s')."\n";
					$log_content.="【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】强更包cdn回调，修改forcepackage表的is_cdn_submit成功，修改agentlist的upurl失败。原因是该包已经被新的替换。\n";
					error_log($log_content, 3, $log_file);

					echo "【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】强更包cdn回调，修改forcepackage表的is_cdn_submit成功，修改agentlist的upurl失败。原因是该包已经被新的替换。";
				}else{
					// 输出日志
					$log_content=date('Y-m-d H:i:s')."\n";
					$log_content.="【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】强更包cdn回调，修改forcepackage表的is_cdn_submit成功，修改agentlist的upurl成功。\n";
					error_log($log_content, 3, $log_file);

					echo "【sourcesn='".$sourcesn."',newgamename='".$newgamename."'】强更包cdn回调，修改forcepackage表的is_cdn_submit成功，修改agentlist的upurl成功。\n";
				}
			}
		}
	}
	//---------------------------------------------------------------

	//下载选中的游戏包
	public function downloadPackage(){
		$this->logincheck();
		$packageid = $_POST['packageid'];
		$packageModel = M('tg_package');
		$package = $packageModel->find($packageid);
		if ($package) {
			$this->ajaxReturn('success',$this->apkstoreurl.$package["packagename"],1);
			exit();
		} else {
			$this->ajaxReturn('fail',"请选择一个游戏包。",0);
			exit();
		}
	}

	//获取包的版本号
	public function getPackageVersion(){
		$this->logincheck();
		$packageid = $_POST['packageid'];
		$packageModel = M('tg_package');
		$package = $packageModel->find($packageid);
		if ($package) {
			$this->ajaxReturn('success',$package["gameversion"],1);
			exit();
		} else {
			$this->ajaxReturn('fail',"获取游戏包版本信息失败。",0);
			exit();
		}
	}
}
?>