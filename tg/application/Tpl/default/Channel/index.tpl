<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "渠道管理";
$page_css[] = "public/css/public.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";

?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["渠道管理"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>渠道管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="p-20">
                            <div class="card-header" style="text-align: right;">
                                <a href="/new_channel/" class="btn btn-primary">新建渠道</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-responsive table-hover">
                                    <thead>
                                    <tr>
                                        <th>渠道名称</th>
                                        <th>创建时间</th>
                                        <th>渠道类型</th>
                                        <th>日均流量</th>
                                        <th style="width: 20%;">渠道介绍</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="channelcontainer">
                                    <foreach name="channel" item="vo" key="k">
                                    <tr>
                                        <td><{$vo['channelname']}></td>
                                        <td><{$vo['createtime']}></td>
                                        <td><{$vo['channeltype']}></td>
                                        <td><{$vo['channelsize']}></td>
                                        <td><{$vo['description']}></td>
                                        <td>
                                            <if condition="$vo['channelname'] neq $defaultChannelname ">
                                                <a href="/index.php?m=channel&a=channeldetail&id=<{$vo['channelid']}>">编辑</a>
                                            &nbsp; <a href="javascript:" onclick="deleteChannel('<{$vo['channelid']}>');" id="delete-<{$vo['channelid']}>">删除</a>
                                            </if>
                                        </td>
                                    </tr>
                                    </foreach>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center holder">
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

<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>

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

    //删除渠道号
    function deleteChannel (chanelid) {
        var confirmcontent = "确认删除该渠道？";
        if(confirm(confirmcontent)) {
            $.ajax({
                type : 'POST',
                url : "index.php?m=channel&a=deletechannel",
                data : {id : chanelid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        $('#delete-'+chanelid).parent().parent().addClass("display-none");
                    } else {
                        notify(data.info, 'danger');
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        } else {
            return false;
        }
    }

    $(document).ready(function() {
        $("div.holder").jPages({
            containerID    : "channelcontainer",
            scrollBrowse   : false,
            perPage: 20
        });
    })
</script>


</body>
</html>