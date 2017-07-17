<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/5
 * Time: 17:21
 */
class CpsapiAction
{

    protected $_game;

    protected $gameid;

    protected $publisher;

    protected $_user;

    public function __construct()
    {
        $this->_game = M('cps_game');

        $this->_user = M('cps_user');


        $this->init();
        /**
         * http://192.168.1.145/Yx/tg/tg/index.php/Cpsapi/u
         * http://tg.yxgames.com/Cpsapi/u
         *
         */
    }

    public function init()
    {

        $this->gameid       = isset($_POST['gi']) ? (int)$_POST['gi'] : '';

        $this->publisher    = $this->_game->where(array('gameid' => $this->gameid))->getField('publisher');
    }


    public function u()
    {
        $action = isset($_POST['t']) ? trim($_POST['t']) : '';

        error_log("[".date("Y-m-d H:i:s")."]---------".print_r($_POST,true)."----------<br />",3,'./data.html');
        switch($action){
            case 'update':
                $this->downUrl();
                break;
            case 'onekey_register':
                $this->createUser();
                break;
            case 'phone_register':
                $this->createUser();
                break;
            case 'pay':
                $this->createPay();
                break;
            case 'bind_phone':
                $this->bindPhone();
                break;
            case 'set_username':
                $this->updateUserName();
                break;
            default :
                $this->error();
        }
    }


    /**
     * 创建订单
     * @return bool
     */
    private function createPay()
    {
        $agent      = isset($_POST['ai']) ? $_POST['ai'] : '';
        $ip         = isset($_POST['i']) ? $_POST['i'] : '';
        $username   = isset($_POST['un']) ? $_POST['un'] : '';
        $payType    = isset($_POST['pw']) ? $_POST['pw'] : '';
        $amount     = isset($_POST['c']) ? $_POST['c'] : '';
        $pn         = isset($_POST['pn']) ? $_POST['pn'] : '';
        $orderid    = isset($_POST['oi']) ? $_POST['oi'] : '';

        $user = $this->getUserByName($username);

        $payModel = M('cps_pay');

        $check = $payModel->where(array('orderid'=>$orderid))->find();

        if(!empty($check)){
            return false;
        }
        $isPtb = $this->checkPtb($pn);
        if(!$isPtb){
            return false;
        }

        $isPw = $this->checkPw($payType);
        if(!$isPw){
            return false;
        }

        if(isMobile($username)){
            $username = $user['username'];
        }

        $payModel->orderid = $orderid;
        $payModel->username = $username;
        $payModel->amount = $amount;
        $payModel->agent = $agent;
        $payModel->regagent = $user['agent'];
        $payModel->gameid = $this->gameid;
        $payModel->productname = $pn;
        $payModel->paytype = $this->payType($payType);
        $payModel->ip = $ip;
        $payModel->status = 1;
        $payModel->create_time = time();
        $payModel->serverid = 'serverid';
        $payModel->add();
        return true;
    }

    /**
     * 注册
     * @return bool
     */
    private function createUser()
    {
        $agent = isset($_POST['ai']) ? $_POST['ai'] : '';
        $ip = isset($_POST['i']) ? $_POST['i'] : '';
        $username = isset($_POST['un']) ? $_POST['un'] : '';
        $mobile = isset($_POST['p']) ? $_POST['p'] : '';

        $user = $this->getUserNameByMobile($mobile);

        if(!empty($user)){
            $this->_user->where(array('id'=>$user['id']))->save(array('mobile'=>''))->save();
        }

        $this->_user->username = $username;
        $this->_user->mobile = isset($mobile) ? $mobile : '';
        $this->_user->ip = $ip;
        $this->_user->agent = $agent;
        $this->_user->gameid = $this->gameid;
        $this->_user->source = $this->publisher;
        $this->_user->reg_time = time();

        $this->_user->add();
        return true;
    }

    /**
     * 强更链接
     */
    private function downUrl()
    {
        $where['gameid'] = $this->gameid;

        $source = $this->_game->where($where)->field('upurl')->find();

        if(!empty($source)){
            $url = $source['upurl'];
        }

        $data = array(
            'stauts' => 'success',
            'url' => $url
        );

        exit(json_encode($data));
    }

    protected function getUserNameByMobile($mobile)
    {
        $where['mobile'] = $mobile;
        $where['publisher'] = $this->publisher;
        return $this->_user->where($where)->find();
    }

    protected function getUserByName($username)
    {
        $where['username'] = $username;
        $where['mobile'] = $username;
        $where['_logic'] = 'OR';

        $map['_complex'] = $where;
        $map['publisher'] = $this->publisher;
        return $this->_user->where($map)->find();
    }


    protected function payType($paytype)
    {
        $config = array(
            'alipay' => 'zfb',
            'wxpay_success' => 'wx',
            'unionpay_success'=>'union',
            'epay'=>'ptb',
            'tenpay'=>'tenpay'
        );

        return isset($config[$paytype]) ? $config[$paytype] : '';
    }

    /**
     * 绑定手机号
     */
    public function bindPhone()
    {
        $username = isset($_POST['un']) ? $_POST['un'] : '';
        $mobile = isset($_POST['p']) ? $_POST['p'] : '';

        $user = $this->getUserNameByMobile($mobile);

        if(!empty($user) && $user['username'] <> $username){
            $this->_user->where(array('id'=>$user['id']))->save(array('mobile'=>''))->save();
        }

        $where['username'] = $username;
        $where['publisher'] = $this->publisher;
        return $this->_user->where($where)->save(array('mobile'=>$mobile));
    }

    /**
     * 设置用户名
     */
    public function updateUserName()
    {
        $username = isset($_POST['un']) ? $_POST['un'] : '';
        $mobile = isset($_POST['p']) ? $_POST['p'] : '';

        $where['mobile'] = $mobile;
        $where['publisher'] = $this->publisher;
        return $this->_user->where($where)->save(array('username'=>$username));

    }

    protected function checkPtb($pn)
    {
        if($pn == '平台币'){
            return false;
        }
        return true;
    }

    protected function checkPw($pw)
    {
        if($pw == 'wxpay_cancel'){
            return false;
        }elseif($pw == 'unionpay_cancel'){
            return false;
        }
        return true;
    }



    private function error()
    {
        $data = array(
            'stauts' => 'error',
            'msg' => 'no zuo no die'
        );
        echo json_encode($data);
        exit;
    }
}