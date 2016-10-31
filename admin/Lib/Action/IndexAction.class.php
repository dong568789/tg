<?php
class IndexAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

	//后台登陆页面
	public function index(){
        $this->display();
    }

	//后台登陆页面
	public function login(){
		session_start();
		$_SESSION = array();
		session_destroy();
        $this->display();
    }

	/**
	 * 后台登陆
	 * 
	 * @return void
	 */
   /* public function userlogin(){
    	$username = $_POST["account"];
		$password = $_POST["password"];
		if ($username == "admin" && $password == "youxia@2015") {
			session_start();
			$_SESSION["userid"] = 1;
			$key = $this->LOGIN_KEY;
			$_SESSION["loginkey"] = $key;
			$this->ajaxReturn("success","登陆成功",1);
			exit();
		}
    } */
	
	public function userlogin(){
		$username = $_POST["account"];
		$password = $_POST["password"];
		$psw = md5($this->sign.$password);
		
		$model = M('sys_admin');
		$departmentmodel = M('sys_department');
		$department = $departmentmodel->where("flag = '0' OR flag = '1'")->select();
		
		
		$userscount = $model->alias("A")->join(C('DB_PREFIX')."sys_department D on A.department_id = D.id", "LEFT")->where("username = '$username' AND (D.flag = '0' OR D.flag = '1')")->count();
		
		if ($userscount > 0) {
			$map['username'] = $username;
			$existuser = $model->where($map)->find();
			$map['password'] = md5($this->sign.$password);
			if($existuser['password']==$map['password']){
				session_start();
				$key = $this->LOGIN_KEY;
				$_SESSION["adminid"] = $existuser["id"];
				$_SESSION["adminname"] = $existuser["username"];
				$_SESSION["loginkey"] = $key;

                $userlogmodel = M('tg_userlog'); //登录成功后插入userlog表
                $data['userid'] = $existuser['id'];
                $data['username'] = $existuser['username'];
                $data['activeflag'] = 1;
                $hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data['loginip'] = gethostbyname($hostname);
				$data['class'] = '后台';
                $data['createtime'] = date('Y-m-d H:i:s',time());
                $userlog = $userlogmodel->add($data);

				$this->ajaxReturn("success",'登陆成功',1);
				exit();
			}else{
				$this->ajaxReturn("fail",'密码错误。',1);
				exit();
			}

		} else {
			$this->ajaxReturn("fail",'账号不存在，请重新输入。',0);
			exit();
		}
	}

}
?>