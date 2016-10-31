<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "注册";
	$page_css[] = "vendors/bower_components/animate.css/animate.min.css";
    $page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
?>
<include file="Inc:head" />
<body class="login-content sw-toggled">
<include file="Inc:original-header" />
<!-- Register -->
<div class="lc-block toggled tab-pane animated fadeOut active" id="l-register">
    <ul class="tab-nav tn-justified tn-icon" role="tablist">
        <li role="presentation" class="active">
            <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-smartphone-iphone icon-tab m-r-5"></i>
                手机注册
            </a>
        </li>
        <li role="presentation">
            <a class="col-xs-6 f-15" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-account icon-tab m-r-5"></i>
                用户名注册
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane animated fadeIn in active m-r-25" id="tab-1">
            <form id="mobileregister" class="form-horizontal" role="form" action="index.php?m=user&a=mobileregister" method="post">
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-smartphone"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="mobile" id="mobile" placeholder="请输入手机号" maxlength="11" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="password" class="form-control" name="password" id="password" placeholder="请输入密码" maxlength="20" autocomplete="off">
                        </div>
                    </div>
                </div>

				<div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="mobileverify" id="mobileverify" placeholder="请输入右图四位数字" maxlength="4">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-default bt-send-code" id='mobileVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/mobileImageVerify')}>' id='mobileVerifyImg' /></a>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="verifymsg" id="verifymsg" placeholder="请输入六位短信验证码" maxlength="6">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-default bt-send-code" id="sendmsg">发送验证码</button>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="mobileprofile" id="mobileprofile" value="1">
                        <i class="input-helper"></i>
                        我已阅读并接受<a href="__ROOT__/plus/profile/游侠游戏用户服务协议.pdf">《游侠游戏用户服务协议》</a>
                    </label>
                </div>

                <a class="btn btn-login btn-warning btn-float" id="mobileregisterforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>

        <div role="tabpanel" class="tab-pane animated fadeIn in m-r-25" id="tab-2">
            <form id="accountregister" class="form-horizontal" role="form" action="index.php?m=user&a=accountregister" method="post">
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-email"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="account" id="account" placeholder="请输入用户名">
                        </div>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="password" class="form-control" name="password" id="password" placeholder="请输入密码" maxlength="20">
                        </div>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="accountverify" id="accountverify" placeholder="请输入右图四位数字" maxlength="4">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-default bt-send-code" id='accountVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/accountImageVerify')}>' id='accountVerifyImg' /></a>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="accountprofile" id="accountprofile" value="1">
                        <i class="input-helper"></i>
                        我已阅读并接受<a href="__ROOT__/plus/profile/游侠游戏用户服务协议.pdf">《游侠游戏用户服务协议》</a>
                    </label>
                </div>

                <a class="btn btn-login btn-warning btn-float" id="accountregisterforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>
    </div>
</div>


<div class="lc-block toggled tab-pane animated fadeOut active register" id="info">
    <ul class="tab-nav tn-justified tn-icon" role="tablist">
        <li role="presentation" class="active">
            <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-edit icon-tab m-r-5"></i>
                请完善个人资料信息
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane animated fadeIn in active m-r-25" id="tab-1">
            <form id="inforegister" class="form-horizontal" role="form" action="index.php?m=user&a=inforegister" method="post">
				<div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-accounts-alt"></i></span>
                    <div class="col-sm-12">
						<div class="fg-line text-left m-t-5">
							<label class="radio radio-inline m-r-20">
								<input type="radio" name="usertype" value="1" checked="true">
								<i class="input-helper p-relative" style="left:-26px;"></i>
								个人
							</label>

							<label class="radio radio-inline m-r-20">
								<input type="radio" name="usertype" value="2">
								<i class="input-helper p-relative" style="left:-26px;"></i>
								公司
							</label>
						</div>
                    </div>
                </div>
				<div class="input-group m-b-20" id="companynamediv">
                    <span class="input-group-addon"><i class="zmdi zmdi-city-alt"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="companyname" name="companyname" placeholder="请输入公司名字">
                        </div>
                    </div>
                </div>

				<div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-mood"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="realname" name="realname" placeholder="请输入联系人姓名">
							<input type="hidden" id="hiddeninfouserid" name="hiddeninfouserid" value="">
							<input type="hidden" id="hiddenregistertype" name="hiddenregistertype" value="">
							<input type="hidden" id="hiddenisfromverify" name="hiddenisfromverify" value="<{$isfromverify}>">
							<input type="hidden" id="hiddenverifyuserid" name="hiddenverifyuserid" value="<{$verifyuserid}>">
							<input type="hidden" id="hiddenregistermethod" name="hiddenregistermethod" value="<{$registermethod}>">
                        </div>
                    </div>
                </div>
				<div class="input-group m-b-20" id="genderdiv">
                    <span class="input-group-addon"><i class="zmdi zmdi-male-female"></i></span>
                    <div class="col-sm-12">
						<div class="fg-line text-left m-t-5">
							<label class="radio radio-inline m-r-20">
								<input type="radio" name="gender" value="1" checked="true">
								<i class="input-helper p-relative" style="left:-26px;"></i>
								男
							</label>

							<label class="radio radio-inline m-r-20 m-l-15">
								<input type="radio" name="gender" value="2">
								<i class="input-helper p-relative" style="left:-26px;"></i>
								女
							</label>
						</div>
                    </div>
                </div>
                <div class="input-group m-b-20" id="contactmobilediv">
                    <span class="input-group-addon"><i class="zmdi zmdi-smartphone-iphone"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="contactmobile" name="contactmobile" placeholder="请输入联系手机号">
                        </div>
                    </div>
                </div>
				<div class="input-group m-b-20" id="contactmobilediv">
                    <span class="input-group-addon"><i class="zmdi zmdi-email"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="contactemail" name="contactemail" placeholder="请输入联系邮箱">
                        </div>
                    </div>
                </div>
				<div class="input-group m-b-20" id="userinvoicetypediv">
                    <span class="input-group-addon"><i class="zmdi zmdi-tag-more"></i></span>
                    <div class="col-sm-12">
						<select class="selectpicker" id="userinvoicetype" name="userinvoicetype">
							<option value="0">不开发票</option>
						</select>
					</div>
                </div>
				<div class="input-group m-b-20" id="companyinvoicetypediv">
                    <span class="input-group-addon"><i class="zmdi zmdi-tag-more"></i></span>
                    <div class="col-sm-12">
						<select class="selectpicker" id="companyinvoicetype" name="companyinvoicetype">
							<option value="1">普通发票</option>
							<option value="2">3%增值税发票</option>
							<option value="3">6%增值税发票</option>
						</select>
					</div>
                </div>
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-home"></i></span>

                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="address" name="address" placeholder="请输入联系地址(选填)">
                        </div>
                    </div>
                </div>
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-collection-item-8"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" id="postnumber" name="postnumber" placeholder="请输入邮编(选填)">
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <a class="btn btn-login btn-warning btn-float" id="infoforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>
    </div>
</div>

<div class="lc-block toggled tab-pane animated fadeOut active register" id="channel">
    <ul class="tab-nav tn-justified tn-icon" role="tablist">
        <li role="presentation" class="active">
            <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-view-compact icon-tab m-r-5"></i>
                最后一步 - 请创建一个渠道
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane animated fadeIn in active m-r-25" id="tab-1">
            <form id="channelregister" class="form-horizontal" role="form" action="index.php?m=user&a=channelregister" method="post">
                <div class="form-group m-b-20">
                    <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">渠道名称</label>
                    <div class="col-sm-9">
						<div class="fg-line">
							<input type="text" class="form-control" id="channelname" name="channelname" placeholder="请输入渠道名称" maxlength="10">
							<input type="hidden" id="hiddenchanneluserid" name="hiddenchanneluserid" value="">
						</div>
                    </div>
                </div>
                <div class="form-group m-b-20">
                    <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">渠道类型</label>
                    <div class="col-sm-9">
						<div class="fg-line">
							<select class="selectpicker bs-select-hidden" id="channeltype" name="channeltype">
								<option>应用</option>
								<option>公会</option>
								<option>网站</option>
								<option>个人</option>
								<option>其他</option>
							</select>
						</div>
                    </div>
                </div>
                <div class="form-group m-b-20">
                    <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">日均流量</label>
                    <div class="col-sm-9">
						<div class="fg-line">
							<select class="selectpicker bs-select-hidden" id="channelsize" name="channelsize">
								<option>10000以下</option>
								<option>10000 - 20000 PV</option>
								<option>20000 - 50000 PV</option>
								<option>50000以上</option>
							</select>
						</div>
                    </div>
                </div>
                <div class="form-group m-b-20">
                    <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">渠道介绍</label>
                    <div class="col-sm-9">
						<div class="fg-line">
							<textarea class="form-control" rows="3" id="description" name="description" placeholder="简要描述网站或者应用的概况，不超过300字" maxlength="300"></textarea>
						</div>
                        
                    </div>
                </div>
                <div class="clearfix"></div>
                <a class="btn btn-login btn-success btn-float" id="channelforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>

    </div>
</div>


<include file="Inc:original-scripts" />
<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>

<script type="text/javascript">
	var msgtime = "";

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

    $(document).ready(function() {
		var isfromverify = $("#hiddenisfromverify").val();
		if (parseInt(isfromverify) == 1) {
			$("#l-register").hide();
			$("#channel").hide();
			var verifyuserid = $("#hiddenverifyuserid").val();
			var registermethod = $("#hiddenregistermethod").val();
			if (parseInt(registermethod) == 1) {
				$("#companynamediv").hide();
				$("#companyinvoicetypediv").hide();
				$("#contactmobilediv").hide();
				$("#hiddeninfouserid").val(verifyuserid);
				$("#hiddenchanneluserid").val(verifyuserid);
				$("#hiddenregistertype").val("mobile");
			} else {
				$("#companynamediv").hide();
				$("#companyinvoicetypediv").hide();
				$("#hiddeninfouserid").val(verifyuserid);
				$("#hiddenchanneluserid").val(verifyuserid);
				$("#hiddenregistertype").val("account");
			}
		} else {
			$("#info").hide();
			$("#channel").hide();
		}
		

		jQuery.validator.addMethod("checkENGsmall", function(value, element) {
			var reg = /^[a-z](\s*[a-z])*$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母！");

		jQuery.validator.addMethod("checkENGsmallNUM", function(value, element) {
			var reg =  /^[0-9a-z]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母或数字！");

		jQuery.validator.addMethod("checkCHN", function(value, element) {
			var reg =  /^[\u4e00-\u9fa5]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入正确的汉字！");

        jQuery.validator.addMethod("mobileNUMBER", function(value, element) {
            var reg =  /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0-9]|170)\d{8}$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的手机号码！");

        //渠道规则
        /*jQuery.validator.addMethod("checkCHANNELNAME", function(value, element) {
            var reg =  /^[0-9a-zA-Z\u4e00-\u9fa5]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入英文小写字母或数字或汉字！");*/


        $("#mobileVerifyImgButton").click(function(){
			var Verify_Url = '<{:U('User/mobileImageVerify')}>';
			Verify_Url=Verify_Url.replace('.html','');
			$("#mobileVerifyImg").attr("src", Verify_Url+'/'+Math.random()); 
        });

		$("#accountVerifyImgButton").click(function(){
			var Verify_Url = '<{:U('User/accountImageVerify')}>';
			Verify_Url=Verify_Url.replace('.html','');
			$("#accountVerifyImg").attr("src", Verify_Url+'/'+Math.random()); 
        });

		var $mobileregister = $('#mobileregister').validate({
			rules : {
				mobile : {
					required : true,
					digits : true,
					minlength : 11,
					maxlength : 11,
                    mobileNUMBER:true
				},
				password : {
					required : true,
					minlength : 6,
					maxlength : 20
				},
				mobileverify : {
					required : true,
					digits : true,
					minlength : 4,
					maxlength : 4
				},
				verifymsg : {
					digits : true,
					minlength : 6,
					maxlength : 6
				},
				mobileprofile : {
					required : true
				}
			},

			messages : {
				mobile : {
					required : '请输入手机号',
					digits : '手机号必须为11位数字',
					minlength : '手机号必须为11位数字',
					maxlength : '手机号必须为11位数字',
                    mobileNUMBER : '手机号码不符合规范'
				},
				password : {
					required : '此项目必填',
					minlength : '密码长度为6-20位',
					maxlength : '密码长度为6-20位'
				},
				mobileverify : {
					required : '请输入图形验证码',
					digits : '图形验证码必须为4位数字',
					minlength : '图形验证码必须为4位数字',
					maxlength : '图形验证码必须为4位数字'
				},
				verifymsg : {
					digits : '短信验证码必须为6位数字',
					minlength : '短信验证码必须为6位数字',
					maxlength : '短信验证码必须为6位数字'
				},
				mobileprofile : {
					required : '请先同意服务协议'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#mobile').blur(function() {
			if ($mobileregister.valid()) {
				var mobilevalue = $('#mobile').val();
				if (mobilevalue != "") {
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=checkMobileExist", 
						data : {mobile : mobilevalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
                            console.log(data);
							if (data.data == "exist") {
								$('#mobile').val("");
								notify(data.info, 'danger');
							}
							return false;
						},
						error : function (xhr) {
							$('#mobile').val("");
							notify('系统错误，请输入一个新的账号。', 'danger');
							return false;
						}
					});
				}
			}
			return false;
		});

		$('#sendmsg').click(function() {
			if ($mobileregister.valid()) {
				var mobilevalue = $('#mobile').val();
				var verifyvalue = $('#mobileverify').val();
				if (mobilevalue != "" && verifyvalue != "") {
					$('#sendmsg').attr("disabled", "disabled");
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=sendMsg", 
						data : {mobile : mobilevalue, verify : verifyvalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
							if (data.data == "success") {
								$('#sendmsg').html("已发送(120)");
								var leftTime = 120;
								msgtime = setInterval( function () {
									leftTime = leftTime - 1;
									if (leftTime > 0) {
										$('#sendmsg').html("已发送("+leftTime.toString()+")");
									} else {
										clearInterval(msgtime);
										$('#sendmsg').removeAttr("disabled");
										$('#sendmsg').html("重新发送");
									}
								}, 1000);
							} else {
								notify(data.info, 'danger');
								$('#sendmsg').removeAttr("disabled");
							}
							return false;
						},
						error : function (xhr) {
							notify('系统错误，请联系管理员。', 'danger');
							$('#sendmsg').removeAttr("disabled");
							return false;
						}
					});
				} else {
					notify('请输入手机号和图形验证码。', 'danger');
				}
			}
			return false;
		});

		$('#mobileregisterforward').click(function() {
			if ($('#mobileregister').valid()) {
				$('#mobileregister').ajaxSubmit({  
					dataType : 'json',    
					success : function (data) {
						if (data.data == "success") {
							$("#l-register").hide();
							$("#info").show();
							$("#companynamediv").hide();
							$("#companyinvoicetypediv").hide();
							$("#contactmobilediv").hide();
							$("#hiddeninfouserid").val(data.info);
							$("#hiddenchanneluserid").val(data.info);
							$("#hiddenregistertype").val("mobile");
							notify('注册成功，请进一步完善资料。', 'success');
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
			}
		});

		var $accountregister = $('#accountregister').validate({
			rules : {
				account : {
					required : true,
					checkENGsmallNUM : true,
					minlength : 6,
					maxlength : 20
				},
				password : {
					required : true,
					minlength : 6,
					maxlength : 20
				},
				accountverify : {
					required : true,
					digits : true,
					minlength : 4,
					maxlength : 4
				},
				accountprofile : {
					required : true
				}
			},

			messages : {
				account : {
					required : '请输入用户名',
					checkENGsmallNUM : '用户名必须为小写字母或数字',
					minlength : '用户名长度为6-20位',
					maxlength : '用户名长度为6-20位'
				},
				password : {
					required : '此项目必填',
					minlength : '密码长度为6-20位',
					maxlength : '密码长度为6-20位'
				},
				accountverify : {
					required : '请输入图形验证码',
					digits : '图形验证码必须为4位数字',
					minlength : '图形验证码必须为4位数字',
					maxlength : '图形验证码必须为4位数字'
				},
				accountprofile : {
					required : '请先同意服务协议'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#account').blur(function() {
			if ($accountregister.valid()) {
				var accountvalue = $('#account').val();
				if (accountvalue != "") {
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=checkAccountExist", 
						data : {account : accountvalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
							if (data.data == "exist") {
								$('#account').val("");
								notify(data.info, 'danger');
							}
							return false;
						},
						error : function (xhr) {
							$('#account').val("");
							alert("系统错误，请输入一个新的账号。");
							return false;
						}
					});
				}
			}
			return false;
		});

		$('#accountregisterforward').click(function() {
			if ($('#accountregister').valid()) {
				$('#accountregister').ajaxSubmit({ 
					dataType : 'json',    
					success : function (data) {
						if (data.data == "success") {
							$("#l-register").hide();
							$("#info").show();
							$("#companynamediv").hide();
							$("#companyinvoicetypediv").hide();
							$("#hiddeninfouserid").val(data.info);
							$("#hiddenchanneluserid").val(data.info);
							$("#hiddenregistertype").val("account");
							notify('注册成功，请进一步完善资料。', 'success');
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
			}
		});

		$("input[name='usertype']").change(function() {
			var type = $("input[name='usertype']:checked").val();
			if (type == 1) {
				$("#genderdiv").show();
				$("#companynamediv").hide();
				$("#userinvoicetypediv").show();
				$("#companyinvoicetypediv").hide();
			} else {
				$("#genderdiv").hide();
				$("#companynamediv").show();
				$("#userinvoicetypediv").hide();
				$("#companyinvoicetypediv").show();
			}
		});

		var $inforegister = $('#inforegister').validate({
			rules : {
				companyname : {
					required : true
				},
				realname : {
					required : true,
					checkCHN : true
				},
				contactmobile : {
					required : true,
					digits : true,
					minlength : 11,
					maxlength : 11,
                    mobileNUMBER : true
				},
				contactemail : {
					required : true,
					email : true
				}
			},

			messages : {
				companyname : {
					required : '请输入公司名称'
				},
				realname : {
					required : '请输入联系人姓名',
					checkCHN : '联系人姓名必须为汉字'
				},
				contactmobile : {
					required : '请输入联系手机号',
					digits : '手机号必须为11位数字',
					minlength : '手机号必须为11位数字',
					maxlength : '手机号必须为11位数字',
                    mobileNUMBER: '手机号码不符合规范'
				},
				contactemail : {
					required : '请输入联系邮箱',
					email : '请输入正确的邮箱'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#infoforward').click(function() {
			if ($('#inforegister').valid()) {
				$('#inforegister').ajaxSubmit({  
					dataType : 'json',    
					success : function (data) {
						if (data.data == "success") {
							$("#info").hide();
							$("#channel").show();
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
			}
		});
		
		var $channelregister = $('#channelregister').validate({
			rules : {
				channelname : {
					required : true
				}
			},

			messages : {
				channelname : {
					required : '请输入渠道名称'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

        $("#channelforward").click(function(){
            if ($('#channelregister').valid()) {
				$('#channelregister').ajaxSubmit({  
					dataType : 'json',    
					success : function (data) {
						if (data.data == "success") {
							self.location.href = '/source/';
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
			}

        });
    })
</script>
</body>
</html>