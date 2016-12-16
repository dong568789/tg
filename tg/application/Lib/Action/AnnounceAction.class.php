<?php
class AnnounceAction extends CommonAction {
    public function __construct(){
        parent::__construct();

        // 如果是子账号没有进入这里的权限
        if(isset($this->userpid) && $this->userpid>0){
            Header("Location: /source/ ");
            exit();
        }
    }

    //公告首页
    public function index(){
        $this->logincheck();
        $announcemodel = D("Announce");
        $announce = $announcemodel->index();
        $this->assign('announce',$announce);
        $this->display();
    }

    //公告详情
    public function announcedetail(){
        $this->logincheck();
		$id = $_GET['id'];
        $announcemodel = D("Announce");
		$announce = $announcemodel->announcedetail($id);
		$this->assign('announce',$announce);
		$this->display();
    }


    //未登录公告首页
    public function announce_unlogged(){
        $announcemodel = D("Announce");
        $announce = $announcemodel->index();
        $this->assign('announce',$announce);
        $this->display();
    }

    //未登录公告详情
    public function announcedetail_unlogged(){
        $announcemodel = D("Announce");
        $id = $_GET['id'];
		$announce = $announcemodel->announcedetail($id);
		$this->assign('announce',$announce);
		$this->display();
    }


}
?>