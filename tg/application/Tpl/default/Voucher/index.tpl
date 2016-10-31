<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "代金券管理";
$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
$page_css[] = "myjs/myjs.css";

?>

<include file="Inc:head" />
<body  onLoad="javascript:document.getElementById('modifyrecharge').reset()">
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["用户充值"]["active"] = true;
    $page_nav["用户充值"]["sub"]["代金券管理"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>代金券管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">
                        </div>
                        <div class="card-body ">
							<div class="p-20">
								<form class="form-horizontal" role="form" id="modifyrecharge" method="post" action="index.php?m=voucher&a=modifyVoucherRecharge">
									<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<{$user['userid']}>">
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<label class="col-sm-12 f-15 text-center" id="preauthamountlabel">您已有预授权：<a id="preauthamounta"><{$user['coinpreauth']}></a> 游侠币</label>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="username" id="username" placeholder="用户手机号/邮箱/用户名" autocomplete="off" maxlength="40">
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<select class="selectpicker" id="increase" name="increase">
												<option value="1">充值代金券</option>
											</select>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<select class="selectpicker" id="sourceid" name="sourceid">
												<option value="-1">
													请选择游戏
												</option>
												<foreach name="source" item="vo" key="k">
													<option value="<{$vo.id}>" data-discount="<{$vo.dicount}>" data-gamename="<{$vo.gamename}>">
														 <{$vo.gamename}> - <{$vo.dicount_zhe}>折
													</option>
												</foreach>
											</select>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="rechargemoney" id="rechargemoney" placeholder="代金券面额" autocomplete="off" maxlength="30">
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<input type="hidden" name="paymoney" id="paymoney"></input>
											
											<div id="payyouxiabi_big_div">支付游侠币：<span id="payyouxiabi_span">0</span>个</div>
											<div id="paymoney_big_div" style="display: none">支付金额：<span id="paymoney_span">0</span>元</div>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-3 radio">
											<label>
			                                    <input type="radio" name="paytype" value="zfb" id="alipay-radio">
			                                    <i class="input-helper"></i>
			                                   支付宝
			                                </label>
			                            </div>
			                            <div class="col-sm-3 radio">
			                                <label>
			                                    <input type="radio" name="paytype" value="ptb" checked="checked" id="youxiabi-radio">
			                                    <i class="input-helper"></i>
			                                    预授权游侠币
			                                </label>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											<button type="button" class="btn btn-primary btn-lg btn-block" id="dorecharge">为用户充值代金券</button>
										</div>
										<div class="col-sm-3">
										</div>
									</div>
									<div class="form-group m-t-25">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-6">
											备注：1000元以下代金券有效期为一周，1000元以上代金券有效期为一个月。
										</div>
									</div>
								</form>
                            </div>
                            <div  class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-command" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th data-column-id="username">充值对象用户</th>
                                            <th data-column-id="mobile">手机号</th>
                                            <th data-column-id="email">邮箱</th>
                                            <th data-column-id="ptb">充值代金券</th>
                                            <th data-column-id="create_time" data-formatter="create_time">充值时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="voucherlog" item="vo" key="k">
											<tr>
												<td><{$vo['username']}></td>
												<td><{$vo['mobile']}></td>
												<td><{$vo['email']}></td>
												<td><{$vo['productname']}></td>
												<td><{$vo['create_time']}></td>
											</tr>
                                        </foreach>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<style type="text/css">
	#window_block {
		height: 400px;
	}
	#window_block ul {
		margin: 30px 0 40px 0;
	}
	#window_block ul li {
		list-style: none;
		line-height: 25px;
		height: 25px;
		font-size: 13px;
	}
	#window_block ul div.jianxi{
		margin-top: 15px;
	}
	#order_alipay_tip {
		color:red;
	}
</style>

<div id="window_block" class="wbox" >
    <h1>
        <em>游侠币支付</em>
        <span class="wclose" title="关闭">x</span>
    </h1>
    <div class="overf">
        <ul>
        	<li>订单信息：</li>
        	<div class="jianxi"></div>
        	<li>订单金额：<span id="order_money"></span> 元</li>
        	<li>商品名称：<span id="order_product_name"></span></li>
        	<li>商品描述：<span id="order_product_intro"></span></li>
        	<div class="jianxi"></div>
        	<li>您的预授权游侠币余额：<span id="all_youxaibi"></span> 游侠币</li>
        	<li>应支付游侠币数量：<span id="order_pay_youxaibi"></span> 游侠币</li>

        	<li>应支付金额：<span id="order_pay_money"></span> 元</li>
        	<div class="jianxi"></div>
        	<!-- <div id="order_alipay_tip">支付成功之后请手动刷新该页面，<br/>充值记录显示在最新的一行</div> -->
        	<div id="order_alipay_tip">支付成功之后不要关闭页面，支付宝会自动跳转！！！</div>
        </ul>
        
        <div>
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<button type="button" class="btn btn-primary btn-lg btn-block wok">确认</button>
			</div>
			<div class="col-sm-2">
			</div>
		</div>
    </div>
</div>

<include file="Inc:footer" />
<include file="Inc:scripts" />

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>
<script src="__ROOT__/plus/myjs/myjs.js"></script>

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

	var isdoingrecharge = 0;


    $(document).ready(function(){
        //Command Buttons
        $("#data-table-command").bootgrid({
            css: {
                icon: 'zmdi icon',
                iconColumns: 'zmdi-view-module',
                iconDown: 'zmdi-expand-more',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-expand-less'
            },
			formatters: {
				"create_time": function(column, row)
				{
					return new Date(parseInt(row.create_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " "); 
				}
			},
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div>"
            }
        });

        // 验证规则
		var $modifyrecharge = $('#modifyrecharge').validate({
			ignore : '',
			rules : {
				username : {
					required : true,
					minlength : 6,
					maxlength : 40
				},
				rechargemoney : {
					required : true,
					digits : true,
					min : 1,
		
				},
				sourceid : {
					min : 0
				}
			},

			messages : {
				username : {
					required : '对象用户名必填',
					minlength : '对象用户名为6-20位',
					maxlength : '对象用户名为6-20位'
				},
				rechargemoney : {
					required : '请输入代金券面额',
					digits : '代金券面额必须为正整数',
					min : '代金券面额必须为正整数',
					
				},
				sourceid : {
					min : '请选择游戏'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				if(element.attr('name')=='paymoney'){
					error.insertAfter($('#rechargemoney').parent());
				}else{
					error.insertAfter(element.parent());
				}
			}
		});

		// 对于select验证，当值改变时候的不过
		$("#sourceid").change(function(){  
	        $("#sourceid").removeData("previousValue").valid();  
	    });  

		// 选择充值金额，决定支付金额
        $("#rechargemoney").blur(function(){
            var amount = $('#rechargemoney').val();
            var discount=$('#sourceid').find("option:selected").attr('data-discount');
            if(discount && amount){
             	var paymoney=(amount*discount).toFixed(1);
	            $("#paymoney").val(paymoney);
	            $("#paymoney_span").html(paymoney);
	            $("#payyouxiabi_span").html(paymoney*10);	
            }
        });

        // 选择渠道号，决定支付金额
        $('#sourceid').change(function(){
        	var amount = $('#rechargemoney').val();
            var discount=$('#sourceid').find("option:selected").attr('data-discount');
            if(discount && amount){
             	var paymoney=(amount*discount).toFixed(1);
	            $("#paymoney").val(paymoney);
	            $("#paymoney_span").html(paymoney);
	            $("#payyouxiabi_span").html(paymoney*10);	
            }
        });


        // 初始化操作
        $('#youxiabi-radio').attr('checked','checked');
        $("#paymoney_big_div").hide();
        $("#payyouxiabi_big_div").show();
        $('#modifyrecharge').attr('action','index.php?m=voucher&a=modifyVoucherRecharge');

        // 切换支付方式，改变支付文字
        $('#alipay-radio').click(function(){
        	$("#paymoney_big_div").show();
            $("#payyouxiabi_big_div").hide();
            $('#modifyrecharge').attr('action','index.php?m=voucher&a=alipay_pay');
            $('#rechargemoney').parent().parent().find('.error').html(''); 
        });
        $('#youxiabi-radio').click(function(){
            $("#paymoney_big_div").hide();
            $("#payyouxiabi_big_div").show();
            $('#modifyrecharge').attr('action','index.php?m=voucher&a=modifyVoucherRecharge');
        });

		$('#dorecharge').click(function() {
			var paytype_val=$('input[name=paytype]:checked').val();
			if(paytype_val=='zfb'){
				$('#paymoney').rules("remove");
			}else{
				$('#paymoney').rules("add",{
					max : <{$user['coinpreauth']/10}>,
					messages: {  
						max : '您的游侠币不够购买该代金券'
					}
				});
			}
			
         	if ($('#modifyrecharge').valid()) {
				var rechargemoney_val=$('#rechargemoney').val();
				var gamename=$('#sourceid').find('option:selected').attr('data-gamename');
				var order_product_name_val="《"+gamename+"》"+rechargemoney_val+'元代金券';
				var order_pay_money_val=$('#paymoney').val();
				var order_pay_youxaibi_val=order_pay_money_val*10;
				$('#order_money').html(rechargemoney_val);
				$('#order_product_name').html(order_product_name_val);
				$('#order_product_intro').html(order_product_name_val);
				
				if(paytype_val=='zfb'){
					$('#order_pay_youxaibi').parent().hide();
					$('#all_youxaibi').parent().hide();
					$('#order_pay_money').html(order_pay_money_val).parent().show();
					$('#order_alipay_tip').show();
					// $('#modifyrecharge').attr('target','_blank');
				}else{
					$('#order_pay_youxaibi').html(order_pay_youxaibi_val).parent().show();
					$('#all_youxaibi').html($('#preauthamounta').html()).parent().show();
					$('#order_pay_money').parent().hide();
					$('#order_alipay_tip').hide();
					// $('#modifyrecharge').removeAttr('target');
				}

				my_alert_window1({
					'window_str':'#window_block',
					'okis_hide_window':false,
					'ok_fun':function(){
						if (isdoingrecharge == 0) {
							isdoingrecharge = 1;

							if(paytype_val=='zfb'){
								$('#modifyrecharge').submit();
							}else{
								$('#modifyrecharge').ajaxSubmit({  
									dataType : 'json',    
									success : function (data) {
										console.log(data);
										if (data.data == "success") {
											// $('#preauthamounta').html(data.info.newcoinpreauth);
											// var  create_time=new Date(parseInt(data.info.create_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
											// var tr_data='<tr><td>'+data.info.username+'</td><td>'+data.info.mobile+'</td><td>'+data.info.email+'</td><td>'+data.info.productname+'</td><td>'+create_time+'</td></tr>';
											// $('#data-table-command tr:eq(0)').after(tr_data);
											notify("为用户充值代金券成功。", 'success');
											// isdoingrecharge = 0;
											// // 清空表单
											// $modifyrecharge.resetForm();
											// $("#paymoney_span").html(0);
											// $("#payyouxiabi_span").html(0);
											// $('.bootstrap-select [data-id=sourceid] span.filter-option').html('请选择游戏');
											setTimeout(function(){
												location.href='/voucher/';
											},'800')
										} else {
											notify(data.info, 'danger');
											isdoingrecharge = 0;
										}
										return false;
									},
									error : function (xhr) {
										notify('系统错误！', 'danger');
										isdoingrecharge = 0;
										return false;
									}
								});
							}

						}
					}
				})
			}
			return false;
		});
    });
</script>
</body>
</html>