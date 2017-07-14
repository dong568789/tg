<?php
class ChannelAction extends CommonAction {
    public function __construct(){
        parent::__construct();

        // 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }
    }

    //用户所有渠道
    public function index(){
        $this->logincheck();
        $Channel = D('Channel');
        $this->assign('channel',$Channel->index());

        $this->assign('defaultChannelname',C('app_fastapply_channelname'));

        $this->display();
    }

    //用户新建渠道页面
    public function new_channel(){
        $this->logincheck();

        $userid = $_SESSION['userid'];
  
        $model= M('tg_channel');
        $where = array('userid' => $userid);
        $count = $model->where($where)->count();
        $count += 1;
        $default_account = $count.'@'.$userid;
        $default_account = str_pad($default_account,6,'0',STR_PAD_LEFT);
        $this->assign('default_account',$default_account);

        $this->display();
    }

    //用户新建渠道方法
    public function addchannel(){
        $this->logincheck();

        $userid = $_SESSION['userid'];
        $id = $_POST['channelid'];
        $channelname =  trim($_POST['channelname']);
        $sub_account = trim($_POST['sub_account']);
        $sub_password = trim($_POST['sub_password']);

        $user_model = M('tg_user');
        $model= M('tg_channel');

        if(!$channelname){
            $this->ajaxReturn("fail",'渠道名不能为空',0);
        }
        if($channelname==C('app_fastapply_channelname')){
            $this->ajaxReturn("fail",'CHANNEL_CO渠道属于系统渠道，不可以创建。',0);
        }
        if( !$sub_account ){
            $this->ajaxReturn("fail",'子账号用户名不能为空',0);
        }
        if( preg_match('/^[0-9a-zA-Z@_]{6,20}$/', $sub_account) === false){
            $this->ajaxReturn("fail",'子账号用户名必须由3-20字母、数字、_、@组成',0);
        } 
        if( !$sub_password  ){
            $this->ajaxReturn('fail','子账号密码不能为空',0);
        }  

        $condition = array();
        $condition["userid"] = $userid;
        $condition['channelname'] = $channelname;
        $condition['activeflag'] = 1;
        $exischannelname = $model->where($condition)->count();

        if ($exischannelname > 0) {
            $this->ajaxReturn("fail",'渠道名已存在，请输入一个新的渠道名。',0);
        } 

        $where = ' binary account="'.$sub_account.'" ';
        $user_count = $user_model->where($where)->count();
        if($user_count > 0){
            $this->ajaxReturn('fail','该子账号已经被占用',0);
        }

        $data = array();
        $data['userid'] = $userid;
        $data['channelname'] = $_POST['channelname'];
        $data['channeltype'] = $_POST['channeltype'];
        $data['channelsize'] = $_POST['channelsize'];
        $data['description'] = $_POST['description'];
        $data['gamecount'] = 0;
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $new_channelid = $model->add($data);
        if($new_channelid === false){
            $this->ajaxReturn('fail','添加渠道失败。',0);
            exit();
        }

        // 添加子账号用户名密码
        $data = array(
            'account' => $sub_account,
            'password' => sha1($sub_password),
            'channelid' => $new_channelid,
            'pid' => $userid,
            'isverified' => 1,
            'activeflag' => 1,
            'createtime' => date('Y-m-d H:i:s',time()),
            'createuser' => $_SESSION['account'],
        );
        $result = $user_model->add($data);
        if($result === false){
            $this->ajaxReturn("fail",'添加子账号失败。',0);
        }

        $this->insertLog($_SESSION['account'],'新建渠道', 'ChannelAction.class.php', 'addchannel', $data['createtime'], $_SESSION['account']."新建了“".$_POST['channelname']."”渠道");

        $this->insertLog($_SESSION['account'],'新建子账号', 'ChannelAction.class.php', 'addchannel', $data['createtime'], $_SESSION['account']."新建了“".$sub_account."”子账号，".json_encode($data)."");
        $this->ajaxReturn('success','新增成功',1);
        exit();
    }

    //编辑页
    public function channeldetail(){
        $this->logincheck();
        $id = $_GET['id'];
        $userid = $_SESSION['userid'];

        $map['C.channelid'] = $id;
        $map['C.userid'] = $userid;
        $model= M('tg_channel');
        $channel = $model->alias('C')
                ->field('C.*,U.account as sub_account')
                ->join('yx_tg_user U on U.channelid=C.channelid','left')
                ->where($map)->find();
        $this->assign('channel',$channel);
        $this->display();
    }

    //编辑渠道方法
    public function editchannel(){
        $this->logincheck();
        
        $userid = $_SESSION['userid'];
        $id = $_POST['channelid'];
        $channelname =  trim($_POST['channelname']);
        $sub_account = trim($_POST['sub_account']);
        $sub_password = trim($_POST['sub_password']);

        $user_model = M('tg_user');
        $model= M('tg_channel');

        if(!$channelname){
            $this->ajaxReturn("fail",'渠道名不能为空',0);
        }
        if($channelname==C('app_fastapply_channelname')){
            $this->ajaxReturn("fail",'CHANNEL_CO渠道属于系统渠道，不可以创建。',0);
        }
        if( !$sub_account ){
            $this->ajaxReturn("fail",'子账号用户名不能为空',0);
        }
        if( preg_match('/^[0-9a-zA-Z@_]{6,20}$/', $sub_account) === false){
            $this->ajaxReturn("fail",'子账号用户名必须由6-20字母、数字、_、@组成',0);
        }   

        // 获取以前该用户的渠道名
        $where = array('channelid'=>$id);
        $oldchannel = $model->field('channelname')->where($where)->find();
        // 获取以前的子渠道用户名
        $olduser = $user_model->field('account')->where($where)->find();

        $condition = array();
        $condition["userid"] = $userid;
        $condition['channelname'] = array(
            array('neq',$oldchannel['channelname']),
            array('eq',$channelname),
        );
        $condition['activeflag'] = 1;
        $exischannelname = $model->where($condition)->count();
        if ($exischannelname > 0) {
            $this->ajaxReturn("fail",'渠道名已存在，请输入一个新的渠道名。',0);
        } 

        $where = ' binary account="'.$sub_account.'" and !(channelid="'.$id.'" and account="'.$olduser['account'].'") ';
        $user_count = $user_model->where($where)->count();
        if($user_count > 0){
            $this->ajaxReturn('fail','该子账号已经被占用',0);
        }

        // 更新渠道
        $data = array();
        $data['channelname'] = $channelname;
        $data['channeltype'] = $_POST['channeltype'];
        $data['channelsize'] = $_POST['channelsize'];
        $data['description'] = $_POST['description'];
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $channel = $model->where("channelid = '$id'")->save($data);
        if($channel === false){
            $this->ajaxReturn('fail','渠道更新失败。',0);
        }

        // 更新子账号用户名密码
        $where = array('channelid'=>$id);
        $data = array('account' => $sub_account);
        if($sub_password){
            $data = array('password' => sha1($sub_password));
        }
        $result = $user_model->where($where)->save($data);
        if($result === false){
            $this->ajaxReturn("fail",'编辑子账号失败。',0);
        }

        if($oldchannel['channelname'] == $channelname){

            $this->insertLog($_SESSION['account'],'编辑渠道', 'ChannelAction.class.php', 'editchannel', $data['createtime'], $_SESSION['account']."编辑了“".$channelname."”渠道,更新子账号用户名为“".$sub_account."”");
        } else{
            $this->updateAgentName($id, $channelname);
            $this->insertLog($_SESSION['account'],'编辑渠道', 'ChannelAction.class.php', 'editchannel', $data['createtime'], $_SESSION['account']."编辑了“".$oldchannel['channelname']."”渠道，渠道名更新为“".$channelname."”,更新子账号用户名为“".$sub_account."”");
        }

        $this->ajaxReturn('success','编辑成功',1);
    }

    /**
     * 更新agentlist agentname
     */
    protected function updateAgentName($channelid,$channel_name)
    {
        $agentlistModel = M('sdk_agentlist');
        $sourceModel = M('tg_source');
        $tgUserModel = M('tg_user');

        $account = $tgUserModel->where(array('userid' => $_SESSION['userid']))->getField('account');
        $source = $sourceModel->where(array('channelid' => $channelid))->field('sourcesn')->select();
        $sourcesn = getFieldArray($source, 'sourcesn');

        $where = array(
            'agent' => array('in', $sourcesn)
        );
        $res = $agentlistModel->where($where)->save(array(
            'agentname' => $account.'_'.$channel_name
        ));

        return $res;
    }

    public function updateBaiBao()
    {
        $account = 'yxtest';
        $isupload = 0;
        $activeflag = 1;
        $userid = M('tg_user')->where(array('account'=> $account))->getField('userid');
        $channelid = M('tg_user')->where(array('pid' => $userid))->getField('channelid');

        $gameid = isset($_GET['gameid']) && $_GET['gameid'] > 0 ? (int)$_GET['gameid'] : 0;
        if($gameid > 0){
            $where = " AND a.gameid={$gameid}";
        }

        $sql = "insert into `yx_tg_source`(userid,gameid,channelid,createtime,sourcesn,activeflag,textureurl,isupload,sourcesharerate,sourcechannelrate,createuser)
                SELECT
                    {$userid} as userid,
                    b.gameid,
                    {$channelid} as channelid,
                    FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') AS createtime,
                    a.agent,
                    {$activeflag} as activeflag,
                    b.texturename,
                    {$isupload} as isupload,
                    b.sharerate as sourcesharerate,
                    b.channelrate as sourcechannelrate,
                    '{$account}' as createuser
                FROM
                    yx_sdk_agentlist a
                INNER JOIN yx_tg_game b ON a.gameid = b.sdkgameid
                WHERE
                   a.agentname like '%白包'
                AND a.agent LIKE '%-01' {$where}";
        $res = M('')->query($sql);
        if($res){
            echo 'success';
        }else{
            echo 'error';
        }
    }


    public function updateCpsBaiBao()
    {
        $account = 'yxtest';
        $isupload = 0;
        $activeflag = 1;
        $userid = M('tg_user')->where(array('account'=> $account))->getField('userid');
        $channelid = M('tg_user')->where(array('pid' => $userid))->getField('channelid');

        $gameid = isset($_GET['gameid']) && $_GET['gameid'] > 0 ? (int)$_GET['gameid'] : 0;
        if($gameid > 0){
            $where['gameid'] = $gameid;
        }

        $game = M('cps_game')->where($where)->find();

        $agent  = $game['gamepinyin']."-{$gameid}-01";

        $source = M('cps_source');
        $source->userid = $userid;
        $source->gameid = $gameid;
        $source->channelid = $channelid;
        $source->createtime = date("Y-m-d H:i:s");
        $source->sourcesn = $agent;
        $source->activeflag = $activeflag;
        $source->textureurl = '';
        $source->isupload = $isupload;
        $source->sourcesharerate = $game['sharerate'];
        $source->sourcechannelrate = $game['channelrate'];
        $source->createuser = "admin";

        $res = $source->add();

        echo "agent：{$agent}";
        echo "<br />";
        echo "gameid：{$gameid}";
        echo "<br />";
        echo "channelid：{$channelid}";
        echo "<br />";
        if($res){
            echo 'success';
        }else{
            echo 'error';
        }
    }

    //删除渠道方法
    public function deletechannel(){
        $this->logincheck();
        $id = $_POST['id'];
        $model= M('tg_channel');
        $data['activeflag'] = 0;
        $time = date('Y-m-d H:i:s',time());
        $oldchannel = $model->where("channelid = '$id'")->find();
        $channel = $model->where("channelid = '$id'")->save($data);
        if($channel){
            $this->insertLog($_SESSION['account'],'删除渠道', 'ChannelAction.class.php', 'deletechannel', $time, $_SESSION['account']."删除了“".$oldchannel['channelname']."”渠道");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

    // 批量生成子账号
    public function batchCreateSubuser(){
        // exit();
        $channelModel = M('tg_channel');
        $userModel = M('tg_user');

        // 已经生成子账号的渠道
        $where = array();
        $where['channelid'] = array('gt','0');
        $user = $userModel->field('channelid')->where($where)->select();
        if($user){
            $where = array();
            $channelidArr = array();
            foreach ($user as $key => $value) {
                $channelidArr[] = $value['channelid'];
            }
            $where['C.channelid'] = array('not in',$channelidArr);

            // 给没有生产子账号的渠道，生成子账号   
            $channel = $channelModel->alias('C')
                    ->join(C('DB_PREFIX').'tg_user U on C.userid=U.userid','left')
                    ->where($where)
                    ->field('C.channelid,C.userid,U.account')
                    ->select();
        }else{
            // 给没有生产子账号的渠道，生成子账号   
            $channel = $channelModel->alias('C')
                    ->join(C('DB_PREFIX').'tg_user U on C.userid=U.userid','left')
                    ->field('C.channelid,C.userid,U.account')
                    ->select();
        }

        foreach ($channel as $key => $value) {
            // 添加子账号用户名密码
            $where = array('userid'=>$value['userid']);
            $where['channelid'] = array('elt',$value['channelid']);
            $count = $channelModel->where($where)->order('channelid asc')->count();
            $sub_account = $count.'@'.$value['userid'];
            $sub_account = str_pad($sub_account,6,'0',STR_PAD_LEFT);
            $data = array(
                'account' => $sub_account,
                'password' => '',
                'channelid' => $value['channelid'],
                'pid' => $value['userid'],
                'isverified' => 1,
                'activeflag' => 1,
                'createtime' => date('Y-m-d H:i:s',time()),
                'createuser' => $value['account'],
            );
            $result = $userModel->add($data);
            if($result === false){
                echo '添加channelid为'.$value['id'].'子账号失败';
                exit();
            }
        }

        echo '添加子账号成功';
        exit();
    }

}
?>