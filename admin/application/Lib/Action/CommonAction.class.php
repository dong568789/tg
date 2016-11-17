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
		$this->apkdownloadurl = "http://download.yxgames.com/DataGames/apk/upfiles/downloadpackage/";
		$this->texturedownloadurl = "http://download.yxgames.com/DataGames/apk/upfiles/texture/";
        $this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/";

        // 测试服务器上使用
        $this->gamebgurl = "http://tgadmin.yxgames.com/DataGames/upfiles/gamebg/";
		$this->screenshoturl = "http://tgadmin.yxgames.com/DataGames/upfiles/screenshot/";
        $this->diylogoourl = "http://tgadmin.yxgames.com/DataGames/upfiles/diylogo/";

        // 正式服务器上使用
        // $this->gamebgurl = "http://img.yxgames.com/images/upfiles/gamebg/";
        // $this->screenshoturl = "http://img.yxgames.com/images/upfiles/screenshot/";
        // $this->diylogourl = "http://img.yxgames.com/images/upfiles/diylogo/";

		$this->packageStoreFolder = "../admin/DataGames/upfiles/basicpackage/";
		$this->downloadStoreFolder = "../admin/DataGames/upfiles/downloadpackage/";
		$this->textureStoreFolder = "../admin/DataGames/upfiles/texture/";
        $this->iconStoreFolder = "../admin/upfiles/gameicon/";
        $this->gamebgStoreFolder = "../admin/DataGames/upfiles/gamebg/";
        $this->diylogoStoreFolder = "../admin/DataGames/upfiles/diylogo/";
        $this->screenshotStoreFolder = "../admin/DataGames/upfiles/screenshot/";
		$this->signCheckFolder = "/var/www/admin/DataGames/upfiles/basicpackage/";
		$this->assign("DOMAINHOST",$this->domainhost);
        $this->assign("TGDOMAIN", $this->tgdomain);
		$this->assign("ADMINDOMAIN", $this->admindomain);
		$this->assign("APKSTOREURL", $this->apkstoreurl);
		$this->assign("APKDOWNLOADURL", $this->apkdownloadurl);
		$this->assign("TEXTUREDOWNLOADURL", $this->texturedownloadurl);
        $this->assign("ICONURL", $this->iconurl);
        $this->assign("GAMEBGURL", $this->gamebgurl);
		$this->assign("SCREEMSHOTURL", $this->screenshoturl);
		$this->assign("PACKAGESTOREFOLDER", $this->packageStoreFolder);
		$this->assign("DOWNLOADSTOREFOLDER", $this->downloadStoreFolder);
		$this->assign("TEXTURESTOREFOLDER", $this->textureStoreFolder);
        $this->assign("ICONSTOREFOLDER", $this->iconStoreFolder);
        $this->assign("GAMEBGSTOREFOLDER", $this->gamebgStoreFolder);
		$this->assign("SCREEMSHOTSTOREFOLDER", $this->screenshotStoreFolder);
    }

	/**
	 * 后台登陆检查
	 * 
	 * @return void
	 */
    public function logincheck(){
    	if(isset($_COOKIE["sessionstorage"]) && $_COOKIE["sessionstorage"] != null){
			session_id($_COOKIE["sessionstorage"]);
			session_start();
			if ((isset($_SESSION["adminid"]) && $_SESSION["adminid"] != null) && ($_SESSION["loginkey"] == $this->LOGIN_KEY)) {
				$adminid = $_SESSION["adminid"];
				return $adminid;
			} else {
				$_SESSION["requestpage"] = $_SERVER["REQUEST_URI"];
				Header("Location: /login/ ");
				exit();
			}
		} else {
			session_start();
			if ((isset($_SESSION["adminid"]) && $_SESSION["adminid"] != null) && ($_SESSION["loginkey"] == $this->LOGIN_KEY)) {
				$adminid = $_SESSION["adminid"];
				return $adminid;
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

    public function menucheck(){
        $checkgame = $this->authoritycheck(10091);
        $checknewgame = $this->authoritycheck(10092);
        $checkgameall = $this->authoritycheck(10103);
        $checkbalance = $this->authoritycheck(10104);
        $checkuserall = $this->authoritycheck(10105);
        $checkuser = $this->authoritycheck(10106);
        $newuser = $this->authoritycheck(10107);
        $checksendmail = $this->authoritycheck(10112);
        $checkannounce = $this->authoritycheck(10113);
        $announceall = $this->authoritycheck(10114);
        $checkguide = $this->authoritycheck(10115);
        $newguide = $this->authoritycheck(10116);
        $checkother = $this->authoritycheck(10117);
        $checkgamecategory = $this->authoritycheck(10118);
        $checkgametag = $this->authoritycheck(10119);
        $checkbalanceall = $this->authoritycheck(10120);
        $checkguidedetail = $this->authoritycheck(10121);
        $announcetype = $this->authoritycheck(10122);
        $newannounce = $this->authoritycheck(10123);
        $checklog = $this->authoritycheck(10124);
        $log = $this->authoritycheck(10125);
        $checkoperate = $this->authoritycheck(10126);
        $this->assign('checkgame',$checkgame);
        $this->assign('checknewgame',$checknewgame);
        $this->assign('checkgameall',$checkgameall);
        $this->assign('checkbalance',$checkbalance);
        $this->assign('checkuserall',$checkuserall);
        $this->assign('checkuser',$checkuser);
        $this->assign('newuser',$newuser);
        $this->assign('checksendmail',$checksendmail);
        $this->assign('checkannounce',$checkannounce);
        $this->assign('announceall',$announceall);
        $this->assign('checkguide',$checkguide);
        $this->assign('newguide',$newguide);
        $this->assign('checkother',$checkother);
        $this->assign('checkgamecategory',$checkgamecategory);
        $this->assign('checkgametag',$checkgametag);
        $this->assign('checkbalanceall',$checkbalanceall);
        $this->assign('checkguidedetail',$checkguidedetail);
        $this->assign('announcetype',$announcetype);
        $this->assign('newannounce',$newannounce);
        $this->assign('checklog',$checklog);
        $this->assign('log',$log);
        $this->assign('checkoperate',$checkoperate);
    }

    //后台菜单权限
    public function authoritycheck($authorityid){
        $id = isset($authorityid) ? $authorityid : '1';
        $userid = $_SESSION["adminid"];

        $usermodel = M('sys_admin');
        $dpmodel = M('sys_department');
        $user = $usermodel->where("id = '$userid'")->find();   //用户
        $departmentid = $user['department_id'];
        $department = $dpmodel ->where("id='$departmentid'")->find(); //该用户对应的部门
        $menuids = $department['menuids'];
        $menuidarr = explode(',', $menuids);   //该部门对应的权限id

        foreach($menuidarr as $k => $v){
            $menumodel = M('sys_menu');
            $menu = $menumodel->where("id = '$v'")->find();
            if($menu && $menu['parentsid'] != 0){
                $parentsid = $menu['parentsid'];
               if(!in_array($parentsid,$menuidarr)){
                   $data['menuids'] = $menuids.','.$parentsid;
                   $newdptment = $dpmodel->where("id='$departmentid'")->save($data);//没有父id的插入父id
               }
            }
        }

        $newdepartment = $dpmodel ->where("id='$departmentid'")->find(); //该用户对应的部门
        $newmenuids = $newdepartment['menuids'];
        $newmenuidarr = explode(',', $newmenuids);   //该部门对应的权限id

        if(in_array($id,$newmenuidarr)){
            return 'ok';
        } else{
            return 'fail';
        }
    }






}
