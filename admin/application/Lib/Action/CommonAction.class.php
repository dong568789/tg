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
		
    	$this->sign = C('sign');  //用于密码加密
		$this->AUTH_KEY = C('AUTH_KEY');
		$this->LOGIN_KEY = C('LOGIN_KEY');
    	$this->appkeysign = C('appkeysign');
		$this->tgdomain = C('tgdomain');
        $this->admindomain = C('admindomain');
		$this->domainhost = C('domainhost');
        $this->iconurl = C('iconurl'); //图标单独上传到admin/upfiles
        $this->apkdownloadcdnurl = C('apkdownloadcdnurl'); //注意:cdn分包，只能是线上测试

        // 测试服务器上使用
        $this->apkstoreurl = C('apkstoreurl'); //母包
        $this->apkdownloadurl = C('apkdownloadurl'); //分包
        $this->texturedownloadurl = C('texturedownloadurl'); //素材包
        $this->gamebgurl = C('gamebgurl');  //游戏背景
		$this->screenshoturl = C('screenshoturl'); //游戏截图
        $this->diylogourl = C('diylogourl'); //自定义合作会员logo

     

		$this->packageStoreFolder = C('packageStoreFolder');
		$this->downloadStoreFolder = C('downloadStoreFolder');
		$this->textureStoreFolder = C('textureStoreFolder');
        $this->iconStoreFolder = C('iconStoreFolder');
        $this->gamebgStoreFolder = C('gamebgStoreFolder');
        $this->diylogoStoreFolder = C('diylogoStoreFolder');
        $this->screenshotStoreFolder = C('screenshotStoreFolder');
		$this->signCheckFolder = C('signCheckFolder');
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
        $menu = A('Permissions','Event')->getMenu($uri);
        //print_r($menu);exit;
        $this->assign('_menu',$menu);
    }

    //后台菜单权限
    public function authoritycheck($authorityid){
        $id = isset($authorityid) ? $authorityid : '1';

        $newmenuidarr = A('Permissions','Event')->getUserPermissions();
        if(array_key_exists($id,$newmenuidarr)){
            return 'ok';
        } else{
            return 'fail';
        }
    }

    /**
     * 是否合作者
     */
    public function getCooperative()
    {
        $usermodel = M('sys_admin');

        $where = array(
            'id' => $_SESSION['adminid']
        );
        $admin = $usermodel->where($where)->field('id,department_id,status')->find();

        if(in_array($admin['department_id'], array(41))){
            return $admin['department_id'];
        }

        return false;
    }

    protected function getUserIdByDep()
    {
        $cooperative = $this->getCooperative();

        $userids = array();
        if(!empty($cooperative)){
            $tgUserModel = M('tg_user');
            $users = $tgUserModel->where(array('cooperative' => $cooperative))->field('userid,account')->select();

            foreach($users as $v){
                $userids[$v['userid']] = $v;
            }

        }
        return $userids;
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
