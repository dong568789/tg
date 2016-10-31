<?php

class UserModel extends Model {
    public function __construct(){
        parent::__construct();
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


}
?>