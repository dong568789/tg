<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "推广资源";
$page_css[] = "public/css/source.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";

?>
<include file="Inc:head" />
<style type="text/css">
	.modal-preview-demo {
		position: absolute;
		top: 200px;
		z-index: 100; 
	}
</style>
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $page_nav["推广资源"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>推广资源</h2>
            </div>

			<div class="clearfix modal-preview-demo">
				<div class="modal" id="downloadurl" style="display:none;"> <!-- Inline style just for preview --> 
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5 text-center">推广链接</h4>
							</div>
						    <div class="modal-body" id="downloadurldiv">
								
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal" id="downloadurl-modalclose">关闭</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix modal-preview-demo">
				<div class="modal" id="force" style="display:none;"> <!-- Inline style just for preview --> 
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title f-700 p-b-5 text-center" style="border-bottom:2px solid #ddd;">【有强更】下载游戏包</h4>
							</div>
							 <div class="modal-body">
								<form class="form-horizontal" role="form" >
									<div class="card-body card-padding">
										<div class="form-group">
											<label class="col-sm-5 control-label">强更时间</label>
											<div class="col-sm-7">
												<div class="fg-line m-t-5">
													<a class='' id="forcetime"></a>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-5 control-label">新的APK包</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<a class='btn btn-default btn-icon-text' href='#' id="newapkurl"><i class='zmdi zmdi-android'></i> 下载APK包</a>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-5 control-label">旧的APK包</label>
											<div class="col-sm-7">
												<div class="fg-line">
													<a class='btn btn-default btn-icon-text' href='#' id="oldapkurl"><i class='zmdi zmdi-android'></i> 下载APK包</a>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal" id="modalclose">关闭</button>
							</div>
						</div>
					</div>
				</div>
			</div>
            <div class="clearfix modal-preview-demo">
                <div class="modal" id="dowloadshow" style="display:none;"> <!-- Inline style just for preview -->
                    <div class="modal-dialog modal-sm" style="width: 660px;">
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
                                                    <a href="#" id="long_url" style="padding: 6px 12px;text-transform: Lowercase; display: inline-block;"></a>
                                                    <span class="btn" id="long_text" data-clipboard-action="copy" data-clipboard-target="#long_url">复制</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-5 control-label" style="width: auto;">短链接</label>
                                            <div class="col-sm-7" style="width: auto;padding:0px;">
                                                <div class="fg-line">
                                                    <a href='#' id="short_url" style="padding: 6px 12px;text-transform: Lowercase; display: inline-block;"></a>
                                                    <span class="btn" id="short_text" data-clipboard-action="copy" data-clipboard-target="#short_url">复制</span>
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
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <ul class="tab-nav" role="tablist">
                                <li role="presentation" class="active" >
                                    <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab" id="tabclick-1">
                                        全部推广
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a class="col-xs-6 f-15" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab" id="tabclick-2">
                                        我的推广
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content p-20">
                                <div role="tabpanel" class="tab-pane animated fadeIn in active" id="tab-1">
                                    <div class="btn-demo source m-l-25">
                                        <p class="gametype">
                                            <span>游戏类型</span>
                                            <span><a class="type classify" data-state="全部">全部</a></span>
                                            <span><a class="type" data-state="单机">单机</a></span>
                                            <span><a class="type" data-state="网游">网游</a></span>
                                        </p>
                                        <p class="gamecategory">
                                            <span>游戏分类</span>
                                            <span><a class="category classify" data-state="0">全部</a></span>
                                            <foreach name="category" item="vo" key="k">
                                                <span><a href="javascript:" class="category" data-id="<{$vo['id']}>" data-type='category' data-state=<{$vo['id']}> ><{$vo['categoryname']}></a></span>
                                            </foreach>
                                        </p>
                                        <p class="gamesize">
                                            <span>游戏大小</span>
                                            <span><a class="size classify" data-state="全部">全部</a></span>
                                            <span><a class="size" data-state="0-10M">0-10M</a></span>
                                            <span><a class="size" data-state="10-30M">10-30M</a></span>
                                            <span><a class="size" data-state="30-50M">30-50M</a></span>
                                            <span><a class="size" data-state="50-100M">50-100M</a></span>
                                            <span><a class="size" data-state="大于100M">大于100M</a></span>
                                        </p>
                                        <p class="gamelable">
                                            <span>游戏标签</span>
                                            <span><a class="tag classify" data-state="0">全部</a></span>
                                            <foreach name="tag" item="vo" key="k">
                                            <span><a href="javascript:" class="tag" data-state=<{$vo['id']}>><{$vo['tagname']}></a></span>
                                            </foreach>
                                        </p>
                                    </div>


                                    <div class="form-group m-t-25 m-b-25 m-l-25">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="selectChannel" class="control-label f-15 m-t-12">选择渠道</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="fg-float">
                                                            <div class="fg-line">
                                                                <select class="btn btn-default dropdown-menu f-14 channel-select" id="sourcechannel">
                                                                    <foreach name="channel" item="vo" key="k">
																		<option value="<{$vo['channelid']}>"><{$vo['channelname']}></option>
                                                                    </foreach>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                            </div>

                                            <div id="data-table-basic-header" class="bootgrid-header container-fluid col-sm-5">
                                                <div class="actionBar m-l-25">
                                                    <div class="search form-group col-sm-9">
                                                        <div class="input-group">
                                                            <span class="zmdi icon input-group-addon glyphicon-search"></span>
                                                            <input type="text" class="form-control search-content" placeholder="Search">
                                                        </div>
                                                    </div>
                                                    <div class="actions btn-group col-sm-2">
                                                        <div class="dropdown btn-group">
                                                            <a class="btn btn-default" href="javascript:void(0);" id="searchGame">搜索</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="loading" class="col-sm-12 text-center" style="display: none;">
                                        <img src="__ROOT__/plus/public/img/progress.gif" alt=""/>
                                        <p class="m-t-10">正在加载数据，请稍后</p>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="data-table-command-all" class="table table-hover table-bordered table-vmiddle">
                                            <thead>
                                            <tr>
                                                <th width="20%">游戏名称</th>
                                                <th width="10%">游戏分类</th>
												<th width="10%">游戏标签</th>
                                                <th width="10%">创建时间</th>
                                                <th width="10%" id="order-apply-hot">热度</th>
                                                <th width="10%">游戏包大小</th>
                                                <th width="10%">分成比例</th>
                                                <th width="15%" id="order-apply-status" data-order="asc">申请状态</th>
                                            </tr>
                                            </thead>
                                            <tbody id="gamecontainer">

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-center holder" id="sourceholder">
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane animated fadeIn in" id="tab-2">
                                    <div class="form-group m-t-25 m-b-25 m-l-25">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="channelselect" class="control-label f-15 m-t-12">选择渠道</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <div class="fg-float">
                                                            <div class="fg-line">
                                                                <select class="btn btn-default dropdown-menu f-14 channel-select" id="applychannel">
                                                                    <foreach name="channel" item="vo" key="k">
                                                                        <option value="<{$vo['channelid']}>"><{$vo['channelname']}></option>
                                                                    </foreach>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                            </div>

                                            <div id="data-table-basic-header" class="bootgrid-header container-fluid col-sm-5">
                                                <div class="actionBar m-l-25">
                                                    <div class="search form-group col-sm-9">
                                                        <div class="input-group">
                                                            <span class="zmdi icon input-group-addon glyphicon-search"></span>
                                                            <input type="text" class="form-control search-content" id="searchcontent" placeholder="Search">
                                                        </div>
                                                    </div>
                                                    <div class="actions btn-group col-sm-2">
                                                        <div class="dropdown btn-group">
                                                            <button class="btn btn-default" type="button">
                                                                <span class="dropdown-text searchMygame" id="searchMygame">搜索</span>
                                                                <span class="caret"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="data-table-command-my" class="table table-hover table-bordered table-vmiddle">
                                            <thead>
                                            <tr>
                                                <th width="17%">游戏名称</th>
                                                <th width="9%">游戏分类</th>
												<th width="9%">游戏标签</th>
												<th width="9%">热度</th>
                                                <th width="9%">游戏包大小</th>
                                                <th width="9%">分成比例</th>
                                                <th width="9%">子账号分成比例</th>
                                                <th width="26%">下载游戏包</th>
                                            </tr>
                                            </thead>
                                            <tbody id="sourcecontainer">
												<{$sourcestr}>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-center holder" id="applyholder">
                                    </div>
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

<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>
<script src="__ROOT__/plus/js/clipboard.min.js"></script>
<script type="text/javascript">
    //回车绑定事件
    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
            document.getElementById("searchGame").click();
            document.getElementById("searchMygame").click();
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
	
    //通过游戏类型筛选游戏
    function selectGame () {
        var type = $('.gametype').find('.classify').attr('data-state');
        var category = $(".category.classify").attr('data-state');
        var size = $(".size.classify").attr('data-state');
        var tag = $(".tag.classify").attr('data-state');
        var channelid = $("#sourcechannel").val();
        var order = $("#order-apply-status").attr('data-order');
        var orderHot = $("#order-apply-hot").attr('data-order');

        $.ajax({
            type: "POST",
            url: "/index.php?m=source&a=selectGame",
            data: {gametype:type, gamecategory:category, gamesize:size, gametag:tag, gamechannel:channelid,order:order,order_hot:orderHot},
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $(".table-responsive").hide();
                $("#data-table-basic-footer").hide();
                $("#loading").show();
            },
            success: function (data) {
                $("#loading").hide();
                $(".table-responsive").show();
                $("#data-table-basic-footer").show();
                if(data.data == "success") {
                    $("#gamecontainer").empty();
					$("#gamecontainer").append(data.info);
                    $("#sourceholder").jPages({
                        containerID    : "gamecontainer",
                        scrollBrowse   : false,
                        perPage: 20
                    });
                } else{
                    $("#sourceholder").jPages({
                        containerID    : "gamecontainer",
                        scrollBrowse   : false,
                        perPage: 20
                    });
					notify(data.info, 'danger');
                    $("#gamecontainer").empty();
                }
            },
			error : function (xhr) {
				notify('系统错误！', 'danger');
				return false;
			}
        });
    }
	
	var isdownloading = 0;

	//下载游戏包
	function downloadApk (sourcesn) {
		if (isdownloading == 1) {
			return false;
		}
		isdownloading = 1;
		$.ajax({
			type: "POST",
			url: "index.php?m=source&a=downloadapk",
			data: {source : sourcesn},
			cache: false,
			dataType: 'json',
			success: function (data) {
				if (data.data == "success") {
					isdownloading = 0;
					self.location.href = ""+data.info+"";
				} else if (data.data == "force") {
					isdownloading = 0;
					$("#newapkurl").attr("href", ""+data.info.newapkurl+"");
					$("#oldapkurl").attr("href", ""+data.info.oldapkurl+"");
					$("#forcetime").html(""+data.info.forcetime+"");
					$("#force").show();
				} else {
					notify(data.info, 'danger');
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

    //获取下载地址
    var urlData = [];
    function downloadUrl(sourcesn)
    {
        console.log(urlData);
        if(urlData[sourcesn]){
            showDownUrl(urlData[sourcesn]);
            return ;
        }
        $.ajax({
            type: "POST",
            url: "index.php?m=source&a=getGameDowUrl",
            data: {sourceid : sourcesn},
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == "1") {
                    showDownUrl(data);
                    urlData[sourcesn] = data;
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

    /**
     * 显示下载地址
     * @param data
     */
    function showDownUrl(data)
    {
        $('#dowloadshow').show();
        $('#long_url').html(data.long_url).attr('href',data.long_url);
        $('#short_url').html(data.short_url).attr('href',data.short_url);
    }

    //下载素材包
    function downloadTextture (sourcesn) {
        if (isdownloading == 1) {
            return false;
        }
        isdownloading = 1;
        $.ajax({
            type: "POST",
            url: "index.php?m=source&a=downloadTextture",
            data: {source : sourcesn},
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.data == "success") {
                    isdownloading = 0;
                    self.location.href = ""+data.info+"";
                } else {
                    notify(data.info, 'danger');
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

    var isdoingapply = 0;

    //筛选游戏
    $(document).ready(function() {
        selectGame ();

        var h = $(window).height();
        $('.modal-dialog').css("margin-top",(h-235)/2 + 'px');

        $('[data-dismiss=modal]').click(function(){
            $(this).parents('.modal').hide();
        });

		$("#modalclose").click(function() {
			$("#force").hide();
		});

		$("#downloadurl-modalclose").click(function() {
			$("#downloadurl").hide();
		});

        // 全部推广，筛选点击时候的效果
        $("span a").click(function(){
            $(this).parents('p').children().children().removeClass("classify");
            $(this).addClass("classify");
        });
        // 点击游戏类型
        $(".type").click(function() {
            selectGame ();
        });
        // 点击游戏分类
        $(".category").click(function() {
            selectGame ();
        });
        // 点击大小
        $(".size").click(function() {
            selectGame ();
        });
        // 点击游戏标签
        $(".tag").click(function() {
            selectGame ();
        });
        // 点击排序
        $("#order-apply-status").click(function() {
            var _this = $(this);
            var order = _this.attr('data-order');
            if(order=='asc'){
                _this.attr('data-order','desc');
                _this.html('申请状态 <span class="zmdi zmdi-caret-up-circle"></span>');
            }else{
                _this.attr('data-order','asc');
                _this.html('申请状态 <span class="zmdi zmdi-caret-down-circle"></span>');
            }
            $("#order-apply-hot").attr('data-order', '');
            $("#order-apply-hot").html('热度');
            selectGame ();
        });

        // 点击排序
        $("#order-apply-hot").click(function() {
            var _this = $(this);
            var order = _this.attr('data-order');
            if(order=='asc'){
                _this.attr('data-order','desc');
                _this.html('热度 <span class="zmdi zmdi-caret-down-circle"></span>');
            }else{
                _this.attr('data-order','asc');
                _this.html('热度 <span class="zmdi zmdi-caret-up-circle"></span>');
            }
            // alert($(this).attr('data-order'));
            selectGame ();
        });

        //申请部分
        $('body').on('click','.app-apply', function(){
            var thisbutton = $(this);
            var gameid = $(this).attr("data-gameid");
            var channelid = $("#sourcechannel").val();

            // 判断该用户是否有渠道
            var channel="<{$channel}>";
            if(!channel){
                swal({
                    title: "您当前没有渠道，是否新建渠道？",
                    showCancelButton: true,
                    confirmButtonColor: "#00ccff",
                    confirmButtonText: "确认",
                    cancelButtonText: "取消",
                    closeOnConfirm: false
                }, function(){
                    location.href = '/new_channel/';
                });
                return false;
            }

            swal({
                title: "确认申请本游戏的推广？",
                text: "申请完成后，您可以在我的推广中查看您所申请到的游戏资源。",
                showCancelButton: true,
                confirmButtonColor: "#00ccff",
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                closeOnConfirm: false
            }, function(){
				if (isdoingapply == 0) {
					$.ajax({
						type: "POST",
						url: "/index.php?m=source&a=applyGame",
						data: {game:gameid,channel:channelid},
						cache: false,
						dataType: 'json',
						success: function (data) {
							if (data.data == "success") {
								swal({title:"已申请", text:"您已完成对本游戏的推广申请。", type: "success"}, function(){
									thisbutton.removeClass("btn-primary");
									thisbutton.addClass("Gray");
									thisbutton.html("已申请");
									thisbutton.attr("disabled","disabled");
								});
								$("#sourcecontainer").append(data.info);
								isdoingapply = 0;
							} else {
								notify(data.info, 'danger');
								isdoingapply = 0;
							}
							return false;
						},
						error : function (xhr, status) {
							alert("系统错误");
							isdoingapply = 0;
							return false;
						}
					});
				}
				isdoingapply = 1;
            });
        });

        // 全部资源 搜索游戏
		$("#searchGame").click(function(){
            var searchContent = $(".search-content").val();
            var searchchannelid = $("#sourcechannel").val();
            $.ajax({
                type: "POST",
                url: "/index.php?m=source&a=searchGame",
                data: {content : searchContent, channelid : searchchannelid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if(data.data == "success") {
                        $("#gamecontainer").empty();
                        $("#gamecontainer").append(data.info);
                        $("#sourceholder").jPages({
                            containerID    : "gamecontainer",
                            scrollBrowse   : false,
                            perPage: 20
                        });
                    } else{
                        notify(data.info, 'danger');
                        $("#gamecontainer").empty();
                    }
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });

        // 显示分页
        $("#sourceholder").jPages({
            containerID    : "gamecontainer",
            scrollBrowse   : false,
            perPage: 20
        });

        //TAB1选择渠道部分
        $("#sourcechannel").change(function(){
			var typevalue = $(".type.classify").attr('data-state');
            var categoryvalue = $(".category.classify").attr('data-state');
            var sizevalue = $(".size.classify").attr('data-state');
            var tagvalue = $(".tag.classify").attr('data-state');
            var channelid = $(this).val();
            selectGame (typevalue, categoryvalue, sizevalue, tagvalue, channelid);
        });

        //下拉框区分大小写
        $(".btn").css("text-transform","none");
		//滚动条
		$(".table-responsive").css("overflow-x","visible");
        $(".search-content").attr("placeholder","请输入游戏名称")

        var longObj = new Clipboard('#long_text');

        longObj.on('success', function(e) {
            notify('复制成功', 'success');
        });

        longObj.on('error', function(e) {
            notify('复制失败', 'danger');
        });

        var shortObj = new Clipboard('#short_text');

        shortObj.on('success', function(e) {
            notify('复制成功', 'success');
        });

        shortObj.on('error', function(e) {
            notify('复制失败', 'danger');
        });
    })

    // 我的推广
    $(function (argument) {
        $('#tabclick-2').click(function(){
            var thischannelid = $("#applychannel").val();
            $.ajax({
                type: "POST",
                url: "/index.php?m=source&a=selectSource",
                data: {channelid : thischannelid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if(data.data == "success") {
                        $("#sourcecontainer").empty();
                        $("#sourcecontainer").append(data.info);
                        $("#applyholder").jPages({
                            containerID    : "sourcecontainer",
                            scrollBrowse   : false,
                            perPage: 20
                        });
                    } else{
                        notify(data.info, 'danger');
                        $("#sourcecontainer").empty();
                    }
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });

        $(".searchMygame").click(function(){
            var searchContent = $("#searchcontent").val();
            var searchchannelid = $("#applychannel").val();
            $.ajax({
                type: "POST",
                url: "/index.php?m=source&a=searchMygame",
                data: {content : searchContent, channelid : searchchannelid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if(data.data == "success") {
                        $("#sourcecontainer").empty();
                        $("#sourcecontainer").append(data.info);
                        $("#applyholder").jPages({
                            containerID    : "sourcecontainer",
                            scrollBrowse   : false,
                            perPage: 20
                        });
                    } else{
                        notify(data.info, 'danger');
                        $("#sourcecontainer").empty();
                    }
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });

        //TAB2选择渠道部分
        $("#applychannel").change(function(){
            var thischannelid = $(this).val();
            $.ajax({
                type: "POST",
                url: "/index.php?m=source&a=selectSource",
                data: {channelid : thischannelid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    if(data.data == "success") {
                        $("#sourcecontainer").empty();
                        $("#sourcecontainer").append(data.info);
                    } else{
                        notify(data.info, 'danger');
                        $("#sourcecontainer").empty();
                    }
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        })
    })
</script>
</body>
</html>