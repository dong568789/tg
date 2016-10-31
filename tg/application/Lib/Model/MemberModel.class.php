<?php

class MemberModel extends Model
{
    public function __construct(){
        parent::__construct();
    }
    //个人资料
    public function index(){
        $usermodel = M('tg_user');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $user = $usermodel->where($map)->find();
        return $user;
    }

    //最近登录信息ueserlog
    public function userlog(){
        $userlog = M('tg_userlog');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $lastCreatetime = $userlog->max("createtime");
        $map['createtime'] = $lastCreatetime;
        $map1['userid'] = $userid;
        $map1['activeflag'] = 1;
        $map1['createtime'] = array('lt',$lastCreatetime);
        $user['first'] = $userlog->where($map)->find();
        $user['others'] = $userlog->where($map1)->order("createtime desc")->limit(50)->select();
        return $user;
    }

    //消息
    public function message(){
        $userid = $_SESSION['userid'];
        $model = M('tg_message');
        $map1['userid'] = $userid;
        $map1['activeflag'] = 1;
        $message['all'] = $model->field()->where($map1)->order('createtime desc')->select(); //全部消息
        $map2['userid'] = $userid;
        $map2['activeflag'] = 1;
        $map2['isread'] = 0;
        $message['unread'] = $model->field()->where($map2)->order('createtime desc')->select();//未读消息
        return $message;
    }

    //用户账号
    public function account(){
        $model1 = M('tg_aliaccount');
        $model2= M('tg_bankaccount');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $account['alipay'] = $model1->field()->where($map)->order('createtime asc')->select(); //全部支付宝账号
        $account['bank'] = $model2->field()->where($map)->order('createtime asc')->select(); //全部银行卡号
        return $account;
    }

    //结算页跳到修改账号信息页
    public function alipayaccount($id){
        $alipaymodel = M('tg_aliaccount');
        $map['id'] = $id;
        $alipayaccount = $alipaymodel->where($map)->find(); //某支付宝账号
        return $alipayaccount;
    }

    //结算页跳到修改账号信息页
    public function bankaccount($id){
        $bankmodel = M('tg_bankaccount');
        $map['id'] = $id;
        $bankaccount = $bankmodel->where($map)->find(); //某银行卡账号
        return $bankaccount;
    }

    //手机验证码
    public function smscode(){
        if(isset($_POST)&&$_POST['kind']=='mobile'){
            $user = M('all_user');
            $map['mobile'] = $_POST['mobile'];
            $users = $user->field('id,mobile')->where($map)->find();
            if($users != ''){
                return 'exist';
            }else{
                $_SESSION['time'] = time();
                return smscode($map['mobile']);
            }
        }
    }

    public function smtpsend($smtpusermail,$smtpemailto,$smtpuser,$smtppass,$mailtitle,$mailcontent){
		$mailtype="HTML";
		require_once "Email.class.php";
        //import("ORG.Email"); 
        //******************** 配置信息 ********************************
        $smtpserver = "smtp.qq.com";//SMTP服务器
        $smtpserverport =25;//SMTP服务器端口
        //************************ 配置信息 ****************************
        $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug = false;//是否显示发送的调试信息
        $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
        return $state;
    }


    //导航栏消息图标
    public function allUnreadMessage(){
        $userid = $_SESSION['userid'];
        $messagemodel = M('tg_message');
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $map['isread'] = 0;
        $message = $messagemodel->where($map)->order('createtime desc')->limit(6)->select();//未读消息显示六条
        $allmessage = $messagemodel->where($map)->order('createtime desc')->select();
        $message['num'] = count($allmessage);
        foreach($message as $k => $v){
            $messagetime = date('Y-m-d H:i',strtotime($v['createtime']));
            $message[$k]['time'] = $messagetime;
        }
        return $message;
    }









}
?>