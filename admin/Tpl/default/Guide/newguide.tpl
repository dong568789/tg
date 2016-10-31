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
    //后台页面用$page_nav
    $page_nav["操作指南管理"]["active"] = true;
    $page_nav["操作指南管理"]["sub"]["新增操作指南"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>新增操作指南</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body">
                            <div class="p-20">
								<form id="addguide" class="form-horizontal" role="form" action="index.php?m=guide&a=addguide" method="post">
									<div class="form-group m-t-25">
										<label for="alipayaccount" class="col-sm-3 control-label f-15">操作指南类型</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<select class="selectpicker bs-select-hidden" name="category">
													<option>操作教程</option>
													<option>常见问题</option>
												</select>
											</div>

										</div>
									</div>

									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">标题</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="title" placeholder="如“流程及操作常见问题”">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">内容</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<div class="html-editor"></div>
												<input type="hidden" id="contenttext" name="contenttext" value="">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button type="submit" id="addguidesubmit" class="btn btn-primary btn-lg m-r-15">新增</button>
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
		var $addguide = $('#addguide').validate({
			rules : {
				title : {
					required : true
				}
			},

			messages : {
				title : {
					required : '请输入标题，此项目必填'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

        $('#addguidesubmit').click(function() {
            if ($(".note-editable").html() == "<p><br></p>") {
				notify("内容不能为空", 'danger');
				return false;
            } else {
				if ($('#addguide').valid()) {
					var confirmcontent = "确认新增该条操作指南？";
					if(confirm(confirmcontent)) {
						$("#contenttext").val($(".note-editable").html());
						$('#addguide').ajaxSubmit({  
							dataType : 'json',
							success : function (data) {
								if (data.data == "success") {
									notify(data.info, 'success');
									 setTimeout(function () {
									 location.href = '/newguide/';
									 }, 600);
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
    })
</script>
</body>
</html>