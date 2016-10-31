<header id="header" class="clearfix" data-current-skin="blue">
	<ul class="header-inner">
		<li id="menu-trigger" data-trigger="#sidebar">
			<div class="line-wrap">
				<div class="line top"></div>
				<div class="line center"></div>
				<div class="line bottom"></div>
			</div>
		</li>

		<li class="logo hidden-xs">
			<img src="__ROOT__/plus/img/pt_logo.jpg" alt="" style="vertical-align:middle;position: relative;top: -8px;"/>
		</li>

		<li class="pull-right">
			<ul class="top-menu">
				<li id="toggle-width">
					<div class="toggle-switch">
						<input id="tw-switch" type="checkbox" hidden="hidden">
						<label for="tw-switch" class="ts-helper"></label>
					</div>
				</li>

				<li id="top-search">
					<a href=""><i class="tm-icon zmdi zmdi-search"></i></a>
				</li>

				<li class="dropdown">
					<a data-toggle="dropdown" href=""><i class="tm-icon zmdi zmdi-more-vert"></i></a>
					<ul class="dropdown-menu dm-icon pull-right">
						<li class="hidden-xs">
							<a data-action="fullscreen" href=""><i class="zmdi zmdi-fullscreen"></i> 全屏显示</a>
						</li>
						<li>
							<a href="/login/"><i class="zmdi zmdi-long-arrow-tab"></i> 登出</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>

	<!-- Top Search Content -->
	<div id="top-search-wrap">
		<div class="tsw-inner">
			<i id="top-search-close" class="zmdi zmdi-arrow-left"></i>
			<input type="text">
			<i id="top-search-action" class="zmdi zmdi-search"></i>
		</div>
	</div>
</header>