<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台游戏标签管理";
$page_css[] = "";
?>

<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //功能页面用$page_nav
    $page_nav["其他项目管理"]["active"] = true;
    $page_nav["其他项目管理"]["sub"]["游戏标签"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>游戏标签管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
						<div class="p-20">
							<div class="card-header">
							</div>

							<div class="table-responsive">
								<table class="table table-responsive table-hover">
									<thead>
										<tr>
											<th>序号</th>
											<th>游戏标签</th>
											<th>创建时间</th>
											<th>创建者</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody id="tagcontainer">
										<foreach name="gametag" item="vo" key="k">
										<tr>
											<td><{$vo['id']}></td>
											<td><{$vo['tagname']}></td>
											<td><{$vo['createtime']}></td>
											<td>admin</td>
											<td><a id="delete-<{$vo['id']}>" href="#" onclick="deleteGametag(<{$vo['id']}>);"> 删除</a></td>
										</tr>
										</foreach>
									</tbody>
								</table>
							</div>

							<div class="p-20">
								<form id="addgametag" class="form-horizontal" role="form" action="index.php?m=other&a=addgametag" method="post">
									<div class="form-group m-t-25">
										<label class="col-sm-3 control-label f-15">游戏标签名字</label>
										<div class="col-sm-7">
											<div class="fg-line">
												<input type="text" class="form-control" name="tagname" id="tagname" placeholder="如“热门游戏”">
											</div>
										</div>
									</div>

									<div class="form-group m-t-25">
										<div class="col-sm-12 text-center">
											<button type="submit" id="addsubmit" class="btn btn-primary btn-lg m-r-15">新增</button>
											<a href="/gametag/" class="btn btn-default btn-lg c-gray">取消</a>
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

	function deleteGametag (tagid) {
		var confirmcontent = "确认删除该条游戏标签？";
		if(confirm(confirmcontent)) {
			$.ajax({    
				type : 'POST',        
				url : "index.php?m=other&a=deletegametag", 
				data : {id : tagid},
				cache : false,    
				dataType : 'json',    
				success : function (data) {
					if (data.data == "success") {
						notify(data.info, 'success');
						$('#delete-'+tagid).parent().parent().addClass("display-none");
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

    $(document).ready(function(){
		var $addgametag = $('#addgametag').validate({
			rules : {
				tagname : {
					required : true
				}
			},

			messages : {
				tagname : {
					required : '游戏类别不能为空'
				}
			},

			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});

        $('#addsubmit').click(function() {
            if ($('#addgametag').valid()) {
				var confirmcontent = "确认新增这条游戏标签？";
				if(confirm(confirmcontent)) {
					$('#addgametag').ajaxSubmit({  
						dataType : 'json',    
						success : function (data) {
							if (data.data == "success") {
								var tagname = $('#tagname').val();
								$('#tagcontainer').append("<tr><td>"+data.info+"</td><td>"+tagname+"</td><td>刚才</td><td>Admin</td><td><a id=\"delete-"+data.info+"\" href=\"#\" onclick=\"deleteGametag("+data.info+");\"> 删除</a></td></tr>");
								$('#tagname').val("");
								notify("新增游戏标签成功。", 'success');
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
        });
    })
</script>
</body>
</html>