<?php
namespace sgpb;

class PopupBuilderActivePackage
{
	// sections and additional options array
	private static $sections = array();

	public static function init()
	{
		self::$sections = array(
			'userStatus' => array('min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER, 'name' => 'userStatus'),
			'popupConditionsSection' => array('min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER, 'name' => 'popupConditionsSection'),
			'popupOtherConditionsSection' => array('min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER, 'name' => 'popupOtherConditionsSection')
		);
	}

	public static function canUseSection($optionName)
	{
		if (!isset(self::$sections[$optionName])) {
			return false;
		}

		return self::checkVersionAndPackage(self::$sections[$optionName]);
	}

	public static function canUseOption($optionName)
	{
		global $SGPB_OPTIONS;
		$currentOption = array();

		foreach ($SGPB_OPTIONS as $option) {
			if ($option['name'] == $optionName) {
				$currentOption = $option;
				break;
			}
		}

		return self::checkVersionAndPackage($currentOption);
	}

	private static function checkVersionAndPackage($option)
	{
		$currentOptionSupportedMinVersion = '';
		$currentOptionSupportedMinPackage = '';

		if (isset($option['min-version'])) {
			$currentOptionSupportedMinVersion = $option['min-version'];
		}
		if (isset($option['min-pkg'])) {
			$currentOptionSupportedMinPackage = $option['min-pkg'];
		}
		$optionAvailable = apply_filters('sgpbOptionAvailable', $option);

		// it can change option availability from extensions
		if (isset($optionAvailable['status'])) {
			return $optionAvailable['status'];
		}

		if ($currentOptionSupportedMinVersion <= SG_POPUP_VERSION) {
			if ($currentOptionSupportedMinPackage <= SGPB_POPUP_PKG) {
				return true;
			}
		}

		return false;
	}
}

PopupBuilderActivePackage::init();
