<?php
namespace sgpb;
use \ConfigDataHelper;
use \WP_Post;

if (class_exists("sgpb\SGPopup")) {
	return;
}

abstract class SGPopup
{
	protected $type;

	private $sanitizedData;
	private $postData = array();
	private $id;
	private $ajax_page_id;
	private $title;
	private $content;
	private $target;
	private $conditions;
	private $events = array();
	private $options;
	private $loadableModes;
	private $saveMode = '';
	private $savedPopup = false;


	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return (int)$this->id;
	}
	public function setCurrentPageIdForAjax($id)
	{
		$this->ajax_page_id = $id;
	}

	public function getCurrentPageIdForAjax()
	{
		return (int)$this->ajax_page_id;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setTarget($target)
	{
		$this->target = $target;
	}

	public function getTarget()
	{
		return $this->target;
	}

	public function setEvents($events)
	{
		$this->events = $events;
	}

	public function getEvents()
	{
		return $this->events;
	}

	public function setConditions($conditions)
	{
		$this->conditions = $conditions;
	}

	public function getConditions()
	{
		return $this->conditions;
	}

	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function setLoadableModes($loadableModes)
	{
		$this->loadableModes = $loadableModes;
	}

	public function getLoadableModes()
	{
		return $this->loadableModes;
	}

	public function setSaveMode($saveMode)
	{
		$this->saveMode = $saveMode;
	}

	public function getSaveMode()
	{
		return $this->saveMode;
	}

	public function setSavedPopup($savedPopup)
	{
		$this->savedPopup = $savedPopup;
	}

	public function getSavedPopup()
	{
		return $this->savedPopup;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setSavedPopupById($popupId)
	{
		$popup = SGPopup::find($popupId);
		if (is_object($popup)) {
			$this->setSavedPopup($popup);
		}
	}

	public function setReportData($popupId)
	{
		$events = $this->getEvents();
		$options = $this->getOptions();
		$targets = $this->getTarget();
		$conditions = $this->getConditions();
		do_action('sgpbDebugReportUpdate', 'options', $options, $popupId);
		do_action('sgpbDebugReportUpdate', 'events', $events, $popupId);
		do_action('sgpbDebugReportUpdate', 'targets', $targets, $popupId);
		do_action('sgpbDebugReportUpdate', 'conditions', $conditions, $popupId);
	}

	public function getPopupAllEvents($postId, $popupId, $popupObj = false)
	{
		$events = array();

		$loadableModes = $this->getLoadableModes();

		if (isset($loadableModes['attr_event'])) {
			$customEvents = SGPopup::getPostPopupCustomEvent($postId, $popupId);
			$events = array_merge($events, $customEvents);
		}

		if (isset($loadableModes['option_event']) || is_null($loadableModes)) {
			$optionEvents = $this->getEvents();
			if (!is_array($optionEvents)) {
				$optionEvents = array();
			}
			$events = array_merge($events, $optionEvents);
		}

		if (!empty($popupObj->getOptionValue('sgpb-enable-floating-button'))) {
			$events[] = array('param' => 'setByCssClass', 'hiddenOption' => array());
		}

		return apply_filters('sgpbPopupEvents', $events, $popupObj);
	}

	public function getContent()
	{
		$postId = $this->getId();

		if (!AdminHelper::hasBlocks($this->content)) {
			$popupContent = wpautop($this->content);
		} else {
			$popupContent = $this->content;
		}

		$editorContent = AdminHelper::checkEditorByPopupId($postId);
		if (!empty($editorContent)) {
			if (class_exists('Vc_Manager')) {
				$popupContent .= $editorContent;
			}
			else {
				$popupContent = $editorContent;
			}
		}

		return $popupContent;
	}

	public function setPostData($postData)
	{
		$this->postData = apply_filters('sgpbSavedPostData', $postData);
	}

	public function getPostData()
	{
		return $this->postData;
	}

	public function getPopupTypeContent()
	{
		return 	$this->getContent();
	}

	public function insertIntoSanitizedData($sanitizedData)
	{
		if (!empty($sanitizedData)) {
			$this->sanitizedData[$sanitizedData['name']] = $sanitizedData['value'];
		}
	}

	abstract public function getExtraRenderOptions();

	public function setSanitizedData($sanitizedData)
	{
		$this->sanitizedData = $sanitizedData;
	}

	public function getSanitizedData()
	{
		return $this->sanitizedData;
	}

	/**
	 * Find popup and create this object
	 *
	 * @since 1.0.0
	 *
	 * @param object|int $popup
	 *
	 * @return object|false $obj
	 */
	public static function find($popup, $args = array())
	{
		if (isset($_GET['sg_popup_preview_id'])) {
			$args['is-preview'] = true;
		}
		// If the popup is object get data from object otherwise we find needed data from WordPress functions
		if ($popup instanceof WP_Post) {
			$status = $popup->post_status;
			$title = $popup->post_title;
			$popupContent = $popup->post_content;
			$popupId = $popup->ID;
		}
		else {
			$popupId = $popup;
			$popupPost = get_post($popupId);
			if (empty($popupPost)) {
				return false;
			}
			$title = get_the_title($popupId);
			$status = get_post_status($popupId);
			$popupContent = $popupPost->post_content;
		}
		$allowedStatus = array('publish', 'draft', 'private');

		if (!empty($args['status'])) {
			$allowedStatus = $args['status'];
		}

		if (!isset($args['checkActivePopupType']) && !in_array($status, $allowedStatus)) {
			return $status;
		}
		$saveMode = '';
		global $post;
		if ((@is_preview() && $post->ID == $popupId) || isset($args['preview'])) {
			$saveMode = '_preview';
		}
		if (isset($args['is-preview'])) {
			$saveMode = '_preview';
		}
		if (isset($args['insidePopup']) && $args['insidePopup'] == 'on') {
			$saveMode = '';
		}
		$currentPost = get_post($popupId);
		$currentPostStatus = $currentPost->post_status;
		if ($currentPostStatus == 'draft') {
			$saveMode = '_preview';
		}

		$savedData = array();
		if (file_exists(dirname(__FILE__).'/PopupData.php')) {
			require_once(dirname(__FILE__).'/PopupData.php');
			$savedData = PopupData::getPopupDataById($popupId, $saveMode);
		}
		$savedData = apply_filters('sgpbPopupSavedData', $savedData);

		if (empty($savedData) && $currentPostStatus !== 'trash') {
			return false;
		}

		$type = 'html';
		if (isset($savedData['sgpb-type'])) {
			$type = $savedData['sgpb-type'];
		}

		$popupClassName = self::getPopupClassNameFormType($type);
		$typePath = self::getPopupTypeClassPath($type);
		if (!file_exists($typePath.$popupClassName.'.php')) {
			return false;
		}
		require_once($typePath.$popupClassName.'.php');
		$popupClassName = __NAMESPACE__.'\\'.$popupClassName;

		$obj = new $popupClassName();
		$obj->setId($popupId);
		$obj->setType($type);
		$obj->setTitle($title);
		$obj->setContent($popupContent);

		if (!empty($savedData['sgpb-target'][0])) {
			$obj->setTarget($savedData['sgpb-target'][0]);
		}
		unset($savedData['sgpb-target']);
		if (!empty($savedData['sgpb-events'][0])) {
			$events = self::shapeEventsToOneArray($savedData['sgpb-events'][0]);
			$obj->setEvents($events);
		}
		unset($savedData['sgpb-events']);
		if (!empty($savedData['sgpb-conditions'][0])) {
			$obj->setConditions($savedData['sgpb-conditions'][0]);
		}
		unset($savedData['sgpb-conditions']);

		$obj->setOptions($savedData);

		return $obj;
	}

	private static function shapeEventsToOneArray($events)
	{
		$eventsData = array();
		if (!empty($events)) {
			foreach ($events as $event) {
				if (empty($event['hiddenOption'])) {
					$eventsData[] = $event;
					continue;
				}
				$hiddenOptions = $event['hiddenOption'];
				unset($event['hiddenOption']);
				$eventsData[] = $event + $hiddenOptions;
			}
		}

		return apply_filters('sgpbEventsToOneArray', $eventsData);
	}

	public static function getPopupClassNameFormType($type)
	{
		$popupName = ucfirst(strtolower($type));
		$popupClassName = $popupName.'Popup';

		return apply_filters('sgpbPopupClassNameFromType', $popupClassName);
	}

	public static function getPopupTypeClassPath($type)
	{
		global $SGPB_POPUP_TYPES;
		$typePaths = $SGPB_POPUP_TYPES['typePath'];

		if (empty($typePaths[$type])) {
			return SG_POPUP_CLASSES_POPUPS_PATH;
		}

		return $typePaths[$type];
	}

	public function sanitizeValueByType($value, $type)
	{
		switch ($type) {
			case 'string':
				if (is_array($value)) {
					$sanitizedValue = $this->recursiveSanitizeTextField($value);
				}
				else {
					$sanitizedValue = htmlspecialchars($value);
				}
				break;
			case 'text':
				$sanitizedValue = htmlspecialchars($value);
				break;
			case 'array':
				$sanitizedValue = $this->recursiveSanitizeTextField($value);
				break;
			case 'email':
				$sanitizedValue = sanitize_email($value);
				break;
			case 'sgpb':
				$sanitizedValue = $this->recursiveHtmlSpecialchars($value);
				break;
			default:
				$sanitizedValue = sanitize_text_field($value);
				break;
		}

		return $sanitizedValue;
	}

	public function recursiveSanitizeTextField($array)
	{
		if (!is_array($array)) {
			return $array;
		}

		foreach ($array as $key => &$value) {
			if (is_array($value)) {
				$value = $this->recursiveSanitizeTextField($value);
			}
			else {
				/*get simple field type and do sanitization*/
				$defaultData = $this->getDefaultDataByName($key);
				if (empty($defaultData['type'])) {
					$defaultData['type'] = 'string';
				}
				$value = $this->sanitizeValueByType($value, $defaultData['type']);
			}
		}

		return $array;
	}

	public function recursiveHtmlSpecialchars($array)
	{
		if (!is_array($array)) {
			return $array;
		}

		foreach ($array as $key => &$value) {
			if (is_array($value)) {
				$value = $this->recursiveHtmlSpecialchars($value);
			}
			else {
				$value = htmlspecialchars($value);
			}
		}

		return $array;
	}

	public static function parsePopupDataFromData($data)
	{
		$popupData = array();
		$data = apply_filters('sgpbFilterOptionsBeforeSaving', $data);

		foreach ($data as $key => $value) {
			if (strpos($key, 'sgpb') === 0) {
				$popupData[$key] = $value;
			}
			if (is_array($value) && isset($value['name']) && strpos($value['name'], 'sgpb') === 0) {
				$popupData[$value['name']] = $value['value'];
			}
			else if (is_array($value) && isset($value['name']) && strpos($value['name'], 'post_ID') === 0) {
				$popupData['sgpb-post-id'] = $value['value'];
			}
		}

		return $popupData;
	}

	public static function create($data = array(), $saveMode = '', $firstTime = 0)
	{
		$obj = new static();
		$obj->setSaveMode($saveMode);
		$additionalData = $obj->addAdditionalSettings($data, $obj);
		$data = array_merge($data, $additionalData);
		$data = apply_filters('sgpbAdvancedOptionsDefaultValues', $data);
		foreach ($data as $name => $value) {
			if (strpos($name, 'sgpb') === 0) {
				$defaultData = $obj->getDefaultDataByName($name);
				if (empty($defaultData['type'])) {
					$defaultData['type'] = 'string';
				}
				$sanitizedValue = $obj->sanitizeValueByType($value, $defaultData['type']);
				$obj->insertIntoSanitizedData(array('name' => $name,'value' => $sanitizedValue));
			}
		}

		$obj->setSavedPopupById($data['sgpb-post-id']);
		$result = $obj->save();

		$result = apply_filters('sgpbPopupCreateResult', $result);

		if ($result) {
			return $obj;
		}

		return $result;
	}

	public function save()
	{
		$this->convertImagesToData();
		$data = $this->getSanitizedData();
		$popupId = $data['sgpb-post-id'];

		$this->setId($popupId);

		if (!empty($data['sgpb-target'])) {
			$this->setTarget($data['sgpb-target']);
			/*remove from popup options because it's useless to save it twice*/
			unset($data['sgpb-target']);
		}
		if (!empty($data['sgpb-conditions'])) {
			$this->setConditions($data['sgpb-conditions']);
			unset($data['sgpb-conditions']);
		}
		if (!empty($data['sgpb-events'])) {
			$this->setEvents($data['sgpb-events']);
			unset($data['sgpb-events']);
		}
		$data = $this->customScriptsSave($data);
		$this->setOptions($data);

		$targets = $this->targetSave();
		$events = $this->eventsSave();
		$options = $this->popupOptionsSave();

		return ($targets && $events && $options);
	}

	public function convertImagesToData()
	{
		$buttonImageData = '';
		$savedImageUrl = '';
		$savedContentBackgroundImageUrl = '';
		$contentBackgroundImageData = '';

		$data = $this->getSanitizedData();
		$buttonImageUrl = isset($data['sgpb-button-image']) ? $data['sgpb-button-image'] : '';
		$contentBackgroundImageUrl = isset($data['sgpb-background-image']) ? $data['sgpb-background-image'] : '';

		$savedPopup = $this->getSavedPopup();

		if (is_object($savedPopup)) {
			$buttonImageData = $savedPopup->getOptionvalue('sgpb-button-image-data');
			$savedImageUrl = $savedPopup->getOptionValue('sgpb-button-image');
			$contentBackgroundImageData = $savedPopup->getOptionValue('sgpb-background-image-data');
			$savedContentBackgroundImageUrl = $savedPopup->getOptionValue('sgpb-background-image');
		}

		if ($buttonImageUrl != $savedImageUrl) {
			$buttonImageData = AdminHelper::getImageDataFromUrl($buttonImageUrl, true);
		}
		if ($contentBackgroundImageUrl != $savedContentBackgroundImageUrl) {
			$contentBackgroundImageData = AdminHelper::getImageDataFromUrl($contentBackgroundImageUrl);
		}

		$data['sgpb-button-image-data'] = $buttonImageData;
		$data['sgpb-background-image-data'] = $contentBackgroundImageData;

		$data = apply_filters('sgpbConvertImagesToData', $data);
		$this->setSanitizedData($data);
	}

	private function customScriptsSave($data)
	{
		$popupId = $this->getId();
		$popupContent = $this->getContent();

		$defaultData = ConfigDataHelper::defaultData();
		$defaultDataJs = $defaultData['customEditorContent']['js']['helperText'];
		$defaultDataCss = $defaultData['customEditorContent']['css']['oldDefaultValue'];

		$finalData = array('js' => array(), 'css' => array());
		$alreadySavedData = get_post_meta($popupId, 'sg_popup_scripts', true);

		// get styles
		$finalData['css'] = $data['sgpb-css-editor'];
		$defaultDataCss = $defaultDataCss[0];

		$defaultDataCss = preg_replace('/\s/', '', $defaultDataCss);
		$temp = preg_replace('/\s/', '', $finalData['css']);

		unset($data['sgpb-css-editor']);

		if ($temp == $defaultDataCss) {
			unset($finalData['css']);
		}

		// get scripts
		foreach ($defaultDataJs as $key => $value) {
			if ($data['sgpb-'.$key] == '') {
				unset($data['sgpb-'.$key]);
				continue;
			}
			if ($key == 'ShouldOpen' || $key == 'ShouldClose') {
				$finalData['js']['sgpb-'.$key] = $data['sgpb-'.$key];
				continue;
			}
			$finalData['js']['sgpb-'.$key] = $data['sgpb-'.$key];
			unset($data['sgpb-'.$key]);
		}

		if ($alreadySavedData == $finalData) {
			return $data;
		}

		array_walk_recursive($finalData, function(&$element){
			$element = str_replace(array("\r\n"), "\n", $element);
		});

		update_post_meta($popupId, 'sg_popup_scripts', $finalData);

		return $data;
	}

	private function targetSave()
	{
		global $SGPB_DATA_CONFIG_ARRAY;
		$saveMode = $this->getSaveMode();
		$popupId = $this->getId();
		$targetData = $this->getTarget();
		$conditionsData = $this->getConditions();

		$targetConfig = $SGPB_DATA_CONFIG_ARRAY['target'];
		$paramsData = $targetConfig['paramsData'];
		$attrs = $targetConfig['attrs'];
		$popupTarget = array();
		if (empty($targetData)) {
			return array();
		}

		foreach ($targetData as $groupId => $groupData) {
			foreach ($groupData as $ruleId => $ruleData) {

				if (empty($ruleData['value']) && !is_null($paramsData[$ruleData['param']])) {
					$targetData[$groupId][$ruleId]['value'] = '';
				}
				if (isset($ruleData['value']) && is_array($ruleData['value'])) {
					$valueAttrs = $attrs[$ruleData['param']]['htmlAttrs'];
					$postType = $valueAttrs['data-value-param'];
					$isNotPostType = '';

					if (isset($valueAttrs['isNotPostType'])) {
						$isNotPostType = $valueAttrs['isNotPostType'];
					}

					if (empty($valueAttrs['isNotPostType'])) {
						$isNotPostType = false;
					}

					if (isset($valueAttrs['isPostCategory'])){
						$targetData[$groupId][$ruleId]['value'] = ConfigDataHelper::getTermsByIds($ruleData['value']);
					} elseif(isset($valueAttrs['isPostTag'])) {
						$targetData[$groupId][$ruleId]['value'] = ConfigDataHelper::getTagsBySlug($ruleData['value']);
					}

					/*
					 * $isNotPostType => false must search inside post types post
					 * $isNotPostType => true must save array data
					 * */
					if (!$isNotPostType) {
						$args = array(
							'post__in' => array_values($ruleData['value']),
							'posts_per_page' => 100,
							'post_type'      => $postType
						);

						$searchResults = ConfigDataHelper::getPostTypeData($args);

						$targetData[$groupId][$ruleId]['value'] = $searchResults;
					}
				}
			}
		}

		$popupTarget['sgpb-target'] = $targetData;
		$popupTarget['sgpb-conditions'] = apply_filters('sgpbSaveConditions', $conditionsData);

		$alreadySavedTargets = get_post_meta($popupId, 'sg_popup_target'.$saveMode, true);
		if ($alreadySavedTargets === $popupTarget) {
			return true;
		}

		$popupTarget = apply_filters('sgpbPopupTargetMetaData', $popupTarget);

		return update_post_meta($popupId, 'sg_popup_target'.$saveMode, $popupTarget);
	}

	private function eventsSave()
	{
		global $SGPB_DATA_CONFIG_ARRAY;

		$eventsData = $this->getEvents();
		$popupId = $this->getId();
		$saveMode = $this->getSaveMode();
		$popupEvents = array();
		$eventsFromPopup = array();

		foreach ($eventsData as $groupId => $groupData) {
			$currentRuleData = array();
			foreach ($groupData as $ruleId => $ruleData) {

				$hiddenOptions = array();
				$currentData = array();
				foreach ($ruleData as $name => $value) {
					if ($name == 'param' || $name == 'value' || $name == 'operator') {
						$currentData[$name] = $value;
					}
					else {
						$hiddenOptions[$name] = $value;
					}
				}
				$currentData['hiddenOption'] = $hiddenOptions;
				$currentRuleData[$ruleId] = $currentData;
			}
			$eventsFromPopup[$groupId] = $currentRuleData;
		}

		$popupEvents['formPopup'] = $eventsFromPopup;
		$alreadySavedEvents = get_post_meta($popupId, 'sg_popup_events'.$saveMode, true);
		if ($alreadySavedEvents === $eventsFromPopup) {
			return true;
		}

		$eventsFromPopup = apply_filters('sgpbPopupEventsMetadata', $eventsFromPopup);

		return update_post_meta($popupId, 'sg_popup_events'.$saveMode, $eventsFromPopup);
	}

	private function popupOptionsSave()
	{
		$popupOptions = $this->getOptions();
		$popupOptions = apply_filters('sgpbSavePopupOptions', $popupOptions);
		//special code added for "Behavior After Special Events" section
		//todo: remove in the future if possible
		$specialBehaviors = array();
		if (!empty($popupOptions['sgpb-behavior-after-special-events'])) {
			$specialBehaviors = $popupOptions['sgpb-behavior-after-special-events'];
		}
		if (!empty($specialBehaviors) && is_array($specialBehaviors)) {
			foreach ($specialBehaviors as $groupId => $groupRow) {
				foreach ($groupRow as $ruleId => $ruleRow) {
					if (!empty($ruleRow['operator']) && $ruleRow['operator'] == 'open-popup') {
						$args = array(
							'post__in' => array($ruleRow['value']),
							'posts_per_page' => 10,
							'post_type'      => SG_POPUP_POST_TYPE
						);

						$searchResults = ConfigDataHelper::getPostTypeData($args);
						$popupOptions['sgpb-behavior-after-special-events'][$groupId][$ruleId]['value'] = $searchResults;
					}
				}
			}
		}

		$popupId = $this->getId();
		$saveMode = $this->getSaveMode();

		$alreadySavedOptions = get_post_meta($popupId, 'sg_popup_options'.$saveMode, true);
		if ($alreadySavedOptions === $popupOptions) {
			return true;
		}

		$popupOptions = apply_filters('sgpbPopupSavedOptionsMetaData', $popupOptions);

		return update_post_meta($popupId, 'sg_popup_options'.$saveMode, $popupOptions);
	}

	public function getOptionValue($optionName, $forceDefaultValue = false)
	{
		require_once(dirname(__FILE__).'/PopupData.php');
		$savedData = PopupData::getPopupDataById($this->getId());
		$this->setPostData($savedData);

		return $this->getOptionValueFromSavedData($optionName, $forceDefaultValue);
	}

	public function getOptionValueFromSavedData($optionName, $forceDefaultValue = false)
	{
		$defaultData = $this->getDefaultDataByName($optionName);
		$savedData = $this->getPostData();

		$optionValue = null;

		if (empty($defaultData['type'])) {
			$defaultData['type'] = 'string';
		}

		if (!empty($savedData)) { //edit mode
			if (isset($savedData[$optionName])) { //option exists in the database
				$optionValue = $savedData[$optionName];
			}
			/* if it's a checkbox, it may not exist in the db
			 * if we don't care about it's existance, return empty string
			 * otherwise, go for it's default value
			 */
			else if ($defaultData['type'] == 'checkbox' && !$forceDefaultValue) {
				$optionValue = '';
			}
		}

		if ($optionValue === null && !empty($defaultData['defaultValue'])) {
			$optionValue = $defaultData['defaultValue'];
		}

		if ($defaultData['type'] == 'checkbox') {
			$optionValue = $this->boolToChecked($optionValue);
		}

		if ($defaultData['type'] == 'number' && $optionValue == 0) {
			$optionValue = 0;
		}

		return $optionValue;
	}

	public static function getSavedData($popupId, $saveMode = '')
	{
		$popupSavedData = array();
		$events = self::getEventsDataById($popupId, $saveMode);
		$targetData = self::getTargetDataById($popupId, $saveMode);

		if (!empty($events)) {
			$popupSavedData['sgpb-events'] = self::getEventsDataById($popupId, $saveMode);
		}
		if (!empty($targetData)) {
			if (!empty($targetData['sgpb-target'])) {
				$popupSavedData['sgpb-target'] = $targetData['sgpb-target'];
			}
			if (!empty($targetData['sgpb-conditions'])) {
				// for the after x pages option backward compatibility
				$targetData['sgpb-conditions'] = apply_filters('sgpbAdvancedTargetingSavedData', $targetData['sgpb-conditions'], $popupId);
				$popupSavedData['sgpb-conditions'] = $targetData['sgpb-conditions'];
			}
		}

		$popupOptions = self::getPopupOptionsById($popupId, $saveMode);//
		if (is_array($popupOptions) && is_array($popupSavedData)) {
			$popupSavedData = array_merge($popupSavedData, $popupOptions);
		}

		return $popupSavedData;
	}

	public static function getEventsDataById($popupId, $saveMode = '')
	{
		$eventsData = array();
		if (get_post_meta($popupId, 'sg_popup_events'.$saveMode, true)) {
			$eventsData = get_post_meta($popupId, 'sg_popup_events'.$saveMode, true);
		}

		return $eventsData;
	}

	public static function getTargetDataById($popupId, $saveMode = '')
	{
		$targetData = array();

		if (get_post_meta($popupId, 'sg_popup_target'.$saveMode, true)) {
			$targetData = get_post_meta($popupId, 'sg_popup_target'.$saveMode, true);
		}

		return $targetData;
	}

	public static function getPopupOptionsById($popupId, $saveMode = '')
	{
		$currentPost = get_post($popupId);

		if (!empty($currentPost) && $currentPost->post_status == 'draft') {
			$saveMode = '_preview';
		}
		$optionsData = array();
		if (get_post_meta($popupId, 'sg_popup_options'.$saveMode, true)) {
			$optionsData = get_post_meta($popupId, 'sg_popup_options'.$saveMode, true);
			if (isset($optionsData['sgpb-subs-gdpr-text'])){
				$optionsData['sgpb-subs-gdpr-text'] = wp_kses($optionsData['sgpb-subs-gdpr-text'], AdminHelper::allowed_html_tags(false));
			}
		}

		return $optionsData;
	}

	public function getDefaultDataByName($optionName)
	{
		global $SGPB_OPTIONS;
		if (empty($SGPB_OPTIONS)) {
			return array();
		}

		foreach ($SGPB_OPTIONS as $option) {
			if ($option['name'] == $optionName) {
				return $option;
			}
		}

		return array();
	}

	/**
	 * Get option default option value
	 *
	 * @param string $optionName
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 *
	 */
	public function getOptionDefaultValue($optionName)
	{
		// return config data array by name
		$optionData = $this->getDefaultDataByName($optionName);

		if (empty($optionData)) {
			return '';
		}

		return $optionData['defaultValue'];
	}

	/**
	 * Changing default options form changing options by name
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaultOptions
	 * @param array $changingOptions
	 *
	 * @return array $defaultOptions
	 */
	public function changeDefaultOptionsByNames($defaultOptions, $changingOptions)
	{
		if (empty($defaultOptions) || empty($changingOptions)) {
			return $defaultOptions;
		}
		$changingOptionsNames = array_keys($changingOptions);

		foreach ($defaultOptions as $key => $defaultOption) {
			$defaultOptionName = $defaultOption['name'];
			if (in_array($defaultOptionName, $changingOptionsNames)) {
				$defaultOptions[$key] = $changingOptions[$defaultOptionName];
			}
		}

		return $defaultOptions;
	}

	/**
	 * Returns separate popup types Free or Pro
	 *
	 * @since 2.5.6
	 *
	 * @return array $popupTypesObj
	 */
	public static function getPopupTypes()
	{
		global $SGPB_POPUP_TYPES;
		$popupTypesObj = array();
		$popupTypes = $SGPB_POPUP_TYPES['typeName'];

		foreach ($popupTypes as $popupType => $level) {

			if (empty($level)) {
				$level = SGPB_POPUP_PKG_FREE;
			}

			$popupTypeObj = new PopupType();
			$popupTypeObj->setName($popupType);
			$popupTypeObj->setAccessLevel($level);

			if (SGPB_POPUP_PKG >= $level) {
				$popupTypeObj->setAvailable(true);
			}
			$popupTypesObj[] = $popupTypeObj;
		}

		return $popupTypesObj;
	}

	public static function savePopupsFromContentClasses($content, $post)
	{
		$postId = $post->ID;
		$clickClassIds = self::getStringNextNumbersByReg($content, 'sg-popup-id-');
		$targetData = array();
		$eventsData = array();

		if (!empty($clickClassIds)) {
			foreach ($clickClassIds as $id) {
				$id = apply_filters('sgpbConvertedPopupId', $id);
				if (empty($eventsData[$postId][$id])) {
					$eventsData[$postId][$id] = array('click');
				}
				else {
					$eventsData[$postId][$id][] = 'click';
				}

				if (empty($targetData[$postId])) {
					$targetData[$postId] = array($id);
				}
				else {
					$targetData[$postId][] = $id;
				}
			}
		}

		$iframeClassIs = self::getStringNextNumbersByReg($content, 'sg-iframe-popup-');

		if (!empty($iframeClassIs)) {
			foreach ($iframeClassIs as $id) {
				$id = apply_filters('sgpbConvertedPopupId', $id);
				$popupObj = self::find($id);

				if (empty($popupObj)) {
					continue;
				}

				// this event should work only for iframe popup type
				if ($popupObj->getType() != 'iframe') {
					continue;
				}

				if (empty($eventsData[$postId][$id])) {
					$eventsData[$postId][$id] = array('iframe');
				}
				else {
					$eventsData[$postId][$id][] = 'iframe';
				}

				if (empty($targetData[$postId])) {
					$targetData[$postId] = array($id);
				}
				else {
					$targetData[$postId][] = $id;
				}
			}
		}

		$confirmClassIds = self::getStringNextNumbersByReg($content, 'sg-confirm-popup-');

		if (!empty($confirmClassIds)) {
			foreach ($confirmClassIds as $id) {
				$id = apply_filters('sgpbConvertedPopupId', $id);
				if (empty($eventsData[$postId][$id])) {
					$eventsData[$postId][$id] = array('confirm');
				}
				else {
					$eventsData[$postId][$id][] = 'confirm';
				}

				if (empty($targetData[$postId])) {
					$targetData[$postId] = array($id);
				}
				else {
					$targetData[$postId][] = $id;
				}
			}
		}

		$hoverClassIds = self::getStringNextNumbersByReg($content, 'sg-popup-hover-');

		if (!empty($hoverClassIds)) {
			foreach ($hoverClassIds as $id) {
				$id = apply_filters('sgpbConvertedPopupId', $id);
				if (empty($eventsData[$postId][$id])) {
					$eventsData[$postId][$id] = array('hover');
				}
				else {
					$eventsData[$postId][$id][] = 'hover';
				}

				if (empty($targetData[$postId])) {
					$targetData[$postId] = array($id);
				}
				else {
					$targetData[$postId][] = $id;
				}
			}
		}

		$targetData = apply_filters('sgpbPopupTargetData', $targetData);
		$eventsData = apply_filters('sgpbPopupEventsData', $eventsData);

		self::saveToTargetFromPage($targetData);
		self::saveToEventsFromPage($eventsData);
	}

	public static function getStringNextNumbersByReg($content, $key)
	{
		$result = array();
		preg_match_all("/(?<=$key)(\d+)/", $content, $ids);

		if (!empty($ids[0])) {
			$result = $ids[0];
		}

		return $result;
	}

	private static function saveToTargetAndEvents($popupsShortcodsInPostPage, $postId)
	{
		if (empty($popupsShortcodsInPostPage)) {
			return false;
		}
		$customEvents = array();
		$customPopups = array();

		foreach ($popupsShortcodsInPostPage as $shortcodesData) {
			$popupId = apply_filters('sgpbConvertedPopupId', $shortcodesData['id']);

			$args = array(
				'post_type' => SG_POPUP_POST_TYPE,
				'post__in'  => array($popupId)
			);
			$postById = ConfigDataHelper::getPostTypeData($args);
			//When target data does not exist
			if (empty($postById)) {
				continue;
			}

			// collect custom inserted popups
			if (empty($customPopups[$postId])) {
				$customPopups[$postId] = array($popupId);
			}
			else {
				$customPopups[$postId][] = $popupId;
			}

			// collect custom inserted popups events
			if (empty($shortcodesData['event'])) {
				$eventName = 'onload';
			}
			else {
				$eventName = $shortcodesData['event'];
			}

			if ($eventName == 'onload') {
				$eventName = 'attr'.$eventName;
			}
			$currentEventData = array(
				'param' => $eventName
			);

			if (empty($customEvents[$postId][$popupId])) {
				$customEvents[$postId][$popupId] = array($currentEventData);
			}
			else {
				$customEvents[$postId][$popupId][] = $currentEventData;
			}
		}

		self::saveToTargetFromPage($customPopups);
		self::saveToEventsFromPage($customEvents);

		return true;
	}

	public static function getPostPopupCustomEvent($postId, $popupId)
	{
		$events = array();

		$customEventsData = self::getCustomInsertedPopupEventsByPostId($postId);

		if (!empty($customEventsData[$popupId])) {
			$events = $customEventsData[$popupId];
		}

		return $events;
	}

	/**
	 * Save popup to custom events from pages
	 *
	 * @since 1.0.0
	 *
	 * @param array $customEvents
	 *
	 * @return bool
	 *
	 */
	public static function saveToEventsFromPage($customEvents)
	{
		if (empty($customEvents)) {
			return false;
		}

		foreach ($customEvents as $postId => $popupsData) {
			$savedCustomEvents = self::getCustomInsertedPopupEventsByPostId($postId);
			$result = AdminHelper::arrayMergeSameKeys($popupsData, $savedCustomEvents);

			if (!$result) {
				return $result;
			}
			update_post_meta($postId, 'sgpb_popup_events_custom', $result);
		}

		return true;
	}

	public static function getCustomInsertedPopupEventsByPostId($postId)
	{
		$eventsData = array();
		$postMetaData = get_post_meta($postId, 'sgpb_popup_events_custom', true);

		if (!empty($postMetaData)) {
			$eventsData = $postMetaData;
		}

		return $eventsData;
	}

	/**
	 * Save popup to custom targets from pages
	 *
	 * @since 1.0.0
	 *
	 * @param array $customPopups
	 *
	 * @return void
	 *
	 */
	public static function saveToTargetFromPage($customPopups)
	{
		if (!empty($customPopups)) {
			foreach ($customPopups as $postId => $popups) {
				$alreadySavedPopups = self::getCustomInsertedDataByPostId($postId);
				$popups = array_merge($popups, $alreadySavedPopups);
				update_post_meta($postId, 'sg_popup_target_custom', $popups);
			}
		}
	}

	/**
	 * Get popup custom targes form saved data
	 *
	 * @since 1.0.0
	 *
	 * @param int $postId
	 *
	 * @return array $postData
	 */
	public static function getCustomInsertedDataByPostId($postId)
	{
		$postData = array();
		$postMetaData = get_post_meta($postId, 'sg_popup_target_custom');

		if (!empty($postMetaData[0])) {
			$postData = $postMetaData[0];
		}

		return $postData;
	}

	public static function getPopupShortcodeMatchesFromContent($content)
	{
		$result = false;
		$pattern = get_shortcode_regex();

		if (preg_match_all('/'.$pattern.'/s', $content, $matches)
			&& !empty($matches)
			&& is_array($matches)
			&& array_key_exists( 2, $matches )
			&& in_array('sg_popup', $matches[2])
		)
		{
			$result = $matches;
		}

		return $result;
	}

	public static function renderPopupContentShortcode($content, $popupId, $event, $args)
	{
		ob_start();
		$wrap = 'a';
		if (!empty($args['wrap'])) {
			if ($args['wrap'] == $wrap) {
				$args['href'] = 'javascript:void(0)';
			}
			$wrap = $args['wrap'];
		}
		unset($args['wrap']);
		unset($args['event']);
		unset($args['id']);
		$attr = AdminHelper::createAttrs($args);
		$allowed_html = AdminHelper::allowed_html_tags();

		?>
		<<?php echo esc_attr($wrap); ?>
		<?php if ($wrap == 'a') : ?>
		href="javascript:void(0)"
		<?php endif ?>
		class="sgpb-show-popup <?php echo esc_attr('sgpb-popup-id-'.$popupId); ?>"
		data-sgpbpopupid="<?php echo esc_attr($popupId); ?>"
		data-popup-event="<?php echo esc_attr($event); ?>"
		<?php echo esc_attr($attr); ?>>
		<?php echo wp_kses($content, $allowed_html); ?>
		</<?php echo esc_attr($wrap); ?>>
		<?php

		$shortcodeContent = ob_get_contents();
		ob_get_clean();

		return $shortcodeContent;
	}

	private static function collectInsidePopupShortcodes($content)
	{
		$pattern = get_shortcode_regex();
		$options = array();
		if (preg_match_all('/'.$pattern.'/s', $content, $matches)
			&& !empty($matches)
			&& is_array($matches)
			&& array_key_exists( 2, $matches )
			&& in_array('sg_popup', $matches[2])
		)
		{
			foreach ($matches[0] as $key => $value) {
				//return current shortcode all attrs as assoc array
				$attrs = shortcode_parse_atts($matches[3][$key]);
				$currentAttrs = array();
				if (!empty($attrs['id'])) {
					$currentAttrs['id'] =  $attrs['id'];
				}
				if (!empty($attrs['insidepopup'])) {
					$currentAttrs['insidepopup'] = $attrs['insidepopup'];
				}
				if (empty($attrs['insidepopup']) || (!empty($attrs['insidepopup']) && $attrs['insidepopup'] != 'on')) {
					continue;
				}

				$options[$currentAttrs['id']] = $value;
			}
		}

		return apply_filters('sgpbPopupInsideShortcodes', $options);
	}

	/**
	 * TODO remove ASAP!
	 *
	 *  Collect all popups by taxonomy slug
	 *
	 * @since 1.0.0
	 *
	 * @param string $popupTermSlug category slug name
	 *
	 * @return array $popupIds random popups id
	 *
	 */
	public static function getPopupsByTermSlug($popupTermSlug)
	{
		$popupIds = array();

		$termPopups = get_transient(SGPB_TRANSIENT_POPUPS_TERMS);
		if ($termPopups === false) {
			$termPopups = get_posts(
				array(
					'post_type' => 'popupbuilder',
					'numberposts' => -1,
					'tax_query' => array(
						array(
							'taxonomy' => SG_POPUP_CATEGORY_TAXONOMY,
							'field' => 'slug',
							'terms' => $popupTermSlug
						)
					)
				)
			);
			set_transient(SGPB_TRANSIENT_POPUPS_TERMS, $termPopups, SGPB_TRANSIENT_TIMEOUT_WEEK);
		}

		if (empty($termPopups)) {
			return $popupIds;
		}

		foreach ($termPopups as $termPopup) {
			$popupIds[] = $termPopup->ID;
		}

		return $popupIds;
	}

	public function boolToChecked($var)
	{
		return ($var?'checked':'');
	}

	/**
	 * Delete custom inserted data
	 *
	 * @since 1.0.0
	 *
	 * @param int $postId current post page id
	 *
	 * @return void
	 *
	 */
	public static function deletePostCustomInsertedData($postId)
	{
		delete_post_meta($postId, 'sg_popup_target_custom');
	}

	/**
	 * Delete custom inserted events
	 *
	 * @since 1.0.0
	 *
	 * @param int $postId current post page id
	 *
	 * @return void
	 *
	 */
	public static function deletePostCustomInsertedEvents($postId)
	{
		delete_post_meta($postId, 'sgpb_popup_events_custom');
	}

	/**
	 * If popup Type does not have getPopupTypeOptions method
	 * it's tell popup does not have custom options
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 *
	 */
	public function getPopupTypeOptionsView()
	{
		return false;
	}

	/**
	 * If popup Type does not have getPopupTypeOptions method
	 * it's tell popup does not have custom options
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 *
	 */
	public function getPopupTypeMainView()
	{
		return false;
	}

	/**
	 * Remove popup option from admin view by option name
	 *
	 * @since 1.0.0
	 *
	 * @return array $removedOptions
	 *
	 */
	public function getRemoveOptions()
	{
		$removeOptions = array();

		return apply_filters('sgpbRemoveOptions', $removeOptions);
	}

	public static function createPopupTypeObjById($popupId)
	{
		global $SGPB_POPUP_TYPES;
		$typePath = '';
		$popupOptionsData = SGPopup::getPopupOptionsById($popupId);
		if (empty($popupOptionsData)) {
			return false;
		}
		$popupType = $popupOptionsData['sgpb-type'];
		$popupName = ucfirst(strtolower($popupType));
		$popupClassName = $popupName.'Popup';

		if (!empty($SGPB_POPUP_TYPES['typePath'][$popupType])) {
			$typePath = $SGPB_POPUP_TYPES['typePath'][$popupType];
		}

		if (!file_exists($typePath.$popupClassName.'.php')) {
			wp_die(esc_html__('Popup class does not exist', SG_POPUP_TEXT_DOMAIN));
		}
		require_once($typePath.$popupClassName.'.php');

		$popupClassName = __NAMESPACE__.'\\'.$popupClassName;
		$popupTypeObj = new $popupClassName();
		$popupTypeObj->setId($popupId);

		return $popupTypeObj;
	}

	/**
	 * if child class does not have this function we call parent function to not get any errors
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 *
	 */
	public static function getTablesSql()
	{
		return array();
	}

	/**
	 * if child class does not have this function we call parent function to not get any errors
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 *
	 */
	public static function getTableNames()
	{
		return array();
	}

	/**
	 *
	 * Get WordPress localization name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 *
	 */
	public function getSiteLocale()
	{
		$locale = get_bloginfo('language');
		$locale = str_replace('-', '_', $locale);

		return $locale;
	}

	public function addAdditionalSettings($postData = array(), $obj = null)
	{
		return array();
	}

	public function allowToLoad()
	{
		global $post;

		$popupChecker = PopupChecker::instance();
		$loadableModes = $popupChecker->isLoadable($this, $post);
		$this->setLoadableModes($loadableModes);

		return ($loadableModes['attr_event'] || $loadableModes['option_event']);
	}

	public function allowToLoadAJAX()
	{
		global $post;

		$popupChecker = PopupChecker::instance();
		$loadableModes = $popupChecker->isLoadable($this, $post, true);
		$this->setLoadableModes($loadableModes);

		return ($loadableModes['attr_event'] || $loadableModes['option_event']);
	}

	public static function getAllPopups($filters = array())
	{
		$args = array(
			'post_type' => SG_POPUP_POST_TYPE
		);
		$allPopups = array();
		$allPostData = ConfigDataHelper::getQueryDataByArgs($args);

		if (empty($allPostData)) {
			return $allPopups;
		}

		foreach ($allPostData->posts as $postData) {
			if (empty($postData)) {
				continue;
			}

			$popup = self::find($postData->ID, $args);
			if (empty($popup) || !($popup instanceof SGPopup)) {
				continue;
			}
			$type = $popup->getType();

			if (isset($filters['type'])) {
				if (is_array($filters['type'])) {
					if (!in_array($type, $filters['type'])) {
						continue;
					}
				}
				else if ($type != $filters['type']) {
					continue;
				}
			}
			$allPopups[] = $popup;
		}

		return $allPopups;
	}

	public function getPopupsIdAndTitle()
	{
		$allPopups = SGPopup::getAllPopups();
		$popupIdTitles = array();

		if (empty($allPopups)) {
			return $popupIdTitles;
		}
		$currentPopupId = $this->getId();

		foreach ($allPopups as $popup) {
			if (empty($popup)) {
				continue;
			}
			$id = $popup->getId();

			if ($id == $currentPopupId) {
				continue;
			}

			$title = $popup->getTitle();
			$type = $popup->getType();

			$popupIdTitles[$id] = $title.' - '.$type;
		}

		return $popupIdTitles;
	}

	public function getSubPopupObj()
	{
		$subPopups = array();
		$options = $this->getOptions();

		$specialBehaviors = isset($options['sgpb-behavior-after-special-events']) ? $options['sgpb-behavior-after-special-events'] : '';
		if (!empty($specialBehaviors) && is_array($specialBehaviors)) {
			foreach ($specialBehaviors as $behavior) {
				foreach ($behavior as $row) {
					if (!empty($row['param']) && $row['param'] == SGPB_CONTACT_FORM_7_BEHAVIOR_KEY) {
						if (!empty($row['operator']) && $row['operator'] == 'open-popup') {
							if (!empty($row['value'])) {
								$popupId = key($row['value']);
								$subPopupObj = self::find((int)$popupId);
								if (!empty($subPopupObj) && ($subPopupObj instanceof SGPopup)) {
									$subPopupObj->setEvents(array('param' => 'click', 'value' => ''));
									$subPopups[] = $subPopupObj;
								}
							}
						}
					}
				}
			}
		}

		return $subPopups;
	}

	public static function doInsideShortcode($insideShortcode)
	{
		return do_shortcode($insideShortcode);
	}

	public function popupShortcodesInsidePopup()
	{
		$popups = array();
		$args = array('insidePopup' => 'on');
		$popupContent = $this->getContent();
		$parentTarget = $this->getTarget();
		$insidePopupShortcodes = self::collectInsidePopupShortcodes($popupContent);
		if (empty($insidePopupShortcodes)) {
			return $popups;
		}
		foreach ($insidePopupShortcodes as $insidePopupId => $insidePopupShortcode) {
			$insidePopupId = (int)$insidePopupId;
			if (!$insidePopupId) {
				continue;
			}
			// true = find inside popup
			$insidePopup = self::find($insidePopupId, $args);
			if (empty($insidePopup) || $insidePopup == 'trash' || $insidePopup == 'inherit') {
				continue;
			}
			$events = array('insideclick');
			$insidePopup->setEvents($events);
			$popups[$insidePopupId] = $insidePopup;
		}

		$popupContent = self::doInsideShortcode($popupContent);
		$this->setContent($popupContent);

		return $popups;
	}

	public function getPopupOpeningCountById($popupId)
	{
		global $wpdb;

		$allCount = 0;
		$popupsCounterData = get_option('SgpbCounter');
		$popupCountFromAnalyticsData = 0;
		$tableName = $wpdb->prefix.'sgpb_analytics';
		if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
			$popupCountFromAnalyticsData = self::getAnalyticsDataByPopupId($popupId);
		}
		if (isset($popupsCounterData[$popupId])) {
			$allCount += $popupsCounterData[$popupId];
		}
		$allCount += $popupCountFromAnalyticsData;

		return $allCount;
	}

	public static function getAnalyticsDataByPopupId($popupId)
	{
		global $wpdb;
		// 7, 12, 13 => exclude close, subscription success, contact success events
		$stmt = $wpdb->prepare('SELECT COUNT(*) FROM '.$wpdb->prefix.'sgpb_analytics WHERE target_id = %d AND event_id NOT IN (7, 12, 13)', $popupId);
		$popupAnalyticsData = $wpdb->get_var($stmt);
		return $popupAnalyticsData;
	}

	public static function deleteAnalyticsDataByPopupId($popupId)
	{
		global $wpdb;
		$prepareSql = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'sgpb_analytics WHERE target_id = %d AND  event_id NOT IN (7, 12, 13) LIMIT 100', $popupId);
		$wpdb->query($prepareSql);
	}

	public static function getActivePopupsQueryString()
	{
		$activePopupsQuery = '';
		$args = array(
			'post_type' => SG_POPUP_POST_TYPE,
			'post_status' => array('trash', 'publish')
		);
		if (!class_exists('ConfigDataHelper')) {
			return $activePopupsQuery;
		}
		$allPostData = ConfigDataHelper::getQueryDataByArgs($args);
		$args['checkActivePopupType'] = true;
		$allPopups = $allPostData->posts;
		foreach ($allPopups as $post) {
			$id = $post->ID;
			$popup = self::find($id, $args);
			if (empty($popup)) {
				$activePopupsQuery .= $id.', ';
			}
		}
		if ($activePopupsQuery != '') {
			$activePopupsQuery = ' AND ID NOT IN ('.$activePopupsQuery.')';
			$activePopupsQuery = str_replace(', )', ') ', $activePopupsQuery);
		}

		return $activePopupsQuery;
	}

	public function isActive()
	{
		return $this->getOptionValue('sgpb-is-active', true);
	}
}
