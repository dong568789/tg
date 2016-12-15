<?php
class StatisticsAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->logincheck();
    
        if($this->userpid=='0'){
            $channelmodel = M('tg_channel');

            // 母账号,获取渠道列表
            $userid = $_SESSION['userid'];
            $map = array();
            $map['userid'] =$userid;
            $map["activeflag"] = 1;
            $channel = $channelmodel->where($map)->select();
            $this->assign('channel',$channel);
        }

        $this->display();
    }


    public function search(){
        $this->logincheck();
        $userid = $_SESSION['userid'];
        $channelid = $_POST["channelid"];
        $gameid = $_POST["gameid"];
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];
        $choose_time = $_POST["choose_time"];

        $model = M("tg_dailyaccount");
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
        $sourcemodel = M('tg_source');
        $condition = array(); //条件

        //根据渠道的 游戏列表
        // 渠道条件
        $gameresult = array(); //游戏列表
        if (isset($channelid) && $channelid > 0) {
            $condition["D.channelid"] = $channelid;

            $sourcecondition = array();
            $sourcecondition["S.channelid"] = $channelid;
            $sourcecondition["S.activeflag"] = 1;
            $sourcecondition["G.activeflag"] = 1;
            $sourcecondition["G.isonstack"] = 0;
            $gamelist = $sourcemodel->alias("S")->join(C('DB_PREFIX')."tg_game G on S.gameid = G.gameid", "LEFT")->where($sourcecondition)->order("S.createtime desc")->select();

            if ($gamelist) {
                foreach ($gamelist as $k => $v) {
                    $checkstate='';
                    if (isset($gameid) && $gameid > 0) { //如果有选择游戏
                        if($gameid==$v["gameid"]){
                            $checkstate=' selected="selected" ';
                        }
                    }
                    $gameresult[] = "<option value=".$v["gameid"].$checkstate.">".$v["gamename"]."</option>";
                }
                array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
            } else {
                array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
            }
        } else {
            $condition["D.userid"] = $userid;
            array_unshift($gameresult, "<option value=\"0\">所有游戏</option>");
        }
        
        // 游戏条件
        if (isset($gameid) && $gameid > 0) {
            $condition["D.gameid"] = $gameid;
        }

        // 时间条件
        if ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }elseif(isset($choose_time) && $choose_time != ""){
            if($choose_time=='currentmonth'){
                $startdate = date('Y-m-01', time()); //获取当前月份第一天
                $enddate = date('Y-m-d', strtotime("$startdate +1 month -1 day"));    //加一个月减去一天 
            }elseif ($choose_time=='sevenday') {
                $enddate = date('Y-m-d', time());
                $startdate = date('Y-m-d', strtotime("$enddate -7 day"));  
            }elseif ($choose_time=='thirtyday') {
                $enddate = date('Y-m-d', time());
                $startdate = date('Y-m-d', strtotime("$enddate -30 days"));
            }
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }

        $condition["D.activeflag"] = 1;
        $condition['_logic'] = 'AND';

        $daily = $model->alias("D")
                ->join(C('DB_PREFIX')."tg_game G on D.gameid = G.gameid", "LEFT")
                ->join(C('DB_PREFIX')."tg_channel C on D.channelid = C.channelid", "LEFT")
                ->join(C('DB_PREFIX')."tg_source S on D.sourceid = S.id", "LEFT")
                ->where($condition)
                ->field('D.*,G.gamename,C.channelname,S.sub_share_rate,S.sub_channel_rate')
                ->order("D.createtime desc")
                ->select();

        foreach ($daily as $k => $v) {
            $daily[$k]["date"] = date("Y年m月d日",strtotime($v["date"]." 12:00:00"));
        }

        // 获取今天数据
		$Statistics = D("Statistics");
		$todaydata = $Statistics->getTodayData($userid, $channelid , $gameid);
        // 根据日期，汇总今天和以前的数据
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
        
        $result = array(); //返回结果
        $result["game"] = $gameresult;//根据渠道的 游戏列表
        $result["daily"] = array(); //流水列表
		
        if ($daily) {
            // 汇总数据
            $dataall["date"] = "数据汇总";
            $dataall["dailyactive"] = 0;
            $dataall["newpeople"] = 0;
            $dataall["paypeople"] = 0;
            $dataall["dailyjournal"] = 0;
            $dataall["dailyincome"] = 0;
            $dataall["sub_dailyincome"] = 0;

            if(isset($this->userpid) && $this->userpid>0){ //子账号
                foreach ($daily as $k => $v) {
    				$dataall["dailyactive"] += $v["dailyactive"];
    				$dataall["newpeople"] += $v["newpeople"];
    				$dataall["paypeople"] += $v["paypeople"];
    				$dataall["dailyjournal"] += $v["dailyjournal"];
    				
                    $cacheincome = $v['dailyjournal'] * $v['sub_share_rate'] * (1 - $v['sub_channel_rate']);
                    $daily[$k]['sub_dailyincome'] = str_replace(",", "", number_format($cacheincome, 2)); //总收入;

                    $dataall["sub_dailyincome"] += $daily[$k]["sub_dailyincome"];
                }
            }else{
                foreach ($daily as $k => $v) {
                    $dataall["dailyactive"] += $v["dailyactive"];
                    $dataall["newpeople"] += $v["newpeople"];
                    $dataall["paypeople"] += $v["paypeople"];
                    $dataall["dailyjournal"] += $v["dailyjournal"];
                    $dataall["dailyincome"] += $v["dailyincome"];
                }
            }
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