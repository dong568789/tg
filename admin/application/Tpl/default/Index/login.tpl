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
	<form id="userlogin" class="form-horizontal" role="form" action="index.php?m=index&a=userlogin" method="post">
		<div class="input-group m-b-20 m-r-15">
			<span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
			<div class="col-sm-12">
				<div class="fg-line">
					<input type="text" class="form-control" name="account" id="account" placeholder="请输入用户名" maxlength="50">
				</div>
			</div>
		</div>

		<div class="input-group m-b-20 m-r-15">
			<span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
			<div class="col-sm-12">
				<div class="fg-line">
					<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码">
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<a href="javascript:void(0);" class="btn btn-login btn-primary btn-float" id="loginforward">
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
            var reg =  /^[0-9a-z]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入英文小写字母或数字！");

        jQuery.validator.addMethod("checkCHN", function(value, element) {
            var reg =  /^[\u4e00-\u9fa5]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的汉字！");

        var $userlogin = $('#userlogin').validate({
            rules : {
                account : {
                    required : true,
                    checkENGsmallNUM : true,
                    minlength : 3,
                    maxlength : 16
                },
                password : {
                    required : true,
                    minlength : 6,
                    maxlength : 20
                }
            },

            messages : {
                account : {
                    required : '请输入用户名',
                    checkENGsmallNUM : '用户名必须为小写字母或数字',
                    minlength : '用户名长度为3-16位',
                    maxlength : '用户名长度为6-16位'
                },
                password : {
                    required : '此项目必填',
                    minlength : '密码长度为6-20位',
                    maxlength : '密码长度为6-20位'
                }
            },

            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });


        $('#loginforward').click(function() {
            if ($('#userlogin').valid()) {
                $('#userlogin').ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            notify("登录成功，2秒后跳转", 'success');
                            setTimeout(function () {
                                self.location.href = '/welcome/';
                            }, 2000);
                        } else {
                            notify(data.info, 'danger');
                        }
                        return false;
                    },
                    error : function (xhr) {
                        notify('账号不存在或密码错误！', 'danger');
                        return false;
                    }
                });
            }
        });
    })
</script>
</body>
</html>