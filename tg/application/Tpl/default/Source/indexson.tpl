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
                <h2>我的推广</h2>
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
                                            <div class="col-sm-7"  style="width: auto;padding:0px; ">
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
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="applychannel" value="<{$userchannelid}>" /> 
                            <div role="tabpanel" class="tab-pane animated fadeIn in" id="tab-2">
                                <div class="form-group m-t-25 m-b-25 m-l-25">
                                    <div class="row">
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
                                    <table id="data-table-command-my" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th width="20%">游戏名称</th>
                                            <th width="9%">游戏分类</th>
                                            <th width="9%">游戏标签</th>
                                            <th width="9%">热度</th>
                                            <th width="9%">游戏包大小</th>
                                            <th width="9%">分成比例</th>
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
    </section>
</section>

<include file="Inc:footer" />
<include file="Inc:scripts" />

<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>
<script src="__ROOT__/plus/clipboard/dist/clipboard.min.js"></script>

<script type="text/javascript">
    //回车绑定事件
    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
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
    function selectGame (type, category, size, tag, channelid) {
        $.ajax({
            type: "POST",
            url: "/index.php?m=source&a=selectGame",
            data: {gametype:type, gamecategory:category, gamesize:size, gametag:tag, gamechannel:channelid},
            cache: false,
            dataType: 'json',
            success: function (data) {
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

    var urlData = [];
    function downloadUrl(sourcesn)
    {
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

    //筛选游戏
    $(document).ready(function() {

        var h = $(window).height();
        $('.modal-dialog').css("margin-top",(h-235)/2 + 'px');
		$("#modalclose").click(function() {
			$("#force").hide();
		});

        $('[data-dismiss=modal]').click(function(){
            $(this).parents('.modal').hide();
        });

		$("#downloadurl-modalclose").click(function() {
			$("#downloadurl").hide();
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

        $("#applyholder").jPages({
            containerID    : "sourcecontainer",
            scrollBrowse   : false,
            perPage: 20
        });

		//滚动条
		$(".table-responsive").css("overflow-x","visible");
        $(".search-content").attr("placeholder","请输入游戏名称");

    });
</script>
</body>
</html>