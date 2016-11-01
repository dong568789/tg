<?php
class GuideAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }
	
	//新增操作指南页面
    public function newguide(){
		$this->logincheck();
        $this->authoritycheck(10116);
        $this->menucheck();
        $this->display();
    }

	//新增操作指南
    public function addguide(){
		$this->logincheck();
        $model= M('tg_guide');
        $data['category'] = $_POST['category'];
        $data['title'] = $_POST['title'];
        $data['content'] = $_POST['contenttext'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
		$data['createuser'] = "Admin";
        $guide = $model->add($data);
        if($guide){
            $this->insertLog($_SESSION['adminname'],'添加操作指南', 'GuideAction.class.php', 'addguide', $data['createtime'], $_SESSION['adminname']."添加了类型为“".$data['category']."”，标题为“".$data['title']."”的操作指南");
            $this->ajaxReturn('success','操作指南新增成功。',1);
            exit();
		}else{
			$this->ajaxReturn('fail','操作指南新增失败。',0);
			exit();
		}
    }
	
	//操作指南详情
    public function guidedetail(){
		$this->logincheck();
        $this->authoritycheck(10121);
        $model= M('tg_guide');
		$condition["activeflag"] = 1;
		$condition["category"] = "操作教程";
        $operation = $model->where($condition)->order('createtime asc')->select();
		$condition["category"] = "常见问题";
        $question = $model->where($condition)->order('createtime asc')->select();
        $this->assign('operation',$operation);
        $this->assign('question',$question);
        $this->menucheck();
        $this->display();

    }

	//读取操作指南
	public function getcontent(){
		$this->logincheck();
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
	
	//编辑操作指南
    public function editguide(){
		$this->logincheck();
		$id = $_POST["id"];
        $model= M('tg_guide');
		$data['content'] = $_POST['contenttext'];
		$condition["id"] = $id;
        $guide = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $guidecontent = $model->where($condition)->find();
        if($guide){
            $this->insertLog($_SESSION['adminname'],'编辑操作指南', 'GuideAction.class.php', 'editguide', $time, $_SESSION['adminname']."编辑了类型为“".$guidecontent['category']."”，标题为“".$guidecontent['title']."”的操作指南");
            $this->ajaxReturn('success','success',1);
			exit();
		}else{
			$this->ajaxReturn('fail','fail',0);
			exit();
		}
    }
	
	//删除操作指南
    public function deleteguide(){
		$this->logincheck();
        $id = $_POST["id"];
        $model= M('tg_guide');
        $condition["id"] = $id;
		$data['activeflag'] = 0;
        $guide = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $guidecontent = $model->where($condition)->find();
        if($guide){
            $this->insertLog($_SESSION['adminname'],'删除操作指南', 'GuideAction.class.php', 'deleteguide', $time, $_SESSION['adminname']."删除了类型为“".$guidecontent['category']."”，标题为“".$guidecontent['title']."”的操作指南");
            $this->ajaxReturn('success','success',1);
			exit();
		}else{
			$this->ajaxReturn('fail','fail',0);
			exit();
		}
    }
}
?>