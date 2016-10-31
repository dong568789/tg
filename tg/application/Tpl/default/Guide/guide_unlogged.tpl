<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "操作指南";
?>
<include file="Inc:head" />
<body>
<include file="Inc:original-header" />

<section id="main" data-layout="layout-1">
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>操作指南</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header"></div>
                        <div class="card-body ">
                            <div class="row p-b-25">
                                <div class="col-sm-3 m-t-15">
                                    <div class="row">
                                        <div class="col-sm-2">
                                        </div>
                                        <div class="col-sm-10">
                                            <ul class="main-menu">
                                                <li class="sub-menu toggled">
                                                    <a href=""><i class="zmdi zmdi-info"></i> 操作教程</a>
                                                    <ul style="display: block;" class="teach">
                                                        <foreach name="guide['operation']" item="vo" key="k">
                                                            <li>
                                                                <a onclick="getContent(<{$vo['id']}>);" class="guidetitle guidetitle-<{$vo['id']}>"><{$vo['title']}></a>
                                                            </li>
                                                        </foreach>
                                                    </ul>
                                                </li>
                                                <li class="sub-menu toggled">
                                                    <a href=""><i class="zmdi zmdi-help"></i> 常见问题</a>
                                                    <ul style="display: block;" class="question">
                                                        <foreach name="guide['question']" item="vo" key="k">
                                                            <li>
                                                                <a onclick="getContent(<{$vo['id']}>);" class="guidetitle guidetitle-<{$vo['id']}>"><{$vo['title']}></a>
                                                            </li>
                                                        </foreach>
                                                    </ul>
                                                </li>
                                                <input type="hidden" id="hiddenid" value="<{$firstguide['id']}>">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-8 m-t-25">
                                    <h2 class="f-700 text-center"></h2>
                                    <div id="guidecontent">
                                        <{$firstguide['content']}>
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
    function notify(message, type){
        $.growl({
            message: message
        },{
            type: type,
            allow_dismiss: false,
            label: '取消',
            className: 'btn-xs btn-inverse',
            placement: {
                from: 'top',
                align: 'right'
            },
            delay: 2500,
            animate: {
                enter: 'animated bounceIn',
                exit: 'animated bounceOut'
            },
            offset: {
                x: 20,
                y: 85
            }
        });
    }

    function getContent (guideid) {
        $.ajax({
            type : 'POST',
            url : "index.php?m=guide&a=getcontent",
            data : {id : guideid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                if (data.info == "success") {
                    $('#guidecontent').html(data.data);
                    $('.guidetitle').removeClass("c-blue");
                    $('.guidetitle-'+guideid).addClass("c-blue");
                }
                return false;
            },
            error : function (xhr) {
                notify('系统错误！', 'danger');
                return false;
            }
        });
    }
    function GetQueryString(url) {
        var reg = new RegExp("(^|&)" + url + "=([^&]*)(&|$)","i");
        var r = window.location.search.substr(1).match(reg);
        if (r!=null) return (decodeURIComponent(r[2])); return null;
    }

    var firstguide = $('#hiddenid').val();
    $(document).ready(function() {
        $('.guidetitle-'+firstguide).addClass("c-blue");

        if(GetQueryString("guideid") != ''){
            getContent(GetQueryString("guideid"));
        }

    })
</script>
</body>
</html>