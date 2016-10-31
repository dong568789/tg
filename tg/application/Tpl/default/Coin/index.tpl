<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "游侠币管理";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["用户充值"]["active"] = true;
    $page_nav["用户充值"]["sub"]["游侠币管理"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>游侠币管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body ">
							<div class="p-20">
								<form class="form-horizontal" role="form" id="modifyrecharge" method="post" action="index.php?m=coin&a=modifyCoinRecharge">
									<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<{$user['userid']}>">
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<label class="col-sm-12 f-15 text-center" id="preauthamountlabel">您已有预授权：<a id="preauthamounta"><{$user['coinpreauth']}></a> 游侠币</label>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="username" id="username" placeholder="用户手机号/邮箱/用户名" autocomplete="off" maxlength="40">
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<div class="col-sm-6 m-t-5" style="padding-left:0;">
												<div class="fg-line">
													<select class="selectpicker" id="increase" name="increase">
														<option value="1">充值游侠币</option>
													</select>
												</div>
											</div>
											<div class="col-sm-6" style="padding-right:0;">
												<div class="fg-line">
													<input type="text" class="form-control" name="rechargeamount" id="rechargeamount" placeholder="数量" autocomplete="off" maxlength="10">
												</div>
											</div>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="information" id="information" placeholder="备注" autocomplete="off" maxlength="30">
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<button type="button" class="btn btn-primary btn-lg btn-block" id="dorecharge">为用户充值游侠币</button>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
								</form>
                            </div>
                            <div  class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-command" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="username">充值对象用户</th>
                                            <th data-column-id="mobile">手机号</th>
                                            <th data-column-id="email">邮箱</th>
                                            <th data-column-id="ptb">充值游侠币数量</th>
                                            <th data-column-id="beizhu">备注</th>
                                            <th data-column-id="create_time" data-formatter="create_time">充值时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="coinlog" item="vo" key="k">
											<tr>
												<td><{$vo['username']}></td>
												<td><{$vo['mobile']}></td>
												<td><{$vo['email']}></td>
												<td><{$vo['ptb']}></td>
												<td><{$vo['beizhu']}></td>
												<td><{$vo['create_time']}></td>
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

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
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

	var isdoingrecharge = 0;

    $(document).ready(function(){
        //Command Buttons
        $("#data-table-command").bootgrid({
            css: {
                icon: 'zmdi icon',
                iconColumns: 'zmdi-view-module',
                iconDown: 'zmdi-expand-more',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-expand-less'
            },
			formatters: {
				"create_time": function(column, row)
				{
					return new Date(parseInt(row.create_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " "); 
				}
			},
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div>"
            }
        });

		var $modifyrecharge = $('#modifyrecharge').validate({
			rules : {
				username : {
					required : true,
					minlength : 6,
					maxlength : 40
				},
				rechargeamount : {
					required : true,
					digits : true,
					min : 1
				}
			},

			messages : {
				username : {
					required : '对象用户名必填',
					minlength : '对象用户名为6-20位',
					maxlength : '对象用户名为6-20位'
				},
				rechargeamount : {
					required : '请输入预授权的游侠币数量',
					digits : '预授权的游侠币数量必须为正整数',
					min : '预授权的游侠币数量必须为正整数'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});
        $("#rechargeamount").blur(function(){
            var amount = $('#rechargeamount').val();
            if(parseInt(amount) > <{$user['coinpreauth']}>){
                alert("预授权的游侠币数量不得大于您游侠币总量");
            }
        });

		$('#dorecharge').click(function() {
			if ($('#modifyrecharge').valid()) {
				var amount = $('#rechargeamount').val();
				if (parseInt(amount) > 10000) {
					var confirmcontent = "此次充值的游侠币数额较大，确认进行充值？";
				} else {
					var confirmcontent = "确认为该用户充值游侠币？";
				}
				if(confirm(confirmcontent)) {
					if (isdoingrecharge == 0) {
						isdoingrecharge = 1;
						$('#modifyrecharge').ajaxSubmit({  
							dataType : 'json',    
							success : function (data) {
								if (data.data == "success") {
									$('#preauthamounta').html(data.info.newcoinpreauth);
									var  create_time=new Date(parseInt(data.info.create_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
									var tr_data='<tr><td>'+data.info.username+'</td><td>'+data.info.mobile+'</td><td>'+data.info.email+'</td><td>'+data.info.ptb+'</td><td>'+data.info.beizhu+'</td><td>'+create_time+'</td></tr>';
									$('#data-table-command tr:eq(0)').after(tr_data);

									notify("为用户充值游侠币成功。", 'success');
									isdoingrecharge = 0;
									$modifyrecharge.resetForm();
								} else {
									notify(data.info, 'danger');
									isdoingrecharge = 0;
								}
								return false;
							},
							error : function (xhr) {
								notify('系统错误！', 'danger');
								isdoingrecharge = 0;
								return false;
							}
						});
					}
					
				} else {
					return false;
				}
			}
			return false;
		});

    });
</script>
</body>
</html>