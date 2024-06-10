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

include_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR.'mo2f_api.php';

class Two_Factor_Setup {

	public $email;
	private $auth_mode = 2;	//  miniorange test or not
	private $https_mode = false; // website http or https
	function check_mobile_status( $tId ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url    = MO_HOST_NAME . '/moas/api/auth/auth-status';
		$fields = array(
			'txId' => $tId
		);
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $fields, $http_header_array );
	}


	function get_curl_error_message() {
		$message = mo2f_lt( 'Please enable curl extension.' ) .
		           ' <a href="admin.php?page=mo_2fa_troubleshooting">' .
		           mo2f_lt( 'Click here' ) .
		           ' </a> ' .
		           mo2f_lt( 'for the steps to enable curl.' );

		return json_encode( array( "status" => 'ERROR', "message" => $message ) );
	}

	function register_mobile( $useremail ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url         = MO_HOST_NAME . '/moas/api/auth/register-mobile';
		$customerKey = get_option( 'mo2f_customerKey' );
		$fields      = array(
			'customerId' => $customerKey,
			'username'   => $useremail
		);
		$mo2fApi= new Mo2f_Api();

		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $fields, $http_header_array );
	}

	function mo_check_user_already_exist( $email ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url         = MO_HOST_NAME . '/moas/api/admin/users/search';
		$customerKey = get_option( 'mo2f_customerKey' );
		$fields      = array(
			'customerKey' => $customerKey,
			'username'    => $email,
		);
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $fields, $http_header_array );
	}

	function mo_create_user( $currentuser, $email ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url         = MO_HOST_NAME . '/moas/api/admin/users/create';
		$customerKey = get_option( 'mo2f_customerKey' );
		$fields      = array(
			'customerKey' => $customerKey,
			'username'    => $email,
			'firstName'   => $currentuser->user_firstname,
			'lastName'    => $currentuser->user_lastname
		);
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		return $mo2fApi->make_curl_call( $url, $fields, $http_header_array );
	}

	function mo2f_get_userinfo( $email ) {

		if ( ! MO2f_Utility::is_curl_installed() ) {
			return $this->get_curl_error_message();
		}

		$url         = MO_HOST_NAME . '/moas/api/admin/users/get';
		$customerKey = get_option( 'mo2f_customerKey' );
		$fields      = array(
			'customerKey' => $customerKey,
			'username'    => $email,
		);
		$mo2fApi= new Mo2f_Api();
		$http_header_array = $mo2fApi->get_http_header_array();

		$data = $mo2fApi->make_curl_call( $url, $fields, $http_header_array );

		if(is_array($data)){
			return json_encode($data);
		} else 
			return $data;

	}

	function mo2f_update_userinfo( $email, $authType, $phone, $tname, $enableAdminSecondFactor ) {
		$cloud_methods = array('MOBILE AUTHENTICATION','PUSH NOTIFICATIONS','SMS', 'SOFT TOKEN');
		if(MO2F_IS_ONPREM and !in_array($authType, $cloud_methods)){
			$response=json_encode(array("status"=>'SUCCESS'));
		}else {

			if ( ! MO2f_Utility::is_curl_installed() ) {
				return $this->get_curl_error_message();
			}

			$url         = MO_HOST_NAME . '/moas/api/admin/users/update';
			$customerKey = get_option( 'mo2f_customerKey' );


			$fields = array(
				'customerKey'            => $customerKey,
				'username'               => $email,
				'phone'                  => $phone,
				'authType'               => $authType,
				'transactionName'        => $tname,
				'adminLoginSecondFactor' => $enableAdminSecondFactor
			);

			$mo2fApi = new Mo2f_Api();

			$http_header_array = $mo2fApi->get_http_header_array();

			$response = $mo2fApi->make_curl_call( $url, $fields, $http_header_array );
		}
		return $response;
	}

	function register_kba_details( $email, $question1, $answer1, $question2, $answer2, $question3, $answer3, $user_id=null ) {

		if(MO2F_IS_ONPREM){
			$answer1 = md5($answer1);
			$answer2 = md5($answer2);
			$answer3 = md5($answer3);
			$question_answer  = array($question1 => $answer1 ,$question2 => $answer2 , $question3 => $answer3 );
			update_user_meta( $user_id , 'mo2f_kba_challenge', $question_answer  );
			global $Mo2fdbQueries;
			$Mo2fdbQueries->update_user_details( $user_id, array('mo2f_configured_2FA_method' =>'Security Questions') );
		    $response=json_encode(array("status"=>'SUCCESS'));
		}else {
			if ( ! MO2f_Utility::is_curl_installed() ) {
				return $this->get_curl_error_message();
			}

			$url          = MO_HOST_NAME . '/moas/api/auth/register';
			$customerKey  = get_option( 'mo2f_customerKey' );
			$q_and_a_list = "[{\"question\":\"" . $question1 . "\",\"answer\":\"" . $answer1 . "\" },{\"question\":\"" . $question2 . "\",\"answer\":\"" . $answer2 . "\" },{\"question\":\"" . $question3 . "\",\"answer\":\"" . $answer3 . "\" }]";
			$field_string = "{\"customerKey\":\"" . $customerKey . "\",\"username\":\"" . $email . "\",\"questionAnswerList\":" . $q_and_a_list . "}";

			$mo2fApi           = new Mo2f_Api();
			$http_header_array = $mo2fApi->get_http_header_array();

			$response= $mo2fApi->make_curl_call( $url, $field_string, $http_header_array );
		}
		return $response;

	}
}

