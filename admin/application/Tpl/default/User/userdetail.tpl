<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台用户详情";
$page_css[] = "vendors/bower_components/chosen/chosen.min.css";
$page_css[] = "vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css";

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
                <h2>用户详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body">
                            <div class="p-20">
								<form id="edituser" class="form-horizontal" role="form" method="post" action="index.php?m=user&a=edituser">
									<input type="hidden" id="isverified" name="isverified" value="">
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">用户名</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="account" name="account" placeholder="请输入6-16位小写英文数字组合的用户名" value="<{$user['account']}>">
												<input type="hidden" id="userid" name="userid" value="<{$user['userid']}>">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25" id="sourcetype" >
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">渠道类型</label>
										<div class="col-sm-7">
											<select class="selectpicker" id="sourcetype" name="sourcetype">
												<option value="1" <if condition="$user['sourcetype'] eq '1'"> selected </if>>公会</option>
												<option value="2" <if condition="$user['sourcetype'] eq '2'"> selected </if>>买量</option>
												<option value="3" <if condition="$user['sourcetype'] eq '3'"> selected </if>>平台YXGAMES</option>
												<option value="4" <if condition="$user['sourcetype'] eq '4'"> selected </if>>CPS</option>
												<option value="5" <if condition="$user['sourcetype'] eq '5'"> selected </if>>应用商店</option>
												<option value="0" <if condition="$user['sourcetype'] eq '0'"> selected </if>>其它</option>
											</select>
										</div>
									</div>

									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">绑定手机号</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="bindmobile" name="bindmobile" placeholder="请输入绑定手机号" maxlength="11" value="<{$user['bindmobile']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">绑定邮箱账号</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="email" class="form-control" id="bindemail" name="bindemail" placeholder="请输入绑定邮箱" value="<{$user['bindemail']}>" autocomplete="off"/>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">用户密码</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="password" name="password" placeholder="请输入用户的登陆密码，长度为6-20位" maxlength="20" autocomplete="off"/>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">用户身份</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<label class="radio radio-inline m-r-20">
													<input class="radioclass" type="radio" name="usertype" value="1" <if condition="1 eq $user['usertype'] ">checked="true"</if>>
													<i class="input-helper p-relative" style="left:-26px;"></i>
													个人
												</label>

												<label class="radio radio-inline m-r-20">
													<input class="radioclass" type="radio" name="usertype" value="2" <if condition="2 eq $user['usertype'] ">checked="true"</if>>
													<i class="input-helper p-relative" style="left:-26px;"></i>
													公司
												</label>
											</div>
										</div>
									</div>
	
									<div class="form-group m-t-25" id="userinvoicetypediv" <if condition="2 eq $user['usertype'] ">style="display:none;"</if>>
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">发票类型</label>
										<div class="col-sm-7">
											<select class="selectpicker" id="userinvoicetype" name="userinvoicetype">
												<option value="0" <if condition="$user['invoicetype'] eq '0'">selected </if> >不开发票</option>
											</select>
										</div>
									</div>

									<div class="form-group m-t-25" id="companyinvoicetypediv" <if condition="1 eq $user['usertype'] ">style="display:none;"</if>>
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">发票类型</label>
										<div class="col-sm-7">
											<select class="selectpicker" id="companyinvoicetype" name="companyinvoicetype">
												<option value="1" <if condition="$user['invoicetype'] eq '1'"> selected </if>>普通发票</option>
												<option value="2" <if condition="$user['invoicetype'] eq '2'"> selected </if>>3%增值税发票</option>
												<option value="3" <if condition="$user['invoicetype'] eq '3'"> selected </if>>6%增值税发票</option>
											</select>
										</div>
									</div>
					
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">联系人姓名</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="realname" name="realname" placeholder="请输入联系人姓名" value="<{$user['realname']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">公司名字</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="companyname" name="companyname" placeholder="请输入公司名字(用户类型为个人时选填)" value="<{$user['companyname']}>">
											</div>
										</div>
									</div>

                                    <div class="form-group m-t-25" id="genderdiv">
                                        <label class="col-sm-3 f-15 control-label">性别</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gender" value="1" <if condition="1 eq $user['gender'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    男
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gender" value="2" <if condition="2 eq $user['gender'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    女
                                                </label>
                                            </div>
                                        </div>
                                    </div>

									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">联系手机号</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="contactmobile" name="contactmobile" placeholder="请输入联系手机号" maxlength="11" value="<{$user['contactmobile']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">联系邮箱</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="email" class="form-control" id="contactemail" name="contactemail" placeholder="请输入联系邮箱" value="<{$user['contactemail']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">提现最小金额</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="withdrawlimit" name="withdrawlimit" placeholder="请输入用户允许提现最小金额" value="<{$user['withdrawlimit']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">联系地址</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="address" name="address" placeholder="请输入联系地址(选填)" value="<{$user['address']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">邮编</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="postnumber" name="postnumber" placeholder="请输入邮编(选填)" value="<{$user['postnumber']}>">
											</div>
										</div>
									</div>

                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 control-label f-15">渠道商务</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <select class="chosen" name="channelbusiness" id="channelbusiness" data-placeholder="请选择对应的商务人员">
                                                    <option value="" selected="true">请选择对应的商务人员</option>
                                                    <foreach name="userlist" item="vo" key="k">
                                                        <option value="<{$vo['beizhu']}>" <if condition="$user['channelbusiness'] eq $vo['beizhu']"> selected </if>><{$vo['mobile']}> <{$vo['beizhu']}></option>
                                                    </foreach>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 control-label f-15">合作会员的自定义网站名称</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" id="diy_webname" name="diy_webname" placeholder="请自定义网站名称" value="<{$user['diy_webname']}>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 f-15 control-label">合作会员的自定义logo(200*50)</label>
                                        <div class="col-sm-9 p-t-5">
											<div class="fileinput fileinput-new">
												<span><if condition="$user['diy_logo']"><a class="f-15" href="<{$diylogoourl}><{$user['diy_logo']}>"><{$user['diy_logo']}></a><else />未上传logo</if></span>
											</div>
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput">
                                                    	<if condition="$user['diy_logo']">
                                                    		<img src="<{$diylogoourl}><{$user['diy_logo']}>" />
                                                    	</if>
                                                    </div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="diy_logo" id="diy_logo" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 control-label f-15">合作会员的自定义是否显示首页头</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="diy_isshow_homeheader" value="1" <if condition="1 eq $user['diy_isshow_homeheader'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    是
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="diy_isshow_homeheader" value="-1" <if condition="-1 eq $user['diy_isshow_homeheader'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    否
                                                </label>
                                            </div>
                                        </div>
                                    </div>

									<if condition = "$user['isverified'] eq 1">
										<div class="form-group m-t-25">
											<div class="col-sm-12 text-center">
												<button id="editusersubmit" type="submit" class="btn btn-primary btn-lg m-r-15">保存</button>
												<a href="javascript:window.history.back(-1)" class="btn btn-default btn-lg c-gray">取消</a>
											</div>
										</div>
									<else/>
										<div class="form-group m-t-25">
											<div class="col-sm-12 text-center">
												<button id="pass" type="button" class="btn btn-success btn-lg m-r-15">通过审核</button>
												<button id="refuse" type="button" class="btn btn-danger btn-lg m-r-15">拒绝用户</button>
											</div>
										</div>
										<div class="form-group m-t-25">
											<label class="col-sm-3 control-label f-15">拒绝理由</label>
											<div class="col-sm-7">
												<label class="checkbox checkbox-inline m-r-20">
													<input name="refusereason[]" type="checkbox" value="1">
													<i class="input-helper p-relative" style="left:-26px;"></i>    
													手机号或账号有误
												</label>
												<label class="checkbox checkbox-inline m-r-20">
													<input name="refusereason[]" type="checkbox" value="2">
													<i class="input-helper p-relative" style="left:-26px;"></i>    
													真实姓名有误
												</label>
												<label class="checkbox checkbox-inline m-r-20">
													<input name="refusereason[]" type="checkbox" value="3">
													<i class="input-helper p-relative" style="left:-26px;"></i>    
													公司名称有误
												</label>
												<label class="checkbox checkbox-inline m-r-20">
													<input name="refusereason[]" type="checkbox" value="4">
													<i class="input-helper p-relative" style="left:-26px;"></i>    
													联系地址有误
												</label>
											</div>
										</div>
									</if>
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
<script src="__ROOT__/plus/vendors/bower_components/chosen/chosen.jquery.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="__ROOT__/plus/vendors/fileinput/fileinput.min.js"></script>

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

    $(document).ready(function() {
		jQuery.validator.addMethod("checkENGsmall", function(value, element) {
			var reg = /^[a-z](\s*[a-z])*$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母！");

		jQuery.validator.addMethod("checkENGsmallNUM", function(value, element) {
			var reg =  /^[0-9a-z\_]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母或数字！");

		jQuery.validator.addMethod("checkCHN", function(value, element) {
			var reg =  /^[\u4e00-\u9fa5]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入正确的汉字！");
		
		var $edituser = $('#edituser').validate({
			rules : {
				account : {
					required : true,
					checkENGsmallNUM : true,
					minlength : 6,
					maxlength : 16
				},
				bindmobile : {
					digits : true,
					minlength : 11,
					maxlength : 11
				},
				bindemail : {
					email : true
				},
				password : {
					minlength : 6,
					maxlength : 20
				},
				realname : {
					checkCHN : true
				},
				contactmobile : {
					digits : true,
					minlength : 11,
					maxlength : 11
				},
				withdrawlimit : {
					digits : true,
					min : 0
				}
			},

			messages : {
				account : {
					required : '请输入用户名，此项目必填',
					checkENGsmallNUM : '用户名只能为英文小写字母或数字',
					minlength : '用户名长度为6-16位',
					maxlength : '用户名长度为6-16位'
				},
				bindmobile : {
					digits : '手机号只能为数字',
					minlength : '请输入正确的11位手机号',
					maxlength : '请输入正确的11位手机号'
				},
				bindemail : {
					email : '请输入正确的邮箱'
				},
				password : {
					minlength : '密码长度为6-20位',
					maxlength : '密码长度为6-20位'
				},
				realname : {
					checkCHN : '联系人姓名必须为汉字'
				},
				contactmobile : {
					digits : '手机号只能为数字',
					minlength : '请输入正确的11位手机号',
					maxlength : '请输入正确的11位手机号'
				},
				withdrawlimit : {
					digits : '用户提现金额限制必须为数字',
					min : '用户提现金额限制必须大于等于0',
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#account').blur(function() {
			if ($edituser.valid()) {
				var accountvalue = $('#account').val();
				if (accountvalue != "") {
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=checkAccountExist", 
						data : {account : accountvalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
							if (data.data.isexist == "exist") {
								$('#account').val("");
								alert(data.info);
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

		$('#bindmobile').blur(function() {
			if ($edituser.valid()) {
				var mobilevalue = $('#bindmobile').val();
				if (mobilevalue != "") {
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=checkMobileExist", 
						data : {mobile : mobilevalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
							if (data.data.isexist == "exist") {
								$('#bindmobile').val("");
								alert(data.info);
							}
							return false;
						},
						error : function (xhr) {
							$('#bindmobile').val("");
							alert("系统错误，请输入一个新的手机号。");
							return false;
						}
					});
				}
			}
 
			return false;
		});

		$('#bindemail').blur(function() {
			if ($edituser.valid()) {
				var emailvalue = $('#bindemail').val();
				if (emailvalue != "") {
					$.ajax({    
						type : 'POST',        
						url : "index.php?m=user&a=checkEmailExist", 
						data : {email : emailvalue},
						cache : false,    
						dataType : 'json',    
						success : function (data) {
							if (data.data.isexist == "exist") {
								$('#bindemail').val("");
								alert(data.info);
							}
							return false;
						},
						error : function (xhr) {
							$('#bindemail').val("");
							alert("系统错误，请输入一个新的邮箱。");
							return false;
						}
					});
				}
			}
			
			return false;
		});

		$('#editusersubmit').click(function() {
			if ($('#edituser').valid()) {
				var confirmcontent = "确认修改用户信息？";
				if(confirm(confirmcontent)) {
					$('#edituser').ajaxSubmit({  
						dataType : 'json',    
						success : function (data) {
							if (data.data == "success") {
								notify(data.info, 'success');
                                setTimeout(function () {
                                    self.location.href = " /userall/ ";
                                }, 2000);
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

		$('#pass').click(function() {
			var confirmcontent = "确认通过该用户的申请？";
			if(confirm(confirmcontent)) {
				$('#pass').attr("disabled","disabled");
				$('#refuse').attr("disabled","disabled");
				$('#isverified').val("1");
				$('#edituser').ajaxSubmit({  
					dataType : 'json',    
					success : function (data) {
                        console.log(data);
						if (data.data == "success") {
							notify(data.info, 'success');
							setTimeout(function () {
								self.location.href = "/userall/ ";
							}, 2000);
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
		});

		$('#refuse').click(function() {
			var confirmcontent = "确认拒绝该用户的申请？";
			if(confirm(confirmcontent)) {
				$('#pass').attr("disabled","disabled");
				$('#refuse').attr("disabled","disabled");
				$('#isverified').val("2");
				$('#edituser').ajaxSubmit({  
					dataType : 'json',    
					success : function (data) {
						if (data.data == "success") {
							notify(data.info, 'success');
							setTimeout(function () {
								self.location.href = " /userall/ ";
							}, 2000);
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
		});

		// 点击用户类型，修改发票类型
		$("input[name='usertype']").change(function() {
			var type = $("input[name='usertype']:checked").val();
			if (type == 1) {
				$("#userinvoicetypediv").show();
				$("#companyinvoicetypediv").hide();
			} else {
				$("#userinvoicetypediv").hide();
				$("#companyinvoicetypediv").show();
			}
		});
    })
</script>
</body>
</html>