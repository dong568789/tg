<?php

class RechargeModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $userid = $_SESSION["userid"];

        $allpaymodel = M("all_pay");
        $sql = "select * from yx_all_pay where agent in ( select sourcesn from yx_tg_source where userid='$userid') AND regagent in ( select sourcesn from yx_tg_source where userid='$userid') and status = '1'";
        $rechargeall = $allpaymodel->query($sql);
        $recharge = array();
        foreach($rechargeall as $k => $v){

                $recharge[] = $v["amount"];

                $v['create_time'] = date('Y-m-d H:i',$v['create_time']);
                if ($v['status'] == 1) {
                    $v['status'] = "<span style='color:#F00'>成功</span>";
                } else if ($v['status'] == 2) {
                    $v['status'] = "<span style='color:#F00'>失败</span>";
                }  else if ($v['status'] == 0) {
                    $v['status'] = "待支付";
                }

                //充值方式
                $payModel = M('dic_paytype');
                $paytype = $payModel->select();
                foreach ($paytype as $k2 => $v2) {
                    if($v['paytype'] == $v2['paytype']){
                        $v['payname'] = $v2['payname'];
                    }

                }
                //获取游戏名
                $gamemodel = M('all_game');
                $game = $gamemodel->select();
                foreach ($game as $k3 => $v3) {
                    if($v['gameid'] == $v3['id']){
                        $v['gamename'] = $v3['name'];
                    }
                }
                $result[] = $v;
                $result[0]['allmoney'] = array_sum($recharge);
        }

        return $result;

    }

    public function channel(){
        $userid = $_SESSION['userid'];
        $channelmodel = M('tg_channel');
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
        return $channel;
    }



}
?>