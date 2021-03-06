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
    $page_nav["日志管理"]["sub"]["登录日志"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>登录日志</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <div class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="id" data-type="numeric" data-order="desc">序号</th>
                                            <th data-column-id="username">登录用户</th>
                                            <th data-column-id="loginip">登录IP地址</th>									
                                            <th data-column-id="createtime" data-formatter="createtime">登录时间</th>
											<th data-column-id="class">登录位置</th>
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

    })

    function loadData()
    {
        $("#data-table-basic").bootgrid({
            ajax: true,
            post: function () {
                /* To accumulate custom parameter with the request object */
                return {

                };
            },
            url: "<{:U('log/index')}>",
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