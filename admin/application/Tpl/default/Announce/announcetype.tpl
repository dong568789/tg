<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台公告管理";
$page_css[] = "";
?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["公告管理"]["active"] = true;
    $page_nav["公告管理"]["sub"]["新增公告类型"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>公告类型管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="p-20">
                            <div class="card-header">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-responsive table-hover">
                                    <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>公告类型</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="categorycontainer">
                                    <foreach name="announcetypename" item="vo" key="k">
                                        <tr>
                                            <td><{$vo['id']}></td>
                                            <td><{$vo['announcetypename']}></td>
                                            <td><{$vo['createtime']}></td>
                                            <td><a id="delete-<{$vo['id']}>" href="#" onclick="deleteAnnouncetype(<{$vo['id']}>);"> 删除</a></td>
                                        </tr>
                                    </foreach>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-20">
                                <form id="addannouncetype" class="form-horizontal" role="form" action="index.php?m=announce&a=addannouncetype" method="post">
                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 control-label f-15">公告类型名字</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" name="announcetypename" id="announcetypename" placeholder="如“系统公告”">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" id="addsubmit" class="btn btn-primary btn-lg m-r-15">新增</button>
                                            <a href="/announcetype/" class="btn btn-default btn-lg c-gray">取消</a>
                                        </div>
                                    </div>
                                </form>
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

    function deleteAnnouncetype (announcetypeid) {
        var confirmcontent = "确认删除该条公告类型？";
        if(confirm(confirmcontent)) {
            $.ajax({
                type : 'POST',
                url : "index.php?m=announce&a=deleteAnnouncetype",
                data : {id : announcetypeid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        $('#delete-'+announcetypeid).parent().parent().addClass("display-none");
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

    $(document).ready(function(){
        var $addannouncetype = $('#addannouncetype').validate({
            rules : {
                announcetypename : {
                    required : true
                }
            },

            messages : {
                announcetypename : {
                    required : '公告类别不能为空'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        $('#addsubmit').click(function() {
            if ($('#addannouncetype').valid()) {
                var confirmcontent = "确认新增这条公告类型？";
                if(confirm(confirmcontent)) {
                    $('#addannouncetype').ajaxSubmit({
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "success") {
                                var announcetypename = $('#announcetypename').val();
                                $('#categorycontainer').append("<tr><td>"+data.info+"</td><td>"+announcetypename+"</td><td>刚才</td><td><a id=\"delete-"+data.info+"\" href=\"#\" onclick=\"deleteAnnouncetype("+data.info+");\"> 删除</a></td></tr>");
                                $('#announcetypename').val("");
                                notify("新增公告类型成功。", 'success');
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
            return false;
        });
    })
</script>
</body>
</html>