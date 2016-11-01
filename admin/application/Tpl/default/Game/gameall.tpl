<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "所有游戏";
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
    $page_nav["游戏管理"]["active"] = true;
    $page_nav["游戏管理"]["sub"]["所有游戏"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />

    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>所有游戏</h2>
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
                                            <th data-column-id="gamename">游戏名称</th>
                                            <th data-column-id="packageversion">游戏拼音</th>
                                            <th data-column-id="gamepinyin">包名</th>
                                            <th data-column-id="gametype">游戏类型</th>
											<th data-column-id="gamecategory">游戏分类</th>
											<th data-column-id="gametag">游戏标签</th>
											<th data-column-id="gamesize">游戏大小</th>
											<!-- <th data-column-id="sharetype">分成模式</th> -->
											<th data-column-id="gameinfo">权重/分成/渠道</th>
											<th data-column-id="isonstack" data-formatter="stackinfo">是否上架</th>
                                            <th data-column-id="operation" data-formatter="link">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="gamecontainer">
                                        <foreach name="gamelist" item="vo" key="k">
                                            <tr>
                                                <td><{$vo['gameid']}></td>
												<td><{$vo['gamename']}></td>
                                                <td><{$vo['gamepinyin']}></td>
												<td><{$vo['packageversion']}></td>
												<td><{$vo['gametype']}></td>
												<td><{$vo['categoryname']}></td>
												<td><{$vo['tagname']}></td>
												<td><{$vo['gamesize']}> MB</td>
												<!-- <td><{$vo['sharetype']}></td> -->
												<td><{$vo['gameauthority']}>/<{$vo['sharerate']}>/<{$vo['channelrate']}></td>
												<td><{$vo['isonstack']}></td>
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

	function deleteGame (id) {
		var confirmcontent = "确认删除该游戏？";
		if(confirm(confirmcontent)) {
			$.ajax({    
				type : 'POST',        
				url : "index.php?m=game&a=deleteGame", 
				data : {gameid : id},
				cache : false,    
				dataType : 'json',    
				success : function (data) {
					if (data.data == "success") {
						notify(data.info, 'success');
						$('#delete-'+id).parent().parent().addClass("display-none");
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
                    if ('<{$editgame}>' == 'ok' && '<{$deletegame}>' == 'ok'){
                        return "<a href=\"/gamedetail/"+row.id+"/\">编辑</a>&nbsp;|&nbsp;<a href=\"#\" id=\"delete-"+row.id+"\" onclick=\"deleteGame("+row.id+");\">删除</a>";

                    } else if('<{$editgame}>' == 'ok' && '<{$deletegame}>' != 'ok'){
                        return "<a href=\"/gamedetail/"+row.id+"/\">编辑</a>";

                     }else if('<{$editgame}>' != 'ok' && '<{$deletegame}>' == 'ok'){
                        return "<a href=\"#\" id=\"delete-"+row.id+"\" onclick=\"deleteGame("+row.id+");\">删除</a>";

                    }else{
                        return "没有操作权限";
                    }
				},
				"stackinfo": function(column, row)
				{
					return row.isonstack == "0" ? "正常" : (row.isonstack == "1" ? "未上架" : "已下架");
				}
			},
			templates: {
				header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p><a class=\"{{css.addnewuser}}\" href=\"/newgame/\">新增一个游戏</a></div></div></div>"
			}
        });
    })
</script>

</body>
</html>