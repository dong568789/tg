<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "数据统计";
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
		$page_nav["数据统计"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>数据统计</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body">
                            <div class="p-20">
                                <div class="row" style="position: relative;top:30px;left: 28%;width: 40%;">
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

                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
											<tr>
												<th data-column-id="date">日期</th>
												<th data-column-id="dailyactive">活跃玩家</th>
												<th data-column-id="newpeople">新增注册</th>
												<th data-column-id="paypeople">付费人数</th>
												<th data-column-id="payrate" data-formatter="payrateformat">付费率</th>
												<th data-column-id="gamename" >游戏名字</th>
												<th data-column-id="channelname" >渠道名字</th>
												<th data-column-id="dailyjournal" >每日流水</th>
												<th data-column-id="dailyincome" >每日收入</th>
											</tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
                                        <foreach name="data" item="vo" key="k">
                                            <tr>
                                                <td><{$vo['date']}></td>
                                                <td><{$vo['dailyactive']}></td>
                                                <td><{$vo['newpeople']}></td>
                                                <td><{$vo['paypeople']}></td>
												<td><{$vo['payrate']}></td>
                                                <td><{$vo['gamename']}></td>
                                                <td><{$vo['channelname']}></td>
                                                <td><{$vo['dailyjournal']}></td>
												<td><{$vo['dailyincome']}></td>
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
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 p-b-25 actionBar\">" +
                "<div class='col-sm-4'><button class='btn btn-default  m-r-5' id='currentmonth'>本月</button>" +
                "<button class='btn btn-default m-r-5' id='latestweek'>最近7天</button>" +
                "<button class='btn btn-default' id='latestmonth'>最近30天</button></div>" +
                "<div class=\"daterange form-group pull-right\"><div class=\"input-group\"><span class=\"zmdi input-group-addon zmdi-calendar\"></span><input type=\"text\" class=\"search-field form-control\" placeholder=\"请选择日期\" name=\"daterange\" id=\"daterange\" readonly=\"true\"><a id=\"viewdaterange\" class=\"input-group-addon btn-info\">查看</a></div></div>" +
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
            var date = $('#daterange').val();
            if (date != "") {
                var start = date.substr(0, 10);
                var end = date.substr(-10, 10);
            }
            var channelid = $('#channelselect').val();
            var gameid = $('#gameselect').val();
            refreshPage(channelid, gameid, start, end, 1);
        });


        function refreshPage (channel, game, start, end, ischannelselect) {
            $.ajax({
                type: "POST",
                url: "/index.php?m=statistics&a=refresh",
                data: {channelid:channel, gameid:game, startdate:start, enddate:end, ischannel:ischannelselect},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.info == "success") {
                        $("#data-table-basic").bootgrid("clear");
                        $("#data-table-basic").bootgrid("append", data.data.daily);
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
            var channelid = $('#channelselect').val();
            var gameid = 0;
            var date = $('#daterange').val();
            if (date != "") {
                var startdate = date.substr(0, 10);
                var enddate = date.substr(-10, 10);
            } else {
                var startdate = "";
                var enddate = "";
            }
            refreshPage(channelid, gameid, startdate, enddate, 1);
        });

        $('#gameselect').change(function(){
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
            refreshPage(channelid, gameid, startdate, enddate, 0);
        });

        $('#latestmonth').click(function(){
            var channelid = $('#channelselect').val();
            var gameid = $('#gameselect').val();
            var seperator1 = "-";
            var startDate = new Date();
            startDate.setDate(startDate.getDate() - 30);
            var startmonth = startDate.getMonth() + 1;
            var startstrDate = startDate.getDate();
            if (startmonth >= 1 && startmonth <= 9) {
                startmonth = "0" + startmonth;
            }
            if (startstrDate >= 0 && startstrDate <= 9) {
                startstrDate = "0" + startstrDate;
            }
            var startdate = startDate.getFullYear() + seperator1 + startmonth + seperator1 + startstrDate;
            var endDate = new Date();
            var endmonth = endDate.getMonth() + 1;
            var endstrDate = endDate.getDate();
            if (endmonth >= 1 && endmonth <= 9) {
                endmonth = "0" + endmonth;
            }
            if (endstrDate >= 0 && endstrDate <= 9) {
                endstrDate = "0" + endstrDate;
            }
            var enddate = endDate.getFullYear() + seperator1 + endmonth + seperator1 + endstrDate;
            refreshPage(channelid, gameid, startdate, enddate, 0);
        });

        $('#latestweek').click(function(){
            var channelid = $('#channelselect').val();
            var gameid = $('#gameselect').val();
            var seperator1 = "-";
            var startDate = new Date();
            startDate.setDate(startDate.getDate() - 7);
            var startmonth = startDate.getMonth() + 1;
            var startstrDate = startDate.getDate();
            if (startmonth >= 1 && startmonth <= 9) {
                startmonth = "0" + startmonth;
            }
            if (startstrDate >= 0 && startstrDate <= 9) {
                startstrDate = "0" + startstrDate;
            }
            var startdate = startDate.getFullYear() + seperator1 + startmonth + seperator1 + startstrDate;
            var endDate = new Date();
            var endmonth = endDate.getMonth() + 1;
            var endstrDate = endDate.getDate();
            if (endmonth >= 1 && endmonth <= 9) {
                endmonth = "0" + endmonth;
            }
            if (endstrDate >= 0 && endstrDate <= 9) {
                endstrDate = "0" + endstrDate;
            }
            var enddate = endDate.getFullYear() + seperator1 + endmonth + seperator1 + endstrDate;
            refreshPage(channelid, gameid, startdate, enddate, 0);
        });
        //本月
        $('#currentmonth').click(function(){
            var channelid = $('#channelselect').val();
            var gameid = $('#gameselect').val();
            var seperator1 = "-";
            var startDate = new Date();
            startDate.setDate(1);
            var startmonth = startDate.getMonth() + 1;
            var startstrDate = startDate.getDate();
            if (startmonth >= 1 && startmonth <= 9) {
                startmonth = "0" + startmonth;
            }
            if (startstrDate >= 0 && startstrDate <= 9) {
                startstrDate = "0" + startstrDate;
            }
            var startdate = startDate.getFullYear() + seperator1 + startmonth + seperator1 + startstrDate;
            var endDate = new Date();
            var endmonth = endDate.getMonth() + 1;
            var endstrDate = endDate.getDate();
            if (endmonth >= 1 && endmonth <= 9) {
                endmonth = "0" + endmonth;
            }
            if (endstrDate >= 0 && endstrDate <= 9) {
                endstrDate = "0" + endstrDate;
            }

            var enddate = endDate.getFullYear() + seperator1 + endmonth + seperator1 + endstrDate;
            refreshPage(channelid, gameid, startdate, enddate, 0);
        });

        //下拉框区分大小写
        $(".btn").css("text-transform","none");
    })
</script>
</body>
</html>