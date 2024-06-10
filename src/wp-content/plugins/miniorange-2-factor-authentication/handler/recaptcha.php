<?php

	class mo2f_ReCaptcha
	{
		function __construct()
		{
			add_filter( 'admin_init' 	, array($this, 'handle_recaptcha'     ), 11, 3 	);
			add_action( 'login_form' 	, array($this, 'custom_login_fields'  )			);
			add_action( 'register_form' , array($this, 'register_with_captcha')			);
			add_action( 'woocommerce_register_form' , array($this, 'woocommerce_register_with_captcha'));
			add_action( 'woocommerce_login_form', array($this, 'woocommerce_login_with_captcha'));
			add_action( 'woocommerce_review_order_before_submit', array($this, 'woocommerce_register_with_captcha_checkout'));
		}


		//Function to handle Testing reCaptcha
		function handle_recaptcha()
		{
			global $moWpnsUtility,$mo2f_dirName;
			if (current_user_can( 'manage_options' ))
			{ 
				if(isset($_REQUEST['option']) && sanitize_text_field($_REQUEST['option'])=='testrecaptchaconfig')
				{	
					if(array_key_exists('g-recaptcha-response',$_POST))
					{
						$userIp 	= $moWpnsUtility->get_client_ip();
						$userIp = sanitize_text_field( $userIp );
						$mocURL 	= new MocURL;
						$response 	= $mocURL->validate_recaptcha($userIp,sanitize_text_field($_POST['g-recaptcha-response']));
						$content	= json_decode($response, true);
						if(isset($content['error-codes']) && in_array("invalid-input-secret", $content['error-codes']))
							echo "<br><br><h2 style=color:red;text-align:center>Invalid Secret Key.</h2>";
						else if(isset($content['success']) && $content['success']==1)
							echo "<br><br><h2 style=color:green;text-align:center>Test was successful and captcha verified.</h2>";
						else
							echo "<br><br><h2 style=color:red;text-align:center>Invalid captcha. Please try again.</h2>";
					}
					show_google_recaptcha_form_v2();
				}
				
				if(isset($_REQUEST['option']) && sanitize_text_field($_REQUEST['option'])=='testrecaptchaconfig3')
				{
					if(array_key_exists('g-recaptcha-response',$_REQUEST))
					{
						
						$mocURL 	= new MocURL;
						$response 	= $mocURL->get_Captcha_v3($_REQUEST['g-recaptcha-response']);
					    $content	= json_decode($response, true);
						if(isset($content['error-codes']) && in_array("invalid-input-secret", $content['error-codes']))
							echo "<br><br><h2 style=color:red;text-align:center>Invalid Secret Key.</h2>";
						else if(isset($content['success']) && $content['success']==1)
						{
							if($content['success'] == 1)
							{
								if($content['score']>=0.9)
								{
									echo "<br><br><h2 style=color:green;text-align:center>Welcome!</h2>";
									echo "<h2 style=color:green;text-align:center>Test was successful and captcha verified.</h2>";
								}
								else echo "<br><br><h2 style=color:red;text-align:center>Captcha verification failed! Permission denied.</h2>";
							}
						}
						else
							echo "<br><br><h2 style=color:red;text-align:center>Invalid captcha. Please try again.</h2>";
					}
					show_google_recaptcha_form_v3();
				}
			}
		}


	function custom_login_fields()
	{
		global $moWpnsUtility,$mo2f_dirName;
		if(get_option('mo_wpns_activate_recaptcha_for_login') && MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option'))
		{
			if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v3')                
				show_google_recaptcha_form_v3_login();
			else if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v2')
			{		
				wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
				wp_enqueue_script( 'mo2f_catpcha_js' );
				
				echo '<div class="g-recaptcha" data-sitekey="'.esc_html(get_option("mo_wpns_recaptcha_site_key")).'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#loginform{padding-bottom:20px;}</style>';
			}
		}
	}


	function register_with_captcha(){
		global $moWpnsUtility,$mo2f_dirName;
		if(get_option('mo_wpns_activate_recaptcha_for_registration'))
		{
			if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v3')
				show_google_recaptcha_form_v3_login();
			else if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v2')
				show_google_recaptcha_form_v2_login();
		}
	}

    function woocommerce_register_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration'))
			{
				wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
				wp_enqueue_script( 'mo2f_catpcha_js' );				
				echo '<div class="g-recaptcha" data-sitekey="'.esc_html(get_option("mo_wpns_recaptcha_site_key")).'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
			}
		}
		
	function woocommerce_login_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_login'))
			{
				
				wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
				wp_enqueue_script( 'mo2f_catpcha_js' );						     
				echo '<div class="g-recaptcha" data-sitekey="'.esc_html(get_option("mo_wpns_recaptcha_site_key")).'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#loginform{padding-bottom:20px;}</style>';
			}
		}
	
	function woocommerce_register_with_captcha_checkout(){
			
			if (!is_user_logged_in()){
				if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration'))
				{
					wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
					wp_enqueue_script( 'mo2f_catpcha_js' );		
					echo '<div class="g-recaptcha" data-sitekey="'.esc_html(get_option("mo_wpns_recaptcha_site_key")).'"></div>';
					echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
				}
			}
		}
		
	public static function recaptcha_verify($response)
		{
			global $moWpnsUtility;
			$userIp 	= $moWpnsUtility->get_client_ip();
			$userIp = sanitize_text_field( $userIp );
			$mocURL 	= new MocURL;
			$response 	= $mocURL->validate_recaptcha($userIp,$response);
			$content	= json_decode($response, true);
			$isvalid 	= isset($content['success']) && $content['success']==1 ? true : false;
			return $isvalid;
		}

	public static function recaptcha_verify_3($response)
	{
		global $moWpnsUtility;
		$userIp 	= $moWpnsUtility->get_client_ip();
		$userIp = sanitize_text_field( $userIp );
		$mocURL 	= new MocURL;
		$response 	= $mocURL->get_Captcha_v3($response);
		$content	= json_decode($response, true);
		$isvalid 	= isset($content['success']) && $content['success']==1 && $content['score']>=0.9? true : false;
		return $isvalid;
	}

	}
	new mo2f_ReCaptcha;
