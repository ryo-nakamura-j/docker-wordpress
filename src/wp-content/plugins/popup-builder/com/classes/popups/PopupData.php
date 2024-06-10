<?php
namespace sgpb;

class PopupData
{
	private static $popupData = array();

	private function __construct()
	{
	}

	public static function getPopupDataById($popupId, $saveMode = '')
	{
		if (!isset(self::$popupData[$popupId])) {
			self::$popupData[$popupId] = SGPopup::getSavedData($popupId, $saveMode);
		}

		return self::$popupData[$popupId];
	}
}
