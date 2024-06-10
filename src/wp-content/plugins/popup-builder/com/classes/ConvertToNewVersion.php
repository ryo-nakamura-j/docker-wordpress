<?php
namespace sgpb;
use sgpb\AdminHelper;
use \ConfigDataHelper;

class ConvertToNewVersion
{
	private $id;
	private $content = '';
	private $type;
	private $title;
	private $options;
	private $customOptions = array();

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setType($type)
	{
		if ($type == 'shortcode') {
			$type = 'html';
		}
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setId($id)
	{
		$this->id = (int)$id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function setCustomOptions($customOptions)
	{
		$this->customOptions = $customOptions;
	}

	public function getCustomOptions()
	{
		return $this->customOptions;
	}

	public static function convert()
	{
		$obj = new self();
		$obj->insertDataToNew();
	}

	public function insertDataToNew()
	{
		$idsMapping = array();
		Installer::install();
		Installer::registerPlugin();
		$popups = $this->getAllSavedPopups();
		$this->convertSettings();

		$arr = array();
		$popupPreviewId = get_option('popupPreviewId');
		foreach ($popups as $popup) {
			if (empty($popup)) {
				continue;
			}
			// we should not convert preview popup
			if ($popup['id'] == $popupPreviewId) {
				continue;
			}

			$popupObj = $this->popupObjectFromArray($popup);
			$arr[] = $popupObj;
			$args = array(
				'post_title' => $popupObj->getTitle(),
				'post_content' => $popupObj->getContent(),
				'post_status' => 'publish',
				'post_type' => SG_POPUP_POST_TYPE
			);
			$id = $popupObj->getId();
			$newOptions = $this->getNewOptionsFormSavedData($popupObj);
			$newPopupId = @wp_insert_post($args);
			$newOptions['sgpb-post-id'] = $newPopupId;
			$this->saveOtherOptions($newOptions);

			update_post_meta($newPopupId, 'sg_popup_options', $newOptions);
			$idsMapping[$id] = $newPopupId;
		}

		$this->convertCounter($idsMapping);
		$this->convertSubscribers();

		update_option('sgpbConvertedIds', $idsMapping);

		return $arr;
	}

	public function convertSubscribers()
	{
		global $wpdb;
		$subscribersSql = 'SELECT `id`, `firstName`, `lastName`, `email`, `subscriptionType`, `status` from '.$wpdb->prefix.'sg_subscribers';
		$subscribers = $wpdb->get_results($subscribersSql, ARRAY_A);

		if (empty($subscribers)) {
			return false;
		}

		foreach ($subscribers as $subscriber) {
			$subscriber['subscriptionType'] = $this->getPostByTitle($subscriber['subscriptionType']);

			$date = date('Y-m-d');
			$sql = $wpdb->prepare('INSERT INTO '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' (`firstName`, `lastName`, `email`, `cDate`, `subscriptionType`, `unsubscribed`) VALUES (%s, %s, %s, %s, %d, %d) ', $subscriber['firstName'], $subscriber['lastName'], $subscriber['email'], $date, $subscriber['subscriptionType'], 0);
			$wpdb->query($sql);
		}
	}

	private function getPostByTitle($pageTitle, $output = OBJECT)
	{
		global $wpdb;
		$post = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='popupbuilder'", $pageTitle));
		if (!empty($post)) {
			return get_post($post, $output)->ID;
		}

		return null;
	}

	/**
	 * Convert settings section saved options to new version
	 *
	 * @since 2.6.7.6
	 *
	 * @return bool
	 */
	private function convertSettings()
	{
		global $wpdb;
		$settings = $wpdb->get_row('SELECT options FROM '.$wpdb->prefix .'sg_popup_settings WHERE id = 1', ARRAY_A);

		if (empty($settings['options'])) {
			return false;
		}
		$settings = json_decode($settings['options'], true);

		$deleteData = 0;

		if (!empty($settings['tables-delete-status'])) {
			$deleteData = 1;
		}
		$userRoles = $settings['plugin_users_role'];

		if (empty($userRoles) || !is_array($userRoles)) {
			$userRoles = array();
		}

		$userRoles = array_map(function($role) {
			// it's remove sgpb_ keyword from selected values
			$role = substr($role, 5, strlen($role)-1);

			return $role;
		}, $userRoles);

		update_option('sgpb-user-roles', $userRoles);
		update_option('sgpb-dont-delete-data', $deleteData);

		return true;
	}

	private function convertCounter($idsMapping)
	{
		$oldCounter = get_option('SgpbCounter');

		if (!$oldCounter) {
			return false;
		}
		$newCounter = array();
		foreach ($oldCounter as $key => $value) {
			if (isset($idsMapping[$key])){
				$newId = $idsMapping[$key];
				$newCounter[$newId] = $value;
			}
		}

		update_option('SgpbCounter', $newCounter);

		return true;
	}


	private function getAllSavedPopups()
	{
		global $wpdb;

		$query = 'SELECT `id`, `type`, `title`, `options` from '.$wpdb->prefix.'sg_popup ORDER BY id';
		$popups = $wpdb->get_results($query, ARRAY_A);

		return $popups;
	}

	public function getNewOptionsFormSavedData($popup)
	{
		$options = $popup->getOptions();
		$customOptions = $popup->getCustomOptions();
		$options = array_merge($options, $customOptions);
		// return addons event from add_connections data
		$addonsEvent = $this->getAddonsEventFromPopup($popup);
		if ($addonsEvent) {
			$options = array_merge($options, $addonsEvent);
		}
		$options = $this->filterOptions($options);

		$names = $this->getNamesMapping();
		$newData = array();
		$type = $popup->getType();

		if (empty($names)) {
			return $newData;
		}

		$newData['sgpb-type'] = $type;

		foreach ($names as $oldName => $newName) {
			if (isset($options[$oldName])) {
				$optionName = $this->changeOldValues($oldName, $options[$oldName]);
				$newData[$newName] = $optionName;
			}
		}
		$newData['sgpb-enable-popup-overlay'] = 'on';
		$newData['sgpb-show-background'] = 'on';

		return $newData;
	}

	private function saveOtherOptions($options)
	{
		$popupId = (int)$options['sgpb-post-id'];
		$mobileOperator = '';
		$conditions = array();
		$conditions['sgpb-target'] = array(array(array('param' => 'not_rule')));
		$conditions['sgpb-conditions'] = array(array());

		$eventsInitialData = array(
			array(
			)
		);

		if (!empty($options['sgpb-option-exit-intent-enable'])) {
			$eventsInitialData[0][] = array(
				'param' => 'exitIntent',
				'value' => isset($options['sgpb-option-exit-intent-type']) ? $options['sgpb-option-exit-intent-type'] : '',
				'hiddenOption' => array(
					'sgpb-exit-intent-expire-time' => isset($options['sgpb-exit-intent-expire-time']) ? $options['sgpb-exit-intent-expire-time'] : '',
					'sgpb-exit-intent-cookie-level' => isset($options['sgpb-exit-intent-cookie-level']) ? $options['sgpb-exit-intent-cookie-level'] : '',
					'sgpb-exit-intent-soft-from-top' => isset($options['sgpb-exit-intent-soft-from-top']) ? $options['sgpb-exit-intent-soft-from-top'] : ''
				)
			);
		}
		else if (!empty($options['sgpb-option-enable-ad-block'])) {
			$eventsInitialData[0][] = array(
				'param' => 'AdBlock',
				'value' => $options['sgpb-popup-delay'],
				'hiddenOption' => array()
			);
		}

		// after inactivity
		if (!empty($options['sgpb-inactivity-status'])) {
			$eventsInitialData[0][] = array(
				'param' => 'inactivity',
				'value' => isset($options['sgpb-inactivity-timer']) ? $options['sgpb-inactivity-timer'] : '',
				'hiddenOption' => array()
			);
		}

		// after scroll
		if (!empty($options['sgpb-onscroll-status'])) {
			$eventsInitialData[0][] = array(
				'param' => 'onScroll',
				'value' => isset($options['sgpb-onscroll-percentage']) ? $options['sgpb-onscroll-percentage'] : '',
				'hiddenOption' => array()
			);
		}

		if (empty($eventsInitialData[0])) {
			$eventsInitialData[0][] = array('param' => 'load', 'value' => '');
		}

		update_post_meta($popupId, 'sg_popup_events', $eventsInitialData);

		// by user status (logged in/out)
		if (!empty($options['sgpb-by-user-status'])) {
			$operator = '==';
			if (isset($options['sgpb-for-logged-in-user']) && $options['sgpb-for-logged-in-user'] === 'false') {
				$operator = '!=';
			}
			$conditions['sgpb-conditions'][0][] = array(
				'param' => 'groups_user_role',
				'operator' => $operator,
				'value' => 'loggedIn'
			);
		}

		// hide or show on mobile
		if (isset($options['sgpb-hide-on-mobile']) && $options['sgpb-hide-on-mobile'] == 'on') {
			$conditions['sgpb-conditions'][0][] = array(
				'param' => 'groups_devices',
				'operator' => '!=',
				'value' => array(
					'is_mobile'
				)
			);
		}
		if (isset($options['sgpb-only-on-mobile']) && $options['sgpb-only-on-mobile'] == 'on') {
			$conditions['sgpb-conditions'][0][] = array(
				'param' => 'groups_devices',
				'operator' => '==',
				'value' => array(
					'is_mobile'
				)
			);
		}

		// detect by country
		if (isset($options['sgpb-by-country']) && $options['sgpb-by-country'] == 'on') {
			if (isset($options['sgpb-allow-countries'])) {
				$options['sgpb-allow-countries'] = '!=';
				if ($options['sgpb-allow-countries'] == 'allow') {
					$options['sgpb-allow-countries'] = '==';
				}
			}
			$conditions['sgpb-conditions'][0][] = array(
				'param' => 'groups_countries',
				'operator' => isset($options['sgpb-allow-countries']) ? $options['sgpb-allow-countries'] : '',
				'value' => explode(',', (isset($options['sgpb-countries-iso']) ? $options['sgpb-countries-iso'] : ''))
			);
		}

		update_post_meta($popupId, 'sg_popup_target', $conditions);

		// random popup
		if (isset($options['sgpb-random-popup']) && $options['sgpb-random-popup'] == 'on') {
			$randomPopupCategory = AdminHelper::getTaxonomyBySlug(SG_RANDOM_TAXONOMY_SLUG);
			wp_set_object_terms($popupId, SG_RANDOM_TAXONOMY_SLUG, SG_POPUP_CATEGORY_TAXONOMY, true);
		}

		$this->saveProTarget($options);

		// MailChimp
		$mailchimpApiKey = get_option("SG_MAILCHIMP_API_KEY");

		if ($mailchimpApiKey) {
			update_option('SGPB_MAILCHIMP_API_KEY', $mailchimpApiKey);
		}

		// AWeber
		$aweberAccessToken = get_option('sgAccessToken');
		if ($aweberAccessToken) {
			$requestTokenSecret = get_option('requestTokenSecret');
			$accessTokenSecret = get_option('sgAccessTokenSecret');

			update_option('sgpbRequestTokenSecret', $requestTokenSecret);
			update_option('sgpbAccessTokenSecret', $accessTokenSecret);
			update_option('sgpbAccessToken', $aweberAccessToken);
		}

		return $options;
	}

	public function saveProTarget($options)
	{
		if (empty($options)) {
			return;
		}
		$popupId = (int)$options['sgpb-post-id'];
		// It's got already saved targets for do not override already saved data
		$popupSavedTarget = get_post_meta($popupId, 'sg_popup_target');
		$target = array();
		$target['sgpb-target'] = array();
		$target['sgpb-conditions'] = array();

		if (!empty($popupSavedTarget[0]['sgpb-conditions'])) {
			$target['sgpb-conditions'] = $popupSavedTarget[0]['sgpb-conditions'];
		}
		$isSavedToHome = false;

		if (!empty($options['allPagesStatus'])) {

			if ($options['allPages'] == 'selected') {
				$savedPages = isset($options['allSelectedPages']) ? (array)$options['allSelectedPages'] : array();
				$savedPagesValues = array_values($savedPages);

				// -1 mean saved for home page
				if (in_array('-1', $savedPagesValues)) {
					$isSavedToHome = true;
				}
				$args = array(
					'post__in' => $savedPagesValues,
					'posts_per_page' => 10,
					'post_type'	  => 'page'
				);

				$searchResults = ConfigDataHelper::getPostTypeData($args);
				if (!empty($searchResults)) {
					$target['sgpb-target'][0][] = array('param' => 'page_selected', 'operator' => '==', 'value' => $searchResults);
				}
			}
			else {
				$target['sgpb-target'][0][] = array('param' => 'page_all', 'operator' => '==');
			}
		}

		if (!empty($options['allPostsStatus'])) {
			$allPosts = $options['allPosts'];
			if ($allPosts == 'selected') {
				$savedPosts = (array)$options['allSelectedPosts'];
				$savedPostsValues = array_values($savedPosts);
				// -1 mean saved for home page
				if (in_array('-1', $savedPostsValues)) {
					$isSavedToHome = true;
				}
				$args = array(
					'post__in' => $savedPostsValues,
					'posts_per_page' => 10,
					'post_type'	  => 'post'
				);

				$searchResults = ConfigDataHelper::getPostTypeData($args);
				if (!empty($searchResults)) {
					$target['sgpb-target'][0][] = array('param' => 'post_selected', 'operator' => '==', 'value' => $searchResults);
				}
			}
			else if ($allPosts == 'all') {
				$target['sgpb-target'][0][] = array('param' => 'post_all', 'operator' => '==');
			}
			else {
				$selectedPostCategories = array_values($options['posts-all-categories']);
				$target['sgpb-target'][0][] = array('param' => 'post_category', 'operator' => '==', 'value' => $selectedPostCategories);
			}
		}

		if ($isSavedToHome) {
			$target['sgpb-target'][0][] = array('param' => 'page_type', 'operator' => '==', 'value' => array('is_home_page', 'is_home'));
		}

		if (!empty($options['allCustomPostsStatus'])) {
			$customPostTypes = $options['all-custom-posts'];
			if (!empty($customPostTypes)) {
				$selectedCustomPosts = $options['allSelectedCustomPosts'];
				if ($options['showAllCustomPosts'] == 'selected') {
					foreach ($customPostTypes as $customPostType) {
						$args = array(
							'post__in' => array_values($selectedCustomPosts),
							'posts_per_page' => 10,
							'post_type'	  => $customPostType
						);

						$searchResults = ConfigDataHelper::getPostTypeData($args);
						if (!empty($searchResults)) {
							$target['sgpb-target'][0][] = array('param' => $customPostType.'_selected', 'operator' => '==', 'value' => $searchResults);
						}
					}
				}

				else {
					$target['sgpb-target'][0][] = array('param' => 'post_type', 'operator' => '==', 'value' => array_values($customPostTypes));

				}
			}
		}

		update_post_meta($popupId, 'sg_popup_target', $target);
	}

	/**
	 * Get Addons options
	 *
	 * @param obj $popup
	 *
	 * @return bool|array
	 */
	private function getAddonsEventFromPopup($popup)
	{
		if (empty($popup)) {
			return false;
		}
		$popupId = $popup->getId();
		global $wpdb;

		$addonsOptionSqlString = 'SELECT options FROM '.$wpdb->prefix.'sg_popup_addons_connection WHERE popupId = %d and extensionType = "option"';
		$addonsSql = $wpdb->prepare($addonsOptionSqlString, $popupId);
		$results = $wpdb->get_results($addonsSql, ARRAY_A);

		if (empty($results)) {
			return false;
		}

		$options = array();

		// it's collect all events saved values ex Exit Intent and AdBlock
		foreach ($results as $result) {
			$currentOptions = json_decode($result['options'], true);

			if (empty($currentOptions)) {
				continue;
			}
			$options = array_merge($options, $currentOptions);
		}

		return $options;
	}

	/**
	 * Filter and change some related values for new version
	 *
	 * @param array $options
	 *
	 * @return array $options
	 */
	private function filterOptions($options)
	{
		if (isset($options['effect']) && $options['effect'] != 'No effect') {
			$options['sgpb-open-animation'] = 'on';
		}

		if (isset($options['isActiveStatus']) && $options['isActiveStatus'] == 'off') {
			$options['isActiveStatus'] = '';
		}

		if (empty($options['sgTheme3BorderColor'])) {
			$options['sgTheme3BorderColor'] = '#000000';
		}

		if (isset($options['popupContentBackgroundRepeat']) && ($options['popupContentBackgroundRepeat'] != 'no-repeat' && $options['popupContentBackgroundSize'] == 'auto')) {
			$options['popupContentBackgroundSize'] = 'repeat';
		}
		$themeNumber = 1;

		if (isset($options['theme'])) {
			$themeNumber = preg_replace('/[^0-9]/', '', $options['theme']);
		}

		if (isset($options['aweber-success-behavior']) && $options['aweber-success-behavior'] == 'redirectToUrl') {
			$options['aweber-success-behavior'] = 'redirectToURL';
		}

		// pro options
		if (isset($options['disablePopupOverlay'])) {
			$options['sgpb-enable-popup-overlay'] = '';
		}
		// contact form new options
		if (isset($options['contact-success-behavior']) && $options['contact-success-behavior'] == 'redirectToUrl') {
			$options['contact-success-behavior'] = 'redirectToURL';
		}
		if (isset($options['contact-text-input-bgcolor'])) {
			$options['sgpb-contact-message-bg-color'] = $options['contact-text-input-bgcolor'];
		}
		if (isset($options['contact-text-bordercolor'])) {
			$options['sgpb-contact-message-border-color'] = $options['contact-text-bordercolor'];
		}
		if (isset($options['contact-inputs-color'])) {
			$options['sgpb-contact-message-text-color'] = $options['contact-inputs-color'];
		}
		if (isset($options['contact-placeholder-color'])) {
			$options['sgpb-contact-message-placeholder-color'] = $options['contact-placeholder-color'];
		}
		if (isset($options['contact-inputs-border-width'])) {
			$options['sgpb-contact-message-border-width'] = $options['contact-inputs-border-width'];
		}

		if (isset($options['contact-success-popups-list'])) {
			$options['contact-success-popups-list'] = sgpGetCorrectPopupId($options['contact-success-popups-list']);
		}

		if (isset($options['popup-content-padding'])) {
			// add theme default padding to content padding
			switch ($themeNumber) {
				case 1:
					$options['popup-content-padding'] += 7;
					break;
				case 4:
				case 6:
					$options['popup-content-padding'] += 12;
					break;
				case 2:
				case 3:
					$options['popup-content-padding'] += 0;
					break;
				case 5:
					$options['popup-content-padding'] += 5;
					break;
			}
		}

		switch ($themeNumber) {
			case 1:
				$buttonImageWidth = 21;
				$buttonImageHeight = 21;
				break;
			case 2:
				$buttonImageWidth = 20;
				$buttonImageHeight = 20;
				break;
			case 3:
				$buttonImageWidth = 38;
				$buttonImageHeight = 19;
				break;
			case 5:
				$buttonImageWidth = 17;
				$buttonImageHeight = 17;
				break;
			case 6:
				$buttonImageWidth = 30;
				$buttonImageHeight = 30;
				break;
			default:
				$buttonImageWidth = 0;
				$buttonImageHeight = 0;
		}

		// social popup add to default value
		if (empty($options['sgMailLable'])) {
			$options['sgMailLable'] = 'E-mail';
		}
		if (empty($options['fbShareLabel'])) {
			$options['fbShareLabel'] = 'Share';
		}
		if (empty($options['lindkinLabel'])) {
			$options['lindkinLabel'] = 'Share';
		}
		if (empty($options['googLelabel'])) {
			$options['googLelabel'] = '+1';
		}
		if (empty($options['twitterLabel'])) {
			$options['twitterLabel'] = 'Tweet';
		}
		if (empty($options['pinterestLabel'])) {
			$options['pinterestLabel'] = 'Pin it';
		}

		if (isset($options['subs-success-behavior']) && $options['subs-success-behavior'] == 'redirectToUrl') {
			$options['subs-success-behavior'] = 'redirectToURL';
		}

		$options['sgpb-subs-form-bg-color'] = '#FFFFFF';
		$options['sgpb-button-image-width'] = $buttonImageWidth;
		$options['sgpb-button-image-height'] = $buttonImageHeight;

		// pro options customizations
		if (SGPB_POPUP_PKG > SGPB_POPUP_PKG_FREE) {
			if (empty($options['sgpb-restriction-yes-btn-radius-type'])) {
				$options['sgpb-restriction-yes-btn-radius-type'] = '%';
			}

			if (empty($options['sgpb-restriction-no-btn-radius-type'])) {
				$options['sgpb-restriction-no-btn-radius-type'] = '%';
			}

			if (empty($options['sgpb-restriction-yes-btn-border-color'])) {
				// border color should be like background color
				$options['sgpb-restriction-yes-btn-border-color'] = $options['yesButtonBackgroundColor'];
			}
			if (empty($options['sgpb-restriction-no-btn-border-color'])) {
				// border color should be like background color
				$options['sgpb-restriction-no-btn-border-color'] = $options['noButtonBackgroundColor'];
			}
		}

		return $options;
	}

	public function changeOldValues($optionName, $optionValue)
	{
		if ($optionName == 'theme') {
			$themeNumber = preg_replace('/[^0-9]/', '', $optionValue);
			$optionValue = 'sgpb-theme-'.$themeNumber;
		}

		return $optionValue;
	}

	private function popupObjectFromArray($arr)
	{
		global $wpdb;

		$options = json_decode($arr['options'], true);
		$type = $arr['type'];

		if (empty($type)) {
			return false;
		}

		$this->setId($arr['id']);
		$this->setType($type);
		$this->setTitle($arr['title']);
		$this->setContent('');

		switch ($type) {
			case 'image':
				$query = $wpdb->prepare('SELECT `url` FROM '.$wpdb->prefix.'sg_image_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['url'])) {
					$options['image-url'] = $result['url'];
				}
				break;
			case 'html':
				$query = $wpdb->prepare('SELECT `content` FROM '.$wpdb->prefix.'sg_html_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}
				break;
			case 'fblike':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_fblike_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}
				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($options)) {
					$this->setCustomOptions($customOptions);
				}
				break;
			case 'shortcode':
				$query = $wpdb->prepare('SELECT `url` FROM '.$wpdb->prefix.'sg_shortCode_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['url'])) {
					$this->setContent($result['url']);
				}
				break;
			case 'iframe':
				$query = $wpdb->prepare('SELECT `url` FROM '.$wpdb->prefix.'sg_iframe_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);
				if (!empty($result['url'])) {
					$options['iframe-url'] =  $result['url'];
				}
				break;
			case 'video':
				$query = $wpdb->prepare('SELECT `url`, `options` FROM '.$wpdb->prefix.'sg_video_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);
				if (!empty($result['url'])) {
					$options['video-url'] =  $result['url'];
				}

				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($customOptions)) {
					$this->setCustomOptions($customOptions);
				}
				break;
			case 'ageRestriction':
				$query = $wpdb->prepare('SELECT `content`, `yesButton` as `yesButtonLabel`, `noButton` as `noButtonLabel`, `url` as `restrictionUrl` FROM '.$wpdb->prefix.'sg_age_restriction_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);
				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}
				unset($result['content']);
				if (!empty($result)) {
					$this->setCustomOptions($result);
				}
				break;
			case 'social':
				$query = $wpdb->prepare('SELECT `socialContent`, `buttons`, `socialOptions` FROM '.$wpdb->prefix.'sg_social_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['socialContent'])) {
					$this->setContent($result['socialContent']);
				}

				$buttons = json_decode($result['buttons'], true);
				$socialOptions = json_decode($result['socialOptions'], true);

				$socialAllOptions = array_merge($buttons, $socialOptions);

				$this->setCustomOptions($socialAllOptions);
				break;
			case 'subscription':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_subscription_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}

				$subsOptions = $result['options'];
				$subsOptions = json_decode($subsOptions, true);

				if (!empty($subsOptions)) {
					$this->setCustomOptions($subsOptions);
				}
				break;
			case 'countdown':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_countdown_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}
				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($options)) {
					$this->setCustomOptions($customOptions);
				}
				break;
			case 'contactForm':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_contact_form_popup WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}
				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($options)) {
					$this->setCustomOptions($customOptions);
				}
				break;
			case 'mailchimp':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_popup_mailchimp WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}

				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($options)) {
					$this->setCustomOptions($customOptions);
				}
				break;
			case 'aweber':
				$query = $wpdb->prepare('SELECT `content`, `options` FROM '.$wpdb->prefix.'sg_popup_aweber WHERE id = %d', $arr['id']);
				$result = $wpdb->get_row($query, ARRAY_A);

				if (!empty($result['content'])) {
					$this->setContent($result['content']);
				}

				$customOptions = $result['options'];
				$customOptions = json_decode($customOptions, true);

				if (!empty($options)) {
					$this->setCustomOptions($customOptions);
				}
				break;
		}

		$this->setOptions($options);

		return $this;
	}

	public function getNamesMapping()
	{
		$names = array(
			'type' => 'sgpb-type',
			'delay' => 'sgpb-popup-delay',
			'isActiveStatus' => 'sgpb-is-active',
			'image-url' => 'sgpb-image-url',
			'theme' => 'sgpb-popup-themes',
			'effect' => 'sgpb-open-animation-effect',
			'duration' => 'sgpb-open-animation-speed',
			'popupOpenSound' => 'sgpb-open-sound',
			'popupOpenSoundFile' => 'sgpb-sound-url',
			'popup-dimension-mode' => 'sgpb-popup-dimension-mode',
			'popup-responsive-dimension-measure' => 'sgpb-responsive-dimension-measure',
			'width' => 'sgpb-width',
			'height' => 'sgpb-height',
			'maxWidth' => 'sgpb-max-width',
			'maxHeight' => 'sgpb-max-height',
			'escKey' => 'sgpb-esc-key',
			'closeButton' => 'sgpb-enable-close-button',
			'buttonDelayValue' => 'sgpb-close-button-delay',
			'scrolling' => 'sgpb-enable-content-scrolling',
			'disable-page-scrolling' => 'sgpb-disable-page-scrolling',
			'overlayClose' => 'sgpb-overlay-click',
			'contentClick' => 'sgpb-content-click',
			'content-click-behavior' => 'sgpb-content-click-behavior',
			'click-redirect-to-url' => 'sgpb-click-redirect-to-url',
			'redirect-to-new-tab' => 'sgpb-redirect-to-new-tab',
			'reopenAfterSubmission' => 'sgpb-reopen-after-form-submission',
			'repeatPopup' => 'sgpb-show-popup-same-user',
			'popup-appear-number-limit' => 'sgpb-show-popup-same-user-count',
			'onceExpiresTime' => 'sgpb-show-popup-same-user-expiry',
			'save-cookie-page-level' => 'sgpb-show-popup-same-user-page-level',
			'popupContentBgImage' => 'sgpb-show-background',
			'popupContentBackgroundSize' => 'sgpb-background-image-mode',
			'popupContentBgImageUrl' => 'sgpb-background-image',
			'sgOverlayColor' => 'sgpb-overlay-color',
			'sg-content-background-color' => 'sgpb-background-color',
			'popup-background-opacity' => 'sgpb-content-opacity',
			'opacity' => 'sgpb-overlay-opacity',
			'sgOverlayCustomClasss' => 'sgpb-overlay-custom-class',
			'sgContentCustomClasss' => 'sgpb-content-custom-class',
			'popup-z-index' => 'sgpb-popup-z-index',
			'popup-content-padding' => 'sgpb-content-padding',
			'popupFixed' => 'sgpb-popup-fixed',
			'fixedPostion' => 'sgpb-popup-fixed-position',
			'sgpb-open-animation' => 'sgpb-open-animation',
			'theme-close-text' => 'sgpb-button-text',
			'sgTheme3BorderRadius' => 'sgpb-border-radius',
			'sgTheme3BorderColor' => 'sgpb-border-color',
			'fblike-like-url' => 'sgpb-fblike-like-url',
			'fblike-layout' => 'sgpb-fblike-layout',
			'fblike-dont-show-share-button' => 'sgpb-fblike-dont-show-share-button',
			'sgpb-button-image-width' => 'sgpb-button-image-width',
			'sgpb-button-image-height' => 'sgpb-button-image-height'
		);
		// iframe pro popup type
		$names['iframe-url'] = 'sgpb-iframe-url';

		// video pro popup type
		$names['video-url'] = 'sgpb-video-url';
		$names['video-autoplay'] = 'sgpb-video-autoplay';

		// age restriction
		$names['yesButtonLabel'] = 'sgpb-restriction-yes-btn';
		$names['noButtonLabel'] = 'sgpb-restriction-no-btn';
		$names['restrictionUrl'] = 'sgpb-restriction-no-url';
		$names['yesButtonBackgroundColor'] = 'sgpb-restriction-yes-btn-bg-color';
		$names['yesButtonTextColor'] = 'sgpb-restriction-yes-btn-text-color';
		$names['yesButtonRadius'] = 'sgpb-restriction-yes-btn-radius';
		$names['sgRestrictionExpirationTime'] = 'sgpb-restriction-yes-expiration-time';
		$names['restrictionCookeSavingLevel'] = 'sgpb-restriction-cookie-level';
		$names['noButtonBackgroundColor'] = 'sgpb-restriction-no-btn-bg-color';
		$names['noButtonTextColor'] = 'sgpb-restriction-no-btn-text-color';
		$names['noButtonRadius'] = 'sgpb-restriction-no-btn-radius';

		// age restriction new options
		$names['sgpb-restriction-yes-btn-radius-type'] = 'sgpb-restriction-yes-btn-radius-type';
		$names['sgpb-restriction-no-btn-radius-type'] = 'sgpb-restriction-no-btn-radius-type';
		$names['sgpb-restriction-yes-btn-border-color'] = 'sgpb-restriction-yes-btn-border-color';
		$names['sgpb-restriction-no-btn-border-color'] = 'sgpb-restriction-no-btn-border-color';

		$proNames = array(
			'autoClosePopup' => 'sgpb-auto-close',
			'popupClosingTimer' => 'sgpb-auto-close-time',
			'disablePopupOverlay' => 'sgpb-enable-popup-overlay',
			'disablePopup' => 'sgpb-disable-popup-closing',
			'popup-schedule-status' => 'sgpb-schedule-status',
			'schedule-start-weeks' => 'sgpb-schedule-weeks',
			'schedule-start-time' => 'sgpb-schedule-start-time',
			'schedule-end-time' => 'sgpb-schedule-end-time',
			'popup-timer-status' => 'sgpb-popup-timer-status',
			'popup-start-timer' => 'sgpb-popup-start-timer',
			'popup-finish-timer' => 'sgpb-popup-end-timer',
			'inActivityStatus' => 'sgpb-inactivity-status',
			'inactivity-timout' => 'sgpb-inactivity-timer',
			'onScrolling' => 'sgpb-onscroll-status',
			'beforeScrolingPrsent' => 'sgpb-onscroll-percentage',
			'sg-user-status' => 'sgpb-by-user-status',
			'loggedin-user' => 'sgpb-for-logged-in-user',
			'forMobile' => 'sgpb-hide-on-mobile',
			'openMobile' => 'sgpb-only-on-mobile',
			'countryStatus' => 'sgpb-by-country',
			'allowCountries' => 'sgpb-allow-countries',
			'countryIso' => 'sgpb-countries-iso',
			'randomPopup' => 'sgpb-random-popup'
		);
		$names = array_merge($names, $proNames);

		// pro options
		$names['allPagesStatus'] = 'allPagesStatus';
		$names['showAllPages'] = 'allPages';
		$names['allSelectedPages'] = 'allSelectedPages';
		$names['allPostsStatus'] = 'allPostsStatus';
		$names['showAllPosts'] = 'allPosts';
		$names['allSelectedPosts'] = 'allSelectedPosts';
		$names['posts-all-categories'] = 'posts-all-categories';
		$names['allCustomPostsStatus'] = 'allCustomPostsStatus';
		$names['all-custom-posts'] = 'all-custom-posts';
		$names['showAllCustomPosts'] = 'showAllCustomPosts';
		$names['allSelectedCustomPosts'] = 'allSelectedCustomPosts';
		// countdown pro popup type
		$names['countdownNumbersBgColor'] = 'sgpb-counter-background-color';
		$names['countdownNumbersTextColor'] = 'sgpb-counter-text-color';
		$names['sg-due-date'] = 'sgpb-countdown-due-date';
		$names['sg-countdown-type'] = 'sgpb-countdown-type';
		$names['sg-time-zone'] = 'sgpb-countdown-timezone';
		$names['counts-language'] = 'sgpb-countdown-language';
		$names['pushToBottom'] = 'sgpb-countdown-show-on-top';
		$names['countdown-autoclose'] = 'sgpb-countdown-close-timeout';
		// contact form pro popup type
		$names['show-form-to-top'] = 'sgpb-contact-show-form-to-top';
		$names['contact-name-status'] = 'sgpb-contact-field-name';
		$names['contact-name'] = 'sgpb-contact-name-placeholder';
		$names['contact-name-required'] = 'sgpb-contact-name-required';
		$names['contact-subject-status'] = 'sgpb-contact-field-subject';
		$names['contact-subject'] = 'sgpb-contact-subject-placeholder';
		$names['contact-subject-required'] = 'sgpb-contact-subject-required';
		$names['contact-email'] = 'sgpb-contact-email-placeholder';
		$names['contact-message'] = 'sgpb-contact-message-placeholder';
		$names['contact-fail-message'] = 'sgpb-contact-error-message';
		$names['contact-receive-email'] = 'sgpb-contact-to-email';
		$names['contact-validation-message'] = 'sgpb-contact-required-message';
		$names['contact-validate-email'] = 'sgpb-contact-invalid-email-message';
		$names['contact-inputs-width'] = 'sgpb-contact-inputs-width';
		$names['contact-inputs-height'] = 'sgpb-contact-inputs-height';
		$names['contact-inputs-border-width'] = 'sgpb-contact-inputs-border-width';
		$names['contact-text-input-bgcolor'] = 'sgpb-contact-inputs-bg-color';
		$names['contact-text-bordercolor'] = 'sgpb-contact-inputs-border-color';
		$names['contact-inputs-color'] = 'sgpb-contact-inputs-text-color';
		$names['contact-placeholder-color'] = 'sgpb-contact-inputs-placeholder-color';
		$names['contact-area-width'] = 'sgpb-contact-message-width';
		$names['contact-area-height'] = 'sgpb-contact-message-height';
		$names['sg-contact-resize'] = 'sgpb-contact-message-resize';
		$names['contact-btn-width'] = 'sgpb-contact-submit-width';
		$names['contact-btn-height'] = 'sgpb-contact-submit-height';
		$names['contact-btn-title'] = 'sgpb-contact-submit-title';
		$names['contact-btn-progress-title'] = 'sgpb-contact-submit-title-progress';
		$names['contact-button-bgcolor'] = 'sgpb-contact-submit-bg-color';
		$names['contact-button-color'] = 'sgpb-contact-submit-text-color';
		$names['dont-show-content-to-contacted-user'] = 'sgpb-contact-hide-for-contacted-users';
		$names['contact-success-behavior'] = 'sgpb-contact-success-behavior';
		$names['contact-success-message'] = 'sgpb-contact-success-message';
		$names['contact-success-redirect-url'] = 'sgpb-contact-success-redirect-URL';
		$names['contact-success-redirect-new-tab'] = 'sgpb-contact-success-redirect-new-tab';
		$names['contact-success-popups-list'] = 'sgpb-contact-success-popup';
		$names['contact-gdpr'] = 'sgpb-contact-gdpr-status';
		$names['contact-gdpr-label'] = 'sgpb-contact-gdpr-label';
		$names['contact-gdpr-text'] = 'sgpb-contact-gdpr-text';
		$names['sgpb-contact-message-bg-color'] = 'sgpb-contact-message-bg-color';
		$names['sgpb-contact-message-border-color'] = 'sgpb-contact-message-border-color';
		$names['sgpb-contact-message-text-color'] = 'sgpb-contact-message-text-color';
		$names['sgpb-contact-message-placeholder-color'] = 'sgpb-contact-message-placeholder-color';
		$names['sgpb-contact-message-border-width'] = 'sgpb-contact-message-border-width';

		// Social
		$names['shareUrlType'] = 'sgpb-social-share-url-type';
		$names['sgShareUrl'] = 'sgpb-social-share-url';
		$names['sgSocialTheme'] = 'sgpb-social-share-theme';
		$names['sgSocialButtonsSize'] = 'sgpb-social-theme-size';
		$names['sgSocialLabel'] = 'sgpb-social-show-labels';
		$names['sgSocialShareCount'] = 'sgpb-social-share-count';
		$names['sgRoundButton'] = 'sgpb-social-round-buttons';
		$names['sgEmailStatus'] = 'sgpb-social-status-email';
		$names['sgMailLable'] = 'sgpb-social-label-email';
		$names['sgFbStatus'] = 'sgpb-social-status-facebook';
		$names['fbShareLabel'] = 'sgpb-social-label-facebook';
		$names['sgLinkedinStatus'] = 'sgpb-social-status-linkedin';
		$names['lindkinLabel'] = 'sgpb-social-label-linkedin';
		$names['sgGoogleStatus'] = 'sgpb-social-status-googleplus';
		$names['googLelabel'] = 'sgpb-social-label-googleplus';
		$names['sgTwitterStatus'] = 'sgpb-social-status-twitter';
		$names['twitterLabel'] = 'sgpb-social-label-twitter';
		$names['sgPinterestStatus'] = 'sgpb-social-status-pinterest';
		$names['pinterestLabel'] = 'sgpb-social-label-pinterest';

		// Subscription
		$names['subscription-email'] = 'sgpb-subs-email-placeholder';
		$names['subs-gdpr'] = 'sgpb-subs-gdpr-status';
		$names['subs-gdpr-label'] = 'sgpb-subs-gdpr-label';
		$names['subs-gdpr-text'] = 'sgpb-subs-gdpr-text';
		$names['subs-first-name-status'] = 'sgpb-subs-first-name-status';
		$names['subs-first-name'] = 'sgpb-subs-first-placeholder';
		$names['subs-first-name-required'] = 'sgpb-subs-first-name-required';
		$names['subs-last-name-status'] = 'sgpb-subs-last-name-status';
		$names['subs-last-name'] = 'sgpb-subs-last-placeholder';
		$names['subs-last-name-required'] = 'sgpb-subs-last-name-required';
		$names['subs-validation-message'] = 'sgpb-subs-validation-message';
		$names['subs-text-width'] = 'sgpb-subs-text-width';
		$names['subs-text-height'] = 'sgpb-subs-text-height';
		$names['subs-text-border-width'] = 'sgpb-subs-text-border-width';
		$names['subs-text-input-bgColor'] = 'sgpb-subs-text-bg-color';
		$names['subs-text-borderColor'] = 'sgpb-subs-text-border-color';
		$names['subs-inputs-color'] = 'sgpb-subs-text-color';
		$names['subs-placeholder-color'] = 'sgpb-subs-text-placeholder-color';
		$names['subs-btn-width'] = 'sgpb-subs-btn-width';
		$names['subs-btn-height'] = 'sgpb-subs-btn-height';
		$names['subs-btn-title'] = 'sgpb-subs-btn-title';
		$names['subs-btn-progress-title'] = 'sgpb-subs-btn-progress-title';
		$names['subs-button-bgColor'] = 'sgpb-subs-btn-bg-color';
		$names['subs-button-color'] = 'sgpb-subs-btn-text-color';
		$names['subs-success-behavior'] = 'sgpb-subs-success-behavior';
		$names['subs-success-message'] = 'sgpb-subs-success-message';
		$names['subs-success-redirect-url'] = 'sgpb-subs-success-redirect-URL';
		$names['subs-success-redirect-new-tab'] = 'sgpb-subs-success-redirect-new-tab';
		$names['subs-success-popups-list'] = 'sgpb-subs-success-popup';
		// Subscription new option
		$names['sgpb-subs-form-bg-color'] = 'sgpb-subs-form-bg-color';

		// Exit Intent extension names
		$names['option-exit-intent-enable'] = 'sgpb-option-exit-intent-enable';
		$names['option-exit-intent-type'] = 'sgpb-option-exit-intent-type';
		$names['option-exit-intent-expire-time'] = 'sgpb-exit-intent-expire-time';
		$names['option-exit-intent-cookie-level'] = 'sgpb-exit-intent-cookie-level';
		$names['option-exit-intent-soft-from-top'] = 'sgpb-exit-intent-soft-from-top';

		// Adblock extension names
		$names['option-enable-ad-block'] = 'sgpb-option-enable-ad-block';
		// MailChimp extension names
		$names['mailchimp-list-id'] = 'sgpb-mailchimp-lists';
		$names['mailchimp-double-optin'] = 'sgpb-enable-double-optin';
		$names['mailchimp-only-required'] = 'sgpb-show-required-fields';
		$names['mailchimp-form-aligment'] = 'sgpb-mailchimp-form-align';
		$names['mailchimp-label-aligment'] = 'sgpb-mailchimp-label-alignment';
		$names['mailchimp-indicates-required-fields'] = 'sgpb-enable-asterisk-label';
		$names['mailchimp-asterisk-label'] = 'sgpb-mailchimp-asterisk-label';
		$names['mailchimp-required-error-message'] = 'sgpb-mailchimp-required-message';
		$names['mailchimp-email-validate-message'] = 'sgpb-mailchimp-email-message';
		$names['mailchimp-email-label'] = 'sgpb-mailchimp-email-label';
		$names['mailchimp-error-message'] = 'sgpb-mailchimp-error-message';
		$names['mailchimp-show-form-to-top'] = 'sgpb-mailchimp-show-form-to-top';
		$names['mailchimp-label-color'] = 'sgpb-mailchimp-label-color';
		$names['mailchimp-input-width'] = 'sgpb-mailchimp-input-width';
		$names['mailchimp-input-height'] = 'sgpb-mailchimp-input-height';
		$names['mailchimp-input-border-radius'] = 'sgpb-mailchimp-border-radius';
		$names['mailchimp-input-border-width'] = 'sgpb-mailchimp-border-width';
		$names['mailchimp-input-border-color'] = 'sgpb-mailchimp-border-color';
		$names['mailchimp-input-bg-color'] = 'sgpb-mailchimp-background-color';
		$names['sgpb-mailchimp-input-color'] = 'sgpb-mailchimp-input-color';
		$names['mailchimp-submit-title'] = 'sgpb-mailchimp-submit-title';
		$names['mailchimp-submit-width'] = 'sgpb-mailchimp-submit-width';
		$names['mailchimp-submit-height'] = 'sgpb-mailchimp-submit-height';
		$names['mailchimp-submit-border-width'] = 'sgpb-mailchimp-submit-border-width';
		$names['mailchimp-submit-border-radius'] = 'sgpb-mailchimp-submit-border-radius';
		$names['mailchimp-submit-border-color'] = 'sgpb-mailchimp-submit-border-color';
		$names['mailchimp-submit-button-bgcolor'] = 'sgpb-mailchimp-submit-background-color';
		$names['mailchimp-submit-color'] = 'sgpb-mailchimp-submit-color';
		$names['mailchimp-success-behavior'] = 'sgpb-mailchimp-success-behavior';
		$names['mailchimp-success-message'] = 'sgpb-mailchimp-success-message';
		$names['mailchimp-success-redirect-url'] = 'sgpb-mailchimp-success-redirect-URL';
		$names['mailchimp-success-redirect-new-tab'] = 'sgpb-mailchimp-success-redirect-new-tab';
		$names['mailchimp-success-popups-list'] = 'sgpb-mailchimp-success-popup';
		$names['mailchimp-close-popup-already-subscribed'] = 'sgpb-mailchimp-close-popup-already-subscribed';
		// AWeber extension
		$names['sg-aweber-list'] = 'sgpb-aweber-list';
		$names['sg-aweber-webform'] = 'sgpb-aweber-signup-form';
		$names['aweber-custom-invalid-email-message'] = 'sgpb-aweber-invalid-email';
		$names['aweber-invalid-email'] = 'sgpb-aweber-invalid-email-message';
		$names['aweber-already-subscribed-message'] = 'sgpb-aweber-custom-subscribed-message';
		$names['aweber-required-message'] = 'sgpb-aweber-required-message';
		$names['aweber-validate-email-message'] = 'sgpb-aweber-validate-email-message';
		$names['aweber-success-behavior'] = 'sgpb-aweber-success-behavior';
		$names['aweber-success-message'] = 'sgpb-aweber-success-message';
		$names['aweber-success-redirect-url'] = 'sgpb-aweber-success-redirect-URL';
		$names['aweber-success-redirect-new-tab'] = 'sgpb-aweber-success-redirect-new-tab';
		$names['aweber-success-popups-list'] = 'sgpb-aweber-success-popup';

		return $names;
	}

	public static function saveCustomInserted()
	{
		global $post;
		if (empty($post)) {
			return false;
		}

		$postId = $post->ID;
		if (get_option('sgpbSaveOldData'.$postId)) {
			return false;
		}

		update_option('sgpbSaveOldData'.$postId, 1);

		add_filter('sgpbConvertedPopupId', 'sgpb\sgpGetCorrectPopupId', 10, 1);
		self::saveMetaboxPopup($postId);
		$content = get_post_field('post_content', $postId);
		SGPopup::deletePostCustomInsertedData($postId);
		SGPopup::deletePostCustomInsertedEvents($postId);
		// We detect all the popups that were inserted as a custom ones, in the content.
		SGPopup::savePopupsFromContentClasses($content, $post);
	}

	public static function saveMetaboxPopup($postId)
	{
		$selectedPost = get_post_meta($postId, 'sg_promotional_popup', true);

		if (empty($selectedPost)) {
			return false;
		}
		global $SGPB_DATA_CONFIG_ARRAY;

		$postType = get_post_type($postId);
		$postTitle = get_the_title($postId);
		$popupId = sgpGetCorrectPopupId($selectedPost);
		$popupTargetParam = $postType.'_selected';

		if (!get_post_meta($popupId, 'sg_popup_events')) {
			update_post_meta($popupId, 'sg_popup_events', array($SGPB_DATA_CONFIG_ARRAY['events']['initialData']));
		}

		$savedTarget = get_post_meta($popupId, 'sg_popup_target');
		if (empty($savedTarget[0]['sgpb-target'][0])) {
			$savedTarget['sgpb-target'][] =  array(array('param' => $popupTargetParam, 'operator' => '==', 'value' => array($postId => $postTitle)));
			$savedTarget['sgpb-conditions'][] = $SGPB_DATA_CONFIG_ARRAY['conditions']['initialData'];

			update_post_meta($popupId, 'sg_popup_target', $savedTarget, true);
			return true;
		}
		$targets = $savedTarget[0]['sgpb-target'][0];
		$targetExists = false;

		foreach ($targets as $key => $target) {
			if ($key == 0 && $target['param'] == 'not_rule') {
				$target['param'] = $popupTargetParam;
				$savedTarget[0]['sgpb-target'][0][$key]['param'] = $popupTargetParam;
			}
			if ($target['param'] == $popupTargetParam) {
				$targetExists = true;
				$targetValue = array();
				if (!empty($target['value'])) {
					$targetValue = $target['value'];
				}

				$targetValue[$postId] = $postTitle;
				$savedTarget[0]['sgpb-target'][0][$key]['value'] = $targetValue;
				break;
			}
		}

		if (!$targetExists) {
			$savedTargetsLength = count($savedTarget[0]['sgpb-target'][0]);
			$savedTarget[0]['sgpb-target'][0][$savedTargetsLength] = array('param' => $popupTargetParam, 'operator' => '==', 'value' => array($postId => $postTitle));
		}

		delete_post_meta($postId, 'sg_promotional_popup');
		delete_post_meta($popupId, 'sg_popup_target');
		update_post_meta($popupId, 'sg_popup_target', $savedTarget[0], true);

		return true;
	}
}

function sgpGetCorrectPopupId($popupId)
{
	$convertedIds = get_option('sgpbConvertedIds');

	if (empty($convertedIds) || empty($convertedIds[$popupId])) {
		return $popupId;
	}

	return $convertedIds[$popupId];
}
