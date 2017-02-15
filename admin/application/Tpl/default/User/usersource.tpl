<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "用户所有资源";
$page_css[] = "vendors/bower_components/daterangepicker/daterangepicker-bs3.css";
$page_css[] = "vendors/bootgrid/jquery.bootgrid.css";
?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["用户管理"]["active"] = true;
    $page_nav["用户管理"]["sub"]["所有用户"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>所有用户</h2>
            </div>


            <div class="clearfix modal-preview-demo">
                <div class="modal" id="dowloadshow" style="display:none;"> <!-- Inline style just for preview -->
                    <div class="modal-dialog modal-sm" style="width: 610px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title f-700 p-b-5 text-center" style="border-bottom:2px solid #ddd;">APK下载链接</h4>
                            </div>
                            <div class="modal-body">
                                <form class="form-horizontal" role="form" >
                                    <div class="card-body card-padding">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" style="width: auto;">长链接</label>
                                            <div class="col-sm-7"  style="width: auto;padding:0px;">
                                                <div class="fg-line">
                                                    <a href='#' id="long_url" style="padding: 6px 12px;text-transform: Lowercase; display: inline-block;"></a>
                                                    <!--<span class="btn" onclick="copyUrl2('long_url')">复制</span>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label" style="width: auto;">短链接</label>
                                            <div class="col-sm-7" style="width: auto;padding:0px;">
                                                <div class="fg-line">
                                                    <a href='#' id="short_url" style="padding: 6px 12px;text-transform: Lowercase; display: inline-block;"></a>
                                                    <!--<span class="btn" onclick="copyUrl2('short_url')">复制</span>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-link" data-dismiss="modal" >关闭</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header ch-alt text-center">
                            <img class="i-logo" src="__ROOT__/plus/img/demo/invoice-logo.png" alt="">
                        </div>
                        <div class="card-body">
							

							<div class="p-20" >
								<div class="row" >
									<div class="col-xs-6">
										<div class="text-right">
											<h4><{$user['account']}></h4>
											<span class="text-muted f-14">
												<{$user['contactmobile']}>
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
											<h4><{$user['realname']}></h4>
											<span class="text-muted f-14">
												<{$user['contactemail']}>
											</span>
											<br>
											<span class="text-muted f-14">
												<if condition = "$user['invoicetype'] eq 1">
													开普通发票(税点 6.72%)
												<elseif condition = "$user['invoicetype'] eq 2" />
													开3%增值税发票(税点 3%)
												<elseif condition = "$user['invoicetype'] eq 3" />
													开6%增值税发票(税点 6%)
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
                            </div>

                            <div id="data-table-basic-header" class="bootgrid-header container-fluid m-b-0">
                                <div class="actionBar">
                                    <select class="btn btn-default dropdown-menu f-14 m-r-10" id="channelselect">
                                        <{$channelstr}>
                                    </select>

                                    <div class="search form-group col-sm-9 m-0 p-l-0">
                                        <div class="input-group">
                                            <span class="zmdi icon input-group-addon glyphicon-search"></span>
                                            <input type="text" class="form-control search-content" id="game-search" placeholder="输入游戏搜索">
                                        </div>
                                    </div>
                                    <div class="actions btn-group">
                                        <div class="dropdown btn-group">
                                            <a class="btn btn-default" href="javascript:void(0);" id="game-search-btn">搜索</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-20" style="padding-top: 0px;min-height: 100px;">
                            	<div id="loading" class="col-sm-12 text-center" style="display: none;">
                                    <img src="__ROOT__/plus/img/progress.gif" alt=""/>
                                    <p class="m-t-10">正在加载数据，请稍后</p>
                                </div>

                                <div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
										<input type="hidden" name="hiddenuserid" id="hiddenuserid" value="<{$user['userid']}>">
										<input type="hidden" name="hiddenchannelstr" id="hiddenchannelstr" value="<{$channelstr}>">
                                        <thead>
                                        <tr>
											<th data-column-id="sourceid" data-visible="false">资源编号</th>
                                            <th data-column-id="img" data-visible="false">游戏图标</th>
                                            <th data-column-id="gameiconstr" data-formatter="gameicon">游戏图标</th>
                                            <th data-column-id="gamename" >游戏名</th>
											<th data-column-id="channelname">渠道名</th>
											<th data-column-id="sourcesn">资源编码</th>
											<th data-column-id="isfixrate">费率固定</th>
                                            <th data-column-id="sourcesharerate">资源分成比例</th>
											<th data-column-id="sourcechannelrate">资源渠道费</th>
											<th data-column-id="sub_share_rate">子账号资源分成比例</th>
											<th data-column-id="sub_channel_rate">子账号资源渠道费</th>
											<if condition="$customRateRight eq 'ok'">
												<th data-column-id="userratestr" data-sortable="false">自定义资源费率</th>
											</if>
                                            <if condition="$downloadApkRight eq 'ok'">
												<th data-column-id="downloadstr" data-sortable="false">立即下载游戏分包</th>
											</if>
											<if condition="$seeDevelopRight eq 'ok'">
												<th data-column-id="developstr" data-sortable="false">查看推广</th>
											</if>
                                        </tr>
                                        </thead>
                                        <tbody id="sourcecontainer">
                                        <!-- <foreach name="source" item="vo" key="k">
											<tr>
												<td><{$vo['id']}></td>
												<td><{$ICONURL}><{$vo['gameicon']}></td>
												<td></td>
												<td><{$vo['gamename']}></td>
												<td><{$vo['channelname']}></td>
												<td><{$vo['sourcesn']}></td>
												<td>
													<if condition="$vo['isfixrate'] eq 0 ">
														否
													<else />
														是
													</if>
												</td>
												<td><{$vo['sourcesharerate']}></td>
												<td><{$vo['sourcechannelrate']}></td>
												<td><{$vo['sub_share_rate']}></td>
												<td><{$vo['sub_channel_rate']}></td>
												<if condition="$customRateRight eq 'ok'">	<td></td> </if>
												<if condition="$downloadApkRight eq 'ok'">	<td></td> </if>
												<if condition="$seeDevelopRight eq 'ok'"> <td></td>	</if>
                                            </tr>
                                        </foreach> -->
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

<include file="Inc:footer" />
<include file="Inc:scripts" />

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

    // 搜索
    function search_page () {
        var channelid = $('#channelselect').val();
		var userid = $("#hiddenuserid").val();
		var game = $("#game-search").val();

        $.ajax({
            type: "POST",
            url: "index.php?m=user&a=getUserSource",
            data: {channelid:channelid,userid:userid,game:game},
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $(".table-responsive").hide();
                $("#data-table-basic-footer").hide();
                $("#loading").show();
            },
            success: function (data) {
                console.log(data);
                $("#loading").hide();
                $(".table-responsive").show();
                $("#data-table-basic-footer").show();
                $("#data-table-basic").bootgrid("clear");
                if (data.info == "success") {
                    $("#data-table-basic").bootgrid("append", data.data);

                } else {
                    notify('没有符合条件的数据', 'danger');
                }
                return false;
            },
            error : function (xhr) {
                notify('系统错误！', 'danger');
                return false;
            }
        });
    }

    function downloadUrl(sourcesn)
    {
        $.ajax({
            type: "POST",
            url: "index.php?m=user&a=getGameDowUrl",
            data: {sourceid : sourcesn},
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == "1") {
                    $('#dowloadshow').show();
                    $('#long_url').html(data.long_url).attr('href',data.long_url);
                    $('#short_url').html(data.short_url).attr('href',data.short_url);
                } else {
                    notify('获取下载地址失败', 'danger');
                    isdownloading = 0;
                }
                return false;
            },
            error : function (xhr, status) {
                alert("系统错误");
                isdownloading = 0;
                return false;
            }
        });
    }
	
    $(document).ready(function() {
		//Basic Example
        $("#data-table-basic").bootgrid({
            css: {
                icon: 'zmdi',
                iconColumns: 'zmdi-menu',
                iconDown: 'zmdi-caret-down-circle',
                iconRefresh: 'zmdi-refresh',
                iconUp: 'zmdi-caret-up-circle'
            },
			formatters: {
				// "gameicon": function(column, row) {
				// 	return "<img width=\"50\" height=\"50\" src=\""+row.img+"\">";
				// },
				// "link": function(column, row) {
				// 	if ('<{$customRateRight}>' == 'ok') {
    //                     return "<a href=\"/userrate/"+row.sourceid+"/\">自定义资源费率</a>";
    //                 }
				// },
				// "download": function(column, row) {
				// 	if ('<{$downloadApkRight}>' == 'ok') {
    //                     return "<a href=\"index.php?m=user&a=downloadapk&source="+row.sourcesn+"\">立即下载游戏分包</a>";
    //                 }
				// },
				// "develop": function(column, row) {
				// 	if ('<{$seeDevelopRight}>' == 'ok') {
    //                     var userid=$('#hiddenuserid').val();
				// 		var userid=$('#hiddenuserid').val();
				// 		return "<a href=\"/material/"+row.sourceid+"/\">查看推广</a>";
    //                 }
				// }
			},
			templates: {
				header: ""
			}
        });

        var h = $(window).height();
        $('.modal-dialog').css("margin-top",(h-235)/2 + 'px');

        $('[data-dismiss=modal]').click(function(){
            $(this).parents('.modal').hide();
        });

        search_page();

		$('#game-search').keydown(function(e){
            if(e.keyCode==13){
               search_page();
            }
        });
        $('#game-search-btn').click(function (argument) {
            search_page();
        })

		$("#channelselect").change(function() {
			search_page();
		});
    })
</script>

</body>
</html>