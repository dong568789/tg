<?php

class ChannelModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $model= M('tg_channel');
        $userid = $_SESSION['userid'];
        $map['C.userid'] = $userid;
        $map['C.activeflag'] = 1;
        $channel = $model->alias('C')
                ->field('C.*,U.account as sub_account')
                ->join('yx_tg_user U on U.channelid=C.channelid','left')
                ->where($map)
                ->order("createtime desc")
                ->select();
		return $channel;
    }


}
?>