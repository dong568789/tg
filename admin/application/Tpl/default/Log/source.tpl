<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "登录日志";
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
    $page_nav["日志管理"]["active"] = true;
    $page_nav["日志管理"]["sub"]["申请资源"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>申请资源</h2>
            </div>
            <div class="clearfix modal-preview-demo">
                <div class="modal" id="editRate" style="display:none;"> <!-- Inline style just for preview -->
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title f-700 p-b-5 text-center">修改分成比例</h4>
                            </div>

                            <fieldset class="col-sm-10">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label ">分成比例</label>
                                    <div class="col-sm-7">
                                        <p class="form-control">
                                            <input type="text" id="sourcesharerate" class="form-control">
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label ">通道费</label>
                                    <div class="col-sm-7">
                                        <p class="form-control">
                                            <input type="text" id="sourcechannelrate" class="form-control">
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" id="sourceid" value="">
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <a href="javascript:void(0);" role="button" onclick="subRate()" class="btn btn-success btn-block btn-lg">提交</a>
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
                    <div class="card">
                        <div class="card-header">
                            <div id="data-table-basic-header" class="bootgrid-header container-fluid p-b-0 m-b-0">
                                <div class="actionBar m-t-20">
                                    <div class="daterange form-group pull-right">
                                        <div class="input-group">
                                            <span class="zmdi input-group-addon zmdi-calendar"></span>
                                            <input class="search-field form-control" placeholder="请选择日期" name="daterange" id="daterange" readonly="true" type="text"><a id="viewdaterange" class="input-group-addon btn-info">查看</a>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="id" data-type="numeric" data-order="desc">序号</th>
                                            <th data-column-id="username">申请用户</th>
                                            <th data-column-id="content">内容</th>
                                            <th data-column-id="sourcesharerate">分成比例</th>
                                            <th data-column-id="sourcechannelrate">通道费</th>
                                            <th data-column-id="createtime" data-formatter="createtime">申请时间</th>
                                            <th data-column-id="operation">操作</th>
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
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/daterangepicker/daterangepicker.js"></script>


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
            delay: 2500,
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



    $(document).ready(function() {
        //Basic Example
        loadData();

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

        $('#viewdaterange').click(function() {
            $("#data-table-basic").bootgrid('destroy');
            loadData();
        });

        $("#downloadurl-modalclose").click(function() {
            $("#editRate").hide();
        });

        var h = $(window).height();
        $('.modal-dialog').css("margin-top",(h-237)/2 + 'px');

    })

    function editRate(sourceid,sourcesharerate,sourcechannelrate){
        $('#sourceid').val(sourceid);
        $('#sourcesharerate').val(sourcesharerate);
        $('#sourcechannelrate').val(sourcechannelrate);
        $('#editRate').show();
    }

    function subRate()
    {
        if(!confirm('确认修改？')){
            return false;
        }
        var sourceid = $('#sourceid').val();
        var sourcesharerate = $('#sourcesharerate').val();
        var sourcechannelrate = $('#sourcechannelrate').val();
        if(!sourceid){
            notify('参数错误','danger');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "/index.php?m=Log&a=editRate",
            data: {sourceid:sourceid,sourcesharerate:sourcesharerate,sourcechannelrate:sourcechannelrate},
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

    function loadData()
    {
        var date = $('#daterange').val();
        var startdate, enddate;
        if (date) {
            startdate = date.substr(0, 10);
            enddate = date.substr(-10, 10);
        }
        $("#data-table-basic").bootgrid({
            ajax: true,
            post: function () {
                /* To accumulate custom parameter with the request object */
                return {
                    startdate: startdate,
                    enddate: enddate,
                };
            },
            url: "<{:U('log/source')}>",
            css: {
                icon: 'zmdi',
                iconColumns: 'zmdi-menu',
                iconDown: 'zmdi-caret-down-circle',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-caret-up-circle'
            },

            templates: {
                header: ""
            },
            selection: true,
            multiSelect: true,
            rowSelect: true,
            keepSelection: true,
            labels: {
                loading: "Loading...", //加载时显示的内容
                noResults: '没有符合条件的数据'//未查询到结果是显示内容
            },
        });
        $(".card .table th").css("width","3%");
    }
</script>

</body>
</html>