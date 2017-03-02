<?php
class StatisticstgAction extends CommonAction {
    public function __construct(){
        parent::__construct();
        
        $this->logincheck();
        $this->tguserid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
        if($this->tguserid <= 0){
            $this->ajaxReturn('fail',"缺少参数",0);
        }
        $this->assign('tguserid', $this->tguserid);
    }

    public function index(){
        $this->menucheck();
        $channelmodel = M('tg_channel');

        // 母账号,获取渠道列表
        $userid = $this->tguserid;
        $map = array();
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
        $this->assign('channel',$channel);
        

        // if($this->sourcetype == 4){
            $this->display('indexcps');
        // }else{
        //     $this->display();
        // }
    }


    public function search(){
        
        $userid = $this->tguserid;
        $channelid = $_POST["channelid"]?$_POST["channelid"]:0;
        $gameid = $_POST["gameid"]?$_POST["gameid"]:0;
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
                $enddate = date('Y-m-d', time());
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
                // vde($model->getlastsql());
        // 获取今天数据
        if( strtotime($enddate)  >= strtotime( date('Y-m-d') )) { //如果结束日期，大于当前的日期
    		$Statistics = D("Statistics");
    		$todaydata = $Statistics->getTodayData($userid, $channelid , $gameid);
        
            $daily = array_merge((array)$todaydata,(array)$daily);
        }

        foreach ($daily as $k => $v) {
            $daily[$k]["date"] = date("Y年m月d日",strtotime($v["date"]." 12:00:00"));
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
            $dataall["sub_dailyincome"] = 0;


            foreach ($daily as $k => $v) {
                $dataall["dailyactive"] += $v["dailyactive"];
                $dataall["newpeople"] += $v["newpeople"];
                $dataall["paypeople"] += $v["paypeople"];
                $dataall["dailyjournal"] += $v["dailyjournal"];
                $dataall["dailyincome"] += $v["dailyincome"];
                $dataall["voucherje"] += $v["voucherje"];
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

    // cps类型用户 每日统计
    public function searchcps(){
        
        $userid = $this->tguserid;
        $channelid = $_POST["channelid"]?$_POST["channelid"]:0;
        $gameid = $_POST["gameid"]?$_POST["gameid"]:0;
        $startdate = $_POST["startdate"];
        $enddate = $_POST["enddate"];
        $choose_time = $_POST["choose_time"];

        $model = M("tg_dailyaccount");
        $condition = array(); //条件

        //根据渠道的 游戏列表
        // 时间条件
        if( !$startdate && !$enddate && !$choose_time ){ //默认第一次加载
            $startdate = date('Y-m-01', time()); //获取当前月份第一天
            $enddate = date('Y-m-d', time());
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }elseif ((isset($startdate) && $startdate != "") && (isset($enddate) && $enddate != "")) {
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }elseif(isset($choose_time) && $choose_time != ""){
            if($choose_time=='currentmonth'){
                $startdate = date('Y-m-01', time()); //获取当前月份第一天
                $enddate = date('Y-m-d', time());
            }elseif ($choose_time=='sevenday') {
                $enddate = date('Y-m-d', time());
                $startdate = date('Y-m-d', strtotime("$enddate -7 day"));  
            }elseif ($choose_time=='thirtyday') {
                $enddate = date('Y-m-d', time());
                $startdate = date('Y-m-d', strtotime("$enddate -30 days"));
            }
            $condition["D.date"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
        }


        $condition["D.userid"] = $userid;


        $condition["D.activeflag"] = 1;
        $condition['_logic'] = 'AND';


        $daily = $model->alias("D")
            ->where($condition)
            ->field('D.date,sum(D.newpeople) as newpeople,sum(D.dailyactive) as dailyactive,sum(D.paypeople) as paypeople,sum(D.dailyjournal) as dailyjournal, sum(D.dailyincome) as dailyincome, sum(D.voucherje) as voucherje')
            ->order("D.date desc")
            ->group('D.date')
            ->select();
            // vde($model->getlastsql());
        foreach ($daily as $key => $value) {
            $daily[$key]['sub_dailyincome'] = 0;
        }


        // 根据日期，汇总今天和以前的数据
        if( strtotime($enddate)  >= strtotime( date('Y-m-d') )) { //如果结束日期，大于当前的日期
            // 获取今天数据
            $Statistics = D("Statistics");
            $todaydata = $Statistics->getTodayData($userid, $channelid , $gameid);
            $today = array();
            // $today = $today[0];
            $today[0]['date'] = date('Y-m-d');
            $today[0]['datestr'] = date('Y年m月d日');
            $today[0]['newpeople'] = 0;
            $today[0]['paypeople'] = 0;
            $today[0]['dailyactive'] = 0;
            $today[0]['dailyjournal'] = 0;
            $today[0]['dailyincome'] = 0;
            $today[0]['sub_dailyincome'] = 0;
            $today[0]['voucherje'] = 0;
            foreach ($todaydata as $key => $value) {
                $today[0]['newpeople'] += $value['newpeople'];
                $today[0]['paypeople'] += $value['paypeople'];
                $today[0]['dailyactive'] += $value['dailyactive'];
                $today[0]['dailyjournal'] += $value['dailyjournal'];
                $today[0]['dailyincome'] += $value['dailyincome'];
                $today[0]['sub_dailyincome'] += $value['sub_dailyincome'];
                $today[0]['voucherje'] += $value['voucherje'];
            }

            $daily = array_merge((array)$today,(array)$daily);
        }

        foreach ($daily as $k => $v) {
            $daily[$k]["date"] = date("Y-m-d",strtotime($v["date"]));
            $daily[$k]["datestr"] = date("Y年m月d日",strtotime($v["date"]));
            $daily[$k]["dailyjournal"] = round($v['dailyjournal'],2);
            $daily[$k]["dailyincome"] = round($v['dailyincome'],2);
            $daily[$k]["sub_dailyincome"] = round($v['sub_dailyincome'],2);
        }

        $result = array(); //返回结果
        $result["daily"] = array(); //流水列表
        if ($daily) {
            // 汇总数据
            $dataall["datestr"] = "数据汇总";
            $dataall["dailyactive"] = 0;
            $dataall["newpeople"] = 0;
            $dataall["paypeople"] = 0;
            $dataall["dailyjournal"] = 0;
            $dataall["dailyincome"] = 0;
            $dataall["sub_dailyincome"] = 0;


            foreach ($daily as $k => $v) {
                $dataall["dailyactive"] += $v["dailyactive"];
                $dataall["newpeople"] += $v["newpeople"];
                $dataall["paypeople"] += $v["paypeople"];
                $dataall["dailyjournal"] += $v["dailyjournal"];
                $dataall["dailyincome"] += $v["dailyincome"];
                $dataall["voucherje"] += $v["voucherje"];

                $daily[$k]['action'] = '<a href="'.U('statisticstg/detail', array('date' => $v["date"],'uid'=>$this->tguserid)).'">查看详情</a>';
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

    // cps类型用户 查看每日详情
    public function detail(){
        $this->menucheck();
        $date = $_GET['date'];
    

        $channelmodel = M('tg_channel');

        // 母账号,获取渠道列表
        $userid = $this->tguserid;
        $map = array();
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
        $this->assign('channel',$channel);


        $this->display();
    }

}
?>