<?php
namespace sgpb;
use \WP_Query;
use \SgpbPopupConfig;
use sgpb\PopupBuilderActivePackage;
use sgpb\SGPopup;

class Filters
{
	private $activePopupsQueryString = '';

	public function setQueryString($activePopupsQueryString)
	{
		$this->activePopupsQueryString = $activePopupsQueryString;
	}

	public function getQueryString()
	{
		return $this->activePopupsQueryString;
	}

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		add_filter('admin_url', array($this, 'addNewPostUrl'), 10, 2);
		add_filter('wpseo_sitemap_exclude_post_type', array($this, 'excludeSitemapsYoast'), 10, 2);
		add_filter('admin_menu', array($this, 'removeAddNewSubmenu'), 10, 2);
		add_filter('manage_'.SG_POPUP_POST_TYPE.'_posts_columns', array($this, 'popupsTableColumns'));
		add_filter('post_row_actions', array($this, 'quickRowLinksManager'), 10, 2);
		add_filter('sgpbAdminJs', array($this, 'adminJsFilter'), 1, 1);
		add_filter('sgpbAdminCssFiles', array($this, 'sgpbAdminCssFiles'), 1, 1);
		add_filter('sgpbPopupContentLoadToPage', array($this, 'filterPopupContent'), 10, 2);
		add_filter('the_content', array($this, 'clearContentPreviewMode'), 10, 1);
		// The priority of this action should be higher than the extensions' init priority.
		add_action('init', array($this, 'excludePostToShowPrepare'), 99999999);
		add_filter('preview_post_link', array($this, 'editPopupPreviewLink'), 10, 2);
		add_filter('upgrader_pre_download', array($this, 'maybeShortenEddFilename'), 10, 4);
		add_filter('sgpbSavedPostData', array($this, 'savedPostData'), 10, 1);
		add_filter('sgpbPopupEvents', array($this, 'popupEvents'), 10, 1);
		add_filter('sgpbAdditionalMetaboxes', array($this, 'metaboxes'), 10, 1);
		add_filter('sgpbOptionAvailable', array($this, 'filterOption'), 10, 1);
		add_filter('export_wp_filename', array($this, 'exportFileName'), 10, 1);
		add_filter('sgpbAdvancedOptionsDefaultValues', array($this, 'defaultAdvancedOptionsValues'), 10, 1);
		add_filter('sgpbPopupContentLoadToPage', array($this, 'popupContentLoadToPage'), 10, 2);
		add_filter('sgpbExtraNotifications', array($this, 'sgpbExtraNotifications'), 10, 1);
		add_filter('sgpbSystemInformation', array($this, 'systemInformation'), 10, 1);
		add_filter('plugin_action_links', array($this, 'pluginActionLinks'), 10, 4);
		add_filter('plugin_row_meta', array( $this, 'pluginRowMetas'), 10, 4);
		add_filter('rank_math/sitemap/exclude_post_type', array($this, 'excludeRankMath'), 10, 2);
		add_filter('sgpbUserSelectionQuery', array($this, 'userSelectionQueryAddExtraAttributes'), 100, 1);
		add_filter('sgpbFilterOptionsBeforeSaving', array($this, 'filterOptionsBeforeSaving'), 100, 1);
		add_filter('sgpbPopupExtraData', array($this, 'popupExtraDataRender'), 100, 2);
		add_filter('wpml_link_to_translation', array($this, 'linkToTranslationWpml'), 30, 4);
		add_filter('pll_get_post_types', array($this, 'enablePBpostTypeForTranslating'), 10, 2);
	}

	public function enablePBpostTypeForTranslating($postTypes, $isSettings)
	{
	    $postTypes[SG_POPUP_POST_TYPE] = SG_POPUP_POST_TYPE;

	    return $postTypes;
	}

	public function linkToTranslationWpml($link, $postId, $lang, $trid)
	{
		if (strpos($link, SG_POPUP_POST_TYPE) && strpos($link, 'source_lang') && isset($trid)) {

			$popupType = 'html';
			$popup = SGPopup::find($postId);
			if (is_object($popup)) {
				$popupType = $popup->getType();
			}
			$link .= '&sgpb_type='.$popupType;
		}

		return $link;
	}

	public function popupExtraDataRender($popupId = 0, $popupOptions = array())
	{
		$floatingButton = '';
		if (empty($popupOptions['sgpb-enable-floating-button'])) {
			return $floatingButton;
		}
		$buttonStyles = 'z-index:99999999999;';
		$buttonClass = 'sgpb-'.$popupOptions['sgpb-floating-button-style'].'-'.$popupOptions['sgpb-floating-button-position'];

		if (isset($popupOptions['sgpb-floating-button-style']) && $popupOptions['sgpb-floating-button-style'] == 'basic' && strstr($popupOptions['sgpb-floating-button-position'], 'center')) {
			if (strstr($popupOptions['sgpb-floating-button-position'], 'top')) {
				$buttonStyles .= 'top: 0%;';
				$buttonStyles .= 'right: '.$popupOptions['sgpb-floating-button-position-right'].'%;';
			}
			else if (strstr($popupOptions['sgpb-floating-button-position'], 'bottom')) {
				$buttonStyles .= 'bottom: 0%;';
				$buttonStyles .= 'right: '.$popupOptions['sgpb-floating-button-position-right'].'%;';
			}
			else if (strstr($popupOptions['sgpb-floating-button-position'], 'left') || strstr($popupOptions['sgpb-floating-button-position'], 'right')) {
				$buttonStyles .= 'top: '.$popupOptions['sgpb-floating-button-position-top'].'%;';
			}
		}

		if (isset($popupOptions['sgpb-floating-button-font-size'])) {
			$buttonStyles .= 'font-size: '.$popupOptions['sgpb-floating-button-font-size'].'px;';
		}
		if (isset($popupOptions['sgpb-floating-button-border-size'])) {
			$buttonStyles .= 'border-width: '.$popupOptions['sgpb-floating-button-border-size'].'px;';
			$buttonStyles .= 'border-style: solid;';
		}
		if (isset($popupOptions['sgpb-floating-button-border-radius'])) {
			$buttonStyles .= 'border-radius: '.$popupOptions['sgpb-floating-button-border-radius'].'px;';
		}
		if (isset($popupOptions['sgpb-floating-button-border-color'])) {
			$buttonStyles .= 'border-color: '.$popupOptions['sgpb-floating-button-border-color'].';';
		}
		if (isset($popupOptions['sgpb-floating-button-bg-color'])) {
			$buttonStyles .= 'background-color: '.$popupOptions['sgpb-floating-button-bg-color'].';';
		}
		if (isset($popupOptions['sgpb-floating-button-text-color'])) {
			$buttonStyles .= 'color: '.$popupOptions['sgpb-floating-button-text-color'].';';
		}

		/*
		*  We first hide to make all CSS load then we will show once document is ready
		*  SgpbEventListener.eventsListenerAfterDocumentReady -> showFloatingButton
		*/
		$buttonStyles .= 'display: none;';

		$floatingButton = '<div class="'.$buttonClass.' sgpb-floating-button sg-popup-id-'.$popupId.'" style="'.$buttonStyles.'"><span class="sgpb-'.$popupOptions['sgpb-floating-button-style'].'-floating-button-text">'.$popupOptions['sgpb-floating-button-text'].'</span></div>';

		return $floatingButton;
	}

	public function filterOptionsBeforeSaving($unfilteredData = array())
	{
		$externalOptions = array(
			'icl_post_language' => 'sgpb-icl_post_language'
		);

		foreach ($externalOptions as $optionKey => $value) {
			if (isset($unfilteredData[$optionKey])) {
				$unfilteredData[$value] = $unfilteredData[$optionKey];
				// there is no need to unset the old index value, because in the next step we'll take only the values from indexes started with "sgpb"
			}
		}

		return $unfilteredData;
	}

	public function userSelectionQueryAddExtraAttributes($query)
	{
		$query .= ' status = 0 and unsubscribed = 0';

		return $query;
	}

	public function excludeRankMath($exclude, $type)
	{
		if ($type == SG_POPUP_POST_TYPE) {
			$exclude = true;
		}

		return $exclude;
	}

	public function pluginRowMetas($pluginMeta, $pluginFile, $pluginData, $status)
	{
		if (empty($pluginFile)) {
			return $pluginMeta;
		}

		$allExtensions = \SgpbDataConfig::allExtensionsKeys();
		$allExtensions = wp_list_pluck($allExtensions, 'pluginKey');
		$allExtensions[] = SG_POPUP_FILE_NAME;

		$rowMeta = array(
			'rateus' => '<a href="'.SG_POPUP_RATE_US_URL.'" target="_blank">'.esc_html__('Rate us', SG_POPUP_TEXT_DOMAIN).'</a>',
			'support' => '<a href="'.SG_POPUP_SUPPORT_URL.'" target="_blank">'.esc_html__('Support', SG_POPUP_TEXT_DOMAIN).'</a>'
		);

		if (in_array($pluginFile, $allExtensions)) {
			$pluginMeta = array_merge($pluginMeta, $rowMeta);
		}

		return $pluginMeta;
	}

	public function pluginActionLinks($actions, $pluginFile, $pluginData, $context)
	{
		if (empty($pluginFile)) {
			return $actions;
		}

		$allExtensions = \SgpbDataConfig::allExtensionsKeys();
		$allExtensions = wp_list_pluck($allExtensions, 'pluginKey');
		$allExtensions[] = SG_POPUP_FILE_NAME;

		$settingPageUrl = admin_url().'edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SG_POPUP_SETTINGS_PAGE;

		$links = array(
			'settings' => '<a href="'.$settingPageUrl.'" target="_blank">'.esc_html__('Settings', SG_POPUP_TEXT_DOMAIN).'</a>',
			'docs' => '<a href="'.SG_POPUP_TICKET_URL.'" target="_blank">'.esc_html__('Docs', SG_POPUP_TEXT_DOMAIN).'</a>'
		);

		if (in_array($pluginFile, $allExtensions)) {
			if ($pluginFile == SG_POPUP_FILE_NAME) {
				$getActiveExtensions = AdminHelper::getAllExtensions();
				if (empty($getActiveExtensions['active'])) {
					$links['upgrade'] = '<a style="color: #4364eb;" href="'.SG_POPUP_BUNDLE_URL.'" target="_blank">'.esc_html__('Upgrade', SG_POPUP_TEXT_DOMAIN).'</a>';
				}
			}
			$actions = array_merge($links, $actions);
		}

		return $actions;
	}

	public function systemInformation($infoContent)
	{

		$infoContent .= 'Platform:           '.isset($platform) ? $platform : '' . "\n";
		$infoContent .= 'Browser Name:       '.isset($bname) ? $bname : '' . "\n";
		$infoContent .= 'Browser Version:    '.isset($version) ? $version : '' . "\n";
		$infoContent .= 'User Agent:         '.isset($uAgent) ? $uAgent : '' . "\n";

		return $infoContent;
	}

	public function popupContentLoadToPage($content, $popupId)
	{
		$customScripts = AdminHelper::renderCustomScripts($popupId);
		$content .= $customScripts;

		return $content;
	}

	public function sgpbExtraNotifications($notifications = array())
	{
		$license = self::licenseNotification();
		if (!empty($license)) {
			$notifications[] = $license;
		}

		$promotional = self::promotionalNotifications();
		if (!empty($promotional)) {
			$notifications[] = $promotional;
		}

		$supportBanner = self::supportBannerNotifcations();
		if (!empty($supportBanner)) {
			$notifications[] = $supportBanner;
		}

		return $notifications;
	}

	public static function supportBannerNotifcations()
	{
		$hideSupportBanner = get_option('sgpb-hide-support-banner');
		if (!empty($hideSupportBanner)) {
			return array();
		}
		$message = AdminHelper::supportBannerNotification();
		$notification['id'] = SGPB_SUPPORT_BANNER_NOTIFICATION_ID;
		$notification['priority'] = 1;
		$notification['type'] = 1;
		$notification['message'] = $message;

		return $notification;
	}

	public static function promotionalNotifications()
	{
		$alreadyDone = get_option('SGPBCloseReviewPopup-notification');
		if (!empty($alreadyDone)) {
			return array();
		}
		$id = SGPB_RATE_US_NOTIFICATION_ID;
		$type = 1;
		$priority = 1;

		$maxOpenPopupStatus = AdminHelper::shouldOpenForMaxOpenPopupMessage();
		// popup opening count notification
		if ($maxOpenPopupStatus) {
			$message = AdminHelper::getMaxOpenPopupsMessage();
		}

		$shouldOpenForDays = AdminHelper::shouldOpenReviewPopupForDays();
		if ($shouldOpenForDays && !$maxOpenPopupStatus) {
			$message = AdminHelper::getMaxOpenDaysMessage();
		}
		if (empty($message)) {
			return array();
		}

		$alternateNotification['priority'] = $priority;
		$alternateNotification['type'] = $type;
		$alternateNotification['id'] = $id;
		$alternateNotification['message'] = $message;

		return $alternateNotification;
	}

	public function licenseNotification()
	{
		$inactiveExtensionNotice = array();
		$dontShowLicenseBanner = get_option('sgpb-hide-license-notice-banner');
		if ($dontShowLicenseBanner) {
			return $notifications;
		}

		$inactive = AdminHelper::getOption('SGPB_INACTIVE_EXTENSIONS');
		$hasInactiveExtensions = AdminHelper::hasInactiveExtensions();

		if (!$inactive) {
			AdminHelper::updateOption('SGPB_INACTIVE_EXTENSIONS', 1);
			if ($hasInactiveExtensions) {
				AdminHelper::updateOption('SGPB_INACTIVE_EXTENSIONS', 'inactive');
				$inactive = 'inactive';
			}

		}

		if ($hasInactiveExtensions && $inactive == 'inactive') {
			$licenseSectionUrl = menu_page_url(SGPB_POPUP_LICENSE, false);
			$partOfContent = '<br><br>'.__('<a href="'.$licenseSectionUrl.'">Follow the link</a> to finalize the activation.', SG_POPUP_TEXT_DOMAIN);
			$message = '<b>'.__('Thank you for choosing our plugin!', SG_POPUP_TEXT_DOMAIN).'</b>';
			$message .= '<br>';
			$message .= '<br>';
			$message .= '<b>'.__('You have activated Popup Builder extension(s). Please, don\'t forget to activate the license key(s) as well.', SG_POPUP_TEXT_DOMAIN).'</b>';
			$message .= '<b>'.$partOfContent.'</b>';

			$inactiveExtensionNotice['priority'] = 1;
			$inactiveExtensionNotice['type'] = 2;
			$inactiveExtensionNotice['id'] = 'sgpbMainActiveInactiveLicense';
			$inactiveExtensionNotice['message'] = $message;
		}

		return $inactiveExtensionNotice;
	}

	public function excludeSitemapsYoast($exclude, $postType)
	{
		$postTypeObject = get_post_type_object($postType);
		if (!is_object($postTypeObject)) {
			return $exclude;
		}

		if ($postTypeObject->public === false || $postType == SG_POPUP_POST_TYPE) {
			return true;
		}

		return $exclude;
	}

	public function defaultAdvancedOptionsValues($options = array())
	{
		$enablePopupOverlay = PopupBuilderActivePackage::canUseOption('sgpb-enable-popup-overlay');
		if (!$enablePopupOverlay) {
			$options['sgpb-enable-popup-overlay'] = 'on';
		}

		return $options;
	}

	public function excludePostToShowPrepare()
	{
		SgpbPopupConfig::popupTypesInit();
		$queryString = SGPopup::getActivePopupsQueryString();
		$this->setQueryString($queryString);
		add_filter('posts_where' , array($this, 'excludePostsToShow'), 10, 1);
	}

	public function exportFileName($fileName)
	{
		if (!empty($_GET['sgpbExportAction'])) {
			return SGPB_POPUP_EXPORT_FILE_NAME;
		}

		return $fileName;
	}

	public function filterOption($filterOption)
	{
		$extensionOptionsData = AdminHelper::getExtensionAvaliabilityOptions();

		if (empty($extensionOptionsData)) {
			return $filterOption;
		}

		foreach ($extensionOptionsData as $extensionKey => $extensionOptions) {
			$isAdvancedClosingActive = is_plugin_active($extensionKey);
			if (isset($filterOption['name']) && !$isAdvancedClosingActive) {
				$name = $filterOption['name'];

				if (in_array($name, $extensionOptions)) {
					$filterOption['status'] = false;
				}
			}
		}

		return $filterOption;
	}

	public function metaboxes($metaboxes)
	{
		$otherConditionsProLabel = '';
		$otherConditionsCanBeUsed = PopupBuilderActivePackage::canUseSection('popupOtherConditionsSection');
		if (!$otherConditionsCanBeUsed) {
			$otherConditionsProLabel .= '<a href="'.SG_POPUP_SCHEDULING_URL.'" target="_blank" class="sgpb-pro-label-metabox">';
			$otherConditionsProLabel .= __('UNLOCK OPTION', SG_POPUP_TEXT_DOMAIN).'</a>';
		}
		$metaboxes['allMetaboxesView'] = array(
			'key' => 'allMetaboxesView',
			'displayName' => 'Main Options',
			'short_description' => 'main options',
			'filePath' => SG_POPUP_VIEWS_PATH.'allMetaboxesView.php',
			'priority' => 'high'
		);

		$metaboxes['targetMetaboxView'] = array(
			'key' => 'targetMetaboxView',
			'displayName' => 'Display Rules',
			'short_description' => 'Select where on the website to trigger the popup',
			'filePath' => SG_POPUP_VIEWS_PATH.'targetView.php',
			'priority' => 'high'
		);

		$metaboxes['eventsMetaboxView'] = array(
			'key' => 'eventsMetaboxView',
			'displayName' => 'Events',
			'short_description' => 'Select the specific actions that will trigger the popup, default event is on load',
			'filePath' => SG_POPUP_VIEWS_PATH.'eventsView.php',
			'priority' => 'high'
		);

		$metaboxes['conditionsMetaboxView'] = array(
			'key' => 'conditionsMetaboxView',
			'displayName' => 'Conditions',
			'short_description' => 'Select advanced conditions for more professional targeting',
			'filePath' => SG_POPUP_VIEWS_PATH.'conditionsView.php',
			'priority' => 'high'
		);

		$metaboxes['behaviorAfterSpecialEventsMetaboxView'] = array(
			'key' => 'behaviorAfterSpecialEventsMetaboxView',
			'displayName' => 'Behavior After Special Events',
			'short_description' => 'Select special events that will trigger the popup',
			'filePath' => SG_POPUP_VIEWS_PATH.'behaviorAfterSpecialEventsView.php',
			'priority' => 'high'
		);

		$metaboxes['popupDesignMetaBoxView'] = array(
			'key' => 'popupDesignMetaBoxView',
			'displayName' => 'Design',
			'short_description' => 'Customize design, background and overlay of the popup',
			'filePath' => SG_POPUP_VIEWS_PATH.'popupDesignView.php',
			'priority' => 'high'
		);

		$metaboxes['closeSettings'] = array(
			'key' => 'closeSettings',
			'displayName' => 'Closing',
			'short_description' => 'Select the desired popup closing method, customize close button',
			'filePath' => SG_POPUP_VIEWS_PATH.'closeSettingsView.php',
			'priority' => 'high'
		);

		$metaboxes['dimensions'] = array(
			'key' => 'dimensions',
			'displayName' => 'Dimensions',
			'short_description' => 'Make your popup mobile friendly and responsive for all devices',
			'filePath' => SG_POPUP_VIEWS_PATH.'dimensionsView.php',
			'priority' => 'high'
		);

		$metaboxes['optionsMetaboxView'] = array(
			'key' => 'optionsMetaboxView',
			'displayName' => 'Advanced',
			'short_description' => 'Use advanced features for popup animations, opening and closing actions',
			'filePath' => SG_POPUP_VIEWS_PATH.'optionsView.php',
			'priority' => 'high'
		);

		$metaboxes['otherConditionsMetaBoxView'] = array(
			'key' => 'otherConditionsMetaBoxView',
			'displayName' => 'Scheduling'.$otherConditionsProLabel,
			'short_description' => 'Schedule your popup for a particular day or for a selected timeframe',
			'filePath' => SG_POPUP_VIEWS_PATH.'otherConditionsView.php',
			'priority' => 'high'
		);

		$metaboxes['floatingButton'] = array(
			'key' => 'floatingButton',
			'displayName' => 'Floating Button',
			'short_description' => 'Set the popup to appear on a scrollable call to action button',
			'filePath' => SG_POPUP_VIEWS_PATH.'floatingButton.php',
			'context' => 'side',
			'priority' => 'high'
		);

		$metaboxes['popupOpeningCounter'] = array(
			'key' => 'popupOpeningCounter',
			'displayName' => 'Statistics',
			'short_description' => 'Enable or disable the counts of the popup',
			'filePath' => SG_POPUP_VIEWS_PATH.'popupOpeningCounter.php',
			'context' => 'side',
			'priority' => 'low'
		);

		$metaboxes['customCssJs'] = array(
			'key' => 'customCssJs',
			'displayName' => 'Custom JS or CSS',
			'short_description' => 'Add custom JS and/or CSS code for extra control',
			'filePath' => SG_POPUP_VIEWS_PATH.'customEditor.php',
			'priority' => 'low'
		);

		$metaboxes = apply_filters('sgpbPopupTypeMainViewMetaboxes', $metaboxes);

		return $metaboxes;
	}

	public function popupEvents($events)
	{
		foreach ($events as $eventKey => $eventData) {
			if (isset($eventData['param'])) {
				if ($eventData['param'] == SGPB_CSS_CLASS_ACTIONS_KEY) {
					unset($events[$eventKey]);
					$events[] = array('param' => 'click');
					$events[] = array('param' => 'hover');
					$events[] = array('param' => 'confirm');
				}
				else if ($eventData['param'] == SGPB_CLICK_ACTION_KEY) {
					$events[$eventKey]['param'] = 'click';
				}
				else if ($eventData['param'] == SGPB_HOVER_ACTION_KEY) {
					$events[$eventKey]['param'] = 'hover';
				}
			}
		}

		return $events;
	}

	public function savedPostData($postData)
	{
		// for old popups here we change already saved old popup id
		if (isset($postData['sgpb-mailchimp-success-popup'])) {
			// sgpGetCorrectPopupId it's a temporary function and it will be removed in future
			if (function_exists(__NAMESPACE__.'\sgpGetCorrectPopupId')) {
				$postData['sgpb-mailchimp-success-popup'] = sgpGetCorrectPopupId($postData['sgpb-mailchimp-success-popup']);
			}
		}
		// for old popups here we change already saved old popup id
		if (isset($postData['sgpb-aweber-success-popup'])) {
			if (function_exists(__NAMESPACE__.'\sgpGetCorrectPopupId')) {
				$postData['sgpb-aweber-success-popup'] = sgpGetCorrectPopupId($postData['sgpb-aweber-success-popup']);
			}
		}

		return $postData;
	}

	public function removeAddNewSubmenu()
	{
		//we don't need the default add new, since we are using our custom page for it
		$page = remove_submenu_page(
			'edit.php?post_type='.SG_POPUP_POST_TYPE,
			'post-new.php?post_type='.SG_POPUP_POST_TYPE
		);
	}

	public function maybeShortenEddFilename($return, $package)
	{
		if (strpos($package, SG_POPUP_STORE_URL) !== false) {
			add_filter('wp_unique_filename', array($this, 'shortenEddFilename'), 100, 2);
		}
		return $return;
	}

	public function shortenEddFilename($filename, $ext)
	{
		$filename = substr($filename, 0, 20).$ext;
		remove_filter('wp_unique_filename', array($this, 'shortenEddFilename'), 10);
		return $filename;
	}

	public function editPopupPreviewLink($previewLink = '', $post = array())
	{
		if (!empty($post) && $post->post_type == SG_POPUP_POST_TYPE) {
			$popupId = $post->ID;
			$targets = get_post_meta($popupId, 'sg_popup_target_preview', true);
			if (empty($targets['sgpb-target'][0])) {
				return $previewLink .= '/?sg_popup_preview_id='.$popupId;
			}
			$targetParams = isset($targets['sgpb-target'][0][0]['param']) ? $targets['sgpb-target'][0][0]['param'] : '';
			if ((!empty($targetParams) && $targetParams == 'not_rule') || empty($targetParams)) {
				$previewLink = home_url();
				$previewLink .= '/?sg_popup_preview_id='.$popupId;

				return $previewLink;
			}
			foreach ($targets['sgpb-target'][0] as $targetKey => $targetValue) {
				if (!isset($targetValue['operator']) || $targetValue['operator'] == '!=') {
					continue;
				}
				$previewLink = self::getPopupPreviewLink($targetValue, $popupId);
				$previewLink .= '/?sg_popup_preview_id='.$popupId;
			}
		}

		return $previewLink;
	}

	public static function getPopupPreviewLink($targetData, $popupId)
	{
		$previewLink = home_url();

		if (empty($targetData['param'])) {
			return $previewLink;
		}
		$targetParam = $targetData['param'];

		if ($targetParam == 'everywhere') {
			return $previewLink;
		}

		$args = array(
			'orderby'   => 'rand'
		);

		// posts
		if (strpos($targetData['param'], '_all')) {
			if ($targetData['param'] == 'post_all') {
				$args['post_type'] = 'post';
			}
			if ($targetData['param'] == 'page_all') {
				$args['post_type'] = 'page';
			}
		}
		if ($targetData['param'] == 'post_type' && !empty($targetData['value'])) {
			$args['post_type'] = $targetData['value'];
		}
		if ($targetData['param'] == 'page_type' && !empty($targetData['value'])) {
			$pageTypes = $targetData['value'];
			foreach ($pageTypes as $pageType) {

				if ($pageType == 'is_home_page') {
					if (is_front_page() && is_home()) {
						// default homepage
						return get_home_url();
					}
					else if (is_front_page()) {
						// static homepage
						return get_home_url();
					}
				}
				else if (function_exists($pageType)) {
					if ($pageType == 'is_home') {
						return get_home_url();
					}
					else if ($pageType == 'is_search') {
						return get_search_link();
					}
					else if ($pageType == 'is_shop') {
						return get_home_url().'/shop/';
					}
				}
			}
		}
		if (isset($args['post_type'])) {
			$the_query = new WP_Query($args);
			foreach ($the_query->posts as $post) {
				$postId = $post->ID;
				if (get_permalink($postId)) {
					return get_permalink($postId);
				}
			}
		}
		// selected post/page/custom_post_types...
		if (strpos($targetData['param'], '_selected') && !empty($targetData['value'])) {
			$value = array_keys($targetData['value']);
			if (!empty($value[0])) {
				if (get_permalink($value[0])) {
					return get_permalink($value[0]);
				}
			}
		}
		if (strpos($targetData['param'], '_archive') && !empty($targetData['value'])) {
			$value = array_keys($targetData['value']);
			if (!empty($value[0])) {
				if (get_permalink($value[0])) {
					return get_permalink($value[0]);
				}
			}
		}

		return $previewLink;
	}

	public function excludePostsToShow($where)
	{
		if (function_exists('is_admin') && is_admin()) {
			if (!function_exists('get_current_screen')) {
				return $where;
			}

			$screen = get_current_screen();
			if (empty($screen)) {
				return $where;
			}

			$postType = $screen->post_type;
			if ($postType == SG_POPUP_POST_TYPE &&
				$screen instanceof \WP_Screen &&
				$screen->id === 'edit-popupbuilder') {
				if (class_exists('sgpb\SGPopup')) {
					$activePopupsQuery = $this->getQueryString();
					if ($activePopupsQuery && $activePopupsQuery != '') {
						$where .= $activePopupsQuery;
					}
				}
			}
		}

		return $where;
	}

	public function clearContentPreviewMode($content)
	{
		global $post_type;

		if (is_preview() && $post_type == SG_POPUP_POST_TYPE) {
			$content = '';
		}

		return $content;
	}

	public function filterPopupContent($content, $popupId)
	{
		preg_match_all('/<iframe.*?src="(.*?)".*?<\/iframe>/', $content, $matches);
		/*$finalContent = '';*/
		// $matches[0] array contain iframes stings
		// $matches[1] array contain iframes URLs
		if (empty($matches) && empty($matches[0]) && empty($matches[1])) {
			return $content;
		}
		$urls = $matches[1];

		foreach ($matches[0] as $key => $iframe) {
			if (empty($urls[$key])) {
				continue;
			}

			$pos = strpos($iframe, $urls[$key]);

			if ($pos === false) {
				continue;
			}

			$content = str_replace(' src="'.$urls[$key].'"', ' src="" data-attr-src="'.esc_attr($urls[$key]).'"', $content);
		}
		if (function_exists('do_blocks')) {
			$content = do_blocks($content);
		}
		// check if inside the popup included the same popup! this will prevent from the infinite loop of nested popups doing shortcode
		preg_match_all('/\[sg_popup.*?id="'.$popupId.'".*?\[\/sg_popup\]/', $content, $matches1);
		if (has_shortcode( $content, 'sg_popup' ) && (!empty($matches1) &&!empty($matches1[0]))){
			return $content;
		}

		return do_shortcode($content);
	}

	public function addNewPostUrl($url, $path)
	{
		if ($path == 'post-new.php?post_type='.SG_POPUP_POST_TYPE) {
			$url = str_replace('post-new.php?post_type='.SG_POPUP_POST_TYPE, 'edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SG_POPUP_POST_TYPE, $url);
		}

		return $url;
	}

	public function popupsTableColumns($columns)
	{
		unset($columns['date']);

		$additionalItems = array();
		$additionalItems['counter'] = __('Views', SG_POPUP_TEXT_DOMAIN);
		$additionalItems['onOff'] = __('Status', SG_POPUP_TEXT_DOMAIN);
		$filterColumnsDisplaySettings = apply_filters('sgpbAddRandomColumnIntoAllPopupsViewTable', $additionalItems);

		if (isset($filterColumnsDisplaySettings['sgpbIsRandomEnabled'])){
			$additionalItems = $filterColumnsDisplaySettings;
		}

		$additionalItems['type'] = __('Type', SG_POPUP_TEXT_DOMAIN);
		$additionalItems['shortcode'] = __('Shortcode', SG_POPUP_TEXT_DOMAIN);
		$additionalItems['className'] = __('Class', SG_POPUP_TEXT_DOMAIN);
		$additionalItems['options'] = __('Actions', SG_POPUP_TEXT_DOMAIN);

		return $columns + $additionalItems;
	}

	/**
	 * Function to add/hide links from popups dataTable row
	 */
	public function quickRowLinksManager($actions, $post)
	{
		global $post_type;

		if ($post_type != SG_POPUP_POST_TYPE) {
			return $actions;
		}
		// remove quick edit link
		unset($actions['inline hide-if-no-js']);
		// remove view link
		unset($actions['view']);
		$url = AdminHelper::popupGetClonePostLink($post->ID , 'display', false);

		$actions['clone'] = '<a href="'.$url.'" title="';
		$actions['clone'] .= esc_attr__("Clone this item", SG_POPUP_TEXT_DOMAIN);
		$actions['clone'] .= '">'. esc_html__('Clone', SG_POPUP_TEXT_DOMAIN).'</a>';

		return $actions;
	}

	/* media button scripts */
	public function adminJsFilter($jsFiles)
	{
		$allowToShow = MediaButton::allowToShow();
		if ($allowToShow) {
			$jsFiles['jsFiles'][] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'select2.min.js');
			$jsFiles['jsFiles'][] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'sgpbSelect2.js');
			$jsFiles['jsFiles'][] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'Popup.js');
			$jsFiles['jsFiles'][] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'PopupConfig.js');
			$jsFiles['jsFiles'][] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'MediaButton.js');

			$jsFiles['localizeData'][] = array(
				'handle' => 'Popup.js',
				'name' => 'sgpbPublicUrl',
				'data' => SG_POPUP_PUBLIC_URL
			);

			$jsFiles['localizeData'][] = array(
				'handle' => 'MediaButton.js',
				'name' => 'mediaButtonParams',
				'data' => array(
					'currentPostType' => get_post_type(),
					'popupBuilderPostType' => SG_POPUP_POST_TYPE,
					'ajaxUrl'   => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce(SG_AJAX_NONCE)
				)
			);
		}

		return $jsFiles;
	}

	/* media button styles */
	public function sgpbAdminCssFiles($cssFiles)
	{
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'sgbp-bootstrap.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'select2.min.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'popupAdminStyles.css', 'dep' => array(), 'ver' => SG_POPUP_VERSION, 'inFooter' => false);
		$cssFiles[] = array('folderUrl' => SG_POPUP_CSS_URL, 'filename' => 'newDesignFromBuild.css', 'dep' => array(), 'ver' => rand(1, 10000), 'inFooter' => false);

		return $cssFiles;
	}

}


