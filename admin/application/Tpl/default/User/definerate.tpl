<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "修改子账号的分成比例和渠道费";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["推广资源"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>修改子账号的分成比例和渠道费</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-body ">
							<div class="p-20">
								<div class="row">
									<h4 class="text-center">子账号：<{$source['account']}>  </h4>
									<div class="col-xs-6">
										<div class="text-right">
											<h4>渠道：<{$source['channelname']}></h4>
											<h4 class="text-muted f-14">
												该资源现分成比例：<a id=""><{$source['sourcesharerate']}></a>
											</h4>
											<h4 class="text-muted f-14">
												该子账号的资源现分成比例：<a id=""><{$source['sub_share_rate']}></a>
											</h4>
										</div>
									</div>

									<div class="col-xs-6">
										<div class="text-left">
											<h4>游戏：<{$source['gamename']}></h4>
											<h4 class="text-muted f-14">
												该资源现渠道费：<a id=""><{$source['sourcechannelrate']}></a>
											</h4>
											<h4 class="text-muted f-14">
												该子账号的资源现渠道费：<a id=""><{$source['sub_channel_rate']}></a>
											</h4>
										</div>
									</div>
								</div>
								
								<form class="form-horizontal" role="form" id="modifyuserrate" method="post" action="<{:U('user/defineRateHandle')}>">
									<input type="hidden" name="sourceid" id="sourceid" value="<{$source['id']}>">
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<label class="col-sm-3 control-label f-15">资源分成比例：</label>
											<div class="col-sm-9">
												<div class="fg-line">
													<input type="text" class="form-control" name="sub_share_rate" id="sub_share_rate" placeholder="以1为100%，比如分成比例40%，此处填写0.4" autocomplete="off" value="<{$source['sub_share_rate']}>" >
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
											<label class="col-sm-3 control-label f-15">
												资源渠道费：
											</label>
											<div class="col-sm-9">
												<div class="fg-line">
													<input type="text" class="form-control" name="sub_channel_rate" id="sub_channel_rate" placeholder="以1为100%，比如通道费6%，此处填写0.06" autocomplete="off" value="<{$source['sub_channel_rate']}>">
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
				sub_share_rate : {
					required : true,
					number : true,
					min : 0,
					max : 1
				},
				sub_channel_rate : {
					required : true,
					number : true,
					min : 0,
					max : 1
				}
			},

			messages : {
				sub_share_rate : {
					required : '分成比例不能为空',
					number : '分成比例必须为大于等于0小于1的小数',
					min : '分成比例必须为大于等于0小于1的小数',
					max : '分成比例必须为大于等于0小于1的小数'
				},
				sub_channel_rate : {
					required : '渠道费不能为空',
					number : '渠道费必须为大于等于0小于1的小数',
					min : '渠道费必须为大于等于0小于1的小数',
					max : '渠道费必须为大于等于0小于1的小数'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#modifyuserratesubmit').click(function() {
			if ($('#modifyuserrate').valid()) {
				var confirmcontent = "确认修改该子账号的资源的分成比例和渠道费率？";
				if(confirm(confirmcontent)) {
					if (isdoinguserrate == 0) {
						isdoinguserrate = 1;
						$('#modifyuserrate').ajaxSubmit({  
							dataType : 'json',    
							success : function (data) {
								if (data.data == "success") {
									notify("自定义资源费率成功，2秒后跳转。", 'success');
									var sourceid = $('#sourceid').val();
									setTimeout(function () {
										self.location.href = " /definerate/"+sourceid+"/ ";
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