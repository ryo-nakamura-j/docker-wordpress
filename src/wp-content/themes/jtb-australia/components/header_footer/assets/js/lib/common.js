/*-----------------------------------------------------------
jquery-rollover.js
jquery-opacity-rollover.js
-------------------------------------------------------------*/

/*-----------------------------------------------------------
jquery-rollover.js　※「_on」画像を作成し、class="over"を付ければOK
-------------------------------------------------------------*/

function initRollOverImages() {
    var image_cache = new Object();
    $("img.over").each(function(i) {
        var imgsrc = this.src;
        var dot = this.src.lastIndexOf('.');
        var imgsrc_on = this.src.substr(0, dot) + '_on' + this.src.substr(dot, 4);
        image_cache[this.src] = new Image();
        image_cache[this.src].src = imgsrc_on;
        $(this).hover(
            function() { this.src = imgsrc_on; },
            function() { this.src = imgsrc; });
    });
}

$(document).ready(initRollOverImages);

/*-----------------------------------------------------------
jquery-opacity-rollover.js　※class="opa"を付ければOK
-------------------------------------------------------------*/

$(document).ready(function() {
    $("img.opa").fadeTo(0, 1.0);
    $("img.opa").hover(function() {
            $(this).fadeTo(200, 0.5);
        },
        function() {
            $(this).fadeTo(200, 1.0);
        });
});

/*=====================================================
meta: {
  title: "jquery-opacity-rollover.js",
  version: "2.1",
  copy: "copyright 2009 h2ham (h2ham.mail@gmail.com)",
  license: "MIT License(http://www.opensource.org/licenses/mit-license.php)",
  author: "THE HAM MEDIA - http://h2ham.seesaa.net/",
  date: "2009-07-21"
  modify: "2009-07-23"
}
=====================================================*/
(function($) {

    $.fn.opOver = function(op, oa, durationp, durationa) {

            var c = {
                op: op ? op : 1.0,
                oa: oa ? oa : 0.2,
                durationp: durationp ? durationp : 'fast',
                durationa: durationa ? durationa : 'fast'
            };


            $(this).each(function() {
                $(this).css({
                    opacity: c.op,
                    filter: "alpha(opacity=" + c.op * 100 + ")"
                }).hover(function() {
                    $(this).fadeTo(c.durationp, c.oa);
                }, function() {
                    $(this).fadeTo(c.durationa, c.op);
                })
            });
        },

        $.fn.wink = function(durationp, op, oa) {

            var c = {
                durationp: durationp ? durationp : 'slow',
                op: op ? op : 1.0,
                oa: oa ? oa : 0.8
            };

            $(this).each(function() {
                $(this).css({
                    opacity: c.op,
                    filter: "alpha(opacity=" + c.op * 100 + ")"
                }).hover(function() {
                    $(this).css({
                        opacity: c.oa,
                        filter: "alpha(opacity=" + c.oa * 100 + ")"
                    });
                    $(this).fadeTo(c.durationp, c.op);
                }, function() {
                    $(this).css({
                        opacity: c.op,
                        filter: "alpha(opacity=" + c.op * 100 + ")"
                    });
                })
            });
        }

})(jQuery);

$(document).ready(function() {
    MenuButton();
    ToTop();
    shopBtn();
    subMenu();
    sliderMain();
    sliderReview();
    subMenuFooter();
});

$(window).on('load resize orientationchange', function() {
    $('.gridSlider').each(function() {
        var $carousel = $(this);
        /* Initializes a slick carousel only on mobile screens */
        // slick on mobile
        if ($(window).width() > 768) {
            if ($carousel.hasClass('slick-initialized')) {
                $carousel.slick('unslick');
            }
        } else {
            if (!$carousel.hasClass('slick-initialized')) {
                $carousel.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    mobileFirst: true,
                    dots: true,
                    infinite: true,
                });
            }
        }
    });

    $('.gridSlider02').each(function() {
        var $carousel = $(this);
        /* Initializes a slick carousel only on mobile screens */
        // slick on mobile
        if ($(window).width() > 768) {
            if ($carousel.hasClass('slick-initialized')) {
                $carousel.slick('unslick');
            }
        } else {
            if (!$carousel.hasClass('slick-initialized')) {
                $carousel.slick({
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    mobileFirst: true
                });
            }
        }
    });
});




var header = document.getElementById("headerWrap");


function ToTop() {
    $('.gotop a').click(function() {
        $('html, body').animate({ scrollTop: 0 }, 1000);
        return false;
    });
}


function shopBtn() {
    $(".shop__btn span").click(function(e) {
        $(".shop__tel").stop(0).slideToggle();
        e.stopPropagation();
    });

    $(".shop__btn02").click(function(e) {
        $(this).next('.submenu').stop().slideToggle();
        e.stopPropagation();
    });

    $(document).click(function() {
        $(".shop__tel").stop().hide();
        $(".shop__btn02").next('.submenu').stop().hide();

    });
}

function subMenu() {
    if ($(window).width() < 768) {
        $(".header__ul01 .sub a").click(function(e) {
            $(this).parent(".sub").toggleClass('active');
            $(this).next('.submenu').stop().slideToggle();
            e.stopPropagation();
        });
    }
}

function subMenuFooter() {
    if ($(window).width() < 768) {
    $(".footer__link .sub").click(function(event) {
        $(this).toggleClass('active');
        $(this).next('dd').stop().slideToggle();
    });}

}


function sliderMain() {
    $('.slider').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        dots: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear',
    });
}

function sliderReview() {
    $('.gridReview').slick({
        dots: true,
        slidesToShow: 2,
        slidesToScroll: 2,
        infinite: true,
        speed: 500,
        responsive: [{
            breakpoint: 767,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }]
    });
}



function MenuButton() {
    $('.menu-btn').on("click", function(e) {
        var __navi_h = $("#headerWrap").outerHeight();
        $('.header__navi').css({ 'height': 'calc(100% - ' + __navi_h + 'px)' });
        e.stopPropagation();
        $('.menu-btn').toggleClass('is-active');
        $('.header__navi').toggleClass('open').stop().slideToggle(300);
        var __cur_p = $(window).scrollTop();
        var __cur_h = $("body").outerHeight();
        if ($('.header__navi').hasClass('open')) {
            if ($(window).width() < 768) {
                $("body").attr("data-position", __cur_p);
                $("body").css({
                'position': 'fixed',
                'z-index:': '-1',
                // 'top': "-"+__cur_p+"px",
                'left': '0px',
                'touch-action':'none',
                'padding-top': '0px',
                'width': '100%',
                'height': '100%',
                'overflow': 'auto'
                });

                 if(__cur_p>0){
                   $(".header__title").hide();
                }

            }
        } else {
            if ($(window).width() < 768) {
                $("body").css({
                  "width": "100%",
                    "height": "100%",
                    "position": "static",
                    "z-index": "-1",
                    "touch-action": "auto",
                    "overflow": "auto",
                });
                $('html, body').animate({
                    scrollTop: $("body").attr("data-position")
                }, 0);
                   $(".header__title").show();
            }
        }

        /* Act on the event */
    });
}




