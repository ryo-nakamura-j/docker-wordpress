// This closure gives access to jQuery as $
// Don't delete it
(function($) {

	// Do stuff
	$(document).ready(function(){

        var customize = wp.customize,
            selectors = cf7md_selectors,
            $stylesheet = $('#cf7-material-design-inline-css'),
            $customStylesheet = $('<style type="text/css"></style>'),
            internalStyles;

        // Find or create the internal stylesheet
        if( !$stylesheet.length ) {
            $stylesheet = $('<style type="text/css" id="cf7-material-design-inline-style"></style>');
            $stylesheet.insertAfter($('#cf7-material-design-css'));
        }
        // Set internalStyles var
        internalStyles = $.grep(document.styleSheets, function(el, i){
            return el.ownerNode === $stylesheet[0];
        })[0];

        // === Options === //

        // Use custom styles
        customize( 'cf7md_options[use_custom_styles]', function( value ) {
            var func = function(newval) {
                $stylesheet.attr('disabled', !newval);
            }
            func( value.get() );
            value.bind( func );
        } );

        // Custom css
        $customStylesheet.insertAfter($stylesheet);
        customize( 'cf7md_options[custom_css]', function( value ) {
            var func = function(newval) {
                $customStylesheet.text(newval);
            }
            func( value.get() );
            value.bind( func );
        } );

        // Selectors
        $.each(selectors, function(prop, val){
            customize( 'cf7md_options[' + prop + ']', function( value ) {
                var func = function(newval) {
                    $.each(val, function(index, selector){
                        // If the value has been set to blank, delete existing rules that reference it
                        if(newval === '') {
                            var rules = internalStyles.rules
                            for(var r=0; r<rules.length; r++) {
                                if(rules[r].selectorText === selector.selector) {
                                    internalStyles.deleteRule(r);
                                    r--; // deleting rule affects future indexes
                                }
                            }
                        }
                        if(selector.property === 'font-size') {
                            newval = newval + 'px';
                        }
                        if(prop === 'text_hint_on_light' || prop === 'text_on_light') {
                            newval = 'rgba(0, 0, 0, ' + newval + ')';
                        } else if(prop === 'text_hint_on_dark' || prop === 'text_on_dark') {
                            newval = 'rgba(255, 255, 255, ' + newval + ')';
                        }
                        internalStyles.insertRule(selector.selector + ' { ' + selector.property + ': ' + newval + '; }' , internalStyles.rules.length);
                    });
                }
                func( value.get() );
                value.bind( func );
            });
        });
        
        // === Misc === //

        // Open section on button click
        $('body').on('click', '.customizer-edit', function(e) {
            e.preventDefault();
            customize.preview.send( 'preview-edit', $(this).data('control') );
        });
		
	});

}(jQuery));