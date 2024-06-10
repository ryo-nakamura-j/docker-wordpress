<?php
global $moWpnsUtility,$mo2f_dirName;
if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = sanitize_text_field($_GET[ 'tab' ]);
} else {
		$active_tab = 'default';
}
update_site_option('mo2f_visit_login_and_spam',true);
include_once $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'login_spam.php';
?>