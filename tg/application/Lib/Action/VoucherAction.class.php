<?php
class VoucherAction extends CommonAction {
    public function __construct(){
        parent::__construct();

        // 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }

        // cps用户没有进入这里的权限
        if($this->sourcetype == 4){
        	Header("Location: /source/ ");
            exit();
        }
    }

    public function index(){
        $this->logincheck();
        // 用户信息
		$userid = $_SESSION["userid"];
		$userModel= M('tg_user');
		$user = $userModel->field('userid,account,coinpreauth')->find($userid);

		// 充值记录
		$voucherModel= M('voucher_buy');
		$condition=array(
			'V.buyer'=>$user["account"]
		);
		$voucherlog = $voucherModel->alias('V')
							->join(C('DB_PREFIX')."all_user U on U.username = V.username", "LEFT")
							->field('V.*,U.email,U.mobile')
							->where($condition)
							->order("V.id desc")
							->select();

		// 获取游戏
		$sourceModel= M('tg_source');
		if(isset($this->userpid) && $this->userpid>0){ //子账号
			$where["S.channelid"] = $this->channelid;
		}else {
			$where["S.userid"] = $userid;
		}
		$where['G.isusedvoucher']=1;
		$field=array(
			'S.id',
			'S.gameid',
			'S.sourcesharerate',
			'S.sourcechannelrate',
			'G.gamename',
			'C.channelname',
		);
		$source=$sourceModel->alias('S')
						->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
						->join(C('DB_PREFIX')."tg_channel C on C.channelid = S.channelid", "LEFT")
						->field($field)
						->where($where)
						->order('S.id desc')
						->group('S.gameid')
						->select();

		foreach ($source as $key => $value) {
			$source[$key]['dicount']=1-(1-$value['sourcechannelrate'])*$value['sourcesharerate'];
			$source[$key]['dicount_zhe']=$source[$key]['dicount']*10;
		}
		
		
        $this->assign('user',$user);
        $this->assign('source',$source);
		$this->assign('voucherlog',$voucherlog);
        $this->display();
    }

	public function modifyVoucherRecharge(){
		$this->logincheck();
		$rechargeuserid = $_SESSION['userid'];

		$rechargeuserModel= M('tg_user');
		$rechargeuser = $rechargeuserModel->field('coinpreauth,account')->find($rechargeuserid);

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
		$source = $sourceModel->field('sourcesn')->where($sourcecondition)->select();
		$sourcelist = array();
		foreach ($source as $k => $v) {
			$sourcelist[] = $v["sourcesn"];
		}

		if (!in_array($user["agent"],$sourcelist)) {
			$this->ajaxReturn('fail','该用户不属于您的推广渠道。',0);
			exit();
		} else {
			$sourceid = $_POST['sourceid'];
			$rechargemoney = $_POST['rechargemoney'];

			// 获取资源折扣
			$where = array('id'=>$sourceid);
			$source=$sourceModel->field('sourcechannelrate,sourcesharerate')->where($where)->find();
			$dicount=1-(1-$source['sourcechannelrate'])*$source['sourcesharerate'];
			// 实际支付的金额
			$paymoney = round($rechargemoney*$dicount,1);

			$rechargeway = 'ptb';
			$newcoinpreauth = $rechargeuser["coinpreauth"] - $paymoney*10;

			if ($newcoinpreauth >= 0) {
				// 充值推广用户表，游侠币减少 tg_user
				$map=array('userid'=>$rechargeuserid);
				$data=array('coinpreauth'=>$newcoinpreauth);
				$preauth = $rechargeuserModel->where($map)->save($data);

				if ($preauth || $preauth == 0) {
					$info=array(
						'sourceid'=>$sourceid,
						'rechargemoney'=>$rechargemoney,
						'paymoney'=>$paymoney,
						'username'=>$user['username'],
						'paytype'=>'ptb',
						'orderid'=> get_orderid(),
					);
					if($voucher_info=$this->sendPlayerVoucher($info)) {
						$voucher_buy_model = M('voucher_buy');
						$voucher_buy_data=$voucher_buy_model->where('voucherid='.$voucher_info)->field('username,productname,create_time')->find();
						$return_data=array(
			            	'username'=>$voucher_buy_data["username"],
			            	'mobile'=>$user["mobile"],
			            	'email'=>$user["email"],
			            	'productname'=> $voucher_buy_data['productname'],
			            	'create_time'=>$voucher_buy_data['create_time'],
			            	'newcoinpreauth'=>$newcoinpreauth,
			            );
			            $this->ajaxReturn('success',$return_data,1);
						exit();
					}else{
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

	// 给玩家充值代金券的内部函数
	public function sendPlayerVoucher($info_outer){
		// 代金券表 yx_voucher_info
		// 谁为谁购买代金券表 yx_voucher_buy
		// 用户领取代金券表 yx_voucher_user
		// 日志 yx_tg_log
		$info_inner=array();
		$info=array_merge($info_inner,$info_outer);
		$sourceid=$info['sourceid'];
		$rechargemoney=$info['rechargemoney'];
		$paymoney=$info['paymoney'];
		$orderid=$info['orderid'];
		$paytype=$info['paytype'];
		$username=$info['username'];
		
		// 获取游戏
		$where=array('S.id'=>$sourceid);
		$sourceModel = M('tg_source');
		$newsource=$sourceModel->alias('S')
					->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
					->join(C('DB_PREFIX')."all_game AG on G.sdkgameid = AG.id", "LEFT")
					->join(C('DB_PREFIX')."tg_user U on U.userid = S.userid", "LEFT")
					->field('S.sourcesn,AG.id,AG.name,U.account')
					->where($where)
					->find();

		$userModel = M('all_user');
		$condition=array('username'=>$username);
		$user = $userModel->field('agent,username')->where($condition)->find();

		// 代金券表 yx_voucher_info
		$createtime=time();
		if($rechargemoney<1000){
			$endtime=$createtime+7*24*3600; //截止时间为一周
		}else{
			$endtime=$createtime+30*24*3600; //截止时间为一月
		}
		$title='《'.$newsource['name'].'》'.$rechargemoney.'元代金券';//游戏+面额+代金券
		$voucher_info_model = M('voucher_info');
		$voucher_info_data = array(
			'title' => $title, 
			'type' => 1, 
			'game' => $newsource['name'], 
			'gameid' => $newsource['id'], 
			'usecondition' => 1, 
			'money' => $rechargemoney, 
			'discount' => 0, 
			'createtime' => $createtime, 
			'endtime' => $endtime, 
			'restcount' => 0, 
			'count' => 1, 
			'isdisplay' => 1, 
			'usetimes' => $rechargemoney,
		);
		$voucher_info = $voucher_info_model->add($voucher_info_data);

		// 谁为谁购买代金券表 yx_voucher_buy
		if($voucher_info){
			$voucher_buy_model = M('voucher_buy');
			$create_time=time();
			$voucher_buy_data = array( 
				'orderid' => $orderid, 
				'amount' => $paymoney, //充值订单金额，必须大于零
				'username' => $user['username'], 
				'paytype' => $paytype, 
				'productname' => $title, 
				'status' => 1, 
				'create_time' => $create_time, 
				'buyer' =>  $newsource['account'], 
				'voucherid' => $voucher_info, 
				'voucherje' => $rechargemoney, 
				'sourceid' => $sourceid,
			);
			$voucher_buy_result=$voucher_buy_model->add($voucher_buy_data);

			// 用户领取代金券表 yx_voucher_user
			$voucher_user_model = M('voucher_user');
			$voucher_user_data = array(
				'voucherid' => $voucher_info, 
				'username' => $user['username'], 
				'createtime' => time(), 
				'getagent' => $newsource['sourcesn'], 
				'regagent' => $user["agent"], 
				'status' => 0, 
				'usetimes' => $rechargemoney, 
				'restmoney' => $rechargemoney, 
			);
			$voucher_user_result=$voucher_user_model->add($voucher_user_data);
		}

		if ($voucher_user_result) {
            $time = date('Y-m-d H:i:s',time());
            $this->insertLog($_SESSION['account'],'充值代金券', 'VoucherAction.class.php', 'modifyVoucherRecharge', $time, $_SESSION['account']."为用户“".$user['username']."”充值了“".$title."”");
			return $voucher_info;
		} else {
			return false;
		}
	}

	// 支付宝回调之前，生成未支付成功的订单信息
	public function InputOrder($info_outer) {
		// 代金券表 yx_voucher_info
		// 谁为谁购买代金券表 yx_voucher_buy
		$info_inner=array();
		$info=array_merge($info_inner,$info_outer);
		$sourceid=$info['sourceid'];
		$rechargemoney=$info['rechargemoney'];
		$paymoney=$info['paymoney'];
		$orderid=$info['orderid'];
		$paytype=$info['paytype'];
		$username=$info['username'];
		
		// 获取游戏
		$where=array('S.id'=>$sourceid);
		$sourceModel = M('tg_source');
		$newsource=$sourceModel->alias('S')
					->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
					->join(C('DB_PREFIX')."all_game AG on G.sdkgameid = AG.id", "LEFT")
					->join(C('DB_PREFIX')."tg_user U on U.userid = S.userid", "LEFT")
					->field('S.sourcesn,AG.id,AG.name,U.account')
					->where($where)
					->find();

		$userModel = M('all_user');
		$condition=array('username'=>$username);
		$user = $userModel->field('agent,username')->where($condition)->find();

		// 代金券表 yx_voucher_info
		$createtime=time();
		if($rechargemoney<1000){
			$endtime=$createtime+7*24*3600; //截止时间为一周
		}else{
			$endtime=$createtime+30*24*3600; //截止时间为一月
		}
		$title='《'.$newsource['name'].'》'.$rechargemoney.'元代金券';//游戏+面额+代金券
		$voucher_info_model = M('voucher_info');
		$voucher_info_data = array(
			'title' => $title, 
			'type' => 1, 
			'game' => $newsource['name'], 
			'gameid' => $newsource['id'], 
			'usecondition' => 1, 
			'money' => $rechargemoney, 
			'discount' => 0, 
			'createtime' => $createtime, 
			'endtime' => $endtime,
			'restcount' => 0, 
			'count' => 1, 
			'isdisplay' => 1, 
			'usetimes' => $rechargemoney,
		);
		$voucher_info = $voucher_info_model->add($voucher_info_data);

		// 谁为谁购买代金券表 yx_voucher_buy
		if($voucher_info){
			$voucher_buy_model = M('voucher_buy');
			$create_time=time();
			$voucher_buy_data = array( 
				'orderid' => $orderid, 
				'amount' => $paymoney, //充值订单金额，必须大于零
				'username' => $user['username'], 
				'paytype' => $paytype, 
				'productname' => $title, 
				'status' => 0, //未支付
				'create_time' => $create_time, 
				'buyer' =>  $newsource['account'], 
				'voucherid' => $voucher_info, 
				'voucherje' => $rechargemoney, 
				'sourceid' => $sourceid,
			);
			$voucher_buy_result=$voucher_buy_model->add($voucher_buy_data);
		}

		if ($voucher_buy_result) {
			return $voucher_info;
		} else {
			return false;
		}
	}

	// 支付宝回调之后，发放代金券
	public function sendVoucher($info_outer){
		// 用户领取代金券表 yx_voucher_user
		// 日志 yx_tg_log
		$info_inner=array();
		$info=array_merge($info_inner,$info_outer);
		$rechargemoney=$info['rechargemoney'];
		$paymoney=$info['paymoney'];
		$orderid=$info['orderid'];
		$paytype=$info['paytype'];

		// 获取订单信息
		$voucher_buy_model = M('voucher_buy');
		$voucher_buy_where=array('orderid'=>$orderid);
		$old_voucher_info_one=$voucher_buy_model->field('status,sourceid,username,buyer')->where($voucher_buy_where)->find();

		$sourceid=$old_voucher_info_one['sourceid'];
		$username=$old_voucher_info_one['username'];
		$account=$old_voucher_info_one['buyer'];
		
		// 获取游戏
		$where=array('S.id'=>$sourceid);
		$sourceModel = M('tg_source');
		$newsource=$sourceModel->alias('S')
					->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
					->join(C('DB_PREFIX')."all_game AG on G.sdkgameid = AG.id", "LEFT")
					->field('S.sourcesn,AG.id,AG.name')
					->where($where)
					->find();

		$userModel = M('all_user');
		$condition=array('username'=>$username);
		$user = $userModel->field('agent,username')->where($condition)->find();

		$title='《'.$newsource['name'].'》'.$rechargemoney.'元代金券';//游戏+面额+代金券

		if($old_voucher_info_one['status']=='0'){

			// 修改订单状态
			$voucher_buy_data = array('status'=>1);
			$voucher_buy_result=$voucher_buy_model->where($voucher_buy_where)->save($voucher_buy_data);
			$voucher_info_one=$voucher_buy_model->field('voucherid')->where($voucher_buy_where)->find();

			// 用户领取代金券表 yx_voucher_user
			$voucher_user_model = M('voucher_user');
			$voucher_user_data = array(
				'voucherid' => $voucher_info_one['voucherid'], 
				'username' => $user['username'], 
				'createtime' => time(), 
				'getagent' => $newsource['sourcesn'], 
				'regagent' => $user["agent"], 
				'status' => 0, 
				'usetimes' => $rechargemoney, 
				'restmoney' => $rechargemoney, 
			);
			$voucher_user_result=$voucher_user_model->add($voucher_user_data);

			// $time = date('Y-m-d H:i:s',time());
	  //   	$this->insertLog($account,'充值代金券', 'VoucherAction.class.php', 'modifyVoucherRecharge', $time, $account."为用户“".$user['username']."”充值了“".$title."”");
		}
	}

	// 支付宝支付
	public function alipay_pay(){
		$this->logincheck();
		$account=$_SESSION['account'];
		$rechargeuserid = $_SESSION['userid'];

		$userModel = M('all_user');
		$username = $_POST["username"];
		$condition["username"] = $username;
		$condition["email"] = $username;
		$condition["mobile"] = $username;
		$condition['_logic'] = 'OR';
		$user = $userModel->field('agent,username,email,mobile')->where($condition)->find();

		$sourceModel = M('tg_source');
		$sourcecondition["userid"] = $rechargeuserid;
		$sourcecondition["activeflag"] = 1;
		$source = $sourceModel->field('sourcesn')->where($sourcecondition)->select();
		$sourcelist = array();
		foreach ($source as $k => $v) {
			$sourcelist[] = $v["sourcesn"];
		}

		if (!in_array($user["agent"],$sourcelist)) {
			// $this->ajaxReturn('fail','该用户不属于您的推广渠道。',0);
			// exit();
			$this->error('该用户不属于您的推广渠道。');
		} else {
			require_once("../Third/alipay/UTF8/alipay.config.php");
			require_once("../Third/alipay/UTF8/lib/alipay_submit.class.php");

			/**************************请求参数**************************/
			$sourceid = $_POST['sourceid'];
			$rechargemoney = $_POST['rechargemoney'];
			$rechargeway = $_POST['rechargeway'];

			// 获取游戏
			$where=array('S.id'=>$sourceid);
			$newsource=$sourceModel->alias('S')
						->join(C('DB_PREFIX')."tg_game G on G.gameid = S.gameid", "LEFT")
						->join(C('DB_PREFIX')."all_game AG on G.sdkgameid = AG.id", "LEFT")
						->field('S.sourcechannelrate,S.sourcesharerate,S.sourcesn,AG.id,AG.name')
						->where($where)
						->find();

			// 获取资源折扣
			$dicount=1-(1-$newsource['sourcechannelrate'])*$newsource['sourcesharerate'];
			// 实际支付的金额
			$paymoney = round($rechargemoney*$dicount,1);

	        //商户订单号，商户网站订单系统中唯一订单号，必填
	        $out_trade_no = get_orderid();

	        //订单名称，必填
	        $title='《'.$newsource['name'].'》'.$rechargemoney.'元代金券';//游戏+面额+代金券
	        $subject = $title;

	        //付款金额，必填
	        $total_fee = $paymoney;
	        // $total_fee = 0.01;

	        //商品描述，可空
	        $body = $title;
			/************************************************************/

			// 填写订单信息
			$info=array(
				'sourceid'=>$sourceid,
				'rechargemoney'=>$rechargemoney,
				'paymoney'=>$paymoney,
				'username'=>$user['username'],
				'paytype'=>'zfb',
				'orderid'=> $out_trade_no,
			);
			$voucher_info=$this->InputOrder($info);

			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service"       => $alipay_config['service'],
					"partner"       => $alipay_config['partner'],
					"seller_id"  => $alipay_config['seller_id'],
					"payment_type"	=> $alipay_config['payment_type'],
					
					"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
					"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
					"out_trade_no"	=> $out_trade_no,
					"subject"	=> $subject,
					"total_fee"	=> $total_fee,
					"body"	=> $body,
					"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
					//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
			        //如"参数名"=>"参数值"

					'extra_common_param' => $rechargemoney,
			);

			$parameter['return_url']='http://tg.yxgames.com/index.php?m=voucher&a=alipay_return';
			// $parameter['notify_url']='http://tg.yxgames.com/index.php?m=voucher1&a=alipay_notify'; //不能用这种网址
			$parameter['notify_url']='http://tg.yxgames.com/alipay_notify.php';
			
			// $log_content=date('Y-m-d H:i:s')."\n";
			// $log_content.='parameter：'.print_r($parameter,1)."\n";
			// error_log($log_content, 3, 'test.log');

			// [service] => create_direct_pay_by_user
		 //    [partner] => 2088021266234053
		 //    [seller_id] => 2088021266234053
		 //    [payment_type] => 1
		 //    [anti_phishing_key] => 
		 //    [exter_invoke_ip] => 
		 //    [out_trade_no] => 147252314436575
		 //    [subject] => 《武娘联萌》1元代金券
		 //    [total_fee] => 0.01
		 //    [body] => 《武娘联萌》1元代金券
		 //    [_input_charset] => utf-8
		 //    [extra_common_param] => 1643,1,d810658847
		 //    [notify_url] => http://tg.yxgames.com/index.php?m=voucher1&a=alipay_notify

			// $parameter=array(
			// 	'service'=>'create_direct_pay_by_user',
			// 	'partner'=>'2088021266234053',
			// 	'seller_id'=>'2088021266234053',
			// 	'payment_type'=>'1',
			// 	'anti_phishing_key'=>'',
			// 	'exter_invoke_ip'=>'',
			// 	'out_trade_no'=>'147252314436575',
			// 	'total_fee'=>'0.01',
			// 	'body'=>'《武娘联萌》1元代金券',
			// 	'_input_charset'=>'utf-8',
			// 	'extra_common_param'=>'1643,1,d810658847',
			// 	'notify_url'=>'http://tg.yxgames.com/index.php?m=voucher1&a=alipay_notify',
			// );
			
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");

			echo $html_text;
		}
	}

	// 支付宝支付的异步返回
	// public function alipay_notify(){
	// 	require_once("../Third/alipay/UTF8/alipay.config.php");
	// 	require_once("../Third/alipay/UTF8/lib/alipay_notify.class.php");

	// 	//计算得出通知验证结果
	// 	$alipayNotify = new AlipayNotify($alipay_config);
	// 	$verify_result = $alipayNotify->verifyNotify();

	// 	$log_content=date('Y-m-d H:i:s')."\n";
	// 	$log_content.='post：'.print_r($_POST,1)."\n";
	// 	error_log($log_content, 3, 'test.log');

	// 	if($verify_result) {//验证成功
	// 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// 		//请在这里加上商户的业务逻辑程序代

			
	// 		//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		
	// 	    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
	// 		//商户订单号
	// 		$out_trade_no = $_POST['out_trade_no'];

	// 		//支付宝交易号
	// 		$trade_no = $_POST['trade_no'];

	// 		//交易状态
	// 		$trade_status = $_POST['trade_status'];

	// 		$extra_common_param = $_POST['extra_common_param'];
	// 		$total_fee = $_POST['total_fee'];

	// 	    if($_POST['trade_status'] == 'TRADE_FINISHED') {
	// 			//判断该笔订单是否在商户网站中已经做过处理
	// 			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
	// 			//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
	// 			//如果有做过处理，不执行商户的业务程序
						
	// 			//注意：
	// 			//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

	// 	        //调试用，写文本函数记录程序运行情况是否正常
	// 	        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

	// 	        error_log(date('Y-m-d H:i:s')."订单：交易结束\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
	// 	    } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
	// 			//判断该笔订单是否在商户网站中已经做过处理
	// 			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
	// 			//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
	// 			//如果有做过处理，不执行商户的业务程序
						
	// 			//注意：
	// 			//付款完成后，支付宝系统发送该交易状态通知

	// 	        //调试用，写文本函数记录程序运行情况是否正常
	// 	        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

	//         	$info=array(
	// 				'rechargemoney'=>$extra_common_param,
	// 				'paymoney'=>$total_fee,
	// 				'paytype'=>'zfb',
	// 				'orderid'=>$out_trade_no,
	// 			);
	// 			$this->sendVoucher($info);
	// 			error_log(date('Y-m-d H:i:s')."订单：付款成功，给了玩家相应的代金券\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
	// 	    }else{
	// 	    	error_log(date('Y-m-d H:i:s')."订单：".$_POST['trade_status']."\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
	// 	    }

	// 		//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		   
	// 		echo "success";		//请不要修改或删除
			
	// 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// 	}else {
	// 	    //验证失败
	// 	    error_log(date('Y-m-d H:i:s')."支付宝验证失败\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
	// 	    echo "fail";

	// 	    //调试用，写文本函数记录程序运行情况是否正常
	// 	    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
	// 	}
	// }

	// 支付宝支付的同步返回
	public function alipay_return(){
		require_once("../Third/alipay/UTF8/alipay.config.php");
		require_once("../Third/alipay/UTF8/lib/alipay_notify.class.php");

		// $alipay_config=array(
		//     'discount' => '0.00',
		//     'extra_common_param' => '878,1,d810658847',
		//     'payment_type' => '1',
		//     'subject' => '《数码世界OL》1元代金券',
		//     'trade_no' => '2016070821001004440225451534',
		//     'buyer_email' => '1149874672@qq.com',
		//     'gmt_create' => '2016-07-08 18:43:30',
		//     'notify_type' => 'trade_status_sync',
		//     'quantity' => '1',
		//     'out_trade_no' => '146797460443426',
		//     'seller_id' => '2088021266234053',
		//     'notify_time' => '2016-07-08 18:47:09',
		//     'body' => '《数码世界OL》1元代金券',
		//     'trade_status' => 'TRADE_SUCCESS',
		//     'is_total_fee_adjust' => 'N',
		//     'total_fee' => '0.01',
		//     'gmt_payment' => '2016-07-08 18:43:36',
		//     'seller_email' => '867672678@qq.com',
		//     'price' => '0.01',
		//     'buyer_id' => '2088702379530442',
		//     'notify_id' => '7654de8ce942235d43a6fadfe02479cjea',
		//     'use_coupon' => 'N',
		//     'sign_type' => 'MD5',
		//     'sign' => '05f67c6357017f6e4f1a940bbf0baefe',
		// );
		// $_GET['out_trade_no'] = $alipay_config['out_trade_no'];
		// $_GET['trade_no'] = $alipay_config['trade_no'];
		// $_GET['trade_status'] = $alipay_config['trade_status'];
		// $_GET['extra_common_param'] = $alipay_config['extra_common_param'];
		// $_GET['total_fee'] = $alipay_config['total_fee'];


		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

			//商户订单号

			$out_trade_no = $_GET['out_trade_no'];

			//支付宝交易号

			$trade_no = $_GET['trade_no'];

			//交易状态
			$trade_status = $_GET['trade_status'];

			$extra_common_param = $_GET['extra_common_param'];
			$total_fee = $_GET['total_fee'];

			if($_GET['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
				error_log(date('Y-m-d H:i:s')."订单：交易结束\n".print_r($_GET,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
			} else if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
		      	$info=array(
					'rechargemoney'=>$extra_common_param,
					'paymoney'=>$total_fee,
					'paytype'=>'zfb',
					'orderid'=>$out_trade_no,
				);
				$this->sendVoucher($info);

				error_log(date('Y-m-d H:i:s')."订单：付款成功，给了玩家相应的代金券\n".print_r($_GET,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
		    }else{
		    	error_log(date('Y-m-d H:i:s')."订单：".$_GET['trade_status']."\n".print_r($_GET,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
		    	echo "trade_status=".$_GET['trade_status'];
		    }
				
			// echo "验证成功<br />";
			$this->redirect('http://tg.yxgames.com/voucher/');

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			//////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    error_log(date('Y-m-d H:i:s')."支付宝验证失败\n".print_r($_GET,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
		    echo "验证失败";
		}
	}
}
?>