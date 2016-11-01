<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "发送信息";
	$page_css[] = "";
?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />


<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
		//后台页面用$page_nav
		$page_nav["用户管理"]["active"] = true;
		$page_nav["用户管理"]["sub"]["发送信息"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>发送信息</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body">
                            <div class="p-20">
								<form id="sendmail" class="form-horizontal" role="form" action="index.php?m=user&a=sendMailAction" method="post">
									<div class="form-group m-t-25">
										<label for="category" class="col-sm-3 control-label f-15 m-t-5">消息类型</label>
										<div class="col-sm-7"> 
											<div class="fg-float">
												<div class="fg-line">
													<select class="selectpicker bs-select-hidden" name="category" id="category">
														<option value="系统消息">系统消息</option>
														<option value="游戏消息">游戏消息</option>
														<option value="其他消息">其他消息</option>
													</select>
												</div>
											</div>
										</div>
									</div>
									<!--
									<div class="form-group m-t-25">
										<label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">消息状态</label>
										<div class="col-sm-7">
											<div class="fg-float">
												<div class="fg-line">
													<select class="selectpicker bs-select-hidden">
														<option>请选择</option>
														<option>已读</option>
														<option>未读</option>
													</select>
												</div>
											</div>
										</div>
									</div>
									-->
									<div class="form-group m-t-25">
										<label for="ts1" class="col-sm-3 control-label f-15">全员发送</label>
										<div class="col-sm-7 m-t-10">
											<div class="toggle-switch">
												<input id="sendtoall" name="sendtoall" type="checkbox" hidden="hidden" value="0">
												<label for="sendtoall" class="ts-helper"></label>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25" id="targetuserdiv">
										<label class="col-sm-3 control-label f-15">对象用户</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="targetuser" id="targetuser" placeholder="请输入用户名或手机号">
											</div>
										</div>
									</div>
									<!--
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">发送日期</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="email" class="form-control input-sm" id="inputEmail3" placeholder="请标明信息发送日期">
											</div>
										</div>
									</div>
									-->
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">消息标题</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="title" id="title" placeholder="如“2016年一月份只结算一部分的通知”">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">消息内容</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<textarea class="form-control" rows="3" name="content" id="textcontent" placeholder="简要描述消息的信息，不超过300字" maxlength="300" ></textarea>
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button id="sendmailsubmit" type="submit" class="btn btn-primary btn-lg m-r-15">发送信息</button>
											<a href="/userall/" class="btn btn-default btn-lg c-gray">取消</a>
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

    $(document).ready(function() {
		var $sendmail = $('#sendmail').validate({
			rules : {
				targetuser : {
					required : true
				},
				title : {
					required : true
				},
				content : {
					required : true
				}
			},

			messages : {
				targetuser : {
					required : '请输入对象用户，此项目必填'
				},
				title : {
					required : '请输入消息标题，此项目必填'
				},
				content : {
					required : '请输入消息内容，此项目必填'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#sendtoall').change(function() {
			if ($('#sendtoall').val() == "0") {
				$('#sendtoall').attr("value","1");
				$('#sendtoall').prop('checked', true);
				$('#targetuserdiv').addClass("display-none");
			} else if ($('#sendtoall').val() == "1") {
				$('#sendtoall').attr("value","0");
				$('#sendtoall').prop('checked', false);
				$('#targetuserdiv').removeClass("display-none");
			}
		});

		$('#sendmailsubmit').click(function() {
			if ($('#sendmail').valid()) {
				var confirmcontent = "确认发送消息？";
				if(confirm(confirmcontent)) {
					swal({
						title: "请稍侯...",   
						text: "消息正在发送中，请等待完成，不要关闭页面", 
						type: "hold",
						showConfirmButton: false
					});
					$('#sendmail').ajaxSubmit({  
						dataType : 'json',
						beforeSend : function() {		
							//do nothing
						},
						uploadProgress : function(event, position, total, percentComplete) {
							//do nothing
						},
						success : function (data) {
							if (data.data == "success") {
								swal({
									title: "已发送",   
									text: "消息发送成功", 
									type: "success",
									showConfirmButton: true
								});
								$(".btn-success").click(function(){
									$("#title").val("");
									$("#textcontent").val("");
								});
							} else {
								swal({
									title: "发送失败",   
									text: "消息发送失败，请检查项目是否填写正确。", 
									type: "error",
									showConfirmButton: true
								});
							}
							return false;
						},
						error : function (xhr) {
							swal({
								title: "系统错误",   
								text: "消息发送失败，请联系管理员。", 
								type: "error",
								showConfirmButton: true
							});
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