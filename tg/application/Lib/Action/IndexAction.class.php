<?php
class IndexAction extends CommonAction {
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
        $Index = D("Index");
		$this->assign('user',$user);
        $this->assign('game',$Index->game());
        $this->assign('announce',$Index->announce());
        $this->display();

    }
	
	/*
    public function logged_index(){
        $this->logincheck();
        $Index = D("Index");
        $this->assign('game',$Index->game());
        $this->assign('announce',$Index->announce());
        $this->display();
    }
	*/
}
?>