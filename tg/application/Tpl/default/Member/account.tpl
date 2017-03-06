<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<?php
	$page_title = "账户管理";
	$page_css[] = "vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css";
    $page_css[] = "vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css";

?>
<include file="Inc:head" />
<body>
<include file="Inc:logged-header" />

<section id="main" data-layout="layout-1">
    <include file="Inc:sidemenuconfig" />
    <?php
    //个人资料页面用$profile_nav
    //功能页面用$page_nav
    $profile_nav["账户管理"]["active"] = true;
    ?>
    <include file="Inc:sidemenu" />
    <section id="content">
        <div class="container">
            <!--内容-->
            <div class="block-header">
                <h2>账户管理</h2>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div>

                        <div class="card-body">
                            <ul class="tab-nav tn-justified tn-icon" role="tablist">
                                <li role="presentation" id="tab-1-header" class="active">
                                    <a class="col-xs-6 f-20" href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">
                                        <i class="zmdi zmdi-shield-check icon-tab m-r-5"></i>
                                        支付宝
                                    </a>
                                </li>
                                <li role="presentation" id="tab-2-header">
                                    <a class="col-xs-6 f-20" href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">
                                        <i class="zmdi zmdi-card icon-tab m-r-5"></i>
                                        银行卡
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content p-20">
                                <div role="tabpanel" class="tab-pane animated fadeIn in active" id="tab-1">
                                    <form class="form-horizontal" id="alipay" role="form">
                                        <div class="form-group m-t-25">
                                            <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">支付宝账号</label>
                                            <div class="col-sm-6">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="aliaccount" id="aliaccount" value="<{$alipayaccount['aliaccount']}>">
                                                        <label class="fg-label">请填写支付宝账号</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group m-t-25">
                                            <label for="alipayaccount" class="col-sm-3 control-label f-15 m-t-5">真实姓名</label>
                                            <div class="col-sm-6">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="aliusername" id="aliusername" value="<{$alipayaccount['aliusername']}>">
                                                        <label class="fg-label">请填写用户姓名</label>
                                                        <input type="hidden" class="form-control" value="" id="one" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-t-25">
                                            <div class="col-sm-12 text-center">
                                                <if condition="$alipayaccount['aliaccount'] eq ''">
                                                <button type="submit" id="addalipay" class="btn btn-primary btn-lg">新增支付宝账户资料</button>
                                                <button id="savealipay" class="btn btn-primary btn-lg" style="display: none;left:42%;">保存修改账户资料</button>
                                                <else/>
                                                <button id="withdrawalipay" data-id="<{$alipayaccount['id']}>" class="btn btn-primary btn-lg">保存修改账户资料</button>
                                                </if>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="card m-t-25">
                                        <div class="listview lv-bordered lv-lg">
                                            <div class="lv-header-alt clearfix">
                                                <h2 class="lvh-label hidden-xs">用户已登陆的支付宝账号</h2>

                                                <div class="lvh-search">
                                                    <input type="text" id="searchalipay" placeholder="请输入搜索内容..." class="lvhs-input">
                                                    <i class="lvh-search-close" id="closeAlipay">&times;</i>
                                                </div>

                                                <ul class="lv-actions actions">
                                                    <li>
                                                        <a href="" class="lvh-search-trigger">
                                                            <i class="zmdi zmdi-search"></i>
                                                        </a>
                                                    </li>
                                                    <li class="dropdown">
                                                        <a href="" data-toggle="dropdown" aria-expanded="true">
                                                            <i class="zmdi zmdi-more-vert"></i>
                                                        </a>

                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="javascript:" onclick="deleteallAlipay();">批量删除</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="lv-body" id="alipaycontainer">
                                                <foreach name="alipay" item="vo" key="k">
                                                    <div class="lv-item media">
                                                        <div class="checkbox pull-left m-t-12">
                                                            <label>
                                                                <input type="checkbox" id="checkalipay" name="checkalipay" value="<{$vo['id']}>">
                                                                <i class="input-helper"></i>
                                                            </label>
                                                        </div>
                                                        <div class="pull-left">
                                                            <img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/alipay.png" alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <ul class="lv-attrs">
                                                                <li class="aliaccount<{$vo['id']}>">支付宝账号: <{$vo['aliaccount']}></li>
                                                                <li class="aliusername<{$vo['id']}>">用户名: <{$vo['aliusername']}></li>
                                                                <li>添加时间: <{$vo['createtime']}></li>
                                                            </ul>

                                                            <div class="lv-actions actions dropdown m-t-12">
                                                                <a href="" data-toggle="dropdown" aria-expanded="true">
                                                                    <i class="zmdi zmdi-more-vert"></i>
                                                                </a>

                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li>
                                                                        <a href="javascript:void(0);" class="edit" id="edit-<{$vo['id']}>" onclick="editAlipay('<{$vo['id']}>');" >编辑</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:" onclick="deleteAlipay('<{$vo['id']}>');" id="delete-<{$vo['id']}>">删除</a>
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

                                <div role="tabpanel" class="tab-pane animated fadeIn" id="tab-2">
                                    <form class="form-horizontal" id="bank" role="form">
                                        <div class="form-group m-t-25">
                                            <label for="bankaccount" class="col-sm-3 control-label f-15 m-t-5">账户名称</label>
                                            <div class="col-sm-6">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="bankusername" id="bankusername" value="<{$bankaccount['bankusername']}>">
                                                        <label class="fg-label">请填写您的银行卡账户名称或企业名称</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group m-t-25">
                                            <label for="bankname" class="col-sm-3 control-label f-15 m-t-5">收款银行</label>
                                            <div class="col-sm-3">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="bankname" id="bankname" value="<{$bankaccount['bankname']}>">
                                                        <label class="fg-label">开户行 如:中国工商银行</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="branchname" id="branchname" value="<{$bankaccount['branchname']}>">
                                                        <label class="fg-label">支行名称 如:洪山支行</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-t-25">
                                            <label for="bankdepositprovince" class="col-sm-3 control-label f-15 m-t-5">开户地</label>
                                            <div class="col-sm-3">
                                                <div class="fg-line">
                                                    <select name="bankprovince" id="bankprovince" style="border: none;width: 100%;border-bottom: 1px solid #ddd;padding-bottom: 9px;"></select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="fg-line">
                                                    <select name="bankcity" id="bankcity" style="border: none;width: 100%;border-bottom: 1px solid #ddd;padding-bottom: 9px;"></select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group m-t-25">
                                            <label for="bankaccount" class="col-sm-3 control-label f-15 m-t-5">银行账号</label>
                                            <div class="col-sm-6">
                                                <div class="input-group-float fg-float">
                                                    <div class="fg-line">
                                                        <input type="text" class="form-control" name="bankaccount" id="bankaccount" value="<{$bankaccount['bankaccount']}>">
                                                        <label class="fg-label">请填写正确的银行账号,我们将以此作为打款依据</label>
                                                        <input type="hidden" class="form-control" value="" id="bankid" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!---判断此页是否有type的返回值---------->
                                        <input type="hidden" id="checktab" data-type="<?php echo $_GET['type'];?>">

                                        <div class="form-group m-t-25">
                                            <div class="col-sm-12 text-center">
                                                <if condition="$bankaccount['bankaccount'] eq ''">
                                                <button type="submit" id="addbank" class="btn btn-primary btn-lg">新增银行卡账户资料</button>
                                                <button type="submit" id="savebank" data-id="<{$bankaccount['id']}>" class="btn btn-primary btn-lg" style="display: none;left:42%;">保存银行卡账户资料</button>
                                                <else/>
                                                <button type="submit" id="withdrawbank" data-id="<{$bankaccount['id']}>" class="btn btn-primary btn-lg">保存银行卡账户资料</button>
                                                </if>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="card m-t-25">
                                        <div class="listview lv-bordered lv-lg">
                                            <div class="lv-header-alt clearfix">
                                                <h2 class="lvh-label hidden-xs">用户已登陆的银行卡账号</h2>

                                                <div class="lvh-search">
                                                    <input type="text" id="searchbank" placeholder="请输入搜索内容..." class="lvhs-input">
                                                    <i class="lvh-search-close" id="closeBank">&times;</i>
                                                </div>

                                                <ul class="lv-actions actions">
                                                    <li>
                                                        <a href="" class="lvh-search-trigger">
                                                            <i class="zmdi zmdi-search"></i>
                                                        </a>
                                                    </li>
                                                    <li class="dropdown">
                                                        <a href="" data-toggle="dropdown" aria-expanded="true">
                                                            <i class="zmdi zmdi-more-vert"></i>
                                                        </a>

                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="javascript:" onclick="deleteallBank();">批量删除</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="lv-body" id="bankContainner">
                                                <foreach name="bank" item="vo" key="k">
                                                    <div class="lv-item media" id="addbankContainner">
                                                        <div class="checkbox pull-left m-t-12">
                                                            <label>
                                                                <input type="checkbox" id="checkbank" name="checkbank" value="<{$vo['id']}>">
                                                                <i class="input-helper"></i>
                                                            </label>
                                                        </div>
                                                        <div class="pull-left">
                                                            <img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/bank.png" alt="">
                                                        </div>
                                                        <div class="media-body">
                                                            <ul class="lv-attrs">
                                                                <li class="bankaccount<{$vo['id']}>">银行卡账号: <{$vo['bankaccount']}></li>
                                                                <li class="bankname<{$vo['id']}>">银行名称: <{$vo['bankname']}></li>
                                                                <li class="bankusername<{$vo['id']}>">用户名: <{$vo['bankusername']}></li>
                                                                <li>添加时间: <{$vo['createtime']}></li>
                                                            </ul>

                                                            <div class="lv-actions actions dropdown m-t-12">
                                                                <a href="" data-toggle="dropdown" aria-expanded="true">
                                                                    <i class="zmdi zmdi-more-vert"></i>
                                                                </a>

                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li>
                                                                        <a href="javascript:void(0);" class="edit" onclick="editBank('<{$vo['id']}>');" >编辑</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:" onclick="deleteBank('<{$vo['id']}>');" id="del-<{$vo['id']}>">删除</a>
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
<script src="__ROOT__/plus/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js"></script>
<script src="__ROOT__/plus/public/js/PCASClass.js" charset="gb2312"></script>

<script type="text/javascript">
    //回车绑定事件
    document.onkeydown=function(event){
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if(e && e.keyCode==13){ // enter 键
            //要做的事情
            document.getElementById("searchalipay").blur();
            document.getElementById("searchbank").blur();
        }
    };


    //删除银行卡号
    function deleteBank (bankid) {
        $.ajax({
            type : 'POST',
            url : "index.php?m=member&a=isusedBank",
            data : {id : bankid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                if (data == 0) {
                    swal({
                        title: "提示",
                        text: "该卡号还没有结算单，确认删除？",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定删除",
                        cancelButtonText: "取消",
                        closeOnConfirm: false
                    }, function(){
                        swal("删除成功!", "", "success");
                        $.ajax({
                            type : 'POST',
                            url : "index.php?m=member&a=deleteBank",
                            data : {id : bankid},
                            cache : false,
                            dataType : 'json',
                            success : function (data) {
                                if (data.data == "success") {
                                    notify(data.info, 'success');
                                    $('#del-'+bankid).parent().parent().parent().parent().parent().addClass("display-none");
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
                    });
                } else {
                    swal({
                        title: "提示",
                        text: "该提现金额要向该卡打款，若删除，还是向该卡打款！",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定删除",
                        cancelButtonText: "取消",
                        closeOnConfirm: false
                    }, function(){
                        swal("删除成功!", "", "success");
                        $.ajax({
                            type : 'POST',
                            url : "index.php?m=member&a=deleteBank",
                            data : {id : bankid},
                            cache : false,
                            dataType : 'json',
                            success : function (data) {
                                if (data.data == "success") {
                                    notify(data.info, 'success');
                                    $('#del-'+bankid).parent().parent().parent().parent().parent().addClass("display-none");
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
                    });
                }
            }
        });
    }

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

    //删除单条支付宝账号
    function deleteAlipay (alipayid) {
        $.ajax({
            type : 'POST',
            url : "index.php?m=member&a=isusedAlipay",
            data : {id : alipayid},
            cache : false,
            dataType : 'json',
            success : function (data) {
                console.log(data);
                if (data == 0) {
                    swal({
                        title: "提示",
                        text: "该账号还没有结算单，确认删除？",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定删除",
                        cancelButtonText: "取消",
                        closeOnConfirm: false
                    }, function(){
                        swal("删除成功!", "", "success");
                        $.ajax({
                            type : 'POST',
                            url : "index.php?m=member&a=deleteAlipay",
                            data : {id : alipayid},
                            cache : false,
                            dataType : 'json',
                            success : function (data) {
                                if (data.data == "success") {
                                    notify(data.info, 'success');
                                    $('#delete-'+alipayid).parent().parent().parent().parent().parent().addClass("display-none");
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
                    });
                } else {
                    swal({
                        title: "提示",
                        text: "目前已有结算申请使用的是该支付方式，若删除后，已有关联结算依然会使用该支付方式，确认删除？",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定删除",
                        cancelButtonText: "取消",
                        closeOnConfirm: false
                    }, function(){
                        swal("删除成功!", "", "success");
                        $.ajax({
                            type : 'POST',
                            url : "index.php?m=member&a=deleteAlipay",
                            data : {id : alipayid},
                            cache : false,
                            dataType : 'json',
                            success : function (data) {
                                if (data.data == "success") {
                                    notify(data.info, 'success');
                                    $('#delete-'+alipayid).parent().parent().parent().parent().parent().addClass("display-none");
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
                    });
                }
            }
        });


    }

    //删除多条支付宝账号
    function deleteallAlipay () {
        if($("input:checkbox[name='checkalipay']:checked")) {
            obj = document.getElementsByName("checkalipay");
            alipayid = [];
            for (k in obj) {
                if (obj[k].checked)
                    alipayid.push(obj[k].value);
            }
            var confirmcontent = "确认删除账号？";
            if (confirm(confirmcontent)) {
                $.ajax({
                    type: 'POST',
                    url: "index.php?m=member&a=deleteallAlipay",
                    data: {id : alipayid},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            notify(data.info, 'success');
                            for(key in alipayid){
                                $('#delete-' + alipayid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            }
                        } else {
                            notify(data.info, 'danger');
                        }
                        return false;
                    },
                    error: function (xhr) {
                        notify('系统错误！', 'danger');
                        return false;
                    }
                });
            } else {
                return false;
            }
        }else{
            notify('请选择要删除的账号。', 'danger');
        }
    }


    //删除多条银行卡账号
    function deleteallBank () {
        if($("input:checkbox[name='checkbank']:checked")) {
            obj = document.getElementsByName("checkbank");
            bankid = [];
            for (k in obj) {
                if (obj[k].checked)
                    bankid.push(obj[k].value);
            }
            var confirmcontent = "确认删除账号？";
            if (confirm(confirmcontent)) {
                $.ajax({
                    type: 'POST',
                    url: "index.php?m=member&a=deleteallBank",
                    data: {id : bankid},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            notify(data.info, 'success');
                            for(key in bankid){
                                $('#del-' + bankid[key]).parent().parent().parent().parent().parent().addClass("display-none");
                            }
                        } else {
                            notify(data.info, 'danger');
                        }
                        return false;
                    },
                    error: function (xhr) {
                        notify('系统错误！', 'danger');
                        return false;
                    }
                });
            } else {
                return false;
            }
        }else{
            notify('请选择要删除的账号。', 'danger');
        }
    }

    //修改支付宝账号
    function editAlipay (alipayid) {

        var confirmcontent = "确认修改账号信息？";
        if(confirm(confirmcontent)) {
            $.ajax({
                type: 'POST',
                url: "index.php?m=member&a=showEditalipay",
                data: {id : alipayid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        var value = data.info;
                        document.getElementById("aliaccount").value = value.aliaccount;
                        document.getElementById("aliusername").value = value.aliusername;
                        document.getElementById("one").value = value.id;
                        $(".fg-label").css("display","none");
                        $("#addalipay").css("display","none");
                        $("#savealipay").css("display","block");
                    } else {
                        notify('有误', 'danger');
                    }
                },
                error: function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });

        } else {
            return false;
        }
    }

    //修改银行卡账号
    function editBank (bankid) {
        var confirmcontent = "确认修改账号信息？";
        if(confirm(confirmcontent)) {
            $.ajax({
                type: 'POST',
                url: "index.php?m=member&a=showEditbank",
                data: {id : bankid},
                cache: false,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        var value = data.info;
                        document.getElementById("bankusername").value = value.bankusername;
                        document.getElementById("bankname").value = value.bankname;
                        document.getElementById("branchname").value = value.branchname;
                        document.getElementById("bankaccount").value = value.bankaccount;
                        document.getElementById("bankprovince").value = value.bankprovince;
                        document.getElementById("bankcity").value = value.bankcity;
                        document.getElementById("bankid").value = value.id;
                        new PCAS("bankprovince","bankcity",bankprovince,bankcity);
                        $(".fg-label").css("display","none");
                        $("#addbank").css("display","none");
                        $("#savebank").css("display","block");
                    } else {
                        notify('有误', 'danger');
                    }
                    return false;
                },
                error: function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        } else {
            return false;
        }
    }

    function checktab(type) {
        if (type == 2) {
            $("#tab-1-header").removeClass("active");
            $("#tab-2-header").addClass("active");
            $("#tab-1").removeClass("active");
            $("#tab-2").addClass("active");
        } else {
            //do nothing
        }
    }

    $(document).ready(function() {
        //个人信息菜单展开
        $(".profile_nav").css("display","block");
        //省和市对应
        var PCA;
        new PCAS("bankprovince","bankcity","湖北省");

        var type = $("#checktab").attr("data-type");  //1为支付宝 2为银行卡
        if(type != ''){
            checktab(type);
        }else{
            //do nothing
        }

        jQuery.validator.addMethod("checkENGsmallNUM", function(value, element) {
            var reg =  /^[0-9a-z]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入英文小写字母或数字！");

        jQuery.validator.addMethod("checkWord", function(value, element) {
            var reg =  /^[\u4e00-\u9fa5]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的汉字！");

        jQuery.validator.addMethod("checkCHN", function(value, element) {
            var reg =  /^[a-zA-Z\u4e00-\u9fa5]+$/;
            return this.optional(element) || reg.test(value);
        }, "请输入正确的字母或汉字！");

        jQuery.validator.addMethod("alipayACCOUNT", function(value, element) {
            var reg =  /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|17[0-9]|18[0-9]|170)\d{8}$/;
            var email = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
            return (this.optional(element) || reg.test(value)) || (this.optional(element) || email.test(value));
        }, "请输入正确的手机号码或者邮箱账号！");

        //保存修改支付宝账号
        $('#savealipay').click(function() {
            var $alipay = $('#alipay').validate({
                rules: {
                    aliaccount: {
                        required: true,
                        alipayACCOUNT : true
                    },
                    aliusername: {
                        required: true,
                        minlength : 2,
                        maxlength : 10,
                        checkCHN : true
                    }
                },

                messages: {
                    aliaccount: {
                        required: '支付宝账号不得为空',
                        alipayACCOUNT : '支付宝账号为手机号或者邮箱账号'
                    },
                    aliusername: {
                        required: '请填写真实姓名',
                        minlength : '真实姓名长度为2-10位字母或汉字',
                        maxlength : '真实姓名长度为2-10位字母或汉字',
                        checkCHN : '真实姓名必须为汉字或字母'
                    }
                },

                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
            if ($('#alipay').valid()) {
                var aliaccount1 = $('#aliaccount').val();
                var aliusername1 = $('#aliusername').val();
                var id1 = $('#one').val();

                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=editalipay",
                    data: {aliaccount:aliaccount1,aliusername:aliusername1,id:id1},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.data == "success") {
                            notify('数据修改成功', 'success');
                            $(".aliaccount" + id1).text('支付宝账号: ' + aliaccount1);
                            $(".aliusername" + id1).text('用户名: ' + aliusername1);
                            $('#aliaccount').val("");
                            $('#aliusername').val("");
                            $("#addalipay").css("display","block");
                            $("#addalipay").css("left","42%");
                            $("#savealipay").css("display","none");
                        } else {
                            notify('数据修改失败', 'danger');
                            return false;
                        }
                        return false;
                    }
                });
                return false;
            }
        });

        //新增支付宝账号
        $('#addalipay').click(function() {
            var $alipay = $('#alipay').validate({
                rules: {
                    aliaccount: {
                        required: true,
                        alipayACCOUNT : true
                    },
                    aliusername: {
                        required: true,
                        minlength : 2,
                        maxlength : 10,
                        checkCHN : true
                    }
                },

                messages: {
                    aliaccount: {
                        required: '支付宝账号不得为空',
                        alipayACCOUNT : '支付宝账号为手机号或者邮箱账号'
                    },
                    aliusername: {
                        required: '请填写真实姓名',
                        minlength : '真实姓名长度为2-10位字母或汉字',
                        maxlength : '真实姓名长度为2-10位字母或汉字',
                        checkCHN : '真实姓名必须为汉字或字母'
                    }
                },

                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
            if ($('#alipay').valid()) {
                var aliaccount = $("#aliaccount").val();
                var aliusername = $("#aliusername").val();
                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=addAlipay",
                    data: {aliaccount:aliaccount,aliusername:aliusername},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            $("#aliaccount").val("");
                            $("#aliusername").val("");
                            var value = data.info;
                            var html = '';
                            html = '<div class="lv-item media"><div class="checkbox pull-left m-t-12"><label><input type="checkbox" value=""><i class="input-helper"></i></label></div><div class="pull-left"><img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/alipay.png" alt=""></div> <div class="media-body"> <ul class="lv-attrs">' +
                            '<li class=aliaccount' + value.id + '>支付宝账号: ' + value.aliaccount + '</li>' +
                            '<li class=aliusername' + value.id + '>用户名: ' + value.aliusername + '</li>' +
                            '<li>添加时间: ' + value.createtime + '</li>' +
                            '</ul>' +
                            '<div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a>' +
                            '<ul class="dropdown-menu dropdown-menu-right">' +
                            '<li><a href="javascript:"  onclick="editAlipay(' + value.id + ');" id=edit-' + value.id + '>编辑</a> </li>' +
                            '<li><a href="javascript:" onclick="deleteAlipay('+ value.id +');" id=delete-' + value.id + '>删除</a></li>' +
                            '</ul></div></div></div>';
                            $("#alipaycontainer").append(html);
                            notify('账号新增成功', 'success');
                        }else{
                            notify(data.info, 'danger');
                            $("#aliusername").val("");
                        }
                        return false;
                    }
                });
                return false;
            }
        });

        //withdraw页面传过来的修改支付宝账号
        $('#withdrawalipay').click(function() {
            var $alipay = $('#alipay').validate({
                rules: {
                    aliaccount: {
                        required: true,
                        alipayACCOUNT : true
                    },
                    aliusername: {
                        required: true,
                        minlength : 2,
                        maxlength : 10,
                        checkCHN : true
                    }
                },

                messages: {
                    aliaccount: {
                        required: '支付宝账号不得为空',
                        alipayACCOUNT : '支付宝账号为手机号或者邮箱账号'
                    },
                    aliusername: {
                        required: '请填写真实姓名',
                        minlength : '真实姓名长度为2-10位字母或汉字',
                        maxlength : '真实姓名长度为2-10位字母或汉字',
                        checkCHN : '真实姓名必须为汉字或字母'
                    }
                },

                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
            if ($('#alipay').valid()) {
                var aliaccount1 = $('#aliaccount').val();
                var aliusername1 = $('#aliusername').val();
                var id1 = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=editalipay",
                    data: {aliaccount:aliaccount1,aliusername:aliusername1,id:id1},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            notify('数据修改成功', 'success');
                            $(".aliaccount" + id1).text('支付宝账号: ' + aliaccount1);
                            $(".aliusername" + id1).text('用户名: ' + aliusername1);
                            setTimeout(function () {
                                location.href = '/index.php?m=balance&a=withdraw&type=2&id='+ id1;
                            }, 1000);
                        } else {
                            notify('数据修改失败', 'danger');
                            return false;
                        }
                        return false;
                    }
                });
                return false;
            }
        });

        //withdraw页面传过来的修改银行卡账号
        $('#withdrawbank').click(function() {
            var bank = $('#bank').validate({
                ignore : ':hidden:not(.selectpicker)',
                rules : {
                    bankaccount : {
                        required : true,
                        digits : true,
                        minlength :10

                    },
                    bankusername : {
                        required : true,
                        minlength : 2,
                        maxlength :20,
                        checkWord : true
                    },
                    bankname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    },
                    branchname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    }
                },

                messages : {
                    bankaccount : {
                        required : '请填写正确的银行卡账号',
                        digits : '银行卡账号必须为数字',
                        minlength : '银行卡账号长度不得低于10位'

                    },
                    bankusername : {
                        required : '请填写账户名称',
                        minlength : '账户名称不得低于2位',
                        maxlength : '账户名称不得高于20位',
                        checkWord: '账户名称必须为汉字'
                    },
                    bankname : {
                        required : '收款银行不得为空',
                        minlength : '收款银行不得低于4位',
                        maxlength : '收款银行不得高于20位',
                        checkWord: '收款银行必须为汉字'
                    },
                    branchname : {
                        required : '支行不得为空',
                        minlength : '支行名称不得低于4位',
                        maxlength : '支行名称不得高于20位',
                        checkWord: '支行名称必须为汉字'
                    }
                },

                errorPlacement : function(error, element) {
                    if (element.hasClass('bs-select-hidden')) {
                        error.insertAfter(element.parent().children().eq(1));
                    } else {
                        error.insertAfter(element.parent());
                    }
                }
            });
            if ($('#bank').valid()) {
                var bankaccount = $("#bankaccount").val();
                var bankusername = $("#bankusername").val();
                var bankname = $("#bankname").val();
                var branchname = $("#branchname").val();
                var bankprovince = $("#bankprovince").val();
                var bankcity = $("#bankcity").val();
                var id1 = $(this).attr("data-id");
                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=editbank",
                    data: {bankaccount:bankaccount,bankusername:bankusername,bankname:bankname,branchname:branchname,bankprovince:bankprovince,bankcity:bankcity,id:id1},

                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.data == "success") {
                            notify('数据修改成功', 'success');
                            $(".bankaccount" + id1).text('银行卡账号: ' + bankaccount);
                            $(".bankname" + id1).text('银行名称: ' + bankname);
                            $(".bankusername" + id1).text('用户名: ' + bankusername);
                            $("input").val("");
                            new PCAS("bankprovince","bankcity","湖北省");
                            setTimeout(function () {
                                location.href = '/index.php?m=balance&a=withdraw&type=1&id='+ id1;
                            }, 1000);
                        } else {
                            notify('数据修改失败', 'danger');
                            return false;
                        }
                        return false;
                    }
                });
                return false;
            }
        });


        //新增银行卡账号
        $('#addbank').click(function() {
            var bank = $('#bank').validate({
                ignore : ':hidden:not(.selectpicker)',
                rules : {
                    bankaccount : {
                        required : true,
                        digits : true,
                        minlength :10

                    },
                    bankusername : {
                        required : true,
                        minlength : 2,
                        maxlength :20,
                        checkWord : true
                    },
                    bankname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    },
                    branchname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    }
                },

                messages : {
                    bankaccount : {
                        required : '请填写正确的银行卡账号',
                        digits : '银行卡账号必须为数字',
                        minlength : '银行卡账号长度不得低于10位'

                    },
                    bankusername : {
                        required : '请填写账户名称',
                        minlength : '账户名称不得低于2位',
                        maxlength : '账户名称不得高于20位',
                        checkWord: '账户名称必须为汉字'
                    },
                    bankname : {
                        required : '收款银行不得为空',
                        minlength : '收款银行不得低于4位',
                        maxlength : '收款银行不得高于20位',
                        checkWord: '收款银行必须为汉字'
                    },
                    branchname : {
                        required : '支行不得为空',
                        minlength : '支行名称不得低于4位',
                        maxlength : '支行名称不得高于20位',
                        checkWord: '支行名称必须为汉字'
                    }
                },

                errorPlacement : function(error, element) {
                    if (element.hasClass('bs-select-hidden')) {
                        error.insertAfter(element.parent().children().eq(1));
                    } else {
                        error.insertAfter(element.parent());
                    }
                }
            });
            if ($('#bank').valid()) {
                var bankaccount = $("#bankaccount").val();
                var bankusername = $("#bankusername").val();
                var bankname = $("#bankname").val();
                var branchname = $("#branchname").val();
                var bankprovince = $("#bankprovince").val();
                var bankcity = $("#bankcity").val();
                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=addbBankaccount",
                    data: {bankaccount:bankaccount,bankusername:bankusername,bankname:bankname,branchname:branchname,bankprovince:bankprovince,bankcity:bankcity},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data.info);
                        if (data.data == "success") {
                            var value = data.info;
                            var html = '';
                            html = '<div class="lv-body"><div class="lv-item media"> <div class="checkbox pull-left m-t-12"> <label> <input type="checkbox" value=""> <i class="input-helper"></i> </label> </div> <div class="pull-left"> <img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/bank.png" alt=""> </div> <div class="media-body"> <ul class="lv-attrs">'+
                            '<li  class="bankaccount'+value.id+'">银行卡账号: ' + value.bankaccount + '</li>'+
                            '<li  class="bankname'+value.id+'">银行名称: ' + value.bankname + '</li>'+
                            '<li  class="bankusername'+value.id+'">用户名: ' + value.bankusername + '</li>'+
                            '<li>添加时间: ' + value.createtime + '</li>'+
                            '</ul>'+
                            '<div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i> </a>'+
                            '<ul class="dropdown-menu dropdown-menu-right">'+
                            '<li><a href="javascript:void(0);" class="edit" onclick="editBank('+ value.id +');" >编辑</a></li>'+
                            '<li> <a href="javascript:" onclick=deleteBank('+ value.id + '); id=del-' + value.id + '>删除</a> </li> '+
                            '</ul></div></div></div></div>';
                            $("#bankContainner").append(html);
                            notify('账号新增成功', 'success');
                            $("input").val("");
                            new PCAS("bankprovince","bankcity","湖北省");
                        }else{
                            notify(data.info, 'danger');
                            $("input").val("");
                        }
                        return false;
                    }
                });
                return false;
            }
        });

        //保存修改银行卡账号
        $('#savebank').click(function() {
            var bank = $('#bank').validate({
                ignore : ':hidden:not(.selectpicker)',
                rules : {
                    bankaccount : {
                        required : true,
                        digits : true,
                        minlength :12

                    },
                    bankusername : {
                        required : true,
                        minlength : 2,
                        maxlength :20,
                        checkWord : true
                    },
                    bankname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    },
                    branchname : {
                        required : true,
                        minlength : 4,
                        maxlength :20,
                        checkWord : true
                    }
                },

                messages : {
                    bankaccount : {
                        required : '请填写正确的银行卡账号',
                        digits : '银行卡账号必须为数字',
                        minlength : '银行卡账号长度不得低于12位'

                    },
                    bankusername : {
                        required : '请填写账户名称',
                        minlength : '账户名称不得低于2位',
                        maxlength : '账户名称不得高于20位',
                        checkWord: '账户名称必须为汉字'
                    },
                    bankname : {
                        required : '收款银行不得为空',
                        minlength : '收款银行不得低于4位',
                        maxlength : '收款银行不得高于20位',
                        checkWord: '收款银行必须为汉字'
                    },
                    branchname : {
                        required : '支行不得为空',
                        minlength : '支行名称不得低于4位',
                        maxlength : '支行名称不得高于20位',
                        checkWord: '支行名称必须为汉字'
                    }
                },

                errorPlacement : function(error, element) {
                    if (element.hasClass('bs-select-hidden')) {
                        error.insertAfter(element.parent().children().eq(1));
                    } else {
                        error.insertAfter(element.parent());
                    }
                }
            });
            if ($('#bank').valid()) {
                var bankaccount = $("#bankaccount").val();
                var bankusername = $("#bankusername").val();
                var bankname = $("#bankname").val();
                var branchname = $("#branchname").val();
                var bankprovince = $("#bankprovince").val();
                var bankcity = $("#bankcity").val();
                var id = $('#bankid').val();

                $.ajax({
                    type: "POST",
                    url: "index.php?m=member&a=editbank",
                    data: {bankaccount:bankaccount,bankusername:bankusername,bankname:bankname,branchname:branchname,bankprovince:bankprovince,bankcity:bankcity,id:id},
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data.data == "success") {
                            notify('数据修改成功', 'success');
                            $(".bankaccount" + id).text('银行卡账号: ' + bankaccount);
                            $(".bankname" + id).text('银行名称:: ' + bankname);
                            $(".bankusername" + id).text('用户名: ' + bankusername);
                            $("input").val("");
                            new PCAS("bankprovince","bankcity","湖北省");
                            $("#addbank").css("display","block");
                            $("#addbank").css("left","42%");
                            $("#savebank").css("display","none");
                        } else {
                            notify('数据修改失败', 'danger');
                            return false;
                        }
                        return false;
                    }
                });
                return false;
            }
        });


        //搜索支付宝账号
        $("#searchalipay").blur(function(){
            var msg = $("#searchalipay").val();
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=searchalipay",
                data : {msg : msg},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify('获取账号成功。', 'success');
                        var value = data.info;
                        var html = '';
                        $("#alipaycontainer").empty();
                        for(var i=0; i<value.length; i++){
                            html='<div class="lv-item media"><div class="checkbox pull-left m-t-12"><label>'+
                                '<input type="checkbox" id="checkalipay" name="checkalipay" value="'+value[i].id+'">'+
                                '<i class="input-helper"></i></label></div><div class="pull-left"> <img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/alipay.png" alt=""> </div> <div class="media-body"> <ul class="lv-attrs">'+
                                '<li class="aliaccount'+value[i].id+'">支付宝账号: '+value[i].aliaccount+'</li>'+
                                '<li class="aliusername'+value[i].id+'">用户名: '+value[i].aliusername+'</li>'+
                                '<li>添加时间:'+value[i].createtime+'</li>'+
                                '</ul><div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a><ul class="dropdown-menu dropdown-menu-right"><li>'+
                                '<a href="javascript:void(0);" class="edit" id="edit-'+value[i].id+'" onclick="editAlipay('+value[i].id+');" >编辑</a></li><li>'+
                                '<a href="javascript:" onclick="deleteAlipay('+value[i].id+');" id="delete-'+value[i].id+'">删除</a>'+
                                '</li></ul></div></div></div>';
                            $("#alipaycontainer").append(html);
                        }

                    } else {
                        notify('没有该账号。', 'danger');
                        $("#alipaycontainer").empty();
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });
        //关闭搜索支付宝
        $("#closeAlipay").click(function(){
            $.ajax({
                url : "/index.php?m=member&a=closeAlipay",
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        var value = data.info;
                        var html = '';
                        $("#alipaycontainer").empty();
                        for(var i=0; i<value.length; i++){
                            html='<div class="lv-item media"><div class="checkbox pull-left m-t-12"><label>'+
                            '<input type="checkbox" id="checkalipay" name="checkalipay" value="'+value[i].id+'">'+
                            '<i class="input-helper"></i></label></div><div class="pull-left"> <img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/alipay.png" alt=""> </div> <div class="media-body"> <ul class="lv-attrs">'+
                            '<li class="aliaccount'+value[i].id+'">支付宝账号: '+value[i].aliaccount+'</li>'+
                            '<li class="aliusername'+value[i].id+'">用户名: '+value[i].aliusername+'</li>'+
                            '<li>添加时间:'+value[i].createtime+'</li>'+
                            '</ul><div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a><ul class="dropdown-menu dropdown-menu-right"><li>'+
                            '<a href="javascript:void(0);" class="edit" id="edit-'+value[i].id+'" onclick="editAlipay('+value[i].id+');" >编辑</a></li><li>'+
                            '<a href="javascript:" onclick="deleteAlipay('+value[i].id+');" id="delete-'+value[i].id+'">删除</a>'+
                            '</li></ul></div></div></div>';
                            $("#alipaycontainer").append(html);
                        }
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });


        //搜索银行卡账号
        $("#searchbank").blur(function(){
            var msg = $("#searchbank").val();
            $.ajax({
                type : 'POST',
                url : "/index.php?m=member&a=searchbank",
                data : {msg : msg},
                cache : false,
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify('获取账号成功。', 'success');
                        var value = data.info;
                        var html = '';
                        $("#bankContainner").empty();
                        for(var i=0; i<value.length; i++){
                            html='<div class="lv-item media"><div class="checkbox pull-left m-t-12"><label>'+
                                '<input type="checkbox" id="checkbank" name="checkbank" value="'+value[i].id+'">'+
                                '<i class="input-helper"></i></label></div><div class="pull-left"><img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/bank.png" alt=""></div><div class="media-body"><ul class="lv-attrs">'+
                                '<li class="bankaccount'+value[i].id+'">银行卡账号: '+value[i].bankaccount+'</li>'+
                                '<li class="bankname'+value[i].id+'">银行名称: '+value[i].bankname+'</li>'+
                                '<li class="bankusername'+value[i].id+'">用户名: '+value[i].bankusername+'</li>'+
                                '<li>添加时间: '+value[i].createtime+'</li>'+
                                '</ul><div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a><ul class="dropdown-menu dropdown-menu-right">'+
                                '<li><a href="javascript:void(0);" class="edit" onclick="editBank('+value[i].id+');" >编辑</a></li><li>'+
                                '<a href="javascript:" onclick="deleteBank('+value[i].id+');" id="del-'+value[i].id+'">删除</a>'+
                                '</li></ul></div></div></div>';
                            $("#bankContainner").append(html);
                        }
                    } else {
                        notify('没有该账号。', 'danger');
                        $("#bankContainner").empty();
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });

        //关闭银行卡搜索
        $("#closeBank").click(function(){
            $.ajax({
                url : "/index.php?m=member&a=closeBank",
                dataType : 'json',
                success : function (data) {
                    console.log(data);
                    if (data.data == "success") {
                        notify('获取账号成功。', 'success');
                        var value = data.info;
                        var html = '';
                        $("#bankContainner").empty();
                        for(var i=0; i<value.length; i++){
                            html='<div class="lv-item media"><div class="checkbox pull-left m-t-12"><label>'+
                            '<input type="checkbox" id="checkbank" name="checkbank" value="'+value[i].id+'">'+
                            '<i class="input-helper"></i></label></div><div class="pull-left"><img class="lv-img-sm" src="__ROOT__/plus/img/paymethod/bank.png" alt=""></div><div class="media-body"><ul class="lv-attrs">'+
                            '<li class="bankaccount'+value[i].id+'">银行卡账号: '+value[i].bankaccount+'</li>'+
                            '<li class="bankname'+value[i].id+'">银行名称: '+value[i].bankname+'</li>'+
                            '<li class="bankusername'+value[i].id+'">用户名: '+value[i].bankusername+'</li>'+
                            '<li>添加时间: '+value[i].createtime+'</li>'+
                            '</ul><div class="lv-actions actions dropdown m-t-12"><a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a><ul class="dropdown-menu dropdown-menu-right">'+
                            '<li><a href="javascript:void(0);" class="edit" onclick="editBank('+value[i].id+');" >编辑</a></li><li>'+
                            '<a href="javascript:" onclick="deleteBank('+value[i].id+');" id="del-'+value[i].id+'">删除</a>'+
                            '</li></ul></div></div></div>';
                            $("#bankContainner").append(html);
                        }
                    }
                    return false;
                },
                error : function (xhr) {
                    notify('系统错误！', 'danger');
                    return false;
                }
            });
        });


    })
</script>

</body>
</html>