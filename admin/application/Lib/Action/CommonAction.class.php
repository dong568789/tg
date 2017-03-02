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
        $this->iconurl = "http://tgadmin.yxgames.com/upfiles/gameicon/"; //图标单独上传到admin/upfiles
        $this->apkdownloadcdnurl = "http://downloadcdn.yxgames.com/dataGames/apk/upfiles/downloadpackage/"; //注意:cdn分包，只能是线上测试

        // 测试服务器上使用
        $this->apkstoreurl = "http://tgadmin.yxgames.com/DataGames/upfiles/basicpackage/"; //母包
        $this->apkdownloadurl = "http://tgadmin.yxgames.com/DataGames/upfiles/downloadpackage/"; //分包
        $this->texturedownloadurl = "http://tgadmin.yxgames.com/DataGames/upfiles/texture/"; //素材包
        $this->gamebgurl = "http://tgadmin.yxgames.com/DataGames/upfiles/gamebg/";  //游戏背景
		$this->screenshoturl = "http://tgadmin.yxgames.com/DataGames/upfiles/screenshot/"; //游戏截图
        $this->diylogoourl = "http://tgadmin.yxgames.com/DataGames/upfiles/diylogo/"; //自定义合作会员logo

        // 正式服务器上使用
        // $this->apkstoreurl = "http://download.yxgames.com/dataGames/apk/upfiles/basicpackage/"; //母包
        // $this->apkdownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/downloadpackage/"; //分包
        // $this->texturedownloadurl = "http://download.yxgames.com/dataGames/apk/upfiles/texture/"; //素材包
        // $this->gamebgurl = "http://img.yxgames.com/images/upfiles/gamebg/"; //游戏背景
        // $this->screenshoturl = "http://img.yxgames.com/images/upfiles/screenshot/"; //游戏截图
        // $this->diylogourl = "http://img.yxgames.com/images/upfiles/diylogo/"; //自定义合作会员logo

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

        if ((isset($_SESSION["adminid"]) && $_SESSION["adminid"] != null) && ($_SESSION["loginkey"] == $this->LOGIN_KEY)) {
            $adminid = $_SESSION["adminid"];
            return $adminid;
        } else {
            $_SESSION["requestpage"] = $_SERVER["REQUEST_URI"];
            Header("Location: /login/ ");
            exit();
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
        $uri = $_SERVER['REQUEST_URI'];
        $menu = $this->getMenu($uri);
        //print_r($menu);exit;
        $this->assign('_menu',$menu);
        /*
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
        $checkChannel = $this->authoritycheck(10182);
        $checkfinance = $this->authoritycheck(10183);
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
        $this->assign('checkChannel',$checkChannel);
        $this->assign('checkfinance',$checkfinance);*/
    }

    //后台菜单权限
    public function authoritycheck($authorityid){
        $id = isset($authorityid) ? $authorityid : '1';

        $newmenuidarr = $this->getUserPermissions();
        if(array_key_exists($id,$newmenuidarr)){
            return 'ok';
        } else{
            return 'fail';
        }
    }

    /**
     * 获取菜单项
     * @return array
     */
    public function  getMenu($uri)
    {
        $menu = $this->getUserPermissions();
        $itemMenu = array();
        foreach($menu as $value){
            if($value['status']  > 0)
                continue;

            if($value['parent'] == 0){
                $itemMenu[$value['id']]['id'] = $value['id'];
                $itemMenu[$value['id']]['title'] = $value['title'];
                $itemMenu[$value['id']]['url'] = $value['url'];
                $itemMenu[$value['id']]['parent'] = $value['parent'];
                $itemMenu[$value['id']]['icon'] = $value['icon'];
            }else{
                $itemMenu[$value['parent']]['children'][$value['id']] = $value;

                if($uri == $value['url']){
                    $itemMenu[$value['parent']]['active'] = true;
                    $itemMenu[$value['parent']]['children'][$value['id']]['active'] = true;
                }
            }
        }

        ksort($itemMenu);
        return $itemMenu;
    }


    protected function getUserPermissions()
    {
        $adminpermissions = session('adminpermissions');
        if(empty($adminpermissions)){
            $departmentModel = M('sys_department');
            $menumodel = M('sys_menu');
            $usermodel = M('sys_admin');

            $where = array(
                'id' => $_SESSION['adminid']
            );
            $admin = $usermodel->where($where)->field('id,department_id,status')->find();

            if(empty($admin['department_id'])){
                return array();
            }

            $where = array(
                'id' => $admin['department_id']
            );
            $dep = $departmentModel->where($where)->field('id,menuids')->find();

            $arrMenu = explode(',', $dep['menuids']);

            $where = array(
                'id' => array('in', $arrMenu),
                'type' => 11
            );
            $menu = $menumodel->where($where)
                ->field("id,(CASE WHEN `first` > '' THEN `first` ELSE `second` END) as title,parentsid as parent,status,url,icon")
                ->select();

            //更新用户父节点
            $parentMenu = $this->updateParent($menu, $arrMenu);
            $menu = array_merge($menu, $parentMenu);
            $adminpermissions = array();
            foreach($menu as $value){
                $adminpermissions[$value['id']] = $value;
            }
            session('adminpermissions',$adminpermissions);
        }

        return $adminpermissions;
    }

    protected function updateParent(&$menu, $allperm)
    {
        $item = array();
        foreach($menu as $value){
            if($value['parent'] <> 0 && !in_array($value['parent'], $allperm)){
                $item[] = $value['parent'];
            }
        }

        if(empty($item)){
            return array();
        }

        $menumodel = M('sys_menu');
        $where = array(
            'id' => array('in', array_unique($item)),
            'status' => 0,
            'type' => 11
        );
        $parentMenu = $menumodel->where($where)
            ->field("id,(CASE WHEN `first` > '' THEN `first` ELSE `second` END) as title,parentsid as parent,status,url,icon")
            ->select();

        return (array)$parentMenu;
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
