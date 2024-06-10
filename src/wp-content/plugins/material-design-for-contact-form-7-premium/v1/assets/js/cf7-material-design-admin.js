// This closure gives access to jQuery as $
// Don't delete it
(function($) {

	// Do stuff
	$(document).ready(function(){

        //console.log(window.cf7md_html);
        
        // === Add instructions metabox === //
		if(typeof window.cf7md_html.instructions_metabox !== 'undefined') {
            var $html = $(window.cf7md_html.instructions_metabox),
                $docs_link = $html.find('.cf7md-open-docs');
            
            // Add to page
            $('#informationdiv').after($html);
            
            // Add click event to docs link
            $docs_link.click(cf7mdOpenDocs);
        }

        // === Add pro advertisement above form editor === //
        if(typeof window.cf7md_html.pro_ad !== 'undefined') {
            var $ad = $(window.cf7md_html.pro_ad),
                $close_btn = $ad.find('.notice-dismiss');

            $('#postbox-container-2').prepend($ad);

            $close_btn.click(function() {
                $('#cf7md-pro-admin').hide();
                $.post(ajaxurl, { action: 'cf7md_close_ad' }, function(response) {
                    //console.log('Close ad response: ', response)
                });
            });

            if($().slick) {
                $('.cf7md-pro-admin-slideshow').slick({
                    autoplay: true,
                    autoplaySpeed: 5000,
                    fade: true,
                    // If these were buttons they'd fail CF7's beforeunload check
                    prevArrow: '<a href="#" class="slick-prev">Previous</a>',
                    nextArrow: '<a href="#" class="slick-next">Next</a>'
                });
            }
        }

        // === Live preview plugin ad below form editor === //
        if(typeof window.cf7md_html.preview_ad !== 'undefined') {
            var $ad = $(window.cf7md_html.preview_ad);

            $('p.submit')
                .addClass('cf7md-submit-wrapper')
                .append($ad);
        }

        // === Trigger open docs tab === //
        function cf7mdOpenDocs(e) {
            //e.preventDefault();
            $('#contextual-help-link').trigger('click');
            $('#tab-link-cf7md-help > a').trigger('click');
        }

        // === Admin page slideshow === //
        if($().slick) {
            $('.cf7md-card--slideshow').slick({
                adaptiveHeight: true,
                autoplay: true,
                autoplaySpeed: 7000,
                fade: true
            });
        }

        // === Move admin notices to below hero === //
        $('.update-nag, .fs-notice, .notice, .notice-warning, .notice-error, .notice-success, .notice-info, .error, .updated').each(function(){
            $(this)
                .css('margin', '5px 0')
                .css('display', 'block')
                .detach()
                .insertBefore('.cf7md-content');
        });
		
	});

}(jQuery));