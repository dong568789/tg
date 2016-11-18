<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/18
 * Time: 11:33
 */
class StatisticsAction extends CommonAction
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->menucheck();
        $this->display();
    }


    public function ajaxData()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $channel = I('request.channel','','intval');


        if(!empty($startTime) && !empty($endTime)){
            $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));
        }elseif(!empty($startTime) && empty($endTime)){
            $where['a.date'] = array('EGT', $startTime);
        }elseif(empty($startTime) && !empty($endTime)){
            $where['a.date'] = array('ELT', $endTime);
        }else{
            $startTime =date('Y-m-1');
            $endTime = date('Y-m-d');

            $where['a.date'] = array(array('EGT', $startTime), array('ELT', $endTime));
        }

        !empty($channel) && $where['a.channelid'] = $channel;


        //每个渠道
        $dailyaccountModel = M('TgDailyaccount');
        $dailCount = $dailyaccountModel->alias('a')
            ->join('left join ' . C('DB_PREFIX') . 'tg_user as b on a.userid=b.userid')
            ->where($where)
            ->field('a.userid,a.channelid,sum(dailyjournal) as sum_dailyjournal,b.realname')
            ->group('a.userid')
            ->select();

        $where = array(
            'b.status' => 1,
            'b.create_time' =>  array(array('EGT',strtotime($startTime.' 00:00')), array('ELT', strtotime($endTime.' 23:59')))
        );
        !empty($channel) && $where['b.channelid'] = $channel;
        $data = M('')->table(C('DB_PREFIX') . 'tg_source as a')
            ->join("left join " . C('DB_PREFIX') . "all_pay as b on a.sourcesn=b.agent ")
            ->where($where)
            ->field('a.userid,a.channelid,sum(case WHEN b.voucherje > 0 THEN b.amount-b.voucherje ELSE b.amount END) as sum_amount')
            ->group('a.userid')
            ->select();

        $itemData = $arrUserid = array();
        foreach($data as $v){
            $itemData[$v['userid']] = $v;
        }

        foreach($dailCount as $v){
            $arrUserid[] = $v['userid'];
        }

        $where = array(
            'userid' => array('in', $arrUserid),
            'activeflag' => 1,
            'balancestatus' => 1
        );
        $balancemodel = M('tg_balance');
        $balance = $balancemodel->where($where)->order("id desc")->select();
        $itemBalance = array();
        foreach($balance as $vla){
            if ($vla["activeflag"] == 1) {
                if ($vla["balancestatus"] == 1) {
                    $itemBalance[$vla['userid']]['unsettled'] += $vla["actualamount"];
                }
            }
        }

        foreach($dailCount as $key => &$value){
            $sumAmount = isset($itemData[$value['userid']]) ? $itemData[$value['userid']]['sum_amount'] : 0;
            $value['yx_amount'] = number_format($sumAmount - $value['sum_dailyjournal'],0);
            $value['yx_countamount'] =  number_format($value['sum_dailyjournal'] + $value['yx_amount'], 0);
            $value['timeZone'] = "{$startTime}至{$endTime}";
            $value['unsettled'] = isset($itemBalance[$value['userid']]) ? $itemBalance[$value['userid']]['unsettled'] : 0;
            $value['sum_dailyjournal'] = number_format($value['sum_dailyjournal'], 0);
            if($value['sum_dailyjournal'] <= 0 && $value['yx_amount'] <= 0){
                unset($dailCount[$key]);
            }

        }

        sort($dailCount);

        if (sizeof($dailCount) > 0) {
            $this->ajaxReturn($dailCount,'success',1);
            exit();
        } else {
            $this->ajaxReturn('fail',"暂无数据。",0);
            exit();
        }
    }



}