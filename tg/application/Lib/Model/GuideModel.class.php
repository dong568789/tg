<?php

class GuideModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function indexpage(){
		$model= M('tg_guide');
		$condition["activeflag"] = 1;
		$condition["category"] = "操作教程";
        $guide['operation'] = $model->where($condition)->order('createtime asc')->select();
		$newcondition["activeflag"] = 1;
		$newcondition["category"] = "常见问题";
        $guide['question'] = $model->where($newcondition)->order('createtime asc')->select();
        return $guide;
    }
}
?>