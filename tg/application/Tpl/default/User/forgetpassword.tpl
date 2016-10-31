<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "找回密码";
	$page_css[] = "vendors/bower_components/animate.css/animate.min.css";

?>
<include file="Inc:head" />
<body class="login-content sw-toggled">
<include file="Inc:original-header" />
<!-- Login -->
<div class="lc-block toggled" id="l-reset">
    <ul class="tab-nav tn-justified tn-icon" role="tablist">
        <li role="presentation" class="active">
            <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-smartphone-iphone icon-tab m-r-5"></i>
                通过绑定手机找回
            </a>
        </li>
        <li role="presentation">
            <a class="col-xs-6 f-15" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">
                <i class="zmdi zmdi-email icon-tab m-r-5"></i>
                通过绑定邮箱找回
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane animated fadeIn in active m-r-25" id="tab-1">
            <form id="mobilereset" class="form-horizontal" role="form" action="/index.php?m=user&a=mobilereset" method="post">
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-smartphone"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="mobile" id="mobile" placeholder="请输入手机号">
                        </div>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="mobileresetverify" id="mobileresetverify" placeholder="请输入右图四位数字" maxlength="4">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-default bt-send-code" id='mobileResetVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/mobileResetImageVerify')}>' id='mobileResetVerifyImg' /></a>
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

                <a class="btn btn-login btn-warning btn-float" id="mobileresetforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>

        <div role="tabpanel" class="tab-pane animated fadeIn in m-r-25" id="tab-2">
            <form id="emailreset" class="form-horizontal" role="form" action="/index.php?m=user&a=emailreset" method="post">
                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-email"></i></span>
                    <div class="col-sm-12">
                        <div class="fg-line">
                            <input type="email" class="form-control" name="email" id="email" placeholder="请输入邮箱">
                        </div>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="emailresetverify" id="emailresetverify" placeholder="请输入右图四位数字" maxlength="4">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-default bt-send-code" id='emailResetVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/emailResetImageVerify')}>' id='emailResetVerifyImg' /></a>
                    </div>
                </div>

                <div class="input-group m-b-20">
                    <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                    <div class="col-sm-8">
                        <div class="fg-line">
                            <input type="text" class="form-control" name="verifyemailmsg" id="verifyemailmsg" placeholder="请输入六位邮箱验证码" maxlength="6">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-default bt-send-code" id="sendemailmsg">发送验证码</button>
                    </div>
                </div>

                <div class="clearfix"></div>

                <a class="btn btn-login btn-warning btn-float" id="emailresetforward">
                    <i class="zmdi zmdi-forward"></i>
                </a>
            </form>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

    <div class="lc-block toggled" id="2-reset">
        <ul class="tab-nav tn-justified tn-icon" role="tablist">
            <li role="presentation" class="active">
                <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                    最后一步 - 重置密码
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane animated fadeIn in active m-r-25" id="tab-1">
                <form id="resetpassword" class="form-horizontal" role="form" action="/index.php?m=user&a=resetpassword" method="post">
                    <div class="input-group m-b-20">
                        <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                        <div class="col-sm-12">
                            <div class="fg-line">
                                <input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="请输入新密码">
                            </div>
                        </div>
                    </div>
                    <div class="input-group m-b-20">
                        <span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                        <div class="col-sm-12">
                            <div class="fg-line">
                                <input type="password" name="againpassword" id="againpassword" class="form-control" placeholder="请重新输入新密码">
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <a class="btn btn-login btn-warning btn-float" id="resetpasswordforward" >
                        <i class="zmdi zmdi-forward"></i>
                    </a>
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

<include file="Inc:original-scripts" />
<script type="text/javascript">
    var msgtime = "";
    var emailtime = "";

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
		$("#2-reset").hide();
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

        $("#mobileResetVerifyImgButton").click(function(){
            var Verify_Url = '<{:U('User/mobileResetImageVerify')}>';
            Verify_Url=Verify_Url.replace('.html','');
            $("#mobileResetVerifyImg").attr("src", Verify_Url+'/'+Math.random());
        });
        $("#emailResetVerifyImgButton").click(function(){
            var Verify_Url = '<{:U('User/emailResetImageVerify')}>';
            Verify_Url=Verify_Url.replace('.html','');
            $("#emailResetVerifyImg").attr("src", Verify_Url+'/'+Math.random());
        });


        /**手机找回*/
		var $mobilereset = $('#mobilereset').validate({
			rules : {
				mobile : {
					required : true,
					digits : true,
					minlength : 11,
					maxlength : 11
				},
				mobileresetverify : {
					required : true,
					digits : true,
					minlength : 4,
					maxlength : 4
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
					maxlength : '手机号必须为11位数字'
				},
				mobileresetverify : {
					required : '请输入图形验证码',
					digits : '图形验证码必须为4位数字',
					minlength : '图形验证码必须为4位数字',
					maxlength : '图形验证码必须为4位数字'
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

        $('#sendmsg').click(function() {
            if ($mobilereset.valid()) {
                var mobilevalue = $('#mobile').val();
                var verifyvalue = $('#mobileresetverify').val();
                if (mobilevalue != "" && verifyvalue != "") {
                    $('#sendmsg').attr("disabled", "disabled");
                    $.ajax({
                        type : 'POST',
                        url : "index.php?m=user&a=sendResetMsg",
                        data : {mobile : mobilevalue, verify : verifyvalue},
                        cache : false,
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "success") {
                                $("#mobile").attr("disabled", "disabled");
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

        $("#mobileresetforward").click(function(){
            if ($mobilereset.valid()) {
                var mobilevalue = $('#mobile').val();
                var verifyvalue = $('#mobileresetverify').val();
                if (mobilevalue != "") {
                    if(verifyvalue != ""){
                    $('#mobilereset').ajaxSubmit({
                        dataType: 'json',
                        success: function (data) {
                            if (data.data == "success") {
                                $("#l-reset").hide();
                                $("#2-reset").show();
                                notify('手机验证成功，请进一步完善信息。', 'success');
                            } else {
                                notify(data.info, 'danger');
                            }
                            return false;
                        },
                        error: function (xhr) {
                            notify('系统错误！', 'danger');
                            return false;
                        }
                    });
                    }else{
                        notify('请输入验证码。', 'danger');
                    }
                }else{
                    notify('请输入手机号。', 'danger');
                }
            }
        });


        /**重置密码*/
        var $resetpassword = $('#resetpassword').validate({
            rules : {
                newpassword : {
                    required : true,
                    minlength : 6,
                    maxlength : 20
                },
                againpassword : {
                    required : true,
                    minlength : 6,
                    maxlength : 20,
                    equalto : "#newpassword"
                }
            },

            messages : {
                newpassword : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位'
                },
                againpassword : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位',
                    equalto : '两次输入的密码必须相同'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });



        $("#resetpasswordforward").click(function(){
            if ($resetpassword.valid()) {
                var newpasswordvalue = $('#newpassword').val();
                var againpasswordvalue = $('#againpassword').val();
                if (newpasswordvalue != "") {
                    if(againpasswordvalue != ""){
                        $('#resetpassword').ajaxSubmit({
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
                                if (data.data == "success") {
                                    notify('修改成功，请重新登录。', 'success');
                                    setTimeout(function () {
                                        location.href = '/login/';
                                    }, 3000);
                                } else {
                                    notify(data.info, 'danger');
                                }
                                return false;
                            },
                            error: function (xhr) {
                                notify('系统错误！', 'danger');
                                return false;
                            }
                        });
                    }else{
                        notify('请重复输入密码。', 'danger');
                    }
                }else{
                    notify('请输入密码。', 'danger');
                }
            }
        });


/**邮箱找回*/

        var $emailreset = $('#emailreset').validate({
            rules : {
                email : {
                    required : true,
                    email : true
                },
                emailresetverify : {
                    required : true,
                    digits : true,
                    minlength : 4,
                    maxlength : 4
                },
                verifyemailmsg : {
                    digits : true,
                    minlength : 6,
                    maxlength : 6
                }
            },

            messages : {
                email : {
                    required : '请输入联系邮箱',
                    email : '请输入正确的邮箱'
                },
                emailresetverify : {
                    required : '请输入图形验证码',
                    digits : '图形验证码必须为4位数字',
                    minlength : '图形验证码必须为4位数字',
                    maxlength : '图形验证码必须为4位数字'
                },
                verifyemailmsg : {
                    digits : '邮箱验证码必须为6位数字',
                    minlength : '邮箱验证码必须为6位数字',
                    maxlength : '邮箱验证码必须为6位数字'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });

        $('#sendemailmsg').click(function() {
            if ($emailreset.valid()) {
                var emailvalue = $('#email').val();
                var emailverifyvalue = $('#emailresetverify').val();
                if (emailvalue != "" && emailverifyvalue != "") {
                    $('#sendemailmsg').attr("disabled", "disabled");
                    $.ajax({
                        type : 'POST',
                        url : "index.php?m=user&a=sendemailMsg",
                        data : {email : emailvalue, emailverify : emailverifyvalue},
                        cache : false,
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "success") {
                                $("#email").attr("disabled", "disabled");
                                $('#sendemailmsg').html("已发送(120)");
                                var leftTime = 120;
                                emailtime = setInterval( function () {
                                    leftTime = leftTime - 1;
                                    if (leftTime > 0) {
                                        $('#sendemailmsg').html("已发送("+leftTime.toString()+")");
                                    } else {
                                        clearInterval(emailtime);
                                        $('#sendemailmsg').removeAttr("disabled");
                                        $('#sendemailmsg').html("重新发送");
                                    }
                                }, 1000);
                            } else {
                                notify(data.info, 'danger');
                                $('#sendemailmsg').removeAttr("disabled");
                            }
                            return false;
                        },
                        error : function (xhr) {
                            notify('系统错误，请联系管理员。', 'danger');
                            $('#sendemailmsg').removeAttr("disabled");
                            return false;
                        }
                    });
                } else {
                    notify('请输入手机号和图形验证码。', 'danger');
                }
            }
            return false;
        });

        $("#emailresetforward").click(function(){
            if ($emailreset.valid()) {
                var emailvalue = $('#email').val();
                var emailverifyvalue = $('#emailresetverify').val();
                if (emailvalue != "") {
                    if(emailverifyvalue != ""){
                        $('#emailreset').ajaxSubmit({
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
                                if (data.data == "success") {
                                    $("#l-reset").hide();
                                    $("#2-reset").show();
                                    notify('邮箱验证成功，请进一步完善信息。', 'success');
                                } else {
                                    notify(data.info, 'danger');
                                }
                                return false;
                            },
                            error: function (xhr) {
                                notify('系统错误！', 'danger');
                                return false;
                            }
                        });
                    }else{
                        notify('请输入验证码。', 'danger');
                    }
                }else{
                    notify('请输入邮箱账号。', 'danger');
                }
            }
        });

    })
</script>
</body>
</html>