<?php
class GuideAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $userid = $_SESSION["userid"];
		if (isset($userid) && $userid > 0) {
			$account = $_SESSION["account"];
			$user = "logged";
			$this->assign('account',$account);
		} else {
			$user = "notlogged";
		}
        $Guide = D("Guide");
        $guide = $Guide->indexpage();
		$this->assign('user',$user);
        $this->assign('guide',$guide);
		$this->assign('firstguide',$guide['operation'][0]);
        $this->display();
    }
    public function guide_unlogged(){
        $userid = $_SESSION["userid"];
        if (isset($userid) && $userid > 0) {
            $account = $_SESSION["account"];
            $user = "logged";
            $this->assign('account',$account);
        } else {
            $user = "notlogged";
        }
        $Guide = D("Guide");
        $guide = $Guide->indexpage();
        $this->assign('user',$user);
        $this->assign('guide',$guide);
        $this->assign('firstguide',$guide['operation'][0]);
        $this->display();
    }

	//读取操作指南
	public function getcontent(){
        $id = $_POST["id"];
        $model= M('tg_guide');
        $guide = $model->find($id);
        if($guide){
			$this->ajaxReturn($guide["content"],'success',1);
			exit();
		}else{
			$this->ajaxReturn('fail','fail',0);
			exit();
		}
    }



}
?>