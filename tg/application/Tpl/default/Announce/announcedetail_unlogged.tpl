<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "公告中心";
$page_css[] = "public/css/source.css";

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
                    <div class="card">
                        <div class="card-body text-left" style="padding:50px;">
                            <h2><b><{$announce['title']}></b></h2>
                            <p class="f-15 m-t-25"><span class="m-r-5"><{$announce['category']}></span>|<span class="m-l-5"><{$announce['createtime']}></span></p>

                            <div class="f-15">
                                <{$announce['content']}>

                            </div>

                            <div class="m-t-25">
                                <div class="text-center">
                                    <a type="button" class="btn btn-default btn-lg" href="javascript:window.history.back(-1)">返回</a>
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
<include file="Inc:original-scripts" />

<script type="text/javascript">
    $(function(){

    })
</script>
</body>
</html>