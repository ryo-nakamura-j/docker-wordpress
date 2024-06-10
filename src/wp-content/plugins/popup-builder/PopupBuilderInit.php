<?php
namespace sgpb;
use \SgpbPopupExtensionRegister;
use sgpb\AdminHelper;

class PopupBuilderInit
{
	private static $instance = null;
	private $actions;
	private $filters;

	private function __construct()
	{
		$this->init();
	}

	private function __clone()
	{

	}

	public static function getInstance()
	{
		if(!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init()
	{
		/*Included required data*/
		$this->includeData();
		$this->registerHooks();
		$this->actions();
		$this->filters();
		$this->versionDetection();
	}

	private function includeData()
	{
		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php');
		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionActivator.php');
		require_once(SG_POPUP_CLASSES_PATH.'Installer.php');
		require_once(SG_POPUP_HELPERS_PATH.'AdminHelper.php');
		require_once(SG_POPUP_HELPERS_PATH.'Functions.php');
		require_once(SG_POPUP_HELPERS_PATH.'ScriptsIncluder.php');
		require_once(SG_POPUP_HELPERS_PATH.'PopupBuilderActivePackage.php');
		require_once(SG_POPUP_HELPERS_PATH.'License.php');
		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtension.php');
		require_once(SG_POPUP_HELPERS_PATH.'MultipleChoiceButton.php');
		require_once(SG_POPUP_CLASSES_PATH.'ConditionBuilder.php');
		require_once(SG_POPUP_CLASSES_PATH.'ConditionCreator.php');
		require_once(SG_POPUP_CLASSES_POPUPS_PATH.'SGPopup.php');
		require_once(SG_POPUP_CLASSES_PATH.'ScriptsLoader.php');
		require_once(SG_POPUP_CLASSES_PATH.'PopupGroupFilter.php');
		require_once(SG_POPUP_CLASSES_PATH.'PopupChecker.php');
		require_once(SG_POPUP_CLASSES_PATH.'PopupLoader.php');
		require_once(SG_POPUP_CLASSES_PATH.'PopupType.php');
		require_once(SG_POPUP_CLASSES_PATH.'MediaButton.php');
		require_once(SG_POPUP_CLASSES_PATH.'Style.php');
		require_once(SG_POPUP_CLASSES_PATH.'Javascript.php');
		require_once(SG_POPUP_CLASSES_PATH.'PopupInstaller.php');
		require_once(SG_POPUP_CLASSES_PATH.'RegisterPostType.php');
		require_once(SG_POPUP_CLASSES_PATH.'Ajax.php');
		require_once(SG_POPUP_CLASSES_PATH.'ConvertToNewVersion.php');
		require_once(SG_POPUP_LIBS_PATH.'Reports.php');
		require_once(SG_POPUP_CLASSES_PATH.'Filters.php');
		require_once(SG_POPUP_CLASSES_PATH.'Actions.php');
		require_once(SG_POPUP_LIBS_PATH.'Table.php');
		require_once(SG_POPUP_CLASSES_PATH.'Updates.php');
		require_once(SG_POPUP_CLASSES_PATH.'NotificationCenter.php');
		require_once(SG_POPUP_CLASSES_PATH.'Notification.php');
		require_once(SG_POPUP_CLASSES_PATH.'Feedback.php');
		require_once(SG_POPUP_COMPONENTS_PATH.'Menu.php');
		require_once(SGPB_POPUP_DETECTION_PATH.'SgpbPopupVersionDetection.php');
	}

	public function actions()
	{
		$this->actions = new Actions();
	}

	public function filters()
	{
		$this->filters = new Filters();
	}
	public function versionDetection()
	{
		new SgpbPopupVersionDetection();
	}

	private function registerHooks()
	{
		register_activation_hook(SG_POPUP_FILE_NAME, array($this, 'activate'));
		register_deactivation_hook(SG_POPUP_FILE_NAME, array($this, 'deactivate'));
	}

	public function activate()
	{
		Installer::install();
		Installer::registerPlugin();
		AdminHelper::filterUserCapabilitiesForTheUserRoles('activate');
	}

	public function deactivate()
	{
		Functions::clearAllTransients();
		AdminHelper::removeSelectedTypeOptions('cron');
		AdminHelper::filterUserCapabilitiesForTheUserRoles('deactivate');
		require_once(SG_POPUP_EXTENSION_PATH.'SgpbPopupExtensionRegister.php');
		$pluginName = SG_POPUP_FILE_NAME;
		// remove AWeber extension from registered extensions
		SgpbPopupExtensionRegister::remove($pluginName);
	}
}

PopupBuilderInit::getInstance();
