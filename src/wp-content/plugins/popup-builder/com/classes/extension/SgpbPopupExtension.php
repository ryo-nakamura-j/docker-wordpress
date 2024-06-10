<?php

require_once(SG_POPUP_EXTENSION_PATH.'SgpbIPopupExtension.php');
use sgpb\AdminHelper;
use \sgpb\SgpbPopupVersionDetection;

if (class_exists('SgpbPopupExtension')) {
	return false;
}

class SgpbPopupExtension implements SgpbIPopupExtension
{
	public function getNewsletterPageKey()
	{
		return SG_POPUP_POST_TYPE.'_page_'.SG_POPUP_NEWSLETTER_PAGE;
	}

	public function getSettingsPageKey()
	{
		return SG_POPUP_POST_TYPE.'_page_'.SG_POPUP_SETTINGS_PAGE;
	}

	public function getScripts($pageName, $data)
	{
		$jsFiles = array();
		$localizeData = array();
		$translatedData = ConfigDataHelper::getJsLocalizedData();
		$currentPostType = AdminHelper::getCurrentPostType();
		$newsletterPage = $this->getNewsletterPageKey();
		$settingsPage = $this->getSettingsPageKey();

		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'ExtensionsNotification.js', 'dep' => array('jquery'));
		$localizeData[] = array(
			'handle' => 'ExtensionsNotification.js',
			'name' => 'SGPB_JS_EXTENSIONS_PARAMS',
			'data' => array(
				'nonce' => wp_create_nonce(SG_AJAX_NONCE),
				'popupPostType' => SG_POPUP_POST_TYPE,
				'extendPage' => SG_POPUP_EXTEND_PAGE,
				'supportUrl' => SG_POPUP_SUPPORT_URL,
				'allExtensionsUrl' => SG_POPUP_ALL_EXTENSIONS_URL,
				'supportPage' => SG_POPUP_SUPPORT_PAGE,
				'reviewUrl' => SG_POPUP_RATE_US_URL
			)
		);

		$allowPages = array(
			'popupType',
			'editpage',
			'popupspage',
			'popupbuilder_page_license',
			$newsletterPage,
			$settingsPage
		);

		if ($pageName == $newsletterPage) {
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Newsletter.js');
		}
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Banner.js', 'dep' => array('jquery'));
		$localizeData[] = array(
			'handle' => 'Banner.js',
			'name' => 'SGPB_JS_PARAMS',
			'data' => array(
				'url'   => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce(SG_AJAX_NONCE)
			)
		);
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'gutenbergBlock.min.js');

		$localizeData[] = array(
			'handle' => 'gutenbergBlock.min.js',
			'name' => 'SGPB_GUTENBERG_PARAMS',
			'data' => array(
				'allPopups' => AdminHelper::getGutenbergPopupsIdAndTitle(),
				'allEvents' => AdminHelper::getGutenbergPopupsEvents(),
				'title'   => __('Popup Builder', SG_POPUP_TEXT_DOMAIN),
				'description'   => __('This block will help you to add Popup Builder’s shortcode inside the page content', SG_POPUP_TEXT_DOMAIN),
				'i18n'=> array(
					'title'            => __( 'WPForms', 'wpforms-lite' ),
					'description'      => __( 'Select and display one of your forms.', 'wpforms-lite' ),
					'form_keyword'     => __( 'form', 'wpforms-lite' ),
					'form_select'      => __( 'Select Popup', 'wpforms-lite' ),
					'form_settings'    => __( 'Form Settings', 'wpforms-lite' ),
					'form_selected'    => __( 'Form', 'wpforms-lite' ),
					'show_title'       => __( 'Show Title', 'wpforms-lite' ),
					'show_description' => __( 'Show Description', 'wpforms-lite' ),
				),
				'logo_url' => SG_POPUP_IMG_URL.'bannerLogo.png',
				'logo_classname' => 'sgpb-gutenberg-logo',
				'clickText' => __('Click me', SG_POPUP_TEXT_DOMAIN)
			)
		);

		if (in_array($pageName, $allowPages) || $currentPostType == SG_POPUP_AUTORESPONDER_POST_TYPE) {
			$jsFiles[] = array('folderUrl'=> '', 'filename' => 'wp-color-picker');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'select2.min.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'sgpbSelect2.js');


			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Backend.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'FloatingButton.js', 'dep' => array('Backend.js'),);
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'NotificationCenter.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Popup.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'PopupConfig.js');

			$localizeData[] = array(
				'handle' => 'Backend.js',
				'name' => 'SGPB_JS_PARAMS',
				'data' => array(
					'url'   => admin_url('admin-ajax.php'),
					'postUrl'   => SG_POPUP_ADMIN_URL.'admin-post.php',
					'nonce' => wp_create_nonce(SG_AJAX_NONCE)
				)
			);

			$localizeData[] = array(
				'handle' => 'sgpbSelect2.js',
				'name' => 'SGPB_JS_PACKAGES',
				'data' => array(
					'packages' => array(
						'current' => SGPB_POPUP_PKG,
						'free' => SGPB_POPUP_PKG_FREE,
						'silver' => SGPB_POPUP_PKG_SILVER,
						'gold' => SGPB_POPUP_PKG_GOLD,
						'platinum' => SGPB_POPUP_PKG_PLATINUM
					),
					'extensions' => array(
						'geo-targeting' => AdminHelper::isPluginActive('geo-targeting'),
						'advanced-closing' => AdminHelper::isPluginActive('advancedClosing')
					),
					'proEvents' => apply_filters('sgpbProEvents', array('inactivity', 'onScroll'))
				)
			);

			$localizeData[] = array(
				'handle' => 'Backend.js',
				'name' => 'SGPB_JS_LOCALIZATION',
				'data' => $translatedData
			);

			$localizeData[] = array(
				'handle' => 'Popup.js',
				'name' => 'sgpbPublicUrl',
				'data' => SG_POPUP_PUBLIC_URL
			);
		}
		else if ($pageName == SG_POPUP_SUBSCRIBERS_PAGE) {
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'select2.min.js', 'dep' => '', 'ver' => '3.86', 'inFooter' => '');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'sgpbSelect2.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Subscribers.js');
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Banner.js');

			$localizeData[] = array(
				'handle' => 'Subscribers.js',
				'name' => 'SGPB_JS_PARAMS',
				'data' => array(
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce(SG_AJAX_NONCE),
					'packages' => array(
						'current' => SGPB_POPUP_PKG,
						'silver' => SGPB_POPUP_PKG_SILVER,
						'gold' => SGPB_POPUP_PKG_GOLD,
						'platinum' => SGPB_POPUP_PKG_PLATINUM
					)
				)
			);

			$localizeData[] = array(
				'handle' => 'sgpbSelect2.js',
				'name' => 'SGPB_JS_PACKAGES',
				'data' => array(
					'packages' => array(
						'current' => SGPB_POPUP_PKG,
						'free' => SGPB_POPUP_PKG_FREE,
						'silver' => SGPB_POPUP_PKG_SILVER,
						'gold' => SGPB_POPUP_PKG_GOLD,
						'platinum' => SGPB_POPUP_PKG_PLATINUM
					)
				)
			);

			$localizeData[] = array(
				'handle' => 'Subscribers.js',
				'name' => 'SGPB_JS_ADMIN_URL',
				'data' => array(
					'url'   => SG_POPUP_ADMIN_URL.'admin-post.php',
					'nonce' => wp_create_nonce(SG_AJAX_NONCE)
				)
			);

			$localizeData[] = array(
				'handle' => 'Subscribers.js',
				'name' => 'SGPB_JS_LOCALIZATION',
				'data' => $translatedData
			);

			$localizeData[] = array(
				'handle' => 'Banner.js',
				'name' => 'SGPB_JS_PARAMS',
				'data' => array(
					'url'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce(SG_AJAX_NONCE)
				)
			);
		}
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Modals.js');
		if (count($versionDetection = SgpbPopupVersionDetection::compareVersions())) {
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'sgpbDetect.js');
			$localizeData[] = array(
				'handle' => 'sgpbDetect.js',
				'name' => 'SGPB_JS_DETECTIONS',
				'data' => $versionDetection
			);
		}

		$scriptData = array(
			'jsFiles' => apply_filters('sgpbAdminJsFiles', $jsFiles),
			'localizeData' => apply_filters('sgpbAdminJsLocalizedData', $localizeData)
		);

		$scriptData = apply_filters('sgpbAdminJs', $scriptData);

		return $scriptData;
	}

	public function getStyles($pageName, $data)
	{
		$cssFiles = array();
		$newsletterPage = $this->getNewsletterPageKey();
		$settingsPage = $this->getSettingsPageKey();

		$allowPages = array(
			'popupType',
			'editpage',
			'popupspage',
			'popupbuilder_page_license',
			$newsletterPage,
			$settingsPage
		);
		if (in_array($pageName, $allowPages)) {
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'jquery.dateTimePicker.min.css', 'dep' => array(), 'ver' => rand(1, 1000), 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'sgbp-bootstrap.css', 'dep' => array(), 'ver' => rand(1, 1000), 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'popupAdminStyles.css', 'dep' => array(), 'ver' => rand(1, 1000), 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'select2.min.css', 'dep' => array(), 'ver' => rand(1, 1000), 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'theme.css', 'dep' => array(), 'ver' => rand(1, 1000), 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => '', 'filename' => 'wp-color-picker');
		}
		else if ($pageName == SG_POPUP_SUBSCRIBERS_PAGE) {
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'sgbp-bootstrap.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'popupAdminStyles.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);
			$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'select2.min.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);

		}
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'sgpb-modal.css', 'dep' => array(), 'ver' => rand(1, 10000), 'inFooter' => false);

		$cssData = array(
			'cssFiles' => apply_filters('sgpbAdminCssFiles', $cssFiles)
		);
		return $cssData;
	}

	public function getFrontendScripts($page, $popupObjs)
	{
		$translatedData = ConfigDataHelper::getJsLocalizedData();
		$jsFiles = array();
		$localizeData = array();
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'Popup.js', 'dep' => array('jquery'));
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'PopupConfig.js', 'dep' => array('Popup.js'));
		$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'PopupBuilder.js', 'dep' => array('PopupConfig.js'));
		if (SGPB_POPUP_PKG >= SGPB_POPUP_PKG_SILVER) {
			$jsFiles[] = array('folderUrl'=> SG_POPUP_JS_URL, 'filename' => 'PopupBuilderProFunctionality.js', 'dep' => array('jquery'));
		}

		$localizeData[] = array(
			'handle' => 'PopupBuilder.js',
			'name' => 'SGPB_POPUP_PARAMS',
			'data' => array(
				'popupTypeAgeRestriction' => SGPB_POPUP_TYPE_RESTRICTION,
				'defaultThemeImages' => array(
					1 => AdminHelper::defaultButtonImage('sgpb-theme-1'),
					2 => AdminHelper::defaultButtonImage('sgpb-theme-2'),
					3 => AdminHelper::defaultButtonImage('sgpb-theme-3'),
					5 => AdminHelper::defaultButtonImage('sgpb-theme-5'),
					6 => AdminHelper::defaultButtonImage('sgpb-theme-6')
				),
				'homePageUrl' => get_home_url().'/',
				'isPreview' => isset($_GET['sg_popup_preview_id']),
				'convertedIdsReverse' => AdminHelper::getReverseConvertIds(),
				'dontShowPopupExpireTime' => SGPB_DONT_SHOW_POPUP_EXPIRY,
				'conditionalJsClasses' => apply_filters('sgpbConditionalJsClasses', array()),
				'disableAnalyticsGeneral' => AdminHelper::getOption('sgpb-enable-debug-mode')
			)
		);

		$localizeData[] = array(
			'handle' => 'PopupBuilder.js',
			'name' => 'SGPB_JS_PACKAGES',
			'data' => array(
				'packages' => array(
					'current' => SGPB_POPUP_PKG,
					'free' => SGPB_POPUP_PKG_FREE,
					'silver' => SGPB_POPUP_PKG_SILVER,
					'gold' => SGPB_POPUP_PKG_GOLD,
					'platinum' => SGPB_POPUP_PKG_PLATINUM
				),
				'extensions' => array(
					'geo-targeting' => AdminHelper::isPluginActive('geo-targeting'),
					'advanced-closing' => AdminHelper::isPluginActive('advancedClosing')
				)
			)
		);

		$localizeData[] = array(
			'handle' => 'Popup.js',
			'name' => 'sgpbPublicUrl',
			'data' => SG_POPUP_PUBLIC_URL
		);

		$localizeData[] = array(
				'handle' => 'Popup.js',
				'name' => 'SGPB_JS_LOCALIZATION',
				'data' => $translatedData
			);

		$localizeData[] = array(
			'handle' => 'PopupBuilder.js',
			'name' => 'SGPB_JS_PARAMS',
			'data' => array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce(SG_AJAX_NONCE)
			)
		);

		$scriptData = array(
			'jsFiles' => apply_filters('sgpbFrontendJsFiles', $jsFiles),
			'localizeData' => apply_filters('sgpbFrontendJsLocalizedData', $localizeData)
		);

		$scriptData = apply_filters('sgpbFrontendJs', $scriptData);

		return $scriptData;
	}

	public function getFrontendStyles($page, $data)
	{
		$cssFiles = array();
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'theme.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);

		$cssData = array(
			'cssFiles' => apply_filters('sgpbFrontendCssFiles', $cssFiles)
		);

		return $cssData;
	}

}
