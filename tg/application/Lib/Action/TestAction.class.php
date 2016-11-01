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
}
?>