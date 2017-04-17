<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "游戏详情";
	$page_css[] = "vendors/bower_components/chosen/chosen.min.css";
	$page_css[] = "vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css";
?>
<include file="Inc:head" />
<body>
<style>
	.fileinput .fileinput-preview img {
		margin-top: 0px;
	}
	.fileinput-preview{line-height:150px;}
</style>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
	<include file="Inc:sidemenuconfig" />
    <?php
		//后台页面用$page_nav
		$page_nav["游戏管理"]["active"] = true;
		$page_nav["游戏管理"]["sub"]["所有游戏"]["active"] = true;
    ?>
	<include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>游戏详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
						</div>
                        <div class="card-body">
                            <div class="p-20">
								<form id="editgame" class="form-horizontal" enctype="multipart/form-data" role="form" action="index.php?m=game&a=editgame" method="post" >
									<input type="hidden" id="gameid" name="gameid" value="<{$game['gameid']}>">
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏名称</label>
										<div class="col-sm-7">
											<div class="fg-line m-t-8">
												<a class="f-15"><{$game['gamename']}></a>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏拼音</label>
										<div class="col-sm-7">
											<div class="fg-line m-t-5">
												<a class="f-15"><{$game['gamepinyin']}></a>
											</div>
										</div>
									</div>
									<!--
                                    <div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">对应的SDK游戏</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<select class="chosen" name="sdkgameid" id="sdkgameid" data-placeholder="请选择对应的SDK游戏，输入框可进行搜索">
													<option value="0">请选择对应的SDK游戏，输入框可进行搜索</option>
													<foreach name="sdkgamelist" item="vo" key="k">
														<option value="<{$vo['id']}>" <if condition="$vo['id'] eq $game['sdkgameid'] ">selected="true"</if>><{$vo['initial']}> <{$vo['name']}></option>
													</foreach>
												</select>
												<foreach name="sdkgamelist" item="vo" key="k">
													<input type="hidden" id="hiddengamename-<{$vo['id']}>" value="<{$vo['name']}>">
													<input type="hidden" id="hiddengamepinyin-<{$vo['id']}>" value="<{$vo['initial']}>">
												</foreach>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏名称</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="gamename" id="gamename" placeholder="游戏名称(选择SDK后自动完成)" value="<{$game['gamename']}>">
												<input type="hidden" id="gameid" name="gameid" value="<{$game['gameid']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏拼音</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="gamepinyin" id="gamepinyin" placeholder="游戏拼音(选择SDK后自动完成)" value="<{$game['gamepinyin']}>">
											</div>
										</div>
									</div>
									-->
                                    <div class="form-group m-t-25">
                                        <label class="col-sm-3 f-15 control-label">游戏类型</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gametype" value="单机" <if condition="'单机' eq $game['gametype'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    单机
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="gametype" value="网游" <if condition="'网游' eq $game['gametype'] ">checked="true"</if>>
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
														<input class="radioclass" type="radio" name="gamecategory" value="<{$vo['id']}>" <if condition="$vo['id'] eq $game['gamecategory'] ">checked="true"</if>>
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
														<option value="<{$vo['id']}>" <if condition="$vo['id'] eq $game['gametag'] ">selected="true"</if>><{$vo['tagname']}></option>
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
												<input type="text" class="form-control" name="gameauthority" id="gameauthority" placeholder="游戏的综合评分，为1-5之间的小数，比如4.32，数字越大排名越靠前" value="<{$game['gameauthority']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">对外分成比例</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="sharerate" id="sharerate" placeholder="以1为100%，比如分成比例40%，此处填写0.4" value="<{$game['sharerate']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">接入分成比例</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="joinsharerate" id="joinsharerate" placeholder="以1为100%，比如分成比例40%，此处填写0.4" value="<{$game['joinsharerate']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">开放范围</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<foreach name="sourceType" item="vo" key="k">
													<label class="checkbox radio-inline m-r-20">
														<input type="checkbox" name="guard[]" value="<{$k}>" <if condition="in_array($k,$game['guardArr'])">checked="checked"</if>>
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
												<input type="text" class="form-control" name="beizhumessage" id="beizhumessage" placeholder="例如“对外合作分成不得高于60%”" value="<{$game['beizhumessage']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">通道费</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="channelrate" id="channelrate" placeholder="以1为100%，比如通道费6%，此处填写0.06" value="<{$game['channelrate']}>">
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
															<input type="text" class="form-control date-picker" name="publishtime" id="publishtime" placeholder="选择游戏发布日期" value="<{$game['publishtime']}>">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group m-t-5">
                                        <label class="col-sm-3 f-15 control-label">上架状态</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                            	<label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="-1" <if condition="-1 eq $game['isonstack'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    待上架
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="0" <if condition="0 eq $game['isonstack'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    正常
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="1" <if condition="1 eq $game['isonstack'] ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    未上架
                                                </label>

												<label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isonstack" value="2" <if condition="2 eq $game['isonstack'] ">checked="true"</if>>
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
                                                    <input class="radioclass" type="radio" name="isusedvoucher" value="0"  <if condition="$game['isusedvoucher'] eq 0 ">checked="true"</if>>
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    否
                                                </label>

                                                <label class="radio radio-inline m-r-20">
                                                    <input class="radioclass" type="radio" name="isusedvoucher" value="1"  <if condition="$game['isusedvoucher'] eq 1 ">checked="true"</if> >
                                                    <i class="input-helper p-relative" style="left:-26px;"></i>
                                                    是
                                                </label>
                                            </div>
                                        </div>
                                    </div>
									

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button id="editgamesubmit" type="button" class="btn btn-primary btn-lg m-r-15">保存</button>
											<a href="javascript:window.history.back(-1)" class="btn btn-default btn-lg c-gray">取消</a>
										</div>
									</div>  
                                </form>
                            </div>
                        </div>
                    </div>

					<div class="card">
                        <div class="card-header">
						</div>
                        <div class="card-body">
							<div class="p-20">
								<form id="uploadpackage" class="form-horizontal" enctype="multipart/form-data" role="form" action="<{:U('game/uploadpackage')}>" method="post" >
									<input type="hidden" id="uploadgameid" name="uploadgameid" value="<{$game['gameid']}>">
									<input type="hidden" id="isconfirmed" name="isconfirmed" value="0">
									<div class="form-group">
										<label class="col-sm-3 f-15 control-label">上传游戏包</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<span class="btn btn-info btn-file m-r-10">
													<span class="fileinput-new">选择文件</span>
													<span class="fileinput-exists">更改</span>
													<input type="file" name="gamepackage" id="gamepackage">
												</span>
												<span class="fileinput-filename"></span>
												<a href="#" class="close fileinput-exists" data-dismiss="fileinput">&times;</a>
											</div>
											<div class="fileinput fileinput-new m-t-5 m-l-25" style="vertical-align:middle;">
												<label class="checkbox f-15 m-l-10">
													<input name="isforcepackage"  id="isforcepackage" type="checkbox" value="1">
													<i class="input-helper p-relative" style="left:-26px;"></i>    
													强更包
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 f-15 control-label">更换素材包</label>
										<div class="col-sm-9 p-t-5">
											<div class="fileinput fileinput-new">
												<span><if condition="$game['texturename'] neq '' "><a class="f-15" href="<{$TEXTUREDOWNLOADURL}><{$game['texturename']}>"><{$game['texturename']}></a><else />未上传素材包</if></span>
											</div>
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
									<div class="form-group">
                                        <label class="col-sm-3 f-15 control-label">更换游戏图标</label>
                                        <div class="col-sm-9 p-t-5">
											<div class="fileinput fileinput-new">
												<span><if condition="$game['gameicon'] neq '' "><a class="f-15" href="<{$ICONURL}><{$game['gameicon']}>"><{$game['gameicon']}></a><else />未上传游戏图标</if></span>
											</div>
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><img src="<{$ICONURL}><{$game['gameicon']}>" /></div>
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
                                    <div class="form-group">
                                        <label class="col-sm-3 f-15 control-label">更换游戏背景</label>
                                        <div class="col-sm-9 p-t-5">
											<div class="fileinput fileinput-new">
												<span><if condition="$game['gamebg'] neq '' "><a class="f-15" href="<{$GAMEBGURL}><{$game['gamebg']}>"><{$game['gamebg']}></a><else />未上传游戏背景</if></span>
											</div>
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['gamebg'] neq '' "><img class="lazy" data-original="<{$GAMEBGURL}><{$game['gamebg']}>" /></if></div>
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
                                    <div class="form-group">
                                        <label class="col-sm-3 f-15 control-label">更换游戏截图</label>
                                        <div class="col-sm-9 p-t-5">
											<div class="fileinput fileinput-new">
												<div><if condition="$game['screenshot1'] neq '' "><a class="f-15" href="<{$SCREEMSHOTURL}><{$game['screenshot1']}>"><{$game['screenshot1']}></a><else />未上传游戏截图1</if></div>
												<div><if condition="$game['screenshot2'] neq '' "><a class="f-15" href="<{$SCREEMSHOTURL}><{$game['screenshot2']}>"><{$game['screenshot2']}></a><else />未上传游戏截图2</if></div>
												<div><if condition="$game['screenshot3'] neq '' "><a class="f-15" href="<{$SCREEMSHOTURL}><{$game['screenshot3']}>"><{$game['screenshot3']}></a><else />未上传游戏截图3</if></div>
												<div><if condition="$game['screenshot4'] neq '' "><a class="f-15" href="<{$SCREEMSHOTURL}><{$game['screenshot4']}>"><{$game['screenshot4']}></a><else />未上传游戏截图4</if></div>
												<div><if condition="$game['screenshot5'] neq '' "><a class="f-15" href="<{$SCREEMSHOTURL}><{$game['screenshot5']}>"><{$game['screenshot5']}></a><else />未上传游戏截图5</if></div>
											</div>
                                            <div class="fg-line">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['screenshot1'] neq '' "><img class="lazy" data-original="<{$SCREEMSHOTURL}><{$game['screenshot1']}>" /></if></div>
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
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['screenshot2'] neq '' "><img class="lazy" data-original="<{$SCREEMSHOTURL}><{$game['screenshot2']}>" /></if></div>
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
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['screenshot3'] neq '' "><img class="lazy" data-original="<{$SCREEMSHOTURL}><{$game['screenshot3']}>" /></if></div>
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
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['screenshot4'] neq '' "><img class="lazy" data-original="<{$SCREEMSHOTURL}><{$game['screenshot4']}>" /></if></div>
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
                                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput"><if condition="$game['screenshot5'] neq '' "><img class="lazy" data-original="<{$SCREEMSHOTURL}><{$game['screenshot5']}>" /></if></div>
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
												<textarea class="form-control" name="description" id="description"><{$game['description']}></textarea>
											</div>
										</div>
									</div>
                                    
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">游戏包查看</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<select class="selectpicker" name="packagelist" id="packagelist" data-clipboard-action="copy" data-clipboard-target="#packagelist">
													<foreach name="packagelist" item="vo" key="k">
														<option value="<{$vo['packageid']}>" <if condition="$vo['isnowactive'] eq 1 ">selected="true"</if>><{$vo['gamename']}> - <{$vo['viewname']}> - <{$vo['gamesize']}>MB-母包<{$vo['packagename']}>
														<if condition="$vo['isnowactive'] eq 1 ">
															【当前包】
														<else/>
															【旧包】
														</if>
														<if condition="$vo['isforcepackage'] eq 1 ">
															【强更包】
															<if condition="$vo['isforced'] eq 1 ">
																【已完成强更】
															<else/>
																【未到强更时间】
															</if>
														</if>
														</option>
													</foreach>
												</select>
											</div>
										</div>
										<div class="col-sm-2">
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">强更历史版本号</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="historyversion" id="historyversion" placeholder="需要强更的游戏包历史版本号" value="<{$versionstr}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 f-15 control-label">强更日期</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<div class="input-group form-group">
													<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
													<div class="col-sm-12" style="padding-left:0px;">
														<div class="dtp-container fg-line">
															<input type="text" class="form-control date-time-picker" name="forcetime" id="forcetime" placeholder="请选择强更日期时间" value="<?php if ($latestpackage['forcetime'] != "") {echo date("Y-m-d H:i",strtotime($latestpackage['forcetime']));}?>">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 f-15 control-label">最新版本号</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="latestversion" id="latestversion" placeholder="最新版本号，请选择游戏包" value="<{$latestpackage['gameversion']}>" disabled="disabled">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button type="button" class="btn btn-warning btn-lg m-r-10" id="forceupdatesubmit">继续生成强更渠道包</button>
											<button type="button" class="btn btn-primary btn-lg" id="uploadpackagesubmit">登陆包信息</button>
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
<script src="__ROOT__/plus/vendors/jquery-lazyload/jquery.lazyload.js"></script>
<script src="__ROOT__/plus/js/clipboard.min.js"></script>
<script type="text/javascript">
	var gameid = $('#uploadgameid').val();

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

	function createForcePackage(isFristTime, nowID) {
		var isfirst = isFristTime;
		var now = nowID;
		if (parseInt(isfirst) == 1) {
			swal({
				title: "请稍侯...",   
				text: "渠道正在分包中，请等待完成，不要关闭页面", 
				type: "hold",
				showConfirmButton: false
			});
			$.ajax({
				type: "POST",
				url: "index.php?m=game&a=createForcePackage",
				data: {gameid : gameid, now : now},
				cache: false,
				dataType: 'json',
				success: function (data) {
					if (data.data == "success") {
						$('.lead').html("已完成资源 "+data.info+" 的分包");
						createForcePackage(0, data.info);
					} else if (data.data == "finish") {
						swal({
							title: "生成渠道包完成",   
							text: data.info, 
							type: "success",
							showConfirmButton: true
						});
					} else {
						swal({
							title: "失败",   
							text: data.info, 
							type: "error",
							showConfirmButton: true
						});
					}
					return false;
				},
				error : function (xhr) {
					alert('系统错误！');
					return false;
				}
			});
		} else {
			$.ajax({
				type: "POST",
				url: "index.php?m=game&a=createForcePackage",
				data: {gameid : gameid, now : now},
				cache: false,
				dataType: 'json',
				success: function (data) {
					if (data.data == "success") {
						$('.lead').html("已完成资源 "+data.info+" 的分包");
						createForcePackage(0, data.info);
					} else if (data.data == "finish") {
						swal({
							title: "生成渠道包完成",   
							text: data.info, 
							type: "success",
							showConfirmButton: true
						});
					} else {
						swal({
							title: "失败",   
							text: data.info, 
							type: "error",
							showConfirmButton: true
						});
					}
					return false;
				},
				error : function (xhr) {
					alert('系统错误！');
					return false;
				}
			});
		}
	}

    $(document).ready(function(){
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

		jQuery.validator.addMethod("checkVERSION", function(value, element) {
			var reg =  /^[0-9.,]+$/;
			return this.optional(element) || reg.test(value);
		}, "版本号只能为数字或点或逗号！");

		$('#sdkgameid').change(function(){
			var gameid = $('#sdkgameid').val();
			var gamename = $('#hiddengamename-'+gameid).val();
			var gamepinyin = $('#hiddengamepinyin-'+gameid).val();
			$('#gamename').val(gamename);
			$('#gamepinyin').val(gamepinyin);
		});

		var $editgame = $('#editgame').validate({
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

		// 编辑游戏时 基本资料提交
		$('#editgamesubmit').click(function() {
			if ($('#editgame').valid()) {
				var confirmcontent = "确认修改这个游戏信息？";
				if(confirm(confirmcontent)) {
					swal({
						title: "请稍侯...",   
						text: "文件正在上传中，请等待完成，不要关闭页面", 
						type: "hold",
						showConfirmButton: false
					});
					$('#editgame').ajaxSubmit({  
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
									},function(){
										swal("好", "", "success");
										//setTimeout(function () {
										//	location.href = '/gameall/';
										//}, 600);
								});
								
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
						clearForm : false
					});
				} else {
					return false;
				}
			}
			return false;
        });

		var $uploadpackage = $('#uploadpackage').validate({
			ignore : ':hidden:not(.selectpicker, .chosen)',
			rules : {
				historyversion : {
					checkVERSION : true
				}
			},

			messages : {
				historyversion : {
					checkVERSION : '版本号只能为数字或点或逗号'
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

		// 修改包信息提交
		$('#uploadpackagesubmit').click(function() {
			if ($('#uploadpackage').valid()) {
				var confirmcontent = "确认上传游戏包？";
				if(confirm(confirmcontent)) {
					swal({
						title: "请稍侯...",   
						text: "文件正在上传中，请等待完成，不要关闭页面", 
						type: "hold",
						showConfirmButton: false
					});
					$('#uploadpackage').ajaxSubmit({  
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
									text: "游戏包已成功上传，如果要使用新上传的包，请刷新页面.", 
									type: "success",
									showConfirmButton: true
									},function(){
										swal("好", "", "success");
								});
							} else if (data.data == "warning") {
								swal({
									title: "确认覆盖",   
									text: "已存在相同版本的游戏包，确认覆盖？", 
									type: "warning",
									showConfirmButton: true,
									showCancelButton: true,
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "是的,覆盖!",   
									cancelButtonText: "取消",   
									closeOnConfirm: false,   
									closeOnCancel: false 
									}, function(isConfirm){   
										if (isConfirm) {
											$('#isconfirmed').val(1);
											var packageid = data.info;
											$.ajax({
												type: "POST",
												url: "index.php?m=game&a=coverPackage",
												data: {packageid : packageid},
												cache: false,
												dataType: 'json',
												success: function (data) {
													if (data.data == "success") {
														swal({
															title: "已激活",   
															text: "已成功上传并激活游戏包，非强更场景所有原有渠道包都被清空，使用新渠道包，强更场景将保留老渠道包至强更，同时可以使用新渠道包.", 
															type: "success",
															showConfirmButton: true
															},function(){
																swal("好", "", "success");
														});
													} else {
														swal({
															title: "上传游戏包失败",   
															text: data.info, 
															type: "error",
															showConfirmButton: true
														});
													}
													return false;
												},
												error : function (xhr) {
													alert('系统错误！');
													return false;
												}
											});
										} else {     
											swal("已取消", "上传游戏包已取消", "error");   
										} 
								});
							} else if (data.data == "force") {
								swal({
									title: "确认开始生成强更渠道包",   
									text: "强更包和信息上传成功，即将开始生成渠道包，请确认.", 
									type: "warning",
									showConfirmButton: true,
									showCancelButton: true,
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "好的!",   
									cancelButtonText: "取消",   
									closeOnConfirm: false,   
									closeOnCancel: false 
									}, function(isConfirm){   
										if (isConfirm) {
											createForcePackage(1, 0);
										} else {     
											swal("已取消", "生成强更渠道包已取消，您可以点击“继续生成强更渠道包”按钮来继续生成强更渠道包.", "error");   
										} 
								});
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
						clearForm : false
					});
				} else {
					return false;
				}
			}	
        });

		// 强更包信息提交
		$("#forceupdatesubmit").click(function(){
			var packageid = $("#packagelist option:selected").val();
			var confirmcontent = "确认继续生成选中的游戏包的强更渠道包？";
			if(confirm(confirmcontent)) {
				$.ajax({
					type: "POST",
					url: "index.php?m=game&a=forceUpdateCheck",
					data: {packageid : packageid},
					cache: false,
					dataType: 'json',
					success: function (data) {
						if (data.data == "success") {
							createForcePackage(1, 0);
						}else{
							swal({
								title: "生成游戏渠道包失败",   
								text: data.info, 
								type: "error",
								showConfirmButton: true
							});
						}
						return false;
					},
					error : function (xhr) {
						alert('系统错误！');
						return false;
					}
				});
			} else {
				return false;
			}
			
        });

		$("img.lazy").lazyload({
			placeholder : "__ROOT__/plus/img/progress.gif", //用图片提前占位
			// placeholder,值为某一图片路径.此图片用来占据将要加载的图片的位置,待图片加载时,占位图则会隐藏
			effect: "fadeIn", // 载入使用何种效果
		});


		var longObj = new Clipboard('#packagelist:selected');

		longObj.on('success', function(e) {
			notify('复制成功', 'success');
		});

		longObj.on('error', function(e) {
			notify('复制失败', 'danger');
		});
    });
</script>
</body>
</html>