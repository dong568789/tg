<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "用户预授权";
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
                <h2>用户预授权</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">
                            <img class="i-logo" src="__ROOT__/plus/img/demo/invoice-logo.png" alt="">
                        </div>
                        <div class="card-body ">
							<div class="p-20">
								<div class="row">
									<div class="col-xs-6">
										<div class="text-right">
											<h4><{$user['account']}></h4>
											<span class="text-muted f-14">
												<{$user['contactmobile']}>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['usertype'] eq 1">
													个人
												<else/>
													公司名称&nbsp;<{$user['companyname']}>
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['address'] neq '' ">
													<{$user['address']}>
												</if>
											</span>
										</div>
									</div>
									
									<div class="col-xs-6">
										<div class="i-to">
											<h4><{$user['realname']}></h4>
											<span class="text-muted f-14">
												<{$user['contactemail']}>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['invoicetype'] eq 1">
													开普通发票(税点 6.72%)
												<elseif condition = "$user['invoicetype'] eq 2" />
													开3%增值税发票(税点 3%)
												<elseif condition = "$user['invoicetype'] eq 3" />
													开6%增值税发票(税点 6%)
												<else />
													不开发票
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['address'] neq '' ">
													<{$user['postnumber']}>
												</if>
											</span>
										</div>
									</div>
								</div>
								
								<form class="form-horizontal" role="form" id="modifypreauth" method="post" action="index.php?m=user&a=modifyCoinPreAuth">
									<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<{$user['userid']}>">
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<label class="col-sm-12 f-15 text-center" id="preauthamountlabel">用户已有预授权：<a id="preauthamounta"><{$user['coinpreauth']}></a> 游侠币</label>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<div class="col-sm-6 m-t-5">
												<div class="fg-line">
													<select class="selectpicker" id="increase" name="increase">
														<option value="1">增加预授权</option>
														<option value="2">减少预授权</option>
													</select>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="fg-line">
													<input type="text" class="form-control" name="preauthamount" id="preauthamount" placeholder="数量" autocomplete="off">
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
											<button type="button" class="btn btn-primary btn-lg btn-block" id="dopreauth">进行预授权</button>
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
											<th data-column-id="id" data-type="numeric" data-order="desc" data-visible="false">充值编号</th>
                                            <th data-column-id="preauthuser">充值管理员ID</th>
                                            <th data-column-id="preauthusername">充值管理员名称</th>
                                            <th data-column-id="amount">充值数量</th>
                                            <th data-column-id="beizhu">备注</th>
                                            <th data-column-id="createtime" >时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="coinlog" item="vo" key="k">
											<tr>
												<td><{$vo['id']}></td>
												<td><{$vo['preauthuser']}></td>
												<td><{$vo['preauthusername']}></td>
												<td><{$vo['amount']}></td>
												<td><{$vo['beizhu']}></td>
												<td><{$vo['createtime']}></td>
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

	function numberCheck (id) {
		var thisvalue = $('#'+id).val();
		thisvalue = parseInt(thisvalue);
		if (thisvalue >= 0) {
			$('#'+id).css("background-color","lightgreen");
		} else {
			notify("游侠币数量请输入正整数", 'warning');
			$('#'+id).css("background-color","white");
			$('#'+id).val("");
		}
	}

	var isdoingpreauth = 0;

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
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div>"
            }
        });

		var $modifypreauth = $('#modifypreauth').validate({
			rules : {
				preauthamount : {
					required : true,
					digits : true,
					min : 0
				}
			},

			messages : {
				preauthamount : {
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

		$('#dopreauth').click(function() {
			if ($('#modifypreauth').valid()) {
				var amount = $('#preauthamount').val();
				if (parseInt(amount) > 100000) {
					var confirmcontent = "此次修改的用户游侠币预授权数量很大，确认进行预授权？";
				} else {
					var confirmcontent = "确认修改该用户的游侠币预授权数量？";
				}
				if(confirm(confirmcontent)) {
					if (isdoingpreauth == 0) {
						isdoingpreauth = 1;
						$('#modifypreauth').ajaxSubmit({  
							dataType : 'json',    
							success : function (data) {
								if (data.data == "success") {
									$('#preauthamounta').html(data.info);
									notify("对用户的预授权成功。", 'success');
									isdoingpreauth = 0;
								} else {
									notify(data.info, 'danger');
									isdoingpreauth = 0;
								}
								return false;
							},
							error : function (xhr) {
								notify('系统错误！', 'danger');
								isdoingpreauth = 0;
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