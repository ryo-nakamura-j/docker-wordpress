<?php
class SGPBRequirementsChecker
{
	public static function init()
	{
		self::checkPhpVersion();
	}

	public static function checkPhpVersion()
	{
		if (version_compare(PHP_VERSION, SG_POPUP_MINIMUM_PHP_VERSION, '<')) {
			wp_die(esc_html__('Popup Builder plugin requires PHP version >= '.SG_POPUP_MINIMUM_PHP_VERSION.' version required. You server using PHP version = '.PHP_VERSION));
		}
	}
}

