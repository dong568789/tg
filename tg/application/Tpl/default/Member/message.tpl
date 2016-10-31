<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
$page_title = "收件箱";
$page_css[] = "public/css/mail.css";
$page_css[] = "vendors/bower_components/jpages/css/jPages.css";
$page_css[] = "vendors/bower_components/jpages/css/animate.css";
$page_css[] = "vendors/bower_components/jpages/css/github.css";

?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $profile_nav["收件箱"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>收件箱</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header"></div>

                        <div class="card-body">
                            <ul class="tab-nav" role="tablist">
                                <li role="presentation" class="active" >
                                    <a class="col-xs-6 f-15" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                                        全部信息
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a class="col-xs-6 f-15" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">
                                        未读信息
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content p-20">
                                <div role="tabpanel" class="tab-pane animated fadeIn in active" id="tab-1">
                                    <div class="card m-t-25">
                                        <div class="listview lv-bordered lv-lg">
                                            <div class="lv-header-alt clearfix">
                                                <h2 class="lvh-label hidden-xs">全部信息</h2>

                                                <div class="lvh-search">
                                                    <input type="text" id="searchallmsg" placeholder="请输入搜索内容..." class="lvhs-input">
                                                    <i class="lvh-search-close">&times;</i>
                                                </div>


                                                <ul class="lv-actions actions">
                                                    <li class="dropdown">
                                                        <a href="" data-toggle="dropdown" aria-expanded="true">
                                                            <i class="zmdi zmdi-more-vert"></i>
                                                        </a>

                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="allMessageUnread();">全部标记为已读</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="alldeleteMessage();">批量删除</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="lv-body" id="messagecontainer">
                                                <foreach name="allmessage" item="vo" key="k">
                                                <div class="lv-item media">
                                                    <div class="checkbox pull-left m-t-12">
                                                        <label>
                                                            <input type="checkbox" id="checkallmsg" name="checkallmsg" data-id="<{$vo['id']}>" value="<{$vo['id']}>">
                                                            <i class="input-helper"></i>
                                                        </label>
                                                    </div>

                                                    <div class="media-body">
                                                        <div class="btn-demo messageTypeblock"  data-read="<{$vo['isread']}>" data-id="<{$vo['id']}>" data-toggle="collapse" href="#collapseExample<{$vo['id']}>" aria-expanded="false" aria-controls="collapseExample">
                                                            <a class="messageType">
                                                                <if condition="$vo['isread'] eq 0">
                                                                    <div class="lv-title m-b-5 collapsed messageTitle<{$vo['id']}>"><b>[<{$vo['category']}>]&nbsp;&nbsp;<{$vo['title']}></b></div>
                                                                <else/>
                                                                    <div class="lv-title m-b-5 collapsed" style="color: #999"><b>[<{$vo['category']}>]&nbsp;&nbsp;<{$vo['title']}></b></div>
                                                                </if>
                                                            </a>
                                                        </div>

                                                        <div class="collapse m-t-10" id="collapseExample<{$vo['id']}>">
                                                            <p><small class="lv-small-message" style="overflow:visible;"><{$vo['content']}></small></p>
                                                        </div>

                                                        <ul class="lv-attrs ">
                                                            <li>时间: <{$vo['createtime']}></li>
                                                        </ul>

                                                        <div class="lv-actions actions dropdown m-t-12 ">
                                                            <a href="" data-toggle="dropdown" aria-expanded="true">
                                                                <i class="zmdi zmdi-more-vert"></i>
                                                            </a>

                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <li>
                                                                    <a href="javascript:" onclick="deleteMessage('<{$vo['id']}>');" id="del-<{$vo['id']}>">删除</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                </foreach>
                                            </div>


                                            <div class="text-center holder">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane animated fadeIn in" id="tab-2">
                                    <div class="card m-t-25">
                                        <div class="listview lv-bordered lv-lg">
                                            <div class="lv-header-alt clearfix">
                                                <h2 class="lvh-label hidden-xs">全部信息</h2>

                                                <div class="lvh-search">
                                                    <input type="text" id="searchunreadmsg" placeholder="请输入搜索内容..." class="lvhs-input">
                                                    <i class="lvh-search-close">&times;</i>
                                                </div>


                                                <ul class="lv-actions actions">
                                                    <li class="dropdown">
                                                        <a href="" data-toggle="dropdown" aria-expanded="true">
                                                            <i class="zmdi zmdi-more-vert"></i>
                                                        </a>

                                                        <ul class="dropdown-menu dropdown-menu-right" id="allunread">
                                                            <li>
                                                                <a href="javascript:" onclick="allUnread();">全部标记为已读</a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:" onclick="alldelete();">批量删除</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="lv-body" id="messageunread">
                                                <foreach name="unreadmessage" item="vo" key="k">
                                                    <div class="lv-item media">
                                                        <div class="checkbox pull-left m-t-12">
                                                            <label>
                                                                <input type="checkbox" id="checkmessage" name="checkmessage" data-id="<{$vo['id']}>" value="<{$vo['id']}>">
                                                                <i class="input-helper"></i>
                                                            </label>
                                                        </div>

                                                        <div class="media-body">
                                                            <div class="btn-demo messageTypeblock"  id="unread-<{$vo['id']}>"  data-read="<{$vo['isread']}>" data-id="<{$vo['id']}>" data-toggle="collapse" href="#collapseExampleUnread<{$vo['id']}>" aria-expanded="false" aria-controls="collapseExample">
                                                                <a class="messageType">
                                                                    <div class="lv-title m-b-5 collapsed messageTitle<{$vo['id']}>"><b>[<{$vo['category']}>]&nbsp;&nbsp;<{$vo['title']}></b></div>
                                                                </a>
                                                            </div>

                                                            <div class="collapse m-t-10" id="collapseExampleUnread<{$vo['id']}>">
                                                                <p><small class="lv-small-message"><{$vo['content']}></small></p>
                                                            </div>

                                                            <ul class="lv-attrs">
                                                                <li>时间: <{$vo['createtime']}></li>
                                                            </ul>

                                                            <div class="lv-actions actions dropdown m-t-12">
                                                                <a href="" data-toggle="dropdown" aria-expanded="true">
                                                                    <i class="zmdi zmdi-more-vert"></i>
                                                                </a>

                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li>
                                                                        <a href="javascript:" onclick="oneUnread('<{$vo['id']}>');" id="oneunread-<{$vo['id']}>">标记为已读</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:" onclick="deleteUnread('<{$vo['id']}>');" id="delete-<{$vo['id']}>">删除</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </foreach>
                                            </div>
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

<script src="__ROOT__/plus/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/jpages/js/jPages.js"></script>

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

    //删除一条已读消息
    function deleteMessage (messageid) {
        $.ajax({
            type : 'POST',
            url : "/index.php?m=member&a=deletemsg",
            data : {id : messageid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                console.log(data);
                if (data.data == "success") {
                    notify(data.info, 'success');
                    $('#del-'+messageid).parent().parent().parent().parent().parent().addClass("display-none");
                    $('#oneunread-'+messageid).parent().parent().parent().parent().parent().addClass("display-none");
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
    }

    //删除一条未读消息
    function deleteUnread (messageid) {
        $.ajax({
            type : 'POST',
            url : "/index.php?m=member&a=deletemsg",
            data : {id : messageid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                console.log(data);
                if (data.data == "success") {
                    notify(data.info, 'success');
                    $('#oneunread-'+messageid).parent().parent().parent().parent().parent().addClass("display-none");
                    $('#del-'+messageid).parent().parent().parent().parent().parent().addClass("display-none");
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
    }

    //未读消息中标一条为已读
    function oneUnread (messageid) {
        $.ajax({
            type : 'POST',
            url : "/index.php?m=member&a=oneunread",
            data : {id : messageid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                console.log(data);
                if (data.data == "success") {
                    notify(data.info, 'success');
                    $('#oneunread-'+messageid).parent().parent().parent().parent().parent().addClass("display-none");
                    $('#del-'+messageid).parent().parent().parent().parent().children(1).children().children().children().css('color','#999');
                    var number = $("#messageCounts").text();
                    var aa = number - 1;
                    if(aa != 0){
                        $("#messageCounts").text(aa);
                    }else{
                        $("#messageCounts").css("display","none");
                        $("#messageContainer").empty();
                    }
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
    }

    //全部消息中标记多条为已读
    function allMessageUnread () {
        if($("input:checkbox[name='checkallmsg']:checked")){
            obj = document.getElementsByName("checkallmsg");
            messageid = [];
            for(k in obj){
                if(obj[k].checked)
                    messageid.push(obj[k].value);
            }
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=allunread",
                data : {id : messageid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        for(key in messageid){
                            $('#del-'+messageid[key]).parent().parent().parent().parent().children(1).children().children().children().css('color','#999');
                            $('#oneunread-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            $("input:checkbox[name='checkallmsg']:checked").attr("checked", false);
                            var leng = messageid.length;
                        }
                        var number = $("#messageCounts").text();
                        var aa = number - leng;
                        if(aa != 0){
                            $("#messageCounts").text(aa);
                        }else{
                            $("#messageCounts").css("display","none");
                            $("#messageContainer").empty();
                        }

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
        } else{
            notify('请选择要标记的消息。', 'danger');
        }

    }


    //未读消息中标多条为已读
    function allUnread () {
        if($("input:checkbox[name='checkmessage']:checked")){
            obj = document.getElementsByName("checkmessage");
            messageid = [];
            for(k in obj){
                if(obj[k].checked)
                    messageid.push(obj[k].value);
            }
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=allunread",
                data : {id : messageid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        for(key in messageid){
                            $('#oneunread-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            $('#del-'+messageid[key]).parent().parent().parent().parent().children(1).children().children().children().css('color','#999');
                            $("input:checkbox[name='checkmessage']:checked").attr("checked", false);
                            var leng = messageid.length;
                        }
                        var number = $("#messageCounts").text();
                        var aa = number - leng;
                        if(aa != 0){
                            $("#messageCounts").text(aa);
                        }else{
                            $("#messageCounts").css("display","none");
                            $("#messageContainer").empty();
                        }

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
        } else{
            notify('请选择要标记的消息。', 'danger');
        }

    }

    //删除多条未读消息
    function alldelete () {
        if(document.getElementById("checkmessage").checked){
            obj = document.getElementsByName("checkmessage");
            messageid = [];
            for(k in obj){
                if(obj[k].checked)
                    messageid.push(obj[k].value);
            }
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=deleteallmsg",
                data : {id : messageid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        for(key in messageid){
                            $('#oneunread-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            $('#del-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                        }

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
        } else{
            notify('请选择要删除的消息。', 'danger');
        }

    }


    //删除多条已读消息
    function alldeleteMessage () {
        if(document.getElementById("checkallmsg").checked){
            obj = document.getElementsByName("checkallmsg");
            messageid = [];
            for(k in obj){
                if(obj[k].checked)
                    messageid.push(obj[k].value);
            }
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=deleteallmsg",
                data : {id : messageid},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify(data.info, 'success');
                        for(key in messageid){
                            $('#del-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            $('#oneunread-'+messageid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                        }

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
        } else{
            notify('请选择要删除的消息。', 'danger');
        }

    }

    $(document).ready(function() {
        //个人信息菜单展开
        $(".profile_nav").css("display","block");

        $("div.holder").jPages({
            containerID    : "messagecontainer",
            scrollBrowse   : false,
            perPage: 20
        });

        //显示消息内容时未读的变已读
        $(".messageTypeblock").click(function(){
            var readtype = $(this).attr("data-read");
            var id = $(this).attr("data-id");
            if(readtype == 0){
                $.ajax({
                    type : 'POST',
                    url : "/index.php?m=member&a=oneunread",
                    data : {id : id},
                    cache : false,
                    dataType : 'json',
                    success : function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            $(".messageTitle"+id).css('color','#999');
                            var number = $("#messageCounts").text();
                            var aa = number - 1;
                            if(aa != 0){
                                $("#messageCounts").text(aa);
                            }else{
                                $("#messageCounts").css("display","none");
                                $("#messageContainer").empty();
                            }
                        }
                    }
                });
            }
        });

    })

</script>
</body>
</html>