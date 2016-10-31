<!-- 引用的 Javascript Libraries -->
<!-- Jquery & Bootstrap -->
<script src="__ROOT__/plus/vendors/bower_components/jquery/dist/jquery.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Custom Scripts -->
<script src="__ROOT__/plus/vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/jquery-validate/jquery.validate.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/Waves/dist/waves.min.js"></script>
<script src="__ROOT__/plus/vendors/bootstrap-growl/bootstrap-growl.min.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.js"></script>
<script src="__ROOT__/plus/vendors/bower_components/jquery-form/jquery.form.js"></script>

<!-- Main function -->
<script src="__ROOT__/plus/js/functions.js"></script>

<script>

    //全部消息变为已读
    $(function(){
        $("#allUnreadMessage").click(function(){
            $.ajax({
                type: "POST",
                url: "/index.php?m=common&a=allMessage",
                cache: false,
                dataType: 'json',
                success: function (data) {
                    $("#messageCounts").addClass("display-none");
                    $('#messageunread').addClass("display-none");
                    $('.lv-title').css('color','#999');
                }
            });

        })
    });

</script>
