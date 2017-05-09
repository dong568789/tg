/**
 * Created by admin on 2017/2/22.
 */


(function($){
    $.fn.guard = function(option){

        var opts = $.extend($.fn.guard.default, option)
        var $this = $(this)


        //add window container
        if($('#J_guard').length == 0){
            $(document.body).append($.format());
        }

        //add data input
        if($('#' + opts.data_id).length == 0){

            $(opts.data_append).append($.appendInput(opts.data_id));
        }

        //get edit data
        $.getGuardData(opts);

        $this.click(function(){
            $.openGuard(opts);
        });

        $('.guard_btn').on('click',function(){
           // $.setGameData(opts.data_id);
            $.setChannelData(opts.data_id);
            $.setUserData(opts.data_id);
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index);
        });
    }

    $.openGuard = function(opt){
        layer.open({
            type: 2,
            title:'白名单',
            skin: 'layui-layer-rim', //加上边框
            area: ['790px', '540px'], //宽高
            content:"/guard/data",
        });

        if(opt.table_hide.length > 0){
            setTimeout(function(){
                var li = $('.tabs').find('li');
                for(i=0;i<opt.table_hide.length;i++){
                    li.eq(opt.table_hide[i]).hide();
                }

                $('.tabs').find('li:visible').first().trigger('click');
            },500);
        }
    }

    $.checkiframe = function(id,i){
        var iframe = $('#iframe_guard');
        var onload = false;
        if(iframe.attachEvent){
            iframe.attachEvent('onload',function(){
                onload = true;
            });
        }else{
            iframe.onload = function(){
                onload = true;
            }
        }
       // console.log(i);
        if(!onload && i < 3){
            i++;
            setTimeout($.checkiframe(id,i),'5000');
        }
        return onload;
    }

    $.setGameData = function(data_id){
        var gameid = $('#guard_game_id').val();
        if (!gameid) {
            gameid = [];
        }
        $.formatData(data_id, 'game_id', gameid)
    }

    $.setChannelData = function(data_id){
        var data = [];
        $('input[name^="channel_id["]').each(function() {
            data.push(this.value);
        });
        $.formatData(data_id, 'channel_id', data)
    }

    $.setUserData = function(data_id){
        //var user=$('#guard_user').val();
        var user = $('#guard_user').val();

        var itemuser;
        if (!user) {
            itemuser = [];
        }else{
            itemuser = user.replace('，',',').split(',');
        }
        $.formatData(data_id, 'user_id', itemuser)
    }

    $.getGuardData = function(opt){
        $('#' + opt.data_id).val('');
        console.log(opt);
        if(!opt.from_table || !opt.from_id)
            return false;

        $.ajax({
            type:'post',
            url:'?c=guard&a=getGuardData',
            data:{from_table:opt.from_table, from_id:opt.from_id},
            dataType:'json',
            async:false,
            success:function(res){
                if(res.status == 1){
                    parent.$('#' + opt.data_id).val(res.data);
                }
            }
        });
        return false;
    }

    $.formatData = function(data_id,key,d){
        var data = parent.$('#' + data_id).val();
       // console.log(data);
        var jsonData = data ? JSON.parse(data) : '';
        if(!jsonData || jsonData instanceof Array){
            jsonData = {};
        }

        jsonData[key] = d;
       // console.log(JSON.stringify(jsonData));
        parent.$('#' + data_id).val(JSON.stringify(jsonData));
    }

    $.format = function(){
        return'<div id="J_guard" style="overflow: hidden;"></div>';
    }

    $.appendInput = function(dataid){
        return'<input type="hidden" value="" name="' + dataid + '" id="' + dataid + '" />';
    }

    $.fn.guard.default = {
        from_table : 'game',
        from_id    : 0,
        data_id    : 'guard_data',
        data_append: '#fm',
        table_hide:[]
    }
})(jQuery);