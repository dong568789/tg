<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "登陆";
	$page_css[] = "vendors/bower_components/animate.css/animate.min.css";
?>
<include file="Inc:head" />
<body class="login-content sw-toggled">
<include file="Inc:original-header" />
	<!-- Login -->
	<div class="lc-block toggled" id="l-login">
		<form id="userlogin" class="form-horizontal" role="form" action="/index.php?m=user&a=userlogin" method="post">
			<div class="input-group m-b-20 m-r-15">
				<span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
				<div class="col-sm-12">
					<div class="fg-line">
                        <if condition = "$_COOKIE['account'] neq ''">
						    <input type="text" class="form-control" value="<{$_COOKIE['account']}>" name="account" id="account" placeholder="请输入手机号/用户名" maxlength="50" autocomplete="off">
					    <else/>
                            <input type="text" class="form-control" name="account" id="account" placeholder="请输入手机号/用户名" maxlength="50" autocomplete="off">
                        </if>
                    </div>
				</div>
			</div>

			<div class="input-group m-b-20 m-r-15">
				<span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
				<div class="col-sm-12">
					<div class="fg-line">
						<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码" autocomplete="off">
					</div>
				</div>
			</div>

            <div class="input-group m-b-20">
                <span class="input-group-addon"><i class="zmdi zmdi-pin"></i></span>
                <div class="col-sm-8">
                    <div class="fg-line">
                        <input type="text" class="form-control" name="accountverify" id="accountverify" placeholder="请输入右图四位数字" maxlength="4" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-3">
                    <a class="btn btn-default bt-send-code" id='accountVerifyImgButton'><img title='点击刷新验证码' src='<{:U('User/accountImageVerify')}>' id='accountVerifyImg' /></a>
                </div>
            </div>

			<div class="clearfix"></div>

			<div class="checkbox m-r-25">
				<label>
                    <if condition = "$_COOKIE['remember'] eq 1">
                        <input type="checkbox" name="remember" id="remember" value="1" checked>
                        <else/>
                        <input type="checkbox" name="remember" id="remember" value="1">
                    </if>
					<i class="input-helper"></i>记住我
				</label>
				<span class="forget" style="float:right;">
					<a href="/forgetpassword/">忘记密码？</a>
				</span>
			</div>

			<a href="javascript:" class="btn btn-login btn-primary btn-float" id="loginforward">
				<i class="zmdi zmdi-forward"></i>
			</a>
		</form>
	</div>
<include file="Inc:original-scripts" />

<script type="text/javascript">
    //回车绑定事件
    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
            document.getElementById("loginforward").click();
        }
    };

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
            var reg =  /^[0-9a-zA-Z_@]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入英文小写字母或数字！");

        jQuery.validator.addMethod("checkCHN", function(value, element) {
            var reg =  /^[\u4e00-\u9fa5]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的汉字！");

        jQuery.validator.addMethod('checkPassword',function (value,element) {
            var password=jQuery.trim(value);
            if(password == ''){
                return false;
            }else{
                var reg=/^((?![\u4e00-\u9fff| ]).){6,20}$/;
                return this.optional(element) || (reg.test(password));
            }
        },'密码不能包含汉字和空格');

        $("#accountVerifyImgButton").click(function(){
            var Verify_Url = '<{:U('User/accountImageVerify')}>';
            Verify_Url=Verify_Url.replace('.html','');
            $("#accountVerifyImg").attr("src", Verify_Url+'/'+Math.random());
        });

        var $userlogin = $('#userlogin').validate({
            rules : {
                account : {
                    required : true,
                    checkENGsmallNUM : true,
                    minlength : 6,
                    maxlength : 20
                },
                password : {
                    required : true,
                    rangelength : [6,20],
                    checkPassword : true
                },
                accountverify : {
                    required : true,
                    digits : true,
                    minlength : 4,
                    maxlength : 4
                }
            },

            messages : {
                account : {
                    required : '请输入用户名',
                    checkENGsmallNUM : '子账号用户名必须为字母、数字、_、@',
                    minlength : '用户名长度为6-20位',
                    maxlength : '用户名长度为6-20位'
                },
                password : {
                    required : '此项目必填',
                    rangelength : jQuery.format('登录密码长度必须是{0}到{1}之间'),
                    checkPassword : '密码不能包含汉字和空格',
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


        $('#loginforward').click(function() {
            if ($('#userlogin').valid()) {
                if(document.getElementById("remember").checked){
                    var remember = 1;
                    $('#userlogin').ajaxSubmit({
                        type : 'POST',
                        data : {remember : remember},
                        cache : false,
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "true") {
                                notify("登录成功", 'success');
                                setTimeout(function () {
                                    location.href = "/source/";
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
                }else{
                    $('#userlogin').ajaxSubmit({
                        dataType : 'json',
                        success : function (data) {
                            console.log(data);
                            if (data.data == "true") {
                                notify("登录成功", 'success');
                                setTimeout(function () {
                                    location.href = "/source/";
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
            }
        });




    })
</script>
</body>
</html>