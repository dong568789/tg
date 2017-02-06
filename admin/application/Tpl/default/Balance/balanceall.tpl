<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "用户结算列表";
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
                <h2>所有结算单</h2>
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
                                            <th data-column-id="id" data-type="numeric" data-order="desc">账单号</th>
                                            <th data-column-id="account">手机号/用户名</th>
                                            <th data-column-id="realname">联系人</th>
                                            <th data-column-id="applytime">申请日期</th>
                                            <th data-column-id="totalamount">提款金额</th>
                                            <th data-column-id="actualamount">税后金额</th>
                                            <th data-column-id="balancestatus" data-formatter="balancestatus">申请状态</th>
                                            <th data-column-id="paidamount">实际结算</th>
                                            <th data-column-id="beizhu">备注</th>
                                            <th data-column-id="commands" data-formatter="link" data-sortable="false">查看详情</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="balance" item="vo" key="k">
                                        <tr>
                                            <td><{$vo['id']}></td>
                                            <td><{$vo['account']}></td>
                                            <td><{$vo['realname']}></td>
                                            <td><{$vo['applytime']}></td>
                                            <td><{$vo['totalamount']}></td>
                                            <td><{$vo['actualamount']}></td>
                                            <td><{$vo['balancestatus']}></td>
                                            <td>
                                                <if condition="$vo['accounttype'] eq '3' ">
                                                    <{$vo['paidamount']|round=###*10}>游侠币
                                                <else />
                                                    <{$vo['paidamount']}>元
                                                </if>
                                            </td>
                                            <td><{$vo['beizhu']}></td>
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
            formatters: {
                "link": function(column, row)
                {
                    return "<a href=\"/balancedetail/"+row.id+"/\">查看详情</a>";
                },
                "balancestatus": function(column, row){
                    if(row.balancestatus=="账单有误"){
                        return "<span style='color:red;'>账单有误</span>";
                    }else{
                        return row.balancestatus;
                    }
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\">"+
                "<p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p>" +
                "<div class=\"daterange form-group\"><div class=\"input-group\"><span class=\"zmdi input-group-addon zmdi-calendar\"></span><input type=\"text\" class=\"search-field form-control\" placeholder=\"请选择日期\" name=\"daterange\" id=\"daterange\" readonly=\"true\"><a id=\"viewdaterange\" class=\"input-group-addon btn-info\">查看</a></div></div>" +
                "<div class='form-group col-sm-3'><select class='selectpicker form-group' id='select-balancestatus'><option value='0'>结算单--全部</option><option value='1'>待审核</option><option value='4'>账单有误</option><option value='3'>结算单审核</option><option value='2'>已结算</option></select></div>"+
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
                        if (data.status == "1") {
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

        $('#select-balancestatus').change(function(){
            search();
        })
    });

    function search() {
        var balancestatus = $('#select-balancestatus').val();

        $.ajax({
            type: "POST",
            url: "/index.php?m=Balance&a=searchBalance",
            data: {balancestatus:balancestatus},
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $(".table-responsive").hide();
                $("#data-table-basic-footer").hide();
                $("#loading").show();
            },
            success: function (data) {
                console.log(data);
                $("#loading").hide();
                $(".table-responsive").show();
                $("#data-table-basic-footer").show();
                $("#data-table-basic").bootgrid("clear");
                if (data.info == "success") {
                    $("#data-table-basic").bootgrid("append", data.data);
                } else {
                    $('#rechargecontainer').html("");
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
</script>
</body>
</html>