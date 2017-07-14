<?php
class SourceAction extends CommonAction {

	protected $tguserid;

    public function __construct(){
        parent::__construct();

		$this->logincheck();
		$this->tguserid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
		if($this->tguserid <= 0){
			$this->ajaxReturn('fail',"缺少参数",0);
		}
		$this->assign('tguserid', $this->tguserid);
    }

    // 资源列表
    public function index(){
		$this->menucheck();
        $Index = D("Source");
       
		$game = $Index->index($this->tguserid);

		$this->assign('customRateRight',$this->authoritycheck(10136));//自定义资源费率
		$this->assign('seeDevelopRight',$this->authoritycheck(10138));//查看推广
		$this->assign('childRateRight',$this->authoritycheck(10224));//自定义子账号费率
		$this->assign('applySourceRight',$this->authoritycheck(10225));//申请资源
		$this->assign('category',$game['category']);
		$this->assign('tag',$game['tag']);
		$this->assign('channel',$game['channel']);
		$this->assign('gamestr',$game['gamestr']);
		$this->assign('sourcestr',$game['sourcestr']);//我的推广
		$this->display();
        
    }

	//申请
    public function applyGame(){
		if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}

		$gameid = $_POST["game"];
		$channelid = $_POST["channel"];
        $userid = $this->tguserid;
		$usermodel = M('tg_user');
        $sourcemodel = M('tg_source');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');
		$agentmodel = M('sdk_agentlist');
		$condition['userid'] = $userid;
        $condition['gameid'] = $gameid;
        $condition['channelid'] = $channelid;
		$condition['activeflag'] = 1;
		$source = $sourcemodel->where($condition)->find();
		if($source) {
			$this->ajaxReturn('fail',"您已经申请过该资源。",0);
			exit();
		} else {
			$game = $gamemodel->find($gameid);
			$user = $usermodel->find($userid);
			$data['activeflag'] = 1;
			$data['userid'] = $userid;
			$data['gameid'] = $gameid;
			$data['channelid'] = $channelid;
			$data['createtime'] = date('Y-m-d : H-i-s',time());
			$packagename = $game["packagename"];
			$newgamename = createstr(30);
			$sourcesn = "tg_".$newgamename;
			$newgamename = $newgamename.".apk";
			$texturename = $game["texturename"];
			$data['sourcesn'] = $sourcesn;
			if ($game["sharerate"] != "") {
				$data['sourcesharerate'] = $game["sharerate"];
			}
			if ($game["channelrate"] != "") {
				$data['sourcechannelrate'] = $game["channelrate"];
			}
			$data['textureurl'] = $texturename;
			$data['isupload'] = 0;
			$data['createuser'] = $user["realname"];
			$sourceid = $sourcemodel->add($data);
			$agentdata["gameid"] = $game["sdkgameid"];
			$agentdata["agent"] = $sourcesn;
			$channel = $channelmodel->find($channelid);
			$agentdata["agentname"] = $user["account"]."_".$channel["channelname"];
			$agentdata["departmentid"] = 20;
			$agentdata["owner"] = "Admin";
			$agentdata["username"] = "Admin";
			$agentdata["cpa_price"] = 0;
			$agentdata["rate"] = $game["sharerate"];
			$agentdata["create_time"] = time();
			$agentid = $agentmodel->add($agentdata);
            $time = date('Y-m-d H:i:s',time());
			if ($sourceid && $agentid) {
				$inccondition["channelid"] = $channelid;
				$channelmodel->where($inccondition)->setInc('gamecount');
                $this->insertLog($_SESSION['account'],'申请资源', 'SourceAction.class.php', 'applyGame', $time, "用户".$_SESSION['account']."在“".$channel["channelname"]."”渠道下申请了“".$game['gamename']."”游戏");
                $this->ajaxReturn('success',$data,1);
				exit();
			} else {
				$this->ajaxReturn('fail',"失败，请联系管理员。",0);
				exit();
			}
		}
    }

    //下载素材包
    public function downloadTextture(){
        $sourcesn = $_POST["source"];
        $sourcemodel = M('tg_source');
        $gamemodel = M('tg_game');
        $channelmodel = M('tg_channel');

        $map["sourcesn"] = $sourcesn;
        $source = $sourcemodel->where($map)->find();
        // $texturename = $source['textureurl'];
        $gameid = $source['gameid'];
        $channelid = $source['channelid'];

        // 改成获取游戏的素材包
        $oldgame = $gamemodel->where("gameid = '$gameid'")->find();
        $texturename = $oldgame['texturename'];

        $oldchannel = $channelmodel->where("channelid = '$channelid'")->find();

        $time = date('Y-m-d H:i:s',time());
        if($texturename){
            $this->insertLog($_SESSION['account'],'下载素材包', 'SourceAction.class.php', 'downloadTextture', $time, "用户".$_SESSION['account']."在“".$oldchannel["channelname"]."”渠道下下载了“".$oldgame['gamename']."”素材包");
            $this->ajaxReturn('success',$this->texturedownloadurl.$texturename,1);
            exit();
        } else{
            $this->ajaxReturn('fail','该素材包不存在',0);
            exit();
        }
    }

    //游戏分类筛选，以及渠道筛选
    public function selectGame(){
        $this->logincheck();
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $gametype = $_POST['gametype'];
        $gamecategory = $_POST['gamecategory'];
        $gamesize = $_POST['gamesize'];
        $gametag = $_POST['gametag'];
        $channelid = $_POST['gamechannel'];
        $order = $_POST['order'];
        $order_hot = $_POST['order_hot'];
        $Index = D("Source");
        $gamestr = $Index->selectGame($gametype,$gamecategory,$gamesize,$gametag,$channelid,$order,$order_hot,$this->tguserid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
    }

    //搜索游戏
    public function searchGame(){
        $this->logincheck();
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $content = $_POST['content'];
		$channelid = $_POST['channelid'];
        $Index = D("Source");
        $gamestr = $Index->searchGame($content,$channelid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
    }

	//tab2选择渠道进行搜索
    public function selectSource(){
        $this->logincheck();
        if (!$this->isPost()){
			$this->ajaxReturn('fail',"非法访问",0);
		}
        $channelid = isset($_POST['channelid'])? (int)$_POST['channelid'] : 0;
		$content = isset($_POST['content']) ? $_POST['content'] : '';
		if($channelid <= 0){
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
        $Index = D("Source");
        $gamestr = $Index->searchSource($content,$channelid);
        if($gamestr){
			$this->ajaxReturn('success',$gamestr,1);
			exit();
		}else{
			$this->ajaxReturn('fail','没有数据。',0);
			exit();
		}
    }

	// --------------推广----------------------------------------------
    // 用户-资源-推广链接
    public function material(){
    	$this->logincheck();
  
    	$sourceid = $_GET['sourceid'];

    	$prefix = C('DB_PREFIX');
		$where=' and a.id="'.$sourceid.'"';
        $sql="SELECT
        		b.gameid,
                b.gamepinyin
        FROM {$prefix}tg_source a
        LEFT JOIN {$prefix}tg_game b ON a.gameid=b.gameid
        WHERE 1 ".$where;
        $result=M()->query($sql);
        $game=$result[0];

        $Source = D('Source');
    	$long_url = $Source->getDownloadURL($sourceid);
    	$short_url = $Source->shortenSinaUrl($long_url);

    	$image = $Source->create_erweima($long_url,$game['gamepinyin']);

    	$this->assign("long_url",$long_url);
        $this->assign("short_url",$short_url);
        $this->assign("image",$image);

        $this->assign("gamepinyin",$game['gamepinyin']);
        $this->assign("sourceid",$sourceid);

        $this->display();
    }

	/**
	 * 获取游戏推广链接
	 */
	public function getGameDowUrl()
	{
		$this->logincheck();

		$sourceid = isset($_POST['sourceid']) ? trim($_POST['sourceid']) : 0;
		$Source = D('Source');
		$data['long_url'] = $Source->getDownloadURL($sourceid);
		$data['short_url'] = $Source->shortenSinaUrl($data['long_url']);
		$data['status'] = 1;

		$this->ajaxReturn($data, 'JSON');
	}

    // 用户-资源-推广链接-手机页面
    public function page(){
    	$sourceid = $_GET['sourceid'];
	
        $prefix = C('DB_PREFIX');
        $where=' and a.id="'.$sourceid.'"';
        $sql="SELECT
                b.*,
                c.categoryname
        FROM {$prefix}tg_source a
        LEFT JOIN {$prefix}tg_game b ON b.gameid=a.gameid
        LEFT JOIN {$prefix}tg_gamecategory c ON b.gamecategory=c.id
        WHERE 1 ".$where;
        $result=M()->query($sql);
        $game=$result[0];
        $yel_num=intval($game['score']/2);//黄星星个数
        $half_num=$game['score']%2;//半星星个数
        $grey_num=5-$yel_num-$half_num;//灰星星个数
        $this->assign("game",$game);
        $this->assign("yel_num",$yel_num);
        $this->assign("half_num",$half_num);
        $this->assign("grey_num",$grey_num);

        $Source = D('Source');
        $long_url = $Source->getDownloadURL($sourceid);
        $long_url .= '/'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $this->assign("long_url",$long_url);

        $this->assign("sourceid",$sourceid);

        $this->display();
    }

    public function llq(){
        $this->display();
    }
}
?>