<?php
namespace sgpb;
use \DateTime;
use \DateTimeZone;
use \ConfigDataHelper;

/**
 * Popup checker class to check if the popup must be loaded on the current page
 *
 * @since 1.0.0
 *
 */
class PopupChecker
{
	private static $instance;
	private $popup;
	private $isGeoAjaxModeEnabled;
	private $post;
	private $isAjaxCall;

	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function setPopup($popup)
	{
		$this->popup = $popup;
	}

	public function getPopup()
	{
		return $this->popup;
	}

	public function setPost($post)
	{
		$this->post = $post;
	}

	public function getPost()
	{
		return $this->post;
	}

	public function setPopupGeoAjaxModeActive($ajaxMode)
	{
		$this->isGeoAjaxModeEnabled = $ajaxMode;
	}

	public function getPopupGeoAjaxMode()
	{
		return $this->isGeoAjaxModeEnabled;
	}

	public function setIsAjaxCall($isAjax)
	{
		$this->isAjaxCall = $isAjax;
	}

	public function getIsAjaxCall()
	{
		return $this->isAjaxCall;
	}

	/**
	 * It checks whether popup should be loaded on the current page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $popupId popup id
	 * @param  object $post page post data
	 * @param  boolean $isAjax
	 *
	 * @return bool
	 *
	 */
	public function isLoadable($popup, $post, $isAjax = false)
	{
		$this->setPopup($popup);
		$this->setPost($post);

		$this->setPopupGeoAjaxModeActive($popup->getOptionValue('sgpb-enable-geo-ajax-mode'));
		$this->setIsAjaxCall($isAjax);

		$popupOptions = $popup->getOptions();
		$isActive = $popup->getOptionValue('sgpb-is-active', true);
		$saveMode = $popup->getSaveMode();
		$allowToLoad = $this->allowToLoad();

		if ($saveMode) {
			$allowToLoad['option_event'] = false;
			return $allowToLoad;
		}

		if (isset($popupOptions['sgpb-is-active'])) {
			$isActive = $popupOptions['sgpb-is-active'];
			if ($isActive) {
				$popup->setReportData($popup->getId());
			}
		}

		if (!$isActive) {
			$allowToLoad['option_event'] = false;
		}

		return $allowToLoad;
	}

	/**
	 * Decides whether popup data should be loaded or not
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 *
	 */
	private function allowToLoad()
	{
		$isCustomInserted = $this->isCustomInserted();

		$insertedModes = array(
			'attr_event' => false,
			'option_event' => false
		);

		if ($isCustomInserted) {
			$insertedModes['attr_event'] = true;
		}

		$target = $this->divideTargetData();
		$isPostInForbidden = $this->isPostInForbidden($target);

		if ($isPostInForbidden) {
			return $insertedModes;
		}

		if (!empty($target['forbidden']) && empty($target['permissive'])) {
			$conditions = $this->divideConditionsData();
			$conditions = apply_filters('sgpbFilterDividedConditions', $conditions);
			$isSatisfyForConditions = $this->isSatisfyForConditions($conditions);

			if ($isSatisfyForConditions === false) {
				return $insertedModes;
			}
			if ($this->isSatisfyForOtherConditions() === false) {
				return $insertedModes;
			}
			$insertedModes['option_event'] = true;
		}

		$isPermissive = $this->isPermissive($target);

		//If permissive for current page check conditions
		if ($isPermissive) {
			$conditions = $this->divideConditionsData();
			$conditions = apply_filters('sgpbFilterDividedConditions', $conditions);
			$isSatisfyForConditions = $this->isSatisfyForConditions($conditions);

			if ($isSatisfyForConditions === false) {
				return $insertedModes;
			}
			if ($this->isSatisfyForOtherConditions() === false) {
				return $insertedModes;
			}
			$insertedModes['option_event'] = $isPermissive;
		}

		return $insertedModes;
	}

	/**
	 * check is Satisfy popup conditions
	 *
	 * @since 1.0.0
	 *
	 * @param array $conditions assoc array
	 *
	 * @return bool
	 *
	 */
	private function isSatisfyForConditions($conditions)
	{
		// proStartSilver
		// this case when selected IS NOT operator
		$forbiddenConditions = $conditions['forbidden'];
		if (!empty($forbiddenConditions)) {
			if (!$this->getIsAjaxCall() && $this->getPopupGeoAjaxMode()) {
				return false;
			}
			foreach ($forbiddenConditions as $forbiddenCondition) {
				$isForbiddenConditions = $this->isSatisfyForConditionsOptions($forbiddenCondition);
				if ($isForbiddenConditions) {
					return false;
				} else {
					return true;
				}
			}
		}

		// this case when selected IS operator
		$permissiveOptions = $conditions['permissive'];
		if (!empty($permissiveOptions)) {
			if (!$this->getIsAjaxCall() && $this->getPopupGeoAjaxMode()) {
				return false;
			}
			foreach ($permissiveOptions as $permissiveOption) {
				$isPermissiveConditions = $this->isSatisfyForConditionsOptions($permissiveOption);
				return $isPermissiveConditions;
			}
		}
		if ($this->getIsAjaxCall() && $this->getPopupGeoAjaxMode()) {
			return false;
		}
		return true;
	}

	private function isSatisfyForConditionsOptions($option)
	{
		global $post;
		$paramName  = $option['param'];
		$defaultStatus = false;
		$isAllowedConditionFilters = array();
		if ($paramName == 'select_role') {
			return true;
		}
		if (!$defaultStatus && do_action('isAllowedForConditions', $option, $post)) {
			$defaultStatus = true;
		}
		$isAllowedConditionFilters = apply_filters('isAllowedConditionFilters', array($option));

		if (isset($isAllowedConditionFilters['status']) && $isAllowedConditionFilters['status'] === true) {
			$defaultStatus = true;
		}

		return $defaultStatus;
	}

	/**
	 * Check is popup inserted via short code or class attribute
	 *
	 * @since 1.0.0
	 *
	 * @param
	 *
	 * @return bool
	 *
	 */
	private function isCustomInserted()
	{
		$customInsertData = $this->getCustomInsertedData();
		$popup = $this->getPopup();
		// When popup object is empty it's mean popup is not custom inserted
		if (empty($popup)) {
			return false;
		}
		$popupId = $popup->getId();

		return in_array($popupId, $customInsertData);
	}

	/**
	 * Should load data in the current page
	 *
	 * @since 1.0.0
	 *
	 * @param array $target popup saved target data
	 *
	 * @return bool $isPermissive true => allow false => don't allow
	 *
	 */
	private function isPermissive($target)
	{
		$isPermissive = false;

		if (empty($target['permissive'])) {
			$isPermissive = false;
			return $isPermissive;
		}

		foreach ($target['permissive'] as $targetData) {
			if ($this->isSatisfyForParam($targetData)) {
				$isPermissive = true;
				break;
			}
		}

		return $isPermissive;
	}

	/**
	 * Check whether the target data disallows loading the popup data on the current page
	 *
	 * @since 1.0.0
	 *
	 * @param array $target popup saved target data
	 *
	 * @return bool $isForbidden true => don't allow false => allow
	 *
	 */
	private function isPostInForbidden($target)
	{
		$isForbidden = false;

		if (empty($target['forbidden'])) {
			return $isForbidden;
		}

		foreach ($target['forbidden'] as $targetData) {
			if ($this->isSatisfyForParam($targetData)) {
				$isForbidden = true;
				break;
			}
		}

		return $isForbidden;
	}

	/**
	 * Check whether the current page is corresponding to the saved target data
	 *
	 * @since 1.0.0
	 *
	 * @param array $targetData popup saved target data
	 *
	 * @return bool $isSatisfy
	 *
	 */
	private function isSatisfyForParam($targetData)
	{
		$isSatisfy = false;
		if ($this->getIsAjaxCall()){
			$popup = $this->getPopup();
			$postId = $popup->getCurrentPageIdForAjax();
		} else {
			$postId = get_queried_object_id();
		}

		if (empty($targetData['param'])) {
			return $isSatisfy;
		}
		$targetParam = $targetData['param'];
		$post = $this->getPost();
		if (isset($post) && empty($postId)) {
			$postId = $post->ID;
		}

		if ($targetParam == 'everywhere') {
			return true;
		}
		if (strpos($targetData['param'], '_all')) {
			$endIndex = strpos($targetData['param'], '_all');
			$postType = substr($targetData['param'], 0, $endIndex);
			$currentPostType = get_post_type($postId);

			if ($postType == $currentPostType) {
				$isSatisfy = true;
			}
		}
		else if (strpos($targetData['param'], '_archive')) {
		    $currentPostType = get_post_type();
		    if ($targetData['param'] == $currentPostType.'_archive') {
		        if (is_post_type_archive($currentPostType)) {
		            $isSatisfy = true;
		        }
		    }
		}
		else if (strpos($targetData['param'], '_selected')) {
			$values = array();

			if (!empty($targetData['value'])) {
				$values = array_keys($targetData['value']);
			}

			if (in_array($postId, $values)) {
				$isSatisfy = true;
			}
		}
		else if (strpos($targetData['param'], '_categories')) {
			$values = array();
			$isSatisfy = false;

			if (!empty($targetData['value'])) {
				$values = array_values($targetData['value']);
			}

			global $post;
			// get current all taxonomies of the current post
			$taxonomies = get_post_taxonomies($post);
			foreach ($taxonomies as $taxonomy) {
				// get current post all categories
				$terms = get_the_terms($post->ID, $taxonomy);
				if (!empty($terms)) {
					foreach ($terms as $term) {
						if (empty($term)) {
							continue;
						}
						if (in_array($term->term_id, $values)) {
							$isSatisfy = true;
							break;
						}
					}
				}
			}
		}
		else if ($targetData['param'] == 'post_type' && !empty($targetData['value'])) {
			$selectedCustomPostTypes = array_values($targetData['value']);
			$currentPostType = get_post_type($postId);

			if (in_array($currentPostType, $selectedCustomPostTypes)) {
				$isSatisfy = true;
			}
		}
		else if ($targetData['param'] == 'post_category' && !empty($targetData['value'])) {
			$values = $targetData['value'];
			$currentPostCategories = get_the_category($postId);
			$currentPostType = get_post_type($postId);
			if (empty($currentPostCategories) && $currentPostType == 'product') {
				$currentPostCategories = get_the_terms($postId, 'product_cat');
			}

			foreach ($currentPostCategories as $categoryName) {
				if (in_array($categoryName->term_id, $values) || in_array($categoryName->term_id, array_keys($values))) {
					$isSatisfy = true;
					break;
				}

			}
		}
		else if ($targetData['param'] == 'page_type' && !empty($targetData['value'])) {
			$postTypes = $targetData['value'];
			foreach ($postTypes as $postType) {

				if ($postType == 'is_home_page') {
					if (is_front_page() && is_home()) {
						// Default homepage
						$isSatisfy = true;
						break;
					} else if ( is_front_page() ) {
						// static homepage
						$isSatisfy = true;
						break;
					}
				}
				else if (function_exists($postType) && $postType()) {
					$isSatisfy = true;
					break;
				}
			}
		}
		else if ($targetData['param'] == 'page_template' && !empty($targetData['value'])) {
			$currentPageTemplate = basename(get_page_template());
			if (in_array($currentPageTemplate, $targetData['value'])) {
				$isSatisfy = true;
			}
		}
		else if ($targetData['param'] == 'post_tags') {
			if (has_tag()) {
				$isSatisfy = true;
			}
		}
		else if ($targetData['param'] == 'post_tags_ids') {
			$tagsObj = wp_get_post_tags($postId);
			$postTagsValues = isset($targetData['value']) ? (array)$targetData['value'] : array();
			$selectedTags = array_values($postTagsValues);

			foreach ($tagsObj as $tagObj) {
				if (in_array($tagObj->slug, $selectedTags) || in_array($tagObj->slug, array_keys($postTagsValues))) {
					$isSatisfy = true;
					break;
				}
			}
		}

		if (!$isSatisfy && do_action('isAllowedForTarget', $targetData, $post)) {
			$isSatisfy = true;
		}

		return $isSatisfy;
	}

	/**
	 * Divide conditions data to Permissive and Forbidden
	 *
	 * @since 1.0.0
	 *
	 * @return array $popupTargetData
	 *
	 */
	private function divideConditionsData()
	{
		$popup = $this->getPopup();
		$conditions = $popup->getConditions();
		$conditions = $this->divideIntoPermissiveAndForbidden($conditions);

		return $conditions;
	}
	/**
	 * Divide target data to Permissive and Forbidden
	 *
	 * @since 1.0.0
	 *
	 * @return array $popupTargetData
	 *
	 */
	public function divideTargetData()
	{
		$popup = $this->getPopup();
		$targetData = $popup->getTarget();
		return $this->divideIntoPermissiveAndForbidden($targetData);
	}

	/**
	 * Divide the Popup target data into Permissive And Forbidden assoc array
	 *
	 * @since 1.0.0
	 *
	 * @param array $postMetaData popup saved target data
	 *
	 * @return array $postMetaDivideData
	 *
	 */
	public function divideIntoPermissiveAndForbidden($targetData)
	{
		$permissive = array();
		$forbidden = array();
		$permissiveOperators = array('==');
		$forbiddenOperators = array('!=');
		$permissiveOperators = apply_filters('sgpbAdditionalPermissiveOperators', $permissiveOperators);
		$forbiddenOperators = apply_filters('sgpbAdditionalForbiddenOperators', $forbiddenOperators);
		if (!empty($targetData)) {
			foreach ($targetData as $data) {
				if (empty($data['operator']) && $data['param'] != 'everywhere' && !strpos($data['param'], '_all') && $data['param'] != 'post_tags') {
					break;
				}
				// set by default '==' (is) value for all not specific conditions like all pages/posts/other_custom_post_types for future correct detection
				if ($data['param'] == 'everywhere' || strpos($data['param'], '_all') || $data['param'] == 'post_tags') {
					$data['operator'] = '==';
				}
				if ((isset($data['operator']) && in_array($data['operator'], $permissiveOperators))) {
					$permissive[] = $data;
				}
				else if (in_array($data['operator'], $forbiddenOperators)) {
					$forbidden[] = $data;
				}
			}
		}

		$postMetaDivideData = array(
			'permissive' => $permissive,
			'forbidden' => $forbidden
		);

		return $postMetaDivideData;
	}

	/**
	 * Get custom inserted data
	 *
	 * @since 1.0.0
	 *
	 * @return array $insertedData
	 */
	public function getCustomInsertedData()
	{
		$post = $this->getPost();
		$insertedData = array();

		if (isset($post)) {
			$insertedData = SGPopup::getCustomInsertedDataByPostId($this->getPost()->ID);
		}

		return $insertedData;
	}

	/**
	 * Check Popup conditions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 *
	 */
	private function isSatisfyForOtherConditions()
	{
		$popup = $this->getPopup();
		$popupOptions = $popup->getOptions();
		$popupId = $popup->getId();

		$dontAlowOpenPopup = apply_filters('sgpbOtherConditions', array('id' => $popupId, 'popupOptions' => $popupOptions, 'popupObj' => $popup));

		return $dontAlowOpenPopup['status'];
	}

	public static function checkUserStatus($savedStatus)
	{
		$equalStatus = true;

		/*When current user status and saved options does not matched popup must not open*/
		if (is_user_logged_in() != (int)$savedStatus) {
			$equalStatus = false;
		}

		return $equalStatus;
	}

	public static function checkLanguage($popupLanguage = '')
	{
		global $post;

		$postId = $post->ID;
		if (!function_exists('wpml_get_language_information')) {
			return true;
		}
		$postLanguage = wpml_get_language_information($postId);
		if (is_wp_error($postLanguage)) {
			return true;
		}
		if (!empty($_GET['lang']) && (sanitize_text_field($_GET['lang']) == $popupLanguage)) {
            return true;
        }
		if ($postLanguage['language_code'] != $popupLanguage) {
			return false;
		}

		return true;
	}

	public static function checkOtherConditionsActions($args)
	{
		if (empty($args['id']) || empty($args['popupOptions'])) {
			return false;
		}

		$popupOptions = $args['popupOptions'];

		if (!empty($popupOptions['sgpb-icl_post_language'])) {
			$languageCompatibilty = PopupChecker::checkLanguage($popupOptions['sgpb-icl_post_language']);

			if ($languageCompatibilty === false) {
				return $languageCompatibilty;
			}
		}


		// proStartSilver
		//User status check
		if (!empty($popupOptions['sgpb-user-status'])) {
			$restrictUserStatus = PopupChecker::checkUserStatus($popupOptions['sgpb-loggedin-user']);

			if ($restrictUserStatus === false) {
				return $restrictUserStatus;
			}
		}

		// proEndSilver

		// proStartPlatinum
		// proEndPlatinum

		// checking by popup type
		if (!empty($popupOptions['sgpb-type'])) {
			$popupClassName = SGPopup::getPopupClassNameFormType($popupOptions['sgpb-type']);
			$popupClassName = __NAMESPACE__.'\\'.$popupClassName;

			if (method_exists($popupClassName, 'allowToOpen')) {
				$allowToOpen = $popupClassName::allowToOpen($popupOptions, $args);
				return $allowToOpen;
			}
		}

		return apply_filters('checkOtherConditions', true);
	}
}
