<?php
require_once(dirname(__FILE__).'/config/config.php');
require_once(dirname(__FILE__).'/config/configPackage.php');

if (file_exists(SG_POPUP_CONFIG_PATH.'dataConfig.php')) {
	require_once(SG_POPUP_CONFIG_PATH.'dataConfig.php');
}

require_once(SG_POPUP_CLASSES_PATH.'SGPBRequirementsChecker.php');

SGPBRequirementsChecker::init();
