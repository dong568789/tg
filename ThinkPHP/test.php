<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>特殊他</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://www.yxgames.com/game/plus/js/Home/jquery-1.11.1.min.js"></script>
<script>
var ua = navigator.userAgent.toLowerCase();
function is_weixn_android(){
    if(ua.match(/microMessenger/i)=="micromessenger") {
    	if(is_android()){
			return true;
    	}
        return false;
    } else {
        return false;
    }  
}
function is_android(){	
    if(ua.match(/android/i)=="android") {
        return true;
    } else {
        return false;
    }
}

function GetRandomNum(Min,Max)
{   
	var Range = Max - Min;
	var Rand = Math.random();
	return(Min + Math.round(Rand * Range));
} 
 
function gotoDownPage(){
	var myurl="http://download.yxgames.com/bird/bird_yxgames.apk";
	$.ajax({ //底层方法； 
		url: "http://api.weibo.com/2/short_url/shorten.json?source=1875115052&url_long="+myurl, 
		type: "GET", 
		dataType: "jsonp", //使用JSONP方法进行AJAX,json有跨域问题； 
		cache: false, 
		async:false,
		success: function (data, status) { 
		        alert('ok');
			    //var a = GetRandomNum(1000,2000);
				//alert(a);
				//alert(status);
				//alert(data.data.error_code);
				//var nurl = data.data.urls[0].url_short.substring(7);
				//nurl ="http://p.imtt.qq.com/h?d="+a+"&u=http%3A%2F%2F"+nurl;
				//alert(nurl);
				//self.location.href=nurl;
		}, 
		error: function(obj,info,errObj){ 
		      alert("--test"+info);
		},
	}); 
}

function gotoDownload()
{
		if(is_weixn_android()){
				//gotoDownPage('http://g18.gdl.netease.com/my_netease_netease.32wan_cps_dev_1.6.0.apk ');						
				//alert("[消息]正在自动进入梦幻西游手游下载....(如未自动下载，请点击下面的Android版下载）");
				//var nurl="http://p.imtt.qq.com/h?d="+a+"&u=http://t.cn/RARFHNy";
				var nurl="http://download.yxgames.com/bird/bird_yxgames.apk";
				alert(nurl);
				self.location.href=nurl;
		}else{
				alert('IOS平台即将上线，敬请期待！');
		}
}


$(document).ready(function(){
	var myurl="http://download.yxgames.com/bird/bird_yxgames.apk";
                        $('#mylink').bind('click', function(){                                  
                            var request = $.ajax({
                                       url: "http://api.weibo.com/2/short_url/shorten.json?source=1875115052&url_long="+myurl, 
                                        type: "GET",
                                        async: false,
                                        data: _json,
                                        dataType: "json",
                                        // contentType: "charset=utf-8",
                                        cache: false,
                                        success: function (r, textStatus) {
                                            alert('ok');
                                        },
                                        error: function (XMLHttpRequest, textStatus, errorThrown) { alert(XMLHttpRequest.readyState); }
                            });
                                    // 搜索不到游戏
                                    if (request.responseText == null) {
                                                                       
                                    }
                        }); 
});

var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?d8b143739338b886eca13475731fe1ba";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();

</script>

</head>
<body>
   <h3>下载测试页面</h3>
   <a id="mylink" href="#" >download</a>
</body>
</html>