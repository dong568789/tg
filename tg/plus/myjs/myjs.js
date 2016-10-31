/*
使元素居中
obj，该元素的jquery对象
obj必须有宽度和高度
*/
function element_center_window(obj) {
	var width=obj.innerWidth();
	var height=obj.height();
	var _window=$(window);
	var win_width=_window.width();
	var win_height=_window.height();
	var left=(win_width-width)/2.00;
	var _top=(win_height-height)/2.20;
	obj.css({'position':'fixed','left':left,'top':_top});
}

//点击此元素，显示某个块；点击除此元素以外的任何地方，隐藏某个块
//click_element 点击的元素；show_element 显示的元素
function click_show_box(click_tag,show_tag,is_droplist) {
	$(click_tag).click(function(event){
		if(is_droplist){
			//其他下拉列表的去掉
			$('.my_droplist ul').hide();
		}
		$(show_tag).show();
    	event.stopPropagation();
    })

	$(document).click(function(){
    	$(show_tag).hide();
	}); 
}

function click_show_box2(click_element,show_element,is_droplist){
	click_show_box(click_element,show_element,is_droplist);
	click_show_box(show_element,show_element,is_droplist);
}

/*
浮上去显示，离开隐藏
hover_tag 显示元素和浮上去元素的父元素的字符串
show_tag   显示元素的字符串
注意：hover_tag必须还是show_tag的父元素
例子：
<div class="block">
	<div class="title">点击</div>
	<div class="content">显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容显示的内容</div>
</div>
调用：
hover_show_box('.block','.content');
*/
function hover_show_box(hover_tag,show_tag){
	$(hover_tag).mouseover(function(){
	    $(show_tag).show();
	}).mouseout(function(){
	    $(show_tag).hide();
	});
}

/*
弹窗
window_str 窗口的选择器，例如：#notify_block
ok_fun   点击确定按钮的执行函数
no_fun   点击取消或者关闭按钮的执行函数
is_reset_width   是否重置该窗口的宽度，如果是，为1，对于手机站的应用
例子：
<span id="alert">点击弹窗</span>
<div id="window_block" class="wbox">
    <h1>
        <em>评估简历</em>
        <span class="wclose" title="关闭">ｘ</span>
    </h1>
    <div class="overf">
        <a href="{:U('Register/index')}">在线注册简历</a>
        <a href="javascript:;" id="up_resume">直接上传简历</a>
    </div>
    <div class="wfooter">
        <span class="wquxiao mybutton">取消</span>
        <span class="wok mybutton">确定</span>
    </div>  
</div>
调用：
$('#alert').click(function(){
	my_alert_window1({
		'window_str':'#window_block',
	});
})
*/
function my_alert_window1(config_outer){
    var config={
        'window_str':'#notify_block',
        'ok_fun':'',
        'no_fun':'',
        'is_reset_width':'',
        'okis_hide_window':true,
    };
    config=$.extend(config,config_outer);

    var window_str=config.window_str;
    var ok_fun=config.ok_fun;
    var no_fun=config.no_fun;
    var is_reset_width=config.is_reset_width;
    var okis_hide_window=config.okis_hide_window;

	var window_obj=$(window_str);

	//是否重置该窗口的宽度，对于手机站的应用
	if(is_reset_width){
		var win_width=$(window).width()*0.8;
		window_obj.width(win_width);
	}

	//弹窗
    $('body').append('<div class="wbox_bg"></div>');
    element_center_window(window_obj);
    window_obj.show();

    //点击弹窗的关闭、取消按钮
    $(window_str+' .wquxiao,'+window_str+' .wclose').click(function(){
        $('.wbox').hide();
        $('.wbox_bg').fadeOut('1000');
        if(no_fun){
        	no_fun();
        }
    });

    //点击详细介绍完成按钮
    $(window_str+' .wok').click(function(){
    	if(ok_fun){
        	ok_fun();
        }
        if(okis_hide_window){
        	$('.wbox').hide();
        	$('.wbox_bg').fadeOut('1000');
        }
    })
}

/*---
自定义下拉列表
例子：
<div class="my_droplist" id="job_category">
	<div class="xiala">
		<input type="text" name="hangye_name" value="" readonly="readonly"/>
		<input type="hidden" name="hangye_id" value="" />
		<img src="__PUBLIC__/default/image/search_sanjiao.png" class="xia_tri">
	</div>
	<ul>
		<if condition="$zhiwei_type_arr">
			<foreach name="zhiwei_type_arr" item="hv">
				<li><a href="javascript:;" hangye_id="{$hv.id}">{$hv.name}</a></li>
			</foreach>
		</if>
	</ul>	
</div>
调用：
my_droplist('job_category','hangye_id','hangye_id','hangye_name');
--*/
function my_droplist(id,a_key,input_key,input_value) {
	//显示下拉列表
	click_show_box2('#'+id+' .xiala','#'+id+' ul','1');

	//赋值
	$('#'+id+' ul li a').click(function(event) {
		var _value=$(this).text();
		var _key=$(this).attr(a_key);
		$('#'+id+' .xiala input[name="'+input_key+'"]').val(_key);
		$('#'+id+' .xiala input[name="'+input_value+'"]').val(_value);
		$('#'+id+' ul').hide();
		$('#'+id+' .xiala input[name="'+input_value+'"]').focus();
		event.stopPropagation();
	});
}

//表单等于默认值清空，为空的时候还原默认值
//传递元素obj
function clear_one_element(obj,default_value){
	obj.click(function(){
		var _this=$(this);
		if(!default_value){
			default_value=this.defaultValue;
		}
		if(_this.val()==default_value){
			_this.val("");
		}
	}).blur(function(){
		var _this=$(this);
		if(!default_value){
			default_value=this.defaultValue;
		}
		if(_this.val()==''){
			_this.val(default_value);
		}
	});
}


// 倒计时相关的-----------------------------------------
//获取单个的倒计时
//end_time是结束时间的php的时间戳
//current_par是单个倒计时父元素
/*
<div class="timerbg">
	<input type="hidden" name="end_time" value="{$v.fastjob_end_time}" />
    <div class="daoend" style="display:none;">你来晚了，职位招满了</div>
    <div class="dao">
        <strong class="RemainD"></strong>天
        <strong class="RemainH"></strong>时
        <strong class="RemainM"></strong>分
        <strong class="RemainS"></strong>秒
    </div>
</div>
*/
function get_countdown(end_time,current_par){	
	var date1 = new Date();  //开始时间
 	// var date2 = new Date(2016, 1, 1,0,0);     //结束时间
 	// var nMS = date2.getTime() - date1.getTime();   
 	var date2 = parseInt(end_time)*1000;     //结束时间
  	var nMS = date2 - date1.getTime();
  	// alert(date1.getTime());
  	// alert(date2);
	var nD = Math.floor(nMS/(1000 * 60 * 60 * 24));
	var nH = Math.floor(nMS/(1000*60*60)) % 24;
	var nM = Math.floor(nMS/(1000*60)) % 60;
	var nS = Math.floor(nMS/1000) % 60;
	if (nMS < 0){
		current_par.find(".dao").hide();
		current_par.find(".daoend").show();
	}else{
	   current_par.find(".dao").show();
	   current_par.find(".daoend").hide();
	   current_par.find(".RemainD").text(nD);
	   current_par.find(".RemainH").text(nH);
	   current_par.find(".RemainM").text(nM);
	   current_par.find(".RemainS").text(nS); 
	}
}

// 执行单个倒计时
function do_countdown(end_time,current_par){
	window.setInterval(function(){
        get_countdown(end_time,current_par);
    }, 1000);
}

//获取列表倒计时
function get_list_countdown(){
	var end_time='';
	$.each($('input[name=end_time]'),function(i,val){ 
		var current_ele=$(val);
		end_time=current_ele.val();
		current_par=current_ele.parent();
		get_countdown(end_time,current_par);
	})
}

// 执行列表倒计时
function do_list_countdown(end_time,current_par){
	window.setInterval('get_list_countdown()', 1000);
}
// ---------------------------------------------------------------------------------