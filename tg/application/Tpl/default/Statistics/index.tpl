<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "数据统计";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
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
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">
                        </div>

                        <div class="card-body">
                            <div id="data-table-basic-header" class="bootgrid-header container-fluid p-b-0 m-b-0">

                                <div class="actionBar" >
                                    <div class="col-sm-4 p-0">
                                        <button class="btn btn-default  m-r-5" id="currentmonth" style="text-transform: none;">本月</button>
                                        <button class="btn btn-default m-r-5" id="latestweek" style="text-transform: none;">最近7天</button>
                                        <button class="btn btn-default" id="latestmonth" style="text-transform: none;">最近30天</button>
                                        <input type="hidden" name="choose-time" id="choose-time" value="">
                                    </div>

                                    <div class="col-sm-4 p-0">
                                        <if condition="$userpid eq 0">
                                            <select class="btn btn-default dropdown-menu f-14 p-l-5 channelselect" id="channelselect">
                                                <option value="0">选择渠道</option>
                                                <foreach name="channel" item="vo" key="k">
                                                    <option value="<{$vo['channelid']}>"><{$vo['channelname']}></option>
                                                </foreach>
                                            </select>
                               
                                            <select class="btn btn-default dropdown-menu f-14 m-l-20" id="gameselect">
                                                <option value="0">选择游戏</option>
                                            </select>

                                            <div class="clear"></div>
                                        <else />
                                            <input type="hidden" name="channelselect" id="channelselect" value="<{$userchannelid}>"> 

                                            <select class="btn btn-default dropdown-menu f-14" id="gameselect">
                                                <option value="0">选择游戏</option>
                                                <foreach name="channelgame" item="vo" key="k">
                                                    <option value="<{$vo['gameid']}>"><{$vo['gamename']}></option>
                                                </foreach>
                                            </select>
                                            <div class="clear"></div>
                                        </if>
                                    </div>

                                    <div class="daterange form-group pull-right">
                                        <div class="input-group">
                                            <span class="zmdi input-group-addon zmdi-calendar"></span>
                                            <input class="search-field form-control" placeholder="请选择日期" name="daterange" id="daterange" readonly="true" type="text"><a id="viewdaterange" class="input-group-addon btn-info">查看</a>
                                        </div>
                                    </div>
                                    <div class="clear"></div>     
                                </div>
                            </div>
                    
                            <div class="p-20" style="min-height: 100px;">
                                <div id="loading" class="col-sm-12 text-center" style="display: none;">
                                    <img src="__ROOT__/plus/public/img/progress.gif" alt=""/>
                                    <p class="m-t-10">正在加载数据，请稍后</p>
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
                                                <if condition="$userpid gt 0" >
                                                    <th data-column-id="sub_dailyincome" >每日收入</th>
                                                <else />
                                                    <th data-column-id="dailyincome" >每日收入</th>
                                                </if>
                                                <th data-column-id="voucherje" >使用代金券金额</th>
											</tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
                                        <foreach name="data" item="vo" key="k">
                                            <tr>
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

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/daterangepicker/daterangepicker.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>

<style>
    .table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
        background-color: #fff;
    }
    .clear{
        clear: both;
    }
</style>
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

    // 搜索
    function search_page () {
        var date = $('#daterange').val();
        if (date != "") {
            var startdate = date.substr(0, 10);
            var enddate = date.substr(-10, 10);
        } else {
            var startdate = "";
            var enddate = "";
        }
	    var choose_time = $('#choose-time').val();
        var channelid = $('#channelselect').val();
        var gameid = $('#gameselect').val();

        $.ajax({
            type: "POST",
            url: "/index.php?m=statistics&a=search",
            data: {channelid:channelid, gameid:gameid, startdate:startdate, enddate:enddate,choose_time:choose_time},
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $(".table-responsive").hide();
                $("#data-table-basic-footer").hide();
                $("#loading").show();
            },
            success: function (data) {
                // console.log(data);
                $("#loading").hide();
                $(".table-responsive").show();
                $("#data-table-basic-footer").show();
                $("#data-table-basic").bootgrid("clear");
                if (data.info == "success") {
                    $("#data-table-basic").bootgrid("append", data.data.daily);
                    $('#gameselect').html("");
                    $('#gameselect').html(data.data.game);
                } else {
                    $('#statisticcontainer').html("");
                    $('#gameselect').html("");
                    $('#gameselect').html(data.data.game);
                
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

    $(document).ready(function() {
        $('#choose-time').val('');
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
                header: ""
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

        window.onload=function() {
            search_page();
        };

        $('#viewdaterange').click(function() {
            search_page();
        });
        $('#searchRecharge').click(function() {
            search_page();
        });
        $('#channelselect').change(function(){
            search_page();
        });
        $('#gameselect').change(function(){
            search_page();
        });
        $('#latestmonth').click(function(){
            $('#choose-time').val('thirtyday');
            $('#daterange').val('');
            search_page();
        });

        $('#latestweek').click(function(){
            $('#choose-time').val('sevenday');
            $('#daterange').val('');
            search_page();
        });
        //本月
        $('#currentmonth').click(function(){
            $('#choose-time').val('currentmonth');
            $('#daterange').val('');
            search_page();
        });

        //下拉框区分大小写
        $(".btn").css("text-transform","none");
    })
</script>
</body>
</html>