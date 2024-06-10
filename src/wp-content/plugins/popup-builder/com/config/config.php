<?php

class SgpbPopupConfig
{
	public static function addDefine($name, $value)
	{
		if (!defined($name)) {
			define($name, $value);
		}
	}

	public static function init()
	{
		self::addDefine('SGPB_POPUP_FREE_MIN_VERSION', '3.0.2');
		self::addDefine('SGPB_POPUP_PRO_MIN_VERSION', '4.0');

		self::addDefine('SGPB_POPUP_PKG_FREE', 1);
		self::addDefine('SGPB_POPUP_PKG_SILVER', 2);
		self::addDefine('SGPB_POPUP_PKG_GOLD', 3);
		self::addDefine('SGPB_POPUP_PKG_PLATINUM', 4);
		self::addDefine('SG_POPUP_PRO_URL', 'https://popup-builder.com/pricing/');
		self::addDefine('SG_POPUP_BUNDLE_URL', 'https://popup-builder.com/pricing/');
		self::addDefine('SG_POPUP_EXTENSIONS_URL', 'https://popup-builder.com/extensions/');
		self::addDefine('SG_POPUP_SUPPORT_URL', 'https://wordpress.org/support/plugin/popup-builder');
		self::addDefine('SG_POPUP_TICKET_URL', 'https://help.popup-builder.com');
		self::addDefine('SG_POPUP_RATE_US_URL', 'https://wordpress.org/support/plugin/popup-builder/reviews/');
		self::addDefine('SG_POPUP_IFRAME_URL', 'https://popup-builder.com/product/iframe-popup//');
		self::addDefine('SG_POPUP_SCROLL_URL', 'https://popup-builder.com/product/scroll-triggered-popup/');
		self::addDefine('SG_POPUP_AD_BLOCK_URL', 'https://popup-builder.com/product/ad-block/');
		self::addDefine('SG_POPUP_ANALYTICS_URL', 'https://popup-builder.com/product/analytics/');
		self::addDefine('SG_POPUP_EXIT_INTENT_URL', 'https://popup-builder.com/product/exit-intent/');
		self::addDefine('SG_POPUP_MAILCHIMP_URL', 'https://popup-builder.com/product/mailchimp-popup/');
		self::addDefine('SG_POPUP_AWEBER_URL', 'https://popup-builder.com/product/aweber-popup/');
		self::addDefine('SG_POPUP_WOOCOMMERCE_URL', 'https://popup-builder.com/product/woocommerce/');
		self::addDefine('SG_POPUP_RECENT_SALES_URL', 'https://popup-builder.com/product/recent-sales/');
		self::addDefine('SG_POPUP_VIDEO_URL', 'https://popup-builder.com/product/video-popup/');
		self::addDefine('SG_POPUP_SOCIAL_URL', 'https://popup-builder.com/product/social-popup/');
		self::addDefine('SG_POPUP_COUNTDOWN_URL', 'https://popup-builder.com/product/countdown-popup/');
		self::addDefine('SG_POPUP_RESTRICTION_URL', 'https://popup-builder.com/product/yes-no-button-popup/');
		self::addDefine('SG_POPUP_CONTACT_FORM_URL', 'https://popup-builder.com/product/contact-form-popup/');
		self::addDefine('SG_POPUP_INACTIVITY_URL', 'https://popup-builder.com/product/inactivity-event/');
		self::addDefine('SG_POPUP_SCHEDULING_URL', 'https://popup-builder.com/product/scheduling/');
		self::addDefine('SG_POPUP_GEO_TARGETING_URL', 'https://popup-builder.com/product/geo-targeting/');
		self::addDefine('SG_POPUP_RANDOM_URL', 'https://popup-builder.com/product/random-popup/');
		self::addDefine('SG_POPUP_ADVANCED_CLOSING_URL', 'https://popup-builder.com/product/advanced-closing/');
		self::addDefine('SG_POPUP_ADVANCED_TARGETING_URL', 'https://popup-builder.com/product/advanced-targeting/');
		self::addDefine('SG_POPUP_ALL_EXTENSIONS_URL', 'https://popup-builder.com/extensions/');
		self::addDefine('SG_POPUP_LOGIN_URL', 'https://popup-builder.com/product/login-popup/');
		self::addDefine('SG_POPUP_REGISTRATION_URL', 'https://popup-builder.com/product/registration-popup/');
		self::addDefine('SG_POPUP_SUBSCRIPTION_PLUS_URL', 'https://popup-builder.com/product/subscription-plus-popup/');
		self::addDefine('SG_POPUP_PUSH_NOTIFICATION_URL', 'https://popup-builder.com/product/web-push-notification-popup/');
		self::addDefine('SGPB_EDD_PLUGIN_URL', 'https://popup-builder.com/product/easy-digital-download-edd-popup/');
		self::addDefine('SGPB_PDF_PLUGIN_URL', 'https://popup-builder.com/product/pdf-popup/');
		self::addDefine('SGPB_GAMIFICATION_PLUGIN_URL', 'https://popup-builder.com/product/gamification-popup-pick-a-gift-popup/');
		self::addDefine('SGPB_AGE_VERIFICATION_PLUGIN_URL', 'https://popup-builder.com/product/age-restriction-popup/');
		self::addDefine('SG_POPUP_ADMIN_URL', admin_url());
		self::addDefine('SG_POPUP_BUILDER_URL', plugins_url().'/'.SG_POPUP_FOLDER_NAME.'/');
		self::addDefine('SG_POPUP_PLUGIN_PATH', WP_PLUGIN_DIR.'/');
		self::addDefine('SG_POPUP_BUILDER_PATH', SG_POPUP_PLUGIN_PATH.SG_POPUP_FOLDER_NAME.'/');
		self::addDefine('SG_POPUP_COM_PATH', SG_POPUP_BUILDER_PATH.'com/');
		self::addDefine('SG_POPUP_CONFIG_PATH', SG_POPUP_COM_PATH.'config/');
		self::addDefine('SG_POPUP_PUBLIC_PATH', SG_POPUP_BUILDER_PATH.'public/');
		self::addDefine('SG_POPUP_CLASSES_PATH', SG_POPUP_COM_PATH.'classes/');
		self::addDefine('SG_POPUP_COMPONENTS_PATH', SG_POPUP_CLASSES_PATH.'components/');
		self::addDefine('SG_POPUP_DATA_TABLES_PATH', SG_POPUP_CLASSES_PATH.'dataTable/');
		self::addDefine('SG_POPUP_CLASSES_POPUPS_PATH', SG_POPUP_CLASSES_PATH.'popups/');
		self::addDefine('SG_POPUP_EXTENSION_PATH', SG_POPUP_CLASSES_PATH.'extension/');
		self::addDefine('SGPB_POPUP_DETECTION_PATH', SG_POPUP_CLASSES_PATH.'_detection/');
		self::addDefine('SG_POPUP_LIBS_PATH', SG_POPUP_COM_PATH.'libs/');
		self::addDefine('SG_POPUP_HELPERS_PATH', SG_POPUP_COM_PATH.'helpers/');
		self::addDefine('SG_POPUP_JS_PATH', SG_POPUP_PUBLIC_PATH.'js/');
		self::addDefine('SG_POPUP_CSS_PATH', SG_POPUP_PUBLIC_PATH.'css/');
		self::addDefine('SG_POPUP_VIEWS_PATH', SG_POPUP_PUBLIC_PATH.'views/');
		self::addDefine('SG_POPUP_TYPE_OPTIONS_PATH', SG_POPUP_VIEWS_PATH.'options/');
		self::addDefine('SG_POPUP_TYPE_MAIN_PATH', SG_POPUP_VIEWS_PATH.'main/');
		self::addDefine('SG_POPUP_PUBLIC_URL', SG_POPUP_BUILDER_URL.'public/');
		self::addDefine('SG_POPUP_JS_URL', SG_POPUP_PUBLIC_URL.'js/');
		self::addDefine('SG_POPUP_CSS_URL', SG_POPUP_PUBLIC_URL.'css/');
		self::addDefine('SG_POPUP_IMG_URL', SG_POPUP_PUBLIC_URL.'img/');
		self::addDefine('SG_POPUP_SOUND_URL', SG_POPUP_PUBLIC_URL.'sound/');
		self::addDefine('SG_POPUP_VIEWS_URL', SG_POPUP_PUBLIC_URL.'views/');
		self::addDefine('SG_POPUP_EMAIL_TEMPLATES_URL', SG_POPUP_VIEWS_URL.'emailTemplates/');
		self::addDefine('SG_POPUP_DEFAULT_TIME_ZONE', 'UTC');
		self::addDefine('SG_POPUP_CATEGORY_TAXONOMY', 'popup-categories');
		self::addDefine('SG_POPUP_MINIMUM_PHP_VERSION', '5.3.3');
		self::addDefine('SG_POPUP_POST_TYPE', 'popupbuilder');
		self::addDefine('SG_POPUP_NEWSLETTER_PAGE', 'sgpbNewsletter');
		self::addDefine('SG_POPUP_SETTINGS_PAGE', 'sgpbSettings');
		self::addDefine('SG_POPUP_SUBSCRIBERS_PAGE', 'sgpbSubscribers');
		self::addDefine('SG_POPUP_SUPPORT_PAGE', 'sgpbSupport');
		self::addDefine('SGPB_POPUP_LICENSE', 'license');
		self::addDefine('SG_POPUP_EXTEND_PAGE', 'extend');
		self::addDefine('SGPB_FILTER_REPEAT_INTERVAL', 50);
		self::addDefine('SG_POPUP_TEXT_DOMAIN', 'popup-builder');
		self::addDefine('SG_POPUP_STORE_URL', 'https://popup-builder.com/');
		self::addDefine('SG_POPUP_AUTHOR', 'Sygnoos');
		self::addDefine('SG_POPUP_KEY', 'POPUP_BUILDER');
		self::addDefine('SG_AJAX_NONCE', 'popupBuilderAjaxNonce');
		self::addDefine('SG_CONDITION_FIRST_RULE', 0);
		self::addDefine('SGPB_AJAX_STATUS_FALSE', 0);
		self::addDefine('SGPB_AJAX_STATUS_TRUE', 1);
		self::addDefine('SGPB_SUBSCRIBERS_TABLE_NAME', 'sgpb_subscribers');
		self::addDefine('SGPB_POSTS_TABLE_NAME', 'posts');
		self::addDefine('SGPB_APP_POPUP_TABLE_LIMIT', 10);
		self::addDefine('SGPB_SUBSCRIBERS_ERROR_TABLE_NAME', 'sgpb_subscription_error_log');
		self::addDefine('SGPB_CRON_REPEAT_INTERVAL', 1);
		self::addDefine('SGPB_NOTIFICATIONS_CRON_REPEAT_INTERVAL', 12); /* Hours */
		self::addDefine('SGPB_METABOX_BANNER_CRON_TEXT_URL', 'https://popup-builder.com/sgpb-banner.php?banner=sidebar1');
		self::addDefine('SGPB_FACEBOOK_APP_ID', 540547196484707);
		self::addDefine('SGPB_POPUP_TYPE_RESTRICTION', 'ageRestriction');
		self::addDefine('SGPB_POPUP_DEFAULT_SOUND', 'popupOpenSound.wav');
		self::addDefine('SGPB_POPUP_EXTENSIONS_PATH', SG_POPUP_COM_PATH.'extensions/');
		self::addDefine('SG_POPUP_BUILDER_NOTIFICATIONS_URL', 'https://popup-builder.com/notifications.json');
		self::addDefine('SGPB_POPUP_ADVANCED_CLOSING_PLUGIN_KEY', 'popupbuilder-advanced-closing/PopupBuilderAdvancedClosing.php');
		self::addDefine('SGPB_DONT_SHOW_POPUP_EXPIRY', 365);
		self::addDefine('SGPB_CONTACT_FORM_7_BEHAVIOR_KEY', 'contact-form-7');
		self::addDefine('SGPB_CSS_CLASS_ACTIONS_KEY', 'setByCssClass');
		self::addDefine('SGPB_CLICK_ACTION_KEY', 'setByClick');
		self::addDefine('SGPB_HOVER_ACTION_KEY', 'setByHover');
		self::addDefine('SG_COUNTDOWN_COUNTER_SECONDS_SHOW', 1);
		self::addDefine('SG_COUNTDOWN_COUNTER_SECONDS_HIDE', 2);
		self::addDefine('SGPB_POPUP_SCHEDULING_EXTENSION_KEY', 'popupbuilder-scheduling/PopupBuilderScheduling.php');
		self::addDefine('SGPB_POPUP_GEO_TARGETING_EXTENSION_KEY', 'popupbuilder-geo-targeting/PopupBuilderGeoTargeting.php');
		self::addDefine('SGPB_POPUP_ADVANCED_TARGETING_EXTENSION_KEY', 'popupbuilder-advanced-targeting/PopupBuilderAdvancedTargeting.php');
		self::addDefine('SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY', 'popupbuilder-subscription-plus/PopupBuilderSubscriptionPlus.php');
		self::addDefine('SGPB_ASK_REVIEW_POPUP_COUNT', 80);
		self::addDefine('SGPB_REVIEW_POPUP_PERIOD', 30);
		self::addDefine('SGPB_POPUP_EXPORT_FILE_NAME', 'PopupBuilderPopups.xml');
		self::addDefine('SG_POPUP_AUTORESPONDER_POST_TYPE', 'sgpbautoresponder');
		self::addDefine('SG_POPUP_TEMPLATE_POST_TYPE', 'sgpbtemplate');
		self::addDefine('SGPB_INACTIVE_EXTENSIONS', 'inactivePBExtensions');
		self::addDefine('SGPB_POPUP_LICENSE_SCREEN', SG_POPUP_POST_TYPE.'_page_'.SGPB_POPUP_LICENSE);
		self::addDefine('SGPB_SUBSCRIPTION_ERROR_MESSAGE', __('There was an error while trying to send your request. Please try again', SG_POPUP_TEXT_DOMAIN).'.');
		self::addDefine('SGPB_SUBSCRIPTION_VALIDATION_MESSAGE', __('This field is required', SG_POPUP_TEXT_DOMAIN).'.');
		self::addDefine('SGPB_SUBSCRIPTION_EMAIL_MESSAGE', __('Please enter a valid email address', SG_POPUP_TEXT_DOMAIN).'.');
		self::addDefine('SGPB_TRANSIENT_TIMEOUT_HOUR', 60 * MINUTE_IN_SECONDS);
		self::addDefine('SGPB_TRANSIENT_TIMEOUT_DAY', 24 * HOUR_IN_SECONDS);
		self::addDefine('SGPB_TRANSIENT_TIMEOUT_WEEK', 7 * DAY_IN_SECONDS);
		self::addDefine('SGPB_TRANSIENT_POPUPS_LOAD', 'sgpbLoadPopups');
		self::addDefine('SGPB_TRANSIENT_POPUPS_TERMS', 'sgpbGetPopupsByTermSlug');
		self::addDefine('SGPB_TRANSIENT_POPUPS_ALL_CATEGORIES', 'sgpbGetPostsAllCategories');
		self::addDefine('SGPB_REGISTERED_PLUGINS_PATHS_MODIFIED', 'sgpbModifiedRegisteredPluginsPaths2');
		self::addDefine('SGPB_POPUP_BUILDER_REGISTERED_PLUGINS', 'sgpbPopupBuilderRegisteredPlugins');
		self::addDefine('SGPB_RATE_US_NOTIFICATION_ID', 'sgpbMainRateUsNotification');
		self::addDefine('SGPB_SUPPORT_BANNER_NOTIFICATION_ID', 'sgpbMainSupportBanner');
		self::addDefine('SGPB_REQUEST_URL', 'https://popup-builder.com/index.php');
		self::popupTypesInit();
	}

	public static function popupTypesInit()
	{
		global $SGPB_POPUP_TYPES;

		$SGPB_POPUP_TYPES['typeName'] = apply_filters('sgpbAddPopupType', array(
			'image' => SGPB_POPUP_PKG_FREE,
			'html' => SGPB_POPUP_PKG_FREE,
			'fblike' => SGPB_POPUP_PKG_FREE,
			'subscription' => SGPB_POPUP_PKG_FREE
		));

		$SGPB_POPUP_TYPES['typePath'] = apply_filters('sgpbAddPopupTypePath', array(
			'image' => SG_POPUP_CLASSES_POPUPS_PATH,
			'html' => SG_POPUP_CLASSES_POPUPS_PATH,
			'fblike' => SG_POPUP_CLASSES_POPUPS_PATH,
			'subscription' => SG_POPUP_CLASSES_POPUPS_PATH
		));

		$SGPB_POPUP_TYPES['typeLabels'] = apply_filters('sgpbAddPopupTypeLabels', array(
			'image' => __('Image', SG_POPUP_TEXT_DOMAIN),
			'html' => __('HTML', SG_POPUP_TEXT_DOMAIN),
			'fblike' => __('Facebook', SG_POPUP_TEXT_DOMAIN),
			'subscription' => __('Subscription', SG_POPUP_TEXT_DOMAIN)
		));
	}
}

SgpbPopupConfig::init();
