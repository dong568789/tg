<?php
class MemberAction extends CommonAction {

    private $userid;
    public function __construct(){
        parent::__construct();

        // 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }

    }


    //个人资料
    public function index(){
        $this->logincheck();
        $Member = D("Member");
        $this->assign('user',$Member->index());
        $this->assign('userlog',$Member->userlog());
        $this->display();
    }
    //修改密码
    public function settings(){
        $this->logincheck();
        $Member = D("Member");
        $this->assign('user',$Member->index());
        $this->assign('userlog',$Member->userlog());
        $this->display();
    }

    //编辑个人资料
    public function edituser(){

        $data['realname'] = $_POST['realname'];
		if ($_POST['companyname'] != "") {
			$data['companyname'] = $_POST['companyname'];
		}
        $data['gender'] = $_POST['gender'];
        $_SESSION['gender'] = $_POST['gender'];
        $data['contactmobile'] = $_POST['contactmobile'];
        $data['contactemail'] = $_POST['contactemail'];
		if ($_POST['address'] != "") {
			$data['address'] = $_POST['address'];
		}
        $data['invoicetype'] = $_POST['invoicetype'];
        $model= M('tg_user');
        $map['userid'] = $_SESSION['userid'];
        $time = date('Y-m-d H:i:s',time());
        $olduser = $model->where($map)->find();
        $user = $model->where($map)->save($data);
        if($olduser['invoicetype'] == 0){
            $olduser['invoicetypename'] = "不开发票";
        }elseif($olduser['invoicetype'] == 1){
            $olduser['invoicetypename'] = "普通发票";
        } elseif($olduser['invoicetype'] == 2){
            $olduser['invoicetypename'] = "3%增值税发票";
        } elseif($olduser['invoicetype'] == 3){
            $olduser['invoicetypename'] = "6%增值税发票";
        }
        if($data['invoicetype'] == 0){
            $data['invoicetypename'] = "不开发票";
        }elseif($data['invoicetype'] == 1){
            $data['invoicetypename'] = "普通发票";
        } elseif($data['invoicetype'] == 2){
            $data['invoicetypename'] = "3%增值税发票";
        } elseif($data['invoicetype'] == 3){
            $data['invoicetypename'] = "6%增值税发票";
        }
		if($user || $user == 0){
            $this->insertLog($_SESSION['account'],'编辑个人资料', 'MemberAction.class.php', 'edituser', $time, $_SESSION['account']."修改了个人资料，联系姓名由“".$olduser['realname']."”变为“".$data['realname']."，联系手机由“".$olduser['contactmobile']."”变为“".$data['contactmobile']."”，联系邮箱由“".$olduser['contactemail']."”变为".$data['contactemail']."”，联系地址由“".$olduser['address']."”变为“".$data['address']."”，发票类型由“".$olduser['invoicetypename']."”变为“".$data['invoicetypename']."”");
            $this->ajaxReturn('success','用户资料更新成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','更新失败。',0);
            exit();
        }
    }

    //收件箱
    public function message(){
        $this->logincheck();
        $Member = D("Member");
        $message = $Member->message();
        $this->assign('allmessage',$message['all']);
        $this->assign('unreadmessage',$message['unread']);
        $this->display();
    }

    //标记一条为已读消息
    public function oneunread(){
        $this->logincheck();
        $model = M('tg_message');
        $id = $_POST['id'];
        $map['id'] = $id;
        $data['isread'] = 1;
        $time = date('Y-m-d H:i:s',time());
        $oldmessage = $model->where($map)->find();
        $message = $model->where($map)->save($data);//未读消息变为已读
        if($message == 1){
            $this->insertLog($_SESSION['account'],'标记一条为已读消息', 'MemberAction.class.php', 'oneunread', $time, $_SESSION['account']."标记了消息“".$oldmessage['title']."”为已读");

            $this->ajaxReturn('success','标记成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','标记失败。',0);
            exit();
        }
    }

    //标记多条为已读消息
    public function allunread(){
        $this->logincheck();
        $model = M('tg_message');
        $arr = $_POST['id'];
        if($arr == '' || $arr == null){
            $this->ajaxReturn('fail','请选择要标记的消息。',$arr);
            exit();
        } else{
            foreach($arr as $k=>$v){
                $map['id'] = $v;
                $data['isread'] = 1;
                $message = $model->where($map)->save($data);//未读消息变为已读
                $oldmessage = $model->where($map)->find();
            }
            $time = date('Y-m-d H:i:s',time());
            if($message == 1){
                $this->insertLog($_SESSION['account'],'标记多条为已读消息', 'MemberAction.class.php', 'oneunread', $time, $_SESSION['account']."标记“".$oldmessage['title']."”等多条消息为已读消息");

                $this->ajaxReturn('success','标记成功。',1);
                exit();
            }else{
                $this->ajaxReturn('fail','标记失败。',0);
                exit();
            }
        }
    }

    //删除单条消息
    public function deletemsg(){
        $this->logincheck();
        $id = $_POST['id'];
        $map['id'] = $id;
        $data['activeflag'] = 0;
        $model= M('tg_message');
        $time = date('Y-m-d H:i:s',time());
        $oldmessage = $model->where($map)->find();
        $message = $model->where($map)->save($data);
        if($message){
            $this->insertLog($_SESSION['account'],'删除消息', 'MemberAction.class.php', 'deletemsg', $time, $_SESSION['account']."删除了标题为“".$oldmessage['title']."”的消息");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //删除多条消息
    public function deleteallmsg(){
        $this->logincheck();
        $model = M('tg_message');
        $arr = $_POST['id'];
        foreach($arr as $k=>$v){
            $map['id'] = $v;
            $data['activeflag'] = 0;
            $message = $model->where($map)->save($data);
            $oldmessage = $model->where($map)->find();
        }
        $time = date('Y-m-d H:i:s',time());
        if($message){
            $this->insertLog($_SESSION['account'],'删除多条消息', 'MemberAction.class.php', 'deleteallmsg', $time, $_SESSION['account']."删除了“".$oldmessage['title']."”等多条消息");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //支付宝账号页面
    public function account(){

        $this->logincheck();
        $Member = D("Member");
        $account = $Member->account();
        $this->assign('alipay',$account['alipay']);
        $this->assign('bank',$account['bank']);

        $id = $_GET['id'];
        $type = $_GET['type']; //1支付宝 2银行卡
		if($type == 1){
            $alipayaccount = $Member->alipayaccount($id);
            $this->assign('alipayaccount',$alipayaccount);
        }
        if($type == 2){
            $bankaccount = $Member->bankaccount($id);
            $this->assign('bankaccount',$bankaccount);
        }
        $this->display();
    }

    //添加支付宝账号
    public function addAlipay(){
        $this->logincheck();
        $userid = $_SESSION['userid'];
        $model= M('tg_aliaccount');
        $data['aliaccount'] = $_POST['aliaccount'];
        $data['aliusername'] = $_POST['aliusername'];
        $data['userid'] = $userid;
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $condition["aliaccount"] = $_POST['aliaccount'];
        $condition["userid"] = $userid;
        $condition["activeflag"] = 1;
        $existalipay = $model->where($condition)->count();
        if ($existalipay > 0) {
            $this->ajaxReturn("fail",'此支付宝账号已存在，请添加其他账号',0);
            exit();
        } else {
            $alipay = $model->add($data);
            $result = $model->field('id,aliaccount,aliusername,createtime')->where($data)->find();
            if($alipay){
                $this->insertLog($_SESSION['account'],'添加支付宝账号', 'MemberAction.class.php', 'addAlipay', $data['createtime'], $_SESSION['account']."添加了账号为“".$data['aliaccount']."”，姓名为".$data['aliusername']."”的支付宝账号");
                $this->ajaxReturn('success',$result,1);
                exit();
            }else{
                $this->ajaxReturn('fail','添加失败。',0);
                exit();
            }
        }

    }

    //修改支付宝账号
    public function editalipay(){
        $this->logincheck();
        $model= M('tg_aliaccount');
        $id = $_POST['id'];
        $data['aliaccount'] = $_POST['aliaccount'];
        $data['aliusername'] = $_POST['aliusername'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $map['id'] = $id;
        $oldalipay = $model->where($map)->find();
        $alipay = $model->where($map)->save($data);
        if($alipay){
            $this->insertLog($_SESSION['account'],'修改支付宝账号', 'MemberAction.class.php', 'addAlipay', $data['createtime'], $_SESSION['account']."修改了账号由“".$oldalipay['aliaccount']."”变为".$data['aliaccount']."”，姓名由".$oldalipay['aliusername']."”变为“".$data['aliusername']."”");

            $this->ajaxReturn('success','修改成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','修改失败。',0);
            exit();
        }
    }

    //withdraw页面传来的修改银行卡账号
    public function editbank(){
        $this->logincheck();
        $model= M('tg_bankaccount');
        $id = $_POST['id'];
        $data['bankaccount'] = $_POST['bankaccount'];
        $data['bankusername'] = $_POST['bankusername'];
        $data['bankname'] = $_POST['bankname'];
        $data['branchname'] = $_POST['branchname'];
        $data['bankprovince'] = $_POST['bankprovince'];
        $data['bankcity'] = $_POST['bankcity'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $map['id'] = $id;
        $time = date('Y-m-d H:i:s',time());
        $oldbank = $model->where($map)->find();
        $bank= $model->where($map)->save($data);
        if($bank){
            $this->insertLog($_SESSION['account'],'修改银行卡账号', 'MemberAction.class.php', 'editbank', $time, $_SESSION['account']."修改了银行账号“".$oldbank['bankaccount']."”变为“".$data['bankaccount']."”，账户名称“".$oldbank['bankusername']."”变为“".$data['bankusername']."”收款银行“".$oldbank['bankname'].$oldbank['branchname']."”变为“".$data['bankname'].$data['branchname']."”开户地为“".$oldbank['bankprovince'].$oldbank['bankcity']."”变为“".$data['bankprovince'].$data['bankcity']."”");
            $this->ajaxReturn('success','修改成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','修改失败。',0);
            exit();
        }
    }

    //修改支付宝展示页
    public function showEditalipay(){
        $this->logincheck();
        $model= M('tg_aliaccount');
        $userid = $_SESSION['userid'];
        $id = $_POST['id'];
        $map['id'] = $id;
        $map['userid'] = $userid;
        $existalipay = $model->where($map)->find();
        if($existalipay){
            $this->ajaxReturn('success',$existalipay,1);
            exit();
        } else{
            $this->ajaxReturn('fail','',0);
            exit();
        }

    }

    //修改银行卡展示页
    public function showEditbank(){
        $this->logincheck();
        $model= M('tg_bankaccount');
        $userid = $_SESSION['userid'];
        $id = $_POST['id'];
        $map['id'] = $id;
        $map['userid'] = $userid;
        $existbank = $model->where($map)->find();
        if($existbank){
            $this->ajaxReturn('success',$existbank,1);
            exit();
        } else{
            $this->ajaxReturn('fail','',0);
            exit();
        }

    }



    //添加银行卡号
    public function addbBankaccount(){
        $this->logincheck();
        $userid = $_SESSION['userid'];
        $model= M('tg_bankaccount');
        $data['bankaccount'] = $_POST['bankaccount'];
        $data['bankusername'] = $_POST['bankusername'];
        $data['bankname'] = $_POST['bankname'];
        $data['branchname'] = $_POST['branchname'];
        $data['bankprovince'] = $_POST['bankprovince'];
        $data['bankcity'] = $_POST['bankcity'];
        $data['userid'] = $userid;
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $condition["bankaccount"] = $_POST['bankaccount'];
        $condition["userid"] = $userid;
        $condition["activeflag"] = 1;
        $existalipay = $model->where($condition)->count();
        if ($existalipay > 0) {
            $this->ajaxReturn("fail",'此银行卡账号已存在，请添加其他账号',0);
            exit();
        } else{
            $bank = $model->add($data);
            $result = $model->field('id,bankaccount,bankusername,bankname,branchname,bankprovince,bankcity,createtime')->where($data)->find();
            if($bank){
                $this->insertLog($_SESSION['account'],'添加银行卡账号', 'MemberAction.class.php', 'addbBankaccount', $data['createtime'], $_SESSION['account']."添加了银行账号为“".$data['bankaccount']."”，账户名称为“".$data['bankaccount']."”收款银行为“".$data['bankname'].$data['branchname']."”开户地为“".$data['bankprovince'].$data['bankcity']."”");
                $this->ajaxReturn('success',$result,1);
                exit();
            }else{
                $this->ajaxReturn('fail','新增失败。',0);
                exit();
            }
        }
    }

    //删除单条支付宝账号
    public function deleteAlipay(){
        $this->logincheck();
        $id = $_POST['id'];
        $model= M('tg_aliaccount');
        $data['activeflag'] = 0;
        $time = date('Y-m-d H:i:s',time());
        $oldalipay = $model->where("id = $id")->find();
        $alipay = $model->where("id = $id")->save($data);
        if($alipay){
            $this->insertLog($_SESSION['account'],'删除支付宝账号', 'MemberAction.class.php', 'deleteAlipay', $time, $_SESSION['account']."删除了账号为“".$oldalipay['aliaccount']."”的支付宝账号");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //删除多条支付宝账号
    public function deleteallAlipay(){
        $this->logincheck();
        $model= M('tg_aliaccount');
        $arr = $_POST['id'];
        foreach($arr as $k=>$v){
            $map['id'] = $v;
            $data['activeflag'] = 0;
            $deletealipay = $model->where($map)->save($data);
            $oldalipay = $model->where($map)->find();
        }
        $time = date('Y-m-d H:i:s',time());
        if($deletealipay){
            $this->insertLog($_SESSION['account'],'删除多个支付宝账号', 'MemberAction.class.php', 'deleteallAlipay', $time, $_SESSION['account']."删除了“".$oldalipay['aliaccount']."”等多个支付宝账号");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //删除单条银行卡账号
    public function deleteBank(){
        $this->logincheck();
        $id = $_POST['id'];
        $model= M('tg_bankaccount');
        $data['activeflag'] = 0;
        $time = date('Y-m-d H:i:s',time());
        $oldbank = $model->where("id = $id")->find();
        $bank = $model->where("id = $id")->save($data);
        if($bank){
            $this->insertLog($_SESSION['account'],'删除银行卡账号', 'MemberAction.class.php', 'deleteBank', $time, $_SESSION['account']."删除了账号为“".$oldbank['bankaccount']."”的银行卡账号");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //删除多条银行卡账号
    public function deleteallBank(){
        $this->logincheck();
        $model= M('tg_bankaccount');
        $arr = $_POST['id'];
        foreach($arr as $k=>$v){
            $map['id'] = $v;
            $data['activeflag'] = 0;
            $deletebank = $model->where($map)->save($data);
            $oldbank = $model->where($map)->find();
        }
        $time = date('Y-m-d H:i:s',time());
        if($deletebank){
            $this->insertLog($_SESSION['account'],'删除多个银行卡账号', 'MemberAction.class.php', 'deleteallBank', $time, $_SESSION['account']."删除“".$oldbank['bankaccount']."”等多个银行卡账号");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    //搜索支付宝账号
    public function searchalipay(){
        $this->logincheck();
        $model = M('tg_aliaccount');
        $userid = $_SESSION['userid'];
        $msg = $_POST['msg'];
        if($msg == ''){
            $alipay = $model->where("userid = '$userid' AND activeflag = 1")->order('createtime desc')->select();
        }else{
            $alipay = $model->where("userid = '$userid' AND (aliaccount LIKE '%$msg%' OR aliusername LIKE '%$msg%') AND activeflag = 1")->select();
        }
        if($alipay){
            $this->ajaxReturn('success',$alipay,1);
            exit();
        }else{
            $this->ajaxReturn('fail','',0);
            exit();
        }

    }

    //关闭搜索支付宝账号
    public function closeAlipay(){
        $this->logincheck();
        $model = M('tg_aliaccount');
        $userid = $_SESSION['userid'];
        $alipay = $model->where("userid = '$userid' AND activeflag = 1")->order('createtime desc')->select();
        if($alipay){
            $this->ajaxReturn('success',$alipay,1);
            exit();
        }else{
            $this->ajaxReturn('fail','',0);
            exit();
        }
    }


    //搜索银行卡账号
    public function searchbank(){
        $this->logincheck();
        $model = M('tg_bankaccount');
        $userid = $_SESSION['userid'];
        $msg = $_POST['msg'];
        if($msg == ''){
            $bank = $model->where("userid = '$userid' AND activeflag = 1")->order('createtime desc')->select();
        }else{
            $bank = $model->where("userid = '$userid' AND (bankaccount LIKE '%$msg%' OR bankusername LIKE '%$msg%' OR bankname LIKE '%$msg%' OR branchname LIKE '%$msg%' OR bankprovince LIKE '%$msg%') AND activeflag = 1")->select();
        }
        if($bank){
            $this->ajaxReturn('success',$bank,0);
            exit();
        }else{
            $this->ajaxReturn('fail','',0);
            exit();
        }
    }

    //关闭搜索银行卡账号
    public function closeBank(){
        $this->logincheck();
        $model = M('tg_bankaccount');
        $userid = $_SESSION['userid'];
        $bank = $model->where("userid = '$userid' AND activeflag = 1")->order('createtime desc')->select();
        if($bank){
            $this->ajaxReturn('success',$bank,1);
            exit();
        }else{
            $this->ajaxReturn('fail','',0);
            exit();
        }
    }

    //支付宝账号是否有结算单
    public function isusedAlipay(){
        $this->logincheck();
        $id = $_POST['id'];
        $model= M('tg_aliaccount');
        $map['id'] = $id;
        $aliaccount = $model->where($map)->find();
        $isused = $aliaccount['isused'];
        $this->ajaxReturn($isused);
        exit();
    }


    //支付宝账号是否有结算单
    public function isusedBank(){
        $this->logincheck();
        $id = $_POST['id'];
        $model= M('tg_bankaccount');
        $map['id'] = $id;
        $bankaccount = $model->where($map)->find();
        $isused = $bankaccount['isused'];
        $this->ajaxReturn($isused);
        exit();
    }


    /***
     ***修改绑定手机
     **/


    //查询用户的绑定手机号是否正确
    public function checkMobile(){
        $this->logincheck();
        $mobile = $_POST["mobile"];
        $model = M('tg_user');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $existmobile = $model->where($map)->find();
        if ($existmobile['bindmobile'] == $mobile) {
            $this->ajaxReturn("exist",'绑定手机号正确',1);
            exit();
        } else {
            $this->ajaxReturn("notexist",'绑定手机号有误',0);
            exit();
        }
    }

    //解绑发送短信验证码
    public function sendMsg(){
        $this->logincheck();
        session_start();//开启缓存
        $usermobile = $_POST["mobile"];
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
                if(!empty($usermobile)){
                    header("content-type:text/html; charset=utf-8;");//开启缓存
                    $_SESSION['smstime'] = date("Y-m-d H:i:s");
                    $smscode = rand(100000,999999);
                    $_SESSION['smscode'] = $smscode;    //将content的值保存在session
                    $username = '70208457';     //用户账号
                    $password = '15927611975';      //密码
                    $content = "此次申请修改绑定手机的验证码为".$smscode.",有效时间5分钟.";        //内容
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

        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //修改绑定手机验证旧手机
    public function editbindmobile(){
        $this->logincheck();
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
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

        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }


    //检验新手机
    public function newMobile(){
        $this->logincheck();
        $mobile = $_POST["newmobile"];
        $model = M('tg_user');
        $condition['bindmobile'] = $mobile;
        $condition['account'] = $mobile;
        $condition['_logic'] = 'OR';
        $existuser = $model->where($condition)->count();
        if ($existuser > 0) {
            $this->ajaxReturn("exist",'该手机号已被绑定，请重新输入',1);
            exit();
        } else {
            $this->ajaxReturn("notexist",'',0);
            exit();
        }
    }

    //检验新邮箱
    public function newEmail(){
        $this->logincheck();
        $email = $_POST["newemail"];
        $model = M('tg_user');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $existemail = $model->where($map)->find();
        if ($existemail['bindemail'] == $email || $existemail['account'] == $email) {
            $this->ajaxReturn("exist",'该绑定邮箱账号已存在，请重新输入',1);
            exit();
        } else {
            $this->ajaxReturn("notexist",'',0);
            exit();
        }
    }

    //新手机发送短信验证码
    public function sendCode(){
        session_start();//开启缓存
        $usermobile = $_POST["mobile"];
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(!empty($usermobile)){
                header("content-type:text/html; charset=utf-8;");//开启缓存
                $_SESSION['smstime'] = date("Y-m-d H:i:s");
                $smscode = rand(100000,999999);
                $_SESSION['smscode'] = $smscode;    //将content的值保存在session
                $username = '70208457';     //用户账号
                $password = '15927611975';      //密码
                $content = "此次申请绑定新手机的验证码为".$smscode.",有效时间5分钟.";        //内容
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

        } else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    //新手机验证码
    public function updatebindmobile(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if ($_POST['verifycode'] != $_SESSION['smscode']) {
                $this->ajaxReturn("fail",'短信验证码错误。',0);
                exit();
            } else {
                $cachetime = time() - strtotime($_SESSION['smstime']);
                if ($cachetime > 300) {
                    $this->ajaxReturn("fail",'短信验证码超时，请重新获取。',0);
                    exit();
                } else {
                    $model = M('tg_user');
                    $userid = $_SESSION['userid'];
                    $map['userid'] = $userid;
                    $data["bindmobile"] = $_POST["newmobile"];
                    $time = date('Y-m-d H:i:s',time());
                    $olduser = $model->where($map)->find();
                    $user = $model->where($map)->save($data);
                    if ($user) {
                        $this->insertLog($_SESSION['account'],'修改绑定手机', 'MemberAction.class.php', 'updatebindmobile', $time, $_SESSION['account']."修改了绑定手机由“".$olduser['bindmobile']."”变为“".$data["bindmobile"]."”");

                        $this->ajaxReturn("success",$user,1);
                        exit();
                    }
                }
            }
        }else {
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }
    }

    /***
     ***修改密码
     **/
    public function editpassword(){
        session_start();//开启缓存
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $oldpassword = sha1($_POST["oldpassword"]);
            $newpassword = $_POST['newpassword'];
            $againpassword = $_POST['againpassword'];
            $map['userid'] = $_SESSION['userid'];
            $model = M("tg_user");
            $user = $model->where($map)->find();
            if ($oldpassword != $user['password']) {
                $this->ajaxReturn("false", '原密码错误。', 0);
                exit();
            } else {
                if ($newpassword != $againpassword) {
                    $this->ajaxReturn("fail", '重复密码错误。', 0);
                    exit();
                } else {
                    $data['password'] = sha1($_POST["newpassword"]);
                    $user = $model->where($map)->save($data);
                    $time = date('Y-m-d H:i:s',time());
                    $this->insertLog($_SESSION['account'],'修改密码', 'MemberAction.class.php', 'editpassword', $time, $_SESSION['account']."修改了密码");
                    $this->ajaxReturn("success", '修改成功，请重新登录。', 1);
                    exit();

                }
            }
        }
    }





    /***
     ***修改绑定邮箱
     **/

    //修改绑定邮箱,向邮箱发送验证码
    public function editbindemail(){
        session_start();//开启缓存
        $useremail = $_POST["email"];
        $verify = $_POST["accountverify"];
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            if(md5($_POST['accountverify']) != $_SESSION['accountverify'] || $_POST['accountverify'] == ''){
                $this->ajaxReturn("fail",'图形验证码错误。',0);
                exit();
            } else {
                if($useremail != ""){
                    $_SESSION['bindemail'] = $useremail;
                    $time = time();
                    $Member = D('Member');
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
                    $html.="此次邮箱的验证码为:".$ad."。";
                    if(isset($_SESSION[$time])){
                        $state  = $Member->smtpsend('service@youxia-inc.com',$_POST['email'],'service@youxia-inc.com','gaea123','游侠推广系统绑定邮箱验证',$html);
                        if($state != ''){
                            $this->ajaxReturn("success",'验证成功，请进行下一步验证',1);
                            exit();
                        }
                    }
                }else{
                    $this->ajaxReturn("fail",'获取用户邮箱账号失败。',0);
                    exit();
                }
            }
        } else {
            $this->ajaxReturn("fail",'系统错误，请联系管理员。',0);
            exit();
        }
    }

    //检验邮箱验证码
    public function updatebindemail(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if ($_POST['emailmsg'] != $_SESSION['smscode']) {
                $this->ajaxReturn("fail", '邮箱验证码错误。', 0);
                exit();
            } else {
                $cachetime = time() - strtotime($_SESSION['smstime']);
                if ($cachetime > 300) {
                    $this->ajaxReturn("fail", '邮箱验证码超时，请重新获取。', 0);
                    exit();
                } else {
                    $model = M('tg_user');
                    $userid = $_SESSION['userid'];
                    $map['userid'] = $userid;
                    $data["bindemail"] = $_SESSION['bindemail'];
                    $time = date('Y-m-d H:i:s',time());
                    $olduser = $model->where($map)->find();
                    $user = $model->where($map)->save($data);
                    if ($user) {
                        $this->insertLog($_SESSION['account'],'修改绑定邮箱', 'MemberAction.class.php', 'updatebindemail', $time, $_SESSION['account']."修改了绑定邮箱由“".$olduser['bindemail']."”变为“".$data["bindemail"]."”");
                        $this->ajaxReturn("success", '修改绑定邮箱成功。', 1);
                        exit();
                    }
                }
            }

        }else{
            $this->ajaxReturn("fail",'系统错误。',0);
            exit();
        }

    }

}
?>