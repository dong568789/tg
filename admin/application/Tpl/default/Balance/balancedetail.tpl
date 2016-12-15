<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "财务结算列表";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
$page_css[] = "myjs/myjs.css";

?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["财务管理"]["active"] = true;
    $page_nav["财务管理"]["sub"]["所有结算单"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>结算单详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header ch-alt text-center">              
                        </div>
                        <div class="card-body ">
							<div class="p-20">
								<!-- 用户详情 -->
								<div class="row">
									<div class="col-xs-6">
										<div class="text-right">
											<h4><{$user['account']}></h4>
											<if condition = "$balance['accounttype'] eq 1">
												<h4 class="c-blue">打款方式&nbsp;支付宝</h4>
											<elseif condition = "$balance['accounttype'] eq 2"/>
												<h4 class="c-blue">打款方式&nbsp;银行卡</h4>
											<elseif condition = "$balance['accounttype'] eq 3"/>
												<h4 class="c-blue">打款方式&nbsp;游侠币</h4>
											</if>
											
											<span class="text-muted f-14">
												<if condition = "$user['contactmobile'] neq ''">
													<{$user['contactmobile']}>
												<else/>
													&nbsp;
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['usertype'] eq 1">
													个人
												<else/>
													公司名称&nbsp;<{$user['companyname']}>
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['address'] neq '' ">
													<{$user['address']}>
												</if>
											</span>
										</div>
									</div>
									
									<div class="col-xs-6">
										<div class="i-to">
											<if condition = "$user['realname'] neq ''">
												<h4><{$user['realname']}></h4>
											<else/>
												<h4>&nbsp;</h4>
											</if>
											<if condition = "$balance['accounttype'] eq 1">
												<h4 class="c-blue">支付宝账号&nbsp;<{$aliaccount['aliaccount']}>&nbsp;<{$aliaccount['aliusername']}></h4>
											<elseif condition = "$balance['accounttype'] eq 2"/>
												<h4 class="c-blue">银行卡账号&nbsp;<{$bankaccount['bankaccount']}>&nbsp;<{$bankaccount['bankusername']}>&nbsp;<{$bankaccount['bankname']}></h4>
											<elseif condition = "$balance['accounttype'] eq 3"/>
												<h4 class="c-blue"><{$user['account']}></h4>
											</if>
											
											<span class="text-muted f-14">
												<if condition = "$user['contactemail'] neq ''">
													<{$user['contactemail']}>
												<else/>
													&nbsp;
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['invoicetype'] eq 1">
													开普通发票(对于我们来说：税点 6.72%)()
												<elseif condition = "$user['invoicetype'] eq 2" />
													开3%增值税发票(对于我们来说：税点 3.36%)
												<elseif condition = "$user['invoicetype'] eq 3" />
													开6%增值税发票(对于我们来说：税点 0%)
												<else />
													不开发票
												</if>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['address'] neq '' ">
													<{$user['postnumber']}>
												</if>
											</span>
										</div>
									</div>
								</div>
								
								<div class="clearfix"></div>
								
								<!-- 账单块显示 -->
								<div class="row m-t-25 p-0 m-b-25">
									<div class="col-xs-3">
										<div class="bgm-cyan brd-2 p-15">
											<div class="c-white m-b-5">账单起始日期</div>
											<h4 class="m-0 c-white f-300"><?php echo date("Y年m月d日", strtotime($balance["startdate"]." 00:00:00"))?></h4>
										</div>
									</div>
									
									<div class="col-xs-3">
										<div class="bgm-cyan brd-2 p-15">
											<div class="c-white m-b-5">账单截至日期</div>
											<h4 class="m-0 c-white f-300"><?php echo date("Y年m月d日", strtotime($balance["enddate"]." 00:00:00"))?></h4>
										</div>
									</div>
									
									<div class="col-xs-3">
										<div class="bgm-green brd-2 p-15">
											<if condition = "$balance['accounttype'] eq 3">
												<div class="c-white m-b-5">申请结算游侠币</div>
												<h4 class="m-0 c-white f-300"><{$balance['actualamount']*10|round=###}></h4>
											<else />
												<div class="c-white m-b-5">申请结算金额(税后)</div>
												<h4 class="m-0 c-white f-300">￥<{$balance['actualamount']}></h4>
											</if>
										</div>
									</div>
									
									<div class="col-xs-3">
										<if condition = "$balance['balancestatus'] eq 4">
											<div class="bgm-red brd-2 p-15" id="paidamountdiv">
												<if condition = "$balance['accounttype'] eq 3">
													<div class="c-white m-b-5">实际结算游侠币</div>
												<else/>	
													<div class="c-white m-b-5">实际结算金额</div>
												</if>
												<h4 class="m-0 c-white f-300" id="paidamountcontent">尚未结算，账单有误</h4>
											</div>
										<elseif condition = "$balance['balancestatus'] eq 1 or $balance['balancestatus'] eq 3" />
											<div class="bgm-green brd-2 p-15" id="paidamountdiv">
												<if condition = "$balance['accounttype'] eq 3">
													<div class="c-white m-b-5">实际结算游侠币</div>
												<else/>	
													<div class="c-white m-b-5">实际结算金额</div>
												</if>
												<h4 class="m-0 c-white f-300" id="paidamountcontent">尚未结算</h4>
											</div>
										<elseif condition = "$balance['balancestatus'] eq 2"/>
											<div class="bgm-green brd-2 p-15" id="paidamountdiv">
												<if condition = "$balance['accounttype'] eq 3">
													<div class="c-white m-b-5">实际结算游侠币</div>
													<h4 class="m-0 c-white f-300" id="paidamountcontent"><{$balance['paidamount']*10}></h4>
												<else/>	
													<div class="c-white m-b-5">实际结算金额</div>
													<h4 class="m-0 c-white f-300">￥<{$balance['paidamount']}></h4>
												</if>
											</div>
										</if>
									</div>
								</div>
								
								<!-- 对于下面两个操作、导出结算单操作，需要 -->
								<input type="hidden" name="hiddenbalanceid" id="hiddenbalanceid" value="<{$balance['id']}>">

								<!-- 对账单的处理 -->
								<if condition = "$user['usertype'] eq 2 and $user['invoicetype'] neq 0 and $balance['balancestatus'] eq 1 ">
									<!-- 对于开发票的公司用户，且处于申请阶段 -->
									<div class="form-group m-t-25">
										<div class="col-sm-3"></div>
										<div class="col-sm-6">
											<button type="button" class="btn btn-primary btn-lg btn-block" id="checkbill">对账单已出，请客户确认对账单并开具发票</button>
										</div>
										<div class="col-sm-3"></div>
									</div>
								<elseif condition = "$balance['balancestatus'] eq 1 or $balance['balancestatus'] eq 3"/>
									<form class="form-horizontal" role="form" id="modifybalance">
										<input type="hidden" name="hiddenactualamount" id="hiddenactualamount" value="<{$balance['actualamount']}>">
										<input type="hidden" name="hiddenaccounttype" id="hiddenaccounttype" value="<{$balance['accounttype']}>">
										<div class="form-group m-t-25">
											<div class="col-sm-3"></div>
											<div class="col-sm-3">
												<if condition = "$balance['accounttype'] eq 3">
													<label class="col-sm-6 control-label f-15">实际打款游侠币</label>
													<div class="col-sm-6">
														<div class="fg-line">
															<input type="text" class="form-control" name="paid_youxiabi_amount" id="paid_youxiabi_amount" placeholder="实际打款游侠币" autocomplete="off">
														</div>
													</div>
												<else />
													<label class="col-sm-6 control-label f-15">实际打款金额</label>
													<div class="col-sm-6">
														<div class="fg-line">
															<input type="text" class="form-control" name="paidamount" id="paidamount" placeholder="实际打款金额" autocomplete="off">
														</div>
													</div>
												</if>
											</div>
                                            <div class="col-sm-3">
                                                <label class="col-sm-6 control-label f-15">备注</label>
                                                <div class="col-sm-6">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="beizhu" id="beizhu" placeholder="备注" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3"></div>
										</div>
										<div class="form-group m-t-25">
											<div class="col-sm-3"></div>
											<div class="col-sm-3">
												<button type="button" class="btn btn-danger btn-lg btn-block" id="iserror">
													账单有误
												</button>
											</div>
											<div class="col-sm-3">
												<button type="button" class="btn btn-primary btn-lg btn-block" id="finishbalance">
													<if condition="$balance['balancestatus'] eq 1">
														已打款，完成结算
													<else />
														已收到发票，打款完成结算。
													</if>
												</button>
											</div>
											<div class="col-sm-3"></div>
										</div>
									</form>										
								</if>

								<!-- 账单处于完成，编辑备注 -->
								<if condition="$balance['balancestatus'] eq 2">
									<div class="m-t-25 form-group">
                                    	<label class="control-label f-15" style="float:left;margin: 10px 10px 0 0;">备注：</label>
                                        <div class="col-sm-3" style="padding: 0px;">
                                        	<input type="text" class="form-control" name="edit-beizhu" id="edit-beizhu" placeholder="备注" autocomplete="off" value="<{$balance['beizhu']}>">
                                        </div>
                                        <div class="col-sm-3"> 
                                       		<button type="button" class="btn btn-primary" id="edit-beizhu-btn">保存</button>
                                       	</div>
                                    </div>
								</if>
                            </div>

							<!-- 列表 -->
                            <div class="p-20">
                                <div class="table-responsive">
                                    <table id="data-table-command" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
											<th data-column-id="balanceid" data-visible="false">账单编号</th>
                                            <th data-column-id="sourceid" data-visible="false">资源编号</th>
                                            <th data-column-id="gameid" data-visible="false">游戏编号</th>
                                            <th data-column-id="img" data-visible="false">游戏图标</th>
                                            <th data-column-id="gameicon" data-formatter="gameicon" data-visible="false">游戏图标</th>
                                            <th data-column-id="gamename" >游戏名</th>
											<th data-column-id="channelname">渠道名</th>
                                            <th data-column-id="channelrate">渠道费</th>
											<th data-column-id="sharerate">分成比例</th>
											<th data-column-id="sourcejournal">总流水</th>
                                            <th data-column-id="sourceincome">总收入</th>
                                            <th data-column-id="actualpaid">需打款金额</th>
                                            <th data-column-id="link" data-formatter="link" data-sortable="false">查看详情</th>

											<!-- 账单处于待审核，修改分成比例 -->
											<if condition = "$balance['balancestatus'] eq 1 and $modeifySharerateRight eq 'ok' ">
					                           	<th data-column-id="modify-sharerate" data-formatter="modify-sharerate" data-sortable="false">修改游戏分成比例</th>
					                        </if>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <foreach name="source" item="vo" key="k">
                                        <tr>
											<td><{$balance['id']}></td>
                                            <td><{$vo['sourceid']}></td>
                                            <td><{$vo['gameid']}></td>
                                            <td><{$ICONURL}><{$vo['gameicon']}></td>
                                            <td></td>
											<td><{$vo['gamename']}></td>
                                            <td><{$vo['channelname']}></td>
                                            <td><{$vo['channelrate']}></td>
											<td><{$vo['sharerate']}></td>
                                            <td><{$vo['sourcejournal']}></td>
                                            <td><{$vo['sourceincome']}></td>
                                            <td><{$vo['actualpaid']}></td>
                                            <td></td>
                                            <if condition = "$balance['balancestatus'] eq 1 and $modeifySharerateRight eq 'ok'">
                                            	<td>
                                            		
                                            	</td>
                                            </if>
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
		<!-- <button class="btn btn-float bgm-red m-btn" data-action="print"><i class="zmdi zmdi-print"></i></button> -->
    </section>
</section>

<style type="text/css">
	#window_block {
		height: 300px;
	}
	#window_block ul {
		margin: 30px 0 50px 0;
	}
	#window_block ul li {
		list-style: none;
		line-height: 30px;
		height: 30px;
		font-size: 13px;
	}
	#window_block ul li input {
		line-height: 25px;
		height: 25px;
	}	
</style>
<div id="window_block" class="wbox" >
    <h1>
        <em>修改游戏分成比例</em>
        <span class="wclose" title="关闭">x</span>
    </h1>
    <div class="overf">
        <ul>
        	<li>游戏名称：<span id="wbox-gamename"></span></li>
        	<li>分成比例：<input name="wbox-sharerate" id="wbox-sharerate" type="text" /></li>
        	<li>渠道费：<input name="wbox-channelrate" id="wbox-channelrate" type="text" /></li>
        </ul>
        
        <div>
			<div class="col-sm-2">
			</div>
			<div class="col-sm-8">
				<input type="hidden" name="wbox-gameid"/>
				<button type="button" class="btn btn-primary btn-lg btn-block wok">确认</button>
			</div>
			<div class="col-sm-2">
			</div>
		</div>
    </div>
</div>

<include file="Inc:footer" />
<include file="Inc:scripts" />

<script src="__ROOT__/plus/myjs/myjs.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/daterangepicker/daterangepicker.js"></script>
<script src="__ROOT__/plus/vendors/bootgrid/jquery.bootgrid.updated.js"></script>

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
                "gameicon": function(column, row) {
                    return "<img width=\"50\" height=\"50\" src=\""+row.img+"\">";
                },
				"payrateformat": function(column, row)
                {
                    return row.payrate+" %";
                },
				"link": function(column, row) {
                    return "<a href=\"/accountdetail/"+row.balanceid+"/"+row.sourceid+"/\">查看详情</a>";
                },
                "modify-sharerate": function(column, row) {
                	if ('<{$modeifySharerateRight}>' == 'ok') {
                   		return "<a href='javascript:;' onclick=\"modifysharerate('"+row.gameid+"','"+row.gamename+"','"+row.sharerate+"','"+row.channelrate+"');\">修改该游戏分成比例</a>";
                   	}
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p><button type=\"button\" class=\"btn btn-primary pull-right\" id=\"export\">导出结算单</</button>" +
                "</div>"
            }
        });

        $('#daterange').daterangepicker({
            format: 'YYYY-MM-DD',
            minDate: '2016-01-01',
            drops: 'down',
            buttonClasses: ['btn', 'btn-default'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-default',
            locale: moment.locale('zh-cn')
        });
		
		/*
        $('#viewdaterange').click(function() {
            var date = $('#daterange').val();
            if (date != "") {
                var start = date.substr(0, 10);
                var end = date.substr(-10, 10);
                $.ajax({
                    type : 'POST',
                    url : "index.php?m=balance&a=viewDaterangeBalance",
                    data : {startdate : start, enddate : end},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        console.log(data);
                        if (data.info == "success") {
                            $("#data-table-command").bootgrid("clear");
                            $("#data-table-command").bootgrid("append", data.data);
                            notify('数据获取成功', 'success');
                        } else {
                            $("#data-table-command").bootgrid("clear");
                            notify('数据获取失败，没有符合条件的数据', 'danger');
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
		*/

		// 账单是否有误
		var iserrorEle=$('#iserror');
		iserrorEle.click(function() {
			iserrorEle.attr("disabled","disabled");
			var beizhu = $('#beizhu').val();
			if(beizhu==''){
				alert('请填写备注');
				iserrorEle.removeAttr("disabled");
				return false;
			}
			var confirmcontent = "确认修改结算单有误？";
			if(confirm(confirmcontent)) {
				var balanceid = $('#hiddenbalanceid').val();

				iserrorEle.attr("disabled","disabled");
				$.ajax({
					type : 'POST',
					url : "index.php?m=balance&a=changeBalanceError",
					data : {id : balanceid, beizhu : beizhu},
					cache : false,
					dataType : 'json',
					success : function (data) {
						console.log(data);
						if (data.info == "success") {
							$('#modifybalance').hide();
							$('#paidamountdiv').removeClass("bgm-green").addClass("bgm-red");
							$('#paidamountcontent').html("尚未结算，账单有误");
							notify('账单信息更新成功', 'success');
						} else {
							notify(data.data, 'danger');
						}
						return false;
					},
					error : function (xhr) {
						notify('系统错误！', 'danger');
						return false;
					}
				});
			}else{
				iserrorEle.removeAttr("disabled");
			}
		});

		// 完成结算
		$('#finishbalance').click(function() {
			if($('#paidamount').length>0){
				var paidamount = $('#paidamount').val();	
			}else {
				var paidamount = $('#paid_youxiabi_amount').val()/10;
			}
            var beizhu = $('#beizhu').val();
			if (paidamount != "") {
				var balanceid = $('#hiddenbalanceid').val();
				var actualamount = $('#hiddenactualamount').val();
				var accounttype = $('#hiddenaccounttype').val();
				if (parseInt(paidamount) > parseInt(actualamount)) {
					var confirmcontent = "实际打款金额大于申请结算金额，确认已打款，完成结算？";
				} else if (parseInt(paidamount) < parseInt(actualamount) && beizhu=='') {
					alert('实际打款金额小于申请结算金额，请填写备注');
					return false;
				} else {
					var confirmcontent = "确认已打款，完成结算？";
				}

				if(confirm(confirmcontent)) {
					$('#finishbalance').attr("disabled","disabled");
					$.ajax({
						type : 'POST',
						url : "index.php?m=balance&a=finishBalance",
						data : {id : balanceid, paidamount : paidamount, beizhu : beizhu,accounttype:accounttype},
						cache : false,
						dataType : 'json',
						success : function (data) {
							console.log(data);
							if (data.info == "success") {
								$('#modifybalance').hide();
								if(accounttype==3){
									$('#paidamountcontent').html(paidamount*10);
								}else{
									$('#paidamountcontent').html("￥"+paidamount);
								}
								notify('结算单已完成', 'success');
							} else {
								notify(data.data, 'danger');
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
			} else {
				notify('请填写实际打款金额！', 'danger');
			}
		});

		// 对账单已出，请客户确认对账单并开具发票
		$('#checkbill').click(function() {
			var confirmcontent = "对账单已出，请客户确认对账单并开具发票？";
			var balanceid = $('#hiddenbalanceid').val();
			if(confirm(confirmcontent)) {
				$('#checkbill').attr("disabled","disabled");
				$.ajax({
					type : 'POST',
					url : "index.php?m=balance&a=checkBill",
					data : {id : balanceid},
					cache : false,
					dataType : 'json',
					success : function (data) {
						console.log(data);
						if (data.info == "success") {
							notify('对账单已出，请客户确认对账单并开具发票', 'success');
							location.href="__SELF__";
						} else {
							notify(data.data, 'danger');
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

		// 导出结算单的excel表
		$('#export').click(function() {
			var balanceid = $('#hiddenbalanceid').val();
			$.ajax({
				type : 'POST',
				url : "index.php?m=balance&a=export",
				data : {id : balanceid},
				cache : false,
				dataType : 'json',
				success : function (data) {
					console.log(data);
					if (data.info == "success") {
						notify('导出结算单成功', 'success');
						location.href = '__ROOT__/'+data.url;
					} else {
						notify(data.data, 'danger');
					}
					return false;
				},
				error : function (xhr) {
					notify('系统错误！', 'danger');
					return false;
				}
			});
		});

		// 编辑备注
		$('#edit-beizhu-btn').click(function() {
			var confirmcontent = "确定保存备注";
			if(confirm(confirmcontent)) {
				var balanceid = $('#hiddenbalanceid').val();
				var beizhu = $('#edit-beizhu').val();
				$.ajax({
					type : 'POST',
					url : "index.php?m=balance&a=editBeizhu",
					data : {balanceid : balanceid,beizhu:beizhu},
					cache : false,
					dataType : 'json',
					success : function (data) {
						console.log(data);
						if (data.info == "success") {
							notify('保存成功', 'success');
							$('#edit-beizhu').val(beizhu);
						} else {
							notify(data.data, 'danger');
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
    });

	//点击修改游戏分成比例
	function modifysharerate(gameid,gamename,sharerate,channelrate){
		$('#wbox-gamename').html(gamename);
		$('#wbox-sharerate').val(sharerate);
		$('#wbox-channelrate').val(channelrate);

		my_alert_window1({
			'window_str':'#window_block',
			'ok_fun':function(){
				var balanceid = $('#hiddenbalanceid').val();
				var newSharerate=$('#wbox-sharerate').val();
				var newChannelrate=$('#wbox-channelrate').val();
				$.ajax({
					type : 'POST',
					url : "index.php?m=balance&a=modifySharerate",
					data : {balanceid : balanceid,sharerate:sharerate,newSharerate:newSharerate,channelrate:channelrate,newChannelrate:newChannelrate,gameid:gameid},
					cache : false,
					dataType : 'json',
					success : function (data) {
						console.log(data);
						if (data.info == "success") {
							notify('保存成功', 'success');
						} else {
							notify(data.data, 'danger');
						}
						return false;
					},
					error : function (xhr) {
						notify('系统错误！', 'danger');
						return false;
					}
				});
			}
		})
	}
</script>
</body>
</html>