<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/10
 * Time: 9:59
 */
class SdkTgEvent
{
    const REGAWARD = 20;
    const ACCOUNT_PREFIX = 'sdk_';
    const WARNING_VALUE = 100;
    private $_user;
    private $_source;
    private $_dailyaccount;
    private $startTime;
    private $endTime;



    public function __construct()
    {
        $this->_user = M('all_user');
        $this->_source = M('tg_source');

        // 这里改日期
        $nowday = date("Y-m-d");
        // $nowday = '2016-07-08';
        //下面不动
        $this->startTime = strtotime($nowday) - 86400; //统计当天的0点的时间戳
        $this->endTime = strtotime($nowday) - 1; //统计当天的23:59:59的时间戳

        //0 1 * * * curl -s "http://tgadmin.yxgames.com/?m=crond&a=sdktg"

    }

    private function getAllSdkTgSource()
    {
        $where['u.issdkuser'] = 1;
        $where['u.activeflag'] = 1;
        $where['s.activeflag'] = 1;
        $where['g.activeflag'] = 1;
        $where['g.isonstack'] = 0;

        $source = $this->_source->alias('s')
            ->join('inner join yx_tg_user u on s.userid=u.userid')
            ->join('inner join yx_tg_game g on s.gameid=g.gameid')
            ->where($where)
            ->field('s.sourcesn,s.userid,s.gameid,s.channelid,u.account,u.issdkuser')
            ->select();
        empty($source) && $source = array();

        return $source;
    }

    private function getRegUser()
    {
        $where['u.mobile'] = array('gt','');
        $where['u.reg_time'] = array(array('egt',$this->startTime),array('elt',$this->endTime));

        $user = $this->_user->alias('u')
            ->join('inner join yx_tg_bindmobile b on u.id=b.userid')
            ->where($where)
            ->field('u.id,u.mobile,u.username,u.agent')
            ->select();
        $data = array();
        empty($user) && $user = array();
        foreach($user as $value){
            $data[$value['agent']][] = $value;
        }
        unset($user);
        return $data;
    }

    private function updateCoin($username,$ttb)
    {
        $coinWalletModel = M('coin_wallet');
        $coinwallet = $coinWalletModel->where(array('username' => $username))->find();
        if(empty($coinwallet)){
            $res = $coinWalletModel->add(array(
                'username' => $username,
                'ttb' => $ttb,
                'shouchong' => 0,
                'create_time' => time(),
                'beizhu' => '个人推广奖励',
            ));
        }else{
            $res = $coinWalletModel->where(array('username' => $username))->setInc('ttb',$ttb);
        }

        if($res)
            $this->insertCoinRecharge($username,$ttb);

        return $res;
    }

    private function insertCoinRecharge($username,$ttb)
    {
        $coinRechargeModel = M('coin_recharge');
        $coinRechargeModel->username = $username;
        $coinRechargeModel->ptb = $ttb;
        $coinRechargeModel->ffusername = 'admin';
        $coinRechargeModel->create_time = time();
        $coinRechargeModel->beizhu = '个人推广奖励';
        $coinRechargeModel->amount = intval($ttb / 10);
        $coinRechargeModel->status = 0;
        return $coinRechargeModel->add();
    }

    private function regWarning($username,$regNum)
    {
        $ttb = intval($regNum*self::REGAWARD);
        $content = "警告：个人推广会员【{$username}】有效注册数超过阀值，注册数为【{$regNum}】,奖励游侠币为【{$ttb}】";

        $this->insertLog('admin','个人推广奖励', 'SdkTgEvent.class.php', 'run',  date('Y-m-d H:i:s'), $content);
    }

    private function insertLog($username,$type,$class,$function,$time,$content) {
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

    public function run()
    {
        $source = $this->getAllSdkTgSource();
        $user = $this->getRegUser();
        $this->_dailyaccount = M('tg_dailyaccount');
        $where['date'] = date('Y-m-d',$this->startTime);
        foreach($source as $value){
            if(array_key_exists($value['sourcesn'],$user)){
                $valpeople = count($user[$value['sourcesn']]);
                if($valpeople > 0){
                    $ttb = $valpeople * self::REGAWARD;
                    $data = array(
                        'getcoin' => $ttb,
                        'valpeople' => $valpeople
                    );
                    $where['userid'] = $value['userid'];
                    $where['channelid'] = $value['channelid'];
                    $where['gameid'] = $value['gameid'];
                    $dai = $this->_dailyaccount->where($where)->save($data);
                    if(!empty($dai)){
                        $username = str_replace(self::ACCOUNT_PREFIX, '', $value['account']);

                        if($valpeople > self::WARNING_VALUE) {
                            $this->regWarning($value['account'],$valpeople);
                        }

                        $this->updateCoin($username, $ttb);
                    }
                }
            }
        }
        return "success";
    }
}