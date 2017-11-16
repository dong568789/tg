<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "用户结算列表";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";

?>
<style>
    #data-table-basic tr th{text-align:center;font-weight:bold;}
</style>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["财务管理"]["active"] = true;
    $page_nav["财务管理"]["sub"]["财务统计"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>财务统计</h2>
            </div>
            <div class="clearfix modal-preview-demo">
                <div class="modal" id="editOffline_coin" style="display:none;"> <!-- Inline style just for preview -->
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title f-700 p-b-5 text-center">现金结算</h4>
                            </div>

                            <fieldset class="col-sm-10">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label ">金额</label>
                                    <div class="col-sm-7">
                                        <p class="form-control">
                                            <input type="text" id="offline_coin" class="form-control">
                                            <input type="hidden" id="financialid" value="">
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <a href="javascript:void(0);" role="button" onclick="subFinancial()" class="btn btn-success btn-block btn-lg">提交</a>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-link" style="position: absolute;top:0px;right: 0px;" data-dismiss="modal" id="downloadurl-modalclose">关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header">
                        </div>
                        <div class="card-body ">
                            <div  class="p-20">
                                <div id="data-table-basic-header" class="bootgrid-header container-fluid">
                                    <div class='daterange form-group' style="border:none;">
                                        <select class='selectpicker form-group' id='select-sourcetype'>
                                            <option value='0'>全部</option>
                                            <option value='1'>公会</option>
                                            <option value='4'>CPS</option>
                                            <option value='3'>称动MM</option>
                                        </select>
                                    </div>
                                    <div class="daterange form-group">
                                        <div class="input-group">
                                            <span class="zmdi input-group-addon zmdi-calendar"></span>
                                            <input type="text" class="search-field form-control" placeholder="请选择日期" name="daterange" id="daterange" readonly="true">
                                            <a id="viewdaterange" class="input-group-addon btn-info">查看</a>
                                        </div>

                                    </div>
                                    <div class="daterange form-group" style="font-weight: bold;width: 880px;
                                    line-height: 25px;border: none;">
                                        <span style="margin-right: 10px;">收入 = 游戏直充 + 买币直充 + APP活动 + 买券 + 线下买币 + 游侠币结算</span>
                                        <span style="margin-right: 10px;">支出 = 渠道分成 + CP分成</span>
                                        <span style="margin-right: 10px;">潜在支出 = 渠道币 + 玩家币 + 代金券总额/2</span>
                                        <p>预估收入 = 收入 - 支出 - 潜在支出</p>
                                    </div>

                                    <div class="daterange form-group" style="width:90px;float: right">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-primary pull-right waves-effect" id="export" data-result="" style="text-transform: none;">导出EXCEL</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <table id="data-table-basic" class="table table-hover table-bordered
                                    table-vmiddle" style="text-align: center">
                                        <thead>
                                        <tr>
                                            <th rowspan="2">日期</th>
                                            <th colspan="3">玩家</th>
                                            <th colspan="4">渠道</th>
                                            <th>CP</th>
                                            <th colspan="3" >库存</th>
                                            <th colspan="4">汇总</th>
                                        </tr>
                                        <tr>
                                            <th data-column-id="amount" style="background-color: green;">游戏直充</th>
                                            <th data-column-id="buy_coin" style="background-color: green;">买币直充</th>
                                            <th data-column-id="app" style="background-color: green;">APP活动</th>
                                            <th data-column-id="balance_coin" style="background-color: green;">游侠币结算</th>
                                            <th data-column-id="cash_over" style="background-color: yellow;">渠道分成</th>
                                            <th data-column-id="buy_voucher" style="background-color: green;">买券</th>
                                            <th data-column-id="offline_coin" style="background-color: green;">线下买币</th>
                                            <th data-column-id="cp_into" style="background-color: yellow;">分成</th>
                                            <th data-column-id="agent_coin" style="background-color: yellow;">渠道币(RMB)</th>
                                            <th data-column-id="game_coin" style="background-color: yellow;">玩家币(RMB)</th>
                                            <th data-column-id="voucher" style="background-color: yellow;">代金券总额</th>
                                            <th data-column-id="earning" style="background-color: #dedede;">收入</th>
                                            <th data-column-id="expend" style="background-color: #dedede;">支出</th>
                                            <th data-column-id="expend_qz" style="background-color: #dedede;">潜在支出</th>
                                            <th data-column-id="earning_qz" style="background-color: #dedede;">预估收入</th>
                                        </tr>
                                        </thead>
                                        <tbody id="J_content">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center holder" id="pages">
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
<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>
<script src="__ROOT__/plus/js/clipboard.min.js"></script>

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
        loadData('','');


        $('#viewdaterange').click(function() {
            loadData();
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

        $('#export').on('click', function(){
            var date = $('#daterange').val();
            var start = '',end = '';
            if (date != "") {
                start = date.substr(0, 10);
                end = date.substr(-10, 10);
            }
            var url = "<{:U('Financial/export')}>";
            loading(true);
            $.ajax({
                type : 'POST',
                url : url,
                data : {startdate : start, enddate : end},
                cache : false,
                dataType : 'json',

                success : function (data) {
                    if (data.status == "1") {
                        loading(false);
                        window.location.href = '/' + data.url;
                    } else {
                        loading(false);
                        notify('数据获取失败，没有符合条件的数据', 'danger');
                    }
                    return false;
                },
                error : function (xhr) {
                    loading(false);
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        })

        $("#downloadurl-modalclose").click(function() {
            $("#editOffline_coin").hide();
        });

        var h = $(window).height();
        $('.modal-dialog').css("margin-top",(h-237)/2 + 'px');

        $(document).on("dblclick",'.offline_coin',function(){
            var _t = $(this).html();
            var id = $(this).attr('lang');
            $('#financialid').val(id);
            $('#offline_coin').val(_t);
            $('#editOffline_coin').show();
        });


        $('#select-sourcetype').change(function(){
            loadData();
        });
    });

    function subFinancial()
    {
        if(!confirm('确认修改？')){
            return false;
        }
        var id = $('#financialid').val();
        var offlineCoin = $('#offline_coin').val();
        if(!id){
            notify('参数错误','danger');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/index.php?m=Financial&a=editOfflineCoin",
            data: {id:id,offline_coin:offlineCoin},
            cache: false,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.info == "success") {
                    notify('修改成功!','success');
                    window.location.reload();
                } else {
                    notify('操作失败', 'danger');
                }
                return false;
            },
            error : function (xhr) {
                notify('系统错误！', 'danger');
                return false;
            }
        });
    }
    //loading
    function loading(flag){
        if(flag){
            $('.page-loader').css('background','none');
            $('.page-loader').find('p').html('');
            $('.page-loader').show();
        }else{
            $('.page-loader').hide();
            $('.page-loader').css('background','#FFF');
        }
    }

    function loadData()
    {
        var date = $('#daterange').val();
        var start,end;
        if (date) {
            start = date.substr(0, 10);
            end = date.substr(-10, 10);
        }
        var sourceType = $('#select-sourcetype').val();

        $('#data-table-basic').find('tbody').html('<tr><td colspan="8" class="loading" style="padding: 20px 0px 1650px;">加载中...</td></tr>');
        $.ajax({
            type : 'POST',
            url : "index.php?m=Financial&a=ajaxData",
            data : {startdate : start, enddate : end, sourcetype:sourceType},
            cache : false,
            dataType : 'json',
            success : function (data) {
               console.log(data);
                var html = '';
                if(data.total > 0){
                    for(var i=0;i<data.rows.length;i++){
                        var d = data.rows[i];
                        html += "<tr>"
                                + "<td>" + d.time + "</td>"
                                + "<td>" + d.amount + "</td>"
                                + "<td>" + d.buy_coin + "</td>"
                                + "<td>" + d.app + "</td>"
                                + "<td>" + d.balance_coin + "</td>"
                                + "<td>" + d.cash_over + "</td>"
                                + "<td>" + d.buy_voucher + "</td>"
                                + "<td  lang='" + d.id + "' class='offline_coin'>" + d.offline_coin + "</td>"
                                + "<td>" + d.cp_into + "</td>"
                                + "<td>" + d.agent_coin + "</td>"
                                + "<td>" + d.game_coin + "</td>"
                                + "<td>" + d.voucher + "</td>"
                                + "<td>" + d.earning + "</td>"
                                + "<td>" + d.expend + "</td>"
                                + "<td>" + d.expend_qz + "</td>"
                                + "<td>" + d.earning_qz + "</td>"
                                +"</tr>"
                    }
                    $("#data-table-basic > tbody").html(html);

                    $("#pages").jPages({
                        containerID    : "J_content",
                        scrollBrowse   : false,
                        perPage: 20
                    });
                    notify('数据获取成功', 'success');
                }else{
                    $("#data-table-basic > tbody").html(html);
                    notify('暂无数据！', 'danger');
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