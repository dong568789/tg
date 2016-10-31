<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "所有用户";
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
    $page_nav["用户管理"]["active"] = true;
    $page_nav["用户管理"]["sub"]["所有用户"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>所有用户</h2>
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
                                            <th data-column-id="account">用户名</th>
                                            <th data-column-id="projectname">项目名</th>
                                            <th data-column-id="sourcetypestr">渠道类型</th>
                                            <th data-column-id="bindmobile" data-visible="false">绑定手机</th>
                                            <th data-column-id="bindemail" data-visible="false">绑定邮箱</th>
                                            <th data-column-id="usertype">会员类型</th>
                                            <th data-column-id="realname">联系人名字</th>
											<th data-column-id="contactmobile">联系电话</th>
                                            <th data-column-id="companyname">公司名字</th>
                                            <th data-column-id="channelbusiness">渠道商务</th>
                                            <th data-column-id="createtime">创建时间</th>
                                            <if condition="$seeSoureceRight eq 'ok'"><th data-column-id="userrate" data-formatter="userrate">渠道</th></if>
                                            <th data-column-id="isverified" data-visible="false">是否通过审核</th>
                                            <if condition="$editUser eq 'ok' || $ptbAuthorization eq 'ok' || $auditUser eq 'ok'"><th data-column-id="operation" data-formatter="link">操作</th></if>
                                        </tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
                                        <foreach name="users" item="vo" key="k">
											<tr>
                                                <td><{$vo['userid']}></td>
                                                <td><{$vo['account']}></td>
                                                <td><{$vo['projectname']}></td>
                                                <td><{$vo['sourcetypestr']}></td>
                                                <td><{$vo['bindmobile']}></td>
                                                <td><{$vo['bindemail']}></td>
												<if condition = "$vo['usertype'] eq 1">
												<td>个人</td>
												<elseif condition = "$vo['usertype'] eq 2"/>
												<td>公司</td>
												<else/>
												<td>未知</td>
												</if>
                                                <td><{$vo['realname']}></td>
												<td><{$vo['contactmobile']}></td>
                                                <td><{$vo['companyname']}></td>
                                                <td><{$vo['channelbusiness']}></td>
                                                <td><{$vo['createtime']}></td>
                                                <if condition="$seeSoureceRight eq 'ok'"><td></td></if>
												<td><{$vo['isverified']}></td>
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

	function deleteUser (id) {
		var confirmcontent = "确认删除该用户？";
		if(confirm(confirmcontent)) {
			$.ajax({    
				type : 'POST',        
				url : "index.php?m=user&a=deleteUser", 
				data : {userid : id},
				cache : false,    
				dataType : 'json',    
				success : function (data) {
					if (data.data == "success") {
						notify(data.info, 'success');
						$('#delete-'+id).parent().parent().addClass("display-none");
					} else if(data.data == "error505") {
						location.href = "/error505/";
					} else{
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

    // 一键分包
    function fastApply(_this,userid){
        // alert();
        var confirmcontent = "确认给该用户一键分包吗？";
        if(confirm(confirmcontent)) {
            swal({
                title: "请稍侯...",   
                text: "正在分包中，请等待完成，不要关闭页面", 
                type: "hold",
                showConfirmButton: false
            });
            $.ajax({    
                type : 'POST',        
                url : "<{:U('User/fastApply')}>", 
                data : {userid : userid},
                cache : false,    
                dataType : 'json',    
                success : function (data) {
                    if (data.data == "success") {
                        swal({
                            title: "成功",   
                            text: data.info, 
                            type: "success",
                            confirmButtonText: "确认"
                        });
                    }else{
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
                    swal({
                        title: "出错了",   
                        text: '系统错误！', 
                        type: "error",
                        confirmButtonText: "确认"
                    });
                    return false;
                }
            });
        } else {
            return false;
        }
    }
	
    $(document).ready(function() {
		//Basic Example
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
                    var str='';
                    if(parseInt(row.isverified) == 1){
                        if('<{$editUser}>' == 'ok'){
                            str+="<a href=\"/userdetail/"+row.id+"/\">编辑</a>&nbsp;|&nbsp;<a href=\"#\" id=\"delete-"+row.id+"\" onclick=\"deleteUser("+row.id+");\">删除</a>";
                        }

                        if('<{$ptbAuthorization}>' == 'ok'){
                            if(str!=''){
                                str+="&nbsp;|&nbsp;"
                            }
                            str+="<a href=\"/userpreauth/"+row.id+"/\">预授权</a>";
                        }

                        if('<{$fastApply}>' == 'ok'){
                            if(str!=''){
                                str+="&nbsp;|&nbsp;"
                            }
                            str+="<a href=\"javascript:;\"onclick=\"fastApply(this,"+row.id+");\" >一键申请资源</a>";
                        }
                    } else if (parseInt(row.isverified) == 0 && '<{$auditUser}>' == 'ok') {
                        str+="<a href=\"/userdetail/"+row.id+"/\" class=\"btn btn-warning btn-xs\">审核新用户</a>";
                    } else if (parseInt(row.isverified) != 0 &&parseInt(row.isverified) != 1 && '<{$auditUser}>' == 'ok')  {
                        str+="<a href=\"/userdetail/"+row.id+"/\" class=\"btn btn-danger btn-xs\">未通过审核</a>";
                    } else{
                        str+="没有操作权限";
                    }

                    return str;
				},
				"userrate": function(column, row)
				{
                    if ('<{$seeSoureceRight}>' == 'ok') {
                        return "<a href=\"/usersource/" + row.id + "/\">查看</a>";
                    }
				}
			},
			templates: {
				header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p><if condition="$newuser eq 'ok'"><a class=\"{{css.addnewuser}}\" href=\"/newuser/\">新增一个用户</a></if></div></div></div>"
			}
        });
    })
</script>

</body>
</html>