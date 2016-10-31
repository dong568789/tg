<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "公告中心";
	$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
	$page_css[] = "vendors/bower_components/jpages/css/animate.css";
	$page_css[] = "vendors/bower_components/jpages/css/github.css";
?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />
<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["公告中心"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>公告中心</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="zmdi zmdi-notifications-active f-20 m-r-10"></i> 最新公告</h2>
                        </div>

                        <div class="card-body card-padding">
                            <div class="alert alert-info alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                游侠游戏分发平台上线了，快来看看吧.
                            </div>

                            <br/>

                            <div class="media-demo p-l-25 p-r-25" id="announcecontainer">
                                <foreach name="announce" item="vo" key="k">
									<div class="media m-b-20">
										<div class="pull-left">
											<a href="/announcedetail/<{$vo['id']}>/">
												<if condition="$vo['category'] eq '系统公告' ">
													<img class="media-object" src="__ROOT__/plus/img/announce_sys.jpg" alt="">
												<elseif condition="$vo['category'] eq '结算公告' "/>
													<img class="media-object" src="__ROOT__/plus/img/announce_balance.jpg" alt="">
												<elseif condition="$vo['category'] eq '产品公告' "/>
													<img class="media-object" src="__ROOT__/plus/img/announce_product.jpg" alt="">
												<else/>
													<img class="media-object" src="__ROOT__/plus/img/announce_other.jpg" alt="">
												</if>
											</a>
										</div>
										<div class="media-body">
											<a href="/announcedetail/<{$vo['id']}>/" class="media-heading c-black"><b><{$vo['title']}></b></a><br>
											<a href="/announcedetail/<{$vo['id']}>/" class="c-gray">
												<if condition="$vo['length'] lt 200">
													<{$vo['content']|msubstr=0,200,'utf-8',false}>
												<else/>
													<{$vo['content']|msubstr=0,200,'utf-8',true}>
												</if>
											</a>
										</div>
									</div>
                                </foreach>
                            </div>

                            <div class="text-center holder">
							</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</section>

<include file="Inc:footer" />
<include file="Inc:scripts" />

<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
		$("div.holder").jPages({
			containerID    : "announcecontainer",
			scrollBrowse   : false,
			perPage: 20
		});
    })
</script>
</body>
</html>