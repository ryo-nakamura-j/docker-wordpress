mo2f_has_tml_class = jQuery('.tml-login');
if(mo2f_has_tml_class.length){
	jQuery('form[name="login"]').removeAttr('action');
 	jQuery('form[name="login"]').removeAttr('method');
 	jQuery('form[name="login"]').attr('data-ajax','2');
 	jQuery('form[name="login"]').prepend( "<div id='mo2f_msg'></div>" );
	jQuery('#user_login').keypress(function (e) {
		if (e.which == 13) {//Enter key pressed		
		e.preventDefault();
		   mo2f_tmlajax();
		}
	});
	jQuery('#user_pass').keypress(function (e) {
		if (e.which == 13) {//Enter key pressed		
		e.preventDefault();
		   mo2f_tmlajax();
		}
	});

	jQuery('.tml' ).on( 'submit', 'form[data-ajax="2"]', function(e){	
		e.preventDefault();
	 	mo2f_tmlajax();
	});

	function mo2f_tmlajax(){
		if(typeof jQuery('#miniorange_rba_attribures').val() != 'undefined'){
			jQuery('#miniorange_rba_attribures').val(JSON.stringify(rbaAttributes.attributes));
			var rba = jQuery('#miniorange_rba_attribures').val();
		}
		else
			var rba = "";
		var data = {
			'action'					: 'mo2f_ajax',
			'mo2f_ajax_option' 			: 'mo2f_ajax_login',
			'username'					: jQuery('#user_login').val(),
			'password'					: jQuery('#user_pass').val(),
			'redirect_to'				: jQuery("input[name=redirect_to]").val(),
			'mo_softtoken'				: jQuery('#mo2f_2fa_code').val(),
      		'nonce'             		: jQuery("input[name=miniorange_login_nonce]").val(),
			'miniorange_rba_attribures' : rba,
			};
			jQuery.post(my_ajax_object.ajax_url, data, function(response) {
				
				if ( typeof response.data === "undefined") {
	                
	                jQuery("html").html(response);
	            }
	            else if ( response.data.notice ) {
	            	jQuery("#mo2f_msg").empty();
					jQuery("#mo2f_msg").append( response.data.notice ).fadeIn();
				}
	            else if(response.data.reload)
	            	location.reload( true );
	            else           
	                location.href = response.data.redirect;  
		});
	}

}