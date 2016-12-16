<?php
class UserAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }
	
	//新增用户页面
	public function newuser(){
		$this->logincheck();
		$this->menucheck();
        $this->authoritycheck(10107);

        $usermodel= M('sys_admin');
        $map['department_id'] = '31';
        $userlist = $usermodel->where($map)->order("create_time desc")->select();
        $this->assign("userlist",$userlist);
        
        $this->display();
    }
	
	//所有用户
    public function userall(){
		$this->logincheck();
		$this->menucheck();

		// 设置权限
        $this->authoritycheck(10105);
        $this->assign('auditUser',$this->authoritycheck(10108));
        $this->assign('seeSoureceRight',$this->authoritycheck(10109));
        $this->assign('ptbAuthorization',$this->authoritycheck(10110));
        $this->assign('editUser',$this->authoritycheck(10111));
        // $this->assign('fastApply',$this->authoritycheck(10172));
        $this->assign('fastApply',$this->authoritycheck(10175));

        $model= M('tg_user');
        $condition["U.activeflag"] = 1;
        $condition['U.isverified'] = array('neq',2);
        $users = $model->alias('U')
                    ->join(C('DB_PREFIX').'tg_user U1 on U.pid = U1.userid')
                    ->where($condition)
                    ->field('U.*,U1.account as paccount')
                    ->order("createtime desc")
                    ->select();
        foreach ($users as $key => $value) {
        	if($value['sourcetype'] == 1){
        		$users[$key]['sourcetypestr'] = '公会';
        	}elseif($value['sourcetype'] == 2){
        		$users[$key]['sourcetypestr'] = '买量';
        	}elseif($value['sourcetype'] == 3){
        		$users[$key]['sourcetypestr'] = '平台YXGAMES';
        	}elseif($value['sourcetype'] == 4){
        		$users[$key]['sourcetypestr'] = 'CPS';
        	}elseif($value['sourcetype'] == 5){
        		$users[$key]['sourcetypestr'] = '应用商店';
        	}elseif($value['sourcetype'] == 0){
        		$users[$key]['sourcetypestr'] = '其它';
        	}
        }
        $this->assign('users',$users);

        $this->display();
    }

	//用户所有资源
    public function usersource(){
		$this->logincheck();

		// 设置权限
		$this->authoritycheck(10109);
        $this->assign('customRateRight',$this->authoritycheck(10136));//自定义资源费率
        $this->assign('downloadApkRight',$this->authoritycheck(10137));//立即下载游戏分包
        $this->assign('seeDevelopRight',$this->authoritycheck(10138));//查看推广

        $userid = $_GET["userid"];
        if ($userid == 0) {
            Header("Location: /userall/ ");
            exit();
        }
        $userModel = M('tg_user');
        $user = $userModel->find($userid);

        $channelmodel = M('tg_channel');
        $channelcondition["userid"] = $userid;
        $channel = $channelmodel->where($channelcondition)->order("createtime desc")->select();
        $channelstr = "<option value='0'>全部渠道</option>";
        foreach ($channel as $k => $v) {
            $channelstr .= "<option value='" . $v["channelid"] . "'>" . $v["channelname"] . "</option>";
        }
        $sourcemodel = M('tg_source');
        $sourcecondition["S.userid"] = $userid;
        $sourcecondition["S.activeflag"] = 1;
        $sourcecondition["G.activeflag"] = 1;
        $sourcecondition["G.isonstack"] = 0;
        $source = $sourcemodel->alias("S")->join(C('DB_PREFIX') . "tg_game G on S.gameid = G.gameid", "LEFT")->join(C('DB_PREFIX') . "tg_channel C on S.channelid = C.channelid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();
        $this->assign('user', $user);
        $this->assign('channelstr', $channelstr);
        $this->assign('source', $source);
        $this->menucheck();
        $this->display();

    }

	//用户指定渠道的资源
	public function getUserSource(){
		$this->logincheck();
        $userid = $_POST["userid"];
        $channelid = $_POST["channelid"];
        $sourcemodel= M('tg_source');
        $sourcecondition["S.userid"] = $userid;
        if ($channelid > 0) {
            $sourcecondition["S.channelid"] = $channelid;
        }
        $sourcecondition["S.activeflag"] = 1;
        $sourcecondition["G.activeflag"] = 1;
        $sourcecondition["G.isonstack"] = 0;
        $source = $sourcemodel->field("S.id as sourceid,G.gameicon as img,G.gamename,C.channelname,S.sourcesn,S.sourcesharerate,S.sourcechannelrate")->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();
        foreach ($source as $k => $v) {
            $source[$k]["img"] = $this->iconurl.$v["img"];
        }
        if($source){
            $this->ajaxReturn($source,'success',1);
            exit();
        }else{
            $this->ajaxReturn('没有数据。','fail',0);
            exit();
        }
	}

    //新增用户
    public function adduser(){
		$this->logincheck();
        $this->authoritycheck(10107);
        $data['account'] = $_POST['account'];
        $data['bindmobile'] = $_POST['bindmobile'];
        $data['bindemail'] = $_POST['bindemail'];
        $data['password'] = sha1($_POST['password']);
        $data['sourcetype'] = $_POST['sourcetype'];
        $data['usertype'] = $_POST['usertype'];
        
        if($data['usertype']==1){
        	$data['invoicetype'] = $_POST['userinvoicetype'];
        }elseif($data['usertype']==2){
        	$data['invoicetype'] = $_POST['companyinvoicetype'];
        }
        
        $data['realname'] = $_POST['realname'];
        $data['companyname'] = $_POST['companyname'];
        $data['gender'] = $_POST['gender'];
        $data['contactmobile'] = $_POST['contactmobile'];
        $data['contactemail'] = $_POST['contactemail'];
		$data['withdrawlimit'] = 100;
        $data['address'] = $_POST['address'];
        $data['postnumber'] = $_POST['postnumber'];
        $data['channelbusiness'] = $_POST['channelbusiness'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $data['createuser'] = "Admin";
        $data['projectname'] = $_POST['projectname'];

        $max_file_size = '1000000000'; //文件小于1GB
        $max_image_size = '5000000'; //图片小于5MB
        $img_extension_list = array("jpg", "jpeg", "gif", "png");
        if (!empty($_FILES)) {
            if (is_uploaded_file($_FILES["diy_logo"]["tmp_name"])) {
                $tempFile = $_FILES["diy_logo"]["tmp_name"];
                $targetPath = $this->diylogoStoreFolder;
                $filesize = $_FILES["diy_logo"]["size"];
                $packagesize = floor($filesize/1000000);
                if ($filesize > $max_file_size) {
                    $this->ajaxReturn('fail',"上传自定义logo不能大于1GB。",0);
                    exit();
                }
                $ftypearr = explode('.',$_FILES["diy_logo"]["name"]);
                $cacheindex = sizeof($ftypearr) - 1;
                if ($cacheindex >= 0) {
                    $ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
                }
                if (!in_array($ftype,$img_extension_list)) {
                    $this->ajaxReturn('fail',"上传自定义logo格式不正确。",0);
                    exit();
                }
                $cacheFileName = createstr(30).".".$ftype;
                $packageFile = $targetPath.$cacheFileName;
                if (!move_uploaded_file($tempFile,$packageFile)) {  
                    $this->ajaxReturn('fail',"上传自定义logo包失败。",0);
                    exit();
                }
                $diy_logo = $cacheFileName;
            }
        }


        // var_dump($_FILES);
        // exit();
        $data['diy_logo'] = $diy_logo;
        $data['diy_webname'] = $_POST['diy_webname'];
        $data['diy_isshow_homeheader'] = $_POST['diy_isshow_homeheader'];


        $model = M('tg_user');

        $users = $model->add($data);
        if($users){
            $this->insertLog($_SESSION['adminname'],'新增用户', 'UserAction.class.php', 'adduser', $data['createtime'], $_SESSION['adminname']."新增了用户：“".$data['account']."”");
            Header("Location: /userall/ ");
            exit();
        }
    }

	//删除用户
    public function deleteUser() {
		$this->logincheck();
        $this->authoritycheck(10111);
        if($this->authoritycheck(10111) == 'ok'){
            $userid = $_POST['userid'];
            $model = M('tg_user');
            $data["activeflag"] = 0;
            $condition["userid"] = $userid;
            $deleteuser = $model->where($condition)->save($data);
            if($deleteuser){
                $this->ajaxReturn('success','用户删除成功。',1);
                exit();
            }else{
                $this->ajaxReturn('fail','用户删除失败。',0);
                exit();
            }
        } else{
            $this->ajaxReturn('error505','无权限。',0);
            exit();
        }

    }

    //编辑页
    public function userdetail(){
		$this->logincheck();
        $userid = $_GET['userid'];
        $usermodel = M('tg_user');
        $user = $usermodel->where("userid = '$userid'")->find();
        if($this->authoritycheck(10111) == 'ok' || $this->authoritycheck(10108) == 'ok'){
            if ($userid == 0) {
                Header("Location: /userall/ ");
                exit();
            } else {
                $model= M('tg_user');
                $user = $model->find($userid);
                $adminmodel= M('sys_admin');
                $map['department_id'] = '31';
                $userlist = $adminmodel->where($map)->order("create_time desc")->select();
                $this->assign("userlist",$userlist);
                $this->assign('user',$user);
                $this->menucheck();
                $this->display();
            }

        }
    }

    //编辑用户方法
    public function edituser(){
		$this->logincheck();
		$userid = $_POST['userid'];
        $data['account'] = $_POST['account'];
        if($_POST['projectname']){
            $data['projectname'] = $_POST['projectname'];
        }
        $data['bindmobile'] = $_POST['bindmobile'];
        $data['bindemail'] = $_POST['bindemail'];
        $data['sourcetype'] = $_POST['sourcetype'];
		if ($_POST['password'] != "") {
			$data['password'] = sha1($_POST['password']);
		}
		if ($_POST['usertype'] != "") {
			$data['usertype'] = $_POST['usertype'];
		}
		if($data['usertype']==1){
        	$data['invoicetype'] = $_POST['userinvoicetype'];
        }elseif($data['usertype']==2){
        	$data['invoicetype'] = $_POST['companyinvoicetype'];
        }
		$data['realname'] = $_POST['realname'];
		$data['companyname'] = $_POST['companyname'];
		if ($_POST['gender'] != "") {
			$data['gender'] = $_POST['gender'];
		}
        $data['contactmobile'] = $_POST['contactmobile'];
        $data['contactemail'] = $_POST['contactemail'];
		$data['withdrawlimit'] = $_POST['withdrawlimit'];
		$data['address'] = $_POST['address'];
		$data['postnumber'] = $_POST['postnumber'];
        $data['channelbusiness'] = $_POST['channelbusiness'];
		if ($_POST['isverified'] != "") {
			$data['isverified'] = $_POST['isverified'];
			if ($_POST['isverified'] == 1) {
				if ($_POST['bindmobile'] != "") {
					$usermobile = $_POST['bindmobile'];
				} else {
					if ($_POST['contactmobile'] != "") {
						$usermobile = $_POST['contactmobile'];
					}
				}
				if ($usermobile != "") {
					session_start();//开启缓存
					header("content-type:text/html; charset=utf-8;");//开启缓存
					$_SESSION['smstime'] = date("Y-m-d H:i:s");
					$username = '70208457';     //用户账号
					$password = '15927611975';      //密码
					$content = "尊敬的用户您好，您在我们游侠游戏推广平台提交的注册资料已经审核通过，平台地址为 http://tg.yxgames.com/ ，有任何问题请联系客服，客服QQ为3080651944.";        //内容
                    $http = 'http://api.duanxin.cm/';
					$smsdata = array
					(
						'action'=>'send',
						'username'=>$username,                  //用户账号
						'password'=>strtolower(md5($password)), //MD5位32密码
						'phone'=>$usermobile,                //号码
						'content'=>$content,            //内容
						'time'=>$_SESSION['smstime'],      //定时发送
						'encode'=>'utf8'
					);
					//POST方式提交 
					$row = parse_url($http);
					$host = $row['host'];
					$port = $row['port'] ? $row['port']:80;
					$file = $row['path'];
					while (list($k,$v) = each($smsdata)){
						$post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
					}
					$post = substr( $post , 0 , -1 );
					$len = strlen($post);
					$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
					if (!$fp) {
						return "$errstr ($errno)\n";
					} else {
						$receive = '';
						$out = "POST $file HTTP/1.0\r\n";
						$out .= "Host: $host\r\n";
						$out .= "Content-type: application/x-www-form-urlencoded\r\n";
						$out .= "Connection: Close\r\n";
						$out .= "Content-Length: $len\r\n\r\n";
						$out .= $post;      
						fwrite($fp, $out);
						while (!feof($fp)) {
							$receive .= fgets($fp, 128);
						}
						fclose($fp);
						$receive = explode("\r\n\r\n",$receive);
						unset($receive[0]);
						$re = implode("",$receive);
					}
				}
			} else if ($_POST['isverified'] == 2) {
				$refusereason = $_POST['refusereason'];
				$refusereasonstr = "";
				for ($i=0;$i<sizeof($refusereason);$i++) {
					$refusereasonstr .= $refusereason[$i].",";
				}
				if ($refusereasonstr != "") {
					$data['refusereason'] = substr($refusereasonstr, 0, -1);
				} else {
					$data['refusereason'] = "";
				}
			}
		}

        $max_file_size = '1000000000'; //文件小于1GB
        $max_image_size = '5000000'; //图片小于5MB
        $img_extension_list = array("jpg", "jpeg", "gif", "png");
        if (!empty($_FILES)) {
            if (is_uploaded_file($_FILES["diy_logo"]["tmp_name"])) {
                $tempFile = $_FILES["diy_logo"]["tmp_name"];
                $targetPath = $this->diylogoStoreFolder;
                $filesize = $_FILES["diy_logo"]["size"];
                $packagesize = floor($filesize/1000000);
                if ($filesize > $max_file_size) {
                    $this->ajaxReturn('fail',"上传自定义logo不能大于1GB。",0);
                    exit();
                }
                $ftypearr = explode('.',$_FILES["diy_logo"]["name"]);
                $cacheindex = sizeof($ftypearr) - 1;
                if ($cacheindex >= 0) {
                    $ftype = strtolower($ftypearr[$cacheindex]); //文件类型拓展名
                }
                if (!in_array($ftype,$img_extension_list)) {
                    $this->ajaxReturn('fail',"上传自定义logo格式不正确。",0);
                    exit();
                }
                $cacheFileName = createstr(30).".".$ftype;
                $packageFile = $targetPath.$cacheFileName;
                if (!move_uploaded_file($tempFile,$packageFile)) {  
                    $this->ajaxReturn('fail',"上传自定义logo包失败。",0);
                    exit();
                }
                $diy_logo = $cacheFileName;
            }
        }
        
        if($diy_logo!= ""){
            $data['diy_logo'] = $diy_logo;
        }
        
        $data['diy_webname'] = $_POST['diy_webname'];
        $data['diy_isshow_homeheader'] = $_POST['diy_isshow_homeheader'];

        // $this->ajaxReturn('fail',$_FILES["diy_logo"]["tmp_name"],0);
        // exit();

        $model = M('tg_user');
		$condition["userid"] = $userid;
        $user = $model->where($condition)->save($data);
        $newuser = $model->where($condition)->find();
        $time = date('Y-m-d H:i:s',time());

        if($user){
            if($newuser['isverified'] == 1){
                $this->insertLog($_SESSION['adminname'],'审核用户', 'UserAction.class.php', 'edituser', $time, $_SESSION['adminname']."审核用户“".$data['account']."”通过");
            } elseif($newuser['isverified'] == 2){
                $this->insertLog($_SESSION['adminname'],'审核用户', 'UserAction.class.php', 'edituser', $time, $_SESSION['adminname']."审核用户“".$data['account']."”拒绝通过");
            }
            $this->ajaxReturn('success','用户信息更新成功。',1);
			exit();
		}else{
            $this->ajaxReturn('fail','用户信息更新失败。',0);
			exit();
		}
    }

	//查询是否已存在相同用户名
    public function checkAccountExist(){
		$this->logincheck();
		$account = $_POST["account"];
        $model = M('tg_user');
		$condition["account"] = $account;
        $existuser = $model->where($condition)->count();
		if ($existuser > 0) {
			$vo["isexist"] = "exist";
			$this->ajaxReturn($vo,'用户已存在，请输入一个新的用户名。',1);
			exit();
		} else {
			$vo["isexist"] = "notexist";
			$this->ajaxReturn($vo,'用户不存在！',0);
			exit();
		}
    }

	//查询是否已存在相同的绑定手机号
    public function checkMobileExist(){
		$this->logincheck();
		$mobile = $_POST["mobile"];
        $model = M('tg_user');
		$condition["bindmobile"] = $mobile;
        $existmobile = $model->where($condition)->count();
		if ($existmobile > 0) {
			$vo["isexist"] = "exist";
			$this->ajaxReturn($vo,'手机号已存在，请输入一个新的手机号。',1);
			exit();
		} else {
			$vo["isexist"] = "notexist";
			$this->ajaxReturn($vo,'手机号不存在！',0);
			exit();
		}
    }

	//查询是否已存在相同的绑定邮箱
    public function checkEmailExist(){
		$this->logincheck();
		$email = $_POST["email"];
        $model = M('tg_user');
		$condition["bindemail"] = $email;
        $existemail = $model->where($condition)->count();
		if ($existemail > 0) {
			$vo["isexist"] = "exist";
			$this->ajaxReturn($vo,'邮箱已存在，请输入一个新的邮箱。',1);
			exit();
		} else {
			$vo["isexist"] = "notexist";
			$this->ajaxReturn($vo,'邮箱不存在！',0);
			exit();
		}
    }

    // 一键申请资源-------------------------------
    // 对某个用户一键申请资源内部函数
    private function fastApplyInner($userid,$isPrintLog){
  		$channelModel=M('tg_channel');
  		$userModel = M('tg_user');

  		$user = $userModel->find($userid);

      	// 判断系统默认渠道是否存在
      	$defaultChannelname=C('app_fastapply_channelname');
      	$condition=array();
  		$condition["userid"] = $userid;
        $condition["channelname"] = $defaultChannelname;
        $condition["activeflag"] = 1;
        $existChannel = $channelModel->where($condition)->find();
        if (!$existChannel) {
            // 如果不存在，创建一个系统默认渠道
	  		$data=array();
	        $data['userid'] = $userid;
	        $data['channelname'] = $defaultChannelname;
	        $data['channeltype'] = '系统';
	        $data['channelsize'] = '50000 pv以上';
	        $data['description'] = '一键生成的系统渠道';
	        $data['gamecount'] = 0;
	        $data['activeflag'] = 1;
	        $data['createtime'] = date('Y-m-d H:i:s',time());
	        $data['createuser'] = $_SESSION['adminname'];
	        $newChannelid = $channelModel->add($data);
	        if($newChannelid){
	        	$this->insertLog($_SESSION['adminname'],'新建系统渠道', 'Admin/User', 'fastFenbao', $data['createtime'], $user['account']."新建了“".$data['channelname']."”渠道");
	        }else{
	        	if($isPrintLog){
					$log_content=date('Y-m-d H:i:s')."\n";
					$log_content.='error：添加用户【'.$user['account'].'】系统渠道失败。'."\n";
					error_log($log_content, 3, 'test.log');
					exit();
	        	}else{
	        		$this->ajaxReturn('fail','添加系统渠道失败。',0);
	            	exit();
	        	}
	        }
        }else{
        	$newChannelid=$existChannel['channelid'];
        }
        
       	// 创建所有游戏的 该渠道的 资源
       	$sourceModel = M('tg_source');
		$agentModel = M('sdk_agentlist');
		$gameModel=M('tg_game');
		$gwebgameModel=M('gweb_game');
		$gameurlModel=M('co_gameurl');
		$sourceDModel = D('Source');

       	$where=array(
  			'isonstack'=>0,
  			'activeflag'=>1
  		);
  		$gameRecords=$gameModel->field('gameid')->where($where)->select();
  		foreach ($gameRecords as $key => $value) {
			$gameid = $value["gameid"];
			$channelid = $newChannelid;
	        $userid = $userid;
	        $game = $gameModel->find($gameid);

			// 判断资源是否存在
			$condition=array();
			$condition['userid'] = $userid;
	        $condition['gameid'] = $gameid;
	        $condition['channelid'] = $channelid;
			$condition['activeflag'] = 1;
			$existSource = $sourceModel->where($condition)->find();

			if(!$existSource) {
				// 如果不存在，创建资源
				// 添加tg_source表
				$data=array();
				$data['activeflag'] = 1;
				$data['userid'] = $userid;
				$data['gameid'] = $gameid;
				$data['channelid'] = $channelid;
				$data['createtime'] = date('Y-m-d : H-i-s',time());
				$newgamename = $this->makeStr(30);
				$sourcesn = "tg_".$newgamename;
				$data['sourcesn'] = $sourcesn;
				if ($game["sharerate"] != "") {
					$data['sourcesharerate'] = $game["sharerate"];
				}
				if ($game["channelrate"] != "") {
					$data['sourcechannelrate'] = $game["channelrate"];
				}
				$data['textureurl'] = $game["texturename"];
				$data['isupload'] = 0;
				$data['createuser'] = $_SESSION['adminname'];
				$newSourceid = $sourceModel->add($data);
				// 添加sdk_agentlist表
				$agentdata=array();
				$agentdata["gameid"] = $game["sdkgameid"];
				$agentdata["agent"] = $sourcesn;
				$agentdata["agentname"] = $user["account"]."_".$defaultChannelname;
				$agentdata["departmentid"] = 20;
				$agentdata["owner"] = "Admin";
				$agentdata["username"] = "Admin";
				$agentdata["cpa_price"] = 0;
				$agentdata["rate"] = $game["sharerate"];
				$agentdata["create_time"] = time();
				$newAgentid = $agentModel->add($agentdata);
	           
				if ($newSourceid && $newAgentid) {
					// 增加渠道的游戏数
					$where=array();
					$where["channelid"] = $channelid;
					$channelModel->where($where)->setInc('gamecount');

					$time = date('Y-m-d H:i:s',time());
	                $this->insertLog($_SESSION['adminname'],'申请资源', 'SourceAction.class.php', 'applyGame', $time, "用户".$user['account']."在“".$defaultChannelname."”渠道下申请了“".$game['gamename']."”游戏");
				} else {
					if($isPrintLog){
						$log_content=date('Y-m-d H:i:s')."\n";
						$log_content.='error：申请用户【'.$user['account'].'】在【系统渠道】的游戏【'.$game['gamename'].'】资源失败。'."\n";
						error_log($log_content, 3, 'test.log');
						exit();
					}else{
						$this->ajaxReturn('fail',"申请资源失败，请稍后重试。",0);
						exit();
					}
				}
			} else{
				$sourcesn=$existSource['sourcesn'];
			}

			// 添加co_gameurl表
			// 看gweb_game里面有没有此游戏
			$where=array('tggameid'=>$gameid);
			$gwebgame=$gwebgameModel->field('id,name')->where($where)->find();
			if($gwebgame){
				// 判断游戏链接是否存在
				$condition=array();
				$condition['tguserid'] = $userid;
		        $condition['gwebgameid'] = $gwebgame['id'];
				$existGameurl = $gameurlModel->where($condition)->find();
				if(!$existGameurl) {
					// 如果不存在，创建资源
					$data=array();
					$data['tguserid'] = $userid;
					// $data['tggameid'] = $gameid;
					$data['gwebgameid'] = $gwebgame['id'];
					$data['gamename'] = $gwebgame['name'];
					$longurl='http://tg.yxgames.com/publicdownload/'.$sourcesn;
					$shorturl=$sourceDModel->shortenSinaUrl($longurl);
					$data['linkurl'] = $shorturl;
					$data['linktype'] = 1;
					$data['account'] = $user['account'];
					$data['realname'] = $user['realname'];
					$data['projectname'] = $user['projectname'];
					$data['addtime'] = time();
					$newGameurl = $gameurlModel->add($data);
					if ($newGameurl) {
						$time = date('Y-m-d H:i:s',time());
		                $this->insertLog($_SESSION['adminname'],'创建游戏链接', 'Admin/User', 'fastFenbao', $time, "创建了用户【".$user['account']."】【".$defaultChannelname."】渠道【“".$game['gamename']."】游戏的游戏链接");
					} else {
						if($isPrintLog){
							$log_content=date('Y-m-d H:i:s')."\n";
							$log_content.='error：创建用户【'.$user['account'].'】在【系统渠道】的游戏【'.$game['gamename'].'】游戏链接失败。'."\n";
							error_log($log_content, 3, 'test.log');
							exit();
						}else{
							$this->ajaxReturn('fail',"创建游戏链接，请稍后重试。",0);
							exit();
						}
					}
				}
			}
        }

        // 生成相应资源的游戏包
        // $config=array(
        // 	'userid'=>$userid,
        // 	'channelid'=>$newChannelid,
        // );
        // $sourceModel->createBao($config);

        if($isPrintLog){
			$log_content=date('Y-m-d H:i:s')."\n";
			$log_content.='success：为用户【'.$user['account'].'】一键申请资源成功。'."\n";
			error_log($log_content, 3, 'test.log');
		}else{
			$this->ajaxReturn('success','一键申请资源成功',1);
			exit();
		}
    }

    // 对某个用户一键申请资源
    public function fastApply(){
    	$this->logincheck();
    	// if($this->authoritycheck(10172) != 'ok'){
    	if($this->authoritycheck(10175) != 'ok'){
    		$this->ajaxReturn('error505','无权限。',0);
            exit();
    	}
    		
    	$userid = $_POST['userid'];
    	$this->fastApplyInner($userid,false);
    }

    // 有新游戏出来的时候，对所有的合作用户，一键申请资源
    public function moreFastApply(){
    	$channelModel=M('tg_channel');
    	$where=array('channelname'=>C('app_fastapply_channelname'));
    	$channelRecords=$channelModel->field('distinct userid')->where($where)->select();

    	foreach ($channelRecords as $key => $value) {
    		$userid=$value['userid'];
    		$this->fastApplyInner($userid,true);
    	}
    }
    // ------------------------------

    public function sendmail(){
		$this->logincheck();
        $this->authoritycheck(10112);
        $this->menucheck();
        $this->display();
    }

	public function sendMailAction(){
		$this->logincheck();
		$category = $_POST["category"];
		$sendtoall = $_POST["sendtoall"];
		if ($sendtoall == "") {
			$sendtoall = 0;
		}
		$title = $_POST["title"];
		$content = $_POST["content"];
		if ($sendtoall == 0) {
			$targetuser = $_POST["targetuser"];
			$condition['account'] = $targetuser;
			$condition['bindmobile'] = $targetuser;
			$condition['_logic'] = 'OR';
			$model = M('tg_user');
			$user = $model->where($condition)->find();
			if ($user) {
				$data["userid"] = $user["userid"];
				$data["category"] = $category;
				$data["title"] = $title;
				$data["content"] = $content;
				$data["isread"] = 0;
				$data['activeflag'] = 1;
				$data['createtime'] = date('Y-m-d H:i:s',time());
				$data['createuser'] = "Admin";
				$mailmodel = M('tg_message');
				$result = $mailmodel->add($data);
				if ($result) {
                    $this->insertLog($_SESSION['adminname'],'发送消息', 'UserAction.class.php', 'sendMailAction', $data['createtime'], $_SESSION['adminname']."向用户“".$targetuser."”发送类型为：“".$data["category"]."”标题为：“".$data["title"]."”的消息");
					$this->ajaxReturn('success','发送成功。',1);
					exit();
				} else {
					$this->ajaxReturn('fail','发送失败。',0);
					exit();
				}
			} else {
				$this->ajaxReturn('fail','发送失败。',0);
				exit();
			}
		} else {
			$condition['activeflag'] = 1;
			$model = M('tg_user');
			$user = $model->field('userid')->where($condition)->select();
			if ($user) {
				$data = array();
				for ($i=0;$i<sizeof($user);$i++) {
					$data[$i]["userid"] = $user[$i]["userid"];
					$data[$i]["category"] = $category;
					$data[$i]["title"] = $title;
					$data[$i]["content"] = $content;
					$data[$i]["isread"] = 0;
					$data[$i]['activeflag'] = 1;
					$data[$i]['createtime'] = date('Y-m-d H:i:s',time());
					$data[$i]['createuser'] = "Admin";
				}
				$mailmodel = M('tg_message');
				$result = $mailmodel->addAll($data);
				if ($result) {
                    $this->insertLog($_SESSION['adminname'],'发送消息', 'UserAction.class.php', 'sendMailAction', $data[$i]['createtime'], $_SESSION['adminname']."向全员发送类型为：“".$data[$i]["category"]."”标题为：“".$data[$i]["title"]."”的消息");
                    $this->ajaxReturn('success','发送成功。',1);
					exit();
				} else {
					$this->ajaxReturn('fail','发送失败。',0);
					exit();
				}
			} else {
				$this->ajaxReturn('fail','发送失败。',0);
				exit();
			}
		}
    }

	public function userpreauth(){
        $this->logincheck();
        $this->authoritycheck(10110);
        if($this->authoritycheck(10110) == 'ok'){
            // if ($_SESSION["adminid"] == 1) {
                $userid = $_GET['userid'];
                if ($userid == 0) {
                    Header("Location: /userall/ ");
                    exit();
                }
                $userModel= M('tg_user');
                $user = $userModel->find($userid);
                $logModel= M('tg_coinlog');
                $condition["userid"] = $userid;
                $condition["activeflag"] = 1;
                $coinlog = $logModel->where($condition)->order("id desc")->select();
                $this->assign('user',$user);
                $this->assign('coinlog',$coinlog);
                $this->menucheck();
                $this->display();
            // } else {
            //     Header("Location: /userall/ ");
            //     exit();
            // }
        } else{
            Header("Location: /error505/ ");
            exit();
        }
    }

	public function modifyCoinPreAuth(){
		$this->logincheck();
		$userid = $_POST['hiddenuserid'];
		$adminid = $_SESSION['adminid'];
		$adminname = $_SESSION['adminname'];
		$userModel= M('tg_user');
		$user = $userModel->find($userid);
		$preauthamount = $_POST['preauthamount'];
		if ($_POST['increase'] == 1) {
			$newcoinpreauth = $user["coinpreauth"] + $preauthamount;
		} else {
			$newcoinpreauth = $user["coinpreauth"] - $preauthamount;
		}
		if ($newcoinpreauth >= 0) {
			$condition["userid"] = $userid;
			$data["coinpreauth"] = $newcoinpreauth;
			$preauth = $userModel->where($condition)->save($data);
			if ($preauth || $preauth == 0) {
				$logModel = M('tg_coinlog');
				$logdata["userid"] = $userid;
				$logdata["preauthuser"] = $adminid;
				$logdata["preauthusername"] = $adminname;
				if ($_POST['increase'] == 1) {
					$logdata["amount"] = $preauthamount;
				} else {
					$logdata["amount"] = 0 - $preauthamount;
				}
				$logdata['activeflag'] = 1;
				$logdata['createtime'] = date('Y-m-d H:i:s',time());
				$logdata['createuser'] = "adminname";
				$log = $logModel->add($logdata);
				$this->ajaxReturn('success',$newcoinpreauth,1);
				exit();
			}
		} else {
			$this->ajaxReturn('fail','减少的预授权数量过大，使用户的预授权游侠币数量小于0。',0);
			exit();
		} 
	}

	public function userrate(){
		$this->logincheck();
		$sourceid = $_GET['sourceid'];
		if ($sourceid == 0) {
			Header("Location: /userall/ ");
			exit();
		}
		$sourceModel= M('tg_source');
		$source = $sourceModel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->join(C('DB_PREFIX')."tg_channel C on S.channelid = C.channelid", "LEFT")->find($sourceid);
		$userModel= M('tg_user');
		$user = $userModel->find($source["userid"]);
		$this->assign('source',$source);
		$this->assign('user',$user);
        $this->menucheck();
		$this->display();
			
    }

	public function modifyUserRate(){
		$this->logincheck();
		$sourceid = $_POST['hiddensourceid'];
		$condition["id"] = $sourceid;
		$sourceModel= M('tg_source');

        $oldsource = $sourceModel->where($condition)->find();
        $channelid = $oldsource['channelid'];
        $userid = $oldsource['userid'];
        $gameid = $oldsource['gameid'];

        // 如果设置的分成比例 小于子账号的分成比例
        if( $_POST['sourcesharerate'] < $oldsource['sub_share_rate']){
            $this->ajaxReturn('fail','母账号的分成比例 必须比 子账号的分成比例 大。',0);
            exit();
        }

        $channelmodel =  M('tg_channel');
        $channel = $channelmodel->field('channelname')->where("channelid = '$channelid'")->find();

        $usermodel = M('tg_user');
        $user = $usermodel->field('account')->where("userid = '$userid'")->find();

        $gameModel = M('tg_game');
        $game = $gameModel->field('gamename')->where("gameid = '$gameid'")->find();

        if ($_POST['sourcesharerate'] != "") {
			$data["sourcesharerate"] = $_POST['sourcesharerate'];
		}
		if ($_POST['sourcechannelrate'] != "") {
			$data["sourcechannelrate"] = $_POST['sourcechannelrate'];
		}
        $data['isfixrate']=1;
        $time = date('Y-m-d H:i:s',time());
		$source = $sourceModel->where($condition)->save($data);
		if ($source || $source == 0) {

			// 对于未结算的，该资源的每日统计进行重新统计
			// 自定义税率不再修改以前的每日统计
			// $balanceModel= M('tg_balance');
			// $balance=$balanceModel->field('enddate')->where('userid='.$userid)->order('enddate desc')->find();
			// $enddate=$balance['enddate'];

			// $dailyaccountModel= M('tg_dailyaccount');
			// $dailyaccount=$dailyaccountModel->where(' sourceid='.$sourceid.' and date>"'.$enddate.'"')->select();
			// foreach ($dailyaccount as $key => $value) {
			// 	$dailyincome=$value['dailyjournal'] * $data["sourcesharerate"] * (1 - $data["sourcechannelrate"]);

			// 	$dailyaccountData=array(
			// 		'sharerate' => $data["sourcesharerate"], 
			// 		'channelrate' => $data["sourcechannelrate"], 
			// 		'dailyincome' =>$dailyincome 
			// 	);
			// 	$dailyaccountModel->where('id='.$value['id'])->save($dailyaccountData);
			// }

            $this->insertLog($_SESSION['adminname'],'自定义分成比例', 'UserAction.class.php', 'modifyUserRate', $time, $_SESSION['adminname']."编辑了用户“".$user['account']."”的渠道名为“".$channel['channelname']."”游戏名为“".$game['gamename']."”，分成比例由“".$oldsource['sourcesharerate']."变为".$data["sourcesharerate"] ."”，通道费由“".$oldsource['sourcechannelrate']."变为".$data['sourcechannelrate']."”");
            $this->ajaxReturn('success',"成功。",1);
			exit();
		} else {
			$this->ajaxReturn('fail','出现一个错误，请联系管理员。',0);
			exit();
		} 
	}

	public function downloadapk(){
		$adminid = $_SESSION["adminid"];
		if (isset($adminid) && $adminid > 0) {
			$sourcesn = $_GET["source"];
			$sourcemodel = M('tg_source');
			$map["sourcesn"] = $sourcesn;
			$source = $sourcemodel->where($map)->find();
			$newgamename = $source["apkurl"];
			if ($source["isupload"] == 1) {
				Header("Location: ".$this->apkdownloadurl.$newgamename." ");
				exit();
			} else {
				$gamemodel = M('tg_game');
				$game = $gamemodel->find($source["gameid"]);
				$packagename = $game["packagename"];
				if ($game["gameversion"] != "") {
					$newgamename = $game["gamepinyin"]."_".$game["gameversion"]."_".$source["channelid"]."_".date("md")."_".$this->makeStr(4).".apk";
				} else {
					$newgamename = $game["gamepinyin"]."_".$source["channelid"].".apk";
				}
				$result = $this->subpackage($packagename,$newgamename,$sourcesn);
				if ($result == "true") {
					$data["isupload"] = 1;
					$data["apkurl"] = $newgamename;
					$upload = $sourcemodel->where($map)->save($data);
					Header("Location: ".$this->apkdownloadurl.$newgamename." ");
					exit();
				}
			}
		}
	}

    public function error505(){
        $this->display();
    }

    public function welcome(){
        $this->logincheck();
        $this->menucheck();
        $this->display();
    }

    // 用户-资源-推广链接
    public function material(){
    	$this->logincheck();
    	$this->menucheck();
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


    // ----------------内部函数--------------
	//分包
	private function subpackage($packagename,$newgamename,$sourcesn){
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

    private function makeStr($length) { 
		$possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"; 
		$str = ""; 
		while(strlen($str) < $length) {
			$str .= substr($possible, (rand() % strlen($possible)), 1);
		}
		return($str); 
	}
}
?>