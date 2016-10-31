<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "结算中心";
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
                <h2>结算中心</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body">
                            <div class="row m-t-25 p-2 m-b-25 m-l-25 m-r-25">
                                <div class="col-xs-4 ">
                                    <div class="bgm-blue brd-2 p-15">
                                        <div class="c-white m-b-5">未提现收入</div>
                                        <if condition = "($money['unwithdraw'] neq '') AND ($money['unwithdraw'] neq 0)">
                                            <h2 class="m-0 c-white f-300" id="income">￥<{$money['unwithdraw']}><a href="/withdraw/" class="f-13 m-l-10 c-white">我要提现</a></h2>
                                            <else/>
                                            <h2 class="m-0 c-white f-300" id="income">￥0<a href="/withdraw/" class="f-13 m-l-10 c-white">我要提现</a></h2>
                                        </if>

                                    </div>
                                </div>

                                <div class="col-xs-4">
                                    <div class="bgm-green brd-2 p-15">
                                        <div class="c-white m-b-5">已结算</div>
                                        <if condition = "($money['settled'] neq '') AND ($money['settled'] neq 0)">
                                            <h2 class="m-0 c-white f-300">￥<{$money['settled']}></h2>
                                            <else/>
                                            <h2 class="m-0 c-white f-300">￥0</h2>
                                        </if>
                                    </div>
                                </div>

                                <div class="col-xs-4">
                                    <div class="bgm-amber brd-2 p-15">
                                        <div class="c-white m-b-5">未结算</div>
                                        <if condition = "($money['unsettled'] neq '') AND ($money['unsettled'] neq 0)">
                                            <h2 class="m-0 c-white f-300">￥<{$money['unsettled']}></h2>
                                            <else/>
                                            <h2 class="m-0 c-white f-300">￥0</h2>
                                        </if>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-content p-20">

                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="id" data-type="numeric" data-order="desc" data-visible="false">序号</th>
                                            <th data-column-id="applytime">申请日期</th>
                                            <th data-column-id="circletime" >申请的周期</th>
                                            <th data-column-id="totalamount" >提现总金额</th>
                                            <th data-column-id="taxrate" >税率</th>
                                            <th data-column-id="paidamount" >实际打款金额</th>
                                            <th data-column-id="beizhu">备注</th>
                                            <th data-column-id="balancestatus" data-formatter="balancestatus">结算单状态</th>
                                            <th data-column-id="link" data-formatter="link" data-sortable="false">查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody id="balancecontainer">
                                        <foreach name="balance" item="vo" key="k">
                                        <tr>
                                            <td><{$vo['id']}></td>
                                            <td><{$vo['applytime']}></td>
                                            <td><{$vo['startdate']}> ~ <{$vo['enddate']}></td>
                                            <td><{$vo['totalamount']}></td>
                                            <td><{$vo['taxrate']}></td>
                                            <td>
                                                <if condition="$vo['accounttype'] eq '3' ">
                                                    <{$vo['paidamount']|round=###*10}>游侠币
                                                <else />
                                                    <{$vo['paidamount']}>元
                                                </if>
                                            </td>
                                            <td><{$vo['beizhu']}></td>
                                            <td><{$vo['balancestatusStr']}></td>
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
                "link": function(column, row){
                    return "<a href=\"/balancedetail/"+row.id+"/\">查看详情</a>";
                },
                "balancestatus": function(column, row){
                    if(row.balancestatus=="账单有误"){
                        return "<span style='color:red;'>账单有误</span> <button onclick='ReDowithdraw(this,"+row.id+");'>重新提现</button>";
                    }else{
                        return row.balancestatus;
                    }
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 p-b-25 actionBar\">" +
                "<div class='col-sm-5 pull-left'><button class='btn btn-default  m-r-5' id='currentmonth'>本月</button>" +
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
                    url : "index.php?m=balance&a=viewDaterangeBalance",
                    data : {startdate : start, enddate : end},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        console.log(data['applytime']);
                        //return false;
                        if (data.info == "success") {
                            $("#data-table-basic").bootgrid("clear");
                            $("#data-table-basic").bootgrid("append", data.data);
                            notify('数据获取成功', 'success');
                        } else {
                            $("#data-table-basic").bootgrid("clear");
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
        });

        //本月，七天，三十天
        function refreshPage (start, end) {
            $.ajax({
                type: "POST",
                url: "/index.php?m=balance&a=refresh",
                data: {startdate:start, enddate:end},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.info == "success") {
                        $("#data-table-basic").bootgrid("clear");
                        $("#data-table-basic").bootgrid("append", data.data);

                        notify('数据获取成功', 'success');
                    } else {
                        $('#balancecontainer').html("");
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

        $('#latestmonth').click(function(){
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
            //alert(startdate);
            refreshPage(startdate, enddate);
        });

        $('#latestweek').click(function(){
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
            refreshPage(startdate, enddate);
        });

        //本月
        $('#currentmonth').click(function(){
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
            refreshPage(startdate, enddate);
        });
    })

    //重新提现(提交申请)
    function ReDowithdraw(_this,balanceid) {
        swal({
            title: "确认重新提交结算申请？",
            text: "确认重新提交结算申请？",
            showCancelButton: true,
            confirmButtonColor: "#00ccff",
            confirmButtonText: "确认",
            cancelButtonText: "取消",
            closeOnConfirm: false
        }, function(){
            $(_this).attr("disabled",'disabled');
            swal({
                title: "请稍侯...",   
                text: "正在生成结算单，请稍后", 
                type: "hold",
                showConfirmButton: false
            });
            $.ajax({
                type : 'POST',
                url : "index.php?m=balance&a=reDoWithdraw",
                data : {balanceid : balanceid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.info == "success") {
                        swal({
                            title: "已提交",   
                            text: "提现申请已成功提交", 
                            type: "success",
                            showConfirmButton: true
                        }, function(isConfirm){   
                            // if (isConfirm) {     
                            //     self.location.href = "/balance/";   
                            // }
                            $(_this).parents('tr').find('td').eq(2).html(data.data);
                            $(_this).parent().html('待审核');
                        });
                    } else {
                        $(_this).removeAttr("disabled");
                        swal({
                            title: "出错了",   
                            text: data.info, 
                            type: "error",
                            confirmButtonText: "确认"
                        });
                    }
                    return false;
                },
                error : function (xhr) {
                    $(_this).removeAttr("disabled");
                    swal({
                        title: "出错了",   
                        text: "系统错误", 
                        type: "error",
                        confirmButtonText: "确认"
                    });
                    return false;
                }
            });
        });
    }
</script>


</body>
</html>