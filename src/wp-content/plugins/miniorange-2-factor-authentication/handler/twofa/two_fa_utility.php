<?php
/** miniOrange enables user to log in through mobile authentication as an additional layer of security over password.
 * Copyright (C) 2015  miniOrange
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * @package        miniOrange OAuth
 * @license        http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/
class MO2f_Utility {

	public static function get_hidden_phone( $phone ) {
		$hidden_phone = 'xxxxxxx' . substr( $phone, strlen( $phone ) - 3 );
		return $hidden_phone;
	}

	public static function mo2f_check_empty_or_null( $value ) {
		if ( ! isset( $value ) || $value == '' ) {
			return true;
		}
		return false;
	}

	public static function is_curl_installed() {
		if ( in_array( 'curl', get_loaded_extensions() ) ) {
			return 1;
		} else {
			return 0;
		}
	}

	public static function get_all_plugins_installed() {
		$all_plugins = get_plugins();
		$plugins = array();
		$form = "";
		$plugins["None"] = "None";
		foreach ($all_plugins as $plugin_name=>$plugin_details){
			$plugins[$plugin_name] = $plugin_details["Name"];
		}
		unset($plugins['miniorange-2-factor-authentication/miniorange_2_factor_settings.php']);
		$form .= '<div class="mo2f_plugin_select">Please select the plugin<br>
			<select name="mo2f_plugin_selected" id="mo2f-plugin-selected">';
		foreach($plugins as $identifier=>$name) {
			$form .= '<option value="' . esc_attr($identifier) . '">' .  esc_attr($name) . '</option>' ;
		}
		$form .= '</select></div>';
		return $form;
	}

	public static function mo2f_check_number_length( $token ) {
		if ( is_numeric( $token ) ) {
			if ( strlen( $token ) >= 4 && strlen( $token ) <= 8 ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function mo2f_get_hidden_email( $email ) {
		if ( ! isset( $email ) || trim( $email ) === '' ) {
			return "";
		}
		$emailsize    = strlen( $email );
		$partialemail = substr( $email, 0, 1 );
		$temp         = strrpos( $email, "@" );
		$endemail     = substr( $email, $temp - 1, $emailsize );
		for ( $i = 1; $i < $temp; $i ++ ) {
			$partialemail = $partialemail . 'x';
		}
		$hiddenemail = $partialemail . $endemail;
		return $hiddenemail;
	}

	public static function check_if_email_is_already_registered( $email ) {
		global $Mo2fdbQueries;
		$users = get_users( array() );
		foreach ( $users as $user ) {
			$user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
			if ( $user_email == $email ) {
				return true;
			}
		}

		return false;
	}

	public static function check_if_request_is_from_mobile_device( $useragent ) {
		if ( preg_match( '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent ) || preg_match( '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr( $useragent, 0, 4 ) ) ) {
			return true;
		} else {
			return false;
		}
	}


	public static function set_user_values(  $user_session_id, $variable, $value){
		global $Mo2fdbQueries;
		$key = get_option( 'mo2f_encryption_key' );
		$data_option=NULL;

		if(empty($data_option)){

			//setting session
			$_SESSION[$variable] = $value;

			// setting cookie values
			if(is_array($value)){
				if($variable == 'mo_2_factor_kba_questions'){
					MO2f_Utility::mo2f_set_cookie_values( 'kba_question1', $value[0]['question']);
					MO2f_Utility::mo2f_set_cookie_values( 'kba_question2', $value[1]['question'] );
				}else if($variable == 'mo2f_rba_status'){
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_status', $value["status"] );
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_sessionUuid', $value["sessionUuid"] );
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_decision_flag', $value["decision_flag"] );
				}
			}else{
				MO2f_Utility::mo2f_set_cookie_values( $variable, $value);
			}

			// setting values in database
			$user_session_id = MO2f_Utility::decrypt_data( $user_session_id, $key );
			$session_id_hash = md5($user_session_id);
			if ( is_array( $value ) ) {
				$string_value = serialize( $value );
				$Mo2fdbQueries->save_user_login_details( $session_id_hash, array( $variable => $string_value ) );
			} else {
				$Mo2fdbQueries->save_user_login_details( $session_id_hash, array( $variable => $value ) );
			}
		} else if (!empty($data_option) && $data_option=="sessions"){

			$_SESSION[$variable] = $value;

		}else if (!empty($data_option) && $data_option=="cookies"){

			if(is_array($value)){
				if($variable == 'mo_2_factor_kba_questions'){
					MO2f_Utility::mo2f_set_cookie_values( 'kba_question1', $value[0] );
					MO2f_Utility::mo2f_set_cookie_values( 'kba_question2', $value[1] );
				}else if($variable == 'mo2f_rba_status'){
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_status', $value["status"] );
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_sessionUuid', $value["sessionUuid"] );
					MO2f_Utility::mo2f_set_cookie_values( 'mo2f_rba_status_decision_flag', $value["decision_flag"] );
				}
			}else{
				MO2f_Utility::mo2f_set_cookie_values( $variable, $value);
			}
		} else if (!empty($data_option) && $data_option=="tables"){
			$user_session_id = MO2f_Utility::decrypt_data( $user_session_id, $key );
			$session_id_hash = md5($user_session_id);
			if ( is_array( $value ) ) {
				$string_value = serialize( $value );
				$Mo2fdbQueries->save_user_login_details( $session_id_hash, array( $variable => $string_value ) );
			} else {
				$Mo2fdbQueries->save_user_login_details( $session_id_hash, array( $variable => $value ) );
			}
		}
	}

	/*
	
	Returns Random string with length provided in parameter.
	
	*/

	/**
	 * @param string $data - crypt response from Sagepay
	 *
	 * @return string
	 */
	public static function decrypt_data( $data, $key ) {
		$c = base64_decode($data);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		$decrypted_text = '';
		if(is_string($hmac) and is_string($calcmac))
		{
			if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
			{
				$decrypted_text=$original_plaintext;
			}
		}

		return $decrypted_text;
	}

	public static function random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {
		$randomString     = '';
		$charactersLength = strlen( $keyspace );
		$keyspace         = $keyspace . microtime( true );
		$keyspace         = str_shuffle( $keyspace );
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $keyspace[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;

	}

	public static function mo2f_set_transient( $session_id, $key, $value, $expiration = 300 ) {
		set_transient($session_id.$key, $value, $expiration);
		$transient_array = get_site_option($session_id);
		$transient_array[$key] = $value;
		update_site_option($session_id, $transient_array);
		MO2f_Utility::mo2f_set_session_value($session_id, $transient_array);
		if(is_array($value))
                $value = json_encode($value) ;
            
		MO2f_Utility::mo2f_set_cookie_values(base64_encode($session_id),$value);
	    	
  }
	public static function mo2f_get_transient( $session_id, $key) {
		   MO2f_Utility::mo2f_start_session();
		
        if(isset($_SESSION[$session_id])){
        	$transient_array = $_SESSION[$session_id];
        	$transient_value = isset($transient_array[$key]) ? $transient_array[$key] : null;
        	return $transient_value;
        }else if(isset($_COOKIE[base64_decode($session_id)])){
        	$transient_value = MO2f_Utility::mo2f_get_cookie_values(base64_decode($session_id));
        	return $transient_value;
        }else{
        	$transient_value = get_transient($session_id.$key);
        	if(!$transient_value){
				$transient_array = get_site_option($session_id);
				$transient_value = isset($transient_array[$key]) ? $transient_array[$key] : null;
			}
		return $transient_value;
        }

		
	}
    public static function mo2f_set_session_value($session_id, $transient_array){
    		 MO2f_Utility::mo2f_start_session();
		    $_SESSION[ $session_id ] = $transient_array ;
    }

    public static function mo2f_start_session() {
		if ( ! session_id() || session_id() == '' || ! isset( $_SESSION ) ) {
			$session_path = ini_get('session.save_path');
			if( is_writable($session_path) && is_readable($session_path) && !headers_sent() ) {
			   if(session_status() != PHP_SESSION_DISABLED )
				   session_start();
			}
		}
	}
	/**
	 * The function returns the session variables, and if not, retrieves the cookie values set in case the right permissions are not aassigned for the sessions folder in the server.
	 *
	 * @param string $variable - the session or cookie variable name
	 * @param string $session_id - the session id of the user
	 *
	 * @return string
	 */
	public static function mo2f_retrieve_user_temp_values( $variable, $session_id = null ) {
		global $Mo2fdbQueries;
		$data_option=NULL;
		if(empty($data_option)){
			if ( isset( $_SESSION[ $variable ] ) && ! empty( $_SESSION[ $variable ] ) ) {
				//update_option('mo2f_data_storage',"sessions");
				return $_SESSION[ $variable ];
			} else {
				$key        = get_option( 'mo2f_encryption_key' );
				$cookie_value = false;
				if ( $variable == 'mo2f_rba_status' ) {
					if ( isset( $_COOKIE['mo2f_rba_status_status'] ) && ! empty( $_COOKIE['mo2f_rba_status_status'] ) ) {
						$mo2f_rba_status_status        = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_status' );
						$mo2f_rba_status_sessionUuid   = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_sessionUuid' );
						$mo2f_rba_status_decision_flag = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_decision_flag' );
						$cookie_value = array(
							"status"        => $mo2f_rba_status_status,
							"sessionUuid"   => $mo2f_rba_status_sessionUuid,
							"decision_flag" => $mo2f_rba_status_decision_flag
						);
					}
				} else if ( $variable == 'mo_2_factor_kba_questions' ) {
					if ( isset( $_COOKIE['kba_question1'] ) && ! empty( $_COOKIE['kba_question1'] ) ) {

						$kba_question1['question'] = MO2f_Utility::mo2f_get_cookie_values( 'kba_question1' );
						$kba_question2['question'] = MO2f_Utility::mo2f_get_cookie_values( 'kba_question2' );
						$cookie_value = array( $kba_question1, $kba_question2 );
					}
				} else {
					$cookie_value = MO2f_Utility::mo2f_get_cookie_values( $variable );
				}
				if($cookie_value){
					return $cookie_value;
				} else {
					$session_id = MO2f_Utility::decrypt_data( $session_id, $key );
					$session_id_hash = md5($session_id);
					$db_value = $Mo2fdbQueries->get_user_login_details( $variable, $session_id_hash );
					if ( in_array( $variable, array( "mo2f_rba_status", "mo_2_factor_kba_questions" ) ) ) {
						$db_value = unserialize( $db_value );
					}
					return $db_value;
				}
			}
		}else if (!empty($data_option) && $data_option=="sessions"){
			if ( isset( $_SESSION[ $variable ] ) && ! empty( $_SESSION[ $variable ] ) ) {
				return $_SESSION[ $variable ];
			}
		}else if (!empty($data_option) && $data_option=="cookies"){
			$key        = get_option( 'mo2f_encryption_key' );
			$cookie_value = false;

			if ( $variable == 'mo2f_rba_status' ) {
				if ( isset( $_COOKIE['mo2f_rba_status_status'] ) && ! empty( $_COOKIE['mo2f_rba_status_status'] ) ) {
					$mo2f_rba_status_status        = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_status' );
					$mo2f_rba_status_sessionUuid   = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_sessionUuid' );
					$mo2f_rba_status_decision_flag = MO2f_Utility::mo2f_get_cookie_values( 'mo2f_rba_status_decision_flag' );

					$cookie_value = array(
						"status"        => $mo2f_rba_status_status,
						"sessionUuid"   => $mo2f_rba_status_sessionUuid,
						"decision_flag" => $mo2f_rba_status_decision_flag
					);
				}

			} else if ( $variable == 'mo_2_factor_kba_questions' ) {

				if ( isset( $_COOKIE['kba_question1'] ) && ! empty( $_COOKIE['kba_question1'] ) ) {
					$kba_question1 = MO2f_Utility::mo2f_get_cookie_values( 'kba_question1' );
					$kba_question2 = MO2f_Utility::mo2f_get_cookie_values( 'kba_question2' );


					$cookie_value = array( $kba_question1, $kba_question2 );
				}

			} else {
				$cookie_value = MO2f_Utility::mo2f_get_cookie_values( $variable );
			}

			if($cookie_value){
				return $cookie_value;
			}
		}else if (!empty($data_option) && $data_option=="tables"){
			$key = get_option( 'mo2f_encryption_key' );
			$session_id = MO2f_Utility::decrypt_data( $session_id, $key );
			$session_id_hash = md5($session_id);
			$db_value = $Mo2fdbQueries->get_user_login_details( $variable, $session_id_hash );
			if ( in_array( $variable, array( "mo2f_rba_status", "mo_2_factor_kba_questions" ) ) ) {
				$db_value = unserialize( $db_value );
			}
			return $db_value;
		}
	}

	/**
	 * The function gets the cookie value after decoding and decryption.
	 *
	 * @param string $cookiename - the cookie name
	 *
	 * @return string
	 */
	public static function mo2f_get_cookie_values( $cookiename ) {
		
		$key        = get_option( 'mo2f_encryption_key' );
		if ( isset( $_COOKIE[ $cookiename ] ) ) {
			$decrypted_data = MO2f_Utility::decrypt_data( base64_decode( $_COOKIE[ $cookiename ] ), $key );

			if(MO2f_Utility::isJSON($decrypted_data))
			 $decrypted_data = json_decode($decrypted_data);

			if ( $decrypted_data ) {
				$decrypted_data_array = explode( '&', $decrypted_data );

				$cookie_value         = $decrypted_data_array[0];
				if(sizeof($decrypted_data_array) == 2 ){
					$cookie_creation_time = new DateTime( $decrypted_data_array[1] );
				}else{
					$cookie_creation_time = new DateTime( array_pop($decrypted_data_array) );
					$cookie_value = implode('&', $decrypted_data_array);
				}
				$current_time         = new DateTime( 'now' );

				$interval = $cookie_creation_time->diff( $current_time );
				$minutes  = $interval->format( '%i' );

				$is_cookie_valid = $minutes <= 5 ? true : false;

				return $is_cookie_valid ? $cookie_value : false;

			} else {
				return false;
			}
		} else {
			return false;
		}
	}
    
   public static function isJSON($string){
	   return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}
	/**
	 * The function sets the cookie value after encryption and encoding.
	 *
	 * @param string $cookiename - the cookie name
	 * @param string $cookievalue - the cookie value to be set
	 *
	 * @return string
	 */
	public static function  mo2f_set_cookie_values( $cookiename, $cookievalue ) {

		$key        = get_option( 'mo2f_encryption_key' );

		$current_time = new DateTime( 'now' );
		$current_time = $current_time->format( 'Y-m-d H:i:sP' );
		$cookievalue  = $cookievalue . '&' . $current_time;

		$cookievalue_encrypted = MO2f_Utility::encrypt_data( $cookievalue, $key );
		// setcookie( $cookiename, base64_encode( $cookievalue_encrypted ) );
		// setcookie( $cookiename, base64_encode( $cookievalue_encrypted ),NULL,NULL,NULL,NULL, TRUE );
		$_COOKIE[$cookiename] = base64_encode( $cookievalue_encrypted );
	}

	/**
	 * @param string $data - the key=value pairs separated with &
	 *
	 * @return string
	 */
	public static function encrypt_data( $data, $key ) {
		$plaintext = $data;
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		return $ciphertext;
	}

	/**
	 * The function unsets the session variables passed.
	 *
	 * @param array $variables - the array of session variables to be unset
	 *
	 * @return NA
	 */
	public static function unset_session_variables( $variables ) {
		if ( gettype( $variables ) == "array" ) {
			foreach ( $variables as $variable ) {
				if ( isset( $_SESSION[ $variable ] ) ) {
					unset( $_SESSION[ $variable ] );
				}
			}
		} else {
			if ( isset( $_SESSION[ $variables ] ) ) {
				unset( $_SESSION[ $variables ] );
			}
		}
	}

	/**
	 * The function unsets the cookie variables passed.
	 *
	 * @param array $variables - the array of cookie variables to be unset
	 *
	 * @return NA
	 */
	public static function unset_cookie_variables( $variables ) {

		if ( gettype( $variables ) == "array" ) {
			foreach ( $variables as $variable ) {
				if ( isset( $_COOKIE[ $variable ] ) ) {
					setcookie( $variable, '', time() - 3600,NULL,NULL,NULL, TRUE );
				}
			}
		} else {
			if ( isset( $_COOKIE[ $variables ] ) ) {
				setcookie( $variables, '', time() - 3600,NULL,NULL,NULL, TRUE );
			}
		}

	}

	/**
	 * The function unsets the temp table variables passed.
	 *
	 * @param array $variables - the array of temporary table variables to be unset
	 * @param string $session_id - the session_id for which it should be destroyed
	 *
	 * @return NA
	 */
	public static function unset_temp_user_details_in_table( $variables, $session_id, $command='' ) {
		global $Mo2fdbQueries;
		$key        = get_option( 'mo2f_encryption_key' );
		$session_id = MO2f_Utility::decrypt_data( $session_id, $key );
		$session_id_hash = md5($session_id);
		if($command == "destroy"){
			$Mo2fdbQueries->delete_user_login_sessions( $session_id_hash );
		}else{
			$Mo2fdbQueries->save_user_login_details( $session_id_hash, array($variables => ''));
		}
	}



	/**
	 * The function decodes the twofactor methods
	 *
	 * @param array $variables - the selected 2-factor method and the decode type.
	 *
	 * @return NA
	 */
	public static function mo2f_decode_2_factor( $selected_2_factor_method, $decode_type ) {

		if ( $selected_2_factor_method == 'NONE' ) {
			return $selected_2_factor_method;
		}else if($selected_2_factor_method == "OTP Over Email")
		{
			$selected_2_factor_method = "OTPOverEmail";	
		}


		$wpdb_2fa_methods = array(
			"miniOrangeQRCodeAuthentication" => "miniOrange QR Code Authentication",
			"miniOrangeSoftToken"            => "miniOrange Soft Token",
			"miniOrangePushNotification"     => "miniOrange Push Notification",
			"GoogleAuthenticator"            => "Google Authenticator",
			"AuthyAuthenticator"             => "Authy Authenticator",
			"SecurityQuestions"              => "Security Questions",
			"EmailVerification"              => "Email Verification",
			"OTPOverSMS"                     => "OTP Over SMS",
			"OTPOverEmail"					 => "OTP Over Email",
			"DuoAuthenticator"               => "Duo Authenticator"
		);

		$server_2fa_methods = array(
			"miniOrange QR Code Authentication" => "MOBILE AUTHENTICATION",
			"miniOrange Soft Token"             => "SOFT TOKEN",
			"miniOrange Push Notification"      => "PUSH NOTIFICATIONS",
			"Google Authenticator"              => "GOOGLE AUTHENTICATOR",
			"Authy Authenticator"               => "GOOGLE AUTHENTICATOR",
			"Security Questions"                => "KBA",
			"Email Verification"                => "OUT OF BAND EMAIL",
			"OTP Over SMS"                      => "SMS",
			"EMAIL"                             => "OTP Over Email",
			"OTPOverEmail"					 	=> "OTP Over Email",
			"Duo Authenticator"                 => "Duo Authenticator",
			"DUO AUTHENTICATOR"					=> "Duo Authenticator",
			"OTP Over Email"					=> "EMAIL",
			"OTP OVER EMAIL"                    => "EMAIL"

		);

		$server_to_wpdb_2fa_methods = array(
			"MOBILE AUTHENTICATION" => "miniOrange QR Code Authentication",
			"SOFT TOKEN"            => "miniOrange Soft Token",
			"PUSH NOTIFICATIONS"    => "miniOrange Push Notification",
			"GOOGLE AUTHENTICATOR"  => "Google Authenticator",
			"KBA"                   => "Security Questions",
			"OUT OF BAND EMAIL"     => "Email Verification",
			"SMS"                   => "OTP Over SMS",
			"EMAIL"                 => "OTP Over Email",
			"OTPOverEmail"			=> "OTP Over Email",
			"OTP OVER EMAIL"		=> "OTP Over Email",
			"OTP Over SMS"			=> "OTP Over SMS",
			"Security Questions"	=> "Security Questions",
			"Google Authenticator"  => "Google Authenticator"
		);

		 $methodname='';
		if ( $decode_type == "wpdb" ) {
			$methodname = isset($wpdb_2fa_methods[ $selected_2_factor_method ])?$wpdb_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		} else if ( $decode_type == "server" ) {
			$methodname = isset($server_2fa_methods[ $selected_2_factor_method ])?$server_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		} else {
			$methodname = isset($server_to_wpdb_2fa_methods[ $selected_2_factor_method ])?$server_to_wpdb_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		}
		return $methodname;

	}
	public static function is_same_method($method,$current_method){
		if($method == $current_method || $method == MO2f_Utility::mo2f_decode_2_factor($current_method,'wpdb') || $method == MO2f_Utility::mo2f_decode_2_factor($current_method,'') || MO2f_Utility::mo2f_decode_2_factor($current_method,'server') == $method)
			return true;
		return false;
	}

	public static function get_plugin_name_by_identifier( $plugin_identitifier ){
		$all_plugins = get_plugins();
		$plugin_details = $all_plugins[$plugin_identitifier];

		return $plugin_details["Name"] ? $plugin_details["Name"] : "No Plugin selected" ;
	}
   
    public static function isBlank($value)
    {
        if (!isset($value) || empty($value)) return TRUE;
        return FALSE;
    }

    public static function get_index_value($var,$index){
    	switch ($var) {
    		case 'GLOBALS':
    			return isset($GLOBALS[$index])?$GLOBALS[$index]:false;
    			break;
    		
    		default:
    			return false;
    			break;
    	}
	}

	public static function get_codes_email_content($codes){
		global $imagePath;
        $message =  '<table cellpadding="25" style="margin:0px auto">
        <tbody>
        <tr>
        <td>
        <table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
        <tbody>
        <tr>
        <td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
        </tr>
        </tbody>
        </table>
        <table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
        <tbody>
        <tr>
        <td>
        <p style="margin-top:0;margin-bottom:20px">Dear Customer,</p>
        <p style="margin-top:0;margin-bottom:10px">You initiated a transaction from <b>WordPress 2 Factor Authentication Plugin</b>:</p>
        <p style="margin-top:0;margin-bottom:10px">Your backup codes are:-
        <table cellspacing="10">
            <tr>';
            for ($x = 0; $x < sizeof($codes); $x++) {
		        $message = $message.'<td>'.$codes[$x].'</td>';
		       
	        }
        $message = $message.'</table></p>
        <p style="margin-top:0;margin-bottom:10px">Please use this carefully as each code can only be used once. Please do not share these codes with anyone.</p>
        <p style="margin-top:0;margin-bottom:10px">Also, we would highly recommend you to reconfigure your two-factor after logging in.</p>
        <p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p>
        <p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
        </div></div></td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>';
        return $message;
    }

	public static function get_codes_warning_email_content($codes_remaining){
		global $imagePath;
        $message =  '<table cellpadding="25" style="margin:0px auto">
        <tbody>
        <tr>
        <td>
        <table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
        <tbody>
        <tr>
        <td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
        </tr>
        </tbody>
        </table>
        <table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
        <tbody>
        <tr>
        <td>
        <p style="margin-top:0;margin-bottom:20px">Dear Customer,</p>
        <p style="margin-top:0;margin-bottom:10px">You have '.$codes_remaining.' backup codes remaining. Kindly reconfigure your two-factor to avoid being locked out.</b></p>
        <p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p>
        <p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
        </div></div></td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>';
        return $message;
    }

    public static function mo2f_email_backup_codes($codes, $mo2f_user_email){
        $subject    = '2-Factor Authentication(Backup Codes)';
        $headers    = array('Content-Type: text/html; charset=UTF-8');
        $message    = MO2f_Utility::get_codes_email_content($codes);
        $result     = wp_mail($mo2f_user_email,$subject,$message,$headers);
        return $result;
    }

    public static function mo2f_download_backup_codes($id, $codes){
        update_user_meta($id, 'mo_backup_code_downloaded', 1);
        header('Content-Disposition: attachment; filename=miniOrange2-factor-BackupCodes.txt');
        echo "Two Factor Backup Codes:".PHP_EOL.PHP_EOL;
        echo "These are the codes that can be used in case you lose your phone or cannot access your email. Please reconfigure your authentication method after login.".PHP_EOL."Please use this carefully as each code can only be used once. Please do not share these codes with anyone..".PHP_EOL.PHP_EOL;
        for ($x = 0; $x < sizeof($codes); $x++){
            $str1= $codes[$x];
            echo(intval($x+1).". ".esc_html($str1)." ");
        }
                                
        exit;
    }

    public static function mo2f_debug_file($text){
    	if(MoWpnsUtility::get_mo2f_db_option('mo2f_enable_debug_log', 'site_option') == 1){
        	$debug_log_path = wp_upload_dir();
    		$debug_log_path = $debug_log_path['basedir'].DIRECTORY_SEPARATOR;
    		$filename = 'miniorange_debug_log.txt';
    		$data = '[' . date("Y/m/d").' '. time().']:'.$text."\n";
    		$handle = fopen($debug_log_path.DIRECTORY_SEPARATOR.$filename,'a+');
			fwrite($handle,$data);
			fclose($handle);
        }
    }


    public static function mo2f_mail_and_download_codes(){
      global $Mo2fdbQueries;
       
        $id = get_current_user_id();
        $mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $id );
        if(empty($mo2f_user_email)){
            $currentuser = get_user_by( 'id', $id );
            $mo2f_user_email = $currentuser->user_email;
        }
        $generate_backup_code = new Customer_Cloud_Setup();
		if(get_transient("mo2f_generate_backup_code")=="1")
		{
			return "TransientActive";
		}
        $codes=$generate_backup_code->mo_2f_generate_backup_codes($mo2f_user_email, site_url());


        if($codes == 'LimitReached'|| $codes == 'UserLimitReached' || $codes == 'AllUsed' || $codes == 'invalid_request' )
		{
			update_user_meta($id, 'mo_backup_code_limit_reached',1);
			return $codes;
		}
		if($codes == 'InternetConnectivityError' )
			return $codes;
        	

        $codes = explode(' ', $codes);
        $result = MO2f_Utility::mo2f_email_backup_codes($codes, $mo2f_user_email);
        update_user_meta($id, 'mo_backup_code_generated', 1);
        update_user_meta($id, 'mo_backup_code_downloaded', 1);

		set_transient("mo2f_generate_backup_code","1",30);
        MO2f_Utility::mo2f_download_backup_codes($id, $codes);

		
    }


}

?>
