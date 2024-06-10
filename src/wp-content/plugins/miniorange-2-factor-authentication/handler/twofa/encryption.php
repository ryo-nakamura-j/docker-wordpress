<?php
/**
 * @package    miniOrange
 * @author	   miniOrange Security Software Pvt. Ltd.
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange Gauth plugin.
 */

class mo2f_GAuth_AESEncryption {
	/**
	* @param string $data - the key=value pairs separated with & 
	* @return string
	*/
	public static function encrypt_data_ga($data, $key) {
		$plaintext = $data;
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
		return $ciphertext;
	}


	/**
	* @param string $data - crypt response from Sagepay
	* @return string
	*/
	public static function decrypt_data($data, $key) {
		$c = base64_decode($data);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$ciphertext_raw = substr($c, $ivlen+32);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		return $original_plaintext;
	}

}
?>