<?php
class StatisticsAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
        $Statistics = D("Statistics");

        $this->assign('channel',$Statistics->channel());
        
		$data = $Statistics->index();
		$todaydata = $Statistics->getTodayData($_SESSION["userid"], 0 , 0);
		$data = array_merge($todaydata,$data);
		$dataall["dailyactive"] = 0;
		$dataall["newpeople"] = 0;
		$dataall["paypeople"] = 0;
		$dataall["dailyjournal"] = 0;
		$dataall["dailyincome"] = 0;
        if (sizeof($data) > 0) {
			foreach ($data as $k => $v) {
				$dataall["dailyactive"] += $v["dailyactive"];
				$dataall["newpeople"] += $v["newpeople"];
				$dataall["paypeople"] += $v["paypeople"];
				$dataall["dailyjournal"] += $v["dailyjournal"];
				$dataall["dailyincome"] += $v["dailyincome"];
            }
			$dataall["date"] = "数据汇总";
			array_unshift($data,$dataall);
		}
        foreach($data as $k => $v){
            $data[$k]['dailyjournal'] = str_replace(",", "", number_format($v['dailyjournal'], 2));
            $data[$k]['dailyincome'] = str_replace(",", "", number_format($v['dailyincome'], 2));
        }
        $this->assign('data',$data);
        
        $this->display();
    }


    public function refresh(){
        $this->logincheck();
        $userid = $_SESSION['userid'];
        $channelid = $_POST["channelid"];
        $gameid = $_POST["gameid"];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];
        $ischannel = $_POST["ischannel"];
        $model = M("tg_dailyaccount");
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
        $condition["D.userid"] = $userid;
        $gameresult = array();
        $dailyresult = array();
        if (isset($channelid) && $channelid > 0) {
            $condition["D.channelid"] = $channelid;
            if ($ischannel == 1) {
                $sourcemodel = M("tg_source");
                $sourcecondition["S.userid"] = $userid;
                $sourcecondition["S.channelid"] = $channelid;
                $sourcecondition["S.activeflag"] = 1;
                $sourcecondition["G.activeflag"] = 1;
				$sourcecondition["G.isonstack"] = 0;
                $gamelist = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();
                if ($gamelist) {
                    foreach ($gamelist as $k => $v) {
                        $gameresult[] = "<option value=".$v["gameid"].">".$v["gamename"]."</option>";
                    }
                    array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
                } else {
                    array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
                }
            }
        } else {
            array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
        }
        if (isset($gameid) && $gameid > 0) {
            $condition["D.gameid"] = $gameid;
        }
        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }
        $condition["D.activeflag"] = 1;
        $condition['_logic'] = 'AND';
        $daily = $model->alias("D")->join(C('DB_PREFIX')."tg_game G on D.gameid = G.gameid", "LEFT")->join(C('DB_PREFIX')."tg_channel C on D.channelid = C.channelid", "LEFT")->where($condition)->order("D.createtime desc")->select();
        foreach ($daily as $k => $v) {
            $daily[$k]["date"] = date("Y年m月d日",strtotime($v["date"]." 12:00:00"));
        }
		$Statistics = D("Statistics");
		$todaydata = $Statistics->getTodayData($userid, $channelid , $gameid);
		if (isset($enddate) && $enddate != "") {
			if ($enddate == date("Y-m-d")) {
				if ($startdate == date("Y-m-d")) {
					$daily = $todaydata;
				} else {
					$daily = array_merge($todaydata,$daily);
				}
			}
		} else {
			$daily = array_merge($todaydata,$daily);
		}
        $result = array();
        $result["game"] = $gameresult;
		$dataall["dailyactive"] = 0;
		$dataall["newpeople"] = 0;
		$dataall["paypeople"] = 0;
		$dataall["dailyjournal"] = 0;
		$dataall["dailyincome"] = 0;
        if ($daily) {
            foreach ($daily as $k => $v) {
				$dataall["dailyactive"] += $v["dailyactive"];
				$dataall["newpeople"] += $v["newpeople"];
				$dataall["paypeople"] += $v["paypeople"];
				$dataall["dailyjournal"] += $v["dailyjournal"];
				$dataall["dailyincome"] += $v["dailyincome"];
            }
			$dataall["date"] = "数据汇总";
			array_unshift($daily,$dataall); 
            $result["daily"] = $daily;
            $this->ajaxReturn($result,'success',1);
            exit();
        } else {
            $this->ajaxReturn($result,'fail',0);
            exit();
        }
    }



}
?>