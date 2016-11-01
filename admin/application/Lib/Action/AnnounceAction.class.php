<?php
class AnnounceAction extends CommonAction {
	public function __construct(){
    	parent::__construct();
    }
	
	//所有公告页面显示
    public function announceall(){
		$this->logincheck();
        $this->authoritycheck(10114);
        $model= M('tg_announce');
        $condition["activeflag"] = 1;
        $announce = $model->where($condition)->order("createtime desc")->select();
        $this->assign('announce',$announce);
        $this->menucheck();
        $this->display();

    }
    //添加公告页
    public function newannounce(){
        $this->logincheck();
        $this->authoritycheck(10123);
        $model = M('tg_announcetype');
        $condition["activeflag"] = 1;
        $announcetypename = $model->where($condition)->order("createtime asc")->select();
        $this->assign('announcetypename',$announcetypename);
        $this->menucheck();
        $this->display();

    }

    //添加公告
    public function addannounce(){
		$this->logincheck();
        $model= M('tg_announce');
        $data['category'] = $_POST['category'];
        $data['title'] = $_POST['title'];
        $data['content'] = $_POST['contenttext'];
        $data['isnew'] = 1;
        $data['activeflag'] = 1;
        if(!$_POST['orderid']){
            $data['orderid'] = 0;
        }else{
           $data['orderid'] = $_POST['orderid']; 
        }
        if (isset($_POST['publishtime']) && $_POST['publishtime'] != "") {
            $data['publishtime'] = $_POST['publishtime']." 00:00:00";
        } else {
            $data['publishtime'] = date("Y-m-d H:i:s",time());
        }
        $data['createtime'] = date('Y-m-d H:i:s',time());
		$data['createuser'] = "Admin";
        $announce = $model->add($data);
        if($announce){
            $this->insertLog($_SESSION['adminname'],'添加公告', 'AnnounceAction.class.php', 'addannounce', $data['createtime'], $_SESSION['adminname']."添加了类型为“".$data['category']."”，标题为“".$data['title']."，发布时间为“".$data['publishtime']."”的公告");
            $this->ajaxReturn('success','新增公告成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','新增公告失败。',0);
			exit();
		}
    }

	//编辑页
    public function announcedetail(){
		$this->logincheck();
        $id = $_GET['id'];
		if ($id == 0) {
			Header("Location: /announceall/ ");
			exit();
		} else {
			$model= M('tg_announce');
			$announce = $model->find($id);
			$this->assign('announce',$announce);
            $this->menucheck();
			$this->display();
		}
    }

    //编辑公告方法
    public function editannounce(){
		$this->logincheck();
		$id = $_POST['announceid'];
        $model = M('tg_announce');
        $condition["id"] = $id;
        $oldannounce = $model->where($condition)->find();
		$data['category'] = $_POST['category'];
        $data['title'] = $_POST['title'];
        $data['content'] = $_POST['contenttext'];
        if(!$_POST['orderid']){
            $data['orderid'] = 0;
        }else{
           $data['orderid'] = $_POST['orderid']; 
        }
        $announce = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        if($announce){
            $this->insertLog($_SESSION['adminname'],'编辑公告', 'AnnounceAction.class.php', 'editannounce', $time, $_SESSION['adminname']."编辑了标题为“".$oldannounce['title']."”的公告");
            $this->ajaxReturn('success','公告更新成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','公告更新失败。',0);
			exit();
		}
    }

    //删除公告
    public function deleteannounce() {
		$this->logincheck();
        $id = $_POST['id'];
        $model = M('tg_announce');
		$data["activeflag"] = 0;
		$condition["id"] = $id;
        $oldannounce = $model->where($condition)->find();
		$deleteannounce = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        if($deleteannounce){
            $this->insertLog($_SESSION['adminname'],'删除公告', 'AnnounceAction.class.php', 'deleteannounce', $time, $_SESSION['adminname']."删除了标题为“".$oldannounce['title']."”的公告");
            $this->ajaxReturn('success','公告删除成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','公告删除失败。',0);
			exit();
		}
    }
	
	//查看指定日期数据
	public function viewDaterangeAnnounce(){
		$this->logincheck();
		$startdate = $_POST['startdate']." 00:00:00";
		$enddate = $_POST['enddate']." 23:59:59";
        $model= M('tg_announce');
		$condition["createtime"]  = array(array('egt',$startdate),array('elt',$enddate),'and');
		$condition["activeflag"] = 1;
		$condition['_logic'] = 'AND';
        $announce = $model->field("id,category,title,createtime")->where($condition)->order("createtime desc")->select();
		if($announce){
			$this->ajaxReturn($announce,'success',1);
			exit();
		}else{
			$this->ajaxReturn('fail','fail',0);
			exit();
		}
    }

    //公告类型页面
    public function announcetype(){
        $this->logincheck();
        $this->authoritycheck(10122);
        if($this->authoritycheck(10122) == 'ok'){
            $model= M('tg_announcetype');
            $condition["activeflag"] = 1;
            $announcetypename = $model->where($condition)->order("createtime asc")->select();
            $this->assign('announcetypename',$announcetypename);
            $this->menucheck();
            $this->display();
        }
    }
    //新增公告类型
    public function addannouncetype(){
        $this->logincheck();
        $model= M('tg_announcetype');
        $data['announcetypename'] = $_POST['announcetypename'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
        $announcetype = $model->add($data);
        if($announcetype){
            $this->insertLog($_SESSION['adminname'],'新增公告类型', 'AnnounceAction.class.php', 'addannouncetype', $data['createtime'], $_SESSION['adminname']."新增公告类型为：“".$data['announcetypename']."”");
            $this->ajaxReturn('success',$announcetype,1);
            exit();
        }else{
            $this->ajaxReturn('fail','新增公告类型失败。',0);
            exit();
        }
    }

    //删除公告类型
    public function deleteAnnouncetype(){
        $this->logincheck();
        $id = $_POST["id"];
        $model= M('tg_announcetype');
        $condition["id"] = $id;
        $data['activeflag'] = 0;
        $announcetype = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $oldannouncetype = $model->where($condition)->find();
        if($announcetype){
            $this->insertLog($_SESSION['adminname'],'删除公告类型', 'AnnounceAction.class.php', 'deleteAnnouncetype', $time, $_SESSION['adminname']."删除的公告类型为：“".$oldannouncetype['announcetypename']."”");
            $this->ajaxReturn('success','删除成功。',1);
            exit();
        }else{
            $this->ajaxReturn('fail','删除失败。',0);
            exit();
        }
    }

}
?>