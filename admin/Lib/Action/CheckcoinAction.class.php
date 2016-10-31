<?php
class CheckcoinAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    //列表
    public function index(){
		$this->logincheck();
        // $this->authoritycheck(10120);
        // if($this->authoritycheck(10120) == 'ok'){
            $this->menucheck();
            $this->checkcoin=$this->authoritycheck(10120);
            $this->checkcoin='ok';


            $dailyaccount=$this->searchpage();

            $this->assign('daily',$dailyaccount);

            $this->display();
        // } else{
        //     Header("Location: /error505/ ");
        //     exit();
        // }
    }

    //指定条件的搜索
    public function searchpage(){
        $startdate = $_POST['startdate'];
        $enddate = $_POST['enddate'];

        if($startdate && $enddate){
            $dailycondition["date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }
        
        $dailyaccountModel= M('tg_dailyaccount');
        $dailycondition['DA.getcoin_ischeck']=0;
        $dailycondition['DA.getcoin']= array('gt',0);
        $dailycondition["DA.activeflag"] = 1;
        $daily = $dailyaccountModel->alias("DA")
            ->join(C('DB_PREFIX')."tg_user U on DA.userid = U.userid", "LEFT")
            ->join(C('DB_PREFIX')."tg_channel C on DA.channelid = C.channelid", "LEFT")
            ->join(C('DB_PREFIX')."tg_game G on DA.gameid = G.gameid", "LEFT")
            ->field('DA.*,U.account,C.channelname,G.gamename')
            ->where($dailycondition)
            ->order("DA.getcoin_ischeck asc")
            ->select();

        return $daily;
    }

    //指定条件的搜索，异步返回
    public function searchpage_ajax(){
        $daily=$this->searchpage();

        if($daily){
            $this->ajaxReturn($daily,'success',1);
            exit();
        }else{
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
    }


    public function checkcoinhandle(){
        if($this->isAjax()){
            $record_id=$_POST['record_id'];

            $dailyaccountModel= M('tg_dailyaccount');

            // 更新每日统计
            $data['getcoin_ischeck']=1;
            $where['id']=$record_id;
            $dailyaccountModel->where($where)->save($data);

            $dailycondition["DA.id"] = $record_id;
            $daily = $dailyaccountModel->alias("DA")
                ->join(C('DB_PREFIX')."tg_user U on DA.userid = U.userid", "LEFT")
                ->where($dailycondition)
                ->field('DA.getcoin,DA.valpeople,U.account')
                ->find();

            $now = time();
            // 添加充值记录
            $coin_recharge_sql = "INSERT INTO yx_coin_recharge (
                                            username,
                                            ptb,
                                            ffusername,
                                            create_time,
                                            beizhu,
                                            amount,
                                            status
                                ) VALUES (
                                        '{$daily["account"]}',
                                        '{$daily["getcoin"]}',
                                        'admin',
                                        '{$now}',
                                        '推广有奖',
                                        '{$daily["valpeople"]}',
                                        '1'
                                ) ";
            $coin_recharge_result = mysql_query($coin_recharge_sql);

            // 更新用户钱包游侠币
            $coin_wallet_sql = "SELECT id FROM yx_coin_wallet WHERE username='{$daily["account"]}' ";
            $coin_wallet_result = mysql_query($coin_wallet_sql);
            $coin_wallet_row = mysql_fetch_assoc($coin_wallet_result);
            if($coin_wallet_row){
                $coin_wallet_sql = "UPDATE yx_coin_wallet SET 
                                            ttb=ttb+{$daily["getcoin"]}
                                        WHERE
                                            username='{$daily["account"]}'
                                    ";
                mysql_query($coin_wallet_sql);
            }else{
                $coin_wallet_sql = "INSERT INTO yx_coin_wallet (
                                            username,
                                            ttb,
                                            create_time
                                       )VALUES(
                                            '{$daily["account"]}',
                                            '{$daily["getcoin"]}',
                                            '{$now}'
                                    )";
                mysql_query($coin_wallet_sql);
            }
           
            $this->ajaxReturn('success','success',1);
            exit();
        }else{
            $this->ajaxReturn('fail','fail',0);
            exit();
        }
    }
}
?>