<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "审核未通过";
	$page_css[] = "public/css/index.css";
?>
<include file="Inc:head" />
<body>
<include file="Inc:original-header" />

	<section id="main" data-layout="layout-1" style="padding-top:150px;">
		<section id="content">
			<div class="container">
				<img src="__ROOT__/plus/img/refused.png">
				<form id="refuse" class="form-horizontal" role="form" action="index.php?m=user&a=register" method="post">
					<input type="hidden" id="isfromverify" name="isfromverify" value="1">
					<input type="hidden" id="userid" name="userid" value="<{$userid}>">
					<div class="text-center">
						<if condition = "$refusereason['reason1'] eq 1">
							<button type="button" class="btn btn-danger">手机号或账号有误</button>
						</if>
						<if condition = "$refusereason['reason2'] eq 1">
							<button type="button" class="btn btn-danger">真实姓名有误</button>
						</if>
						<if condition = "$refusereason['reason3'] eq 1">
							<button type="button" class="btn btn-danger">公司名称有误</button>
						</if>
						<if condition = "$refusereason['reason4'] eq 1">
							<button type="button" class="btn btn-danger">联系地址有误</button>
						</if>
						
					</div>
					<div class="m-t-25 text-center">
						<button type="submit" class="btn btn-primary btn-lg"  id="resubmit">点击这里再次提交资料</button>
					</div>
				</form>
			</div>
		</section>
	</section>

<include file="Inc:footer" />
<include file="Inc:original-scripts" />

<script type="text/javascript">
    $(document).ready(function() {
    })
</script>
</body>
</html>