/**
 *  Tab navigation for shortcode generator
 */
(function ($) {
    'use strict';

    $(document).ready(function(){

        $('div.sp-pc-mbf-nav a').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('.sp-pc-mbf-nav a').removeClass('nav-tab-active');
            $('.sp-pc-mbf-tab-content').removeClass('nav-tab-active');

            $(this).addClass('nav-tab-active');
            $("#"+tab_id).addClass('nav-tab-active');
        })

    });

    // Initializing WP Color Picker
    $('.sp-pc-color-picker').each(function(){
        $(this).wpColorPicker();
    });

    $('.sp-pc-select').chosen({
        width: '100%',
        allow_single_deselect: true
    });

})(jQuery);