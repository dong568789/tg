<?php

class AnnounceModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $model= M('tg_announce');
		$condition["activeflag"] = 1;
        $announce = $model->where($condition)->order("orderid desc,publishtime desc")->select();
		foreach($announce as $k =>$v){
			$announce[$k]['content'] = strip_tags($announce[$k]['content']);
			$length = mb_strlen($announce[$k]['content'],'UTF8');
			$announce[$k]['length'] = $length;
		}
        return $announce;
    }

    public function announcedetail($id){
        $model= M('tg_announce');
        $announce = $model->find($id);
        return $announce;
    }

}
?>