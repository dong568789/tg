<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "所有用户";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
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
                            <div id="data-table-basic-header" class="bootgrid-header container-fluid m-b-0">
                                <div class="actionBar">
                                    <!-- <select class="btn btn-default dropdown-menu f-14 m-r-10" id="subtype">
                                        <option value="mother">母账号</option>
                                        <option value="sub">子账号</option>
                                        <option value="all">全部</option>
                                    </select> -->

                                    <select class="btn btn-default dropdown-menu f-14 m-r-10" id="isverified">
                                        <option value="yes">认证通过</option>
                                        <option value="pending">未认证</option>
                                        <option value="no">不通过</option>
                                        <option value="all">全部</option>
                                    </select>

                                    <select class="btn btn-default dropdown-menu f-14 m-r-10" id="is_allow_cdn">
                                        <option value="all">全部(cdn)</option>
                                        <option value="yes">允许cdn</option>
                                        <option value="no">不允许cdn</option>
                                    </select>

                                    <div style="clear:both;"></div>
                                </div>

                                <div class="actionBar m-t-20">
                                    <div class="search form-group col-sm-9 m-0 p-l-0">
                                        <div class="input-group">
                                            <span class="zmdi icon input-group-addon glyphicon-search"></span>
                                            <input type="text" class="form-control search-content" id="account" placeholder="输入账号搜索">
                                        </div>
                                    </div>
                                    <div class="actions btn-group">
                                        <div class="dropdown btn-group">
                                            <a class="btn btn-default" href="javascript:void(0);" id="search-btn">搜索</a>
                                        </div>
                                    </div>

                                    <if condition="$newuser eq 'ok'"><a class="btn btn-primary btn-default waves-effect btn-addnewuser" href="/newuser/">新增一个用户</a></if>
                                </div>
                            </div>

                            <div class="p-20" style="padding-top: 0px;min-height: 100px;">
                                <div id="loading" class="col-sm-12 text-center" style="display: none;">
                                    <img src="__ROOT__/plus/img/progress.gif" alt=""/>
                                    <p class="m-t-10">正在加载数据，请稍后</p>
                                </div>

                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="id">序号</th>
                                            <th data-column-id="account">用户名</th>
                                            <th data-column-id="projectname">项目名</th>
                                            <th data-column-id="sourcetypestr">渠道类型</th>
                                            <th data-column-id="bindmobile" data-visible="false">绑定手机</th>
                                            <th data-column-id="bindemail" data-visible="false">绑定邮箱</th>
                                            <th data-column-id="usertypestr">会员类型</th>
                                            <!-- <th data-column-id="paccount">母账号</th> -->
                                            <th data-column-id="realname">联系人名字</th>
											<th data-column-id="contactmobile">联系电话</th>
                                            <th data-column-id="companyname">公司名字</th>
                                            <th data-column-id="channelbusiness">渠道商务</th>
                                            <th data-column-id="createtime">创建时间</th>
                                            <th data-column-id="isverified" data-visible="false">是否通过审核</th>
                                            <if condition="$editUser eq 'ok' || $ptbAuthorization eq 'ok' || $auditUser eq 'ok'"><th data-column-id="operationstr">操作</th></if>
                                        </tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
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

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>
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

    // 搜索
    function search_page () {
        // var subtype = $('#subtype').val();
        var isverified = $('#isverified').val().trim();
        var is_allow_cdn = $('#is_allow_cdn').val();
        var account = $('#account').val();

        $.ajax({
            type: "POST",
            url: "/index.php?m=user&a=search_user",
            data: {
                // subtype:subtype, 
                isverified:isverified,
                is_allow_cdn:is_allow_cdn,
                account:account,
            },
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $(".table-responsive").hide();
                $("#data-table-basic-footer").hide();
                $("#loading").show();
            },
            success: function (data) {
                // console.log(data);
                $("#loading").hide();
                $(".table-responsive").show();
                $("#data-table-basic-footer").show();
                $("#data-table-basic").bootgrid("clear");
                if (data.info == "success") {
                    $("#data-table-basic").bootgrid("append", data.data);
                } else {
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
            },
            templates: {
                header: ""
            }
        });

        search_page();

        // $('#subtype').change(function(){
        //     search_page();
        // })
        $('#isverified').change(function(){
            search_page();
        })
        $('#is_allow_cdn').change(function(){
            search_page();
        })
        $('#account').keydown(function(e){
            if(e.keyCode==13){
               search_page();
            }
        });
        $('#search-btn').click(function (argument) {
            search_page();
        })
    })
</script>

</body>
</html>