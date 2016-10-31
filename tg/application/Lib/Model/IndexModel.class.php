<?php

class IndexModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function game(){
        $gameModel = M('tg_game');
        $gametagModel = M('tg_gametag');
        $gamecategoryModel = M('tg_gamecategory');
        $map['activeflag'] = 1;
        $map['isonstack'] = 0;
        $games = $gameModel->where($map)->order("publishtime desc")->limit(9)->select();
        foreach($games as $k => $v){
            $gametagid = $v['gametag'];
            $gamecategoryid = $v['gamecategory'];
            $gametag = $gametagModel->where("id = '$gametagid'")->find();
            $gamecategory = $gamecategoryModel->where("id = '$gamecategoryid'")->find();
            $games[$k]['gametag'] = $gametag['tagname'];
            $games[$k]['gamecategory'] = $gamecategory['categoryname'];
        }
        return $games;
    }

    public function announce(){
        $model= M('tg_announce');
        $condition["activeflag"] = 1;
        $announce = $model->where($condition)->order("orderid desc,createtime desc")->limit(10)->select();
        foreach($announce as $k =>$v){
            $announce[$k]['content'] = strip_tags($announce[$k]['content']);
            $length = mb_strlen($announce[$k]['content'],'UTF8');
            $announce[$k]['length'] = $length;
        }
        return $announce;
    }

}
?>