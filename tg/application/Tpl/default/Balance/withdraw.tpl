<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "收入提现";
$page_css[] = "vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css";

?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["结算中心"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>收入提现</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body p-b-25">
                            <div class="row m-t-25 p-2 m-b-25 m-l-25 m-r-25">
                                <div class="col-xs-4 ">
                                    <div class="bgm-blue brd-2 p-15">
                                        <div class="c-white m-b-5">未提现收入</div>
                                        <if condition = "($money['unwithdraw'] neq '') AND ($money['unwithdraw'] neq 0)">
                                        <h2 class="m-0 c-white f-300" id="income">￥<{$money['unwithdraw']}></h2>
                                        <else/>
                                        <h2 class="m-0 c-white f-300" id="income">￥0</h2>
                                        </if>
                                    </div>
                                </div>

                                <div class="col-xs-4">
                                    <div class="bgm-green brd-2 p-15">
                                        <div class="c-white m-b-5">已结算</div>
                                        <if condition = "($money['settled'] neq '') AND ($money['settled'] neq 0)">
                                        <h2 class="m-0 c-white f-300">￥<{$money['settled']}></h2>
                                        <else/>
                                        <h2 class="m-0 c-white f-300">￥0</h2>
                                        </if>
                                    </div>
                                </div>

                                <div class="col-xs-4">
                                    <div class="bgm-amber brd-2 p-15">
                                        <div class="c-white m-b-5">未结算</div>
                                        <if condition = "($money['unsettled'] neq '') AND ($money['unsettled'] neq 0)">
                                        <h2 class="m-0 c-white f-300">￥<{$money['unsettled']}></h2>
                                        <else/>
                                        <h2 class="m-0 c-white f-300">￥0</h2>
                                        </if>
                                    </div>
                                </div>

                            </div>

                            <div class="row p-20">
								<div class="col-sm-12">
									<form class="form-horizontal">
										<input type="hidden" name="hiddenwithdrawlimit" id="hiddenwithdrawlimit" value="<{$user['withdrawlimit']}>">
										<input type="hidden" name="hiddenstartdate" id="hiddenstartdate" value="<{$startdate}>">
										<input type="hidden" name="hiddenenddate" id="hiddenenddate" value="<{$enddate}>">
										<div class="form-group">
											<label class="col-sm-3 control-label text-left">起始日期：</label>
											<div class="col-sm-4 m-t-8">
												<span><a class="m-r-20"><{$startdate}></a>*起始日期无法更改</span>
											</div>
										</div>

										<div class="form-group m-t-15">
											<label class="col-sm-3 control-label text-left">截至日期：</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<input type="text" class="form-control balance-date-picker" name="enddate" id="enddate" placeholder="请输入提现截至日期" value="<{$enddate}>" autocomplete="false" list="url_list">

													<!-- <datalist id="url_list">
														<option label="W3Schools" value="15549079440" />
														<option label="Google" value="http://www.google.com" />
														<option label="Microsoft" value="http://www.microsoft.com" />
													</datalist> -->
												</div>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label text-left">提现金额：</label>
											<div class="col-sm-4 m-t-8">
												<span><a class="m-r-20" id="withdrawamount">￥<{$money['unwithdraw']}></a></span>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-3 control-label text-left">提现方式：</label>
											<div class="col-sm-7">
												<button type="button" class="btn btn-warning" id="alipay"  style="padding: 8px 12%;margin-right: 3%;">支付宝</button>
												<button type="button" class="btn btn-danger" id="bank" style="padding: 8px 12%;margin-right: 3%;">银行卡</button>

												<button type="button" class="btn btn-info" id="youxiabi" style="padding: 8px 12%;" data-type="3">游侠币</button>

												<p style="margin:15px 0 5px 0;">
													*  提现之前请先完善账号信息 <br/>
													*  如果提现为游侠币，不大于 5万 游侠币，不需要审核，自动转入
												</p>
												<p style="display:none;margin:0;" id="newaccount">*  请点击 <a href="/account/">这里</a> 新增一个支付宝或银行账号</p>
											</div>
										</div>

										<!--判断是否存在银行卡支付宝账号-->
										<input type="hidden" id="isbank" data-bank="<{$bank}>" value="<{$bank}>">
										<input type="hidden" id="isalipay" data-alipay="<{$alipay}>" value="<{$alipay}>">

										<!---判断此页是否有type 和 id 的返回值-->
										<input type="hidden" id="checktab" data-type="<?php echo $_GET['type'];?>" data-id="<?php echo $_GET['id'];?>">

										<div class="form-group" id="paymethod-alipay" style="display: none">
											<label class="col-sm-3 control-label text-left p-l-0">选择账户：</label>
											<div class="row col-sm-8 p-l-25 m-b-20" id="alipaylist">
												<foreach name="alipay" item="vo" key="k">
												<div class="col-sm-5 m-r-25 p-t-10 m-b-10 account-alipay alipay<{$vo['id']}>" id="account-alipay<{$vo['id']}>" style="border: 1px solid #ddd;border-radius: 10px;">
													<div class="col-sm-12 p-0">
														<a href="javascript:void(0);" class="c-gray bankalipay" data-id="<{$vo['id']}>" data-type="1">
															<p>账户名称：<{$vo['aliusername']}></p>
															<p>&nbsp;</p>
															<p>支付宝账号：<{$vo['aliaccount']}></p>
														</a>
													</div>
													<div style="position: absolute;top: 10px;right: 20px">
														<a href="/index.php?m=member&a=account&id=<{$vo['id']}>&type=1" type="button" class="btn btn-default select-button">修改</a>
													</div>
												</div>
												</foreach>
											</div>
										</div>

										<div class="form-group" id="paymethod-bank" style="display: none">
											<label class="col-sm-3 control-label text-left p-l-0">选择账户：</label>
											<div class="row col-sm-8 p-l-25 m-b-20" id="banklist">
												<foreach name="bank" item="vo" key="k">
												<div class="col-sm-5 m-r-25 p-t-10 m-b-10 account-bank bank<{$vo['id']}>" style="border: 1px solid #ddd;border-radius: 10px;">
													<div class="col-sm-12 p-0">
														<a href="javascript:void(0);" class="c-gray bankalipay" data-id="<{$vo['id']}>" data-type="2">
															<p>账户名称：<{$vo['bankusername']}></p>
															<p>银行卡号：<{$vo['bankaccount']}></p>
															<p>开户银行：<{$vo['bankname']}></p>
														</a>
													</div>
													<div style="position: absolute;top: 10px;right: 20px">
														<a href="/index.php?m=member&a=account&id=<{$vo['id']}>&type=2" type="button" class="btn btn-default select-button">修改</a>
													</div>
												</div>
												</foreach>
											</div>
										</div>

										<div class="form-group" id="paymethod-youxiabi" style="display: none">
											<label class="col-sm-3 control-label text-left p-l-0">提现数量：</label>
											<div class="col-sm-4 m-t-8">
												<span id="youxiabiamount"><{$money['unwithdraw']*10|round=###}></span>游侠币
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label text-left">确认密码：</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<input type="password" id="password" class="form-control" placeholder="请输入登录密码">
												</div>
											</div>
										</div>

										<div class="form-group m-t-25">
											<div class="col-sm-4">
											</div>
											<div class="col-sm-4">
												<a href="javascript:" id="submitApply" class="btn btn-primary btn-lg col-md-12">提交申请</a>
											</div>
											<div class="col-sm-4">
											</div>
										</div>
									</form>
								</div>
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

<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

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

    //修改账号后的返回页
    function checktab(type,id) {
        if (type == 1) {
            $("#paymethod-bank").css("display","block");
            $(".bank"+id).addClass("bgm-cyan");
        } else if(type == 2){
            $("#paymethod-alipay").css("display","block");
            $(".alipay"+id).addClass("bgm-cyan");
        } else{
            $("#paymethod-bank").css("display","none");
            $("#paymethod-alipay").css("display","none");
        }
    }
	
	var accountid = 0; //设置全局变量判断是否有选择银行卡或支付宝
    var type = 0; //1为支付宝 2为银行卡

	var isdoingwithdraw = 0;

    //选择账号
    $(document).ready(function() {
		if ($('.balance-date-picker')[0]) {
			var startdate = $("#hiddenstartdate").val();
			var enddate = $("#hiddenenddate").val();
			$('.balance-date-picker').datetimepicker({
				format: 'YYYY-MM-DD',
				minDate: startdate,
				maxDate: enddate,
				locale: moment.locale('zh-cn')
			});
		}

		$('#enddate').blur(function() {
			var startdate = $("#hiddenstartdate").val();
			var enddate = $('#enddate').val();
			var reg=/^\d{4}-\d{2}-\d{2}$/;

			if(!reg.test(enddate)){
				notify('请选择提现方式。', 'danger');
				return false;
			}

			$.ajax({
				type : 'POST',
				url : "index.php?m=balance&a=getUnwithdraw",
				data : {start : startdate, end : enddate},
				cache : false,
				dataType : 'json',
				success : function (data) {
					if (data.data == "success") {
						$("#withdrawamount").html("￥"+data.info+"");
						$("#youxiabiamount").html(Math.round(data.info*10));
					} else {
						swal({
							title: "出错了",   
							text: data.info, 
							type: "error",
							confirmButtonText: "确认"
						});
					}
					return false;
				},
				error : function (xhr) {
					swal({
						title: "出错了",   
						text: "系统错误", 
						type: "error",
						confirmButtonText: "确认"
					});
					return false;
				}
			});
		});

        var type = $("#checktab").attr("data-type");
        var id = $("#checktab").attr("data-id");
        if(type != '' && id != ''){
            checktab(type, id);
        }else{
            //do nothing
        }

        // 支付方式切换
        // 使用变量的方式缓存jQuery对象，优化
        var bank_ele=$("#bank");
        var alipay_ele=$("#alipay");
        var youxiabi_ele=$("#youxiabi");
        var newaccount_ele=$("#newaccount");
        var paymethod_alipay_ele=$("#paymethod-alipay");
        var paymethod_bank_ele=$("#paymethod-bank");
        var paymethod_youxiabi_ele=$("#paymethod-youxiabi");

        bank_ele.click(function() {
            var bank = $("#isbank").attr("data-bank");
            if(bank == '' || bank == null){
                notify('暂无银行卡账号，请先到个人中心添加账号。', 'danger');
				newaccount_ele.show();
            }else{
				newaccount_ele.hide();
                paymethod_bank_ele.css("display","block");
            }
            paymethod_alipay_ele.css("display","none");
           	paymethod_youxiabi_ele.css("display","none");
        });

        alipay_ele.click(function() {
            var alipay = $("#isalipay").attr("data-alipay");
            if(alipay == "" || alipay == null){
                notify('暂无支付宝账号，请先到个人中心添加账号。', 'danger');
				newaccount_ele.show();
            } else{
				newaccount_ele.hide();
                paymethod_alipay_ele.css("display","block");
            }
            paymethod_bank_ele.css("display","none");
            paymethod_youxiabi_ele.css("display","none");
        });

        youxiabi_ele.click(function() {
			newaccount_ele.hide();
            paymethod_alipay_ele.css("display","none");
            paymethod_bank_ele.css("display","none");
            paymethod_youxiabi_ele.css("display","block");

            // 赋值
            accountid = 0;
            type = $(this).attr("data-type");
        });

        $(".account-bank").click(function() {
            $(this).addClass("bgm-cyan").siblings().removeClass("bgm-cyan");
			$("#alipaylist").children().removeClass("bgm-cyan");
        });
        $(".account-alipay").click(function() {
            $(this).addClass("bgm-cyan").siblings().removeClass("bgm-cyan");
			$("#banklist").children().removeClass("bgm-cyan");
        });

        $(".bankalipay").click(function(){
            accountid = $(this).attr("data-id");
            type = $(this).attr("data-type");
        });
		
		//提现(提交申请)
        $('#submitApply').click(function() {
        	$('#submitApply').attr("disabled","disabled");
			var withdrawlimit = $("#hiddenwithdrawlimit").val();
			var startdate = $("#hiddenstartdate").val();
			var enddate = $('#enddate').val();
            var money = $('#withdrawamount').text();
            var money = money.substr(1);
            if ((parseInt(money) < withdrawlimit) || (parseInt(money) == 0)) {
                notify('提现金额太低，暂不可提现。', 'danger');    //判断提现金额
                $('#submitApply').removeAttr("disabled");
                return false;
            } 

            var reg=/^\d{4}-\d{2}-\d{2}$/;
            if(!reg.test(enddate)){
            	notify('请选择正确的截至日期', 'danger');    //判断提现金额
            	$('#submitApply').removeAttr("disabled");
				return false;
			}

            if( !type ||  ((type == 1 || type == 2) && (accountid == 0 || accountid == ''))){     //判断是否选择提现方式
                notify('请选择提现方式。', 'danger');
                $('#submitApply').removeAttr("disabled");
                return false;
            }else{
                var password = $('#password').val();
                if (password == "") {             //判断登录密码
                    notify('请输入登录密码。', 'danger');
                    $('#submitApply').removeAttr("disabled");
                } else{
                	$('#submitApply').attr("disabled","disabled");
					swal({
						title: "确认提交结算申请？",
						text: "提交申请完成后，您截至到指定日期的所有收入都将进入结算，您今天将无法再次提交申请。",
						showCancelButton: true,
						confirmButtonColor: "#00ccff",
						confirmButtonText: "确认",
						cancelButtonText: "取消",
						closeOnConfirm: false
					}, function(){
						isdoingwithdraw = 1;
						swal({
							title: "请稍侯...",   
							text: "正在生成结算单，请稍后", 
							type: "hold",
							showConfirmButton: false
						});
						$.ajax({
							type : 'POST',
							url : "index.php?m=balance&a=dowithdraw",
							data : {password : password, type : type, accountid : accountid, start : startdate, end : enddate},
							cache : false,
							dataType : 'json',
							success : function (data) {
								if (data.data == "success") {
									swal({
										title: "已提交",   
										text: "提现申请已成功提交", 
										type: "success",
										showConfirmButton: true
									}, function(isConfirm){   
										if (isConfirm) {     
											self.location.href = "/balance/";   
										}
									});
								} else {
									isdoingwithdraw = 0;
									$('#submitApply').removeAttr("disabled");
									swal({
										title: "出错了",   
										text: data.info, 
										type: "error",
										confirmButtonText: "确认"
									});
								}
								return false;
							},
							error : function (xhr) {
								isdoingwithdraw = 0;
								$('#submitApply').removeAttr("disabled");
								swal({
									title: "出错了",   
									text: "系统错误", 
									type: "error",
									confirmButtonText: "确认"
								});
								return false;
							}
						});
					});
                }
            }
        });
    })
</script>
</body>
</html>