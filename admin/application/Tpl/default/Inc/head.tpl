<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $page_title != "" ? $page_title."_" : ""; ?>游侠游戏管理后台</title>
	<link rel="icon" href="__ROOT__/plus/img/logo.png" type="image/x-icon">
	<?php
		if ($page_css) {
			foreach ($page_css as $css) {
				echo '<link rel="stylesheet" href="__ROOT__/plus/'.$css.'">';
			}
		}
	?>
	<!-- Basic CSS -->
	<link href="__ROOT__/plus/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">
	<link href="__ROOT__/plus/vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet">
	<link href="__ROOT__/plus/vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
	<link href="__ROOT__/plus/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css" rel="stylesheet">
	<link href="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">
	<!-- Main CSS -->
	<link href="__ROOT__/plus/css/app.min.1.css" rel="stylesheet">
	<link href="__ROOT__/plus/css/app.min.2.css" rel="stylesheet">
</head>