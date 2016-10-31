<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once("../Third/alipay/UTF8/alipay.config.php");
require_once("../Third/alipay/UTF8/lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
$verify_result = 1;

// $log_content=date('Y-m-d H:i:s')."\n";
// $log_content.='post：'.print_r($_POST,1)."\n";
// error_log($log_content, 3, 'test.log');

// $_POST=array(
//     'discount' => '0.00',
//     'extra_common_param' => '1612,1,d810658847',
//     'payment_type' => '1',
//     'subject' => '《巅峰战舰》1元代金券',
//     'trade_no' => '2016083021001004050216946793',
//     'buyer_email' => 'service@youxia-inc.com',
//     'gmt_create' => '2016-08-30 11:52:19',
//     'notify_type' => 'trade_status_sync',
//     'quantity' => '1',
//     'out_trade_no' => '147252902370019',
//     'seller_id' => '2088021266234053',
//     'notify_time' => '2016-08-30 13:17:10',
//     'body' => '《巅峰战舰》1元代金券',
//     'trade_status' => 'TRADE_SUCCESS',
//     'is_total_fee_adjust' => 'N',
//     'total_fee' => '0.01',
//     'gmt_payment' => '2016-08-30 11:52:31',
//     'seller_email' => '867672678@qq.com',
//     'price' => '0.01',
//     'buyer_id' => '2088122604012057',
//     'notify_id' => '7fdee61992b11387ff862ad902d3efbgdy',
//     'use_coupon' => 'N',
//     'sign_type' => 'MD5',
//     'sign' => 'acd9fc73bb42febf22818ab487ffc171',
// );

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//商户订单号
	$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号
	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];

	$extra_common_param = $_POST['extra_common_param'];
	$total_fee = $_POST['total_fee'];

    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
		//如果有做过处理，不执行商户的业务程序
				
		//注意：
		//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

        error_log(date('Y-m-d H:i:s')."订单：交易结束\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
    } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
		//如果有做过处理，不执行商户的业务程序
				
		//注意：
		//付款完成后，支付宝系统发送该交易状态通知

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

    	$SDKdbhost = '10.13.58.56';
		$SDKdbuser = 'User_youxia_tg';
		$SDKdbpw   = 'YOUxiadb@2016';
		$SDKdbname = 'db_youxia_new';

        mysql_connect($SDKdbhost,$SDKdbuser,$SDKdbpw);
		mysql_select_db($SDKdbname);
		mysql_query("set names utf8;");

    	$info=array(
			'rechargemoney'=>$extra_common_param,
			'paymoney'=>$total_fee,
			'paytype'=>'zfb',
			'orderid'=>$out_trade_no,
		);

		// 用户领取代金券表 yx_voucher_user
		// 日志 yx_tg_log
		$rechargemoney=$info['rechargemoney'];
		$paymoney=$info['paymoney'];
		$orderid=$info['orderid'];
		$paytype=$info['paytype'];
		
		// 获取订单信息
		$old_voucher_info_query = "select status,voucherid,username,buyer,sourceid from yx_voucher_buy where orderid='".$orderid."' limit 1 ";
		$old_voucher_info_result = mysql_query($old_voucher_info_query);
		$old_voucher_info_one = mysql_fetch_assoc($old_voucher_info_result);

		$sourceid=$old_voucher_info_one['sourceid'];
		$username=$old_voucher_info_one['username'];
		$account=$old_voucher_info_one['buyer'];

		// 获取游戏
		$newsource_query = "select 
									S.sourcesn,AG.name 
							from yx_tg_source S
							left join yx_tg_game G on G.gameid = S.gameid
							left join yx_all_game AG on G.sdkgameid = AG.id
							where S.id='".$sourceid."' 
							limit 1 ";
		$newsource_result = mysql_query($newsource_query);
		$newsource = mysql_fetch_assoc($newsource_result);

		// 获取活动代金券的人的信息
		$user_query = "select agent,username from yx_all_user where username='".$username."' limit 1 ";
		$user_result = mysql_query($user_query);
		$user = mysql_fetch_assoc($user_result);

		$title='《'.$newsource['name'].'》'.$rechargemoney.'元代金券';//游戏+面额+代金券


		if($old_voucher_info_one['status']=='0'){
			// 修改订单状态
			$voucher_info_query = "update yx_voucher_buy set status=1 where orderid='".$orderid."'";
			$voucher_info_query_result = mysql_query($voucher_info_query);


			// 用户领取代金券表 yx_voucher_user
			$voucher_info_query = "insert into yx_voucher_user (
												voucherid,
												username,
												createtime,
												getagent,
												regagent,
												status,
												usetimes,
												restmoney
									) values (
										     '".$old_voucher_info_one['voucherid']."',	
										     '".$user['username']."',	
										     '".time()."',	
										     '".$newsource['sourcesn']."',	
										     '".$user['agent']."',	
										     '0',	
										     '".$rechargemoney."',	
										     '".$rechargemoney."'
									)";
			$voucher_info_query_result = mysql_query($voucher_info_query);

			// 插入日志
			$time = date('Y-m-d H:i:s',time());
	        $log_query = "insert into yx_tg_log (
												username,
												type,
												class,
												function,
												createtime,
												content
									) values (
										     '".$account."',	
										     '充值代金券',	
										     'alipay_notify.php',	
										     'alipay_notify.php',	
										     '".$time."',
										     '".$account."为用户“".$user['username']."”充值了“".$title."”'
									)";
			$log_result = mysql_query($log_query);
		}

		error_log(date('Y-m-d H:i:s')."订单：付款成功，给了玩家相应的代金券\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
    }else{
    	error_log(date('Y-m-d H:i:s')."订单：".$_POST['trade_status']."\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
    }

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
   
	echo "success";		//请不要修改或删除
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}else {
    //验证失败
    error_log(date('Y-m-d H:i:s')."支付宝验证失败\n".print_r($_POST,1)."\n", 3, 'log/alipay/'.date('Y-m-d').'.log');
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}

// header("location:index.php?m=voucher1&a=alipay_notify"); 
?>
