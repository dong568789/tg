<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "安全设置";
$page_css[] = "public/css/settings.css";

?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $profile_nav["安全设置"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>安全设置</h2>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body">
                            <div class="tab-header clearfix">
                                <ul>
                                    <li>
                                        <dl>
                                            <dd>
                                                <div class="operations-pwd col-sm-12">
                                                    <div class="col-sm-3"></div>
                                                    <div class="operations-pwd-content operations-content fl col-sm-6">
                                                        <div class="col-sm-1">
                                                            <img src="__ROOT__/plus/img/pwd.png" alt=""/>
                                                        </div>
                                                        <div class="col-sm-8 f-15 text-left">
                                                            <p class="p-relative m-b-0">密码管理<!--<span class="p-relative c-gray m-l-20">安全程度（<label class="font16 c-black"> 低 </label>）</span>--></p>
                                                            <p class="c-gray p-relative" style="top:-2px;">为了账号安全，请定期修改密码</p>
                                                        </div>
                                                        <div class="dialog col-sm-1">
                                                            <a data-toggle="modal" href="#psw" class="btn btn-info">修改</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
											<dd>
                                                <div class="operations-mail col-sm-12">
                                                    <div class="col-sm-3"></div>
                                                    <div class="operations-pwd-content operations-content fl col-sm-6">
                                                        <div class="col-sm-1">
                                                            <img src="__ROOT__/plus/img/mail.png" alt=""/>
                                                        </div>
                                                        <div class="col-sm-8 f-15 text-left">
                                                            <p class="p-relative m-b-0">绑定邮箱<span class="p-relative c-gray m-l-20"><{$user['bindemail']}></span></p>
                                                            <p class="c-gray p-relative" style="top:-2px;">可在找回密码时使用</p>
                                                        </div>
                                                        <div class="dialog col-sm-1">
                                                            <if condition="$user['bindemail'] neq ''">
                                                            <a data-toggle="modal" href="#mail" class="btn btn-info">修改</a>
                                                                <else/>
                                                                <a data-toggle="modal" href="#mail" class="btn btn-info">绑定</a>
                                                                </if>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
											<dd>
                                                <div class="operations-phone col-sm-12">
                                                    <div class="col-sm-3"></div>
                                                    <div class="operations-pwd-content operations-content fl col-sm-6">
                                                        <div class="col-sm-1">
                                                            <img src="__ROOT__/plus/img/phone.png" alt=""/>
                                                        </div>
                                                        <div class="col-sm-8 f-15 text-left">
                                                            <p class="p-relative m-b-0">绑定手机<span class="p-relative c-gray m-l-20"><{$user['bindmobile']}></span></p>
                                                            <p class="c-gray p-relative" style="top:-2px;">提高安全等级，可用于登陆和找回密码</p>
                                                        </div>
                                                        <div class="dialog col-sm-1">
                                                            <if condition="$user['bindmobile'] neq ''">
																<a data-toggle="modal" href="#phone" class="btn btn-info">修改</a>
                                                            <else/>
                                                                <a data-toggle="modal" href="#phone" class="btn btn-info">绑定</a>
                                                            </if>

                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--修改密码-->
            <div class="modal fade" id="psw" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">修改密码</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" id="editpassword" action="/index.php?m=member&a=editpassword" method="post">
                                <div class="card-body card-padding">
                                    <div class="form-group">
                                        <label for="oldpassword" class="col-sm-3 control-label">原密码</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="password" class="form-control" name="oldpassword" id="oldpassword" placeholder="请输入原密码">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="newpassword" class="col-sm-3 control-label">新密码</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="password" class="form-control" name="newpassword" id="newpassword" placeholder="请输入新密码">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="againpassword" class="col-sm-3 control-label">重复新密码</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="password" class="form-control" name="againpassword" id="againpassword" placeholder="请重复输入新密码">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="savepassword">保存</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>
            </div>


            <!--修改绑定邮箱-->
            <div class="modal fade" id="mail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" id="mailstep1">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">修改登录邮箱</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" enctype="multipart/form-data" id="editbindemail" action="/index.php?m=member&a=editbindemail" method="post">
                                <div class="card-body card-padding">
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-sm-3 control-label">新邮箱</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="请输入新邮箱地址">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="accountverify" class="col-sm-3 control-label">验证码</label>
                                        <div class="col-sm-4">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" name="accountverify" id="accountverify" placeholder="请输入右边四位数字验证码">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <a class="btn btn-default bt-send-code" id='accountVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/accountImageVerify')}>' id='accountVerifyImg' /></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary" id="editemailforward">下一步</a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>

                <div class="modal-dialog" id="mailstep2" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">修改登录邮箱</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" enctype="multipart/form-data" id="updatebindemail" action="/index.php?m=member&a=updatebindemail" method="post">
                                <div class="card-body card-padding">
                                    <div class="form-group">
                                        <div class="col-sm-1"></div>
                                        <div class="col-sm-11 text-left">
                                            <h4>已经发送验证码到你的邮箱：<span id="getemail"></span></h4>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <label for="emailmsg" class="col-sm-3 control-label">验证码</label>
                                        <div class="col-sm-4">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" name="emailmsg" id="emailmsg" placeholder="请输入邮箱验证码">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <button class="btn btn-default bt-send-code" id="sendemailmsg">重新发送</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary" id="updateemailforward">下一步</a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>

                <div class="modal-dialog" id="mailstep3" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">修改登录邮箱</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form">
                                <div class="card-body card-padding">
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <h4>您已成功修改登录邮箱</h4>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <a href="/settings/" class="btn btn-primary">确定</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!--修改绑定手机-->
			<if condition="$user['bindmobile'] neq ''">
				<div class="modal fade" id="phone" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog" id="phone-1">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">解绑手机验证</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form" id="editbindmobile" action="/index.php?m=member&a=editbindmobile" method="post">
									<div class="card-body card-padding">
										<div class="form-group">
											<label class="col-sm-3 control-label">要解绑的手机号码</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<input type="text" class="form-control" name="mobile" value="<{$user['bindmobile']}>"  id="mobile" placeholder="请输入要解绑的手机号码" disabled>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="inputPassword3" class="col-sm-3 control-label">验证码</label>
											<div class="col-sm-4">
												<div class="fg-line">
													<input type="text" class="form-control" name="verifymsg" id="verifymsg" placeholder="请输入短信验证码">
												</div>
											</div>
											<div class="col-sm-3">
												<button class="btn btn-default bt-send-code" id="sendmsg">发送验证码</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<a data-toggle="modal" href="javascript:" id="editmobileforward" class="btn btn-primary">下一步</a>
								<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							</div>
						</div>
					</div>
					<div class="modal-dialog" id="phone-2" style="display:none;">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">新手机验证</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form" id="updatebindmobile" action="/index.php?m=member&a=updatebindmobile" method="post">
									<div class="card-body card-padding">
										<div class="form-group">
											<label class="col-sm-3 control-label">新手机号码</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<input type="text" class="form-control" name="newmobile" id="newmobile" placeholder="请输入新手机号码">
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="inputPassword3" class="col-sm-3 control-label">验证码</label>
											<div class="col-sm-4">
												<div class="fg-line">
													<input type="text" class="form-control" name="verifycode" id="verifycode"  placeholder="请输入短信验证码">
												</div>
											</div>
											<div class="col-sm-3">
												<button class="btn btn-default bt-send-code" id="sendcode">发送验证码</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<a data-toggle="modal" href="javascript:" id="updatemobileforward" class="btn btn-primary">下一步</a>
								<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							</div>
						</div>
					</div>
					<div class="modal-dialog"  id="phone-3" style="display:none;">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5" style="border-bottom: 2px solid #ddd;">修改绑定手机</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form">
									<div class="card-body card-padding">
										<div class="form-group">
											<div class="col-sm-12 text-center">
												<h4>您已成功修改绑定手机</h4>
											</div>
										</div>
                                        <div class="form-group">
                                            <div class="col-sm-12 text-center">
                                                <a href="/settings/" class="btn btn-primary">确定</a>
                                            </div>
                                        </div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			<else />
				<div class="modal fade" id="phone" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog" id="phone-2">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5" style="border-bottom:2px solid #ddd;">新手机验证</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form" id="updatebindmobile" action="/index.php?m=member&a=updatebindmobile" method="post">
									<div class="card-body card-padding">
										<div class="form-group">
											<label class="col-sm-3 control-label">新手机号码</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<input type="text" class="form-control" name="newmobile" id="newmobile" placeholder="请输入新手机号码">
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="inputPassword3" class="col-sm-3 control-label">验证码</label>
											<div class="col-sm-4">
												<div class="fg-line">
													<input type="text" class="form-control" name="verifycode" id="verifycode"  placeholder="请输入短信验证码">
												</div>
											</div>
											<div class="col-sm-3">
												<button class="btn btn-default bt-send-code" id="sendcode">发送验证码</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<a data-toggle="modal" href="javascript:" id="updatemobileforward" class="btn btn-primary">下一步</a>
								<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							</div>
						</div>
					</div>
					<div class="modal-dialog"  id="phone-3" style="display:none;">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5" style="border-bottom: 2px solid #ddd;">修改绑定手机</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form">
									<div class="card-body card-padding">
										<div class="form-group">
											<div class="col-sm-12 text-center">
												<h4>您已成功修改绑定手机</h4>
											</div>
										</div>
                                        <div class="form-group">
                                            <div class="col-sm-12 text-center">
                                                <a href="/settings/" class="btn btn-primary">确定</a>
                                            </div>
                                        </div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</if>
        </div>
    </section>
</section>

<include file="Inc:footer" />
<include file="Inc:scripts" />
<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>

<script type="text/javascript">
    var msgtime = "";
    var newmobiletime = "";
    var newemailtime = "";

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
        //个人信息菜单展开
        $(".profile_nav").css("display","block");

        jQuery.validator.addMethod("mobileNUMBER", function(value, element) {
            var reg =  /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|170)\d{8}$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的手机号码！");

        $("#accountVerifyImgButton").click(function(){
            var Verify_Url = '<{:U('User/accountImageVerify')}>';
            Verify_Url=Verify_Url.replace('.html','');
            $("#accountVerifyImg").attr("src", Verify_Url+'/'+Math.random());
        });

        //解绑旧手机验证
        var $editbindmobile = $('#editbindmobile').validate({
            rules : {
                mobile : {
                    required : true,
                    digits : true,
                    minlength : 11,
                    maxlength : 11,
                    mobileNUMBER : true
                },
                verifymsg : {
                    digits : true,
                    minlength : 6,
                    maxlength : 6
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
                verifymsg : {
                    digits : '短信验证码必须为6位数字',
                    minlength : '短信验证码必须为6位数字',
                    maxlength : '短信验证码必须为6位数字'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        $('#mobile').blur(function() {
            if ($editbindmobile.valid()) {
                var mobilevalue = $('#mobile').val();
                var _this = this;
                $.ajax({
                    type: 'POST',
                    url: "index.php?m=member&a=checkMobile",
                    data: {mobile : mobilevalue},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "notexist") {
                            $('#mobile').val("");
                            notify(data.info, 'danger');
                        }
                        return false;
                    },
                    error: function (xhr) {
                        $('#mobile').val("");
                        notify('系统错误，请输入一个新的账号。', 'danger');
                        return false;
                    }
                });

            }
            return false;
        });

        //发送短信验证码
        $('#sendmsg').click(function() {
            if ($editbindmobile.valid()) {
                var mobilevalue = $('#mobile').val();
                if (mobilevalue != "") {
                    $('#sendmsg').attr("disabled", "disabled");
                    $.ajax({
                        type : 'POST',
                        url : "index.php?m=member&a=sendMsg",
                        data : {mobile : mobilevalue},
                        cache : false,
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
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
                    notify('请输入手机号。', 'danger');
                }
            }
            return false;
        });

        $('#editmobileforward').click(function() {
            if ($('#editbindmobile').valid()) {
                $('#editbindmobile').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            $("#phone-1").hide();
                            $("#phone-2").show();
                            notify('验证成功，请进一步完善资料。', 'success');
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

        //新手机验证
        var $updatebindmobile = $('#updatebindmobile').validate({
            rules : {
                newmobile : {
                    required : true,
                    digits : true,
                    minlength : 11,
                    maxlength : 11,
                    mobileNUMBER:true
                },
                verifycode : {
                    digits : true,
                    minlength : 6,
                    maxlength : 6
                }
            },

            messages : {
                newmobile : {
                    required : '请输入手机号',
                    digits : '手机号必须为11位数字',
                    minlength : '手机号必须为11位数字',
                    maxlength : '手机号必须为11位数字',
                    mobileNUMBER : '手机号码不符合规范'

                },
                verifycode : {
                    digits : '短信验证码必须为6位数字',
                    minlength : '短信验证码必须为6位数字',
                    maxlength : '短信验证码必须为6位数字'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        $('#newmobile').blur(function() {
			var mobilevalue = $('#newmobile').val();
			if (mobilevalue != "") {
				if ($updatebindmobile.valid()) {
					var _this = this;
					$.ajax({
						type: 'POST',
						url: "index.php?m=member&a=newMobile",
						data: {newmobile : mobilevalue},
						cache: false,
						dataType: 'json',
						success: function (data) {
							if (data.data == "exist") {
								$('#newmobile').val("");
								notify(data.info, 'danger');
								return false;
							}
							return false;
						},
						error: function (xhr) {
							$('#newmobile').val("");
							notify('系统错误，请输入一个新的手机号。', 'danger');
							return false;
						}
					});
				}
				return false;
			}
        });
        //新手机发送短信验证码
        $('#sendcode').click(function() {
            if ($updatebindmobile.valid()) {
                var mobilevalue = $('#newmobile').val();
                if (mobilevalue != "") {
                    $('#sendcode').attr("disabled", "disabled");
                    $.ajax({
                        type : 'POST',
                        url : "index.php?m=member&a=sendCode",
                        data : {mobile : mobilevalue},
                        cache : false,
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "success") {
                                $('#sendcode').html("已发送(120)");
                                var leftTime = 120;
                                newmobiletime = setInterval( function () {
                                    leftTime = leftTime - 1;
                                    if (leftTime > 0) {
                                        $('#sendcode').html("已发送("+leftTime.toString()+")");
                                        $("#newmobile").attr("disabled", "disabled");
                                    } else {
                                        clearInterval(newmobiletime);
                                        $('#sendcode').removeAttr("disabled");
                                        $('#sendcode').html("重新发送");
                                    }
                                }, 1000);
                            } else {
                                notify(data.info, 'danger');
                                $('#sendcode').removeAttr("disabled");
                            }
                            return false;
                        },
                        error : function (xhr) {
                            notify('系统错误，请联系管理员。', 'danger');
                            $('#sendcode').removeAttr("disabled");
                            return false;
                        }
                    });
                } else {
                    notify('请输入手机号。', 'danger');
                }
            }
            return false;
        });

        $('#updatemobileforward').click(function() {
            if ($('#updatebindmobile').valid()) {
                $('#updatebindmobile').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            $("#phone-2").hide();
                            $("#phone-3").show();
                            $("#newmobilep").html($('#newmobile').val());
                            notify('恭喜修改绑定手机成功。', 'success');
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


        //修改密码
        var $editpassword = $('#editpassword').validate({
            rules : {
                oldpassword : {
                    required : true,
                    minlength : 6,
                    maxlength : 20
                },
                newpassword : {
                    required : true,
                    minlength : 6,
                    maxlength : 20
                },
                againpassword : {
                    required : true,
                    minlength : 6,
                    maxlength : 20
                }
            },

            messages : {
                oldpassword : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位'
                },
                newpassword : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位'
                },
                againpassword : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        $('#savepassword').click(function() {
            if ($editpassword.valid()) {
                $('#editpassword').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            notify(data.info, 'success');
                            setTimeout(function () {
                                location.href = '/login/';
                            }, 3000);
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

        //修改绑定邮箱
        var $editbindemail = $('#editbindemail').validate({
            rules : {
                email : {
                    required : true,
                    email : true
                },
                accountverify : {
                    required : true,
                    digits : true,
                    minlength : 4,
                    maxlength : 4
                }
            },

            messages : {
                email : {
                    required : '请输入新邮箱',
                    email : '请输入正确的邮箱'
                },
                accountverify : {
                    required : '请输入图形验证码',
                    digits : '图形验证码必须为4位数字',
                    minlength : '图形验证码必须为4位数字',
                    maxlength : '图形验证码必须为4位数字'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        //检验新邮箱
        $('#email').blur(function() {
            var emailvalue = $('#email').val();
			if (emailvalue != "") {
				var _this = this;
				$.ajax({
					type: 'POST',
					url: "index.php?m=member&a=newEmail",
					data: {newemail : emailvalue},
					cache: false,
					dataType: 'json',
					success: function (data) {
						if (data.data == "exist") {
							notify(data.info, 'danger');
							$('#email').val("");
						}
						return false;
					},
					error: function (xhr) {
						$('#email').val("");
						notify('系统错误，请输入一个新的邮箱账号。', 'danger');
						return false;
					}
				});
			}
        });

        //发送邮箱验证码
        $('#editemailforward').click(function() {
            if ($('#editbindemail').valid()) {
                $('#editbindemail').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            $("#mailstep1").hide();
                            $("#mailstep2").show();
                            $("#getemail").html($('#email').val());
                            notify(data.info, 'success');
                            $('#sendemailmsg').html("已发送(120)");
                            var leftTime = 120;
                            newemailtime = setInterval( function () {
                                leftTime = leftTime - 1;
                                if (leftTime > 0) {
                                    $('#sendemailmsg').html("已发送("+leftTime.toString()+")");
                                    $("#sendemailmsg").attr("disabled", "disabled");
                                } else {
                                    clearInterval(newemailtime);
                                    $('#sendemailmsg').removeAttr("disabled");
                                    $('#sendemailmsg').html("重新发送");
                                }
                            }, 1000);
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
            return false;
        });

        //重新发送邮箱验证码
        $('#sendemailmsg').click(function() {
            if ($('#editbindemail').valid()) {
                $('#editbindemail').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            $("#mailstep1").hide();
                            $("#mailstep2").show();
                            $("#getemail").html($('#email').val());
                            notify(data.info, 'success');
                            $('#sendemailmsg').html("已发送(120)");
                            var leftTime = 120;
                            newemailtime = setInterval( function () {
                                leftTime = leftTime - 1;
                                if (leftTime > 0) {
                                    $('#sendemailmsg').html("已发送("+leftTime.toString()+")");
                                    $("#sendemailmsg").attr("disabled", "disabled");
                                } else {
                                    clearInterval(newemailtime);
                                    $('#sendemailmsg').removeAttr("disabled");
                                    $('#sendemailmsg').html("重新发送");
                                }
                            }, 1000);
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
            return false;
        });

        //新邮箱验证码
        var $updatebindemail = $('#updatebindemail').validate({
            rules : {
                emailmsg : {
                    digits : true,
                    minlength : 6,
                    maxlength : 6
                }
            },

            messages : {
                emailmsg : {
                    digits : '邮箱验证码必须为6位数字',
                    minlength : '邮箱验证码必须为6位数字',
                    maxlength : '邮箱验证码必须为6位数字'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        //检验邮箱验证码
        $('#updateemailforward').click(function() {
            if ($updatebindemail.valid()) {
                $('#updatebindemail').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        if (data.data == "success") {
                            $("#mailstep2").hide();
                            $("#mailstep3").show();
                            $("#newemail").html($('#getemail').val());
                            notify('恭喜修改绑定邮箱成功。', 'success');
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
            return false;
        });
    })
</script>
</body>
</html>