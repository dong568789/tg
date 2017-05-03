<?php
class BalanceAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    //所有结算单显示
    public function balanceall(){
		$this->logincheck();
        $this->authoritycheck(10120);
        if($this->authoritycheck(10120) == 'ok'){
           /* $model= M('tg_balance');
            $condition["B.activeflag"] = 1;
            $balance = $model->alias("B")->join(C('DB_PREFIX')."tg_user U on B.userid = U.userid", "LEFT")->where($condition)->order("B.createtime desc")->select();
            foreach($balance as $k => $v){
                if($v['balancestatus'] == 1){
                    $balance[$k]['balancestatus'] = '待审核';
                } else if($v['balancestatus'] == 2){
                    $balance[$k]['balancestatus'] = '已结算';
                } else if($v['balancestatus'] == 3){
                    $balance[$k]['balancestatus'] = '结算单审核';
                } else if($v['balancestatus'] == 4){
                    $balance[$k]['balancestatus'] = '账单有误';
                }

				$balance[$k]['totalamount'] = str_replace(",", "", number_format($v['totalamount'], 2));
				$balance[$k]['actualamount'] = str_replace(",", "", number_format($v['actualamount'], 2));
            }
            $this->assign('balance',$balance);*/
            $usertype = I('get.usertype','','intval');
            $this->menucheck();
            $this->assign('usertype',$usertype);
            $this->display();
        } else{
            Header("Location: /error505/ ");
            exit();
        }
    }

    public function searchBalance(){
        if(!$this->isAjax()){
            $this->ajaxReturn('fail',"非法操作",0);
            exit();
        }

        $balancestatus = isset($_POST['balancestatus']) ? $_POST['balancestatus'] : '';
        $startdate = isset($_POST['startdate']) ? $_POST['startdate'] : '';
        $enddate = isset($_POST['enddate']) ? $_POST['enddate'] : '';
        $sourcetype = isset($_POST['sourcetype']) ? $_POST['sourcetype'] : '';
        $account = isset($_POST['account']) ? $_POST['account'] : '';
        $current    = isset($_POST['current']) ? (int)$_POST['current'] : 1;
        $rowCount   = isset($_POST['rowCount']) ? (int)$_POST['rowCount'] : 1;
        $usertype   = isset($_POST['usertype']) ? (int)$_POST['usertype'] : '';

        $sort = $this->parseOrder();
        $where=array();
        if($balancestatus){
            $where["B.balancestatus"] = $balancestatus;
        }
        if(!empty($startdate)){
           $where["B.applytime"]  = array(array('egt',$startdate." 00:00:00"),array('elt',$enddate." 23:59:59"));
        }
        if(!empty($sourcetype)){
            $where["U.sourcetype"] = $sourcetype;
        }
        if(!empty($account)){
            $complex['U.account'] = array('like', "%{$account}%");
            $complex['U.bindmobile'] = array('like', "%{$account}%");
            $complex["U.bindemail"] = array('like','%'.$account.'%');
            $complex['_logic'] = 'OR';
            $where['_complex'] = $complex;
        }

        if($usertype > 0){
            $where['U.usertype'] = $usertype;
        }

        $where["B.activeflag"] = 1;

        $model= M('tg_balance');
        $count = $model->alias("B")
            ->join(C('DB_PREFIX')."tg_user U on B.userid = U.userid", "LEFT")
            ->where($where)
            ->count();
        $balance = $model->alias("B")
            ->join(C('DB_PREFIX')."tg_user U on B.userid = U.userid", "LEFT")
            ->where($where)->order("B.createtime desc")
            ->order($sort)
            ->page($current, $rowCount)
            ->select();
        empty($balance) && $balance = array();
        foreach($balance as $k => $v){
            if($v['balancestatus'] == 1){
                $balance[$k]['balancestatus'] = '待审核';
            } else if($v['balancestatus'] == 2){
                $balance[$k]['balancestatus'] = '已结算';
            } else if($v['balancestatus'] == 3){
                $balance[$k]['balancestatus'] = '结算单审核';
            } else if($v['balancestatus'] == 4){
                $balance[$k]['balancestatus'] = '账单有误';
            }

            $balance[$k]['totalamount'] = str_replace(",", "", number_format($v['totalamount'], 2));
            $balance[$k]['actualamount'] = str_replace(",", "", number_format($v['actualamount'], 2));
        }

        echo json_encode(array(
            'current' => $current,
            'rowCount' => $rowCount,
            'rows' => $balance,
            'total' => $count
        ));
        exit;
    }

    protected function parseOrder(){
        $sort = isset($_POST['sort']) ? $_POST['sort'] : '';

        $order = 'B.createtime desc';
        foreach($sort as $k => $v){
            $order = "{$k} {$v}";
        }

        return $order;
    }
	
    //结算单详情
    public function balancedetail(){
		$this->logincheck();

        // $this->assign('modeifySharerateRight',$this->authoritycheck(10144));// 正式
	   $this->assign('modeifySharerateRight',$this->authoritycheck(10148));// 测试

		$id = $_GET['balanceid'];
		if ($id == 0) {
			Header("Location: /balanceall/ ");
			exit();
		}
		$balanceModel= M('tg_balance');
		$balance = $balanceModel->find($id);

		$userModel= M('tg_user');
		$user = $userModel->find($balance["userid"]);
		if ($balance["accounttype"] == 1) {
			$aliaccountModel= M('tg_aliaccount');
			$aliaccount = $aliaccountModel->find($balance["aliaccountid"]);
		} else if ($balance["accounttype"] == 2) {
			$bankaccountModel= M('tg_bankaccount');
			$bankaccount = $bankaccountModel->find($balance["bankaccountid"]);
		}

        $sourceaccountModel= M('tg_sourceaccount');
        $condition['SA.balanceid'] = $id;
        $condition["SA.activeflag"] = 1;
        $sourceaccount = $sourceaccountModel->alias("SA")
                    ->join(C('DB_PREFIX')."tg_game G on SA.gameid = G.gameid", "LEFT")
                    ->join(C('DB_PREFIX')."tg_channel C on SA.channelid = C.channelid", "LEFT")
                    ->join(C('DB_PREFIX')."tg_source S on SA.sourceid = S.id", "LEFT")
                    ->where($condition)
                    ->order("SA.createtime desc")
                    ->field("SA.sourceid,SA.gameid,SA.sharerate,SA.channelrate,SA.sourceincome,SA.sourcejournal,SA.actualpaid,G.gameicon,G.gamename,C.channelname")
                    ->select();

		if ($balance["accounttype"] == 1) {
			$this->assign('aliaccount',$aliaccount);
		} else if ($balance["accounttype"] == 2) {
			$this->assign('bankaccount',$bankaccount);
		}
		foreach($sourceaccount as $k => $v){
            $sourceaccount[$k]['sourceincome'] = str_replace(",", "", number_format($v['sourceincome'], 2));
			$sourceaccount[$k]['sourcejournal'] = str_replace(",", "", number_format($v['sourcejournal'], 2));
		}

        $this->assign('source',$sourceaccount);
        $this->assign('balance',$balance);
        $this->assign('user',$user);

        $this->menucheck();

        $this->display();
    }

	//查看所有结算单指定日期数据
    public function viewDaterangeBalance(){
		$this->logincheck();
        $startdate = $_POST['startdate']." 00:00:00";
        $enddate = $_POST['enddate']." 23:59:59";
        $model= M('tg_balance');
        $condition["B.applytime"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        $condition["B.activeflag"] = 1;
        $condition['_logic'] = 'AND';
        $balance = $model->field("B.id,U.account,B.applytime,B.totalamount,B.actualamount,B.balancestatus,B.paidamount")->alias("B")->join(C('DB_PREFIX')."tg_user U on B.userid = U.userid", "LEFT")->where($condition)->order("B.createtime desc")->select();
        foreach($balance as $k => $v){
            if($v['balancestatus'] == 1){
                $balance[$k]['balancestatus'] = '待审核';
            } else if($v['balancestatus'] == 2){
                $balance[$k]['balancestatus'] = '已结算';
            } else if($v['balancestatus'] == 3){
                $balance[$k]['balancestatus'] = '结算单审核';
            } else if($v['balancestatus'] == 4){
                $balance[$k]['balancestatus'] = '账单有误';
            }
        }
        if($balance){
            $this->ajaxReturn($balance,'success',1);
            exit();
        }else{
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
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
        $this->menucheck();
        $this->display();
    }

    public function resetBalance(){
        $this->logincheck();

        $id = isset($_POST["id"]) ? (int)$_POST["id"] : '';
        $money = isset($_POST["money"]) ? $_POST["money"] : 0;
        $beizhu = isset($_POST["beizhu"]) ? $_POST["beizhu"] : '';

        if(empty($id)){
            $this->ajaxReturn('参数错误','error',0);
        }

        $modal = M('tg_balance');

        $condition["id"] = $id;
        $balance = $modal->where($condition)->find();

        if($balance['accounttype'] == 3){
            $this->ajaxReturn('游侠币提现不能撤销','fail',0);
        }

        if($balance['balancestatus'] <> 2){
            $this->ajaxReturn('未结算不能撤销','fail',0);
        }

        $data["paidamount"] = $money;
        $data["beizhu"] = $beizhu;
        $data["balancestatus"] = 1;
        $data["updatetime"] = date("Y-m-d H:i:s");
        $data["updateuser"] = $_SESSION["userid"];
        $result = $modal->where($condition)->save($data);

        $this->insertLog($_SESSION['adminname'],'结算单重置', 'BalanceAction.class.php', 'resetBalance', $data['updatetime'], $_SESSION['adminname']."重置【".$balance['createuser']."】结算单,时间【".$balance["createtime"]."】,原始结算：".$balance["paidamount"].",修改>结算:".$data["paidamount"]);
        if($result){
            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('未能更新成功','fail',0);
            exit();
        }
    }

    //查看指定日期每日流水
    public function viewDaterangeDaily(){
		$this->logincheck();
		$balanceid = $_GET['balanceid'];
		$sourceid = $_GET['sourceid'];
        $startdate = $_POST['startdate'];
        $enddate = $_POST['enddate'];
		$sourceaccountModel= M('tg_sourceaccount');
        $condition['SA.balanceid'] = $balanceid;
		$condition['SA.sourceid'] = $sourceid;
        $condition["SA.activeflag"] = 1;
		$balance = $sourceaccountModel->alias("SA")->join(C('DB_PREFIX')."tg_balance B on SA.balanceid = B.id", "LEFT")->where($condition)->order("SA.createtime desc")->find();
		if ($balance["startdate"] > $startdate) {
			$startdate = $balance["startdate"];
		}
		if ($balance["enddate"] > $enddate) {
			$enddate = $balance["enddate"];
		}
		$dailyaccountModel= M('tg_dailyaccount');
		$dailycondition["date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
		$dailycondition["sourceid"]  = $sourceid;
        $dailycondition["activeflag"] = 1;
        $daily = $dailyaccountModel->field("id,date,dailyactive,newpeople,paypeople,payrate,dailyjournal,dailyincome")->where($dailycondition)->order("createtime desc")->select();
        if($daily){
            $this->ajaxReturn($daily,'success',1);
            exit();
        }else{
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
    }

	//修改结算单的是否有误状态
    public function changeBalanceerror(){
		$this->logincheck();

        $id = $_POST["id"];
		$beizhu = $_POST["beizhu"];

		$modal = M('tg_balance');
		$condition["id"] = $id;
        $data["balancestatus"] = 4;//账单有误状态。不需要is_error字段
        $data["beizhu"] = $beizhu;
        $data["updatetime"] = date("Y-m-d H:i:s");
		$data["updateuser"] = $_SESSION["userid"];
		$result = $modal->where($condition)->save($data);
        if($result){
            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('未能更新成功','fail',0);
            exit();
        }
    }

	//完成结算单
    public function finishBalance(){
		$this->logincheck();

        $adminname=$_SESSION["adminname"];
        $adminid=$_SESSION["adminid"];

        $accounttype = $_POST["accounttype"];
        $id = $_POST["id"];
		$paidamount = $_POST["paidamount"];
        
		$modal = M('tg_balance');
		$condition["id"] = $id;
		$data["paidamount"] = $paidamount;
        $data["beizhu"] = $_POST["beizhu"];
		$data["balancestatus"] = 2;
		$data["updatetime"] = date("Y-m-d H:i:s");
		$data["updateuser"] = $adminname;
		$result = $modal->where($condition)->save($data);

        if($accounttype==3){ 
            // 如果结算为游侠币，则给用户冲游侠币
            $sql = 'SELECT 
                            a.userid,
                            b.issdkuser,
                            b.account 
                    FROM yx_tg_balance a 
                    LEFT JOIN yx_tg_user b on a.userid = b.userid 
                    WHERE a.id='.$id;
            $result = M()->query($sql);
            $username=$result[0]['account'];

            // 在sdk中，用户没有sdk_
            $username=substr($username,4,strlen($username)-4);

            $issdkuser=$result[0]['issdkuser'];
            $userid=$result[0]['userid'];
            $getcoin=round($paidamount*10);
            
            if($issdkuser==1){ 
                // 如果是玩家
                // 充值记录表 coin_recharge
                // 用户游侠币钱包表 coin_wallet
               
                // 添加充值记录
                $now=time();
                $coin_recharge_sql = "INSERT INTO yx_coin_recharge (
                                                username,
                                                ptb,
                                                ffusername,
                                                create_time,
                                                beizhu,
                                                amount,
                                                status
                                    ) VALUES (
                                            '{$username}',
                                            '{$getcoin}',
                                            'admin',
                                            '{$now}',
                                            '推广有奖',
                                            '{$paidamount}',
                                            '1'
                                    ) ";
                $coin_recharge_result = mysql_query($coin_recharge_sql);

                // 更新用户钱包游侠币
                $coin_wallet_sql = "SELECT id FROM yx_coin_wallet WHERE username='{$username}' ";
                $coin_wallet_result = mysql_query($coin_wallet_sql);
                $coin_wallet_row = mysql_fetch_assoc($coin_wallet_result);
                if($coin_wallet_row){
                    $coin_wallet_sql = "UPDATE yx_coin_wallet SET 
                                                ttb=ttb+{$getcoin}
                                            WHERE
                                                username='{$username}'
                                        ";
                    mysql_query($coin_wallet_sql);
                }else{
                    $coin_wallet_sql = "INSERT INTO yx_coin_wallet (
                                                username,
                                                ttb,
                                                create_time
                                           )VALUES(
                                                '{$username}',
                                                '{$getcoin}',
                                                '{$now}'
                                        )";
                    mysql_query($coin_wallet_sql);
                }
            }else{ 
                // 如果是推广会员
                // 充值记录表 yx_tg_coinlog
                // 推广用户表 用户tg_user
                $data=array(
                    'userid' => $userid, 
                    'preauthuser' => $adminid, 
                    'preauthusername' => $adminname, 
                    'amount' => $getcoin, 
                    'activeflag' => 1, 
                    'createtime' => date('Y-m-d H:i:s'), 
                    'createuser' => $adminname, 
                    'beizhu' => '结算转入', 
                );
                $coinlog_model=M('tg_coinlog');
                $coinlog_model->add($data);

                $user_model=M('tg_user');
                $where = array('userid' =>$userid);
                $user_model->where($where)->setInc('coinpreauth',$getcoin);
            }
        }

        if($result){
            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('未能完成，请联系管理员','fail',0);
            exit();
        }
    }

    // 对于开发票的公司用户，对账单已出，请客户确认对账单并开具发票
    public function checkBill(){
        $this->logincheck();
        $id = $_POST["id"];
        $modal = M('tg_balance');
        $condition["id"] = $id;
        $data["balancestatus"] = 3;//对于开发票的公司用户，有一个中间状态。
        $data["updatetime"] = date("Y-m-d H:i:s");
        $data["updateuser"] = $_SESSION["userid"];
        $result = $modal->where($condition)->save($data);
        if($result){
            $balance = $modal->field('userid,enddate')->find($id);

            $userModel = M('tg_user');
            $user = $userModel->field('account,channelbusiness,companyname')->find($balance['userid']);

            $systemModel = M('sys_admin');
            $systemWhere = array( 'beizhu' => $user['channelbusiness']);
            $system = $systemModel->where($systemWhere)->field('mobile')->find();
            $businessmobile = $system['mobile'];

            $currentTime=date('Y-m-d H:i:s',time());

            // 给用户发站内消息
            $content = '您于'.$currentTime.'提交的提现申请已经审核通过，请您尽快在结算中心核对本期对账单，确认无误后请按照结算单提供的发票信息开具发票，为保障结算进度，建议每月10号前将对账单及发票寄达我司。';        //内容
            $messageData = array(
                'userid' => $balance['userid'], 
                'category' => '系统消息', 
                'title' => '结算通知', 
                'content' => $content, 
                'isread' => 0, 
                'activeflag' => 1, 
                'createtime' => $currentTime, 
                'createuser' => 'Admin', 
            );
            $messageModal = M('tg_message');
            if ($messageModal->add($messageData)) {
                $this->insertLog($_SESSION['adminname'],'发送消息', 'UserAction.class.php', 'sendMailAction', $messageData['createtime'], $_SESSION['adminname']."向用户【".$user['account']."发送类型为：".$messageData["category"]."标题为：".$messageData["title"]."的消息】");
            } else {
                $this->ajaxReturn('fail','给用户发站内消息失败。',0);
                exit();
            }

            // 给商务发短信
            if(!empty($businessmobile)){
                header("content-type:text/html; charset=utf-8;");//开启缓存
                $_SESSION['smstime'] = date("Y-m-d H:i:s");
                $smscode = rand(100000,999999);
                $_SESSION['smscode'] = $smscode;    //将content的值保存在session
                $username = '70208457';     //用户账号
                $password = '15927611975';      //密码
                $content = $user['companyname'].'公司'.$balance['enddate'].'对账单已出，请尽快通知客户完成对账单核对并开具发票，保障结算进度。';        //内容
                // $content = "此次申请修改绑定手机的验证码为".$smscode.",有效时间5分钟.";        //内容
                $http = 'http://api.duanxin.cm/';
                $data = array
                (
                    'action'=>'send',
                    'username'=>$username,                  //用户账号
                    'password'=>strtolower(md5($password)), //MD5位32密码
                    'phone'=>$businessmobile,                //号码
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
                    // $this->ajaxReturn("success",'短信验证码发送成功，有效时间5分钟。',1);
                    $this->ajaxReturn($result,'success',1);
                    exit();
                }else{
                    $this->ajaxReturn("fail",'给商务发短信失败。',0);
                    exit();
                }
            }
        }else{
            $this->ajaxReturn('未能完成，请联系管理员','fail',0);
            exit();
        }
    }

    // 导出结算单的excel表
    public function export(){
        $this->logincheck();
        $id = $_POST["id"];
        if ($id) {
            //-----引入PHPExcel类
            include_once '../Third/PHPExcel/Classes/PHPExcel.php';
            
            //-----获取数据
            $balanceModel= M('tg_balance');
            $balance = $balanceModel->find($id);

            $userModel= M('tg_user');
            $user = $userModel->find($balance["userid"]);
            if ($balance["accounttype"] == 1) {
                $aliaccountModel= M('tg_aliaccount');
                $aliaccount = $aliaccountModel->find($balance["aliaccountid"]);
            } else if ($balance["accounttype"] == 2) {
                $bankaccountModel= M('tg_bankaccount');
                $bankaccount = $bankaccountModel->find($balance["bankaccountid"]);
            }
            if ($balance["accounttype"] == 1) {
                $this->assign('aliaccount',$aliaccount);
            } else if ($balance["accounttype"] == 2) {
                $this->assign('bankaccount',$bankaccount);
            }

            $systemModel = M('sys_admin');
            $systemWhere = array( 'beizhu' => $user['channelbusiness']);
            $system = $systemModel->where($systemWhere)->field('mobile')->find();

            $prefix = C('DB_PREFIX');
            $where = ' and a.balanceid='.$id.' and a.activeflag=1 ';
            $sql = "SELECT DISTINCT
                    a.gameid
            FROM {$prefix}tg_sourceaccount a
            WHERE 1 ".$where;
            $sourceaccount=M()->query($sql);

            foreach ($sourceaccount as $key => $value) {
                $where=' and a.balanceid='.$id.' and a.activeflag=1 and a.gameid='.$value['gameid'].' and a.sourcejournal!=0 ';
                $sql="SELECT
                        a.*,
                        b.*,
                        c.*,
                        d.*
                FROM {$prefix}tg_sourceaccount a
                LEFT JOIN {$prefix}tg_game b ON a.gameid=b.gameid
                LEFT JOIN {$prefix}tg_channel c ON a.channelid=c.channelid
                LEFT JOIN {$prefix}tg_source d ON  a.sourceid=d.id
                WHERE 1 ".$where;
                $detail=M()->query($sql);
                $sourceaccount[$key]['detail']=$detail;
                $sourceaccount[$key]['detailNum']=count($detail);
                if($sourceaccount[$key]['detailNum']==0){
                    unset($sourceaccount[$key]);
                }
            }

            //------写内容
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);// 设置当前的sheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');// 设置sheet的name

            // 设置默认字体和大小
            $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', '宋体'));
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);


            //设置样式
            $centerStyle = array(
                'alignment' => array(
                   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                   'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $boldStyle = array(
                'font' => array(
                   'bold' => true,
                ),
                'alignment' => array(
                   'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $centerBoldStyle = array(
                'font' => array(
                   'bold' => true,
                ),
                'alignment' => array(
                   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                   'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $centerBoldSmallStyle = array(
                'font' => array(
                   'bold' => true,
                   'size'=>10,
                ),
                'alignment' => array(
                   'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                   'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            $boldSmallStyle = array(
                'font' => array(
                   'bold' => true,
                   'size'=>10,
                ),
                'alignment' => array(
                   'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );

            // 设置单元格的值
            $titleContent = $user['realname'].'-结算单';
            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', $titleContent);
            // 设置样式
            $objPHPExcel->getActiveSheet()->getStyle('A1' )->applyFromArray($centerBoldStyle);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
             $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);

            $typeDateContent='打印日期：'.date('Y年m月d日',time());
            $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
            $objPHPExcel->getActiveSheet()->setCellValue('A2', $typeDateContent);
            // 设置样式
            $objPHPExcel->getActiveSheet()->getStyle('A2' )->applyFromArray($boldStyle);
            $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(25);
            
            $objPHPExcel->getActiveSheet()->setCellValue('A3', '结算周期');
            $objPHPExcel->getActiveSheet()->setCellValue('B3', '游戏名称');
            $objPHPExcel->getActiveSheet()->setCellValue('C3', '渠道名');
            $objPHPExcel->getActiveSheet()->setCellValue('D3', '充值金额（￥）');
            $objPHPExcel->getActiveSheet()->setCellValue('E3', '通道费');
            $objPHPExcel->getActiveSheet()->setCellValue('F3', '税率');
            $objPHPExcel->getActiveSheet()->setCellValue('G3', '分成比例');
            $objPHPExcel->getActiveSheet()->setCellValue('H3', '结算金额（￥）');
            // 设置样式
            $objPHPExcel->getActiveSheet()->getStyle('A3:H3' )->applyFromArray($centerBoldStyle);
            $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(25);

            $current_line=4;
            // 根据游戏，资源统计出来
            if($sourceaccount){
                if ($user["invoicetype"] == 1) {
                    $taxrateContent = '0.0672(普通发票)';
                } else if ($user["invoicetype"] == 2) {
                    $taxrateContent = '0.0336(3%增值税发票)';
                } else if ($user["invoicetype"] == 3) {
                    $taxrateContent = '0(6%增值税发票)';
                } else if ($user["invoicetype"] == 0 && $user['usertype'] ==2 ) {
                    $taxrateContent = '0.0672(公司不开发票)';
                } else if ($user["invoicetype"] == 0 && $user['usertype'] ==1 ) {
                    $taxrateContent = '0.03(个人用户)';
                }

                $balancePeriodContent = $balance['startdate'].'到'.$balance['enddate'];
                $totalChargeContent=0;

                foreach ($sourceaccount as $key => $value) {
                    $objPHPExcel->getActiveSheet()->mergeCells('B'.$current_line.':B'.($current_line+$value['detailNum']-1));

                    foreach ($value['detail'] as $key1 => $value1) {
                        $gamenameContent = $value1['gamename'];
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$current_line, $gamenameContent);

                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $balancePeriodContent);

                        $channelnameContent = $value1['channelname'];
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$current_line, $channelnameContent);

                        $sourcejournalContent = '￥'.number_format($value1['sourcejournal'],'2');
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$current_line, $sourcejournalContent);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$current_line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        $totalChargeContent+=$value1['sourcejournal'];

                        $sourcechannelrateContent = $value1['sourcechannelrate'];
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$current_line, $sourcechannelrateContent);
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$current_line, $taxrateContent);

                        $sourcesharerateContent = $value1['sourcesharerate'];
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$current_line, $sourcesharerateContent);

                        $sourceincomeContent = '￥'.number_format($value1['sourceincome'],2);
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$current_line, $sourceincomeContent);
                        $objPHPExcel->getActiveSheet()->getStyle('H'.$current_line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        //设置样式
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line.':C'.$current_line)->applyFromArray($centerStyle);
                        $objPHPExcel->getActiveSheet()->getStyle('E'.$current_line.':G'.$current_line)->applyFromArray($centerStyle);
                        $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(25);

                        $current_line++;
                    }
                }
            }

            $totalItemContent='合计：';
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$current_line.':C'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $totalItemContent);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line)->applyFromArray($centerBoldSmallStyle);


            $totalChargeContent = '￥'.number_format($totalChargeContent,2);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$current_line, $totalChargeContent);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$current_line)->applyFromArray($boldSmallStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$current_line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            // $totalBalanceContent = '=SUM(H4:H'.($current_line-1).')';
            $totalBalanceContent = '￥'.number_format($balance['actualamount'],2);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$current_line, $totalBalanceContent);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$current_line)->applyFromArray($boldSmallStyle);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$current_line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(25);
            $current_line++;

            $capitalMoneyItemContent='金额大写：';
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$current_line.':C'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $capitalMoneyItemContent);

            $capitalMoneyContent='￥'.numbers_transform_capital($balance['actualamount']);
            $objPHPExcel->getActiveSheet()->mergeCells('D'.$current_line.':H'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$current_line, $capitalMoneyContent);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line.':H'.$current_line)->applyFromArray($centerBoldSmallStyle);

            $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(25);
            $current_line++;

            $formulaItemContent='收入成分计算公式：';
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$current_line.':C'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $formulaItemContent);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line)->applyFromArray($centerBoldSmallStyle);

            $formulaContent='甲方收入分成 =（游戏总收入-支付通道手续费-运营工具使用费）*（1-税率）* 分成比例';
            $objPHPExcel->getActiveSheet()->mergeCells('D'.$current_line.':H'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$current_line, $formulaContent);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$current_line)->applyFromArray($centerBoldSmallStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$current_line)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE); // 黑色

            $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(25);
            $current_line++;

            $firstContent="\n";
            $firstContent.='甲方：';
            if($balance['usertype']==1){
                $firstContent.=$user['realname']."（个人）\n";
            }else{
                $firstContent.=$user['companyname']."（公司）\n";
            }

            if($balance['accounttype']==1){
                $firstContent.='支付宝账号：'.$aliaccount['aliaccount']."\n";
                $firstContent.='支付宝账号用户名：：'.$aliaccount['aliusername']."\n";
            }else{
                $firstContent.='开户行：'.$bankaccount['bankname']."\n";
                $firstContent.='银行账户：'.$bankaccount['bankusername'].' '.$bankaccount['bankaccount']."\n";
            }

            $firstContent.='联系人：'.$user['realname']."\n";
            $firstContent.='联系地址：'.$user['address']."\n";
            $firstContent.='电话：'.$user['contactmobile']."\n";
            $firstContent.='盖章：'."\n";
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$current_line.':D'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$current_line, $firstContent);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$current_line)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            $secondContent= "\n";
            $secondContent.='乙方：武汉游侠精灵科技有限公司'."\n";
            $secondContent.='纳税识别号：91420100303481622A '."\n";
            $secondContent.='开户银行：招商银行股份有限公司武汉光谷科技支行'."\n";
            $secondContent.='银行账户：1279 0736 3810 506'."\n";
            $secondContent.='开票地址：武汉市东湖开发区关山一路1号曙光软件园内恒隆大楼'."\n";
            $secondContent.='电话：027-87782538'."\n";
            $secondContent.='联系地址：武汉洪山区光谷软件园D8栋3楼'."\n";
            $secondContent.='联系人：'.$user['channelbusiness'].' '.$system['mobile']."\n";
            $secondContent.='盖章：'."\n";
            $objPHPExcel->getActiveSheet()->mergeCells('E'.$current_line.':H'.$current_line);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$current_line, $secondContent);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$current_line)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$current_line)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            //设置宽度
            // $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            // 设置高度
            $objPHPExcel->getActiveSheet()->getRowDimension($current_line)->setRowHeight(180);

            //----------保存excel—2007格式
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            //或者$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); 非2007格式

            // 生成文件目录
            include_once '../Share/classes/DirClass.php';
            $today=date('Y/m/d/');
            $save_path='upfiles/exportBalance/'.$today;
            Dir::create_dir_auto($save_path);
            // 文件名
            $file_name=md5(time());
            $file_all_path=$save_path.$file_name.'.xlsx';

            // 生成excel
            $objWriter->save($file_all_path);

            $data['status'] = 1;
            $data['info'] = 'success';
            $data['url'] = $file_all_path;
            $this->ajaxReturn($data,'JSON');
            exit();
        }else{
            $this->ajaxReturn('未能完成，请联系管理员','fail',0);
            exit();
        }
    }

    // 编辑备注
    public function editBeizhu(){
        $this->logincheck();
        if (!$this->isAjax()){
            $this->ajaxReturn('非法访问','fail',0);
            exit();
        }

        $balanceid = $_POST["balanceid"];
        $beizhu = $_POST["beizhu"];

        // 获取以前账单信息
        $balanceModal = M('tg_balance');
        $where=array('id'=>$balanceid);
        $oldBalance=$balanceModal->field('userid,beizhu,startdate,enddate')->where($where)->find();

        $data=array('beizhu'=>$beizhu);
        $where=array('id'=>$balanceid);
        $result = $balanceModal->where($where)->save($data);
        if($result){
            // 写日志
            $userModel = M('tg_user');
            $where=array('userid'=>$oldBalance['userid']);
            $user=$userModel->field('account')->where($where)->find();

            $time = date('Y-m-d H:i:s',time());
            $this->insertLog($_SESSION['adminname'],'编辑结算备注', 'BalanceAction.class.php', 'editBeizhu', $time, $_SESSION['adminname']."编辑了balanceid为“".$balanceid."”用户为".$user['account']."”时间间隔为“".$oldBalance['startdate']."——".$oldBalance['enddate']."”的备注，备注由“".$oldBalance['beizhu']."变为".$beizhu);

            $this->ajaxReturn($result,'success',1);
            exit();
        }else{
            $this->ajaxReturn('未能完成，请联系管理员','fail',0);
            exit();
        }
    }

    // 修改该结算的分成比例
    public function modifySharerate(){
        $this->logincheck();
        if (!$this->isAjax()){
            $this->ajaxReturn('非法访问','fail',0);
            exit();
        }

        // if($this->authoritycheck(10144) != 'ok'){ // 正式
	if($this->authoritycheck(10148) != 'ok'){ //测试
            $this->ajaxReturn('没有权限','fail',0);
            exit();
        }

        // 修改该结算单周期的分成比例
        $balanceid = $_POST["balanceid"];
        $gameid = $_POST["gameid"];
        $sharerate = $_POST["sharerate"];
        $newSharerate = $_POST["newSharerate"];
        $channelrate = $_POST["channelrate"];
        $newChannelrate = $_POST["newChannelrate"];

        $balanceModal = M('tg_balance');
        $condition["id"] = $balanceid;
        $balance = $balanceModal->field('userid,startdate,enddate')->where($condition)->find();

        // 游戏，人，时间
        $dailyaccountModel= M('tg_dailyaccount');
        $where=array('gameid'=>$gameid); //第一次使用这种方式，是初始化，防止以前有该数组
        $where['userid']=$balance['userid'];
        $where['date']=array(array('egt',$balance['startdate']),array('elt',$balance['enddate']));
        $dailyaccount=$dailyaccountModel->field('id,dailyjournal')->where($where)->select();

        foreach ($dailyaccount as $key => $value) {
            $dailyincome=$value['dailyjournal'] * $newSharerate * (1 - $newChannelrate);

            $dailyaccountData=array(
                'sharerate' => $newSharerate, 
                'channelrate' => $newChannelrate, 
                'dailyincome' =>$dailyincome 
             );
            $dailyaccountModel->where('id='.$value['id'])->save($dailyaccountData);
        }

        $time = date('Y-m-d H:i:s',time());

        $usermodel = M('tg_user');
        $where=array('userid'=>$balance['userid']);
        $user = $usermodel->field('account')->where($where)->find();

        $gameModel = M('tg_game');
        $where=array('gameid'=>$gameid);
        $game = $gameModel->field('gamename')->where($where)->find();

        $this->insertLog($_SESSION['adminname'],'结算修改分成比例', 'BalanceAction.class.php', 'modifySharerate', $time, $_SESSION['adminname']."编辑了用户“".$user['account']."”游戏名为“".$game['gamename']."”时间间隔为“".$balance['startdate']."-".$balance['enddate']."”的每日统计，分成比例由“".$sharerate."变为".$newSharerate."”，渠道费由“".$channelrate."变为".$newChannelrate);

        $this->ajaxReturn("保存成功",'success',1);
        exit();
    }

    // 暂时开发接口，前台没有入口
    public function cancelWithdraw(){
        $balanceid=$_POST['balanceid'];
    
        // 删除以前的结算单统计
        $sourceaccountModel = M('tg_sourceaccount');
        $sourceaccountModel->where('balanceid='.$balanceid)->delete();

        $balanceModel = M('tg_balance');
        $where=array('id'=>$balanceid);
        $oldbalance = $balanceModel->where($where)->find();
        $balanceModel->where('id='.$balanceid)->delete();

        $time = date('Y-m-d H:i:s',time());
        $this->insertLog($_SESSION['adminname'],'撤销提现', 'BalanceAction.class.php', 'cancelWithdraw', $time, $_SESSION['adminname']."申请提现，提现周期为：“".$oldbalance['startdate']." ~ ".$oldbalance['enddate']."”提现金额为：“".$oldbalance['totalamount']."”，提现方式为“".$oldbalance['accounttypename']."”");
    }
}
?>