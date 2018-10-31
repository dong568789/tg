<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>九州天空城</title>
    <script type="text/javascript" src="http://img.yxgames.com/gamemobile4/games/js/flexible.js"></script>
    <link rel="stylesheet" type="text/css" href="http://img.yxgames.com/gamemobile4/games/css/jz.css?v=2"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.2/css/swiper.min.css">
</head>
<body>
<div class="warp">
    <a href="javascript:void(0);" onclick="commonDown('','<{$long_url}>')" class="download_btn"></a>
</div>
<div class="btn2">
    <a href="javascript:void(0);" onclick="commonDown('','<{$long_url}>')" class="download_btn"></a>
</div>

<div class="focus swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="http://img.yxgames.com/gamemobile4/games/images/jz/index_1.png" onerror="this.src='http://img.yxgames.com/gamemobile4/games/images/jlb/oribanner.jpg'" alt="1" width="100%" /></div>
        <div class="swiper-slide"><img src="http://img.yxgames.com/gamemobile4/games/images/jz/index_2.png" onerror="this.src='http://img.yxgames.com/gamemobile4/games/images/jlb/oribanner.jpg'" alt="1" width="100%" /></div>
        <div class="swiper-slide"><img src="http://img.yxgames.com/gamemobile4/games/images/jz/index_3.png" onerror="this.src='http://img.yxgames.com/gamemobile4/games/images/jlb/oribanner.jpg'" alt="1" width="100%" /></div>
        <div class="swiper-slide"><img src="http://img.yxgames.com/gamemobile4/games/images/jz/index_4.png" onerror="this.src='http://img.yxgames.com/gamemobile4/games/images/jlb/oribanner.jpg'" alt="1" width="100%" /></div>
        <div class="swiper-slide"><img src="http://img.yxgames.com/gamemobile4/games/images/jz/index_5.png" onerror="this.src='http://img.yxgames.com/gamemobile4/games/images/jlb/oribanner.jpg'" alt="1" width="100%" /></div>
    </div>
    <!-- 如果需要分页器 -->
    <div class="swiper-pagination"></div>
</div>
<div id="right_img_div"><img src="http://www.yxgames.com/casddj/images/321.png"  style="position:absolute;top:30px;width: 100%;" id="right_img"></div>

<script type="text/javascript" src="http://img.yxgames.com/gamemobile4/games/js/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.2/js/swiper.min.js"></script>
<script type="text/javascript" src="http://img.yxgames.com/gamemobile4/games/js/common.js"></script>

<script type="text/javascript">
    $(function(){
        // 轮播图
        var slider = new Swiper('.swiper-container', {
            speed: 500,
            slidesPerView: 2,
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
            },
        });

        $('.download_btn').click(function(){
            $.post("<{:U('source/log')}>", {sourceid:"<{$_GET['sourceid']}>"}, function(){

            });
        });
    })
</script>
</body>
</html>