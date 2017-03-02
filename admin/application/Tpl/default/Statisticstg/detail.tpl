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
                <h2><{$Think.get.date}>数据统计</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-left">
                                <a class="btn" href="javascript:history.go(-1)">返回</a>
                        </div>

                        <div class="card-body">
                            <div id="data-table-basic-header" class="bootgrid-header container-fluid p-b-0 m-b-0">

                                <div class="actionBar" >
                                    <div class="col-sm-12 p-0">
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
                                        <else />
                                            <input type="hidden" name="channelselect" id="channelselect" value="<{$userchannelid}>"> 

                                            <select class="btn btn-default dropdown-menu f-14" id="gameselect">
                                                <option value="0">选择游戏</option>
                                                <foreach name="channelgame" item="vo" key="k">
                                                    <option value="<{$vo['gameid']}>"><{$vo['gamename']}></option>
                                                </foreach>
                                            </select>
                                        </if>
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
                                    <table id="data-table-basic" class="table table-hover table-vmiddle table-bordered">
                                        <thead>
											<tr>
												<th data-column-id="date">日期</th>
                                                <th data-column-id="gamename" >游戏名称</th>
                                                <th data-column-id="channelname" >渠道名称</th>

                                                <if condition="$sourcetype neq '1' ">
    												<th data-column-id="dailyactive">活跃玩家</th>
    												<th data-column-id="newpeople">新增注册</th>
    												<th data-column-id="paypeople">付费人数</th>
                                                </if>
												<!-- <th data-column-id="payrate" data-formatter="payrateformat">付费率</th> -->
												
												<th data-column-id="dailyjournal" >每日流水</th>

                                                <if condition="$sourcetype eq '1' ">
                                                    <th data-column-id="voucherje" >代金券使用</th>
                                                </if>

                                                <if condition="$userpid gt 0" >
                                                    <th data-column-id="sub_dailyincome" >每日收益</th>
                                                <else />
                                                    <th data-column-id="dailyincome" >每日收益</th>
                                                </if>
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

    $("#data-table-basic").bootgrid({
        rowCount:20,
        templates: {
            header: ""
        }
    });
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
        var channelid = $('#channelselect').val();
        var gameid = $('#gameselect').val();
        var startdate = "<{$Think.get.date}>";
        var enddate = "<{$Think.get.date}>";
 
        $.ajax({
            type: "POST",
            url: "<{:U('statisticstg/search',array('uid' => $tguserid))}>",
            data: {channelid:channelid, gameid:gameid, startdate:startdate, enddate:enddate},
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

        window.onload=function() {
            search_page();
        };

        $('#channelselect').change(function(){
            search_page();
        });
        $('#gameselect').change(function(){
            search_page();
        });

        //下拉框区分大小写
        $(".btn").css("text-transform","none");
    })
</script>
</body>
</html>