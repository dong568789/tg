<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "后台公告中心";
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
    $page_nav["公告管理"]["active"] = true;
    $page_nav["公告管理"]["sub"]["所有公告"]["active"] = true;
	?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>所有公告</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <div class="p-20">
								<div class="table-responsive">
                                    <table id="data-table-basic" class="table table-hover table-vmiddle">
                                        <thead>
                                        <tr>
											<th data-column-id="id" data-type="numeric" data-order="desc">序号</th>
                                            <th data-column-id="category">公告类型</th>
                                            <th data-column-id="title">公告标题</th>
                                            <th data-column-id="createtime">创建日期</th>
                                            <th data-column-id="orderid">排序</th>
                                            <th data-column-id="operation" data-formatter="link">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="statisticcontainer">
                                        <foreach name="announce" item="vo" key="k">
                                            <tr>
                                                <td><{$vo['id']}></td>
                                                <td><{$vo['category']}></td>
                                                <td><{$vo['title']}></td>
                                                <td><{$vo['createtime']}></td>
                                                <td><{$vo['orderid']}></td>
                                                <td></td>
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
    </section>
</section>

<include file="Inc:footer" />
<include file="Inc:scripts" />

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

	function deleteAnnounce (announceid) {
		var confirmcontent = "确认删除该条公告？";
		if(confirm(confirmcontent)) {
			$.ajax({    
				type : 'POST',        
				url : "index.php?m=announce&a=deleteAnnounce", 
				data : {id : announceid},
				cache : false,    
				dataType : 'json',    
				success : function (data) {
					if (data.data == "success") {
						notify(data.info, 'success');
						$('#delete-'+announceid).parent().parent().addClass("display-none");
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
				"link": function(column, row)
				{
					return "<a href=\"/announcedetail/"+row.id+"/\">编辑</a>&nbsp;|&nbsp;<a href=\"#\" id=\"delete-"+row.id+"\" onclick=\"deleteAnnounce("+row.id+");\">删除</a>";
				}
			},
			templates: {
				header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p>" +
				"<div class=\"daterange form-group\"><div class=\"input-group\"><span class=\"zmdi input-group-addon zmdi-calendar\"></span><input type=\"text\" class=\"search-field form-control\" placeholder=\"请选择日期\" name=\"daterange\" id=\"daterange\" readonly=\"true\"><a id=\"viewdaterange\" class=\"input-group-addon btn-info\">查看</a></div></div>" +
				"<a class=\"{{css.addnewuser}}\" href=\"/newannounce/\">新增一条公告</a></div></div></div>"
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

		$('#viewdaterange').click(function() {
			var date = $('#daterange').val();
			if (date != "") {
				var start = date.substr(0, 10);
				var end = date.substr(-10, 10);
				$.ajax({    
					type : 'POST',        
					url : "index.php?m=announce&a=viewDaterangeAnnounce", 
					data : {startdate : start, enddate : end},
					cache : false,    
					dataType : 'json',    
					success : function (data) {
						if (data.info == "success") {
							$("#data-table-basic").bootgrid("clear");
							$("#data-table-basic").bootgrid("append", data.data);
							notify('数据获取成功', 'success');
						} else {
							$("#data-table-basic").bootgrid("clear");
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
    });
</script>
</body>
</html>