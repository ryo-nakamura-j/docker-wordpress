// This closure gives access to jQuery as $
// Don't delete it
(function($) {

	// Do stuff
	$(document).ready(function(){

        // Find fonts on the page
        var document_fonts = [],
            selectors = ['body', 'p', 'h1', 'h2', 'h3', 'h4', 'label', 'input[type="text"]'];
        
        $.each(selectors, function(index, selector){
            var $el = $(selector).first();

            if($el.length) {
                var el = $el[0],
                    font = window.getComputedStyle(el).fontFamily;

                document_fonts.push(font);
            }
        });

        // Remove duplicates
        if(typeof Array.prototype.filter === 'function'){
            document_fonts = document_fonts.filter(function(val, index, arr){
                return arr.indexOf(val) === index;
            });
        }

        // Remove the default (roboto) if it's in there
        var roboto_index = document_fonts.indexOf( 'Roboto, sans-serif')
        if(roboto_index >= 0 ) {
            document_fonts.splice(roboto_index, 1);
        }

        // Send to PHP
        set_fonts(document_fonts);
        
        // Ajax call to send fonts
        function set_fonts(fonts) {

            var postData = {
                action: 'set_fonts',
                fonts: fonts,
            }

            $.ajax({
                type: "POST",
                data: postData,
                dataType: "json",
                url: cf7md_customize.ajaxUrl,
                success: function (response) {
                    //console.log(response);
                }
            }).fail(function(data) {
                console.log(data);
            });
        }
		
	});

}(jQuery));