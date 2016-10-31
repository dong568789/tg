<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "账目明细";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";

?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />
<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["财务管理"]["active"] = true;
    $page_nav["财务管理"]["sub"]["所有结算单"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>账目明细</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header">
                        </div>
                        <div class="card-body ">
                            <div  class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
											<tr>
												<th data-column-id="id" data-type="numeric" data-order="desc" data-visible="false">序号</th>
												<th data-column-id="date">日期</th>
												<th data-column-id="dailyactive">活跃玩家</th>
												<th data-column-id="newpeople">新增注册</th>
												<th data-column-id="paypeople">充值人数</th>
												<th data-column-id="payrate" data-formatter="payrateformat">充值比例</th>
												<th data-column-id="dailyjournal">每日流水</th>
                                                <th data-column-id="sharerate">分成比例</th>
                                                <th data-column-id="channelrate">渠道费</th>
												<th data-column-id="dailyincome">每日收入</th>
											</tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="daily" item="vo" key="k">
                                            <tr>
                                                <td><{$vo['id']}></td>
                                                <td><{$vo['date']}></td>
												<td><{$vo['dailyactive']}></td>
                                                <td><{$vo['newpeople']}></td>
                                                <td><{$vo['paypeople']}></td>
												<td><{$vo['payrate']}></td>
												<td><{$vo['dailyjournal']}></td>
                                                <td><{$vo['sharerate']}></td>
                                                <td><{$vo['channelrate']}></td>
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
        $("#data-table-basic").bootgrid({
            css: {
                icon: 'zmdi',
                iconColumns: 'zmdi-menu',
                iconDown: 'zmdi-caret-down-circle',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-caret-up-circle'
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p>" +
                "<div class=\"daterange form-group\"><div class=\"input-group\"><span class=\"zmdi input-group-addon zmdi-calendar\"></span><input type=\"text\" class=\"search-field form-control\" placeholder=\"请选择日期\" name=\"daterange\" id=\"daterange\" readonly=\"true\"><a id=\"viewdaterange\" class=\"input-group-addon btn-info\">查看</a></div></div>" +
                "</div></div></div>"
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

        $('#viewdaterange').click(function() {
            var date = $('#daterange').val();
            if (date != "") {
                var start = date.substr(0, 10);
                var end = date.substr(-10, 10);
                $.ajax({
                    type : 'POST',
                    url : "index.php?m=balance&a=viewDaterangeDaily",
                    data : {startdate : start, enddate : end},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        console.log(data);
                        if (data.info == "success") {
                            $("#data-table-basic").bootgrid("clear");
                            $("#data-table-basic").bootgrid("append", data.data);
                            notify('数据获取成功', 'success');
                        } else {
                            $("#data-table-basic").bootgrid("clear");
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

    });
</script>
</body>
</html>