<?php

class BalanceModel extends Model
{
    public function __construct(){
        parent::__construct();
    }



    public function money($userid){
        $balancemodel = M('tg_balance');
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $balance = $balancemodel->where($map)->order("id desc")->select();
        $sourcemodel = M('tg_source');
        $dailymodel = M('tg_dailyaccount');
        $money['unsettled'] = 0;
        $money['settled'] = 0;
        $money['unwithdraw'] = 0;
        //如果还未提现过，开始时间选择资源创造最早的那个时间
        if ($balance) {
            foreach ($balance as $k => $v) {
                if ($v["activeflag"] == 1) {
                    if ($v["balancestatus"] == 1) {
                        $money['unsettled'] += $v["actualamount"];
                    } else if ($v["balancestatus"] == 2) {
                        $money['settled'] += $v["paidamount"];
                    }
                }
            }
            $money['unsettled'] = str_replace(",", "", number_format($money['unsettled'], 2));
            $money['settled'] = str_replace(",", "", number_format($money['settled'], 2));
            $lastbalance = $balance[0];
            $startdate = date("Y-m-d",strtotime($lastbalance["enddate"]." 12:00:00"."+1 day"));
            $enddate = date("Y-m-d",strtotime("-1 day"));
            $dailycondition["date"] = array(array('EGT',$startdate),array('ELT',$enddate),'AND');
            $dailycondition["userid"] = $userid;
            $dailycondition["activeflag"] = 1;
            $daily = $dailymodel->where($dailycondition)->order("id desc")->select();
            if ($daily) {
                foreach ($daily as $k => $v) {
                    if ($v["activeflag"] == 1) {
                        $money['unwithdraw'] += $v["dailyincome"];
                    }
                }
                $money['unwithdraw'] = str_replace(",", "", number_format($money['unwithdraw'], 2));
            }
        } else {
            $sourcecondition["userid"] = $userid;
            $source = $sourcemodel->where($sourcecondition)->order("id asc")->select();
            if ($source) {
                $startdate = date("Y-m-d",strtotime($source[0]["createtime"]));
                $enddate = date("Y-m-d",strtotime("-1 day"));
                $dailycondition["date"] = array(array('EGT',$startdate),array('ELT',$enddate),'AND');
                $dailycondition["userid"] = $userid;
                $dailycondition["activeflag"] = 1;
                $daily = $dailymodel->where($dailycondition)->order("id desc")->select();
                if ($daily) {
                    foreach ($daily as $k => $v) {
                        if ($v["activeflag"] == 1) {
                            $money['unwithdraw'] += $v["dailyincome"];
                        }
                    }
                    $money['unwithdraw'] = str_replace(",", "", number_format($money['unwithdraw'], 2));
                }
            }
        }
        return $money;
    }

    public function getUserBalanceMoney($balanceid, $user)
    {
        $taxrate = $this->getUserRate($user);

        $prefix = C('DB_PREFIX');
        $where = 'a.balanceid='.$balanceid.' and a.activeflag=1  and a.sourcejournal!=0 ';
        $sql="SELECT
                      sum(
                      case WHEN a.sourcejournal > 0 THEN a.sourcejournal*d.sourcesharerate*(1-d.sourcechannelrate)*
                      (1-{$taxrate})
                      END
                      ) as actualamount
                FROM {$prefix}tg_sourceaccount a
                LEFT JOIN {$prefix}tg_source d ON  a.sourceid=d.id
                WHERE {$where}";
        $detail=M()->query($sql);

        return number_format($detail[0]['actualamount'], 2, '.', '');
    }
    /**
     * 用户税率
     * @param $user
     * @return string
     */
    public function getUserRate($user)
    {
        if ($user["invoicetype"] == 1) {
            $taxrateContent = '0.0672';
        } else if ($user["invoicetype"] == 2) {
            $taxrateContent = '0.0336';
        } else if ($user["invoicetype"] == 3) {
            $taxrateContent = '0';
        } else if ($user["invoicetype"] == 0 && $user['usertype'] ==2 ) {
            $taxrateContent = '0.0672';
        } else if ($user["invoicetype"] == 0 && $user['usertype'] ==1 ) {
            $taxrateContent = '0.03';
        }

        return $taxrateContent;
    }

}
?>