<?php
	
	class Spam
	{
		function __construct()
		{
			if(get_option('mo_wpns_enable_comment_spam_blocking') || get_option('mo_wpns_enable_comment_recaptcha'))
			{
				add_filter( 'preprocess_comment'		, array($this, 'comment_spam_check'			) );
				add_action( 'comment_form_after_fields' , array($this, 'comment_spam_custom_field'	) );
			}
		}

		function comment_spam_check( $comment_data ) 
		{
			if(!is_user_logged_in()){
			global $moWpnsUtility;
			if( isset($_POST['mocomment']) && !empty($_POST['mocomment']))
				wp_die( __( 'You are not authorised to perform this action.'));
			else if(get_option('mo_wpns_enable_comment_recaptcha'))
			{
				if(is_wp_error($moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']))))
					wp_die( __( 'Invalid captcha. Please verify captcha again.'));
			}
			return $comment_data;
		}
		else{
			return $comment_data;	
		}
		}

		function comment_spam_custom_field()
		{
			echo '<input type="hidden" name="mocomment" />';
			if(get_option('mo_wpns_enable_comment_recaptcha'))
			{
				wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
				wp_enqueue_script( 'mo2f_catpcha_js' );		
				echo '<div class="g-recaptcha" data-sitekey="'.get_option('mo_wpns_recaptcha_site_key').'"></div>';
			}
		}
	}
	new Spam;