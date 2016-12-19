<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "首页";
	$page_css[] = "public/css/index.css";
	$page_css[] = "public/css/imgscroll.css";
?>
<include file="Inc:head" />

<body>
<if condition="$user eq 'logged'">
    <header id="header" class="clearfix" data-current-skin="blue">
        <ul class="header-inner">
            <li class="logo hidden-xs">
                <a href="/" style="padding:0;">
                    <img src="__ROOT__/plus/img/pt_logo.jpg" alt="" style="vertical-align:middle;position: relative;top: -8px;"/>
                </a>
            </li>

            <li class="pull-right">
                <ul class="top-menu">

                    <li class="hidden-xs">
                        <a target="_self" href="/channel/">
                            <span class="tm-label">渠道管理</span>
                        </a>
                    </li>

                    <li class="hidden-xs">
                        <a target="_self" href="/source/">
                            <span class="tm-label">推广资源</span>
                        </a>
                    </li>

                    <li class="hidden-xs">
                        <a target="_self" href="/statistics/">
                            <span class="tm-label">数据统计</span>
                        </a>
                    </li>

                    <li class="hidden-xs">
                        <a target="_self" href="/balance/">
                            <span class="tm-label">结算中心</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a data-toggle="dropdown" href="">
                            <i class="tm-icon zmdi zmdi-email"></i>
                            <if condition="$allUnreadMessage['num'] neq '' && $allUnreadMessage['num'] neq 0">
                                <i class="tmn-counts" id="messageCounts"><{$allUnreadMessage['num']}></i>
                            </if>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg pull-right">
                            <div class="listview" id="notifications">
                                <div class="lv-header">
                                    我的消息
                                    <ul class="actions">
                                        <li class="dropdown">
                                            <if condition="$allUnreadMessage['num'] neq '' && $allUnreadMessage['num'] neq 0">
                                                <a href="javascript:" data-clear="notification" id="allUnreadMessage">
                                                    <i class="zmdi zmdi-check-all"></i>
                                                </a>
                                            </if>
                                        </li>
                                    </ul>
                                </div>
                                <div class="lv-body" id="messageContainer">
                                    <foreach name="allUnreadMessage" item="vo" key="k">
                                        <a class="lv-item" href="/message/">
                                            <div class="media">
                                                <div class="media-body">
                                                    <div class="lv-title"><{$vo['title']|msubstr=0,8,'utf-8',false}></div>
                                                    <small class="lv-small"><{$vo['content']}></small>
                                                </div>
                                                <div class="pull-right" style="position: relative;top: -35px; color: #666"><{$vo['time']}></div>
                                            </div>
                                        </a>
                                    </foreach>
                                </div>
                                <a class="lv-footer" href="/message/">查看所有消息</a>
                            </div>
                        </div>
                    </li>

                    <li class="dropdown">
                        <a data-toggle="dropdown" href=""><i class="tm-icon zmdi zmdi-more-vert"></i></a>
                        <ul class="dropdown-menu dm-icon pull-right">
                            <li class="hidden-xs">
                                <a data-action="fullscreen" href=""><i class="zmdi zmdi-fullscreen"></i> 全屏显示</a>
                            </li>
                            <li>
                                <a href="index.php?m=user&a=logout"><i class="zmdi zmdi-long-arrow-tab"></i> 登出</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Top Search Content -->
        <div id="top-search-wrap">
            <div class="tsw-inner">
                <i id="top-search-close" class="zmdi zmdi-arrow-left"></i>
                <input type="text">
                <i id="top-search-action" class="zmdi zmdi-search"></i>
            </div>
        </div>
    </header>
<else />
	<include file="Inc:original-header" />
</if>
	<section id="main" data-layout="layout-1" style="padding-top:82px;">
		<section id="content">
			<div class="index-top fullSlide focusMap2" style="margin-top: -30px;height: 400px;">
                <div class="banners">
                    <ul class="banner-list">
                        <li class="banner-1 "><a href="" title=""><img src="__ROOT__/plus/img/index/top.jpg"></a></li>
                        <li class="banner-2 "><a href="" title=""><img src="__ROOT__/plus/img/index/pt_banner3.jpg"></a></li>
                    </ul>
                </div>
                <!--banner 切换图片按钮 end-->
                <div class="banner-btns">
                    <ul>
                        <li class="on"></li>
                        <li></li>
                    </ul>
                </div>

				<div class="index-top-login p-15 z-depth-3">
					<if condition="$user eq 'logged'">
						<div class="m-t-25 text-center">
							<span class="f-20 m-t-25">欢迎回来</span>
							<div class="fg-line m-t-10" style="font-size:25px;">
								<a><{$account}></a>
							</div>
							<div class="fg-line m-t-10 f-15">
								你可以点击<a href="/new_channel/">这里</a>新建渠道
							</div>
							<div class="fg-line m-t-10 f-15">
								或点击<a href="/source/">这里</a>获取推广资源
							</div>
						</div>
					<else />
						<form id="userlogin" class="form-horizontal" role="form" action="/index.php?m=user&a=do_login" method="post">
							<div class="input-group m-b-20 m-r-15 m-t-10">
								<span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
                                <div class="col-sm-14">
    								<div class="fg-line">
                                        <if condition = "$_COOKIE['account'] neq ''">
                                            <input type="text" style="padding-left: 6px;" class="form-control" value="<{$_COOKIE['account']}>" name="account" id="account" placeholder="请输入手机号/用户名" maxlength="50" autocomplete="off">
                                            <else/>
                                            <input type="text" style="padding-left: 6px;" class="form-control" name="account" id="account" placeholder="请输入手机号/用户名" maxlength="50" autocomplete="off">
                                        </if>
    								</div>
                                </div>
							</div>

							<div class="input-group m-b-20 m-r-15">
								<span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                                <div class="col-sm-14">
    								<div class="fg-line">
    									<input type="password" style="padding-left: 6px;" class="form-control" name="password" id="password" placeholder="请输入密码">
    								</div>
                                </div>
							</div>

							<div class="clearfix"></div>

							<div class="checkbox m-r-25">
								<label class="m-l-25">
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

							<div class="p-10">
								<a href="javascript:void(0);" class="btn btn-primary btn-block"  id="loginforward">
									登陆
								</a>
							</div>
						</form>
						<div class="p-5">
							<span class="m-l-25">
								<a href="/register/">还未注册？赶快成为我们的会员吧</a>
							</span>
						</div>
					</if>
				</div>
			</div>

			<div class="container">
				<div class="row m-t-25" style="position: relative;">
					<div class="col-sm-8">
						<div class="card go-social p-b-15" style="height: 452px;">
                            <div class="card-header">
                                <h2>热门游戏</h2>
                            </div>
                            <div class="card-body clearfix">
                                <foreach name="game" item="vo" key="k">
                                <div class="col-sm-4" >
                                    <a href="" class="col-sm-12"><img src="<{$ADMINDOMAIN}>/upfiles/gameicon/<{$vo['gameicon']}>" style="width:68px;height: 68px;" class="img-responsive" alt=""></a>
									<a href="" class="gamename"><{$vo['gamename']}></a>
									<p class="gamedescription m-t-15"><{$vo['gamecategory']}></p>
                                </div>
                                </foreach>
                            </div>
                        </div>
					</div>

					<div class="col-sm-4">
						<div class="card">
                            <div class="listview" style="height: 452px;">
                                <div class="lv-header">
                                    最新公告
                                </div>
                                <div class="lv-body">
                                    <foreach name="announce" item="vo" key="k">
                                    <a class="lv-item announce_item" href="/announcedetail/<{$vo['id']}>/" style="padding: 8px 20px;">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="lv-title">【<{$vo['category']}>】 <{$vo['title']}></div>
                                            </div>
                                        </div>
                                    </a>
                                    </foreach>
                                </div>
                                <a href="/announce/" class="lv-footer p-t-10 p-b-10">查看全部</a>
                            </div>
                        </div>
					</div>
				
				</div>

                <!--内容-->
				
				<div class="row" style="position: relative;">
					<div class="col-sm-12">
						<div class="card go-social p-b-15">
                            <div class="card-header">
                                <h2>新手上路<small></small></h2>
                            </div>

                            <div class="card-body clearfix" style="position: relative;left:4%;">
                                <if condition="$_SESSION['userid'] eq ''">
                                <a class="col-xs-3" href="/index.php?m=guide&a=guide_unlogged&guideid=4">
                                    <img src="__ROOT__/plus/img/index/bottom01-down.png" class="img-responsive" alt="">
                                </a>
                                
                                <a class="col-xs-3" href="/index.php?m=guide&a=guide_unlogged&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom02-down.png" class="img-responsive" alt="">
                                </a>
                                
                                <a class="col-xs-3" href="/index.php?m=guide&a=guide_unlogged&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom03-down.png" class="img-responsive" alt="">
                                </a>

                                <a class="col-xs-3" href="/index.php?m=guide&a=guide_unlogged&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom04-down.png" class="img-responsive" alt="">
                                </a>
                                <else/>
                                <a class="col-xs-3" href="/index.php?m=guide&a=index&guideid=4">
                                    <img src="__ROOT__/plus/img/index/bottom01-down.png" class="img-responsive" alt="">
                                </a>

                                <a class="col-xs-3" href="/index.php?m=guide&a=index&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom02-down.png" class="img-responsive" alt="">
                                </a>

                                <a class="col-xs-3" href="/index.php?m=guide&a=index&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom03-down.png" class="img-responsive" alt="">
                                </a>

                                <a class="col-xs-3" href="/index.php?m=guide&a=index&guideid=5">
                                    <img src="__ROOT__/plus/img/index/bottom04-down.png" class="img-responsive" alt="">
                                </a>
                                </if>
                        </div>

                    </div>
					</div>
				</div>

			</div>
		</section>
	</section>

<include file="Inc:footer" />
<include file="Inc:original-scripts" />

<script type="text/javascript" src="__ROOT__/plus/public/js/slide.js"></script>
<script type="text/javascript" src="__ROOT__/plus/public/js/ie6.png.js"></script>
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
            var reg=/^((?![\u4e00-\u9fff| ]).){6,20}$/;
            return this.optional(element) || (reg.test(value));
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
                    checkPassword : '子账号密码不能包含汉字和空格',
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
                error.parents('.input-group ').removeClass('m-b-20');
            },
            success: function(label) {
                label.parents('.input-group ').addClass('m-b-20');
            }
        });

        $('.banners ul li img').css('width','100%');

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
                        dataType: 'json',
                        success: function (data) {
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
                        error: function (xhr) {
                            notify('系统错误！', 'danger');
                            return false;
                        }
                    });
                }
            }
        });

        $(window).load(function(){
            $(".fullSlide").slide({
                titCell:".banner-btns li",
                mainCell:".banners ul",
                effect:"fold",
                autoPlay:true,
                delayTime:700
            });
        });

        // 设置banner的轮播图的自适应屏幕的宽度
        $(window).resize(function(){
            var body_width=$(document.body).width();
            $('.banners ul').css('width', body_width);
            $('.banners ul li').css('width',body_width);
            $('.banners ul li img').css('width',body_width);
        });
    })
</script>

</body>
</html>