<?php
class CoinAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
		$userid = $_SESSION["userid"];

		// 用户信息
		$userModel= M('tg_user');
		$user = $userModel->field('userid,account,coinpreauth')->find($userid);

		// 充值记录
		$logModel= M('coin_recharge');
		$condition["L.ffusername"] = "tg_".$user["account"];
		$coinlog = $logModel->alias('L')
							->join(C('DB_PREFIX')."all_user U on U.username = L.username", "LEFT")
							->field('L.*,U.email,U.mobile')
							->where($condition)
							->order("L.id desc")
							->select();

        $this->assign('user',$user);
		$this->assign('coinlog',$coinlog);
        $this->display();
    }


	public function modifyCoinRecharge(){
		$this->logincheck();
		$rechargeuserid = $_SESSION['userid'];
		$rechargeuserModel= M('tg_user');
		$rechargeuser = $rechargeuserModel->find($rechargeuserid);

		$userModel = M('all_user');
		$username = $_POST["username"];
		$condition["username"] = $username;
		$condition["email"] = $username;
		$condition["mobile"] = $username;
		$condition['_logic'] = 'OR';
		$user = $userModel->field('agent,username,email,mobile')->where($condition)->find();

		// 自己的渠道
		$sourceModel = M('tg_source');
		if(isset($this->userpid) && $this->userpid>0){ //子账号
			$sourcecondition["channelid"] = $this->channelid;
		}else {
			$sourcecondition["userid"] = $rechargeuserid;
		}
		$sourcecondition["activeflag"] = 1;
		$source = $sourceModel->where($sourcecondition)->select();
		$sourcelist = array();
		foreach ($source as $k => $v) {
			$sourcelist[] = $v["sourcesn"];
		}

		if (!in_array($user["agent"],$sourcelist)) {
			$this->ajaxReturn('fail','该用户不属于您的推广渠道。',0);
			exit();
		} else {
			$rechargeamount = $_POST['rechargeamount'];
			$information = $_POST['information'];
			$newcoinpreauth = $rechargeuser["coinpreauth"] - $rechargeamount;
			if ($newcoinpreauth >= 0) {
				$map["userid"] = $rechargeuserid;
				$data["coinpreauth"] = $newcoinpreauth;
				$preauth = $rechargeuserModel->where($map)->save($data);
				if ($preauth || $preauth == 0) {
					$logModel = M('coin_recharge');
					$logdata["username"] = $user["username"];
					$logdata["ptb"] = $rechargeamount;
					$logdata["ffusername"] = "tg_".$rechargeuser["account"];
					$logdata["create_time"] = time();
					$logdata["beizhu"] = $information;
					$logdata["amount"] = ceil($rechargeamount / 10);
					$log = $logModel->add($logdata);
					$walletModel = M('coin_wallet');
					$walletmap["username"] = $user["username"];
					$wallet = $walletModel->where($walletmap)->find();
					if ($wallet) {
						$walletdata["ttb"] = $wallet["ttb"] + $rechargeamount;
						$walletdata["beizhu"] = $information;
						$lastmap["id"] = $wallet["id"];
						$result = $walletModel->where($lastmap)->save($walletdata);
					} else {
						$walletdata["username"] = $user["username"];
						$walletdata["ttb"] = $rechargeamount;
						$walletdata["shouchong"] = 0;
						$walletdata["create_time"] = time();
						$walletdata["beizhu"] = $information;
						$result = $walletModel->add($walletdata);
					}
					if ($result || $result == 0) {
                        $time = date('Y-m-d H:i:s',time());
                        $this->insertLog($_SESSION['account'],'充值游侠币', 'CoinAction.class.php', 'modifyCoinRecharge', $time, $_SESSION['account']."为用户“".$username."”充值了“".$rechargeamount."”游侠币");

                        $return_data=array(
                        	'username'=>$logdata["username"],
                        	'mobile'=>$user["mobile"],
                        	'email'=>$user["email"],
                        	'ptb'=> $logdata["ptb"],
                        	'create_time'=>$logdata["create_time"],
                        	'beizhu'=>$logdata["beizhu"],
                        	'newcoinpreauth'=>$newcoinpreauth,
                        );
                        $this->ajaxReturn('success',$return_data,1);
						exit();
					} else {
						$this->ajaxReturn('fail','充值失败，请联系管理员。',0);
						exit();
					}
				}
			} else {
				$this->ajaxReturn('fail','游侠币余额不足。',0);
				exit();
			}
		}
	}
}
?>