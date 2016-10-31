<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "用户注册数实时查询";
	$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
	$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
	$page_css[] = "public/css/statistics.css";
?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
		//个人资料页面用$profile_nav
		//功能页面用$page_nav
		$page_nav["用户查询"]["active"] = true;
        $page_nav["用户查询"]["sub"]["注册查询"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>用户注册数实时查询</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body">
                            <div class="p-20">
                                <div class="row col-sm-12 m-b-25">
                                    <div class="form-group m-t-25">
                                        <label for="username" class="pull-left control-label f-15 m-t-5">用户名</label>
                                        <div class="col-sm-3">
                                            <div class="input-group-float fg-float">
                                                <div class="fg-line">
                                                    <input type="text" class="form-control" name="user" id="user" value="">
                                                    <label class="fg-label">请输入用户名</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="">
                                        <select class="btn btn-default dropdown-menu f-14 p-l-5 channelselect" id="channelselect">
                                            <option value="0">选择渠道</option>
                                            <foreach name="channel" item="vo" key="k">
                                                <option value="<{$vo['channelid']}>"><{$vo['channelname']}></option>
                                            </foreach>
                                        </select>
                                    </div>
                                    <div class="" style="position: relative;left: 1%;">
                                        <select class="btn btn-default dropdown-menu f-14" id="gameselect">
                                            <option value="0">选择游戏</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="loading" class="col-sm-12 text-center" style="display: none;margin-top: 35px;">
                                    <img src="__ROOT__/plus/public/img/progress.gif" alt=""/>
                                    <p class="m-t-10">正在加载数据，请稍后</p>
                                </div>
                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="username">用户名</th>
                                            <th data-column-id="channelname">注册渠道</th>
                                            <th data-column-id="gamename">游戏</th>
                                            <th data-column-id="reg_time">注册时间</th>
                                            <th data-column-id="login_time" >最近登录</th>
                                        </tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
                                        <foreach name="registration" item="vo" key="k">
                                            <tr>
                                                <td><{$vo['username']}></td>
                                                <td><{$vo['channelname']}></td>
                                                <td><{$vo['gamename']}></td>
                                                <td><{$vo['reg_time']}></td>
                                                <td><{$vo['login_time']}></td>
                                            </tr>
                                        </foreach>
                                        </tbody>
                                    </table>
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

<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/daterangepicker/daterangepicker.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
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
                delay: 3000,
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

        $("#data-table-basic").bootgrid({
            css: {
                icon: 'zmdi',
                iconColumns: 'zmdi-menu',
                iconDown: 'zmdi-caret-down-circle',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-caret-up-circle'
            },
            formatters: {
                "payrateformat": function(column, row)
                {
                    if (parseInt(row.payrate) > 0) {
                        return row.payrate+" %";
                    } else {
                        return "未统计";
                    }
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 p-b-25 actionBar\" style=\"position:absolute;top:88px;right:120px;\">" +
                "<div class=\"daterange form-group pull-right\"><div class=\"input-group\"><span class=\"zmdi input-group-addon zmdi-calendar\"></span><input type=\"text\" class=\"search-field form-control\" placeholder=\"请选择日期\" name=\"daterange\" id=\"daterange\" readonly=\"true\"><a id=\"viewdaterange\" class=\"input-group-addon btn-info\">查询</a></div></div>" +
                "</div></div></div>"
            }
        });


        $('#daterange').daterangepicker({
            format: 'YYYY-MM-DD',
            minDate: '2016-01-01',
            drops: 'down',
            opens: 'left',
            buttonClasses: ['btn', 'btn-default'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-default',
            locale: moment.locale('zh-cn')
        });

        $('#viewdaterange').click(function() {
            refreshPage(1);
        });

        function refreshPage (ischannelselect) {
            var username = $('#user').val();
            var channelid = $('#channelselect').val();
            var gameid = $('#gameselect').val();
            var date = $('#daterange').val();
            if (date != "") {
                var startdate = date.substr(0, 10);
                var enddate = date.substr(-10, 10);
            } else {
                var startdate = "";
                var enddate = "";
            }

            $.ajax({
                type: "POST",
                url: "/index.php?m=registration&a=refresh",
                data: {username:username, gameid:gameid,channelid:channelid, startdate:startdate, enddate:enddate, ischannel:ischannelselect},
                cache: false,
                dataType: 'json',
                beforeSend: function () {
                    $("#data-table-basic-footer").hide();
                    $(".table-responsive").hide();
                    $("#loading").show();
                },
                success: function (data) {
                    $("#loading").hide();
                    $(".table-responsive").show();
                    $("#data-table-basic-footer").show();
                    $("#data-table-basic").bootgrid("clear");
                    if (data.info == "success") {
                        $("#data-table-basic").bootgrid("append", data.data.userall);
                        if (ischannelselect == 1) {
                            $('#gameselect').html("");
                            $('#gameselect').html(data.data.game);
                        }
                    } else {
                        $('#statisticcontainer').html("");
                        if (ischannelselect == 1) {
                            $('#gameselect').html("");
                            $('#gameselect').html(data.data.game);
                        }
                        notify('没有符合条件的数据', 'danger');
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });

        }


        $('#channelselect').change(function(){
            refreshPage(1);
        });

        $('#gameselect').change(function(){
            refreshPage(0);
        });

        $('#user').blur(function(){
            refreshPage(0);
        });

        window.onload=function() {
            refreshPage(1);
        };

        //下拉框区分大小写
        $(".btn").css("text-transform","none");
    })
</script>
</body>
</html>