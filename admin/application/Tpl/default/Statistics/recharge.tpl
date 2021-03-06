<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "充值查询";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";

?>

<include file="Inc:head" />
<body>
<style>
    .table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
        background-color: #fff;
    }
    .clear{
        clear: both;
    }

    .select2-container .select2-selection--single{
        height: 35px;
        border-radius: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 35px;
    }
</style>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
	    //个人资料页面用$profile_nav
    	//功能页面用$page_nav
    	$page_nav["用户查询"]["active"] = true;
    	$page_nav["用户查询"]["sub"]["充值查询"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>充值查询</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body ">
                            <div id="data-table-basic-header" class="bootgrid-header container-fluid p-b-0 m-b-0">
                                <div class="actionBar">
                                    <div class="search form-group col-sm-9 m-0 p-l-0">
                                        <div class="input-group">
                                            <span class="zmdi icon input-group-addon glyphicon-search"></span>
                                            <input type="text" class="form-control search-content" id="account" placeholder="输入账号搜索">
                                        </div>
                                    </div>
                                    <div class="actions btn-group">
                                        <div class="dropdown btn-group">
                                            <a class="btn btn-default" href="javascript:void(0);" id="searchRecharge">搜索</a>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary pull-right" id="export" data-result="">导出EXCEL</button>
                                </div>

                                <div class="actionBar m-t-20">
                                    <div class="col-sm-2 p-0">
                                        <select class="js-example-basic-multiple js-states form-control search-field"
                                                name="gameid" id="gameselect" style="height: 50px;">
                                            <option value="">所有游戏</option>
                                            <volist name="games" id="vo">
                                                <option value="<{$vo.sdkgameid}>" data-select2-id="<{$vo.sdkgameid}>"><{$vo
                                                    .gamename}></option>
                                            </volist>
                                        </select>
                                            <div class="clear"></div>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="js-example-basic-multiple js-states form-control search-field"
                                                name="userid" id="select_user">
                                            <option value="">所有用户</option>
                                            <volist name="users" id="vo">
                                                <option value="<{$vo.userid}>" data-select2-id="<{$vo.userid}>"><{$vo.account}></option>
                                            </volist>
                                        </select>
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

                                <div class="table-responsive">
                                    <table id="grid-keep-selection" class="table table-hover table-vmiddle" aria-busy="false">
                                        <thead>
                                        <tr>
                                            <th data-column-id="gamename">游戏</th>
                                            <th data-column-id="channelname">渠道</th>
                                            <th data-column-id="username">账号</th>
                                            <th data-column-id="amount">金额（汇总：）</th>
                                            <th data-column-id="status">状态</th>
                                            <th data-column-id="agent">充值渠道</th>
                                            <th data-column-id="regagent">注册渠道</th>
                                            <th data-column-id="voucherje">优惠券</th>
                                            <th data-column-id="create_time" data-formatter="create_time" data-order="desc">时间</th>
                                            <th data-column-id="payname" data-sortable="false">充值方式</th>
                                        </tr>
                                        </thead>
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
<script src="__ROOT__/plus/select2/js/select2.min.js"></script>

<script type="text/javascript">
    //回车绑定事件
    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
            document.getElementById("searchRecharge").click();
        }
    };
    $(function(){
        loadData();

        $('#viewdaterange').click(function() {
            $("#grid-keep-selection").bootgrid('destroy');
            loadData();
        });
        $('#searchRecharge').click(function() {
            $("#grid-keep-selection").bootgrid('destroy');
            loadData();
        });

        $('#gameselect').change(function(){
            $("#grid-keep-selection").bootgrid('destroy');
            loadData();
        });

        $('#select_user').change(function() {
            $("#grid-keep-selection").bootgrid('destroy');
            loadData();
        });

        //下拉框区分大小写
        $(".btn").css("text-transform","none");

        //综合筛选
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

        $("#gameselect").select2();
        $("#select_user").select2();

        // 导出excel表
        $('#export').click(function() {
            swal({
                title: "确认导出excel？",
                // text: "确认导出excel。",
                showCancelButton: true,
                confirmButtonColor: "#00ccff",
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                closeOnConfirm: true
            }, function(){

                var date = $('#daterange').val();
                var startdate = "";
                var enddate = "";
                if (date != "") {
                    startdate = date.substr(0, 10);
                    enddate = date.substr(-10, 10);
                }
                var channelid = $('#channelselect').val();
                var username = $('#account').val().trim();
                var gameid = $('#gameselect').val();
                var userid = $('#select_user').val();
                $.ajax({
                    type : 'POST',
                    url : "<{:U('statistics/export')}>",
                    data : {username:username,gameid:gameid,startdate:startdate,enddate:enddate,userid:userid},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        if (data.info == "success") {
                            notify('导出结算单成功', 'success');
                            location.href = '__ROOT__/'+data.url;
                        } else {
                            notify('记录为空，不能导出', 'danger');
                        }
                        return false;
                    },
                    error : function (xhr) {
                        notify('系统错误！', 'danger');
                        return false;
                    }
                });
            })
        });
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

    function loadData(){
        var date = $('#daterange').val();
        var startdate = "";
        var enddate = "";
        if (date != "") {
            startdate = date.substr(0, 10);
            enddate = date.substr(-10, 10);
        }
        var channelid = $('#channelselect').val();
        var username = $('#account').val().trim();
        var gameid = $('#gameselect').val();
        var userid = $('#select_user').val();


        $("#grid-keep-selection").bootgrid({
            css: {
                icon: 'zmdi',
                iconColumns: 'zmdi-menu',
                iconDown: 'zmdi-caret-down-circle',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-caret-up-circle'
            },
            formatters: {
            },
            templates: {
                header: ""
            },
            ajax: true,
            post: function ()
            {
                /* To accumulate custom parameter with the request object */
                return {
                    username:username,
                    gameid:gameid,
                    startdate:startdate,
                    enddate:enddate,
                    userid:userid

                };
            },
            url: "<{:U('statistics/ajaxRecharge')}>",
            selection: true,
            multiSelect: true,
            rowSelect: true,
            keepSelection: true,
            labels: {
                loading: "Loading...", //加载时显示的内容
                noResults: '没有符合条件的数据'//未查询到结果是显示内容
            },
            responseHandler:function(response){
                $('th[data-column-id=amount] .text').html('金额（汇总：'+response.allmoney+'）');
                return   response;
            }
        });
    }
</script>
</body>
</html>