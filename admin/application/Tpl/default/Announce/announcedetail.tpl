<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台公告管理";
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
    $page_nav["公告管理"]["active"] = true;
    $page_nav["公告管理"]["sub"]["所有公告"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>公告详情</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body p-b-25">
                            <div class="p-20">
								<form id="editannounce" class="form-horizontal" role="form" action="index.php?m=announce&a=editannounce" method="post">
									<div class="form-group m-t-25">
										<label for="alipayaccount" class="col-sm-3 control-label f-15">公告类型</label>
										<div class="col-sm-7">
											<div class="fg-float">
												<div class="fg-line">
													<select class="selectpicker bs-select-hidden" name="category" value="<{$announce['category']}>">
														<option <if condition="$announce['category'] eq 系统公告"> selected </if>>系统公告</option>
														<option <if condition="$announce['category'] eq 结算公告"> selected </if>>结算公告</option>
														<option <if condition="$announce['category'] eq 产品公告"> selected </if>>产品公告</option>
														<option <if condition="$announce['category'] eq 更新公告"> selected </if>>更新公告</option>
														<option <if condition="$announce['category'] eq 其他公告"> selected </if>>其他公告</option>
													</select>

												</div>
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">公告标题</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="title" value="<{$announce['title']}>">
												<input type="hidden" id="announceid" name="announceid" value="<{$announce['id']}>">
											</div>
										</div>
									</div>
									<div class="form-group m-t-25">
                                        <label class="col-sm-3 control-label f-15">排序</label>
                                        <div class="col-sm-7">
                                            <div class="fg-line">
                                                <input type="text" class="form-control" placeholder="排序越大在前面，默认为0" id="orderid" name="orderid" value="<{$announce['orderid']}>">
                                            </div>
                                        </div>
                                    </div>
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">公告内容</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<div class="html-editor"><{$announce['content']}></div>
												<input type="hidden" id="contenttext" name="contenttext" value="">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button id="editannouncesubmit" type="submit" class="btn btn-primary btn-lg m-r-15">保存</button>
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

    $(document).ready(function(){
		var $editannounce = $('#editannounce').validate({
			rules : {
				title : {
					required : true
				},
				orderid : {
					digits : true
				}
			},

			messages : {
				title : {
					required : '标题不得为空'
				},
				orderid : {
					digits : '排序必须为数字'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

		$('#editannouncesubmit').click(function() {
			if ($(".note-editable").html() == "<p><br></p>") {
				notify("内容不能为空", 'danger');
				return false;
            } else {
				if ($('#editannounce').valid()) {
					var confirmcontent = "确认修改这条公告？";
					if(confirm(confirmcontent)) {
						$("#contenttext").val($(".note-editable").html());
						$('#editannounce').ajaxSubmit({  
							dataType : 'json',    
							success : function (data) {
								if (data.data == "success") {
									notify(data.info, 'success');
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
				return false;
			}
		});
    });
</script>
</body>
</html>