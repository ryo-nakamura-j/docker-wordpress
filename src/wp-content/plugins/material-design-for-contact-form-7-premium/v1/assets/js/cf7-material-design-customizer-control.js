// This closure gives access to jQuery as $
// Don't delete it
(function($) {

	// Do stuff
	$(document).ready(function(){

        var customize = wp.customize;
 
        customize.previewer.bind('preview-edit', function( data ) {
            var control = customize.control( data.name );
        
            control.focus();
        });
		
	});

}(jQuery));