<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="keyword" content="www.yxgames.com">
    <meta content="width=device-width,user-scalable=no" name="viewport">
    <meta name="HandheldFriendly" content="true">
    <meta http-equiv="x-rim-auto-match" content="none">
    <meta name="format-detection" content="telephone=no">
    <title><{$game['gamename']}></title>
    
    <link rel="stylesheet" href="__ROOT__/plus/bxslider/jquery.bxslider.css">
    <link rel="stylesheet" href="__PUBLIC__/default/css/Material/page.css">
    <script type="text/javascript" src="__ROOT__/plus/jquery/jquery-1.7.min.js"></script>
    
</head>
<body>
<div id="container">
    <div id="top">
        <if condition="$game['gamebg']">
            <img src="<{$GAMEBGURL}><{$game['gamebg']}>" class="gamebg">
        <else />
            <img src="__ROOT__/upfiles/default_gamebg.jpg" class="gamebg">
        </if>

        <img src="__PUBLIC__/default/img/triangle.png" class="triangle">
        
        <div class="top-text">
            <if condition="$game['gameicon']">
                <img src="<{$ICONURL}><{$game['gameicon']}>" class="gameicon">
            <else />
                <img src="__ROOT__/upfiles/default_gameicon.jpg" class="gameicon">
            </if>
            <ul>
                <li class="game-name">
                    <{$game['gamename']}>
                </li>
                <li class="game-score">
                    评分：
                    <for start="0" end="$yel_num"><img src="__PUBLIC__/default/img/star_yellow.png"></for><for start="0" end="$half_num"><img src="__PUBLIC__/default/img/star_half.png"></for><for start="0" end="$grey_num"><img src="__PUBLIC__/default/img/star_grey.png"></for>
                </li>
                <li class="game-type">
                    <{$game['categoryname']}>  版本：<{$game['gameversion']}>
                </li>
            </ul>
        </div>
    </div>
    <div id="content">
        <div class="games-screenshot">
            <h3 class="game-title"><span class="point">● </span>游戏截图</h3>
            <div class="division-pic-slide">
                <div class="slider1" id="img">
                    <if condition="$game['screenshot1']">
                        <div class="slide" style="">
                            <img src="<{$SCREEMSHOTURL}><{$game['screenshot1']}>" >
                        </div>
                    <else />
                        <div class="slide" style="">
                            <img src="__ROOT__/upfiles/default_screenshot.jpg" >
                        </div>
                    </if>

                    <if condition="$game['screenshot2']">
                        <div class="slide" style="">
                            <img src="<{$SCREEMSHOTURL}><{$game['screenshot2']}>" >
                        </div>
                    <else />
                        <div class="slide" style="">
                            <img src="__ROOT__/upfiles/default_screenshot.jpg" >
                        </div>
                    </if>

                    <if condition="$game['screenshot3']">
                        <div class="slide" style="">
                            <img src="<{$SCREEMSHOTURL}><{$game['screenshot3']}>"   />
                        </div>
                    <else />
                        <div class="slide" style="">
                            <img src="__ROOT__/upfiles/default_screenshot.jpg" />
                        </div>
                    </if>

                    <if condition="$game['screenshot4']">
                        <div class="slide" style="">
                            <img src="<{$SCREEMSHOTURL}><{$game['screenshot4']}>" />
                        </div>
                    <else />
                        <div class="slide" style="">
                            <img src="__ROOT__/upfiles/default_screenshot.jpg" />
                        </div>
                    </if>

                    <if condition="$game['screenshot5']">
                        <div class="slide" style="">
                            <img src="<{$SCREEMSHOTURL}><{$game['screenshot5']}>"  />
                        </div>
                    <else />
                        <div class="slide" style="">
                            <img src="__ROOT__/upfiles/default_screenshot.jpg" />
                        </div>
                    </if>
                </div>
            </div>

            <section class="division-pic-slide">
                <div class="pic" style="width: 100%;height:100%;text-align:center;top: 0;left: 0; background: rgba(0,0,0,0.7);display: none" >
                    <div class="slide" style="">
                        <img src="" class="one" id="showImg" style="width: 100%">
                    </div>
                </div>
            </section>
        </div>

        <div class="games-intro">
            <h3 class="game-title"><span class="point">● </span>游戏介绍</h3>

            <if condition="$game['description']">
                <div class="games-intro-content" id="games-intro-content">
                    <{$game['description']}>
                </div>
    
                <a href="javascript:;" id="show-more"><img src="__PUBLIC__/default/img/down_tri.png"><span>详情</span></a>
                <a href="javascript:;" id="hide-more"><img src="__PUBLIC__/default/img/up_tri.png"><span>收起</span></a>
                <div class="clear"></div>
            <else />
                <div class="games-intro-content">暂无</div>
            </if>
        </div>

         <div class="games-download">
            <a href="<{$long_url}>">下载（<{$game['gamesize']}>M）</a>
        </div>
    </div>
</div>

<script type="text/javascript" src="__ROOT__/plus/bxslider/jquery.bxslider.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // 轮播图
        $('.slider1').bxSlider({
            slideWidth: 160,
            minSlides: 1,
            maxSlides: 5,
            slideMargin: 10,
            infiniteLoop:false
        });

        // 点击轮播图的单个图片，展开大图
        $(".slide img").click(function(){
           var _h = parseInt($(window).height());

           //alert(count);
           $("#showImg").attr("src", $(this).attr('src'));
           $("#showImg").removeClass("one").addClass("active");
           $(".pic").css("position","fixed");
           $(".pic").css("z-index","999");
           $(".pic").css("display","block");
           $("body").css("height",_h);
           $(".slide").css("position","relative");
           $("#showImg").css("margin","6% auto");


           $(".pic").click(function(){
               $("#showImg").addClass("one").removeClass("active");
               $(".pic").css("display","none");
           });
        });

        // 游戏介绍显示更多
        var show_more=$('#show-more');
        var hide_more=$('#hide-more');
        var games_intro_content=$('#games-intro-content');
        show_more.live('click',function(){
            games_intro_content.css('height','auto');
            show_more.hide();
            hide_more.show();
        })
        hide_more.live('click',function(){
            games_intro_content.css('height','80px');
            show_more.show();
            hide_more.hide();
        })
    })
</script>
</body>
</html>
