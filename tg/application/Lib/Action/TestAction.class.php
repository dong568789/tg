<?php
class TestAction extends CommonAction {

	public function index(){
		$balanceid=1;
		$balanceModal = M('tg_balance');
        $condition["id"] = $balanceid;
        $balance = $balanceModal->field('userid,startdate,enddate')->where($condition)->find();
        echo '<pre>';
		echo  $balanceModal->getLastSql();
		echo "<br/>";
		var_dump($balance) ;
		echo '</pre>';
		exit();
	}




	public function createSource()
	{

		$isupload = 0;
		$activeflag = 1;
		$userid = 73;
		$channelid = 75;



		$gameModel = M('tg_game');
		$sourceModel = M('tg_source');
		$games = $gameModel->where(array('isonstack' => '0', 'activeflag' => 1))->select();

		foreach($games as $game){

			$check = $sourceModel->where(array('userid' => $userid, 'channelid' => $channelid, 'gameid' =>
				$game['gameid']))->find();

			if(!empty($check)) {
				$ok[] = $check;

				continue;
			}


			$newgamename = createstr(30);
			$sourcesn = "tg_".$newgamename;
			$item[] = array(
				'userid' => $userid,
				'gameid' => $game['gameid'],
				'channelid' => $channelid,
				'createtime'=> date('Y-m-d H:i:s'),
				'sourcesn' => $sourcesn,
				'activeflag' => $activeflag,
				'textureurl' => $game['texturename'],
				'isupload' => $isupload,
				'sourcesharerate' => $game['sharerate'],
				'sourcechannelrate' => $game['channelrate'],
				'createuser' => 'limengjun'
			);
		}

		$res = $sourceModel->addAll($item);
		if($res){
			echo 'success';
		}else{
			echo 'error';
		}
	}
}
?>