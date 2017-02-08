<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "获取推广素材";
	$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
    $page_css[] = "vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css";

?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $profile_nav["账户管理"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>获取推广素材</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body " style="height: 1200px;">
                            <div class="p-20 col-sm-12">
                                <div class="col-sm-6">
                                    <div class="form-group m-t-25">
                                        <div class="col-sm-12 text-center">
                                            <button id="getTwocode" data-url="<{$link}>" class="btn btn-primary btn-lg" style="width: 339px;margin-bottom: 20px;">一键生成推广链接和二维码</button>
                                        </div>
                                        <div id="hahaha">
                                            <div class="col-sm-12 text-center">
                                                <a href=""><img src="__ROOT__/upfiles/QRCode/tg_<{$gamepinyin}>.png" alt="" style="margin:20px;width:230px;height: 230px;"/></a>
                                            </div>

                                            <div class="col-sm-12 text-center">
                                                <a href="__ROOT__/upfiles/QRCode/tg_<{$gamepinyin}>.png" id=pic1  onclick="goDownload();" class="btn bgm-amber waves-effect" style="width: 339px;">下载二维码</a>
                                            </div>

                                            <div class="col-sm-12 text-center">
                                                <p class="" style="margin-top: 50px;word-break:break-all;">推广长链接：<a href="<{$long_url}>"><{$long_url}></a></p>
                                                <p class="" style="position: relative;left: -146px;">推广短链接：<a href="<{$short_url}>"><{$short_url}></a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group m-t-25">
                                        <div class="col-sm-12 text-center">
                                            <a href="javascript:void(0)" id="preview" class="btn btn-primary btn-lg" style="width: 339px;margin-bottom: 20px;">一键生成推广页</a>
                                        </div>
                                        <div class="col-sm-12 text-center m-t-25" id="J_page">

                                        </div>
                                        <div class="col-sm-12 text-center pagelink">
                                            <p class="m-t-10">页面链接：<a href="http://tg.yxgames.com/page/<{$sourceid}>/" target="__blank">http://tg.yxgames.com/page/<{$sourceid}>/</a></p>
                                        </div>
                                    </div>
                                </div>
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#hahaha").hide();
        $("#getTwocode").click(function() {
            $("#hahaha").show();
        });
        $("iframe").hide();
        $(".pagelink").hide();
        $("#preview").click(function(){
            if($('#J_page').find('iframe').length == 0){
                $('#J_page').html('<iframe src="http://tg.yxgames.com/page/<{$sourceid}>/" width="414" height="800" scrolling="no" frameborder="0" name="main"></iframe>');
            }
            $("iframe").show();
            $(".pagelink").show();
        });


    })
</script>

</body>
</html>