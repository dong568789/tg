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
            <a href="/" style="padding:0;">
				<img src="__ROOT__/plus/img/pt_logo.jpg" alt="" style="vertical-align:middle;position: relative;top: -8px;"/>
			</a>
		</li>

		<li class="pull-right">
			<ul class="top-menu">
				<li id="toggle-width">
					<div class="toggle-switch">
						<input id="tw-switch" type="checkbox" hidden="hidden">
						<label for="tw-switch" class="ts-helper"></label>
					</div>
				</li>

				<li class="hidden-xs">
					<a target="_self" href="/channel/">
						<span class="tm-label">渠道管理</span>
					</a>
				</li>

				<li class="hidden-xs">
					<a target="_self" href="/source/">
						<span class="tm-label">推广资源</span>
					</a>
				</li>

				<li class="hidden-xs">
					<a target="_self" href="/statistics/">
						<span class="tm-label">数据统计</span>
					</a>
				</li>

				<li class="hidden-xs">
					<a target="_self" href="/balance/">
						<span class="tm-label">结算中心</span>
					</a>
				</li>

                <li class="dropdown">
                    <a data-toggle="dropdown" href="">
                        <i class="tm-icon zmdi zmdi-email"></i>
                        <if condition="$allUnreadMessage['num'] neq '' && $allUnreadMessage['num'] neq 0">
							<i class="tmn-counts" id="messageCounts"><{$allUnreadMessage['num']}></i>
						</if>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg pull-right">
                        <div class="listview" id="notifications">
                            <div class="lv-header">
                                我的消息
                                <ul class="actions">
                                    <li class="dropdown">
										<if condition="$allUnreadMessage['num'] neq '' && $allUnreadMessage['num'] neq 0">
											<a href="javascript:" data-clear="notification" id="allUnreadMessage">
												<i class="zmdi zmdi-check-all"></i>
											</a>
										</if>
                                    </li>
                                </ul>
                            </div>
                            <div class="lv-body" id="messageContainer">
								<foreach name="allUnreadMessage" item="vo" key="k">
									<a class="lv-item" href="/message/">
										<div class="media">
											<div class="media-body">
												<div class="lv-title"><{$vo['title']|msubstr=0,8,'utf-8',false}></div>
												<small class="lv-small"><{$vo['content']}></small>
											</div>
											<div class="pull-right" style="position: relative;top: -35px; color: #666"><{$vo['time']}></div>
										</div>
									</a>
								</foreach>
                            </div>
                            <a class="lv-footer" href="/message/">查看所有消息</a>
                        </div>
                    </div>
                </li>

				<li class="dropdown">
					<a data-toggle="dropdown" href=""><i class="tm-icon zmdi zmdi-more-vert"></i></a>
					<ul class="dropdown-menu dm-icon pull-right">
						<li class="hidden-xs">
							<a data-action="fullscreen" href=""><i class="zmdi zmdi-fullscreen"></i> 全屏显示</a>
						</li>
						<li>
							<a href="index.php?m=user&a=logout"><i class="zmdi zmdi-long-arrow-tab"></i> 登出</a>
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