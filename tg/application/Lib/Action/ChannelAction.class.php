<?php
class ChannelAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    //用户所有渠道
    public function index(){
        $this->logincheck();
        $Channel = D('Channel');
        $this->assign('channel',$Channel->index());

                $userid = $_SESSION['userid'];
        $channelmodel = M('tg_channel');
        $map['userid'] =$userid;
        $map["activeflag"] = 1;
        $channel = $channelmodel->where($map)->select();
        $log_content=date('Y-m-d H:i:s')."\n";
$log_content.='userid：'.$userid."\n";
$log_content.='exsitsn2：'.print_r($channel,1)."\n";
$log_content.='sql：'.$channelmodel->getlastsql()."\n";
error_log($log_content, 3, 'test.log');

        $this->assign('defaultChannelname',C('app_fastapply_channelname'));

        $this->display();
    }
    //用户新建渠道页面
    public function new_channel(){
        $this->logincheck();
        $this->display();
    }
    //用户新建渠道方法
    public function addchannel(){
        $this->logincheck();
        $model= M('tg_channel');
        $userid = $_SESSION['userid'];
        $channelname =  $_POST['channelname'];

        if($channelname==C('app_fastapply_channelname')){
            $this->ajaxReturn("fail",'CHANNEL_CO渠道属于系统渠道，不可以创建。',0);
            exit();
        }

        $condition["userid"] = $userid;
        $condition["channelname"] = $channelname;
        $condition["activeflag"] = 1;
        $exischannelname = $model->where($condition)->count();
        if ($exischannelname > 0) {
            $this->ajaxReturn("fail",'渠道名称已存在，请输入一个新的渠道名。',0);
            exit();
        } else {
            $data['userid'] = $userid;
            $data['channelname'] = $_POST['channelname'];
            $data['channeltype'] = $_POST['channeltype'];
            $data['channelsize'] = $_POST['channelsize'];
            $data['description'] = $_POST['description'];
            $data['gamecount'] = 0;
            $data['activeflag'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s',time());
            $channel = $model->add($data);
            $result = $model->field('channelid,channelname,channeltype,channelsize,description,createtime')->where($data)->find();
            if($channel){
                $this->insertLog($_SESSION['account'],'新建渠道', 'ChannelAction.class.php', 'addchannel', $data['createtime'], $_SESSION['account']."新建了“".$data['channelname']."”渠道");
                $this->ajaxReturn('success',$result,1);
                exit();
            }else{
                $this->ajaxReturn('fail','添加失败。',0);
                exit();
            }
        }
    }

    //编辑页
    public function channeldetail(){
        $this->logincheck();
        $id = $_GET['id'];
        $userid = $_SESSION['userid'];
        $map['channelid'] = $id;
        $map['userid'] = $userid;
        $model= M('tg_channel');
        $channel = $model->field()->where($map)->find();
        $this->assign('channel',$channel);
        $this->display();
    }
    //编辑渠道方法
    public function editchannel(){
        $this->logincheck();
        $model= M('tg_channel');
        $userid = $_SESSION['userid'];
        $channelname =  $_POST['channelname'];
        $id = $_POST['channelid'];

        if($channelname==C('app_fastapply_channelname')){
            $this->ajaxReturn("fail",'CHANNEL_CO渠道属于系统渠道，不可以创建。',0);
            exit();
        }

        // 获取本身的渠道
        $oldone=$model->field('channelname')->where('channelid='.$id)->find();

        $condition["userid"] = $userid;
        $condition["channelname"] = $channelname;
        $condition['activeflag'] = 1;
        $exischannelname = $model->where($condition)->count();
        if ($exischannelname > 1) {
            $this->ajaxReturn("fail",'渠道名已存在，请输入一个新的渠道名。',0);
            exit();
        } else{
            $data['channelname'] = $_POST['channelname'];
            $data['channeltype'] = $_POST['channeltype'];
            $data['channelsize'] = $_POST['channelsize'];
            $data['description'] = $_POST['description'];
            $data['createtime'] = date('Y-m-d H:i:s',time());
            $oldchannel = $model->where("channelid = '$id'")->find();
            $channel = $model->where("channelid = '$id'")->save($data);
            if($channel){
                if($oldchannel['channelname'] == $data['channelname']){
                    $this->insertLog($_SESSION['account'],'编辑渠道', 'ChannelAction.class.php', 'editchannel', $data['createtime'], $_SESSION['account']."编辑了“".$data['channelname']."”渠道");
                } else{
                    $this->insertLog($_SESSION['account'],'编辑渠道', 'ChannelAction.class.php', 'editchannel', $data['createtime'], $_SESSION['account']."编辑了“".$oldchannel['channelname']."”渠道，渠道名更新为“".$data['channelname']."”");
                }

                $this->ajaxReturn('success',$data,1);
                exit();
            }else{
                $this->ajaxReturn('fail','渠道更新失败。',0);
                exit();
            }
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


}
?>