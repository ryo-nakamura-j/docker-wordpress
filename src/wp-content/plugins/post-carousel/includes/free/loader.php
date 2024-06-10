<?php

/**
 * The Free Loader Class
 *
 * @package post-carousel
 * @since 2.1
 */
class SP_PC_Free_Loader {

	function __construct() {
		require_once SP_PC_PATH . 'public/views/shortcoderender.php';
		require_once SP_PC_PATH . 'public/views/scripts.php';
		require_once SP_PC_PATH . 'admin/views/scripts.php';
		require_once SP_PC_PATH . 'admin/views/mce-button.php';
	}

}

new SP_PC_Free_Loader();
