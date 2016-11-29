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
		
    	$this->sign = "admin";  //用于密码加密
		$this->AUTH_KEY = '9e13yK8RN2M0lKP8CLRLhGs468d1WMaSlbDeCcI_1tsdk@you@sdk@2015';
		$this->LOGIN_KEY = '5dfb49dkm1c25n7cgh6s_tg';
    	$this->appkeysign = "chuyou_sdk_2014";
		$this->tgdomain = "http://tg.yxgames.com";
        $this->admindomain = "http://tgadmin.yxgames.com";
		$this->domainhost = "http://www.yxgames.com";
		$this->apkstoreurl = "http://tgadmin.yxgames.com/DataGames/upfiles/basicpackage/";
		$this->apkdownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/downloadpackage/";
		$this->texturedownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/texture/";
		$this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/";
                $this->gamebgurl = "http://img.yxgames.com/images/upfiles/gamebg/";
                $this->screenshoturl = "http://img.yxgames.com/images/upfiles/screenshot/";
                $this->packageStoreFolder = "../admin/DataGames/upfiles/basicpackage/";
		$this->downloadStoreFolder = "../admin/DataGames/upfiles/downloadpackage/";
		$this->textureStoreFolder = "../admin/DataGames/upfiles/texture/";
		$this->iconStoreFolder = "../admin/upfiles/gameicon/";
		$this->gamebgStoreFolder = "../admin/DataGames/upfiles/gamebg/";
        $this->screenshotStoreFolder = "../admin/DataGames/upfiles/screenshot/";
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
    public function insertLog($username,$type,$class,$function,$time,$content) {
        $model = M('tg_log');
        $data['username'] = $username;
        $data['type'] = $type;
        $data['class'] = $class;
        $data['function'] = $function;
        $data['createtime'] = $time;
        $data['content'] = $content;
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

}
