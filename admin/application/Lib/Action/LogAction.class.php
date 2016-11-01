<?php
class LogAction extends CommonAction {

    public function __construct(){
        parent::__construct();

    }

    /**
     * 登录日志页面
     *
     */

    public function index(){
        $this->authoritycheck(10125);
        $userlogmodel = M('tg_userlog');
        $userlog = $userlogmodel->order("createtime desc")->select();
        $this->assign('userlog',$userlog);
        $this->menucheck();
        $this->display();

    }


    /**
     * 操作日志页面
     *
     */
    public function operate() {
        $this->logincheck();
        $this->authoritycheck(10126);
        $model = M('tg_log');
        $operate = $model->order("createtime desc")->select();
        $this->assign('operate',$operate);
        $this->menucheck();
        $this->display();

    }

}