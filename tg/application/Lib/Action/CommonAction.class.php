<?php
/**
* 公用类
*
* @author
*/

class CommonAction extends Action {
	public $website;
	public $appkeysign;
	public $sign;
	public $_departid;
	public $AUTH_KEY;
    public $domainhost;
	/**
	 * 构造函数
	 * 
	 * @return void
	 */
    public function __construct(){
    	parent::__construct();
		
    	$this->sign = C("sign");  //用于密码加密
		$this->AUTH_KEY = C('AUTH_KEY');
		$this->LOGIN_KEY = C('LOGIN_KEY');
    	$this->appkeysign = C("appkeysign");
		$this->tgdomain = C("tgdomain");
        $this->admindomain = C("admindomain");
		$this->domainhost = C("domainhost");
		$this->iconurl = C("iconurl"); //图标单独上传到admin/upfiles
        $this->apkdownloadcdnurl = C("apkdownloadcdnurl"); //注意:cdn分包，只能是线上测试

        // 测试服务器上使用
        $this->apkstoreurl = C("apkstoreurl"); //母包
        $this->apkdownloadurl = C("apkdownloadurl"); //分包
        $this->texturedownloadurl = C("texturedownloadurl"); //素材包
        $this->gamebgurl = C("gamebgurl"); //游戏背景
		$this->screenshoturl = C("screenshoturl"); //游戏截图

        $this->packageStoreFolder = C("packageStoreFolder");
		$this->downloadStoreFolder = C("downloadStoreFolder");
		$this->textureStoreFolder = C("textureStoreFolder");
		$this->iconStoreFolder = C("iconStoreFolder");
		$this->gamebgStoreFolder = C("gamebgStoreFolder");
        $this->screenshotStoreFolder = C("screenshotStoreFolder");
		$this->assign("DOMAINHOST",$this->domainhost);
        $this->assign("TGDOMAIN", $this->tgdomain);
		$this->assign("ADMINDOMAIN", $this->admindomain);
		$this->assign("APKDOWNLOADURL", $this->apkdownloadurl);
		$this->assign("TEXTUREDOWNLOADURL", $this->texturedownloadurl);
		$this->assign("ICONURL", $this->iconurl);
		$this->assign("GAMEBGURL", $this->gamebgurl);
		$this->assign("SCREEMSHOTURL", $this->screenshoturl);
        $this->assign('allUnreadMessage',$this->allUnreadMessage());
		$this->assign("usergender",$_SESSION['gender']);
		$this->assign("usertype",$_SESSION['usertype']);
		$this->assign("useraccount",$_SESSION['account']);
		$this->assign("ICONSTOREFOLDER", $this->iconStoreFolder);
		$this->assign("GAMEBGSTOREFOLDER", $this->gamebgStoreFolder);
		$this->assign("SCREEMSHOTSTOREFOLDER", $this->screenshotStoreFolder);
		//dump($this->allUnreadMeaasge());
		//exit;

		// 如果存在cookie，则自动登录
        $auto=$_COOKIE['yx_auto'];
        if($auto){
            $userMModel = M('tg_user');
            $jiemi_auto=base64_decode($auto);
            $jiemi_auto_arr=explode('|',$jiemi_auto);
            $userid=$jiemi_auto_arr[0];
            $existuser=$userMModel->field('userid,account,usertype,gender,isverified')->where('userid='.$userid)->find();
            $_SESSION['userid'] = $existuser['userid'];
            $_SESSION['account'] = $existuser['account'];
            $_SESSION['usertype'] = $existuser['usertype'];
            $_SESSION['gender'] = $existuser['gender'];
			$_SESSION['isverified'] = $existuser['isverified'];
			$key = $this->LOGIN_KEY;
			$_SESSION["loginkey"] = $key;
        }

        if($_SESSION['userid']){
        	$userModel = M('tg_user');
        	$where = array('userid' => $_SESSION['userid'] );
        	$user = $userModel->field('pid,channelid,sourcetype,cooperative')->where($where)->find();

        	if($user['pid'] > 0){ //子账号
        		$sourceuserid =  $user['pid']; //资源关联的时候所需要的用户id

                //对于子账号来说，母账号如果是cps类型，自己也是cps类型
                $where = array('userid' => $user['pid']);
                $sourcetype =  $userModel->field('sourcetype')->where($where)->find();
                $sourcetype = $sourcetype['sourcetype'];
        	}else{ //母账号
        		$sourceuserid =  $_SESSION['userid'];
                $sourcetype = $user['sourcetype'];
        	}
        	
            $this->userpid = $user['pid'];
        	$this->userid = $user['userid'];
            $this->userchannelid = $user['channelid'];
        	$this->sourcetype = $sourcetype;
        	$this->sourceuserid = $sourceuserid;

        	$_SESSION["userpid"] = $user['pid'];
        	$_SESSION["userchannelid"] = $user['channelid'];
        	$_SESSION["sourceuserid"] = $sourceuserid;
        	$_SESSION["cooperative"] = $user['cooperative'];
            $this->assign('userpid',$user['pid']);
            $this->assign('userid',$user['userid']);
        	$this->assign('sourcetype',$sourcetype);
        	$this->assign('userchannelid',$user['channelid']);
        	$this->assign('sourceuserid',$sourceuserid);
        }
    }
    
    /**
	 * 登陆检查
	 * 
	 * @return void
	 */
    public function logincheck(){
    	if(isset($_COOKIE["sessionstorage"]) && $_COOKIE["sessionstorage"] != null){
			session_id($_COOKIE["sessionstorage"]);
			session_start();
			if ((isset($_SESSION["userid"]) && $_SESSION["userid"] != null) && ($_SESSION["loginkey"] == $this->LOGIN_KEY)) {
				if ($_SESSION["isverified"] == 0) {
					Header("Location: /verifying/ ");
					exit();
				} else if ($_SESSION["isverified"] == 1) {
					$userid = $_SESSION["userid"];
					return $userid;
				} else if ($_SESSION["isverified"] == 2) {
					Header("Location: /refused/ ");
					exit();
				} else {
					Header("Location: /verifying/ ");
					exit();
				}
			} else {
				$_SESSION["requestpage"] = $_SERVER["REQUEST_URI"];
				Header("Location: /login/ ");
				exit();
			}
		} else {
			session_start();
			if ((isset($_SESSION["userid"]) && $_SESSION["userid"] != null) && ($_SESSION["loginkey"] == $this->LOGIN_KEY)) {
				if ($_SESSION["isverified"] == 0) {
					Header("Location: /verifying/ ");
					exit();
				} else if ($_SESSION["isverified"] == 1) {
					$userid = $_SESSION["userid"];
					return $userid;
				} else if ($_SESSION["isverified"] == 2) {
					Header("Location: /refused/ ");
					exit();
				} else {
					Header("Location: /verifying/ ");
					exit();
				}
			} else {
				$_SESSION["requestpage"] = $_SERVER["REQUEST_URI"];
				Header("Location: /login/ ");
				exit();
			}
		}
    }

    /**
     *
     * 记录操作日志
     * @param $type      	操作类型
     * @param $class		操作的类
     * @param $function		操作的方法
     * @param $time			操作的时间
     * @param $content	    操作的内容
     */
    public function insertLog($username,$type,$class,$function,$time,$content,$sourceid='') {
        $model = M('tg_log');
        $data['username'] = $username;
        $data['type'] = $type;
        $data['class'] = $class;
        $data['function'] = $function;
        $data['createtime'] = $time;
        $data['content'] = $content;
        $data['source_id'] = $sourceid;
        $result = $model->data($data)->add();
        return $result;
    }

    /*导航未读消息显示6条*/
    public function allUnreadMessage(){
        $Member = D('Member');
        $allUnreadMessage = $Member->allUnreadMessage();
        return $allUnreadMessage;
    }

    /*导航未读消息显示6条*/
    public function allMessage(){
        $model = M('tg_message');
        $map['isread'] = 0;
        $data['isread'] = 1;
        $message = $model->where($map)->save($data);//未读消息变为已读
        if($message){
            $this->ajaxReturn('success','标记成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','标记失败。',0);
            exit();
        }
    }

    /**
     * HTTP请求
     * @param string $Url       地址
     * @param string $Params    请求参数
     * @param string $Method    请求方法
     * @return array $callback  返回数组
     */
    function httpreq($Url, $Params, $Method='post'){
            $Curl = curl_init();//初始化curl
            if ('get' == $Method){//以GET方式发送请求
                curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
            }else{//以POST方式发送请求
                curl_setopt($Curl, CURLOPT_URL, $Url);
                curl_setopt($Curl, CURLOPT_POST, 1);//post提交方式
                curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);//设置传送的参数
            }

            curl_setopt($Curl, CURLOPT_HEADER, false);//设置header
            curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
            //curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间

            $Res = curl_exec($Curl);//运行curl
            curl_close($Curl);//关闭curl
            return $Res;
    }

}
