<?php

class ChannelModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $model= M('tg_channel');
        $userid = $_SESSION['userid'];
        $map['userid'] = $userid;
        $map['activeflag'] = 1;
        $channel = $model->where($map)->order("createtime desc")->select();
		return $channel;
    }


}
?>