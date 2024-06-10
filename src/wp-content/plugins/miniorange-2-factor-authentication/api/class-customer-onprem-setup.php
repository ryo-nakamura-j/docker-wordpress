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

class Customer_Setup extends Customer_Cloud_Setup {



	function send_otp_token( $uKey, $authType, $cKey, $apiKey,  $currentuser=null ) {
   
		$cloud_methods = array('MOBILE AUTHENTICATION','PUSH NOTIFICATIONS','SMS');
		if(MO2F_IS_ONPREM and !in_array($authType, $cloud_methods)){
			include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Mo2f_OnPremRedirect.php';
			$mo2fOnPremRedirect = new Mo2f_OnPremRedirect();
			if(is_null($currentuser) or !isset($currentuser))
				$currentuser = wp_get_current_user();
			$content = $mo2fOnPremRedirect->OnpremSendRedirect($uKey,$authType,$currentuser);//change parameters as per your requirement but make sure other methods are not affected.

		}else {

			$content= parent::send_otp_token($uKey, $authType, $cKey, $apiKey, $currentuser=null);

		}

		return $content;
	}


	function validate_otp_token( $authType, $username, $transactionId, $otpToken, $cKey, $customerApiKey, $current_user =null) {
		$content='';
		if(MO2F_IS_ONPREM and $authType != 'SOFT TOKEN' and $authType !='OTP Over Email' and $authType != 'SMS' and $authType != 'OTP Over SMS'){
			include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Mo2f_OnPremRedirect.php';
			$mo2fOnPremRedirect = new Mo2f_OnPremRedirect();
			if(!isset($current_user) or is_null($current_user) )
				$current_user = wp_get_current_user();
			$content = $mo2fOnPremRedirect->OnpremValidateRedirect($authType, $otpToken,$current_user );
			//change parameters as per your requirement but make sure other methods are not affected.

		}else{

			$content= parent::validate_otp_token( $authType, $username, $transactionId, $otpToken, $cKey, $customerApiKey, $current_user =null);

		}
		return $content;
	}


}

?>