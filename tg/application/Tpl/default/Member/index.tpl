<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "用户资料";
$page_css[] = "vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $profile_nav["用户资料"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>用户资料</h2>
            </div>

            <div class="card" id="profile-main">
                <div class="pm-overview c-overflow">
                    <div class="pmo-block pmo-contact hidden-xs">
                        <if condition="$_SESSION['usertype'] eq 1">
                            <if condition="$_SESSION['gender'] eq 1">
                                <img class="col-sm-12 p-t-5" src="__ROOT__/plus/img/profile-pics/user-male-middle.png" alt="">
                                <else/>
                                <img class="col-sm-12 p-t-5" src="__ROOT__/plus/img/profile-pics/user-female-middle.png" alt="">
                            </if>
                            <else/>
                            <img class="col-sm-12 p-t-5" src="__ROOT__/plus/img/profile-pics/company-middle.png" alt="">
                        </if>

                        <div class="col-sm-12 p-l-25">
                            <p class="m-t-25 f-13"><i class="zmdi zmdi-face icon-tab m-r-5"></i><span class="c-gray"><{$user['account']}></span></p>
                            <i class="zmdi zmdi-edit icon-tab m-r-5"></i><a href="javascript:void();" class="c-gray edit f-13">修改资料</a>
                        </div>
                    </div>
                </div>

                <div class="pm-body message clearfix">
                    <ul class="tab-nav tn-justified tn-icon" role="tablist">
                        <li role="presentation" class="active">
                            <a class="col-xs-6 f-20" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                                <i class="zmdi zmdi-account icon-tab m-r-5"></i>
                                用户信息
                            </a>
                        </li>
                        <li role="presentation">
                            <a class="col-xs-6 f-20" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">
                                <i class="zmdi zmdi-time-restore icon-tab m-r-5"></i>
                                过往履历
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-20">
                        <div role="tabpanel" class="tab-pane animated fadeIn in active" id="tab-1">
                            <div class="pmb-block">
                                <div class="pmbb-header">
                                    <h2><i class="zmdi zmdi-account m-r-5"></i> 基本信息</h2>
                                </div>
                                <div class="pmbb-body p-l-30">
                                    <div class="pmbb-view">
                                        <dl class="dl-horizontal">
                                            <dt>账户</dt>
                                            <dd><{$user['account']}></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>用户身份</dt>
                                            <?php
                                            echo "<dd>";
                                            echo $user['usertype'] == 2 ? "公司" : "个人";
                                            echo "</dd>";
                                            ?>
                                        </dl>
                                    </div>

                                </div>
                            </div>
                            <div class="pmb-block">
                                <div class="pmbb-header">
                                    <h2><i class="zmdi zmdi-phone m-r-5"></i> 联系信息</h2>
                                </div>
                                <div class="pmbb-body p-l-30">
                                    <div class="pmbb-view">
										<dl class="dl-horizontal">
                                            <dt>联系人</dt>
                                            <dd><{$user['realname']}></dd>
                                        </dl>
                                        <if condition="1 eq $user['usertype'] ">
										<dl class="dl-horizontal">
                                            <dt>性别</dt>
                                            <?php
                                            echo "<dd>";
                                            echo $user['gender'] == 0 ? "未知" : ($user['gender'] == 1 ? "男" : "女");
                                            echo "</dd>";
                                            ?>
                                        </dl>
                                        </if>
										<dl class="dl-horizontal">
                                            <dt>公司名称</dt>
                                            <dd><{$user['companyname']}></dd>
                                        </dl>
										<dl class="dl-horizontal">
                                            <dt>发票类型</dt>
                                            <?php
                                            echo "<dd>";
                                            echo $user['invoicetype'] == 0 ? "不开发票" : ($user['invoicetype'] == 1 ? "普通发票" : ($user['invoicetype'] == 2 ? "3%增值税发票" : "6%增值税发票"));
                                            echo "</dd>";
                                            ?>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>联系手机</dt>
                                            <dd><{$user['contactmobile']}></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>联系邮箱</dt>
                                            <dd><{$user['contactemail']}></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>联系地址</dt>
                                            <dd><{$user['address']}></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane animated fadeIn" id="tab-2">
                            <div class="pmb-block">
                                <div class="pmbb-header">
                                    <h2><i class="zmdi zmdi-account m-r-5"></i>最近登录信息</h2>
                                </div>
                                <div class="pmbb-body p-l-30">
                                    <div class="pmbb-view">
                                        <dl class="dl-horizontal">
                                            <dt>最近登录时间</dt>
                                            <dd><{$userlog['first']['createtime']}></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>最近登录IP</dt>
                                            <dd><{$userlog['first']['loginip']}></dd>
                                        </dl>
                                    </div>

                                </div>
                            </div>
                            <div class="pmb-block">
                                <div class="pmbb-header">
                                    <h2><i class="zmdi zmdi-account m-r-5"></i>过往登录信息</h2>
                                </div>
                                <foreach name="userlog['others']" item="vo" key="k">
                                <div class="pmbb-body p-l-30 p-b-30">
                                    <div class="pmbb-view">
                                        <dl class="dl-horizontal">
                                            <dt>登录时间</dt>
                                            <dd><{$vo['createtime']}></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>登录IP</dt>
                                            <dd><{$vo['loginip']}></dd>
                                        </dl>
                                    </div>
                                </div>
                                </foreach>
                            </div>
                        </div>
                    </div>
                </div>
          
                <div class="pm-body information clearfix">
                    <ul class="tab-nav tn-justified tn-icon" role="tablist">
                        <li role="presentation" class="active">
                            <a class="col-xs-6 f-20" aria-controls="tab-1" role="tab" data-toggle="tab">
                                <i class="zmdi zmdi-edit icon-tab m-r-5"></i>
                                编辑资料信息
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="pmb-block">
                            <form class="form-horizontal" id="profile" role="form" action="/index.php?m=member&a=edituser" method="post">
                                <div class="form-group m-t-25">
									<label for="account" class="col-sm-3 control-label f-15 m-t-5">用户帐号</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<input type="text" class="form-control" value="<{$user['account']}>" name="account" disabled>
										</div>
									</div>
                                </div>
								<div class="form-group m-t-25">
									<label class="col-sm-3 f-15 control-label">用户身份</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<label class="radio radio-inline m-r-20">
												<input class="radioclass" type="radio" name="usertype" value="1" disabled <if condition="1 eq $user['usertype'] ">checked="true"</if>>
												<i class="input-helper p-relative" style="left:-26px;"></i>
												个人
											</label>

											<label class="radio radio-inline m-r-20">
												<input class="radioclass" type="radio" name="usertype" value="2" disabled <if condition="2 eq $user['usertype'] ">checked="true"</if>>
												<i class="input-helper p-relative" style="left:-26px;"></i>
												公司
											</label>
										</div>
									</div>
								</div>
								<if condition="2 eq $user['usertype'] ">
									<div class="form-group m-t-25" id="companynamediv">
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">公司名字</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="companyname" name="companyname" placeholder="请输入公司名字" value="<{$user['companyname']}>">
											</div>
										</div>
									</div>
								<else />
									<div class="form-group m-t-25" id="companynamediv" style="display:none;">
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">公司名字</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" id="companyname" name="companyname" placeholder="请输入公司名字" value="<{$user['companyname']}>">
											</div>
										</div>
									</div>
								</if>
								<div class="form-group m-t-25">
									<label for="account" class="col-sm-3 control-label f-15 m-t-5">联系人姓名</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<input type="text" class="form-control" value="<{$user['realname']}>" name="realname">
										</div>
									</div>
                                </div>
								<if condition="1 eq $user['usertype'] ">
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
								<else />
									<div class="form-group m-t-25" id="genderdiv" style="display:none;">
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
								</if>
								<div class="form-group m-t-25">
									<label for="account" class="col-sm-3 control-label f-15 m-t-5">联系手机</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<input type="text" class="form-control" value="<{$user['contactmobile']}>" name="contactmobile" id="contactmobile">
										</div>
									</div>
                                </div>
								<div class="form-group m-t-25">
									<label for="account" class="col-sm-3 control-label f-15 m-t-5">联系邮箱</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<input type="text" class="form-control" value="<{$user['contactemail']}>" name="contactemail">
										</div>
									</div>
                                </div>
								<if condition="1 eq $user['usertype'] ">
									<div class="form-group m-t-25" id="userinvoicetypediv">
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">发票类型</label>
										<div class="col-sm-7">
											<select class="selectpicker" id="userinvoicetype" name="invoicetype">
												<option value="0">不开发票</option>
											</select>
										</div>
									</div>
								<else />
									<div class="form-group m-t-25" id="companyinvoicetypediv">
										<label for="account" class="col-sm-3 control-label f-15 m-t-5">发票类型</label>
										<div class="col-sm-7">
											<select class="selectpicker" id="companyinvoicetype" name="invoicetype">
												<option value="1" <if condition="$user['invoicetype'] eq '1'"> selected </if>>普通发票</option>
												<option value="2" <if condition="$user['invoicetype'] eq '2'"> selected </if>>3%增值税发票</option>
												<option value="3" <if condition="$user['invoicetype'] eq '3'"> selected </if>>6%增值税发票</option>
											</select>
										</div>
									</div>
								</if>
								<div class="form-group m-t-25">
									<label for="account" class="col-sm-3 control-label f-15 m-t-5">联系地址</label>
									<div class="col-sm-7">
										<div class="fg-line">
											<input type="text" class="form-control" value="<{$user['address']}>" name="address">
										</div>
									</div>
                                </div>
								
                                <div class="form-group m-t-25">
									<div class="col-sm-12 text-center m-t-25">
										<button id="saveprofile" type="button" class="btn btn-primary btn-lg m-r-15">保存</button>
										<a href="/profile/" class="btn btn-default btn-lg c-gray">取消</a>
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

<script src="__ROOT__/plus/vendors/bower_components/autosize/dist/autosize.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>

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
        //个人信息菜单展开
        $(".profile_nav").css("display","block");

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
            var reg =  /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|170)\d{8}$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的手机号码！");


		$(".information").hide();
        $(".edit").click(function(){
            $(".message").hide();
            $(".information").show();
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

		var $profile = $('#profile').validate({
			rules : {
				usertype : {
					required : true
				},
				realname : {
                    checkCHN : true
				},
				gender : {
					required : true
				},
				contactmobile : {
					required : true,
					digits : true,
					minlength : 11,
					maxlength : 11,
                    mobileNUMBER:true
				},
				contactemail : {
					required : true,
					email : true
				}
			},

			messages : {
				usertype : {
					required : '请选择您的身份'
				},
				realname : {
                    checkCHN : '联系人姓名必须为汉字'
				},
				gender : {
					required : '请选择性别'
				},
				contactmobile : {
					required : '请输入手机号',
					digits : '手机号必须为11位数字',
					minlength : '手机号必须为11位数字',
					maxlength : '手机号必须为11位数字',
                    mobileNUMBER: '手机号码不符合规范'
				},
				contactemail : {
					required : '请输入邮箱地址',
					email : '请输入正确的邮箱'
				}
				
				
			},

			errorPlacement : function(error, element) {
				if (element.hasClass('bs-select-hidden')) {
					error.insertAfter(element.parent().children().eq(1));
				} else if (element.hasClass('radioclass')) {
					error.insertAfter(element.parent().parent());
				} else {
					error.insertAfter(element.parent());
				}
			}
		});

        $('#saveprofile').click(function() {
			if ($('#profile').valid()) {
				var confirmcontent = "确认修改个人信息？";
				if(confirm(confirmcontent)) {
					$('#profile').ajaxSubmit({  
						dataType : 'json',    
						success : function (data) {
                            console.log(data);
							if (data.data == "success") {
								notify(data.info, 'success');
                                setTimeout(function () {
                                    location.href = '/profile/';
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
				} else {
					return false;
				}
			}
        });
    })
</script>


</body>
</html>