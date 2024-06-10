jQuery(window).bind('load', function()

{	
	var list, index;
	jQuery('form[class="lwa-form"]').prepend( "<div id='mo2f_msg'></div>" );

	login = document.getElementById("lwa_wp-submit");
	if(login===null)
	return;
	password = document.getElementsByName('pwd')[0];
	mo2f_name = document.getElementsByName('log')[0];
	login.setAttribute('type', 'button');
    login.setAttribute('id','mo2f_lwa_login');
	password.setAttribute('id','mo2f_lwa_user_pass');
	mo2f_name.setAttribute('id','mo2f_lwa_user_name');
	password.setAttribute('name','mo2f_lwa_user_pass');
	mo2f_name.setAttribute('name','mo2f_lwa_user_name');

	jQuery('#mo2f_lwa_login').click(function(){
		mo2f_lwa_login();
	})

	jQuery('#mo2f_lwa_user_pass').keypress(function (e) {
		if (e.which == 13) {//Enter key pressed		
		e.preventDefault();
		   mo2f_lwa_login();
		}
	});

	jQuery('#mo2f_2fa_code').keypress(function (e) {
		if (e.which == 13) {//Enter key pressed		
		e.preventDefault();
		   mo2f_lwa_login();
		}
	});

	function mo2f_lwa_login()
	{
		var data;
		jQuery("#mo2f_msg").empty();
		data = {
			'action'                    : 'mo_two_factor_ajax',
			'mo_2f_two_factor_ajax' 	: 'mo2f_ajax_login_redirect',
			'username'					: jQuery('#mo2f_lwa_user_name').val(),
			'nonce'             		: jQuery("input[name=miniorange_login_nonce]").val(),
			'password'					: jQuery('#mo2f_lwa_user_pass').val(),
			'mo_softtoken'				: jQuery('#mo2f_2fa_code').val(),
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
	jQuery('#mo2f_select_2fa_methods_form').submit();
	}
});
