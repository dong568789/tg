<?php
class OtherAction extends CommonAction {
    public function __construct(){
        parent::__construct();
    }
	
	//游戏类型
    public function gamecategory(){
		$this->logincheck();
        $this->authoritycheck(10118);
        if($this->authoritycheck(10118) == 'ok'){
            $model= M('tg_gamecategory');
            $condition["activeflag"] = 1;
            $gamecategory = $model->where($condition)->order("createtime asc")->select();
            $this->assign('gamecategory',$gamecategory);
            $this->menucheck();
            $this->display();
        }
    }
	
	//添加游戏类型
    public function addgamecategory(){
		$this->logincheck();
        $model= M('tg_gamecategory');
        $data['categoryname'] = $_POST['categoryname'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
		$data['createuser'] = "Admin";
        $gamecategory = $model->add($data);
        if($gamecategory){
            $this->insertLog($_SESSION['adminname'],'添加游戏类型', 'OtherAction.class.php', 'addgamecategory', $data['createtime'], $_SESSION['adminname']."添加了游戏类型：".$data['categoryname']);
			$this->ajaxReturn('success',$gamecategory,1);
			exit();
		}else{
			$this->ajaxReturn('fail','新增游戏类型失败。',0);
			exit();
		}
    }

	//删除游戏类型
	public function deletegamecategory(){
		$this->logincheck();
        $id = $_POST["id"];
        $model= M('tg_gamecategory');
        $condition["id"] = $id;
		$data['activeflag'] = 0;
        $gamecategory = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $category = $model->where($condition)->find();
        $categoryname = $category['categoryname'];
        if($gamecategory){
            $this->insertLog($_SESSION['adminname'],'删除游戏类型', 'OtherAction.class.php', 'deletegamecategory', $time, $_SESSION['adminname']."删除了游戏类型：".$categoryname);
            $this->ajaxReturn('success','删除成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','删除失败。',0);
			exit();
		}
    }

	//游戏标签
	public function gametag(){
		$this->logincheck();
        $this->authoritycheck(10119);
        $model= M('tg_gametag');
        $condition["activeflag"] = 1;
        $gametag = $model->where($condition)->order("createtime asc")->select();
        $this->assign('gametag',$gametag);
        $this->menucheck();
        $this->display();

    }
	
	//添加游戏标签
    public function addgametag(){
		$this->logincheck();
        $model= M('tg_gametag');
        $data['tagname'] = $_POST['tagname'];
        $data['activeflag'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s',time());
		$data['createuser'] = "Admin";
        $gametag = $model->add($data);
        if($gametag){
            $this->insertLog($_SESSION['adminname'],'添加游戏标签', 'OtherAction.class.php', 'addgametag', $data['createtime'], $_SESSION['adminname']."添加了游戏标签：".$data['tagname']);
            $this->ajaxReturn('success',$gametag,1);
			exit();
		}else{
			$this->ajaxReturn('fail','新增游戏标签失败。',0);
			exit();
		}
    }

	//删除游戏类型
	public function deletegametag(){
		$this->logincheck();
        $id = $_POST["id"];
        $model= M('tg_gametag');
        $condition["id"] = $id;
		$data['activeflag'] = 0;
        $gametag = $model->where($condition)->save($data);
        $time = date('Y-m-d H:i:s',time());
        $tag = $model->where($condition)->find();
        $tagname = $tag['tagname'];
        if($gametag){
            $this->insertLog($_SESSION['adminname'],'删除游戏标签', 'OtherAction.class.php', 'addgametag', $time, $_SESSION['adminname']."删除了游戏标签：".$tagname);

            $this->ajaxReturn('success','删除成功。',1);
			exit();
		}else{
			$this->ajaxReturn('fail','删除失败。',0);
			exit();
		}
    }
}
?>