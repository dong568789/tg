<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/27
 * Time: 11:25
 */
class FinancialAction extends CommonAction
{

    public function index()
    {
        $this->logincheck();
        $this->menucheck();
        $this->display('Statistics:financial');
    }

    public function ajaxData()
    {
        $financialModel = M('static_financial');
        $condition = $this->parseWhere();
        $sort = $this->parseOrder();

        $financial = $financialModel
            ->where($condition)
            ->order($sort)
            ->select();

        foreach($financial as &$value){
            $value['earning'] = $value['amount'] + $value['buy_coin'] + $value['app'] + $value['buy_voucher'] +
                $value['offline_coin'] + $value['cps_into'];
            $value['expend'] = $value['cash_over'] + $value['cp_into'];


            $value['expend_qz'] = $value['balance_wait'] + $value['agent_coin'] + $value['game_coin'] + $value['voucher'];
            $value['earning_qz'] = $value['earning'] - $value['expend_qz'];
        }

        echo json_encode(array(
            'rows' => $financial,
            'total' =>count($financial)
        ));
    }

    public function parseWhere()
    {
        $startTime = I('request.startdate');
        $endTime = I('request.enddate');
        $where = array();
        if(empty($startTime) || empty($endTime)){
            $startTime =date('Y-m-01');
            $endTime = date('Y-m-d');
        }

        if(strtotime($endTime)  >= mktime(0,0,0)){
            $endTime = date('Y-m-d', strtotime('-1 day', mktime(0,0,0)));
        }

       // $where['time'] = array(array('EGT', $startTime), array('ELT', $endTime));

        return $where;
    }

    public function parseOrder()
    {
        return "time desc";
    }
}