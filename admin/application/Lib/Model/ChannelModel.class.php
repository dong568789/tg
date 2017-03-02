<?php

class ChannelModel extends Model
{
    public function __construct(){
        parent::__construct();
    }

    public function index($userid){
        $model= M('tg_channel');
        $map['C.userid'] = $userid;
        $map['C.activeflag'] = 1;
        $channel = $model->alias('C')
                ->field('C.*,U.account as sub_account')
                ->join('yx_tg_user U on U.channelid=C.channelid','left')
                ->where($map)
                ->order("C.createtime desc")
                ->select();

		return $channel;
    }


}
?>