<!DOCTYPE html>
<html>
<head>
    <!-- Main CSS -->
    <link href="__ROOT__/plus/css/app.min.1.css" rel="stylesheet">
    <link href="__ROOT__/plus/css/app.min.2.css" rel="stylesheet">
    <link href="__ROOT__/plus/vendors/bootstrap-3.3.7/css/bootstrap.css" rel="stylesheet" />
    <link href="__ROOT__/plus/vendors/select2-4.0.3/dist/css/select2.min.css" rel="stylesheet" />
    <link href="__ROOT__/plus/vendors/zTree_v3/css/zTreeStyle/zTreeStyle.css" rel="stylesheet" />
    <script type="text/javascript" src="__ROOT__/plus/vendors/select2-4.0.3/vendor/jquery-2.1.0.js"></script>
    <script type="text/javascript" src="__ROOT__/plus/vendors/bootstrap-3.3.7/js/bootstrap.js"></script>
    <script src="__ROOT__/plus/vendors/layer/layer.js"></script>

    <style>
    .panel-body{padding:0px;}
    .form-horizontal .form-group {margin:0px;}
    </style>
</head>
<body>
<div class="card" id="">
    <div class="card-body card-padding">
        <ul class="tab-nav" role="tablist">
            <li class="active"><a href="#tab_home" aria-controls="tab_home" role="tab" data-toggle="tab">渠道</a></li>
           <li><a href="#tab_agent" aria-controls="tab_agent" role="tab" data-toggle="tab">用户</a></li>
        </ul>
        <div class="tab-content" style="position: relative;">
            <div role="tabpanel" class="tab-pane active" id="tab_home">
                <div style="display: none;" id="channel_ids">

                </div>
                <ul id="treeDemo" class="ztree"></ul>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_agent">
                <textarea style="width:500px;height: 200px" id="guard_user"></textarea>

                </select>
            </div>

            <div class="right-btn" style="position: absolute;bottom:5px;right: 0px;">
                <button class="btn bgm-green waves-effect guard_btn">保存</button>
            </div>
        </div>

    </div>
</div>


<script src="__ROOT__/plus/vendors/select2-4.0.3/dist/js/select2.min.js"></script>
<script type="text/javascript" src="__ROOT__/plus/vendors/zTree_v3/js/jquery.ztree.core.js"></script>
<script type="text/javascript" src="__ROOT__/plus/vendors/zTree_v3/js/jquery.ztree.excheck.js"></script>
<script type="text/javascript" src="__ROOT__/plus/js/guard.js"></script>

<script>
    $(function(){
        var method = {};
        method.removeAllChannels = function(){
            $('#channel_ids').empty();
        };
        method.removeChannel = function(id) {

            //$('input[name^="channel_id[' + wildcard+'"]', '#channel_ids').remove();
            $('input[name="channel_id['+id+']"]', '#channel_ids').remove();
        };
        method.getChannels = function() {
            var data = [];
            $('input[name^="channel_id["]').each(function() {
                data.push(this.value);
            });
            return data;
        }
        method.addChannel = function(id) {
            method.removeChannel(id);
            $('<input type="hidden" name="channel_id['+id+']" value="'+id+'">').appendTo('#channel_ids');
        };
        method.existsChannel = function(id)
        {
            return $('input[name="channel_id['+id+']"]', '#channel_ids').length > 0;
        };
        method.existsParents = function(id)
        {
            var ids = id.split('-');
            var res = 0;
            if (ids[1] != '*')
            {
                if (ids[2] != '*')
                    res |= method.existsChannel(ids[0] + '-' + ids[1] + '-*');
                res |= method.existsChannel(ids[0] + '-*-*' );
            }
            return res;
        };
        method.existsChildren = function(id)
        {
            var ids = id.split('-');
            var wildcard = id;
            if (ids[1] == '*') wildcard = ids[0] + '-';
            else if (ids[2] == '*') wildcard = ids[0] + '-' + ids[1] + '-';

            return $('input[name^="channel_id[' + wildcard+'"]', '#channel_ids').length >= 1;
        };
        method.checkChannel = function(id)
        {
            var zTree = $.fn.zTree.getZTreeObj("treeDemo");
            var root = zTree.getNodeByParam('id', id);

            if (root.getCheckStatus().checked)
                return false;
            if (method.existsChannel(id) || method.existsParents(id)) {
                zTree.checkNode(root, true, false, true);
                return true;
            }
            else if (method.existsChildren(id))
            {
                root.halfCheck = true;
                root.checked = true;
                zTree.updateNode(root);
                return null;
            }
            return false;
        };

        var setting = {
            view: {
                selectedMulti: false
            },
            check: {
                enable: true,
                autoCheckTrigger: true,
                chkboxType: { "Y": "ps", "N": "ps" }
            },
            async: {
                enable: true,
                url:"<{:U('Guard/getChannel')}>",
                autoParam:["id"],
                dataFilter: filter
            },
            callback: {
                beforeClick: beforeClick,
                beforeAsync: beforeAsync,
                onAsyncError: onAsyncError,
                onAsyncSuccess: function(event, treeId, treeNode, msg) {
                    var data = JSON.parse(msg);
                    for(var i = 0; i < data.length; ++i)
                        method.checkChannel(data[i].id);
                },
                onCheck: function(event, treeId, treeNode){
                    if(treeNode.getCheckStatus().checked)
                        method.addChannel(treeNode.id);
                    else
                    {
                        method.removeChannel(treeNode.id);
                        node = treeNode;
                        while(node != null && !!node)
                        {
                            method.removeChannel(node.id);
                            node = node.getParentNode();
                        }
                    }
                    if (treeNode.getCheckStatus().half)
                        method.removeChannel(treeNode.id);
                }
            }
        };
        var znodes = <{$treeData|json_encode}>;
        var zTree = $.fn.zTree.init($("#treeDemo"), setting, znodes);
        method.removeAllChannels();

        //$('.easyui-tabs').tableGuard({data_id:'guard_data'});

        var $gameMulti = $(".js-example-basic-multiple").select2({
            language: 'zh-CN',
            data:<{$allGame}>,
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,

        });

            $('#J_foragent').guard({
                from_table:'tg_game',
                data_append:'#addgame',
                from_id:parent.$('#sdmgameid').val()
            });

        var guardData = parent.$('#guard_data').val();
            console.log(guardData);
        if(guardData){
            // select2
            var jsonData = $.parseJSON(guardData);

            $gameMulti.val(jsonData.game_id).trigger("change");

            if(jsonData.channel_id) {
                for(var n in jsonData.channel_id) {
                    method.addChannel(jsonData.channel_id[n]);
                }
                for (var n in znodes) {
                    method.checkChannel(znodes[n].id);
                }
            }

            if(jsonData.user_id) $('#guard_user').val(jsonData.user_id.join(','));
        }


    });

   // var guardData = $('#editGuardData').val();
   // $.parseJSON(guardData);

   // $gameMulti.val(["CA", "AL"]).trigger("change");



    function filter(treeId, parentNode, childNodes) {
        if (!childNodes) return null;
        for (var i=0, l=childNodes.length; i<l; i++) {
            childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
        }
        return childNodes;
    }

    function beforeClick(treeId, treeNode) {
        if (!treeNode.isParent) {
            alert("请选择父节点");
            return false;
        } else {
            return true;
        }
    }
    var log, className = "dark";
    function beforeAsync(treeId, treeNode) {
        className = (className === "dark" ? "":"dark");
        showLog("[ "+getTime()+" beforeAsync ]&nbsp;&nbsp;&nbsp;&nbsp;" + ((!!treeNode && !!treeNode.name) ? treeNode.name : "root") );
        return true;
    }
    function onAsyncError(event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown) {
        showLog("[ "+getTime()+" onAsyncError ]&nbsp;&nbsp;&nbsp;&nbsp;" + ((!!treeNode && !!treeNode.name) ? treeNode.name : "root") );
    }


    function showLog(str) {

    }
    function getTime() {
        var now= new Date(),
                h=now.getHours(),
                m=now.getMinutes(),
                s=now.getSeconds(),
                ms=now.getMilliseconds();
        return (h+":"+m+":"+s+ " " +ms);
    }

    function refreshNode(e) {

    }

    $(document).ready(function(){
		
        $(".select2-container").css({width:'100%'});
    });
</script>
</body>
</html>