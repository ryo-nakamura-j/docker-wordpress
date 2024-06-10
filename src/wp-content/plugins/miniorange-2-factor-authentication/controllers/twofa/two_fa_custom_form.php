<?php

$is_woocommerce 		= get_site_option('mo2f_custom_reg_wocommerce');
$is_bbpress 			= get_site_option('mo2f_custom_reg_bbpress');
$is_any_of_woo_bb 		= $is_woocommerce || $is_bbpress;
$is_custom				= get_site_option('mo2f_custom_reg_custom');
$is_registered 			= get_site_option('mo2f_customerkey');

include_once $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_custom_form.php';