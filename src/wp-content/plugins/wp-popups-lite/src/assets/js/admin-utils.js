;
var wpp = {

	cachedFields: {},
	savedState: false,
	orders:  {
		fields: [],
		choices: {}
	},

	// This file contains a collection of utility functions.
	/**
	 * Start the engine.
	 *
	 * @since 2.0.0
	 */
	init: function() {
		window.wp.hooks.addAction( 'wppopupsAdminBuilderReady', 'wppopups-pro', wpp.ready, 20);
	},

	/**
	 * Document ready.
	 *
	 * @since 2.0.0
	 */
	ready: function() {

		// Load initial form saved state.
		wpp.savedState = wpp.getFormState( '#wppopups-builder-popup' );

	},

	/**
	 * Element bindings.
	 *
	 * @since 1.0.1
	 */
	bindUIActions: function() {

		// The following items should all trigger the fieldUpdate trigger.
		jQuery(document).on('wppopupsFieldAdd', wpp.setFieldOrders);
		jQuery(document).on('wppopupsFieldDelete', wpp.setFieldOrders);
		jQuery(document).on('wppopupsFieldMove', wpp.setFieldOrders);
		jQuery(document).on('wppopupsFieldAdd', wpp.setChoicesOrders);
		jQuery(document).on('wppopupsFieldChoiceAdd', wpp.setChoicesOrders);
		jQuery(document).on('wppopupsFieldChoiceDelete', wpp.setChoicesOrders);
		jQuery(document).on('wppopupsFieldChoiceMove', wpp.setChoicesOrders);
		jQuery(document).on('wppopupsFieldAdd', wpp.fieldUpdate);
		jQuery(document).on('wppopupsFieldDelete', wpp.fieldUpdate);
		jQuery(document).on('wppopupsFieldMove', wpp.fieldUpdate);
		jQuery(document).on('focusout', '.wppopups-field-option-row-label input', wpp.fieldUpdate);
		jQuery(document).on('wppopupsFieldChoiceAdd', wpp.fieldUpdate);
		jQuery(document).on('wppopupsFieldChoiceDelete', wpp.fieldUpdate);
		jQuery(document).on('wppopupsFieldChoiceMove', wpp.fieldUpdate);
		jQuery(document).on('focusout', '.wppopups-field-option-row-choices input.label', wpp.fieldUpdate);
	},

	/**
	 * Store the order of the fields.
	 *
	 * @since 1.4.5
	 */
	setFieldOrders: function() {

		wpp.orders.fields = [];

		jQuery( '.wppopups-field-option' ).each(function() {
			wpp.orders.fields.push( jQuery( this ).data( 'field-id' ) );
		});
	},

	/**
	 * Store the order of the choices for each field.
	 *
	 * @since 1.4.5
	 */
	setChoicesOrders: function() {

		wpp.orders.choices = {};

		jQuery( '.choices-list' ).each(function() {
			var fieldID = jQuery( this ).data( 'field-id' );
			wpp.orders.choices[ 'field_'+ fieldID ] = [];
			jQuery( this ).find( 'li' ).each( function() {
				wpp.orders.choices[ 'field_' + fieldID ].push( jQuery( this ).data( 'key' ) );
			});
		});
	},

	/**
	 * Return the order of choices for a specific field.
	 *
	 * @since 1.4.5
	 *
	 * @param int id Field ID.
	 *
	 * @return array
	 */
	getChoicesOrder: function( id ) {

		var choices = [];

		jQuery( '#wppopups-field-option-'+id ).find( '.choices-list li' ).each( function() {
			choices.push( jQuery( this ).data( 'key' ) );
		});

		return choices;
	},

	/**
	 * Trigger fired for all field update related actions.
	 *
	 * @since 1.0.1
	 */
	fieldUpdate: function() {

		var fields = wpp.getFields();

		jQuery(document).trigger('wppopupsFieldUpdate', [fields] );

		wpp.debug('fieldUpdate triggered');
	},

	/**
	 * Dynamically get the fields from the current form state.
	 *
	 * @since 1.0.1
	 * @param array allowedFields
	 * @param bool useCache
	 * @return object
	 */
	getFields: function( allowedFields, useCache ) {

		useCache = useCache || false;

		if ( useCache && ! jQuery.isEmptyObject(wpp.cachedFields) ) {

			// Use cache if told and cache is primed.
			var fields = jQuery.extend({}, wpp.cachedFields);

			wpp.debug('getFields triggered (cached)');

		} else {

			// Normal processing, get fields from builder and prime cache.
			var formData       = wpp.formObject( '#wppopups-field-options' ),
				fields         = formData.fields,
				fieldOrder     = [],
				fieldsOrdered  = [],
				fieldBlacklist = [ 'html', 'pagebreak' ];

			if (!fields) {
				return false;
			}

			for( var key in fields) {
				if ( ! fields[key].type || jQuery.inArray(fields[key].type, fieldBlacklist) > -1 ){
					delete fields[key];
				}
			}

			// Cache the all the fields now that they have been ordered and initially
			// processed.
			wpp.cachedFields = jQuery.extend({}, fields);

			wpp.debug('getFields triggered');
		}

		// If we should only return specfic field types, remove the others.
		if ( allowedFields && allowedFields.constructor === Array ) {
			for( var key in fields) {
				if ( jQuery.inArray( fields[key].type, allowedFields ) === -1 ){
					delete fields[key];
				}
			}
		}

		return fields;
	},

	/**
	 * Get field settings object.
	 *
	 * @since 1.4.5
	 *
	 * @param int id Field ID.
	 *
	 * @return object
	 */
	getField: function( id ) {

		var field = wpp.formObject( '#wppopups-field-option-'+id );

		return field.fields[ Object.keys( field.fields )[0] ];
	},

	/**
	 * Toggle the loading state/indicator of a field option.
	 *
	 * @since 1.2.8
	 */
	fieldOptionLoading: function(option, unload) {

		var $option = jQuery(option),
			$label  = $option.find('label'),
			unload  = (typeof unload === 'undefined') ? false : true,
			spinner = '<i class="fa fa-spinner fa-spin wppopups-loading-inline"></i>';

		if (unload) {
			$label.find('.wppopups-loading-inline').remove();
			$label.find('.wppopups-help-tooltip').show();
			$option.find('input,select,textarea').prop('disabled', false);
		} else {
			$label.append(spinner);
			$label.find('.wppopups-help-tooltip').hide();
			$option.find('input,select,textarea').prop('disabled', true);
		}
	},

	/**
	 * Get form state.
	 *
	 * @since 2.0.0
	 * @param object el
	 */
	getFormState: function( el ) {

		// Serialize tested the most performant string we can use for
		// comparisons.
		return jQuery( el ).serialize();
	},


	/**
	 * Update query string in URL.
	 *
	 * @since 2.0.0
	 */
	updateQueryString: function(key, value, url) {

		if ( ! url) {
			url = window.location.href;
		}
		var re = new RegExp( "([?&])" + key + "=.*?(&|#|$)(.*)", "gi" ),
			hash;

		if (re.test( url )) {
			if (typeof value !== 'undefined' && value !== null) {
				return url.replace( re, '$1' + key + "=" + value + '$2$3' );
			} else {
				hash = url.split( '#' );
				url  = hash[0].replace( re, '$1$3' ).replace( /(&|\?)$/, '' );
				if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
					url += '#' + hash[1];
				}
				return url;
			}
		} else {
			if (typeof value !== 'undefined' && value !== null) {
				var separator = url.indexOf( '?' ) !== -1 ? '&' : '?';
				hash          = url.split( '#' );
				url           = hash[0] + separator + key + '=' + value;
				if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
					url += '#' + hash[1];
				}
				return url;
			} else {
				return url;
			}
		}
	},

	/**
	 * Get query string in a URL.
	 *
	 * @since 2.0.0
	 */
	getQueryString: function(name) {

		var match = new RegExp( '[?&]' + name + '=([^&]*)' ).exec( window.location.search );
		return match && decodeURIComponent( match[1].replace( /\+/g, ' ' ) );
	},

	/**
	 * Is number?
	 *
	 * @since 2.0.0
	 */
	isNumber: function(n) {
		return ! isNaN( parseFloat( n ) ) && isFinite( n );
	},

	/**
	 * Empty check similar to PHP.
	 *
	 * @link http://locutus.io/php/empty/
	 * @since 2.0.0
	 */
	empty: function(mixedVar) {

		var undef;
		var key;
		var i;
		var len;
		var emptyValues = [undef, null, false, 0, '', '0'];

		for ( i = 0, len = emptyValues.length; i < len; i++ ) {
			if (mixedVar === emptyValues[i]) {
				return true;
			}
		}

		if ( typeof mixedVar === 'object' ) {
			for ( key in mixedVar ) {
				if ( mixedVar.hasOwnProperty( key ) ) {
					return false;
				}
			}
			return true;
		}

		return false;
	},

	/**
	 * Debug output helper.
	 *
	 * @since 2.0.0
	 * @param msg
	 */
	debug: function( msg ) {

		if ( wpp.isDebug() ) {
			if ( typeof msg === 'object' || msg.constructor === Array ) {
				console.log( 'WP Popups Debug:' );
				console.log( msg )
			} else {
				console.log( 'WP Popups Debug: ' + msg );
			}
		}
	},

	/**
	 * Is debug mode.
	 *
	 * @since 2.0.0
	 */
	isDebug: function() {

		return ( ( window.location.hash && '#wppopupsdebug' === window.location.hash ) || wppopups_builder.debug );
	},

	/**
	 * Initialize wppopups admin area tooltips.
	 *
	 * @since 1.4.8
	 */
	initTooltips: function() {

		jQuery( '.wppopups-help-tooltip' ).tooltipster( {
			contentAsHTML: true,
			position: 'right',
			maxWidth: 300,
			multiple: true,
			interactive: true
		} );
	},

	/**
	 * Focus the input/textarea and put the caret at the end of the text.
	 *
	 * @since 2.0.0
	 */
	focusCaretToEnd: function( el ) {
		el.focus();
		var $thisVal = el.val();
		el.val( '' ).val( $thisVal );
	},

	/**
	 * Creates a object from form elements.
	 *
	 * @since 1.4.5
	 */
	formObject: function( el ) {

		var form         = jQuery( el ),
			fields       = form.find( '[name]' ),
			json         = {},
			arraynames   = {};

		for ( var v = 0; v < fields.length; v++ ){

			var field     = jQuery( fields[v] ),
				name      = field.prop( 'name' ).replace( /\]/gi,'' ).split( '[' ),
				value     = field.val(),
				lineconf  = {};

			if ( ( field.is( ':radio' ) || field.is( ':checkbox' ) ) && ! field.is( ':checked' ) ) {
				continue;
			}
			for ( var i = name.length-1; i >= 0; i-- ) {
				var nestname = name[i];
				if ( typeof nestname === 'undefined' ) {
					nestname = '';
				}
				if ( nestname.length === 0 ){
					lineconf = [];
					if ( typeof arraynames[name[i-1]] === 'undefined' )  {
						arraynames[name[i-1]] = 0;
					} else {
						arraynames[name[i-1]] += 1;
					}
					nestname = arraynames[name[i-1]];
				}
				if ( i === name.length-1 ){
					if ( value ) {
						if ( value === 'true' ) {
							value = true;
						} else if ( value === 'false' ) {
							value = false;
						}else if ( ! isNaN( parseFloat( value ) ) && parseFloat( value ).toString() === value ) {
							value = parseFloat( value );
						} else if ( typeof value === 'string' && ( value.substr( 0,1 ) === '{' || value.substr( 0,1 ) === '[' ) ) {
							try {
								value = JSON.parse( value );
							} catch (e) {}
						} else if ( typeof value === 'object' && value.length && field.is( 'select' ) ){
				 			var new_val = {};
							for ( var i = 0; i < value.length; i++ ){
								new_val[ 'n' + i ] = value[ i ];
							}
				 		 	value = new_val;
						}
			 	 	}
			  		lineconf[nestname] = value;
				} else {
					var newobj = lineconf;
					lineconf = {};
					lineconf[nestname] = newobj;
				}
		  	}
			jQuery.extend( true, json, lineconf );
		}

		return json;
	},

	/**
	 * Sanitize HTML.
	 * Uses: `https://github.com/cure53/DOMPurify`
	 *
	 * @since 1.5.9
	 *
	 * @param {string} string HTML to sanitize.
	 *
	 * @returns {string} Sanitized HTML.
	 */
	sanitizeHTML: function( string ) {

		var purify = window.DOMPurify;

		if ( typeof purify === 'undefined' ) {
			return string;
		}

		return purify.sanitize( string, {SAFE_FOR_JQUERY: true} );
	},

};
wpp.init();
