<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "新增游戏";
	$page_css[] = "vendors/bower_components/chosen/chosen.min.css";
	$page_css[] = "vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css";
?>
<include file="Inc:head" />
<style>
	.error{	
		color: red;
	}
</style>
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
	<include file="Inc:sidemenuconfig" />
    <?php
		//后台页面用$page_nav
		$page_nav["游戏管理"]["active"] = true;
		$page_nav["游戏管理"]["sub"]["新增游戏"]["active"] = true;
    ?>
	<include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>新增游戏</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
						</div>
                        <div class="card-body">
                            <div class="p-20">
                                <form id="addgame" class="form-horizontal" enctype="multipart/form-data" role="form" action="index.php?m=cgame&a=addgame" method="post" >
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏名称</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="gamename" id="gamename" placeholder="游戏名称" value="">
											</div>
										</div>
									</div>
                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 f-15 control-label">游戏类型</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gametype" value="单机" checked="true">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    单机
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gametype" value="网游">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    网游
                                                </label>
                                            </div>
                                        </div>
                                    </div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">游戏分类</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<foreach name="gamecategory" item="vo" key="k">
													<label class="radio radio-inline m-r-20">
														<input class="radioclass" type="radio" name="gamecategory" value="<{$vo['id']}>">
														<i class="input-helper p-relative" style="left:-26px;"></i>
														<{$vo['categoryname']}>
													</label>
												</foreach>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">游戏标签</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<select class="selectpicker" name="gametag" id="gametag">
													<option value="0">请选择一个游戏标签</option>
													<foreach name="gametag" item="vo" key="k">
														<option value="<{$vo['id']}>"><{$vo['tagname']}></option>
													</foreach>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">游戏评分</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="score" id="score" placeholder="游戏评分：1-10" value="<{$game['score']}>">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">游戏权重</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="gameauthority" id="gameauthority" placeholder="游戏的综合评分，为1-5之间的小数，比如4.32，数字越大排名越靠前">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">对外分成比例</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="sharerate" id="sharerate" placeholder="以1为100%，比如分成比例40%，此处填写0.4">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">接入分成比例</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="joinsharerate" id="joinsharerate" placeholder="以1为100%，比如分成比例40%，此处填写0.4">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">开放范围</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<foreach name="sourceType" item="vo" key="k">
													<label class="checkbox radio-inline m-r-20">
														<input type="checkbox" name="guard[]" value="<{$k}>">
														<i class="input-helper p-relative" style="left:-26px;"></i>
														<{$vo}>
													</label>
												</foreach>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">备注信息</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="beizhumessage" id="beizhumessage" placeholder="例如“对外合作分成不得高于60%”">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">通道费</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="channelrate" id="channelrate" placeholder="以1为100%，比如通道费6%，此处填写0.06">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">发布日期</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<div class="input-group form-group">
													<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
													<div class="col-sm-12" style="padding-left:0px;">
														<div class="dtp-container fg-line">
															<input type="text" class="form-control date-picker" name="publishtime" id="publishtime" placeholder="选择游戏发布日期">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">厂商</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<select class="selectpicker" name="publisher">
													<foreach name="publisher" item="vo" key="k">
														<option value="<{$vo['id']}>"><{$vo['name']}></option>
													</foreach>
												</select>
											</div>
										</div>
										<div class="col-sm-2">
										</div>
									</div>
									<div class="form-group m-t-5">
                                        <label class="col-sm-3 f-15 control-label">上架状态</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                            	<label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="-1">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    待上架
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="0" >
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    正常
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="1">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    未上架
                                                </label>

												<label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="2">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    已下架
                                                </label>
                                            </div>
                                        </div>
                                    </div>

									<div class="form-group m-t-5">
                                        <label class="col-sm-3 f-15 control-label">是否能使用代金券</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isusedvoucher" value="0">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    否
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isusedvoucher" value="1" checked="true">
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    是
                                                </label>
                                            </div>
                                        </div>
                                    </div>


									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">上传素材包</label>
										<div class="col-sm-7 p-t-5">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<span class="btn btn-info btn-file m-r-10">
													<span class="fileinput-new">选择文件</span>
													<span class="fileinput-exists">更改</span>
													<input type="file" name="texturepackage" id="texturepackage">
												</span>
												<span class="fileinput-filename"></span>
												<a href="#" class="close fileinput-exists" data-dismiss="fileinput">&times;</a>
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
                                        <label class="col-sm-3 f-15 control-label">游戏图标</label>
                                        <div class="col-sm-7 p-t-5">
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="gameicon" id="gameicon" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 f-15 control-label">游戏背景</label>
                                        <div class="col-sm-7 p-t-5">
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="gamebg" id="gamebg" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 f-15 control-label">游戏截图</label>
                                        <div class="col-sm-7 p-t-5">
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="screenshot1" id="screenshot1" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>

                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="screenshot2" id="screenshot2" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>

                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="screenshot3" id="screenshot3" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>

                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="screenshot4" id="screenshot4" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>

                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
                                                    <div>
                                                        <span class="btn btn-info btn-file">
                                                            <span class="fileinput-new">选择一张图片</span>
                                                            <span class="fileinput-exists">更改</span>
                                                            <input type="file" name="screenshot5" id="screenshot5" class="radioclass">
                                                        </span>
                                                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">移除</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏详情</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<textarea class="form-control" name="description" id="description"></textarea>
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button id="addgamesubmit" type="button" class="btn btn-primary btn-lg m-r-15">新增游戏</button>
											<a href="javascript:window.history.back(-1)" class="btn btn-default btn-lg c-gray">取消</a>
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

<script src="__ROOT__/plus/vendors/bower_components/chosen/chosen.jquery.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="__ROOT__/plus/vendors/fileinput/fileinput.min.js"></script>
<script src="__ROOT__/plus/vendors/layer/layer.js"></script>
<script src="__ROOT__/plus/js/guard.js"></script>


<script type="text/javascript">
	var T1;
	var setTimeoutFlag = 0;

	function checkTimeout (issuccessed) {
		if (issuccessed == 0) {
			T1 = setTimeout( function () {
				swal({
					title: "超时",   
					text: "系统超时，请重新上传。", 
					type: "error",
					showConfirmButton: true
				});
				return false;
			}, 30000);
		} else {
			var isexist = isset(T1);
			if (isexist) {
				clearTimeout(T1);
				return false;
			}
		}
	}

	function isset(variable) {
		if ((typeof (variable) === 'undefined') || (variable === null)) {
			return false;
		} else {
			return true;
		}
	}

    $(document).ready(function(){
		jQuery.validator.addMethod("checkENGsmall", function(value, element) {
			var reg = /^[a-z](\s*[a-z])*$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母！");

		jQuery.validator.addMethod("checkVERSION", function(value, element) {
			var reg =  /^[0-9a-zA-Z._]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入英文小写字母或数字或下划线或点！");

		jQuery.validator.addMethod("checkCHN", function(value, element) {
			var reg =  /^[\u4e00-\u9fa5]+$/;
			return this.optional(element) || reg.test(value);
		}, "请输入正确的汉字！");

		$('#sdkgameid').change(function(){
			var gameid = $('#sdkgameid').val();
			var gamename = $('#hiddengamename-'+gameid).val();
			var gamepinyin = $('#hiddengamepinyin-'+gameid).val();
			$('#gamename').val(gamename);
			$('#gamepinyin').val(gamepinyin);
		});

		$('#addgamesubmit').click(function() {
			var $addgame = $('#addgame').validate({
				ignore : ':hidden:not(.selectpicker, .chosen)',
				rules : {
					gameid : {
						min : 1
					},
					gamename : {
						required : true
					},
					// gamepinyin : {
					// 	required : true
					// },
					// gamecategory : {
					// 	required : true
					// },
					// gametag : {
					// 	min : 1
					// },
					// score : {
					// 	required : true,
					// 	number : true,
					// 	min : 0,
					// 	max : 10
					// },
					// gameauthority : {
					// 	required : true,
					// 	number : true,
					// 	min : 1,
					// 	max : 5
					// },
					// sharerate : {
					// 	required : true,
					// 	number : true,
					// 	min : 0,
					// 	max : 1
					// },
					// channelrate : {
					// 	required : true,
					// 	number : true,
					// 	min : 0,
					// 	max : 1
					// },
					gameicon : {
						required : true
					},
					// gamebg : {
					// 	required : true
					// },
					// description : {
					// 	required : true
					// },
					// gametype : {
					// 	required : true
					// },
					isonstack : {
						required : true
					}
				},

				messages : {
					gameid : {
						min : '请选择一个SDK游戏'
					},
					gamename : {
						required : '请输入游戏名称'
					},
					// gamepinyin : {
					// 	required : '请输入游戏拼音'
					// },
					// gamecategory : {
					// 	required : '请选择一个游戏分类'
					// },
					// gametag : {
					// 	min : '请选择一个游戏标签'
					// },
					// score : {
					// 	required : '请选择一个游戏评分',
					// 	number : '游戏评分必须为数字',
					// 	min : '游戏评分最小为0',
					// 	max : '游戏评分最大为10'
					// },
					// gameauthority : {
					// 	required : '请填写游戏权重，此项必填',
					// 	number : '游戏权重必须为数字',
					// 	min : '游戏权重最大为1',
					// 	max : '游戏权重最大为5'
					// },
					// sharerate : {
					// 	required : '请填写分成比例，此项必填',
					// 	number : '分成比例必须为数字',
					// 	min : '分成比例必须大于0',
					// 	max : '分成比例最大为1(100%)'
					// },
					// channelrate : {
					// 	required : '请填写通道费，此项必填',
					// 	number : '通道费必须为数字',
					// 	min : '分成比例必须大于0',
					// 	max : '通道费最大为1(100%)'
					// },
					gameicon : {
						required : '游戏图标必须上传'
					},
					// gamebg : {
					// 	required : '游戏背景必须上传'
					// },
					// description : {
					// 	required : '请填写游戏详情，此项必填'
					// },
					// gametype : {
					// 	required : '请选择一个游戏类型'
					// },
					isonstack : {
						required : '请选择一个上架状态'
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

			if ($('#addgame').valid()) {
				var confirmcontent = "确认新增这个游戏？";
				if(confirm(confirmcontent)) {
					swal({
						title: "请稍侯...",   
						text: "文件正在上传中，请等待完成，不要关闭页面", 
						type: "hold",
						showConfirmButton: false
					});
					$('#addgame').ajaxSubmit({  
						dataType : 'json',
						cache : false,
						beforeSend : function() {		
							//do nothing
						},
						uploadProgress : function(event, position, total, percentComplete) {
							var processVal = percentComplete + '%';
							$('.sweet-alert').children().eq(6).html("完成进度："+processVal);
							if (percentComplete == 100) {
								if (setTimeoutFlag == 0) {
									checkTimeout (0);
									setTimeoutFlag = 1;
								} else {
									return false;
								}
							}
						},
						success : function (data) {
							checkTimeout (1);
							setTimeoutFlag = 0;
							if (data.data == "success") {
								swal({
									title: "已上传",   
									text: data.info, 
									type: "success",
									showConfirmButton: true
								});
								// $('#addgame').resetForm(); 
								location.href="<{:U('gameall')}>";
							} else {
								swal({
									title: "上传失败",   
									text: data.info, 
									type: "error",
									showConfirmButton: true
								});
							}
							return false;
						},
						error : function (xhr, status) {
							checkTimeout (1);
							setTimeoutFlag = 0;
							swal({
								title: "系统错误",   
								text: "页面错误编号 "+xhr.status, 
								type: "error",
								showConfirmButton: true
							});
							return false;
						},
						// clearForm : true
					});
				} else {
					return false;
				}
			}
			return false;
        });

		$('#J_foragent').guard({
			'from_table':'tg_game',
			'data_append':'#addgame'
		});

		$('#pub_switch_tg').click(function () {
			var _this = $(this);
			var _val = _this.prop('checked');
			if( _val){
				$('#J_foragent_box').show();
			}else{
				$('#J_foragent_box').hide();
			}
		});
    });
</script>
</body>
</html>