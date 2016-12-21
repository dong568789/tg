<?php
class UserAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

	public function verifying(){
        $this->display();
    }

	public function refused(){
		$userid = $_SESSION["userid"];
		
		$model = M('tg_user');
		$user = $model->find($userid);
		if ($user["refusereason"] != "") {
			$reasonarr = explode(',',$user["refusereason"]);
		}
		$refusereason["reason1"] = 0;
		$refusereason["reason2"] = 0;
		$refusereason["reason3"] = 0;
		$refusereason["reason4"] = 0;
		foreach ($reasonarr as $k => $v) {
			if ($v == 1) {
				$refusereason["reason1"] = 1;
				continue;
			} else if ($v == 2) {
				$refusereason["reason2"] = 1;
				continue;
			} else if ($v == 3) {
				$refusereason["reason3"] = 1;
				continue;
			} else if ($v == 4) {
				$refusereason["reason4"] = 1;
				continue;
			}
		}
		$this->assign("userid",$userid);
		$this->assign("refusereason",$refusereason);
        $this->display();
    }

    public function login(){
        $this->display();
    }

	public function register(){
		if (isset($_POST["isfromverify"]) && $_POST["isfromverify"] == 1) {
			if ($_POST["userid"] > 0) {
				$this->assign("isfromverify",1);
				$this->assign("verifyuserid",$_POST["userid"]);
				$model = M('tg_user');
				$user = $model->find($_POST["userid"]);
				if ($user["bindmobile"] != "") {
					$this->assign("registermethod",1);
				} else {
					$this->assign("registermethod",2);
				}
				$this->display();
			} else {
				$this->assign("isfromverify",0);
				$this->display();
			}
		} else {
			$this->assign("isfromverify",0);
			$this->display();
		}
    }

	public function mobileImageVerify() {
		session_start();//开启缓存	  
		import("ORG.Util.Image"); 
		Image::buildImageVerify($length=4,$mode=1,$type='png',$width=50,$height=20,$verifyName='mobileverify');

	}

	public function accountImageVerify() {
		session_start();//开启缓存	  
		import("ORG.Util.Image"); 
		Image::buildImageVerify($length=4,$mode=1,$type='png',$width=50,$height=20,$verifyName='accountverify');

	}

	public function mobileResetImageVerify() {
		session_start();//开启缓存	  
		import("ORG.Util.Image"); 
		Image::buildImageVerify($length=4,$mode=1,$type='png',$width=50,$height=20,$verifyName='mobileresetverify');

	}

	public function emailResetImageVerify() {
		session_start();//开启缓存	  
		import("ORG.Util.Image"); 
		Image::buildImageVerify($length=4,$mode=1,$type='png',$width=50,$height=20,$verifyName='emailresetverify');

	}
	
	//查询是否已存在相同用户名
    public function checkAccountExist(){
		$account = $_POST["account"];
        $model = M('tg_user');
        $existuser = $model->where("activeflag = 1 AND (account='$account' OR bindmobile='$account')")->count();
		if ($existuser > 0) {
			$this->ajaxReturn("exist",'用户已存在，请输入一个新的用户名。如果您已注册，请直接登陆。',1);
			exit();
		} else {
			$this->ajaxReturn("notexist",'用户不存在！',0);
			exit();
		}
    }

	//查询是否已存在相同的手机号账号
    public function checkMobileExist(){
		$mobile = $_POST["mobile"];
        $model = M('tg_user');
        $existmobile = $model->where("activeflag = 1 AND (account='$mobile' OR bindmobile='$mobile')")->count();
		if ($existmobile > 0) {
			$this->ajaxReturn("exist",'账号已存在或被他人绑定，请重新输入一个手机号。如果您已注册，请直接登陆。',1);
			exit();
		} else {
			$this->ajaxReturn("notexist",'手机号不存在。',0);
			exit();
		}
    }

	//发送短信验证码
    public function sendMsg(){
		session_start();//开启缓存
		$usermobile = $_POST["mobile"];
		$verify = $_POST["verify"];
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			if(md5($_POST['verify']) != $_SESSION['mobileverify'] || $_POST['verify'] == ''){
				$this->ajaxReturn("fail",'图形验证码错误。',0);
				exit();
			} else {
				if(!empty($usermobile)){
					header("content-type:text/html; charset=utf-8;");//开启缓存
					$_SESSION['smstime'] = date("Y-m-d H:i:s");
					$smscode = rand(100000,999999);
					$_SESSION['smscode'] = $smscode;    //将content的值保存在session
					$username = '70208457';     //用户账号
					$password = '15927611975';      //密码
					$content = "此次申请绑定手机的验证码为".$smscode.",有效时间5分钟.";        //内容
					$http = 'http://api.duanxin.cm/';
					$data = array
					(
					'action'=>'send',
					'username'=>$username,                  //用户账号
					'password'=>strtolower(md5($password)), //MD5位32密码
					'phone'=>$usermobile,                //号码
					'content'=>$content,            //内容
					'time'=>$_SESSION['smstime'],      //定时发送
					'encode'=>'utf8'
					);
					/*POST方式提交*/    
					$row = parse_url($http);
					$host = $row['host'];
					$port = $row['port'] ? $row['port']:80;
					$file = $row['path'];
					while (list($k,$v) = each($data)){
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
					if( trim($re) == '100' ){
						$this->ajaxReturn("success",'短信验证码发送成功，有效时间5分钟。',1);
						exit();
					}else{
						$this->ajaxReturn("fail",'短信验证码发送失败。',0);
						exit();
					}
				}else{
					$this->ajaxReturn("fail",'获取用户手机号码失败。',0);
					exit();
				}						
			} 
		} else {
			$this->ajaxReturn("fail",'系统错误。',0);
			exit();
		}
    }

	//手机号注册
	public function mobileregister(){
		session_start();//开启缓存
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			if(md5($_POST['mobileverify']) != $_SESSION['mobileverify'] || $_POST['mobileverify'] == ''){
				$this->ajaxReturn("fail",'图形验证码错误。',0);
				exit();
			} else {
				if ($_POST['verifymsg'] != $_SESSION['smscode']) {
					$this->ajaxReturn("fail",'短信验证码错误。',0);
					exit();
				} else {
					$cachetime = time() - strtotime($_SESSION['smstime']);
					if ($cachetime > 300) {
						$this->ajaxReturn("fail",'短信验证码超时，请重新获取。',0);
						exit();
					} else {
						$model = M('tg_user');
                        $username = $_POST["mobile"];
						$check["account"] = $_POST["mobile"];
                        $check["activeflag"] = 1;
						$checkresult = $model->where($check)->find();
						if ($checkresult) {
							$this->ajaxReturn("fail",'该用户已注册，请直接登陆。',0);
							exit();
						} else {
							$data["account"] = $_POST["mobile"];
							$data["bindmobile"] = $_POST["mobile"];
							$data["contactmobile"] = $_POST["mobile"];
							$data["password"] = sha1($_POST["password"]);
							$data["usertype"] = 1;
							$data["gender"] = 0;
							$data["invoicetype"] = 0;
							$data["withdrawlimit"] = 100;
							$data['activeflag'] = 1;
							$data['createtime'] = date('Y-m-d H:i:s',time());
							$data['createuser'] = $_POST["mobile"];
							$data['isverified'] = 0;
							$user = $model->add($data);
							if ($user) {
								$_SESSION["account"] = $data["account"];
                                $userone = $model->where("account='$username' AND activeflag = 1")->find();
                                $userlogmodel = M('tg_userlog'); //注册成功后插入userlog表
                                $data1['userid'] = $userone['userid'];
                                $data1['username'] = $userone['account'];
                                $data1['activeflag'] = 1;
                                $hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                                $data1['loginip'] = gethostbyname($hostname);
                                $data1['createtime'] = date('Y-m-d H:i:s',time());
                                $userlog = $userlogmodel->add($data1);
								$this->ajaxReturn("success",$user,1);
								exit();
							} else {
								$this->ajaxReturn("fail",'注册失败，请联系管理员。',0);
								exit();
							}
						}
					}
				}
			}
		} else {
			$this->ajaxReturn("fail",'系统错误。',0);
			exit();
		}
	}

	//用户名注册
	public function accountregister(){
		session_start();//开启缓存
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			if(md5($_POST['accountverify']) != $_SESSION['accountverify'] || $_POST['accountverify'] == ''){
				$this->ajaxReturn("fail",'图形验证码错误。',0);
				exit();
			} else {
                $model = M('tg_user');
                $username = $_POST["account"];
                $existuser = $model->where("account='$username' OR bindmobile='$username' AND activeflag = 1")->count();
                if ($existuser > 0) {
                    $this->ajaxReturn("exist",'用户已存在，请输入一个新的用户名。如果您已注册，请直接登陆。',1);
                    exit();
                } else{
					$model = M('tg_user');
					$check["account"] = $_POST["account"];
					$checkresult = $model->where($check)->find();
					if ($checkresult) {
						$this->ajaxReturn("fail",'该用户已注册，请直接登陆。',0);
						exit();
					} else {
						$data["account"] = $_POST["account"];
						$data["password"] = sha1($_POST["password"]);
						$data["usertype"] = 1;
						$data["gender"] = 0;
						$data["invoicetype"] = 0;
						$data['activeflag'] = 1;
						$data['createtime'] = date('Y-m-d H:i:s',time());
						$data['createuser'] = $_POST["account"];
						$data['isverified'] = 0;
						$user = $model->add($data);
						if ($user) {
                            $_SESSION["account"] = $data["account"];
                            $userone = $model->where("account='$username' AND activeflag = 1")->find();
                            $userlogmodel = M('tg_userlog'); //注册成功后插入userlog表
                            $data1['userid'] = $userone['userid'];
                            $data1['username'] = $userone['account'];
                            $data1['activeflag'] = 1;
                            $hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                            $data1['loginip'] = gethostbyname($hostname);
                            $data1['createtime'] = date('Y-m-d H:i:s',time());
                            $userlog = $userlogmodel->add($data1);
							$this->ajaxReturn("success",$user,1);
							exit();
						} else {
							$this->ajaxReturn("fail",'注册失败，请联系管理员。',0);
							exit();
						}
					}
                }
			}
		} else {
			$this->ajaxReturn("fail",'系统错误。',0);
			exit();
		}
	}

	//完善信息
	public function inforegister(){
		if (isset($_POST["hiddeninfouserid"]) && $_POST["hiddeninfouserid"] != "") {
			$condition["userid"] = $_POST["hiddeninfouserid"];
			$model = M('tg_user');
			$data["usertype"] = $_POST["usertype"];
			$data["companyname"] = $_POST["companyname"];
			$data["gender"] = $_POST["gender"];
			$data["realname"] = $_POST["realname"];
			$registertype = $_POST["hiddenregistertype"];
			if ($registertype == "account") {
				$data["contactmobile"] = $_POST["contactmobile"];
			}
			$data["contactemail"] = $_POST["contactemail"];
			if ($_POST["usertype"] == 1) {
				$data["invoicetype"] = $_POST["userinvoicetype"];
			} else {
				$data["invoicetype"] = $_POST["companyinvoicetype"];
			}
			$data["address"] = $_POST["address"];
			$data["postnumber"] = $_POST["postnumber"];
			if ($_POST["hiddenisfromverify"] == 1) {
				$data['isverified'] = 0;
			}
			$user = $model->where($condition)->save($data);
			if ($user) {
                $_SESSION['usertype'] = $data['usertype'];
                $_SESSION['gender'] = $data['gender'];
				$this->ajaxReturn("success",$user,1);
				exit();
			} else {
				$this->ajaxReturn("fail",'信息更新失败，请联系管理员。',0);
				exit();
			}
		} else {
			$this->ajaxReturn("fail",'信息更新失败，请联系管理员。',0);
			exit();
		}
	}

	//新增一个渠道
	public function channelregister(){
		if (isset($_POST["hiddenchanneluserid"]) && $_POST["hiddenchanneluserid"] != "") {
			$channelmodel = M('tg_channel');
			$data["userid"] = $_POST["hiddenchanneluserid"];
			$data["channelname"] = $_POST["channelname"];
			$data["channeltype"] = $_POST["channeltype"];
			$data["channelsize"] = $_POST["channelsize"];
			$data["description"] = $_POST["description"];
			$data["gamecount"] = 0;
			$data['activeflag'] = 1;
			$data['createtime'] = date('Y-m-d H:i:s',time());
			$channel = $channelmodel->add($data);
			if ($channel) {
				$userid = $_POST["hiddenchanneluserid"];
				$_SESSION["userid"] = $userid;
				$usermodel = M('tg_user');
				$existuser = $usermodel->find($userid);
				$_SESSION['account'] = $existuser['account'];
                $_SESSION['usertype'] = $existuser['usertype'];
                $_SESSION['gender'] = $existuser['gender'];
				$_SESSION['isverified'] = $existuser['isverified'];
				$key = $this->LOGIN_KEY;
				$_SESSION["loginkey"] = $key;
				$this->ajaxReturn("success",$channel,1);
				exit();
			} else {
				$this->ajaxReturn("fail",'新增渠道失败，请联系管理员。',0);
				exit();
			}
		} else {
			$this->ajaxReturn("fail",'新增渠道失败，请联系管理员。',0);
			exit();
		}
	}

    //用户登录
    public function userlogin(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(md5($_POST['accountverify']) != $_SESSION['accountverify'] || $_POST['accountverify'] == ''){
                $this->ajaxReturn("fail",'验证码错误。',0);
                exit();
            }else{
                $username = $_POST["account"];
                $remember = $_POST['remember'];
                $model = M('tg_user');
                $existuser = $model->where("activeflag = 1 AND (binary account='$username' OR bindmobile='$username')")->count();
                if ($existuser > 0) {
                    $existuser = $model->where("activeflag = 1 AND (binary account='$username' OR bindmobile='$username')")->find();
                    $map['password'] = sha1($_POST["password"]);
					
					
					// Todo 这里有一个通用密码，需要在测试完成后修改


                    if($existuser['password']==$map['password'] || $map['password']==sha1("147258")){
                        $_SESSION['userid'] = $existuser['userid'];
                        $_SESSION['account'] = $existuser['account'];
                        $_SESSION['usertype'] = $existuser['usertype'];
                        $_SESSION['gender'] = $existuser['gender'];
						$_SESSION['isverified'] = $existuser['isverified'];
						$key = $this->LOGIN_KEY;
						$_SESSION["loginkey"] = $key;
                        $userlogmodel = M('tg_userlog'); //登录成功后插入userlog表
                        $data['userid'] = $existuser['userid'];
                        $data['username'] = $existuser['account'];
                        $data['activeflag'] = 1;
                        $hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $data['loginip'] = gethostbyname($hostname);
                        $data['class'] = '前台';
                        $data['createtime'] = date('Y-m-d H:i:s',time());
                        $userlog = $userlogmodel->add($data);
                        if($remember == 1){
	                        $aut_login_days=30;
	                        $aut_login_seconds=3600*24*$aut_login_days;
	                        $ip = get_client_ip();
	                        $value = $_SESSION['userid'] . '|' . $ip;
	                        $value = base64_encode($value);
	                        cookie('auto',$value,array('expire'=>$aut_login_seconds,'prefix'=>'yx_'));
                        }

                        $this->ajaxReturn("true",'密码正确。',1);
                        exit();
                    } else {
                        $this->ajaxReturn("false",'密码错误。',1);
                        exit();
                    }

                } else {
                    $this->ajaxReturn("notexist",'账号不存在，请重新输入。',0);
                    exit();
                }
            }

        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }


    //首页的登录
    public function do_login(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if($_POST["account"] == ''){
                $this->ajaxReturn("fail",'请输入一个账号。',0);
                exit();
            }else{
                $username = $_POST["account"];
                $remember = $_POST['remember'];
                $model = M('tg_user');
                $existuser = $model->where("activeflag = 1 AND (binary account='$username' OR bindmobile='$username')")->count();
                if ($existuser > 0) {
                    $existuser = $model->where("activeflag = 1 AND (binary account='$username' OR bindmobile='$username')")->find();
                    $map['password'] = sha1($_POST["password"]);
                    if($existuser['password']==$map['password']){
                        $_SESSION['userid'] = $existuser['userid'];
                        $_SESSION['account'] = $existuser['account'];
                        $_SESSION['usertype'] = $existuser['usertype'];
                        $_SESSION['gender'] = $existuser['gender'];
						$_SESSION['isverified'] = $existuser['isverified'];
                        $key = $this->LOGIN_KEY;
                        $_SESSION["loginkey"] = $key;
                        $userlogmodel = M('tg_userlog'); //登录成功后插入userlog表
                        $data['userid'] = $existuser['userid'];
                        $data['username'] = $existuser['account'];
                        $data['activeflag'] = 1;
                        $data['class'] = '前台';
                        $data['createtime'] = date('Y-m-d H:i:s',time());
                        $hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $data['loginip'] = gethostbyname($hostname);
                        $userlog = $userlogmodel->add($data);
                        if($remember == 1){
	                        $aut_login_days=30;
	                        $aut_login_seconds=3600*24*$aut_login_days;
	                        $ip = get_client_ip();
	                        $value = $_SESSION['userid'] . '|' . $ip;
	                        $value = base64_encode($value);
	                        cookie('auto',$value,array('expire'=>$aut_login_seconds,'prefix'=>'yx_'));
                        }
                        $this->ajaxReturn("true",'密码正确。',1);
                        exit();
                    }else{
                        $this->ajaxReturn("false",'密码不正确。',1);
                        exit();
                    }

                } else {
                    $this->ajaxReturn("notexist",'账号不存在，请重新输入。',0);
                    exit();
                }
            }
        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }


    //手机找回密码发送短信验证码
    public function sendResetMsg(){
        session_start();//开启缓存
        $mobile = $_POST["mobile"];
        $map['bindmobile'] = $mobile;
        $map['activeflag'] = 1;
        $usermodel = M('tg_user');
        $user = $usermodel->where($map)->find();
        if(!$user){
        	$this->ajaxReturn("fail",'该绑定手机号不存在。',0);
            exit();
        }

        if($user['pid'] >0){
        	$this->ajaxReturn("fail",'你是子账号，不能修改密码，请联系母账号修改',0);
            exit();
        }

        $usermobile = $user['bindmobile'];
        $verify = $_POST["verify"];
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(md5($_POST['verify']) != $_SESSION['mobileresetverify'] || $_POST['verify'] == ''){
                $this->ajaxReturn("fail",'图形验证码错误。',0);
                exit();
            } else {
                if(!empty($usermobile)){
                    header("content-type:text/html; charset=utf-8;");//开启缓存
                    $_SESSION['bindmobile'] = $usermobile;
                    $_SESSION['smstime'] = date("Y-m-d H:i:s");
                    $smscode = rand(100000,999999);
                    $_SESSION['smscode'] = $smscode;    //将content的值保存在session
                    $username = '70208457';     //用户账号
                    $password = '15927611975';      //密码
                    $content = "此次申请绑定手机的验证码为".$smscode.",有效时间5分钟.";        //内容
                    $http = 'http://api.duanxin.cm/';
                    $data = array
                    (
                        'action'=>'send',
                        'username'=>$username,                  //用户账号
                        'password'=>strtolower(md5($password)), //MD5位32密码
                        'phone'=>$usermobile,                //号码
                        'content'=>$content,            //内容
                        'time'=>$_SESSION['smstime'],      //定时发送
                        'encode'=>'utf8'
                    );
                    /*POST方式提交*/
                    $row = parse_url($http);
                    $host = $row['host'];
                    $port = $row['port'] ? $row['port']:80;
                    $file = $row['path'];
                    while (list($k,$v) = each($data)){
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
                    if( trim($re) == '100' ){
                        $this->ajaxReturn("success",'短信验证码发送成功，有效时间5分钟。',1);
                        exit();
                    }else{
                        $this->ajaxReturn("fail",'短信验证码发送失败。',0);
                        exit();
                    }
                }else{
                    $this->ajaxReturn("fail",'该绑定手机号不存在。',0);
                    exit();
                }
            }
        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //手机找回密码下一步，跳重置密码
    public function mobilereset(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(md5($_POST['mobileresetverify']) != $_SESSION['mobileresetverify'] || $_POST['mobileresetverify'] == ''){
                $this->ajaxReturn("fail",'图形验证码错误。',0);
                exit();
            } else {
                if ($_POST['verifymsg'] != $_SESSION['smscode']) {
                    $this->ajaxReturn("fail",'短信验证码错误。',0);
                    exit();
                } else {
                    $cachetime = time() - strtotime($_SESSION['smstime']);
                    if ($cachetime > 300) {
                        $this->ajaxReturn("fail",'短信验证码超时，请重新获取。',0);
                        exit();
                    } else {
                        $this->ajaxReturn("success",'验证成功。',0);
                        exit();
                    }
                }
            }
        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //找回密码重置密码
    public function resetpassword(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $newpassword = $_POST['newpassword'];
            $againpassword = $_POST['againpassword'];
            if ($newpassword != $againpassword) {
                $this->ajaxReturn("fail", '两次密码不一致。', 0);
                exit();
            } else {
                if (isset($_SESSION['bindemail']) && $_SESSION['bindemail'] != "") {
                    $map['bindemail'] = $_SESSION['bindemail'];
                } else {
                    if (isset($_SESSION['bindmobile']) && $_SESSION['bindmobile'] != "") {
                        $map['bindmobile'] = $_SESSION['bindmobile'];
                    }
                }
                $model = M("tg_user");
                $data['password'] = sha1($_POST["newpassword"]);
                $user = $model->where($map)->save($data);
                if ($user) {
                    $this->ajaxReturn("success", '修改成功，请重新登录。', 1);
                    exit();
                } else {
                    if ($user == 0) {
                        $this->ajaxReturn("success", '修改成功，请重新登录。', 1);
                        exit();
                    } else {
                        $this->ajaxReturn("fail", "密码修改失败，请联系管理员。", 0);
                        exit();

                    }
                }
            }

        }else{
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //邮箱找回密码发送邮箱验证码
    public function sendemailMsg(){
        session_start();//开启缓存
        $email = $_POST["email"];
        $map['bindemail'] = $email;
        $usermodel = M('tg_user');
        $user = $usermodel->where($map)->find();
        $useremail = $user['bindemail'];
        $accountuser = $user['account'];
        $verify = $_POST["emailverify"];
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(md5($_POST['emailverify']) != $_SESSION['emailresetverify'] || $_POST['emailverify'] == ''){
                $this->ajaxReturn("fail",'图形验证码错误。',0);
                exit();
            } else {
                if(!empty($useremail)){
			        if($user['pid'] >0){
			        	$this->ajaxReturn("fail",'你是子账号，不能修改密码，请联系母账号修改',0);
			            exit();
			        }
                    $_SESSION['bindemail'] = $useremail;
                    header("content-type:text/html; charset=utf-8;");//开启缓存
                    $time = time();
                    $User = D('User');
                    session_start();//开启缓存
                    $id = session_id();
                    $_SESSION[$time]=array(
                        'email' => $_POST['email']
                    );
                    $_SESSION['email'] = $_SESSION[$time]['email'];
                    $ad = rand(100000,999999);
                    $_SESSION['smscode'] = $ad;
                    $_SESSION['smstime'] = date("Y-m-d H:i:s");
                    $html='';
                    $html.="此次邮箱的验证码为:".$ad."。您的登录账号为：".$accountuser."";
                    if(isset($_SESSION[$time])){
                        $state  = $User->smtpsend('service@youxia-inc.com',$_POST['email'],'service@youxia-inc.com','gaea123','游侠推广系统绑定邮箱验证',$html);
                        if($state != ''){
                            $this->ajaxReturn("success",'验证成功，请进行下一步验证',1);
                            exit();
                        }
                    }
                }else{
                    $this->ajaxReturn("fail",'该绑定邮箱账号不存在。',0);
                    exit();
                }
            }
        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //邮箱找回密码下一步，跳重置密码
    public function emailreset(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if (md5($_POST['emailresetverify']) != $_SESSION['emailresetverify'] || $_POST['emailresetverify'] == '') {
                $this->ajaxReturn("fail", '图形验证码错误。', 0);
                exit();
            } else {
                if ($_POST['verifyemailmsg'] != $_SESSION['smscode']) {
                    $this->ajaxReturn("fail", '邮箱验证码错误。', 0);
                    exit();
                } else {
                    $cachetime = time() - strtotime($_SESSION['smstime']);
                    if ($cachetime > 300) {
                        $this->ajaxReturn("fail", '邮箱验证码超时，请重新获取。', 0);
                        exit();
                    } else {
                        $this->ajaxReturn("success", '验证成功。', 1);
                        exit();
                    }
                }
            }
        }else{
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

	//登出
	public function logout(){
		session_start();
		$_SESSION = array();
		session_destroy();
		//删除用于自动登录的COOKIE
        setcookie('yx_auto', '', time() - 3600, '/');
		Header("Location: /login/ ");
		exit();
	}
}
?>
