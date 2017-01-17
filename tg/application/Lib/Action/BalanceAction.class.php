<?php
class BalanceAction extends CommonAction {
    public function __construct(){
        parent::__construct();

        // 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }
    }

    public function index(){
        $this->logincheck();
        $Index = D('Balance');
        $balance = $Index->balance();

       	$userModel= M('tg_user');
       	$userid = $_SESSION['userid'];
		$user = $userModel->field('usertype,invoicetype')->find($userid);
		
		$this->assign('user',$user);
		$this->assign('balance', $balance);
        $this->assign('money',$Index->money());

        $this->display();
    }

    //结算单详情
    public function balancedetail(){
		$this->logincheck();
		$id = $_GET['balanceid'];
		if ($id == 0) {
			Header("Location: /balanceall/ ");
			exit();
		}

		$balance['id'] = $id;
		$this->assign('balance',$balance);

        $sourceaccountModel= M('tg_sourceaccount');
        $condition['SA.balanceid'] = $id;
        $condition["SA.activeflag"] = 1;
        $sourceaccount = $sourceaccountModel->alias("SA")
        			->join(C('DB_PREFIX')."tg_game G on SA.gameid = G.gameid", "LEFT")
        			->join(C('DB_PREFIX')."tg_channel C on SA.channelid = C.channelid", "LEFT")
        			->join(C('DB_PREFIX')."tg_source S on SA.sourceid = S.id", "LEFT")
        			->where($condition)
        			->order("SA.createtime desc")
        			->field("SA.sourceid,SA.sharerate,SA.channelrate,SA.sourceincome,SA.sourcejournal,SA.actualpaid,G.gameicon,G.gamename,C.channelname")
        			->select();
		foreach($sourceaccount as $k => $v){
            $sourceaccount[$k]['sourceincome'] = str_replace(",", "", number_format($v['sourceincome'], 2));
			$sourceaccount[$k]['sourcejournal'] = str_replace(",", "", number_format($v['sourcejournal'], 2));
		}
        $this->assign('source',$sourceaccount);

        $this->display();
    }

    //账目明细(每个资源的每日详情)
    public function accountdetail(){
		$this->logincheck();
		$balanceid = $_GET['balanceid'];
		$sourceid = $_GET['sourceid'];
		if ($balanceid == 0 || $sourceid == 0) {
			Header("Location: /balanceall/ ");
			exit();
		}
		$sourceaccountModel= M('tg_sourceaccount');
        $condition['SA.balanceid'] = $balanceid;
		$condition['SA.sourceid'] = $sourceid;
        $condition["SA.activeflag"] = 1;
		$balance = $sourceaccountModel->alias("SA")->join(C('DB_PREFIX')."tg_balance B on SA.balanceid = B.id", "LEFT")->where($condition)->order("SA.createtime desc")->find();
        $dailyaccountModel= M('tg_dailyaccount');
		$dailycondition["date"]  = array(array('egt',$balance["startdate"]),array('elt',$balance["enddate"]),'and');
		$dailycondition["sourceid"]  = $sourceid;
        $dailycondition["activeflag"] = 1;
        $dailyaccount = $dailyaccountModel->where($dailycondition)->order("createtime desc")->select();
        $this->assign('daily',$dailyaccount);

        $this->display();
    }

    public function withdraw(){
        $this->logincheck();
		$userid = $_SESSION['userid'];
        $Index = D('Balance');
        $this->assign('money',$Index->money());
        $this->assign('bank',$Index->bank());
        $this->assign('alipay',$Index->alipay());
		$balancemodel = M('tg_balance');
		$map['userid'] = $userid;
		$map['activeflag'] = 1;
		$balance = $balancemodel->where($map)->order("id desc")->select();
		$sourcemodel = M('tg_source');
		$sourcecondition["userid"] = $userid;
		$sourcecondition["activeflag"] = 1;
		$source = $sourcemodel->where($sourcecondition)->order("id asc")->select();
		if ($balance) {
			$lastbalance = $balance[0];
			$startdate = date("Y-m-d",strtotime($lastbalance["enddate"]." 12:00:00"."+1 day"));
		} else {
			if ($source) {
				$startdate = date("Y-m-d",strtotime($source[0]["createtime"]));
			} else {
				$startdate = date("Y-m-d", strtotime("-1 day"));
			}
		}
		$this->assign('startdate',$startdate);
		$this->assign('enddate',date("Y-m-d", strtotime("-1 day")));
		$usermodel = M('tg_user');
		$user = $usermodel->find($userid);
		$this->assign('user',$user);
        $this->display();
    }

    //查看指定日期数据
    public function viewDaterangeBalance(){
        $this->logincheck();
        $userid= $_SESSION['userid'];
        $startdate = $_POST['startdate']." 00:00:00";
        $enddate = $_POST['enddate']." 23:59:59";
        $model= M('tg_balance');
        $condition["applytime"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        $condition["activeflag"] = 1;
        $condition['_logic'] = 'AND';
        $condition["userid"] = $userid;
        $balance = $model->where($condition)->order("createtime desc")->select();
        $result = array();
        foreach($balance as $k =>$v){
            $result[$k]['id'] = $balance[$k]['id'];
            $result[$k]['applytime'] = date("Y年m月d日",strtotime($balance[$k]['applytime']));
            $result[$k]['circletime'] = $balance[$k]['startdate']." ~ ".$balance[$k]['enddate'];
            $result[$k]['totalamount'] = $balance[$k]['totalamount'];
            $result[$k]['taxrate'] = $balance[$k]['taxrate'];
            $result[$k]['paidamount'] = $balance[$k]['paidamount'];
            if($balance[$k]['iserror'] == 0){
                $result[$k]['iserror'] = '无误';
            } else{
                $result[$k]['iserror'] = '有误';
            }

            if($balance[$k]['balancestatus'] == 1){
                $result[$k]['balancestatus'] = '申请中';
            } else if($balance[$k]['balancestatus'] == 2){
                $result[$k]['balancestatus'] = '已结算';
            } else{
                $result[$k]['balancestatus'] = '未知';
            }
        }

        if($balance){
            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
    }

    //本月 七天 三十天查询
    public function refresh(){
        $this->logincheck();
        $userid= $_SESSION['userid'];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];
        $model = M("tg_balance");
        $condition["userid"] = $userid;

        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $startdate = $_POST['startdate']." 00:00:00";
            $enddate = $_POST['enddate']." 23:59:59";
            $condition["applytime"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }
        $condition["activeflag"] = 1;
        $condition['_logic'] = 'AND';
        $balance = $model->where($condition)->order("createtime desc")->select();
        $result = array();
        foreach($balance as $k =>$v){
            $result[$k]['id'] = $balance[$k]['id'];
            $result[$k]['applytime'] = date("Y年m月d日",strtotime($balance[$k]['applytime']));
            $result[$k]['circletime'] = $balance[$k]['startdate']." ~ ".$balance[$k]['enddate'];
            $result[$k]['totalamount'] = $balance[$k]['totalamount'];
            $result[$k]['taxrate'] = $balance[$k]['taxrate'];
            $result[$k]['paidamount'] = $balance[$k]['paidamount'];
            if($balance[$k]['iserror'] == 0){
                $result[$k]['iserror'] = '无误';
            } else{
                $result[$k]['iserror'] = '有误';
            }

            if($balance[$k]['balancestatus'] == 1){
                $result[$k]['balancestatus'] = '申请中';
            } else if($balance[$k]['balancestatus'] == 2){
                $result[$k]['balancestatus'] = '已结算';
            } else{
                $result[$k]['balancestatus'] = '未知';
            }
        }
        if ($balance) {
            $this->ajaxReturn($result,'success',1);
            exit();
        } else {
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
    }

    //提现
    public function dowithdraw(){
		$this->logincheck();
        $userid = $_SESSION['userid'];
		$usermodel = M('tg_user');
		$user = $usermodel->find($userid);
		if ($user["usertype"] == 0 || $user["usertype"] == '') {
			$this->ajaxReturn('fail','请先完善个人信息。',0);
            exit();
		}
		$password = $_POST['password'];
		if(sha1($password) == $user['password']){
			// 结算表 tg_balance
			// 结算周期统计表 tg_sourceaccount
			// 日志表 tg_log

			// 服务器端限定多次提交
			$balanceModel = M('tg_balance');
			$where=array(
				'userid'=>$userid,
			);
			$balance_one = $balanceModel->field('createtime,enddate')->where($where)->order('createtime desc')->find();
			if( ( time()-strtotime($balance_one['createtime']) )<10){
				$this->ajaxReturn('fail','请稍后再提交。',0);
            	exit();
			}

			// 如果提交的开始时间 小于 上次订单的截止日期，说明结算单已经提交过，错误结算单
			$startdate = $_POST["start"];
			if( strtotime($startdate) <= strtotime($balance_one['enddate']) ){
				$this->ajaxReturn('fail','该结算单已提交，进入结算中心查看',0);
            	exit();
			}

			$enddate = $_POST["end"];
			$data["startdate"] = $startdate;
			$data["enddate"] = $enddate;
			$data["userid"] = $userid;
			$data["accounttype"] = $_POST["type"];
			$data["aliaccountid"] = 0;
			$data["bankaccountid"] = 0;
			if ($_POST["type"] == 1) {
				$data["aliaccountid"] = $_POST["accountid"];
                $map1['id'] = $_POST["accountid"];
                $data1['isused'] = 1;
                $alipaymodel = M('tg_aliaccount');
                $alipay = $alipaymodel->where($map1)->save($data1);
				$inc = $alipaymodel->where($map1)->setInc('usedtimes',1);
			} else if ($_POST["type"] == 2) {
				$data["bankaccountid"] = $_POST["accountid"];
                $map2['id'] = $_POST["accountid"];
                $data2['isused'] = 1;
                $bankmodel = M('tg_bankaccount');
                $bank = $bankmodel->where($map2)->save($data2);
				$inc = $bankmodel->where($map2)->setInc('usedtimes',1);
			}
			$data["applytime"] = date("Y-m-d H:i:s");
			$dailymodel = M('tg_dailyaccount');
			$dailycondition["date"] = array(array('EGT',$startdate),array('ELT',$enddate),'AND');
			$dailycondition["userid"] = $userid;
			$dailycondition["activeflag"] = 1;
			$daily = $dailymodel->where($dailycondition)->order("id desc")->select();
			$unwithdraw = 0;
			if ($daily) {
				foreach ($daily as $k => $v) {
					if ($v["activeflag"] == 1) {
						$unwithdraw += $v["dailyincome"];
					}
				}
			}
			$data["totalamount"] = $unwithdraw;
			$taxrate = 0;
			// 结算为游戏币不扣税
			if($_POST["type"] != 3){
				// 普通发票扣0.0672；3%扣0.0336；6%不扣，普通用户扣3%
				if ($user["invoicetype"] == 1) {
					$taxrate = 0.0672;
				} else if ($user["invoicetype"] == 2) {
					$taxrate = 0.0336;
				} else if ($user["invoicetype"] == 3) {
					$taxrate = 0;
				} else if ($user["invoicetype"] == 0 && $user['usertype'] == 2) {
					$taxrate = 0.0672;
				} else if ($user["invoicetype"] == 0 && $user['usertype'] == 1) {
					$taxrate = 0.03;
				}
			}
			$data["taxrate"] = $taxrate;
			$data["actualamount"] = round(($unwithdraw * (1 - $taxrate)),2); //四舍五入
			$data["paidamount"] = 0;
			$data["balancestatus"] = 1;
			$data["iserror"] = 0;
			$data['activeflag'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s');
			$data['createuser'] = $user["realname"];
			$data['updatetime'] = date('Y-m-d H:i:s');
			$data['updateuser'] = $user["realname"];

			// 如果结算为游侠币，20000钱以为不需要审核
			if($_POST["type"] == 3 && $data["actualamount"]<=20000){
	            // 充值记录表 yx_tg_coinlog
	            // 推广用户表 用户tg_user
	            $getcoin = round($data["actualamount"]*10);
	            $coinlog_data=array(
	                'userid' => $userid, 
	                'preauthuser' => 0, 
	                'preauthusername' => 'system', 
	                'amount' => $getcoin, 
	                'activeflag' => 1, 
	                'createtime' => date('Y-m-d H:i:s'), 
	                'createuser' => 'system', 
	                'beizhu' => '结算转入（系统自动）', 
	            );
	            $coinlog_model=M('tg_coinlog');
	            $coinlog_model->add($coinlog_data);

	            $user_model=M('tg_user');
	            $where = array('userid' =>$userid);
	            $user_model->where($where)->setInc('coinpreauth',$getcoin);

	            $data["paidamount"] = $data["actualamount"];
	            $data["balancestatus"] = 2;
			}

			$newbalance = $balanceModel->add($data);
			if ($newbalance) {
				// 新增加 每个结算周期的资源数据统计表 数据
				$sourceaccount = array();

				$sourcemodel = M('tg_source');
				$sourcecondition["userid"] = $userid;
				$sourcecondition["activeflag"] = 1;
				$source = $sourcemodel->where($sourcecondition)->order("id asc")->select();

				if ($daily) {
					if ($source) {
						foreach($source as $k =>$v){
							$sourceaccount[$k]["balanceid"] = $newbalance;
							$sourceaccount[$k]["sourceid"] = $v["id"];
							$sourceaccount[$k]["userid"] = $userid;
							$sourceaccount[$k]["channelid"] = $v["channelid"];
							$sourceaccount[$k]["gameid"] = $v["gameid"];

							$sourcejournal = 0;
							$sourceactive = 0;
							$newpeople = 0;
							$paypeople = 0;
							$payrate = 0;
							$sourceincome = 0;
							$final_sharerate=array();
							$final_channelrate=array();
							foreach($daily as $dk =>$dv){
								if ($dv["sourceid"] == $v["id"]) {
									$sourcejournal += $dv["dailyjournal"];
									$sourceactive += $dv["dailyactive"];
									$newpeople += $dv["newpeople"];
									$paypeople += $dv["paypeople"];
									$sourceincome += $dv["dailyincome"];
									if(!in_array($dv["sharerate"],$final_sharerate)){
										$final_sharerate[]=$dv["sharerate"];
									}
									if(!in_array($dv["channelrate"],$final_channelrate)){
										$final_channelrate[]=$dv["channelrate"];
									}
								}
							}
							// 计算当前所有每日统计的分成比例
							$sourceaccount[$k]["sharerate"] = implode('/',$final_sharerate);
							$sourceaccount[$k]["channelrate"] = implode('/',$final_channelrate);

							if ($sourceactive != 0) {
								$payrate = ceil($paypeople * 100 / $sourceactive);
							}
							$sourceaccount[$k]["sourcejournal"] = $sourcejournal;
							$sourceaccount[$k]["sourceactive"] = $sourceactive;
							$sourceaccount[$k]["newpeople"] = $newpeople;
							$sourceaccount[$k]["paypeople"] = $paypeople;
							$sourceaccount[$k]["payrate"] = $payrate;
							$sourceincome = str_replace(",", "", number_format($sourceincome, 2));
							$sourceaccount[$k]["sourceincome"] = $sourceincome;
							$sourceaccount[$k]["actualpaid"] = 0;
							$sourceaccount[$k]['activeflag'] = 1;
							$sourceaccount[$k]['createtime'] = date('Y-m-d H:i:s');
							$sourceaccount[$k]['createuser'] = $user["realname"];
						}
					}
				} else {
					if ($source) {
						foreach($source as $k =>$v){
							$sourceaccount[$k]["balanceid"] = $newbalance;
							$sourceaccount[$k]["sourceid"] = $v["id"];
							$sourceaccount[$k]["userid"] = $userid;
							$sourceaccount[$k]["channelid"] = $v["channelid"];
							$sourceaccount[$k]["gameid"] = $v["gameid"];
							$sourceaccount[$k]["channelrate"] = $v["sourcechannelrate"];
							$sourceaccount[$k]["sharerate"] = $v["sourcesharerate"];
							$sourcejournal = 0;
							$sourceactive = 0;
							$newpeople = 0;
							$paypeople = 0;
							$payrate = 0;
							$sourceincome = 0;
							$sourceaccount[$k]["sourcejournal"] = $sourcejournal;
							$sourceaccount[$k]["sourceactive"] = $sourceactive;
							$sourceaccount[$k]["newpeople"] = $newpeople;
							$sourceaccount[$k]["paypeople"] = $paypeople;
							$sourceaccount[$k]["payrate"] = $payrate;
							$sourceaccount[$k]["sourceincome"] = $sourceincome;
							$sourceaccount[$k]["actualpaid"] = 0;
							$sourceaccount[$k]['activeflag'] = 1;
							$sourceaccount[$k]['createtime'] = date('Y-m-d H:i:s');
							$sourceaccount[$k]['createuser'] = $user["realname"];
						}
					}
				}
				$sourceaccountmodel = M('tg_sourceaccount');
				$result = $sourceaccountmodel->addAll($sourceaccount);
				if ($result) {
                    $time = date('Y-m-d H:i:s',time());
                    $where=array('id'=>$newbalance);
                    $oldbalance = $balanceModel->where($where)->find();
                    if($oldbalance['accounttype'] == 1){
                        $oldbalance['accounttypename'] = "支付宝";
                    }elseif($oldbalance['accounttype'] == 2){
                        $oldbalance['accounttypename'] = "银行卡";
                    }elseif($oldbalance['accounttype'] == 3){
                        $oldbalance['accounttypename'] = "游侠币";
                    }
                    $this->insertLog($_SESSION['account'],'提现申请', 'BalanceAction.class.php', 'dowithdraw', $time, $_SESSION['account']."申请提现，提现周期为：“".$oldbalance['startdate']." ~ ".$oldbalance['enddate']."”提现金额为：“".$oldbalance['totalamount']."”，提现方式为“".$oldbalance['accounttypename']."”");
                    $this->ajaxReturn('success','成功。',1);
					exit();
				} else {
					$this->ajaxReturn('fail','生成账单失败。',0);
					exit();
				}
			} else {
				$this->ajaxReturn('fail','系统错误。',0);
				exit();
			}
        }else{
            $this->ajaxReturn('fail','登录密码错误。',0);
            exit();
        }
    }

    public function reDoWithdraw(){
		$this->logincheck();

        $userid = $_SESSION['userid'];
        $balanceid=$_POST['balanceid'];
    
    	// 删除以前的结算单统计
    	$sourceaccountModel = M('tg_sourceaccount');
    	$sourceaccountModel->where('balanceid='.$balanceid)->delete();

    	//提取以前结算单数据
    	$balanceModel = M('tg_balance');
    	$where=array('id'=>$balanceid);
        $oldbalance = $balanceModel->where($where)->find();
        $startdate=$oldbalance['startdate'];
        $enddate=$oldbalance['enddate'];

    	// 对结算单进行编辑
		// 结算表 tg_balance
		// 结算周期统计表 tg_sourceaccount
		// 日志表 tg_log

    	// 对于 totalamount 字段计算
		$dailymodel = M('tg_dailyaccount');
		$dailycondition["date"] = array(array('EGT',$startdate),array('ELT',$enddate),'AND');
		$dailycondition["userid"] = $userid;
		$dailycondition["activeflag"] = 1;
		$daily = $dailymodel->where($dailycondition)->order("id desc")->select();
		$unwithdraw = 0;
		if ($daily) {
			foreach ($daily as $k => $v) {
				if ($v["activeflag"] == 1) {
					$unwithdraw += $v["dailyincome"];
				}
			}
		}
		$data["totalamount"] = $unwithdraw;

		$taxrate = 0;
		$usermodel = M('tg_user');
		$user = $usermodel->field('invoicetype,realname')->find($userid);
		if ($user["invoicetype"] == 1) {
			$taxrate = 0.0336;
		} else if ($user["invoicetype"] == 2) {
			$taxrate = 0.0672;
		} else if ($user["invoicetype"] == 3) {
			$taxrate = 0;
		} else if ($user["invoicetype"] == 0) {
			$taxrate = 0;
		}
		$data["taxrate"] = $taxrate;

		$data["actualamount"] = round(($unwithdraw * (1 - $taxrate)),2); //四舍五入
		$data["paidamount"] = 0;
		$data["balancestatus"] = 1;
		$data["iserror"] = 0;
		$data['activeflag'] = 1;
		$data['updatetime'] = date('Y-m-d H:i:s');
		$data['updateuser'] = $user["realname"];
		$data['beizhu'] = '';
		$newbalance = $balanceModel->where('id='.$balanceid)->save($data);
		if ($newbalance) {
			// 新增加 每个结算周期的资源数据统计表 数据
			$sourceaccount = array();

			$sourceModel = M('tg_source');
			$sourcecondition["userid"] = $userid;
			$sourcecondition["activeflag"] = 1;
			$source = $sourceModel->where($sourcecondition)->order("id asc")->select();

			if ($daily) {
				if ($source) {
					foreach($source as $k =>$v){
						$sourceaccount[$k]["balanceid"] = $balanceid;
						$sourceaccount[$k]["sourceid"] = $v["id"];
						$sourceaccount[$k]["userid"] = $userid;
						$sourceaccount[$k]["channelid"] = $v["channelid"];
						$sourceaccount[$k]["gameid"] = $v["gameid"];

						$sourcejournal = 0;
						$sourceactive = 0;
						$newpeople = 0;
						$paypeople = 0;
						$payrate = 0;
						$sourceincome = 0;
						$final_sharerate=array();
						$final_channelrate=array();
						foreach($daily as $dk =>$dv){
							if ($dv["sourceid"] == $v["id"]) {
								$sourcejournal += $dv["dailyjournal"];
								$sourceactive += $dv["dailyactive"];
								$newpeople += $dv["newpeople"];
								$paypeople += $dv["paypeople"];
								$sourceincome += $dv["dailyincome"];
								if(!in_array($dv["sharerate"],$final_sharerate)){
									$final_sharerate[]=$dv["sharerate"];
								}
								if(!in_array($dv["channelrate"],$final_channelrate)){
									$final_channelrate[]=$dv["channelrate"];
								}
							}
						}
						// 计算当前所有每日统计的分成比例
						$sourceaccount[$k]["sharerate"] = implode('/',$final_sharerate);
						$sourceaccount[$k]["channelrate"] = implode('/',$final_channelrate);

						if ($sourceactive != 0) {
							$payrate = ceil($paypeople * 100 / $sourceactive);
						}
						$sourceaccount[$k]["sourcejournal"] = $sourcejournal;
						$sourceaccount[$k]["sourceactive"] = $sourceactive;
						$sourceaccount[$k]["newpeople"] = $newpeople;
						$sourceaccount[$k]["paypeople"] = $paypeople;
						$sourceaccount[$k]["payrate"] = $payrate;
						$sourceincome = str_replace(",", "", number_format($sourceincome, 2));
						$sourceaccount[$k]["sourceincome"] = $sourceincome;
						$sourceaccount[$k]["actualpaid"] = 0;
						$sourceaccount[$k]['activeflag'] = 1;
						$sourceaccount[$k]['createtime'] = date('Y-m-d H:i:s');
						$sourceaccount[$k]['createuser'] = $user["realname"];
					}
				}
			} else {
				if ($source) {
					foreach($source as $k =>$v){
						$sourceaccount[$k]["balanceid"] = $balanceid;
						$sourceaccount[$k]["sourceid"] = $v["id"];
						$sourceaccount[$k]["userid"] = $userid;
						$sourceaccount[$k]["channelid"] = $v["channelid"];
						$sourceaccount[$k]["gameid"] = $v["gameid"];
						$sourceaccount[$k]["channelrate"] = $v["sourcechannelrate"];
						$sourceaccount[$k]["sharerate"] = $v["sourcesharerate"];
						$sourcejournal = 0;
						$sourceactive = 0;
						$newpeople = 0;
						$paypeople = 0;
						$payrate = 0;
						$sourceincome = 0;
						$sourceaccount[$k]["sourcejournal"] = $sourcejournal;
						$sourceaccount[$k]["sourceactive"] = $sourceactive;
						$sourceaccount[$k]["newpeople"] = $newpeople;
						$sourceaccount[$k]["paypeople"] = $paypeople;
						$sourceaccount[$k]["payrate"] = $payrate;
						$sourceaccount[$k]["sourceincome"] = $sourceincome;
						$sourceaccount[$k]["actualpaid"] = 0;
						$sourceaccount[$k]['activeflag'] = 1;
						$sourceaccount[$k]['createtime'] = date('Y-m-d H:i:s');
						$sourceaccount[$k]['createuser'] = $user["realname"];
					}
				}
			}
			$result = $sourceaccountModel->addAll($sourceaccount);
			if ($result) {
                $time = date('Y-m-d H:i:s',time());
                if($oldbalance['accounttype'] == 1){
		            $oldbalance['accounttypename'] = "支付宝";
		        }elseif($oldbalance['accounttype'] == 2){
		            $oldbalance['accounttypename'] = "银行卡";
		        }elseif($oldbalance['accounttype'] == 3){
		            $oldbalance['accounttypename'] = "游侠币";
		        }
                $this->insertLog($_SESSION['account'],'重新提现申请', 'BalanceAction.class.php', 'reDoWithdraw', $time, $_SESSION['account']."申请提现，提现周期为：“".$oldbalance['startdate']." ~ ".$oldbalance['enddate']."”提现金额为：“".$unwithdraw."”，提现方式为“".$oldbalance['accounttypename']."”");
                $this->ajaxReturn($unwithdraw,'success',1);
				exit();
			} else {
				$this->ajaxReturn('fail','生成账单失败。',0);
				exit();
			}
		} else {
			$this->ajaxReturn('fail','系统错误。',0);
			exit();
		}
    }

	public function getUnwithdraw(){
		$userid = $_SESSION['userid'];
        $dailymodel = M('tg_dailyaccount');
		$startdate = $_POST["start"];
		$enddate = $_POST["end"];
		$dailycondition["date"] = array(array('EGT',$startdate),array('ELT',$enddate),'AND');
		$dailycondition["userid"] = $userid;
		$dailycondition["activeflag"] = 1;
		$daily = $dailymodel->where($dailycondition)->order("id desc")->select();
		$unwithdraw = 0;
		if ($daily) {
			foreach ($daily as $k => $v) {
				if ($v["activeflag"] == 1) {
					$unwithdraw += $v["dailyincome"];
				}
			}
			$unwithdraw = str_replace(",", "", number_format($unwithdraw, 2));
		}
        $this->ajaxReturn('success',$unwithdraw,1);
		exit();
    }

}
?>