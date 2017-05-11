<?php
class SdkfenbaoAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
		$username = $_POST['username'];
		$sdkgid = $_POST['gameid'];

		//生成sdk推广信息
		$this->applyGame($username,$sdkgid);
    }

	 //信息生成
    public function applyGame($username,$sdkgid){
    	$time =  date('Y-m-d : H-i-s',time());
    	$gamemodel = M('tg_game');
    	$usermodel = M('tg_user');
    	$channelmodel = M('tg_channel');
    	$sourcemodel = M('tg_source');
    	$sdkusermodel = M('all_user');
    	$sdkagent = M('sdk_agentlist');
    	$sdkuser = $sdkusermodel->field('password')->where("username='{$username}'")->find();
    	$game = $gamemodel->where("sdkgameid={$sdkgid}")->find();
    	if(empty($game)){
    		exit(json_encode(array('code'=>'0','msg'=>'该游戏不存在')));
    	}
    	// 这样就减少导致和用户重复的机会，用户重复会对推广系统有影响
    	$new_username='sdk_'.$username; // 默认用户前面加sdk_
    	$user = $usermodel->where("account='{$new_username}' AND issdkuser=1")->find();
    	if( empty($user) ){
    		//user 用户信息
    		$data = array();
    		$data["account"] = $new_username;
			$data["usertype"] = 1;
			$data["gender"] = 0;
			$data["invoicetype"] = 0;
			$data["withdrawlimit"] = 100;
			$data['activeflag'] = 1;
			$data['createtime'] = $time;
			$data['isverified'] = 1;//默认审核用户
			$data['issdkuser'] = 1;
			$userid = $usermodel->add($data);
    	}else{
    		$userid = $user['userid'];
    	}

    	$map = array();
    	$map['userid'] = $userid;
	    $map['channelname'] = 'SDK推广';
    	$channel = $channelmodel->where($map)->find();
    	if( empty($channel) ){
    		//channel 渠道名称
			$data = array();
			$data["userid"] = $userid;
			$data["channelname"] = 'SDK推广';
			$data["gender"] = 0;
			$data["channeltype"] = 'sdk';
			$data["withdrawlimit"] = 100;
			$data['activeflag'] = 1;
			$data['createtime'] = $time;
			$data['createuser'] = 'sdk';
			$channelid = $channelmodel->add($data);
    	}else{
    		$channelid = $channel['channelid'];
    	}

    	$map = array();
    	$map['userid'] = $userid;
	    $map['gameid'] = $game['gameid'];
    	$source = $sourcemodel->where($map)->find();
    	if(empty($source)){
    		$data = array();
    		$data['activeflag'] = 1;
			$data['userid'] = $userid;
			$data['gameid'] = $game['gameid'];
			$data['channelid'] = $channelid;
			$data['createtime'] = $time;
			$packagename = $game["packagename"];
			$newgamename = createstr(30);
			$sourcesn = "tg_".$newgamename;
			$newgamename = $newgamename.".apk";
			$texturename = $game["texturename"];
			$data['sourcesn'] = $sourcesn;
			$data['sourcesharerate'] = 0.1;
			$data['sourcechannelrate'] = 0;
			$data['textureurl'] = $texturename;
			$data['isupload'] = 0;
			$data['createuser'] = 'sdk';
			$sourceid = $sourcemodel->add($data);
			$map = array('id' => $sourceid );
			$source = $sourcemodel->where($map)->find();
    	}

    	$map = array();
	    $map['agent'] = $source['sourcesn'];
    	$agent = $sdkagent->where($map)->find();
    	if(empty($agent)){
    		$data = array();
			$data['gameid'] = $sdkgid;
			$data['agent'] = $source['sourcesn'];
			$data['agentname'] = $new_username.'_SDK推广';
			$data['departmentid'] = 20;
			$data['owner'] = 'admin';
			$data['username'] = 'admin';
			$data['cpa_price'] = 0;
			$data['rate'] = $game["sharerate"];
			$data['create_time'] = time();
			$data['supcard'] = 0;
			$sdkagent->add($data);
    	}

    	$returnData = array(
    		'sourceid' => $source['id'],
    		'gamename' => $game['gamename'],
    		'gameicon' => $this->iconurl.$game['gameicon'],
    		'description' => $game['description'],
    	);
    	exit(json_encode(array('code'=>'1','msg'=>'分包成功','data'=>$returnData)));
    }

}
?>