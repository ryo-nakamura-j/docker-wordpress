<?php
	
	class FeaturePluginInterface
	{
		function __construct()
		{
			if ( ! function_exists( 'get_plugins' ) )
				require_once ABSPATH . 'wp-admin/includes/plugin.php';	
		}
	}


	class TwoFAPlugin extends FeaturePluginInterface
	{	

		function getstatus()
		{
		    $all_plugins = get_plugins();
			$status 	 = 'NOT_INSTALLED';
			if(isset($all_plugins[MoWpnsConstants::TWO_FACTOR_SETTINGS]))
				$status = is_plugin_active(MoWpnsConstants::TWO_FACTOR_SETTINGS) ? 'ACTIVE' : 'INSTALLED';
			return $status;
		}

		
		function updatePluginConfiguration()
		{
			if(!get_option('mo_wpns_enable_2fa'))
				return;

			if(!get_option( 'mo2f_customerKey') || !get_option( 'mo2f_api_key') || !get_option( 'mo2f_customer_token') || !get_option( 'mo2f_app_secret'))
			{
				global $mo2f_dirName;
				$current_user = wp_get_current_user();
				$mo2fa 		  = new Two_Factor_Setup();
				update_option( 'mo2f_email'				,get_option( 'mo2f_email'));
				update_option( 'mo2f_host_name' 		,MoWpnsConstants::HOST_NAME);
				update_option( 'mo2f_phone'				,get_option( 'mo_wpns_admin_phone'));
				update_option( 'mo2f_customerKey'		,get_option( 'mo2f_customerKey'));
				update_option( 'mo2f_api_key'			,get_option( 'mo2f_api_key'));
				update_option( 'mo2f_customer_token'	,get_option( 'mo2f_customer_token'));
				update_option( 'mo2f_app_secret'		,get_option( 'mo2f_app_secret'));
				update_option( 'mo2f_miniorange_admin'	,$current_user->ID);
				update_option( 'mo2f_new_customer'		,true);

				update_option( 'mo_2factor_admin_registration_status','MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS');
				update_user_meta($current_user->ID,'mo_2factor_user_registration_with_miniorange','SUCCESS');
				update_user_meta($current_user->ID,'mo_2factor_map_id_with_email',get_option( 'mo2f_email'));
				update_user_meta($current_user->ID,'mo_2factor_user_registration_status','MO_2_FACTOR_PLUGIN_SETTINGS');
				$mo2fa->mo2f_update_userinfo(get_user_meta($current_user->ID,'mo_2factor_map_id_with_email',true), 'OUT OF BAND EMAIL',null,'API_2FA',true);
				update_user_meta($current_user->ID,'mo2f_email_verification_status',true);
			}
		}
	}


	class OTPPlugin extends FeaturePluginInterface
	{
		function getstatus()
		{
		    $all_plugins = get_plugins();
			$status = 'NOT_INSTALLED';
			if(isset($all_plugins[MoWpnsConstants::OTP_VERIFICATION_SETTINGS]))
				$status = is_plugin_active(MoWpnsConstants::OTP_VERIFICATION_SETTINGS) ? 'ACTIVE' : 'INSTALLED';
			return $status;
		}

		function updatePluginConfiguration()
		{
			if(!get_option('mo_wpns_enable_advanced_user_verification'))
				return;

			if(!get_option( 'mo_customer_validation_admin_email') || !get_option( 'mo_customer_validation_admin_customer_key') || !get_option( 'mo_customer_validation_admin_api_key') || !get_option( 'mo_customer_validation_customer_token'))
			{
				update_option( 'mo_customer_validation_wp_default_enable'	,1);
				update_option( 'mo_customer_validation_admin_email'			,get_option( 'mo2f_email'));
				update_option( 'mo_customer_validation_admin_phone'			,get_option( 'mo_wpns_admin_phone'));
				update_option( 'mo_customer_validation_admin_customer_key'	,get_option( 'mo2f_customerKey') );
				update_option( 'mo_customer_validation_admin_api_key'		,get_option( 'mo2f_api_key') );
				update_option( 'mo_customer_validation_customer_token'		,get_option( 'mo2f_customer_token') );
				update_option( 'mo_customer_validation_admin_password'		,'');
				update_option( 'mo_customer_validation_message'				,'Registration complete!');
				update_option( 'mo_customer_validation_registration_status'	,'MO_CUSTOMER_VALIDATION_REGISTRATION_COMPLETE');
				update_option( 'mo_customer_email_transactions_remaining'	,10);
				update_option( 'mo_customer_phone_transactions_remaining'	,10);
				update_option( 'mo_otp_plugin_version'						,1.8);	
			}
		}
		
	}


	class SocialPlugin extends FeaturePluginInterface
	{
		function getstatus()
		{
		    $all_plugins = get_plugins();
			$status = 'NOT_INSTALLED';
			if(isset($all_plugins[MoWpnsConstants::SOCIAL_LOGIN_SETTINGS]))
				$status = is_plugin_active(MoWpnsConstants::SOCIAL_LOGIN_SETTINGS) ? 'ACTIVE' : 'INSTALLED';
			return $status;
		}
	}