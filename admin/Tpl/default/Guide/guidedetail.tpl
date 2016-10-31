<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台操作指南管理";
$page_css[] = "vendors/summernote/dist/summernote.css";
$page_css[] = "vendors/farbtastic/farbtastic.css";
?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["操作指南管理"]["active"] = true;
    $page_nav["操作指南管理"]["sub"]["操作指南详情"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>操作指南详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card p-b-25">
                        <div class="card-header">
						</div>
                        <div class="card-body">
                            <div class="row p-b-25">
                                <div class="col-sm-3 m-t-25">
                                    <div class="row">
                                        <div class="col-sm-2">
                                        </div>
                                        <div class="col-sm-10">
                                            <ul class="main-menu">
                                                <li class="sub-menu toggled">
                                                    <a href=""><i class="zmdi zmdi-info"></i> 操作教程</a>
                                                    <ul style="display: block;" class="teach">
                                                        <foreach name="operation" item="vo" key="k">
                                                            <li>
																<a onclick="getContent(<{$vo['id']}>);" class="guidetitle guidetitle-<{$vo['id']}>"><{$vo['title']}></a>
                                                            </li>
                                                        </foreach>
                                                    </ul>
                                                </li>
                                                <li class="sub-menu">
                                                    <a href=""><i class="zmdi zmdi-help"></i> 常见问题</a>
                                                    <ul style="display: block;" class="question">
                                                        <foreach name="question" item="vo" key="k">
                                                            <li><a onclick="getContent(<{$vo['id']}>);" class="guidetitle guidetitle-<{$vo['id']}>"><{$vo['title']}></a>
                                                            </li>
                                                        </foreach>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="card">
										<div class="card-header">
										</div>
                                        <div class="card-body card-padding">
                                            <div class="m-b-10">
                                                <button class="btn btn-primary btn-sm hec-button">编辑</button>
                                                <button type="submit" id="saveguide" class="btn btn-success btn-sm hec-save" style="display:none;">保存</button>
                                                <a id="deleteguide" class="btn btn-danger btn-sm pull-right">删除</a>
                                            </div>
                                            <div class="html-editor-click">
                                            </div>
                                            <input type="hidden" id="contenttext" name="contenttext" value="">
											<input type="hidden" id="hiddenguideid" name="hiddenguideid" value="">
                                            <br/>
                                            <br/>
                                        </div>
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

<script src="__ROOT__/plus/vendors/summernote/dist/summernote-updated.js"></script>
<script src="__ROOT__/plus/vendors/farbtastic/farbtastic.min.js"></script>

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

	function getContent (guideid) {
		$('#hiddenguideid').val(guideid);
		$.ajax({    
			type : 'POST',        
			url : "index.php?m=guide&a=getcontent", 
			data : {id : guideid},
			cache : false,    
			dataType : 'json',    
			success : function (data) {
				if (data.info == "success") {
					$('.html-editor-click').html(data.data);
					$('.guidetitle').removeClass("c-blue");
					$('.guidetitle-'+guideid).addClass("c-blue");
					notify("内容获取成功。", 'success');
				} else {
					notify("内容获取失败。", 'danger');
				}
				return false;
			},
			error : function (xhr) {
				notify('系统错误！', 'danger');
				return false;
			}
		});
	}


    $(document).ready(function() {
        $('#saveguide').click(function() {
			var guideid = $("#hiddenguideid").val();
            var content = $(".note-editable").html();
            $.ajax({    
				type : 'POST',        
				url : "index.php?m=guide&a=editguide", 
				data : {id : guideid, contenttext : content},
				cache : false,    
				dataType : 'json',    
				success : function (data) {
					if (data.info == "success") {
						notify("修改成功。", 'success');
					} else {
						notify("修改失败。", 'danger');
					}
					return false;
				},
				error : function (xhr) {
					notify('系统错误！', 'danger');
					return false;
				}
			});
        });

		$('#deleteguide').click(function() {
			var guideid = $("#hiddenguideid").val();
			var confirmcontent = "确认删除该条操作指南？";
			if(confirm(confirmcontent)) {
				$.ajax({    
					type : 'POST',        
					url : "index.php?m=guide&a=deleteguide", 
					data : {id : guideid},
					cache : false,    
					dataType : 'json',    
					success : function (data) {
						if (data.info == "success") {
							$('.guidetitle-'+guideid).parent().addClass("display-none");
							$('.html-editor-click').html("");
							notify("删除成功。", 'success');
						} else {
							notify("删除失败。", 'danger');
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
    })
</script>
</body>
</html>