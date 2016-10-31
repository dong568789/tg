<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "结算列表";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";

?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["结算中心"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>结算单详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">              
                        </div>
                        <div class="card-body ">
                            <div  class="p-20">
                                <div class="table-responsive">
                                	<input type="hidden" name="hiddenbalanceid" id="hiddenbalanceid" value="<{$balance['id']}>">
                                    <table id="data-table-command" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
											<th data-column-id="balanceid" data-visible="false">账单编号</th>
                                            <th data-column-id="sourceid" data-visible="false">资源编号</th>
                                            <th data-column-id="img" data-visible="false">游戏图标</th>
                                            <th data-column-id="gameicon" data-formatter="gameicon">游戏图标</th>
                                            <th data-column-id="gameid" >游戏名</th>
											<th data-column-id="channelid">渠道名</th>
                                            <th data-column-id="sharerate">分成比例</th>
											<th data-column-id="channelrate">渠道费</th>
											<th data-column-id="sourcejournal">总流水</th>
                                            <th data-column-id="sourceincome">总收入</th>
                                            <th data-column-id="actualpaid">需打款金额</th>
                                            <th data-column-id="link" data-formatter="link" data-sortable="false">查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="source" item="vo" key="k">
                                        <tr>
											<td><{$balance['id']}></td>
                                            <td><{$vo['sourceid']}></td>
                                            <td><{$ICONURL}><{$vo['gameicon']}></td>
                                            <td></td>
											<td><{$vo['gamename']}></td>
                                            <td><{$vo['channelname']}></td>
											<td><{$vo['sharerate']}></td>
											<td><{$vo['channelrate']}></td>
                                            <td><{$vo['sourcejournal']}></td>
                                            <td><{$vo['sourceincome']}></td>
                                            <td><{$vo['actualpaid']}></td>
                                            <td></td>
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
		<button class="btn btn-float bgm-red m-btn" data-action="print"><i class="zmdi zmdi-print"></i></button>
    </section>
</section>


<include file="Inc:footer" />
<include file="Inc:scripts" />

<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/daterangepicker/daterangepicker.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>

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

    $(document).ready(function(){
        //Command Buttons
        $("#data-table-command").bootgrid({
            css: {
                icon: 'zmdi icon',
                iconColumns: 'zmdi-view-module',
                iconDown: 'zmdi-expand-more',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-expand-less'
            },
            formatters: {
                "gameicon": function(column, row) {
                    return "<img width=\"50\" height=\"50\" src=\""+row.img+"\">";
                },
				"payrateformat": function(column, row)
                {
                    return row.payrate+" %";
                },
				"link": function(column, row) {
                    return "<a href=\"/accountdetail/"+row.balanceid+"/"+row.sourceid+"/\">查看详情</a>";
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p>" +
                "</div>"
            }
        });

        $('#daterange').daterangepicker({
            format: 'YYYY-MM-DD',
            minDate: '2016-01-01',
            drops: 'down',
            buttonClasses: ['btn', 'btn-default'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-default',
            locale: moment.locale('zh-cn')
        });
		
		/*
        $('#viewdaterange').click(function() {
            var date = $('#daterange').val();
            if (date != "") {
                var start = date.substr(0, 10);
                var end = date.substr(-10, 10);
                $.ajax({
                    type : 'POST',
                    url : "index.php?m=balance&a=viewDaterangeBalance",
                    data : {startdate : start, enddate : end},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        console.log(data);
                        if (data.info == "success") {
                            $("#data-table-command").bootgrid("clear");
                            $("#data-table-command").bootgrid("append", data.data);
                            notify('数据获取成功', 'success');
                        } else {
                            $("#data-table-command").bootgrid("clear");
                            notify('数据获取失败，没有符合条件的数据', 'danger');
                        }
                        return false;
                    },
                    error : function (xhr) {
                        notify('系统错误！', 'danger');
                        return false;
                    }
                });
            }
        });
		*/
    });
</script>
</body>
</html>