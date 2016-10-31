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
<include file="Inc:original-header" />

<section id="main" data-layout="layout-1">
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>公告中心</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header">
                            <h2><i class="zmdi zmdi-notifications-active f-20 m-r-10"></i> 最新公告</h2>

                            <ul class="actions">
                                <li class="dropdown action-show">
                                    <a href="" data-toggle="dropdown">
                                        <i class="zmdi zmdi-more-vert"></i>
                                    </a>

                                    <ul class="dropdown-menu pull-right">
                                        <li>
                                            <a href="/login/">查看更多</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
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
                                        <a href="/announcedetail_unlogged/<{$vo['id']}>/">
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
                                        <h4 class="media-heading"><b><{$vo['title']}></b></h4>
                                        <a href="/announcedetail_unlogged/<{$vo['id']}>/" class="c-gray"><{$vo['content']}></a>
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
<include file="Inc:original-scripts" />

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