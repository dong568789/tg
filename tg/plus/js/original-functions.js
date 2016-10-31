/*
 * Detact Mobile Browser
 */
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
   $('html').addClass('ismobile');
}

$(window).load(function () {
    /* --------------------------------------------------------
     Page Loader
     -----------------------------------------------------------*/
    if(!$('html').hasClass('ismobile')) {
        if($('.page-loader')[0]) {
            setTimeout (function () {
                $('.page-loader').fadeOut();
            }, 500);

        }
    }
})

$(document).ready(function(){
    
    /* --------------------------------------------------------
        Scrollbar
    -----------------------------------------------------------*/
    function scrollBar(selector, theme, mousewheelaxis) {
        $(selector).mCustomScrollbar({
            theme: theme,
            scrollInertia: 100,
            axis:'yx',
            mouseWheel: {
                enable: true,
                axis: mousewheelaxis,
                preventDefault: true
            }
        });
    }

    if (!$('html').hasClass('ismobile')) {
        //On Custom Class
        if ($('.c-overflow')[0]) {
            scrollBar('.c-overflow', 'minimal-dark', 'y');
        }
    }

    /*
     * Top Search
     */
    (function(){
        $('body').on('click', '#top-search > a', function(e){
            e.preventDefault();

            $('#header').addClass('search-toggled');
            $('#top-search-wrap input').focus();
        });

        $('body').on('click', '#top-search-close', function(e){
            e.preventDefault();

            $('#header').removeClass('search-toggled');
        });
    })();

    /*
     * Sidebar
     */
    (function(){
        //Toggle
        $('body').on('click', '#menu-trigger, #chat-trigger', function(e){
            e.preventDefault();
            var x = $(this).data('trigger');

            $(x).toggleClass('toggled');
            $(this).toggleClass('open');

    	    //Close opened sub-menus
    	    $('.sub-menu.toggled').not('.active').each(function(){
        		$(this).removeClass('toggled');
        		$(this).find('ul').hide();
    	    });



	    $('.profile-menu .main-menu').hide();

            if (x == '#sidebar') {

                $elem = '#sidebar';
                $elem2 = '#menu-trigger';

                $('#chat-trigger').removeClass('open');

                if (!$('#chat').hasClass('toggled')) {
                    $('#header').toggleClass('sidebar-toggled');
                }
                else {
                    $('#chat').removeClass('toggled');
                }
            }

            if (x == '#chat') {
                $elem = '#chat';
                $elem2 = '#chat-trigger';

                $('#menu-trigger').removeClass('open');

                if (!$('#sidebar').hasClass('toggled')) {
                    $('#header').toggleClass('sidebar-toggled');
                }
                else {
                    $('#sidebar').removeClass('toggled');
                }
            }

            //When clicking outside
            if ($('#header').hasClass('sidebar-toggled')) {
                $(document).on('click', function (e) {
                    if (($(e.target).closest($elem).length === 0) && ($(e.target).closest($elem2).length === 0)) {
                        setTimeout(function(){
                            $($elem).removeClass('toggled');
                            $('#header').removeClass('sidebar-toggled');
                            $($elem2).removeClass('open');
                        });
                    }
                });
            }
        })

        //Submenu
        $('body').on('click', '.sub-menu > a', function(e){
            e.preventDefault();
            $(this).next().slideToggle(200);
            $(this).parent().toggleClass('toggled');
        });
    })();

    /*
     * Clear Notification
     */
    $('body').on('click', '[data-clear="notification"]', function(e){
      e.preventDefault();

      var x = $(this).closest('.listview');
      var y = x.find('.lv-item');
      var z = y.size();

      $(this).parent().fadeOut();

      x.find('.list-group').prepend('<i class="grid-loading hide-it"></i>');
      x.find('.grid-loading').fadeIn(1500);


      var w = 0;
      y.each(function(){
          var z = $(this);
          setTimeout(function(){
          z.addClass('animated fadeOutRightBig').delay(1000).queue(function(){
              z.remove();
          });
          }, w+=150);
      })

	//Popup empty message
	setTimeout(function(){
	    $('#notifications').addClass('empty');
	}, (z*150)+200);
    });

    /*
     * Dropdown Menu
     */
    if($('.dropdown')[0]) {
	//Propagate
	$('body').on('click', '.dropdown.open .dropdown-menu', function(e){
	    e.stopPropagation();
	});

	$('.dropdown').on('shown.bs.dropdown', function (e) {
	    if($(this).attr('data-animation')) {
		$animArray = [];
		$animation = $(this).data('animation');
		$animArray = $animation.split(',');
		$animationIn = 'animated '+$animArray[0];
		$animationOut = 'animated '+ $animArray[1];
		$animationDuration = ''
		if(!$animArray[2]) {
		    $animationDuration = 500; //if duration is not defined, default is set to 500ms
		}
		else {
		    $animationDuration = $animArray[2];
		}

		$(this).find('.dropdown-menu').removeClass($animationOut)
		$(this).find('.dropdown-menu').addClass($animationIn);
	    }
	});

	$('.dropdown').on('hide.bs.dropdown', function (e) {
	    if($(this).attr('data-animation')) {
    		e.preventDefault();
    		$this = $(this);
    		$dropdownMenu = $this.find('.dropdown-menu');

    		$dropdownMenu.addClass($animationOut);
    		setTimeout(function(){
    		    $this.removeClass('open')

    		}, $animationDuration);
    	    }
    	});
    }




    /*
     * Auto Hight Textarea
     */
    if ($('.auto-size')[0]) {
	   autosize($('.auto-size'));
    }

    /*
    * Profile Menu
    */
    $('body').on('click', '.profile-menu > a', function(e){
        e.preventDefault();
        $(this).parent().toggleClass('toggled');
	    $(this).next().slideToggle(200);
    });

    /*
     * Text Feild
     */

    //Add blue animated border and remove with condition when focus and blur
    if($('.fg-line')[0]) {
        $('body').on('focus', '.fg-line .form-control', function(){
            $(this).closest('.fg-line').addClass('fg-toggled');
        })

        $('body').on('blur', '.form-control', function(){
            var p = $(this).closest('.form-group, .input-group, .input-group-float');
            var i = p.find('.form-control').val();

            if (p.hasClass('fg-float')) {
                if (i.length == 0) {
                    $(this).closest('.fg-line').removeClass('fg-toggled');
                }
            }
            else {
                $(this).closest('.fg-line').removeClass('fg-toggled');
            }
        });
    }

    //Add blue border for pre-valued fg-flot text feilds
    if($('.fg-float')[0]) {
        $('.fg-float .form-control').each(function(){
            var i = $(this).val();

            if (!i.length == 0) {
                $(this).closest('.fg-line').addClass('fg-toggled');
            }

        });
    }

    /*
     * Audio and Video
     */
    if($('audio, video')[0]) {
        $('video,audio').mediaelementplayer();
    }

    /*
     * Tag Select
     */
    if($('.chosen')[0]) {
        $('.chosen').chosen({
            width: '100%',
            allow_single_deselect: true
        });
    }

    /*
     * Input Slider
     */
    //Basic
    if($('.input-slider')[0]) {
        $('.input-slider').each(function(){
            var isStart = $(this).data('is-start');

            $(this).noUiSlider({
                start: isStart,
                range: {
                    'min': 0,
                    'max': 100,
                }
            });
        });
    }

    //Air Mode
    if($('.html-editor-airmod')[0]) {
        $('.html-editor-airmod').summernote({
            airMode: true
        });
    }




    /*
     * Waves Animation
     */
    (function(){
         Waves.attach('.btn:not(.btn-icon):not(.btn-float)');
         Waves.attach('.btn-icon, .btn-float', ['waves-circle', 'waves-float']);
        Waves.init();
    })();

    /*
     * Lightbox
     */
    if ($('.lightbox')[0]) {
        $('.lightbox').lightGallery({
            enableTouch: true
        });
    }

    /*
     * Link prevent
     */
    $('body').on('click', '.a-prevent', function(e){
        e.preventDefault();
    });

    /*
     * Collaspe Fix
     */
    if ($('.collapse')[0]) {

        //Add active class for opened items
        $('.collapse').on('show.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });

        $('.collapse').on('hide.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').removeClass('active');
        });

        //Add active class for pre opened items
        $('.collapse.in').each(function(){
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });
    }

    /*
     * Tooltips
     */
    if ($('[data-toggle="tooltip"]')[0]) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    /*
     * Popover
     */
    if ($('[data-toggle="popover"]')[0]) {
        $('[data-toggle="popover"]').popover();
    }

    /*
     * Message
     */

    //Actions
    if ($('.on-select')[0]) {
        var checkboxes = '.lv-avatar-content input:checkbox';
        var actions = $('.on-select').closest('.lv-actions');

        $('body').on('click', checkboxes, function() {
            if ($(checkboxes+':checked')[0]) {
                actions.addClass('toggled');
            }
            else {
                actions.removeClass('toggled');
            }
        });
    }

    if($('#ms-menu-trigger')[0]) {
        $('body').on('click', '#ms-menu-trigger', function(e){
            e.preventDefault();
            $(this).toggleClass('open');
            $('.ms-menu').toggleClass('toggled');
        });
    }

    /*
     * Login
     */
    if ($('.login-content')[0]) {
        //Add class to HTML. This is used to center align the logn box
        $('html').addClass('login-content');

        $('body').on('click', '.login-navigation > li', function(){
            var z = $(this).data('block');
            var t = $(this).closest('.lc-block');

            t.removeClass('toggled');

            setTimeout(function(){
                $(z).addClass('toggled');
            });

        })
    }

    /*
     * Fullscreen Browsing
     */
    if ($('[data-action="fullscreen"]')[0]) {
	var fs = $("[data-action='fullscreen']");
	fs.on('click', function(e) {
	    e.preventDefault();

	    //Launch
	    function launchIntoFullscreen(element) {

		if(element.requestFullscreen) {
		    element.requestFullscreen();
		} else if(element.mozRequestFullScreen) {
		    element.mozRequestFullScreen();
		} else if(element.webkitRequestFullscreen) {
		    element.webkitRequestFullscreen();
		} else if(element.msRequestFullscreen) {
		    element.msRequestFullscreen();
		}
	    }

	    //Exit
	    function exitFullscreen() {

		if(document.exitFullscreen) {
		    document.exitFullscreen();
		} else if(document.mozCancelFullScreen) {
		    document.mozCancelFullScreen();
		} else if(document.webkitExitFullscreen) {
		    document.webkitExitFullscreen();
		}
	    }

	    launchIntoFullscreen(document.documentElement);
	    fs.closest('.dropdown').removeClass('open');
	});
    }









   

});
