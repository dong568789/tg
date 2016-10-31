<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "修改渠道";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";

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
                <h2>修改渠道</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <div class="p-20">
                                <form class="form-horizontal" id="editchannel" role="form">
                                    <div class="form-group m-t-25">
                                        <label for="channelname" class="col-sm-3 control-label f-15 m-t-5">渠道名称</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" name="channelname" id="channelname" value="<{$channel['channelname']}>" maxlength="10">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label for="channeltype" class="col-sm-3 control-label f-15 m-t-5">渠道类型</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <select class="selectpicker" name="channeltype" id="channeltype">
                                                    <option <if condition="$channel['channeltype'] eq 应用"> selected </if>>应用</option>
                                                    <option <if condition="$channel['channeltype'] eq 公会"> selected </if>>公会</option>
                                                    <option <if condition="$channel['channeltype'] eq 网站"> selected </if>>网站</option>
                                                    <option <if condition="$channel['channeltype'] eq 个人"> selected </if>>个人</option>
                                                    <option <if condition="$channel['channeltype'] eq 其他"> selected </if>>其他</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label for="channelsize" class="col-sm-3 control-label f-15 m-t-5">日均流量</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <select class="selectpicker" name="channelsize" id="channelsize">
                                                    <option <if condition="$channel['channelsize'] eq '10000 PV以下'"> selected </if>>10000 PV以下</option>
                                                    <option <if condition="$channel['channelsize'] eq '10000 - 20000 PV'"> selected </if>>10000 - 20000 PV</option>
                                                    <option <if condition="$channel['channelsize'] eq '20000 - 50000 PV'"> selected </if>>20000 - 50000 PV</option>
                                                    <option <if condition="$channel['channelsize'] eq '50000 PV以上'"> selected </if>>50000 PV以上</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">渠道介绍</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <textarea class="form-control" rows="3" name="description" id="description" maxlength="300"><{$channel['description']}></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" class="btn btn-primary btn-lg m-r-15" data-id="<{$channel['channelid']}>" id="savechannel">保存信息</button>
                                            <a href="/channel/" type="button" class="btn btn-default btn-lg c-gray">取消</a>
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

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>

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
    $(document).ready(function() {
            //渠道规则
            /*jQuery.validator.addMethod("checkCHANNELNAME", function(value, element) {
                var reg =  /^[0-9a-zA-Z\u4e00-\u9fa5]+$/;
                return this.optional(element) || reg.test(value);
            }, "请输入英文小写字母或数字！");*/

            $('#savechannel').click(function() {
            var $addchannel = $('#editchannel').validate({
                rules : {
                    channelname : {
                        required : true
                    }
                },
                messages : {
                    channelname : {
                        required : '渠道名不得为空'
                    }
                },


                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });

            if ($('#editchannel').valid()) {
                var channelname = $("#channelname").val();
                var channeltype = $("#channeltype option:selected").val();
                var channelsize = $("#channelsize option:selected").val();
                var description = $("#description").val();
                var channelid = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "index.php?m=channel&a=editchannel",
                    data: {channelname:channelname,channeltype:channeltype,channelsize:channelsize,description:description,channelid:channelid},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.data == "success") {
                            var value = data.info;
                            var html = '';
                            html = '<tr>'+
                            '<td>'+value.channelname+'</td>'+
                            '<td>'+value.createtime+'</td>'+
                            '<td>'+value.channeltype+'</td>'+
                            '<td>'+value.channelsize+'</td>'+
                            '<td>'+value.description+'</td>'+
                            '<td><a href="/index.php?m=channel&a=channeldetail&id='+value.channelid+'">编辑</a> &nbsp;<a href="javascript:" onclick="deleteChannel('+value.channelid+');" id="delete-'+value.channelid+'">删除</a></td>'+
                            '</tr>';
                            $(html).insertBefore("#channelcontainer");
                            notify('数据更新成功', 'success');
                            setTimeout(function () {
                                location.href = '/channel/';
                            }, 1000);
                        }else{
                            notify(data.info, 'danger');
                        }
                        return false;
                    }
                });
                return false;
            }

        });
    })
</script>
</body>
</html>