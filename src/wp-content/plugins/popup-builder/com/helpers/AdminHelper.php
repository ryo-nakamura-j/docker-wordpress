<?php
namespace sgpb;
use \DateTime;
use \DateTimeZone;
use \SgpbDataConfig;
use \Elementor;
use sgpbsubscriptionplus\SubscriptionPlusAdminHelper;

class AdminHelper
{
	/**
	 * Get extension options data which are included inside the free version
	 *
	 * @since 3.0.8
	 *
	 * @return assoc array $extensionOptions
	 */
	public static function getExtensionAvaliabilityOptions()
	{
		$extensionOptions = array();
		// advanced closing option
		$extensionOptions[SGPB_POPUP_ADVANCED_CLOSING_PLUGIN_KEY] = array(
			'sgpb-close-after-page-scroll',
			'sgpb-auto-close',
			'sgpb-enable-popup-overlay',
			'sgpb-disable-popup-closing'
		);
		// schedule extension
		$extensionOptions[SGPB_POPUP_SCHEDULING_EXTENSION_KEY] = array(
			'otherConditionsMetaBoxView'
		);
		// geo targeting extension
		$extensionOptions[SGPB_POPUP_GEO_TARGETING_EXTENSION_KEY] = array(
			'popupConditionsSection'
		);
		// advanced targeting extension
		$extensionOptions[SGPB_POPUP_ADVANCED_TARGETING_EXTENSION_KEY] = array(
			'popupConditionsSection'
		);

		return $extensionOptions;
	}

	public static function getPopupTypesPageURL()
	{
		return admin_url('edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SG_POPUP_POST_TYPE);
	}

	public static function getSettingsURL($args = array())
	{
		$url = admin_url('/edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SG_POPUP_SETTINGS_PAGE);

		return self::addArgsToURl($url, $args);
	}

	public static function getPopupExportURL()
	{
		$exportURL = admin_url('export.php');
		$url = add_query_arg(array(
			'download' => true,
			'content' => SG_POPUP_POST_TYPE,
			'sgpbExportAction' => 1
		), $exportURL);

		return $url;
	}

	public static function addArgsToURl($url, $args = array())
	{
		$resultURl = add_query_arg($args, $url);

		return $resultURl;
	}

	public static function buildCreatePopupUrl($popupType)
	{
		$isAvailable = $popupType->isAvailable();
		$name = $popupType->getName();

		$popupUrl = SG_POPUP_ADMIN_URL.'post-new.php?post_type='.SG_POPUP_POST_TYPE.'&sgpb_type='.$name;

		if (!$isAvailable) {
			$popupUrl = SG_POPUP_PRO_URL;
		}

		return $popupUrl;
	}

	public static function getPopupThumbClass($popupType)
	{
		$isAvailable = $popupType->isAvailable();
		$name = $popupType->getName();

		$popupTypeClassName = $name.'-popup';

		if (!$isAvailable) {
			$popupTypeClassName .= '-pro';
		}

		return $popupTypeClassName;
	}

	public static function createSelectBox($data, $selectedValue, $attrs)
	{
		$attrString = '';
		$selected = '';
		$selectBoxCloseTag = '</select>';

		if (!empty($attrs) && isset($attrs)) {

			foreach ($attrs as $attrName => $attrValue) {
				$attrString .= ''.$attrName.'="'.$attrValue.'" ';
			}
		}

		$selectBox = '<select '.$attrString.'>';
		if (empty($data) || !is_array($data)) {
			$selectBox .= $selectBoxCloseTag;
			return $selectBox;
		}

		foreach ($data as $value => $label) {
			// When is multiSelect
			if (is_array($selectedValue)) {
				$isSelected = in_array($value, $selectedValue);
				if ($isSelected) {
					$selected = 'selected';
				}
			}
			else if ($selectedValue == $value) {
				$selected = 'selected';
			}
			else if (is_array($value) && in_array($selectedValue, $value)) {
				$selected = 'selected';
			}

			if (is_array($label)) {
				$selectBox .= '<optgroup label="'.$value.'">';
				foreach ($label as $key => $optionLabel) {
					$selected = '';
					if (is_array($selectedValue)) {
						$isSelected = in_array($key, $selectedValue);
						if ($isSelected) {
							$selected = 'selected';
						}
					}
					else if ($selectedValue == $key) {
						$selected = 'selected';
					}
					else if (is_array($key) && in_array($selectedValue, $key)) {
						$selected = 'selected';
					}

					$selectBox .= '<option value="'.$key.'" '.$selected.'>'.$optionLabel.'</option>';
				}
				$selectBox .= '</optgroup>';
			}
			else {
				$selectBox .= '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
			}

			$selected = '';
		}

		$selectBox .= $selectBoxCloseTag;

		return $selectBox;
	}

	public static function createInput($data, $selectedValue, $attrs)
	{
		$attrString = '';
		$savedData = $data;
		if (isset($selectedValue) && $selectedValue !== '') {
			$savedData = $selectedValue;
		}
		if (empty($savedData)) {
			$savedData = '';
		}

		if (!empty($attrs) && isset($attrs)) {

			foreach ($attrs as $attrName => $attrValue) {
				if ($attrName == 'class') {
					$attrValue .= '';
				}
				$attrString .= ''.$attrName.'="'.$attrValue.'" ';
			}
		}

		$input = "<input $attrString value=\"".esc_attr($savedData)."\">";

		return $input;
	}

	public static function createCheckBox($data, $selectedValue, $attrs)
	{
		$attrString = '';
		$checked = '';

		if (!empty($selectedValue)) {
			$checked = 'checked';
		}
		if (!empty($attrs) && isset($attrs)) {

			foreach ($attrs as $attrName => $attrValue) {
				$attrString .= ''.$attrName.'="'.$attrValue.'" ';
			}
		}

		$input = "<input $attrString $checked>";

		return $input;
	}

	public static function createRadioButtons($elements, $name, $selectedInput, $lineMode = false, $extraHtmlAfterInput = '')
	{
		$str = '';
		$allowed_html = self::allowed_html_tags();

		foreach ($elements as $key => $element) {
			$value = '';
			$checked = '';
			$stringWithlabel = '';
			$labelClasses = '';
			$stringLabel = '';

			if (isset($element['value'])) {
				$value = $element['value'];
			}
			if (isset($element['label_class'])) {
				$labelClasses = 'class="'.$element['label_class'].'"';
				$stringLabel = '<span class="sgpb-margin-bottom-10">'.esc_attr($value).'</span>';
			}

			if (is_array($element) && $element['value'] == $selectedInput) {
				$checked = 'checked';
			}
			else if (!is_array($element) && $element == $selectedInput) {
				$checked = 'checked';
			}
			$attrStr = '';
			if (isset($element['data-attributes'])) {
				foreach ($element['data-attributes'] as $attrKey => $dataValue) {
					$attrStr .= $attrKey.'="'.esc_attr($dataValue).'" ';
				}
			}
			if (!empty($extraHtmlAfterInput)) {
				if ($extraHtmlAfterInput == 'img') {
					$extraHtmlAfterInput = '<img src="">';
				}
				else if ($extraHtmlAfterInput == 'bg_img') {
					$extraHtmlAfterInput = '<span class="sgpb-popup-theme-img sgpb-margin-x-7"></span>';
				}
			}

			if ($lineMode) {
				if (!empty($extraHtmlAfterInput)) {
					$str .= '<label '.$labelClasses.'><input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.$checked.' '.$attrStr.'>'.$extraHtmlAfterInput.$stringLabel.'</label>';
				}
				else {
					$str .= '<input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.$checked.' '.$attrStr.'>';
				}
			}
			else {
				$str .= '<div class="row form-group">';
				$str .= '<label class="col-md-5 control-label">'.__($element['title'], SG_POPUP_TEXT_DOMAIN).'</label>';
				$str .= '<div class="col-sm-7"><input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.$checked.' autocomplete="off">'.$extraHtmlAfterInput.'</div>';
				$str .= '</div>';
			}
		}

		echo wp_kses($str, $allowed_html);
	}

	public static function getDateObjFromDate($dueDate, $timezone = 'America/Los_Angeles', $format = 'Y-m-d H:i:s')
	{
		$dateObj = new DateTime($dueDate, new DateTimeZone($timezone));
		$dateObj->format($format);

		return $dateObj;
	}

	/**
	 * Serialize data
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 *
	 * @return string $serializedData
	 */
	public static function serializeData($data = array())
	{
		$serializedData = serialize($data);

		return $serializedData;
	}

	/**
	 * Get correct size to use it safely inside CSS rules
	 *
	 * @since 1.0.0
	 *
	 * @param string $dimension
	 *
	 * @return string $size
	 */
	public static function getCSSSafeSize($dimension)
	{
		if (empty($dimension)) {
			return 'inherit';
		}

		$size = (int)$dimension.'px';
		// If user write dimension in px or % we give that dimension to target otherwise the default value will be px
		if (strpos($dimension, '%') || strpos($dimension, 'px')) {
			$size = $dimension;
		}

		return $size;
	}

	public static function deleteSubscriptionPopupSubscribers($popupId)
	{
		global $wpdb;

		$prepareSql = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE subscriptionType = %s', $popupId);
		$wpdb->query($prepareSql);
	}

	public static function subscribersRelatedQuery($query = '', $additionalColumn = '')
	{
		global $wpdb;
		$subscribersTablename = $wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME;
		$postsTablename = $wpdb->prefix.SGPB_POSTS_TABLE_NAME;

		if ($query == '') {
			$query = 'SELECT firstName, lastName, email, cDate, '.$additionalColumn.' '.$postsTablename.'.post_title AS subscriptionTitle FROM '.$subscribersTablename.' ';
		}

		$searchQuery = ' unsubscribed <> 1';
		$filterCriteria = '';

		$query .= ' LEFT JOIN '.$postsTablename.' ON '.$postsTablename.'.ID='.$subscribersTablename.'.subscriptionType';

		if (isset($_GET['sgpb-subscription-popup-id']) && !empty($_GET['sgpb-subscription-popup-id'])) {
			$filterCriteria = sanitize_text_field($_GET['sgpb-subscription-popup-id']);
			if ($filterCriteria != 'all') {
				$searchQuery .= " AND (subscriptionType = '".esc_sql((int)$filterCriteria)."')";
			}
		}
		if ($filterCriteria != '' && $filterCriteria != 'all' && isset($_GET['s']) && !empty($_GET['s'])) {
			$searchQuery .= ' AND ';
		}
		if (isset($_GET['s']) && !empty($_GET['s'])) {
			$searchCriteria = sanitize_text_field($_GET['s']);
			$lastPartOfTheQuery = substr($searchQuery, -5);
			if (strpos($lastPartOfTheQuery, 'AND') <= 0) {
				$searchQuery .= ' AND ';
			}
			$searchCriteria = "%" . esc_sql($wpdb->esc_like( $searchCriteria )) . "%";
			$searchQuery .= "(firstName LIKE '$searchCriteria' or lastName LIKE '$searchCriteria' or email LIKE '$searchCriteria' or $postsTablename.post_title LIKE '$searchCriteria')";
		}
		if (isset($_GET['sgpb-subscribers-date']) && !empty($_GET['sgpb-subscribers-date'])) {
			$filterCriteriaDate = sanitize_text_field($_GET['sgpb-subscribers-date']);
			if ($filterCriteriaDate != 'all') {
				if ($searchQuery != '') {
					$searchQuery .= ' AND ';
				}
				$searchQuery .= " cDate LIKE '".esc_sql( $wpdb->esc_like($filterCriteriaDate))."%'";
			}
		}
		if ($searchQuery != '') {
			$query .= " WHERE $searchQuery";
		}

		return $query;
	}

	public static function themeRelatedSettings($popupId, $buttonPosition, $theme)
	{
		if ($popupId) {
			if ($theme == 'sgpb-theme-1' || $theme == 'sgpb-theme-4' || $theme == 'sgpb-theme-5') {
				if (!isset($buttonPosition)) {
					$buttonPosition = 'bottomRight';
				}
			}
			else if ($theme == 'sgpb-theme-2' || $theme == 'sgpb-theme-3' || $theme == 'sgpb-theme-6') {
				if (!isset($buttonPosition)) {
					$buttonPosition = 'topRight';
				}
			}
		}
		else {
			if (isset($theme)) {
				if ($theme == 'sgpb-theme-1' || $theme == 'sgpb-theme-4' || $theme == 'sgpb-theme-5') {
					$buttonPosition = 'bottomRight';
				}
				else if ($theme == 'sgpb-theme-2' || $theme == 'sgpb-theme-3' || $theme == 'sgpb-theme-6') {
					$buttonPosition = 'topRight';
				}
			}
			else {
				/* by default set position for the first theme */
				$buttonPosition = 'bottomRight';
			}
		}

		return $buttonPosition;
	}

	/**
	 * Create html attrs
	 *
	 * @since 1.0.0
	 *
	 * @param array $attrs
	 *
	 * @return string $attrStr
	 */
	public static function createAttrs($attrs)
	{
		$attrStr = '';

		if (empty($attrs)) {
			return $attrStr;
		}

		foreach ($attrs as $attrKey => $attrValue) {
			$attrStr .= $attrKey.'="'.$attrValue.'" ';
		}

		return $attrStr;
	}

	public static function getFormattedDate($date)
	{
		$date = strtotime($date);
		$month = date('F', $date);
		$year = date('Y', $date);

		return $month.' '.$year;
	}

	public static function defaultButtonImage($theme, $closeImage = '')
	{
		$currentPostType = self::getCurrentPopupType();
		if (defined('SGPB_POPUP_TYPE_RECENT_SALES') && $currentPostType == SGPB_POPUP_TYPE_RECENT_SALES) {
			$theme = 'sgpb-theme-6';
		}
		// if no image, set default by theme
		if ($closeImage == '') {
			if ($theme == 'sgpb-theme-1' || !$theme) {
				$closeImage = SG_POPUP_IMG_URL.'theme_1/close.png';
			}
			else if ($theme == 'sgpb-theme-2') {
				$closeImage = SG_POPUP_IMG_URL.'theme_2/close.png';
			}
			else if ($theme == 'sgpb-theme-3') {
				$closeImage = SG_POPUP_IMG_URL.'theme_3/close.png';
			}
			else if ($theme == 'sgpb-theme-5') {
				$closeImage = SG_POPUP_IMG_URL.'theme_5/close.png';
			}
			else if ($theme == 'sgpb-theme-6') {
				$closeImage = SG_POPUP_IMG_URL.'theme_6/close.png';
			}
		}
		else {
			$closeImage = self::getImageDataFromUrl($closeImage);
		}

		return $closeImage;
	}

	public static function getPopupPostAllowedUserRoles()
	{
		$userSavedRoles = get_option('sgpb-user-roles');

		if (empty($userSavedRoles) || !is_array($userSavedRoles)) {
			$userSavedRoles = array('administrator');
		}
		else {
			array_push($userSavedRoles, 'administrator');
		}

		return $userSavedRoles;
	}

	public static function showMenuForCurrentUser()
	{
		return self::userCanAccessTo();
	}

	public static function getPopupsIdAndTitle($excludesPopups = array())
	{
		$allPopups = SGPopup::getAllPopups();
		$popupIdTitles = array();

		if (empty($allPopups)) {
			return $popupIdTitles;
		}

		foreach ($allPopups as $popup) {
			if (empty($popup)) {
				continue;
			}

			$id = $popup->getId();
			$title = $popup->getTitle();
			$type = $popup->getType();

			if (!empty($excludesPopups)) {
				foreach ($excludesPopups as $excludesPopupId) {
					if ($excludesPopupId != $id) {
						$popupIdTitles[$id] = $title.' - '.$type;
					}
				}
			}
			else {
				$popupIdTitles[$id] = $title.' - '.$type;
			}
		}

		return $popupIdTitles;
	}

	/**
	 * Merge two array and merge same key values to same array
	 *
	 * @since 1.0.0
	 *
	 * @param array $array1
	 * @param array $array2
	 *
	 * @return array|bool
	 *
	 */
	public static function arrayMergeSameKeys($array1, $array2)
	{
		if (empty($array1)) {
			return array();
		}

		$modified = false;
		$array3 = array();
		foreach ($array1 as $key => $value) {
			if (isset($array2[$key]) && is_array($array2[$key])) {
				$arrDifference = array_diff($array2[$key], $array1[$key]);
				if (empty($arrDifference)) {
					continue;
				}

				$modified = true;
				$array3[$key] = array_merge($array2[$key], $array1[$key]);
				unset($array2[$key]);
				continue;
			}

			$modified = true;
			$array3[$key] = $value;
		}

		// when there are no values
		if (!$modified) {
			return $modified;
		}

		return $array2 + $array3;
	}

	public static function getCurrentUserRole()
	{
		$role = array('administrator');

		if (is_multisite()) {
			$getUsersObj = array();
			if (get_current_user_id() !== 0){
				$getUsersObj = get_users(
					array(
						'blog_id' => get_current_blog_id(),
						'search' => get_current_user_id()
					)
				);
			}

			if (!empty($getUsersObj[0])) {
				$roles = $getUsersObj[0]->roles;

				if (is_array($roles) && !empty($roles)) {
					$role = array_merge($role, $getUsersObj[0]->roles);
				}
			}

			return $role;
		}

		global $current_user;
		if (!empty($current_user)) {
            $role = $current_user->roles;
		}

		return $role;
	}

	public static function hexToRgba($color, $opacity = false)
	{
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if (empty($color)) {
			return $default;
		}

		//Sanitize $color if "#" is provided
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		}
		else if (strlen($color) == 3) {
			$hex = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		}
		else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb = array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if ($opacity !== false) {
			if (abs($opacity) > 1) {
				$opacity = 1.0;
			}
			$output = 'rgba('.implode(',', $rgb).','.$opacity.')';
		}
		else {
			$output = 'rgb('.implode(',', $rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}

	public static function getAllActiveExtensions()
	{
		$extensions = SgpbDataConfig::getOldExtensionsInfo();
		$labels = array();

		foreach ($extensions as $extension) {
			if (file_exists(WP_PLUGIN_DIR.'/'.$extension['folderName'])) {
				$labels[] = $extension['label'];
			}
		}

		return $labels;
	}

	public static function renderExtensionsContent()
	{
		$extensions = self::getAllActiveExtensions();
		ob_start();
		?>
		<p class="sgpb-extension-notice-close">x</p>
		<div class="sgpb-extensions-list-wrapper">
			<div class="sgpb-notice-header">
				<h3><?php esc_html_e('Popup Builder plugin has been successfully updated', SG_POPUP_TEXT_DOMAIN); ?></h3>
				<h4><?php esc_html_e('The following extensions need to be updated manually', SG_POPUP_TEXT_DOMAIN); ?></h4>
			</div>
			<ul class="sgpb-extensions-list">
				<?php foreach ($extensions as $extensionName): ?>
					<a target="_blank" href="https://popup-builder.com/forms/control-panel/"><li><?php echo esc_html($extensionName); ?></li></a>
				<?php endforeach; ?>
			</ul>
		</div>
		<p class="sgpb-extension-notice-dont-show"><?php esc_html_e('Don\'t show again', SG_POPUP_TEXT_DOMAIN)?></p>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	public static function getReverseConvertIds()
	{
		$idsMappingSaved = get_option('sgpbConvertedIds');
		$ids = array();

		if ($idsMappingSaved) {
			$ids = $idsMappingSaved;
		}

		return array_flip($ids);
	}

	public static function getAllFreeExtensions()
	{
		$allExtensions = SgpbDataConfig::allFreeExtensionsKeys();

		$notActiveExtensions = array();
		$activeExtensions = array();

		foreach ($allExtensions as $extension) {
			if (!is_plugin_active($extension['pluginKey'])) {
				$notActiveExtensions[] = $extension;
			}
			else {
				$activeExtensions[] = $extension;
			}
		}

		$divideExtension = array(
			'noActive' => $notActiveExtensions,
			'active' => $activeExtensions
		);

		return $divideExtension;
	}

	public static function getAllExtensions()
	{
		$allExtensions = SgpbDataConfig::allExtensionsKeys();

		$notActiveExtensions = array();
		$activeExtensions = array();

		foreach ($allExtensions as $extension) {
			if (!is_plugin_active($extension['pluginKey'])) {
				$notActiveExtensions[] = $extension;
			}
			else {
				$activeExtensions[] = $extension;
			}
		}

		$divideExtension = array(
			'noActive' => $notActiveExtensions,
			'active' => $activeExtensions
		);

		return $divideExtension;
	}

	public static function renderAlertProblem()
	{
		ob_start();
		?>
		<div id="welcome-panel" class="update-nag sgpb-alert-problem">
			<div class="welcome-panel-content">
				<p class="sgpb-problem-notice-close">x</p>
				<div class="sgpb-alert-problem-text-wrapper">
					<h3><?php esc_html_e('Popup Builder plugin has been updated to the new version 3.', SG_POPUP_TEXT_DOMAIN); ?></h3>
					<h5><?php esc_html_e('A lot of changes and improvements have been made.', SG_POPUP_TEXT_DOMAIN); ?></h5>
					<h5><?php _e('In case of any issues, please contact us <a href="<?php echo SG_POPUP_TICKET_URL; ?>" target="_blank">here</a>.', SG_POPUP_TEXT_DOMAIN); ?></h5>
				</div>
				<p class="sgpb-problem-notice-dont-show"><?php esc_html_e('Don\'t show again', SG_POPUP_TEXT_DOMAIN); ?></p>
			</div>
		</div>
		<?php
		$content = ob_get_clean();

		return $content;
	}

	public static function getTaxonomyBySlug($slug = '')
	{
		$allTerms = get_terms(array('hide_empty' => false));

		$result = array();
		if (empty($allTerms)) {
			return $result;
		}
		if ($slug == '') {
			return $allTerms;
		}
		foreach ($allTerms as $term) {
			if ($term->slug == $slug) {
				return $term;
			}
		}
	}

	public static function getCurrentPopupType()
	{
		$type = '';
		if (!empty($_GET['sgpb_type'])) {
			$type  = sanitize_text_field($_GET['sgpb_type']);
		}

		$currentPostType = self::getCurrentPostType();

		if ($currentPostType == SG_POPUP_POST_TYPE && !empty($_GET['post'])) {
			$popupObj = SGPopup::find(sanitize_text_field($_GET['post']));
			if (is_object($popupObj)) {
				$type = $popupObj->getType();
			}
		}

		return $type;
	}

	public static function getCurrentPostType()
	{
		global $post_type;
		global $post;
		$currentPostType = '';

		if (is_object($post)) {
			$currentPostType = $post->post_type;
		}

		// in some themes global $post returns null
		if (empty($currentPostType)) {
			$currentPostType = $post_type;
		}

		if (empty($currentPostType) && !empty($_GET['post'])) {
			$currentPostType = get_post_type(sanitize_text_field($_GET['post']));
		}

		return $currentPostType;
	}

	/**
	 * Get image encoded data from URL
	 *
	 * @param $imageUrl
	 * @param $shouldNotConvertBase64
	 *
	 * @return string
	 */

	public static function getImageDataFromUrl($imageUrl, $shouldNotConvertBase64 = false)
	{
		$remoteData = wp_remote_get($imageUrl);
		if (is_wp_error($remoteData) && $shouldNotConvertBase64) {
			return SG_POPUP_IMG_URL.'NoImage.png';
		}

		if (!$shouldNotConvertBase64) {
			$imageData = wp_remote_retrieve_body($remoteData);
			$imageUrl = base64_encode($imageData);
		}

		return $imageUrl;
	}

	public static function deleteUserFromSubscribers($params = array())
	{
		global $wpdb;

		$email = '';
		$popup = '';
		$noSubscriber = true;

		if (isset($params['email'])) {
			$email = $params['email'];
		}
		if (isset($params['popup'])) {
			$popup = $params['popup'];
		}

		$prepareSql = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE email = %s && subscriptionType = %s', $email, $popup);
		$res = $wpdb->get_row($prepareSql, ARRAY_A);
		if (!isset($res['id'])) {
			$noSubscriber = false;
		}
		$params['subscriberId'] = $res['id'];

		$subscriber = self::subscriberExists($params);
		if ($subscriber && $noSubscriber) {
			self::deleteSubscriber($params);
		}
		else if (!$noSubscriber) {
			_e('<span>Oops, something went wrong, please try again or contact the administrator to check more info.</span>', SG_POPUP_TEXT_DOMAIN);
			wp_die();
		}
	}

	public static function subscriberExists($params = array())
	{
		if (empty($params)) {
			return false;
		}

		$receivedToken = $params['token'];
		$realToken = md5($params['subscriberId'].$params['email']);
		if ($receivedToken == $realToken) {
			return true;
		}

	}

	public static function deleteSubscriber($params = array())
	{
		global $wpdb;
		$homeUrl = get_home_url();

		if (empty($params)) {
			return false;
		}
		// send email to admin about user unsubscription
		self::sendEmailAboutUnsubscribe($params);

		$prepareSql = $wpdb->prepare('UPDATE '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' SET unsubscribed = 1 WHERE id = %s ', $params['subscriberId']);
		$wpdb->query($prepareSql);

		_e('<span>You have successfully unsubscribed. <a href="'.esc_attr($homeUrl).'">click here</a> to go to the home page.</span>', SG_POPUP_TEXT_DOMAIN);
		wp_die();
	}

	public static function sendEmailAboutUnsubscribe($params = array())
	{
		if (empty($params)) {
			return false;
		}

		$newsletterOptions = get_option('SGPB_NEWSLETTER_DATA');
		$receiverEmail = get_bloginfo('admin_email');
		$userEmail = $params['email'];
		$emailTitle = __('Unsubscription', SG_POPUP_TEXT_DOMAIN);
		$subscriptionFormId = (int)$newsletterOptions['subscriptionFormId'];
		$subscriptionFormTitle = get_the_title($subscriptionFormId);

		$message = __('User with '.$userEmail.' email has unsubscribed from '.$subscriptionFormTitle.' mail list', SG_POPUP_TEXT_DOMAIN);

		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'From: WordPress Popup Builder'."\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n"; //set UTF-8

		wp_mail($receiverEmail, $emailTitle, $message, $headers);
	}

	public static function addUnsubscribeColumn()
	{
		global $wpdb;

		$sql = 'ALTER TABLE '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' ADD COLUMN unsubscribed INT NOT NULL DEFAULT 0 ';
		$wpdb->query($sql);
	}

	public static function isPluginActive($key)
	{
		$allExtensions = SgpbDataConfig::allExtensionsKeys();
		$isActive = false;
		foreach ($allExtensions as $extension) {
			if (isset($extension['key']) && $extension['key'] == $key) {
				if (is_plugin_active($extension['pluginKey'])) {
					$isActive = true;
					return $isActive;
				}
			}
		}

		return $isActive;
	}

	public static function supportBannerNotification()
	{
		$content = '<div class="sgpb-support-notification-wrapper sgpb-wrapper"><h4 class="sgpb-support-notification-title">'.__('Need some help?', SG_POPUP_TEXT_DOMAIN).'</h4>';
		$content .= '<h4 class="sgpb-support-notification-title">'.__('Let us know what you think.', SG_POPUP_TEXT_DOMAIN).'</h4>';
		$content .= '<a class="btn btn-info" target="_blank" href="'.SG_POPUP_RATE_US_URL.'"><span class="dashicons sgpb-dashicons-heart sgpb-info-text-white"></span><span class="sg-info-text">'.__('Rate Us', SG_POPUP_TEXT_DOMAIN).'</span></a>';
		$content .= '<a class="btn btn-info" target="_blank" href="'.SG_POPUP_TICKET_URL.'"><span class="dashicons sgpb-dashicons-megaphone sgpb-info-text-white"></span>'.__('Support Portal', SG_POPUP_TEXT_DOMAIN).'</a>';
		$content .= '<a class="btn btn-info" target="_blank" href="https://wordpress.org/support/plugin/popup-builder"><span class="dashicons sgpb-dashicons-admin-plugins sgpb-info-text-white"></span>'.__('Support Forum', SG_POPUP_TEXT_DOMAIN).'</a>';
		$content .= '<a class="btn btn-info" target="_blank" href="'.SG_POPUP_STORE_URL.'"><span class="dashicons sgpb-dashicons-editor-help sgpb-info-text-white"></span>'.__('LIVE chat', SG_POPUP_TEXT_DOMAIN).'</a>';
		$content .= '<a class="btn btn-info" target="_blank" href="mailto:support@popup-builder.com?subject=Hello"><span class="dashicons sgpb-dashicons-email-alt sgpb-info-text-white"></span>'.__('Email', SG_POPUP_TEXT_DOMAIN).'</a></div>';
		$content .= '<div class="sgpb-support-notification-dont-show">'.__('Bored of this?').'<a class="sgpb-dont-show-again-support-notification" href="javascript:void(0)">'.__(' Press here ').'</a>'.__('and we will not show it again!').'</div>';

		return $content;
	}

	public static function getMaxOpenDaysMessage()
	{
		$getUsageDays = self::getPopupUsageDays();
		$firstHeader = __('<h1 class="sgpb-review-h1"><strong class="sgrb-review-strong">This is great!</strong> We have noticed that you are using Popup Builder plugin on your site for '.$getUsageDays.' days, we are thankful for that.</h1>', SG_POPUP_TEXT_DOMAIN);
		$popupContent = self::getMaxOpenPopupContent($firstHeader, 'days');

		return $popupContent;
	}

	public static function getPopupUsageDays()
	{
		$installDate = get_option('SGPBInstallDate');

		$timeDate = new \DateTime('now');
		$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
		$diff = $timeNow-$installDate;
		$days  = floor($diff/(60*60*24));

		return $days;
	}

	public static function getMaxOpenPopupContent($firstHeader, $type)
	{
		ob_start();
		?>
		<style>
			.sgpb-buttons-wrapper .press{
				box-sizing:border-box;
				cursor:pointer;
				display:inline-block;
				font-size:1em;
				margin:0;
				padding:0.5em 0.75em;
				text-decoration:none;
				transition:background 0.15s linear
			}
			.sgpb-buttons-wrapper .press-grey {
				background-color:#9E9E9E;
				border:2px solid #9E9E9E;
				color: #FFF;
			}
			.sgpb-buttons-wrapper .press-lightblue {
				background-color:#03A9F4;
				border:2px solid #03A9F4;
				color: #FFF;
			}
			.sgpb-buttons-wrapper {
				text-align: center;
			}
			.sgpb-review-wrapper{
				text-align: center;
				padding: 20px;
			}
			.sgpb-review-wrapper p {
				color: black;
			}
			.sgpb-review-h1 {
				font-size: 22px;
				font-weight: normal;
				line-height: 1.384;
			}
			.sgrb-review-h2{
				font-size: 20px;
				font-weight: normal;
			}
			:root {
				--main-bg-color: #1ac6ff;
			}
			.sgrb-review-strong{
				color: var(--main-bg-color);
			}
			.sgrb-review-mt20{
				margin-top: 20px
			}
		</style>
		<div class="sgpb-review-wrapper">
			<div class="sgpb-review-description">
				<?php echo wp_kses($firstHeader, 'post'); ?>
				<h2 class="sgrb-review-h2"><?php esc_html_e('This is really great for your website score.', SG_POPUP_TEXT_DOMAIN); ?></h2>
				<p class="sgrb-review-mt20"><?php _e('Have your input in the development of our plugin, and we’ll provide better conversions for your site!<br /> Leave your 5-star positive review and help us go further to the perfection!', SG_POPUP_TEXT_DOMAIN); ?></p>
			</div>
			<div class="sgpb-buttons-wrapper">
				<button class="press press-grey sgpb-button-1 sgpb-close-promo-notification" data-action="sg-already-did-review"><?php esc_html_e('I already did', SG_POPUP_TEXT_DOMAIN); ?></button>
				<button class="press press-lightblue sgpb-button-3 sgpb-close-promo-notification" data-action="sg-you-worth-it"><?php esc_html_e('You worth it!', SG_POPUP_TEXT_DOMAIN); ?></button>
				<button class="press press-grey sgpb-button-2 sgpb-close-promo-notification" data-action="sg-show-popup-period" data-message-type="<?php echo esc_attr($type); ?>"><?php esc_html_e('Maybe later', SG_POPUP_TEXT_DOMAIN); ?></button></div>
			<div> </div>
		</div>
		<?php
		$popupContent = ob_get_clean();

		return $popupContent;
	}

	public static function shouldOpenReviewPopupForDays()
	{
		$shouldOpen = true;
		$dontShowAgain = get_option('SGPBCloseReviewPopup-notification');
		$periodNextTime = get_option('SGPBOpenNextTime');
		/*if (!$dontShowAgain) {
			return true;
		}
		else {
			return false;
		}*/
		// When period next time does not exits it means the user is old
		if (!$periodNextTime) {
			$usageDays = self::getPopupMainTableCreationDate();
			update_option('SGPBUsageDays', $usageDays);
			if (!defined('SGPB_REVIEW_POPUP_PERIOD')) {
				define('SGPB_REVIEW_POPUP_PERIOD', '500');
			}
			// For old users
			if (defined('SGPB_REVIEW_POPUP_PERIOD') && $usageDays > SGPB_REVIEW_POPUP_PERIOD && !$dontShowAgain) {
				return $shouldOpen;
			}
			$remainingDays = SGPB_REVIEW_POPUP_PERIOD - $usageDays;

			$popupTimeZone = \ConfigDataHelper::getDefaultTimezone();
			$timeDate = new DateTime('now', new DateTimeZone($popupTimeZone));
			$timeDate->modify('+'.$remainingDays.' day');

			$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('SGPBOpenNextTime', $timeNow);

			return false;
		}

		$currentData = new \DateTime('now');
		$timeNow = $currentData->format('Y-m-d H:i:s');
		$timeNow = strtotime($timeNow);

		if ($periodNextTime > $timeNow) {
			$shouldOpen = false;
		}

		return $shouldOpen;
	}

	public static function getPopupMainTableCreationDate()
	{
		global $wpdb;

		$query = $wpdb->prepare('SELECT table_name, create_time FROM information_schema.tables WHERE table_schema=%s AND table_name=%s', DB_NAME, $wpdb->prefix.'sgpb_subscribers');
		$results = $wpdb->get_results($query, ARRAY_A);
		if (empty($results)) {
			return 0;
		}

		$createTime = $results[0]['create_time'];
		$createTime = strtotime($createTime);
		update_option('SGPBInstallDate', $createTime);
		$diff = time() - $createTime;
		$days = floor($diff/(60*60*24));

		return $days;
	}

	public static function shouldOpenForMaxOpenPopupMessage()
	{
		$counterMaxPopup = self::getMaxOpenPopupId();

		if (empty($counterMaxPopup)) {
			return false;
		}
		$dontShowAgain = get_option('SGPBCloseReviewPopup-notification');
		$maxCountDefine = get_option('SGPBMaxOpenCount');

		if (!$maxCountDefine) {
			$maxCountDefine = SGPB_ASK_REVIEW_POPUP_COUNT;
		}

		return $counterMaxPopup['maxCount'] >= $maxCountDefine && !$dontShowAgain;
	}

	public static function getMaxOpenPopupId()
	{
		$popupsCounterData = get_option('SgpbCounter');
		if (!$popupsCounterData) {
			return 0;
		}

		$counters = array_values($popupsCounterData);
		$maxCount = max($counters);
		$popupId  = array_search($maxCount, $popupsCounterData);

		$maxPopupData = array(
			'popupId' => $popupId,
			'maxCount' => $maxCount
		);

		return $maxPopupData;
	}

	public static function getMaxOpenPopupsMessage()
	{
		$counterMaxPopup = self::getMaxOpenPopupId();
		$maxCountDefine = get_option('SGPBMaxOpenCount');
		$popupTitle = get_the_title($counterMaxPopup['popupId']);

		if (!empty($counterMaxPopup['maxCount'])) {
			$maxCountDefine = $counterMaxPopup['maxCount'];
		}

		$firstHeader = __('<h1 class="sgpb-review-h1"><strong class="sgrb-review-strong">Awesome news!</strong> <b>Popup Builder</b> plugin helped you to share your message via <strong class="sgrb-review-strong">'.$popupTitle.'</strong> popup with your visitors for <strong class="sgrb-review-strong">'.$maxCountDefine.' times!</strong></h1>', SG_POPUP_TEXT_DOMAIN);
		$popupContent = self::getMaxOpenPopupContent($firstHeader, 'count');

		return $popupContent;
	}

	/**
	 * Get email headers
	 *
	 * @since 3.1.0
	 *
	 * @param email $fromEmail
	 * @param array $args
	 *
	 * @return string $headers
	 */
	public static function getEmailHeader($fromEmail, $args = array())
	{
		$contentType = 'text/html';
		$charset = 'UTF-8';
		$blogInfo = wp_specialchars_decode( get_bloginfo() );

		if (!empty($args['contentType'])) {
			$contentType = $args['contentType'];
		}
		if (!empty($args['charset'])) {
			$charset = $args['charset'];
		}
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers  .= 'From: "'.$blogInfo.'" <'.$fromEmail.'>'."\r\n";
		$headers .= 'Content-type: '.$contentType.'; charset='.$charset.''."\r\n"; //set UTF-8

		return $headers;
	}

	/**
	 * Get file content from URL
	 *
	 * @since 3.1.0
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public static function getFileFromURL($url)
	{
		$data = '';
		$remoteData = wp_remote_get($url);

		if (is_wp_error($remoteData)) {
			return $data;
		}

		$data = wp_remote_retrieve_body($remoteData);

		return $data;
	}

	public static function getRightMetaboxBannerText()
	{
		$bannerText = get_option('sgpb-metabox-banner-remote-get');

		return $bannerText;
	}

	public static function getGutenbergPopupsIdAndTitle($excludesPopups = array())
	{
		$allPopups = SGPopup::getAllPopups();
		$popupIdTitles = array();
		$excludesPopups = apply_filters('sgpb_exclude_from_popups_list', $excludesPopups);

		if (empty($allPopups)) {
			return $popupIdTitles;
		}

		foreach ($allPopups as $popup) {
			if (empty($popup)) {
				continue;
			}

			$id = $popup->getId();
			$title = $popup->getTitle();
			$type = $popup->getType();

			if (!empty($excludesPopups)) {
				foreach ($excludesPopups as $excludesPopupId) {
					if ($excludesPopupId != $id) {
						$array = array();
						$array['id'] = $id;
						$array['title'] = $title.' - '.$type;
						$popupIdTitles[] = $array;
					}
				}
			}
			else {
				$array = array();
				$array['id'] = $id;
				$array['title'] = $title.' - '.$type;
				$popupIdTitles[] = $array;
			}
		}

		return $popupIdTitles;
	}

	public static function getGutenbergPopupsEvents()
	{
		$data =  array(
			array('value' => '', 'title' => __('Select Event', SG_POPUP_TEXT_DOMAIN)),
			array('value' => 'inherit', 'title' => __('Inherit', SG_POPUP_TEXT_DOMAIN)),
			array('value' => 'onLoad', 'title' => __('On load', SG_POPUP_TEXT_DOMAIN)),
			array('value' => 'click', 'title' => __('On click', SG_POPUP_TEXT_DOMAIN)),
			array('value' => 'hover', 'title' => __('On hover', SG_POPUP_TEXT_DOMAIN))
		);

		return $data;
	}

	public static function checkEditorByPopupId($popupId)
	{
		$popupContent = '';
		if (class_exists('\Elementor\Plugin')) {
			$elementorContent = get_post_meta($popupId, '_elementor_edit_mode', true);
			if (!empty($elementorContent) && $elementorContent == 'builder') {
				$popupContent = Elementor\Plugin::instance()->frontend->get_builder_content_for_display($popupId);
			}
		}
		else if (class_exists('Vc_Manager')) {
			$stylesAndScripts = self::renderWPBakeryScriptsAndStyles($popupId);
			$popupContent .= '<style>'.$stylesAndScripts.'</style>';
		}

		return $popupContent;
	}

	public static function renderWPBakeryScriptsAndStyles($popupId = 0)
	{
		return get_post_meta($popupId, '_wpb_shortcodes_custom_css', true);
	}

	/**
	 * countdown popup, convert date to seconds
	 *
	 * @param $dueDate
	 * @param $timezone
	 * @return false|int|string
	 */
	public static function dateToSeconds($dueDate, $timezone)
	{
		if (empty($timezone)) {
			return '';
		}

		$dateObj = self::getDateObjFromDate('now', $timezone);
		$timeNow = gettype($dateObj) == 'string' ? strtotime($dateObj) : 0;
		$dueDateTime = gettype($dueDate) == 'string' ? strtotime($dueDate) : 0;
		$seconds = $dueDateTime-$timeNow;
		if ($seconds < 0) {
			$seconds = 0;
		}

		return $seconds;
	}

	/**
	 * Get site protocol
	 *
	 * @since 1.0.0
	 *
	 * @return string $protocol
	 *
	 */
	public static function getSiteProtocol()
	{
		$protocol = 'http';

		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			$protocol = 'https';
		}

		return $protocol;
	}

	public static function findSubscribersByEmail($subscriberEmail = '', $list = 0)
	{
		global $wpdb;
		$subscriber = array();

		$prepareSql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE email = %s AND subscriptionType = %d ', $subscriberEmail, $list);
		$subscriber = $wpdb->get_row($prepareSql, ARRAY_A);
		if (!$list) {
			$prepareSql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE email = %s ', $subscriberEmail);
			$subscriber = $wpdb->get_results($prepareSql, ARRAY_A);
		}

		return $subscriber;
	}

	/**
	 * Update option
	 *
	 * @since 3.1.9
	 *
	 * @return void
	 */
	public static function updateOption($optionKey, $optionValue)
	{
		update_option($optionKey, $optionValue);
	}

	public static function getOption($optionKey, $default = false)
	{
		return get_option($optionKey, $default);
	}

	public static function deleteOption($optionKey)
	{
		delete_option($optionKey);
	}

	/**
	 * It's change popup registered plugins static paths to dynamic
	 *
	 * @since 3.1.9
	 *
	 * @return bool where true mean modified false mean there is not need modification
	 */
	public static function makeRegisteredPluginsStaticPathsToDynamic()
	{
		// remove old outdated option sgpbModifiedRegisteredPluginsPaths
		delete_option('sgpbModifiedRegisteredPluginsPaths');
		delete_option('SG_POPUP_BUILDER_REGISTERED_PLUGINS');
		$hasModifiedPaths = AdminHelper::getOption(SGPB_REGISTERED_PLUGINS_PATHS_MODIFIED);
		if ($hasModifiedPaths) {
			return false;
		}
		else {
			Installer::registerPlugin();
		}
		AdminHelper::updateOption(SGPB_REGISTERED_PLUGINS_PATHS_MODIFIED, 1);

		$registeredPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);
		if (empty($registeredPlugins)) {
			return false;
		}

		$registeredPlugins = json_decode($registeredPlugins, true);
		if (empty($registeredPlugins)) {
			return false;
		}

		foreach ($registeredPlugins as $key => $registeredPlugin) {
			if (empty($registeredPlugin['classPath'])) {
				continue;
			}
			$registeredPlugins[$key]['classPath'] = str_replace(WP_PLUGIN_DIR, '', $registeredPlugin['classPath']);
			if (!empty($registeredPlugin['options']['licence']['file'])) {
				$registeredPlugins[$key]['options']['licence']['file'] = $registeredPlugin['options']['licence']['file'];
			}
		}
		$registeredPlugins = json_encode($registeredPlugins);

		AdminHelper::updateOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS, $registeredPlugins);
		return true;
	}

	public static function hasInactiveExtensions()
	{
		$hasInactiveExtensions = false;
		$allRegiseredPBPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);
		$allRegiseredPBPlugins = !empty($allRegiseredPBPlugins) ? json_decode($allRegiseredPBPlugins, true) : array();
		if (empty($allRegiseredPBPlugins)) {
			return $hasInactiveExtensions;
		}

		foreach ($allRegiseredPBPlugins as $pluginPath => $registeredPlugin) {
			if (!isset($registeredPlugin['options']['licence']['key'])) {
				continue;
			}
			if (!isset($registeredPlugin['options']['licence']['file'])) {
				continue;
			}
			$extensionKey = $registeredPlugin['options']['licence']['file'];
			$isPluginActive = is_plugin_active($extensionKey);
			$pluginKey = $registeredPlugin['options']['licence']['key'];
			$isValidLicense = get_option('sgpb-license-status-'.$pluginKey);

			// if we even have at least one inactive extension, we don't need to check remaining extensions
			if ($isValidLicense != 'valid' && $isPluginActive) {
				$hasInactiveExtensions = true;
				break;
			}
		}

		return $hasInactiveExtensions;
	}

	public static function getSubscriptionColumnsById($id)
	{
		$popup = SGPopup::find($id);
		if (empty($popup) || !is_object($popup)) {
			return array();
		}
		$freeSavedOptions = $popup->getOptionValue('sgpb-subs-fields');

		if (!empty($freeSavedOptions)) {
			return array('firstName' => 'First name','lastName' => 'Last name', 'email' => 'Email', 'date' => 'Date');
		}
		$formFieldsJson = $popup->getOptionValue('sgpb-subscription-fields-json');
		if (!empty($formFieldsJson)) {
			$data = apply_filters('sgpbGetSubscriptionLabels', array(), $popup);
			$data['date'] = 'Date';
			return $data;
		}

		return array();
	}

	public static function getCustomFormFieldsByPopupId($popupId)
	{
		if (!class_exists('sgpbsubscriptionplus\SubscriptionPlusAdminHelper')) {
			return array();
		}

		if (method_exists('sgpbsubscriptionplus\SubscriptionPlusAdminHelper', 'getCustomFormFieldsByPopupId')) {
			return SubscriptionPlusAdminHelper::getCustomFormFieldsByPopupId($popupId);
		}

		return array();
	}

	public static function removeAllNonPrintableCharacters($title, $defaultValue)
	{
		$titleRes = $title;
		$pattern  = '/[\\\^£$%&*()}{@#~?><>,|=_+¬-]/u';
		$title = preg_replace($pattern, '', $title);
		$title = mb_ereg_replace($pattern, '', $title);
		$title = htmlspecialchars($title, ENT_IGNORE, 'UTF-8');
		$result = str_replace(' ', '', $title);
		if (empty($result)) {
			$titleRes = $defaultValue;
		}

		return $titleRes;
	}

	public static function renderCustomScripts($popupId)
	{
		$finalResult = '';
		$postMeta = get_post_meta($popupId, 'sg_popup_scripts', true);
		if (empty($postMeta)) {
			return '';
		}
		$defaultData = \ConfigDataHelper::defaultData();

		// get scripts
		if (!isset($postMeta['js'])) {
			$postMeta['js'] = array();
		}
		$jsPostMeta = $postMeta['js'];
		$jsDefaultData = $defaultData['customEditorContent']['js']['helperText'];
		$suspiciousStrings = array('document.createElement', 'createElement', 'String.fromCharCode', 'fromCharCode', '<!--', '-->');
		$finalContent = '';
		$suspiciousStringFound = false;
		if (!empty($jsPostMeta)) {
			$customScripts = '<script id="sgpb-custom-script-'.$popupId.'">';
			foreach ($jsDefaultData as $key => $value) {
				$eventName = 'sgpb'.$key;
				if ((!isset($jsPostMeta['sgpb-'.$key]) || empty($jsPostMeta['sgpb-'.$key])) || $key == 'ShouldOpen' || $key == 'ShouldClose') {
					continue;
				}
				$content = isset($jsPostMeta['sgpb-'.$key]) ? $jsPostMeta['sgpb-'.$key] : '';
				$content = str_replace('popupId', $popupId, $content);
				$content = str_replace("<", "&lt;", $content);
				$content = str_replace(">", "&gt;", $content);
				foreach ($suspiciousStrings as $string) {
					if (strpos($content, $string)) {
						$suspiciousStringFound = true;
						break;
					}
				}
				if ($suspiciousStringFound) {
					break;
				}
				$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

				$finalContent .= 'jQuery(document).ready(function(){';
				$finalContent .= 'sgAddEvent(window, "'.$eventName.'", function(e) {';
				$finalContent .= 'if (e.detail.popupId == "'.$popupId.'") {';
				$finalContent .= $content;
				$finalContent .= '};';
				$finalContent .= '});';
				$finalContent .= '});';
			}
			$customScripts .= $finalContent;
			$customScripts .= '</script>';
			if (empty($finalContent)) {
				$customScripts = '';
			}
			$finalResult .= $customScripts;
		}

		// get styles
		if (isset($postMeta['css'])) {
			$cssPostMeta = $postMeta['css'];
		}
		$finalContent = '';
		if (!empty($cssPostMeta)) {
			$customStyles = '<style id="sgpb-custom-style-'.$popupId.'">';
			$finalContent = str_replace('popupId', $popupId, $cssPostMeta);
			$finalContent = html_entity_decode($finalContent, ENT_QUOTES, 'UTF-8');

			$customStyles .= $finalContent;
			$customStyles .= '</style>';
			$finalResult .= $customStyles;
		}


		return $finalResult;
	}

	public static function removeSelectedTypeOptions($type)
	{
		switch ($type) {
			case 'cron':
				$crons = _get_cron_array();
				foreach ($crons as $key => $value) {
					foreach ($value as $key => $body) {
						if (strstr($key, 'sgpb')) {
							wp_clear_scheduled_hook($key);
						}
					}
				}
				break;
		}
	}

	public static function getSystemInfoText() {
		global $wpdb;

		$browser = self::getBrowser();

		// Get theme info
		if (get_bloginfo('version') < '3.4') {
			$themeData = wp_get_theme(get_stylesheet_directory().'/style.css');
			$theme = $themeData['Name'].' '.$themeData['Version'];
		}
		else {
			$themeData = wp_get_theme();
			$theme = $themeData->Name.' '.$themeData->Version;
		}

		// Try to identify the hosting provider
		$host = self::getHost();

		$systemInfoContent = '### Start System Info ###'."\n\n";

		// Start with the basics...
		$systemInfoContent .= '-- Site Info'."\n\n";
		$systemInfoContent .= 'Site URL:                 '.site_url()."\n";
		$systemInfoContent .= 'Home URL:                 '.home_url()."\n";
		$systemInfoContent .= 'Multisite:                '.(is_multisite() ? 'Yes' : 'No')."\n";

		// Can we determine the site's host?
		if ($host) {
			$systemInfoContent .= "\n".'-- Hosting Provider'."\n\n";
			$systemInfoContent .= 'Host:                     '.$host."\n";
		}

		// The local users' browser information, handled by the Browser class
		$systemInfoContent .= "\n".'-- User Browser'."\n\n";
		$systemInfoContent .= $browser;

		// WordPress configuration
		$systemInfoContent .= "\n".'-- WordPress Configuration'."\n\n";
		$systemInfoContent .= 'Version:                  '.get_bloginfo('version')."\n";
		$systemInfoContent .= 'Language:                 '.(defined('WPLANG') && WPLANG ? WPLANG : 'en_US')."\n";
		$systemInfoContent .= 'Permalink Structure:      '.(get_option('permalink_structure') ? get_option('permalink_structure') : 'Default')."\n";
		$systemInfoContent .= 'Active Theme:             '.$theme."\n";
		$systemInfoContent .= 'Show On Front:            '.get_option('show_on_front')."\n";

		// Only show page specs if frontpage is set to 'page'
		if (get_option('show_on_front') == 'page') {
			$frontPageId = get_option('page_on_front');
			$blogPageId  = get_option('page_for_posts');

			$systemInfoContent .= 'Page On Front:            '.($frontPageId != 0 ? get_the_title($frontPageId).' (#'.$frontPageId.')' : 'Unset')."\n";
			$systemInfoContent .= 'Page For Posts:           '.($blogPageId != 0 ? get_the_title($blogPageId).' (#'.$blogPageId.')' : 'Unset')."\n";
		}

		$systemInfoContent .= 'Table Prefix:             '.'Prefix: '.$wpdb->prefix.'  Length: '.strlen($wpdb->prefix ).'   Status: '.( strlen($wpdb->prefix) > 16 ? 'ERROR: Too long' : 'Acceptable')."\n";
		$systemInfoContent .= 'WP_DEBUG:                 '.(defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set')."\n";
		$systemInfoContent .= 'Memory Limit:             '.WP_MEMORY_LIMIT."\n";
		$systemInfoContent .= 'Registered Post Stati:    '.implode(', ', get_post_stati())."\n";

		// Must-use plugins
		$muplugins = get_mu_plugins();
		if ($muplugins && count($muplugins)) {
			$systemInfoContent .= "\n".'-- Must-Use Plugins'."\n\n";

			foreach ($muplugins as $plugin => $plugin_data) {
				$systemInfoContent .= $plugin_data['Name'].': '.$plugin_data['Version']."\n";
			}
		}

		$registered = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);
		$registered = json_decode($registered, true);

		if (empty($registered)) {
			return false;
		}
		// remove free package data, we don't need it
		array_shift($registered);

		$systemInfoContent .= "\n".'-- Popup Builder License Data'."\n\n";
		if (!empty($registered)) {
			foreach ($registered as $singleExntensionData) {
			    if (empty($singleExntensionData['options'])) {
                    continue;
			    }

				$key = $singleExntensionData['options']['licence']['key'];
				$name = $singleExntensionData['options']['licence']['itemName'];
				$licenseKey = __('No license');
				if (!empty($key)) {
					$licenseKey = self::getOption('sgpb-license-key-'.$key);
				}
				$licenseStatus = 'Inactive';
				if (self::getOption('sgpb-license-status-'.$key) == 'valid') {
					$licenseStatus = 'Active';
				}

				$systemInfoContent .= 'Name:             '.$name."\n";
				$systemInfoContent .= 'License key:      '.$licenseKey."\n";
				$systemInfoContent .= 'License status:  '.$licenseStatus."\n";
				$systemInfoContent .= "\n";
			}
		}

		$systemInfoContent .= "\n".'-- All created Popups'."\n\n";
		$allPopups = self::getPopupsIdAndTitle();
		$args['status'] = array('publish', 'draft', 'pending', 'private', 'trash');
		foreach ($allPopups as $id => $popupTitleType) {
			$popup = SGPopup::find($id, $args);
			$popupStatus = ($popup->getOptionValue('sgpb-is-active')) ? 'Enabled' : 'Disabled';
			$systemInfoContent .= 'Id:     '.$id."\n";
			$systemInfoContent .= 'Title:  '.get_the_title($id)."\n";
			$systemInfoContent .= 'Type:   '.$popup->getOptionValue('sgpb-type')."\n";
			$systemInfoContent .= 'Status: '.$popupStatus."\n";
			$systemInfoContent .= "\n";
		}

		// WordPress active plugins
		$systemInfoContent .= "\n".'-- WordPress Active Plugins'."\n\n";

		$plugins        = get_plugins();
		$activePlugins = get_option('active_plugins', array());
		foreach ($plugins as $pluginPath => $plugin) {

			if (! in_array($pluginPath, $activePlugins)) {
				continue;
			}

			$systemInfoContent .= $plugin['Name'].': '.$plugin['Version']."\n";
		}
		// WordPress inactive plugins
		$systemInfoContent .= "\n".'-- WordPress Inactive Plugins'."\n\n";

		foreach ($plugins as $pluginPath => $plugin) {
			if (in_array($pluginPath, $activePlugins)) {
				continue;
			}
			$systemInfoContent .= $plugin['Name'].': '.$plugin['Version']."\n";
		}

		if (is_multisite()) {
			// WordPress Multisite active plugins
			$systemInfoContent .= "\n".'-- Network Active Plugins'."\n\n";

			$plugins        = wp_get_active_network_plugins();
			$activePlugins = get_site_option('active_sitewide_plugins', array());

			foreach ($plugins as $pluginPath) {
				$plugin_base = plugin_basename($pluginPath);

				if (! array_key_exists($plugin_base, $activePlugins)) {
					continue;
				}

				$plugin = get_plugin_data($pluginPath);
				$systemInfoContent .= $plugin['Name'].': '.$plugin['Version']."\n";
			}
		}

		// Server configuration (really just versioning)
		$systemInfoContent .= "\n".'-- Webserver Configuration'."\n\n";
		$systemInfoContent .= 'PHP Version:              '.PHP_VERSION."\n";
		$systemInfoContent .= 'MySQL Version:            '.$wpdb->db_version()."\n";
		$systemInfoContent .= 'Webserver Info:           '.isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:''."\n";

		// PHP configs... now we're getting to the important stuff
		$systemInfoContent .= "\n".'-- PHP Configuration'."\n\n";
		$systemInfoContent .= 'Memory Limit:             '.ini_get('memory_limit')."\n";
		$systemInfoContent .= 'Upload Max Size:          '.ini_get('upload_max_filesize')."\n";
		$systemInfoContent .= 'Post Max Size:            '.ini_get('post_max_size')."\n";
		$systemInfoContent .= 'Upload Max Filesize:      '.ini_get('upload_max_filesize')."\n";
		$systemInfoContent .= 'Time Limit:               '.ini_get('max_execution_time')."\n";
		$systemInfoContent .= 'Max Input Vars:           '.ini_get('max_input_vars')."\n";
		$systemInfoContent .= 'Display Errors:           '.(ini_get('display_errors') ? 'On ('.ini_get('display_errors').')' : 'N/A')."\n";

		// PHP extensions and such
		$systemInfoContent .= "\n".'-- PHP Extensions'."\n\n";
		$systemInfoContent .= 'cURL:                     '.(function_exists('curl_init') ? 'Supported' : 'Not Supported')."\n";
		$systemInfoContent .= 'fsockopen:                '.(function_exists('fsockopen') ? 'Supported' : 'Not Supported')."\n";
		$systemInfoContent .= 'SOAP Client:              '.(class_exists('SoapClient') ? 'Installed' : 'Not Installed')."\n";
		$systemInfoContent .= 'Suhosin:                  '.(extension_loaded('suhosin') ? 'Installed' : 'Not Installed')."\n";

		// Session stuff
		$systemInfoContent .= "\n".'-- Session Configuration'."\n\n";
		$systemInfoContent .= 'Session:                  '.(isset($_SESSION ) ? 'Enabled' : 'Disabled')."\n";

		// The rest of this is only relevant is session is enabled
		if (isset($_SESSION)) {
			$systemInfoContent .= 'Session Name:             '.esc_html( ini_get('session.name'))."\n";
			$systemInfoContent .= 'Cookie Path:              '.esc_html( ini_get('session.cookie_path'))."\n";
			$systemInfoContent .= 'Save Path:                '.esc_html( ini_get('session.save_path'))."\n";
			$systemInfoContent .= 'Use Cookies:              '.(ini_get('session.use_cookies') ? 'On' : 'Off')."\n";
			$systemInfoContent .= 'Use Only Cookies:         '.(ini_get('session.use_only_cookies') ? 'On' : 'Off')."\n";
		}

		$systemInfoContent = apply_filters('sgpbSystemInformation', $systemInfoContent);

		$systemInfoContent .= "\n".'### End System Info ###';

		return $systemInfoContent;
	}

	public static function getHost()
	{
		if (defined('WPE_APIKEY')) {
			return 'WP Engine';
		}
		else if (defined('PAGELYBIN')) {
			return 'Pagely';
		}
		else if (DB_HOST == 'localhost:/tmp/mysql5.sock') {
			return 'ICDSoft';
		}
		else if (DB_HOST == 'mysqlv5') {
			return 'NetworkSolutions';
		}
		else if (strpos(DB_HOST, 'ipagemysql.com') !== false) {
			return 'iPage';
		}
		else if (strpos(DB_HOST, 'ipowermysql.com') !== false) {
			return 'IPower';
		}
		else if (strpos(DB_HOST, '.gridserver.com') !== false) {
			return 'MediaTemple Grid';
		}
		else if (strpos(DB_HOST, '.pair.com') !== false) {
			return 'pair Networks';
		}
		else if (strpos(DB_HOST, '.stabletransit.com') !== false) {
			return 'Rackspace Cloud';
		}
		else if (strpos(DB_HOST, '.sysfix.eu') !== false) {
			return 'SysFix.eu Power Hosting';
		}
		else if (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'Flywheel') !== false) {
			return 'Flywheel';
		}
		else {
			// Adding a general fallback for data gathering
			return 'DBH: '.DB_HOST.', SRV: '.(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
		}
	}

	public static function getBrowser()
	{
		$uAgent = 'Unknown';
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$uAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		$bname = $platform = $ub = $version = 'Unknown';
		$browserInfoContent = '';

		//First get the platform?
		if (preg_match('/linux/i', $uAgent)) {
			$platform = 'Linux';
		}
		else if (preg_match('/macintosh|mac os x/i', $uAgent)) {
			$platform = 'Apple';
		}
		else if (preg_match('/windows|win32/i', $uAgent)) {
			$platform = 'Windows';
		}

		if (preg_match('/MSIE/i',$uAgent) && !preg_match('/Opera/i',$uAgent)) {
			$bname = 'Internet Explorer';
			$ub = 'MSIE';
		}
		else if (preg_match('/Firefox/i',$uAgent)) {
			$bname = 'Mozilla Firefox';
			$ub = 'Firefox';
		}
		else if (preg_match('/OPR/i',$uAgent)) {
			$bname = 'Opera';
			$ub = 'Opera';
		}
		else if (preg_match('/Chrome/i',$uAgent) && !preg_match('/Edge/i',$uAgent)) {
			$bname = 'Google Chrome';
			$ub = 'Chrome';
		}
		else if (preg_match('/Safari/i',$uAgent) && !preg_match('/Edge/i',$uAgent)) {
			$bname = 'Apple Safari';
			$ub = 'Safari';
		}
		else if (preg_match('/Netscape/i',$uAgent)) {
			$bname = 'Netscape';
			$ub = 'Netscape';
		}
		else if (preg_match('/Edge/i',$uAgent)) {
			$bname = 'Edge';
			$ub = 'Edge';
		}
		else if (preg_match('/Trident/i',$uAgent)) {
			$bname = 'Internet Explorer';
			$ub = 'MSIE';
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>'.implode('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		$matches = array();
		preg_match_all($pattern, $uAgent, $matches);

		// see how many we have
		$i = count($matches['browser']);
		//we will have two since we are not using 'other' argument yet
		if ($i != 1) {
			//see if version is before or after the name
			if (strripos($uAgent,"Version") < strripos($uAgent,$ub)) {
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		  // check if we have a number
		if ($version == null || $version == "") {$version = "?" ;}

		$browserInfoContent .= 'Platform:           '.$platform."\n";
		$browserInfoContent .= 'Browser Name:       '.$bname."\n";
		$browserInfoContent .= 'Browser Version:    '.$version."\n";
		$browserInfoContent .= 'User Agent:         '.$uAgent."\n";

		return $browserInfoContent;
	}

	// checking user roles capability to do actions
	public static function userCanAccessTo()
	{
		// if this is not admin side screen we don't need to check roles and capabilities
		if (!is_admin()) {
			return true;
		}

		$allow = false;

		$savedUserRolesInPopup = self::getPopupPostAllowedUserRoles();
		$currentUserRole = self::getCurrentUserRole();

		// we need to check if there are any intersections between saved user roles and current user
		$hasIntersection = array_intersect($currentUserRole, $savedUserRolesInPopup);
		if (!empty($hasIntersection)) {
			$allow = true;
		}

		return $allow;
	}

	public static function filterUserCapabilitiesForTheUserRoles($hook = 'save')
	{
		global $wp_roles;

		$allAvailableWpRoles = $wp_roles->roles;
		$savedUserRoles = get_option('sgpb-user-roles');
		// we need to remove from all roles, either when deactivating the plugin and when there is no saved roles
		if (empty($savedUserRoles) || $hook == 'deactivate') {
			$savedUserRoles = array();
		}
		$rolesToBeRestricted = array();
		// selected user roles, which have access to the PB
		foreach ($allAvailableWpRoles as $allAvailableWpRole) {
			if (isset($allAvailableWpRole['name']) && in_array(lcfirst($allAvailableWpRole['name']), $savedUserRoles)) {
				$indexToUnset = lcfirst($allAvailableWpRole['name']);
				continue;
			}
			$rolesToBeRestricted[] = lcfirst($allAvailableWpRole['name']);
		}

		$caps = array(
			'read_private_sgpb_popups',
			'edit_sgpb_popup',
			'edit_sgpb_popups',
			'edit_others_sgpb_popups',
			'edit_published_sgpb_popups',
			'publish_sgpb_popups',
			'delete_sgpb_popups',
			'delete_published_posts',
			'delete_others_sgpb_popups',
			'delete_private_sgpb_popups',
			'delete_private_sgpb_popup',
			'delete_published_sgpb_popups',
			'sgpb_manage_options',
			'manage_popup_terms',
			'manage_popup_categories_terms'
		);

		if ($hook == 'activate') {
			$rolesToBeRestricted = $savedUserRoles;
		}
		foreach ($rolesToBeRestricted as $roleToBeRestricted) {
			if ($roleToBeRestricted == 'administrator' || $roleToBeRestricted == 'admin') {
				continue;
			}
			foreach ($caps as $cap) {
				// only for the activation hook we need to add our capabilities back
				if ($hook == 'activate') {
					$wp_roles->add_cap($roleToBeRestricted, $cap);
				}
				else {
					$wp_roles->remove_cap($roleToBeRestricted, $cap);
				}
			}
		}
	}

	public static function removeUnnecessaryCodeFromPopups()
	{
		$alreadyClearded = self::getOption('sgpb-unnecessary-scripts-removed-1');
		if ($alreadyClearded) {
			return true;
		}

		global $wpdb;
		$getAllDataSql = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'posts WHERE post_type = %s', SG_POPUP_POST_TYPE);
		$popupsId = $wpdb->get_results($getAllDataSql, ARRAY_A);
		if (empty($popupsId)) {
			return true;
		}
		foreach ($popupsId as $popupId) {
			if (empty($popupId['id'])) {
				continue;
			}
			$id = $popupId['id'];
			$customScripts = get_post_meta($id, 'sg_popup_scripts', true);
			if (empty($customScripts)) {
				continue;
			}
			if (isset($customScripts['js'])) {
				unset($customScripts['js']);
				update_post_meta($id, 'sg_popup_scripts', $customScripts);
			}
		}

		self::updateOption('sgpb-unnecessary-scripts-removed-1', 1);
	}

	public static function sendTestNewsletter($newsletterData = array())
	{
		$mailSubject = $newsletterData['newsletterSubject'];
		$fromEmail = $newsletterData['fromEmail'];
		$emailMessage = $newsletterData['messageBody'];
		$blogInfo = wp_specialchars_decode( get_option( 'blogname' ) );
		$headers = array(
			'From: "'.$blogInfo.'" <'.$fromEmail.'>' ,
			'MIME-Version: 1.0' ,
			'Content-type: text/html; charset=UTF-8'
		);

		$emails = get_option('admin_email');
		if (!empty($newsletterData['testSendingEmails'])) {
			$emails = $newsletterData['testSendingEmails'];
			$emails = str_replace(' ', '', $emails);

			$receiverEmailsArray = array();
			$emails = explode(',', $emails);
			foreach ($emails as $mail) {
				if (is_email($mail)) {
					$receiverEmailsArray[] = $mail;
				}
			}
			$emails = $receiverEmailsArray;
		}

		$newsletterOptions = get_option('SGPB_NEWSLETTER_DATA');
		$allAvailableShortcodes = array();
		$allAvailableShortcodes['patternBlogName'] = '/\[Blog name]/';
		$allAvailableShortcodes['patternUserName'] = '/\[User name]/';
		$allAvailableShortcodes['patternUnsubscribe'] = '';

		$pattern = "/\[(\[?)(Unsubscribe)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]\*+(?:\[(?!\/\2\])[^\[]\*+)\*+)\[\/\2\])?)(\]?)/";
		preg_match($pattern, $emailMessage, $matches);
		$title = __('Unsubscribe', SG_POPUP_TEXT_DOMAIN);
		if ($matches) {
			$patternUnsubscribe = $matches[0];
			// If user didn't change anything inside the [unsubscribe] shortcode $matches[2] will be equal to 'Unsubscribe'
			if ($matches[2] == 'Unsubscribe') {
				$pattern = '/\s(\w+?)="(.+?)"]/';
				preg_match($pattern, $matches[0], $matchesTitle);
				if (!empty($matchesTitle[2])) {
					$title = AdminHelper::removeAllNonPrintableCharacters($matchesTitle[2], 'Unsubscribe');
				}
			}
			$allAvailableShortcodes['patternUnsubscribe'] = $patternUnsubscribe;
		}

		$emailMessageCustom = preg_replace($allAvailableShortcodes['patternBlogName'], $newsletterOptions['blogname'], $emailMessage);
		$emailMessageCustom = preg_replace($allAvailableShortcodes['patternUserName'], $newsletterOptions['username'], $emailMessageCustom);
		$emailMessageCustom = str_replace($allAvailableShortcodes['patternUnsubscribe'], '', $emailMessageCustom);

		$mailStatus = wp_mail($emails, $mailSubject, $emailMessageCustom, $headers);

		wp_die(esc_html($newsletterData['testSendingStatus']));
	}

	// wp uploaded images
	public static function getImageAltTextByUrl($imageUrl = '')
	{
		$imageId = attachment_url_to_postid($imageUrl);
		$altText = get_post_meta($imageId, '_wp_attachment_image_alt', true);

		return $altText;
	}

	public static function hasBlocks($content)
	{
		if (function_exists('has_blocks')) {
			return has_blocks($content);
		}

		return false !== strpos( (string) $content, '<!-- wp:' );
	}
	/**
	 * Retrieve duplicate post link for post.
	 *
	 * @param int $id Optional. Post ID.
	 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
	 * @return string
	 */
	public static function popupGetClonePostLink($id = 0, $context = 'display')
	{
		if (!$post = get_post($id)) {
			return;
		}
		$actionName = "popupSaveAsNew";

		if ('display' == $context) {
			$action = '?action='.$actionName.'&amp;post='.$post->ID;
		} else {
			$action = '?action='.$actionName.'&post='.$post->ID;
		}

		$postTypeObject = get_post_type_object($post->post_type);

		if (!$postTypeObject) {
			return;
		}

		return wp_nonce_url(apply_filters('popupGetClonePostLink', admin_url("admin.php".$action), $post->ID, $context), 'duplicate-post_' . $post->ID);
	}
	private static function checkIfLicenseIsActive($license, $itemId, $key) {
		$transient = 'sgpb-license-key-'.$key.'-requested';
		if ( false !== ( $value = get_transient( $transient ) ) ) {
			return;
		}
		$params = array(
			'woo_sl_action'     => 'status-check',
			'licence_key'       => $license,
			'product_unique_id' => $itemId,
			'domain'            => home_url()
		);
		$requestUri = SGPB_REQUEST_URL.'?'.http_build_query($params);
		$response = wp_remote_get($requestUri);
		if (!is_wp_error($response) || 200 == wp_remote_retrieve_response_code($response)) {
			$licenseData = json_decode(wp_remote_retrieve_body($response));
			$status = (isset($licenseData[0]->licence_status) && $licenseData[0]->licence_status === 'active') ? 'valid' : $licenseData[0]->licence_status;
			update_option('sgpb-license-status-'.$key, $status);
			set_transient($transient, $licenseData[0]->status_code, WEEK_IN_SECONDS);
		}
	}

	public static function updatesInit()
	{
		if (!class_exists('sgpb\WOOSL_CodeAutoUpdate')) {
			// load our custom updater if it doesn't already exist
			require_once(SG_POPUP_LIBS_PATH .'WOOSL_CodeAutoUpdate.php');
		}
		$licenses = (new License())->getLicenses();

		foreach ($licenses as $license) {
			$key = isset($license['key']) ?$license['key'] : '';
			$itemId = isset($license['itemId']) ? $license['itemId'] : '';
			$filePath = isset($license['file']) ? $license['file'] : '';
			$pluginMainFilePath = strpos($filePath, SG_POPUP_PLUGIN_PATH) !== 0 ? SG_POPUP_PLUGIN_PATH.$filePath : $filePath;

			$licenseKey = trim(get_option('sgpb-license-key-'.$key));
			$status = get_option('sgpb-license-status-'.$key);

			if ($status == false || $status != 'valid') {
				continue;
			}
			self::checkIfLicenseIsActive($licenseKey, $itemId, $key);
			switch($key) {
				case 'POPUP_SOCIAL':
					if (defined('SGPB_SOCIAL_POPUP_VERSION')) {
						$version = defined('SGPB_SOCIAL_POPUP_VERSION') ? constant('SGPB_SOCIAL_POPUP_VERSION') : '';
					} else {
						$version = defined('SG_VERSION_'.$key) ? constant('SG_VERSION_'.$key) : '';
					}
					break;
				case 'POPUP_AGE_VERIFICATION':
					if (defined('SGPB_AGE_VERIFICATION_POPUP_VERSION')) {
						$version = defined('SGPB_AGE_VERIFICATION_POPUP_VERSION') ? constant('SGPB_AGE_VERIFICATION_POPUP_VERSION') : '';
					} else{
						$version = defined('SG_VERSION_'.$key) ? constant('SG_VERSION_'.$key) : '';
					}
					break;
				case 'POPUP_GAMIFICATION':
					if (defined('POPUP_GAMIFICATION')) {
						$version = defined('POPUP_GAMIFICATION') ? constant('POPUP_GAMIFICATION') : '';
					} else {
						$version = defined('SG_VERSION_'.$key) ? constant('SG_VERSION_'.$key) : '';
					}
					break;
				default :
					$version = defined('SG_VERSION_'.$key) ? constant('SG_VERSION_'.$key) : '';
					break;
			}
			// If the version of the extension is not found, update will not possibly be shown
			if(empty($version)) {
				continue;
			}
			$sgpbUpdater = new WOOSL_CodeAutoUpdate(
				SGPB_REQUEST_URL,
				$pluginMainFilePath,
				$itemId,
				$licenseKey,
				$version
			);
		}
	}

	public static function allowed_html_tags($allowScript = true)
	{
		$allowedPostTags = array();
		$allowedPostTags = wp_kses_allowed_html('post');
		$allowed_atts = array(
			'role'             => array(),
			'checked'          => array(),
			'align'            => array(),
			'preload'          => array(),
			'aria-live'        => array(),
			'aria-label'       => array(),
			'aria-disabled'    => array(),
			'aria-atomic'      => array(),
			'aria-required'    => array(),
			'aria-invalid'     => array(),
			'aria-hidden'      => array(),
			'aria-valuenow'    => array(),
			'aria-valuemin'    => array(),
			'aria-haspopup'    => array(),
			'aria-expanded'    => array(),
			'aria-valuemax'    => array(),
			'aria-labelledby'  => array(),
			'aria-checked'     => array(),
			'aria-describedby' => array(),
			'aria-valuetext'   => array(),
			'placeholder'      => array(),
			'controls'         => array(),
			'allowfullscreen'  => array(),
			'class'            => array(),
			'type'             => array(),
			'id'               => array(),
			'dir'              => array(),
			'size'             => array(),
			'cols'             => array(),
			'rows'             => array(),
			'lang'             => array(),
			'muted'            => array(),
			'style'            => array(),
			'xml:lang'         => array(),
			'src'              => array(),
			'autocomplete'     => array(),
			'maxlength'        => array(),
			'pattern'          => array(),
			'alt'              => array(),
			'href'             => array(),
			'rel'              => array(),
			'rev'              => array(),
			'target'           => array(),
			'novalidate'       => array(),
			'value'            => array(),
			'name'             => array(),
			'tabindex'         => array(),
			'action'           => array(),
			'method'           => array(),
			'for'              => array(),
			'width'            => array(),
			'height'           => array(),
			'data-*'           => true,
			'title'            => array(),
			'enctype'          => array(),
			'attr'             => array(),
			'label'            => array(),
			'selected'         => array(),
			'multiple'         => array()
		);
		if ($allowScript){
			$allowedPostTags['script'] = $allowed_atts;
			$allowed_atts['onclick'] = array();
		}
		$allowedPostTags['select'] = $allowed_atts;
		$allowedPostTags['optgroup'] = $allowed_atts;
		$allowedPostTags['option'] = $allowed_atts;
		$allowedPostTags['form'] = $allowed_atts;
		$allowedPostTags['fieldset'] = $allowed_atts;
		$allowedPostTags['legend'] = $allowed_atts;
		$allowedPostTags['label'] = $allowed_atts;
		$allowedPostTags['input'] = $allowed_atts;
		$allowedPostTags['video'] = $allowed_atts;
		$allowedPostTags['source'] = $allowed_atts;
		$allowedPostTags['textarea'] = $allowed_atts;
		$allowedPostTags['iframe'] = $allowed_atts;

		$allowedPostTags['style'] = $allowed_atts;
		$allowedPostTags['strong'] = $allowed_atts;
		$allowedPostTags['small'] = $allowed_atts;
		$allowedPostTags['table'] = $allowed_atts;
		$allowedPostTags['span'] = $allowed_atts;
		$allowedPostTags['abbr'] = $allowed_atts;
		$allowedPostTags['code'] = $allowed_atts;
		$allowedPostTags['pre'] = $allowed_atts;
		$allowedPostTags['div'] = $allowed_atts;
		$allowedPostTags['img'] = $allowed_atts;
		$allowedPostTags['h1'] = $allowed_atts;
		$allowedPostTags['h2'] = $allowed_atts;
		$allowedPostTags['h3'] = $allowed_atts;
		$allowedPostTags['h4'] = $allowed_atts;
		$allowedPostTags['h5'] = $allowed_atts;
		$allowedPostTags['h6'] = $allowed_atts;
		$allowedPostTags['ol'] = $allowed_atts;
		$allowedPostTags['ul'] = $allowed_atts;
		$allowedPostTags['li'] = $allowed_atts;
		$allowedPostTags['em'] = $allowed_atts;
		$allowedPostTags['hr'] = $allowed_atts;
		$allowedPostTags['br'] = $allowed_atts;
		$allowedPostTags['tr'] = $allowed_atts;
		$allowedPostTags['td'] = $allowed_atts;
		$allowedPostTags['p'] = $allowed_atts;
		$allowedPostTags['a'] = $allowed_atts;
		$allowedPostTags['b'] = $allowed_atts;
		$allowedPostTags['i'] = $allowed_atts;
		add_filter('safe_style_css', function($styles){
			$styles[] = 'position';
			$styles[] = 'opacity';
			$styles[] = 'inset';
			$styles[] = 'margin';
			$styles[] = 'display';
			$styles[] = 'z-index';
			$styles[] = 'top';
			$styles[] = 'left';
			$styles[] = 'bottom';
			$styles[] = 'right';

			return $styles;
		}, 10, 1);

		return $allowedPostTags;
	}
}
