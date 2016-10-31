<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "修改资源的分成比例和渠道费";
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
                <h2>修改资源的分成比例和渠道费</h2>
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

											<h4><{$source['channelname']}></h4>
											<span class="text-muted f-14">
												该资源现分成比例：<a id="sourceshareratea"><{$source['sourcesharerate']}></a>
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

											<h4><{$source['gamename']}></h4>
											<span class="text-muted f-14">
												该资源现渠道费：<a id="sourcechannelratea"><{$source['sourcechannelrate']}></a>
											</span>
										</div>
									</div>
								</div>
								
								<form class="form-horizontal" role="form" id="modifyuserrate" method="post" action="index.php?m=user&a=modifyUserRate">
									<input type="hidden" name="hiddensourceid" id="hiddensourceid" value="<{$source['id']}>">
									<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<{$user['userid']}>">
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<div>
												<div class="col-sm-6" style="padding-left:0;">
													<div class="fg-line">
														<input type="text" class="form-control" name="sourcesharerate" id="sourcesharerate" placeholder="资源分成比例" autocomplete="off">
													</div>
												</div>
											</div>
											<div>
												<div class="col-sm-6" style="padding-right:0;">
													<div class="fg-line">
														<input type="text" class="form-control" name="sourcechannelrate" id="sourcechannelrate" placeholder="资源渠道费" autocomplete="off">
													</div>
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
											<button type="button" class="btn btn-primary btn-lg btn-block" id="modifyuserratesubmit">修改</button>
										</div>
										<div class="col-sm-3">
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

	var isdoinguserrate = 0;

    $(document).ready(function(){
		var $modifyuserrate = $('#modifyuserrate').validate({
			rules : {
				sourcesharerate : {
					number : true,
					min : 0,
					max : 1
				},
				sourcechannelrate : {
					number : true,
					min : 0,
					max : 1
				}
			},

			messages : {
				sourcesharerate : {
					number : '分成比例必须为大于0小于1的小数',
					min : '分成比例必须为大于0小于1的小数',
					max : '分成比例必须为大于0小于1的小数'
				},
				sourcechannelrate : {
					number : '渠道费必须为大于0小于1的小数',
					min : '渠道费必须为大于0小于1的小数',
					max : '渠道费必须为大于0小于1的小数'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#modifyuserratesubmit').click(function() {
			if ($('#modifyuserrate').valid()) {
				var confirmcontent = "确认修改该资源的分成比例和渠道费率？";
				if(confirm(confirmcontent)) {
					if (isdoinguserrate == 0) {
						isdoinguserrate = 1;
						$('#modifyuserrate').ajaxSubmit({  
							dataType : 'json',    
							success : function (data) {
								if (data.data == "success") {
									notify("自定义资源费率成功，2秒后跳转。", 'success');
									if ($('#sourcesharerate').val() != "") {
										$('#sourceshareratea').html($('#sourcesharerate').val());
									}
									if ($('#sourcechannelrate').val() != "") {
										$('#sourcechannelratea').html($('#sourcechannelrate').val());
									}
									isdoinguserrate = 0;
									var userid = $('#hiddenuserid').val();
									setTimeout(function () {
										self.location.href = " /usersource/"+userid+"/ ";
									}, 2000);
								} else {
									notify(data.info, 'danger');
									isdoinguserrate = 0;
								}
								return false;
							},
							error : function (xhr) {
								notify('系统错误！', 'danger');
								isdoinguserrate = 0;
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