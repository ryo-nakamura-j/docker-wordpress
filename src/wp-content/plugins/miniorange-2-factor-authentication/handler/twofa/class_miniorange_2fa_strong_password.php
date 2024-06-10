<?php

class class_miniorange_2fa_strong_password {
	
	function __construct(){		
	}
	
	public static function woocommerce_password_protection($errors, $username, $password, $email) {
		if ($password == false) { return $errors; }
		if ($errors->get_error_data("pass")) { return $errors; }
		
		$enforceStrongPasswds = MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option');

		if ($enforceStrongPasswds && !class_miniorange_2fa_strong_password::mo2f_isStrongPasswd($password, $username)) {
			$errors->add('pass', __('Please choose a stronger password. Try including numbers, symbols, and a mix of upper and lowercase letters and remove common words.'));
			return $errors;
		}
		
		return $errors;
	}
	public static function validatePassword($errors, $update, $userData){
		$Users 		 = MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords_for_accounts', 'get_option');
		$UserRole = isset($_POST['role'])?sanitize_text_field($_POST['role']):NULL;
		if(is_null($UserRole) and $Users == 'user')
		{
			return true;
		}
		
		if($Users == 'all' or ($Users == 'admin' and $UserRole == 'administrator') or ($Users == 'user' and $UserRole != 'administrator' ) or ($Users == 'admin' and is_null($UserRole)))
		{
			$password = (isset($_POST['pass1']) && trim($_POST['pass1'])) ? sanitize_text_field($_POST['pass1']) : false;
			$password=($password==false)?(isset($_POST['password_1'])?$_POST['password_1']:false):$password ;
			$user_id = isset($userData->ID) ? $userData->ID : false;
			$username = isset($_POST["user_login"]) ? sanitize_text_field($_POST["user_login"]) : (isset($userData->user_login) ? sanitize_user($userData->user_login) : sanitize_email($userData->user_email));
			
			if ($password == false) { return $errors; }
			if ($errors->get_error_data("pass")) { return $errors; }
			
			$enforceStrongPasswds = MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option');
			if ($enforceStrongPasswds && !class_miniorange_2fa_strong_password::mo2f_isStrongPasswd($password, $username)) {
				$errors->add('pass', __('Please choose a stronger password. Try including numbers, symbols, and a mix of upper and lowercase letters and remove common words.'));
				return $errors;
			}
			
			return $errors;
		}

		return true;
	}
	public static function woocommerce_password_registration_protection($errors, $username, $email) {
		if(get_option( 'woocommerce_registration_generate_password' )=='yes')
			return $errors;
		$password= sanitize_text_field($_POST['account_password']);
		return class_miniorange_2fa_strong_password::is_validPassword($errors, $username, $password);	
	}
	
	public static function woocommerce_password_edit_account($errors, $user) {
		$password= sanitize_text_field($_POST['password_1']);
		$user =get_userdata($user->ID);
		$username=$user->user_login;
		$enforceStrongPasswds = MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option');

		if ($enforceStrongPasswds && !class_miniorange_2fa_strong_password::mo2f_isStrongPasswd($password, $username)) {
			$errors->add('pass', __('Please choose a stronger password. Try including numbers, symbols, and a mix of upper and lowercase letters and remove common words.'));
			return $errors;
		}
	}

	public static function is_validPassword($errors, $username, $password){
		
		$enforceStrongPasswds = MoWpnsUtility::get_mo2f_db_option('mo2f_enforce_strong_passswords', 'get_option');
		if ($enforceStrongPasswds && !class_miniorange_2fa_strong_password::mo2f_isStrongPasswd($password, $username)) {
			$errors->add('pass', __('Please choose a stronger password. Try including numbers, symbols, and a mix of upper and lowercase letters and remove common words.'));
			return $errors;
		}
		
		return $errors;
		
	}
	public static function mo2f_isStrongPasswd($passwd, $username ) {
		$strength = 0;
				
		if(strlen( trim( $passwd ) )  < 5)
			return false;
		
		if(strtolower( $passwd ) == strtolower( $username ) )
			return false;
		
		if(preg_match('/(?:password|passwd|mypass|wordpress)/i', $passwd)){
			return false;
		}
		if($num = preg_match_all( "/\d/", $passwd, $matches) ){
			$strength += ((int)$num * 10);
		}
		if ( preg_match( "/[a-z]/", $passwd ) )
			$strength += 26;
		if ( preg_match( "/[A-Z]/", $passwd ) )
			$strength += 26;
		if ($num = preg_match_all( "/[^a-zA-Z0-9]/", $passwd, $matches)){
			$strength += (31 * (int)$num);

		}
		if($strength > 60){
			return true;
		}
	}
}
?>