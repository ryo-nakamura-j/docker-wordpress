<?php
namespace sgpb;
require_once(dirname(__FILE__).'/SGPopup.php');
require_once(ABSPATH.'wp-admin/includes/plugin.php');

class SubscriptionPopup extends SGPopup
{
	private $data;
	private $formContent = '';

	public function __construct()
	{
		add_filter('sgpbPopupRenderOptions', array($this, 'renderOptions'), 12, 1);
		add_filter('sgpbAdminJsFiles', array($this, 'adminJsFilter'), 1, 1);
		add_filter('sgpbAdminCssFiles', array($this, 'adminCssFilter'), 1, 1);
		add_filter('sgpbSubscriptionForm', array($this, 'subscriptionForm'), 1, 1);
	}

	private function frontendFilters()
	{
		$isSubscriptionPlusActive = is_plugin_active(SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY);

		if (!$isSubscriptionPlusActive) {
			add_filter('sgpbFrontendJsFiles', array($this, 'frontJsFilter'), 1, 1);
		}
		add_filter('sgpbFrontendCssFiles', array($this, 'frontCssFilter'), 1, 1);
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setFormContent($formContent)
	{
		$this->formContent = $formContent;
	}

	public function getFormContent()
	{
		return $this->formContent;
	}

	public static function getTablesSql()
	{
		$tablesSql = array();
		$dbEngine = Functions::getDatabaseEngine();

		$tablesSql[] = SGPB_SUBSCRIBERS_TABLE_NAME.' (
					`id` int(12) NOT NULL AUTO_INCREMENT,
					`firstName` varchar(255),
					`lastName` varchar(255),
					`email` varchar(255),
					`subscriptionType` int(12),
					`cDate` date,
					`status` varchar(255),
					`unsubscribed` int(11) default 0,
					PRIMARY KEY (id)
			) ENGINE='.$dbEngine.' DEFAULT CHARSET=utf8;';

		$tablesSql[] = SGPB_SUBSCRIBERS_ERROR_TABLE_NAME.' (
					`id` int(12) NOT NULL AUTO_INCREMENT,
					`firstName` varchar(255),
					`popupType` varchar(255),
					`email` varchar(255),
					`date` varchar(255),
					PRIMARY KEY (id)
			) ENGINE='.$dbEngine.' DEFAULT CHARSET=utf8;';

		return $tablesSql;
	}

	/**
	 * Return Subscription popup type need all table names
	 *
	 * @since 1.0.0
	 *
	 * @return array $table names
	 *
	 */
	public static function getTableNames()
	{
		$tableNames = array(
			SGPB_SUBSCRIBERS_TABLE_NAME,
			SGPB_SUBSCRIBERS_ERROR_TABLE_NAME
		);

		return $tableNames;
	}

	public function frontJsFilter($jsFiles)
	{
		$jsFiles[] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'Subscription.js');
		$jsFiles[] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'Validate.js');

		return $jsFiles;
	}

	public function adminJsFilter($jsFiles)
	{
		$jsFiles[] = array('folderUrl' => SG_POPUP_JS_URL, 'filename' => 'Subscription.js');

		return $jsFiles;
	}

	public function adminCssFilter($cssFiles)
	{
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'ResetFormStyle.css'
		);
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'SubscriptionForm.css'
		);

		return $cssFiles;
	}

	public function frontCssFilter($cssFiles)
	{
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'ResetFormStyle.css',
			'inFooter' => true
		);
		$cssFiles[] = array(
			'folderUrl' => SG_POPUP_CSS_URL,
			'filename' => 'SubscriptionForm.css',
			'inFooter' => true
		);

		return $cssFiles;
	}

	public function addAdditionalSettings($postData = array(), $obj = null)
	{
		$this->setData($postData);
		$this->setPostData($postData);
		$isSubscriptionPlusActive = is_plugin_active(SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY);

		if (!$isSubscriptionPlusActive) {
			$postData['sgpb-subs-fields'] = $this->createFormFieldsData();
		}

		return $postData;
	}

	public function setSubsFormData($formId)
	{
		$savedData = array();

		if (!empty($formId)) {
			$savedData = SGPopup::getSavedData($formId);
		}

		$this->setData($savedData);
	}

	private function getFieldValue($optionName)
	{
		$optionValue = '';
		$postData = $this->getPostData();

		if (!empty($postData[$optionName])) {
			return $postData[$optionName];
		}

		$defaultData = $this->getDefaultDataByName($optionName);

		// when Saved data does not exist we try find inside default values
		if (empty($postData) && !empty($defaultData)) {
			return $defaultData['defaultValue'];
		}

		return $optionValue;
	}

	/**
	 * Create form fields data
	 *
	 * @since 1.0.0
	 *
	 * @return array $formData
	 */
	public function createFormFieldsData()
	{
		$formData = array();
		$inputStyles = array();
		$submitStyles = array();
		$emailPlaceholder = $this->getFieldValue('sgpb-subs-email-placeholder');
		if ($this->getFieldValue('sgpb-subs-text-width'))  {
			$inputWidth = $this->getFieldValue('sgpb-subs-text-width');
			$inputStyles['width'] = AdminHelper::getCSSSafeSize($inputWidth);
		}
		if ($this->getFieldValue('sgpb-subs-text-height')) {
			$inputHeight = $this->getFieldValue('sgpb-subs-text-height');
			$inputStyles['height'] = AdminHelper::getCSSSafeSize($inputHeight);
		}
		if ($this->getFieldValue('sgpb-subs-text-border-width')) {
			$inputBorderWidth = $this->getFieldValue('sgpb-subs-text-border-width');
			$inputStyles['border-width'] = AdminHelper::getCSSSafeSize($inputBorderWidth);
		}
		if ($this->getFieldValue('sgpb-subs-text-border-color')) {
			$inputStyles['border-color'] = $this->getFieldValue('sgpb-subs-text-border-color');
		}
		if ($this->getFieldValue('sgpb-subs-text-bg-color')) {
			$inputStyles['background-color'] = $this->getFieldValue('sgpb-subs-text-bg-color');
		}
		if ($this->getFieldValue('sgpb-subs-text-color')) {
			$inputStyles['color'] = $this->getFieldValue('sgpb-subs-text-color');
		}
		$inputStyles['autocomplete'] = 'off';

		if ($this->getFieldValue('sgpb-subs-btn-width')) {
			$submitWidth = $this->getFieldValue('sgpb-subs-btn-width');
			$submitStyles['width'] = AdminHelper::getCSSSafeSize($submitWidth);
		}
		if ($this->getFieldValue('sgpb-subs-btn-height')) {
			$submitHeight = $this->getFieldValue('sgpb-subs-btn-height');
			$submitStyles['height'] = AdminHelper::getCSSSafeSize($submitHeight);
		}
		if ($this->getFieldValue('sgpb-subs-btn-bg-color')) {
			$submitStyles['background-color'] = $this->getFieldValue('sgpb-subs-btn-bg-color').' !important';
		}
		if ($this->getFieldValue('sgpb-subs-btn-text-color')) {
			$submitStyles['color'] = $this->getFieldValue('sgpb-subs-btn-text-color');
		}
		if ($this->getFieldValue('sgpb-subs-btn-border-radius')) {
			$submitStyles['border-radius'] = AdminHelper::getCSSSafeSize($this->getFieldValue('sgpb-subs-btn-border-radius')).' !important';
		}
		if ($this->getFieldValue('sgpb-subs-btn-border-width')) {
			$submitStyles['border-width'] = AdminHelper::getCSSSafeSize($this->getFieldValue('sgpb-subs-btn-border-width')).' !important';
		}
		if ($this->getFieldValue('sgpb-subs-btn-border-color')) {
			$submitStyles['border-color'] = $this->getFieldValue('sgpb-subs-btn-border-color').' !important';
		}
		$submitStyles['text-transform'] = 'none !important';
		$submitStyles['border-style'] = 'solid';

		$formData['email'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'email',
				'data-required' => true,
				'name' => 'sgpb-subs-email',
				'placeholder' => $emailPlaceholder,
				'class' => 'js-subs-text-inputs js-subs-email-input',
				'data-error-message-class' => 'sgpb-subs-email-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => isset($inputStyles["width"]) ?$inputStyles["width"] : ''
		);

		$firstNamePlaceholder = $this->getFieldValue('sgpb-subs-first-placeholder');
		$firstNameRequired = $this->getOptionValueFromSavedData('sgpb-subs-first-name-required');
		$firstNameRequired = (!empty($firstNameRequired)) ? true : false;
		$isShow = ($this->getFieldValue('sgpb-subs-first-name-status')) ? true : false;

		$formData['first-name'] = array(
			'isShow' => $isShow,
			'attrs' => array(
				'type' => 'text',
				'data-required' => $firstNameRequired,
				'name' => 'sgpb-subs-first-name',
				'placeholder' => $firstNamePlaceholder,
				'class' => 'js-subs-text-inputs js-subs-first-name-input',
				'data-error-message-class' => 'sgpb-subs-first-name-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => isset($inputStyles['width']) ? $inputStyles['width'] : ''
		);

		$lastNamePlaceholder = $this->getFieldValue('sgpb-subs-last-placeholder');
		$lastNameRequired = $this->getOptionValueFromSavedData('sgpb-subs-last-name-required');
		$lastNameRequired = (!empty($lastNameRequired)) ? true : false;
		$isShow = ($this->getFieldValue('sgpb-subs-last-name-status')) ? true : false;

		$formData['last-name'] = array(
			'isShow' => $isShow,
			'attrs' => array(
				'type' => 'text',
				'data-required' => $lastNameRequired,
				'name' => 'sgpb-subs-last-name',
				'placeholder' => $lastNamePlaceholder,
				'class' => 'js-subs-text-inputs js-subs-last-name-input',
				'data-error-message-class' => 'sgpb-subs-last-name-error-message'
			),
			'style' => $inputStyles,
			'errorMessageBoxStyles' => isset($inputStyles['width']) ? $inputStyles['width'] : ''
		);

		/* GDPR checkbox */
		$gdprLabel = $this->getOptionValueFromSavedData('sgpb-subs-gdpr-label');
		$gdprRequired = ($this->getOptionValueFromSavedData('sgpb-subs-gdpr-status')) ? true : false;
		$isShow = ($this->getOptionValueFromSavedData('sgpb-subs-gdpr-status')) ? true : false;

		$formData['gdpr'] = array(
			'isShow' => $isShow,
			'attrs' => array(
				'type' => 'customCheckbox',
				'data-required' => $gdprRequired,
				'name' => 'sgpb-subs-gdpr',
				'class' => 'js-subs-gdpr-inputs js-subs-gdpr-label',
				'id' => 'sgpb-gdpr-field-label',
				'data-error-message-class' => 'sgpb-gdpr-error-message'
			),
			'style' => array('width' => isset($inputWidth)? $inputWidth :''),
			'label' => $gdprLabel,
			'text' => wp_kses($this->getFieldValue('sgpb-subs-gdpr-text'), AdminHelper::allowed_html_tags(false)),
			'errorMessageBoxStyles' => isset($inputStyles['width'])?$inputStyles['width']:''
		);
		/* GDPR checkbox */

		$hiddenChecker['position'] = 'absolute';
		// For protected bots and spams
		$hiddenChecker['left'] = '-5000px';
		$hiddenChecker['padding'] = '0';
		$formData['hidden-checker'] = array(
			'isShow' => false,
			'attrs' => array(
				'type' => 'hidden',
				'data-required' => false,
				'name' => 'sgpb-subs-hidden-checker',
				'value' => '',
				'class' => 'js-subs-text-inputs js-subs-last-name-input'
			),
			'style' => $hiddenChecker
		);

		$submitTitle = $this->getFieldValue('sgpb-subs-btn-title');
		$progressTitle = $this->getFieldValue('sgpb-subs-btn-progress-title');
		$formData['submit'] = array(
			'isShow' => true,
			'attrs' => array(
				'type' => 'submit',
				'name' => 'sgpb-subs-submit',
				'value' => $submitTitle,
				'data-title' => $submitTitle,
				'data-progress-title' => $progressTitle,
				'class' => 'js-subs-submit-btn'
			),
			'style' => $submitStyles
		);

		return $formData;
	}

	/**
	 * Create validation obj for jQuery validate
	 *
	 * @since 1.0.0
	 *
	 * @param array $subsFields
	 * @param array $validationMessages
	 *
	 * @return string
	 */
	private function createValidateObj($subsFields, $validationMessages)
	{
		$validateObj = '';
		$id = $this->getId();
		$requiredMessage = $this->getOptionValue('sgpb-subs-validation-message');
		$emailMessage = $this->getOptionValue('sgpb-subs-invalid-message');

		if (empty($subsFields)) {
			return $validateObj;
		}

		if (empty($emailMessage)) {
			$emailMessage = SGPB_SUBSCRIPTION_EMAIL_MESSAGE;
		}

		if (empty($requiredMessage)) {
			$requiredMessage = SGPB_SUBSCRIPTION_VALIDATION_MESSAGE;
		}

		$rules = 'rules: { ';
		$messages = 'messages: { ';

		$validateObj = 'var sgpbSubsValidateObj'.$id.' = { ';
		foreach ($subsFields as $subsField) {

			if (empty($subsField['attrs'])) {
				continue;
			}

			$attrs = $subsField['attrs'];
			$type = 'text';
			$name = '';
			$required = false;

			if (!empty($attrs['type'])) {
				$type = $attrs['type'];
			}
			if (!empty($attrs['name'])) {
				$name = $attrs['name'];
			}
			if (!empty($attrs['data-required'])) {
				$required = $attrs['data-required'];
			}

			if ($type == 'email') {
				$rules .= '"'.$name.'": {required: true, email: true},';
				$messages .= '"'.$name.'": {
					"required": "'.$requiredMessage.'",
					"email": "'.$emailMessage.'"
				},';
				continue;
			}

			if (!$required) {
				continue;
			}

			$messages .= '"'.$name.'": "'.$requiredMessage.'",';
			$rules .= '"'.$name.'" : "required",';

		}
		$rules = rtrim($rules, ',');
		$messages = rtrim($messages, ',');

		$rules .= '},';
		$messages .= '}';

		$validateObj .= $rules;
		$validateObj .= $messages;

		$validateObj .= '};';

		return $validateObj;
	}

	private function getSubscriptionValidationScripts($validateObj)
	{
		$script = '<script type="text/javascript">';
		$script .= $validateObj;
		$script .= '</script>';

		return $script;
	}

	public function getFormCustomStyles($styleData)
	{
		$placeholderColor = $styleData['placeholderColor'];
		$formBackgroundColor = $this->getFieldValue('sgpb-subs-form-bg-color');
		$formPadding = $this->getFieldValue('sgpb-subs-form-padding');
		$formBackgroundOpacity = $this->getFieldValue('sgpb-subs-form-bg-opacity');
		$popupId = $this->getId();
		if (isset($styleData['formBackgroundOpacity'])) {
			$formBackgroundOpacity = $styleData['formBackgroundOpacity'];
		}
		if (isset($styleData['formColor'])) {
			$formBackgroundColor = $styleData['formColor'];
		}
		if (isset($styleData['formPadding'])) {
			$formPadding = $styleData['formPadding'];
		}
		$formBackgroundColor = AdminHelper::hexToRgba($formBackgroundColor, $formBackgroundOpacity);

		ob_start();
		?>
			<style type="text/css">
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> {background-color: <?php echo esc_html($formBackgroundColor); ?>;padding: <?php echo esc_html($formPadding).'px'; ?>}
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> .js-subs-text-inputs::-webkit-input-placeholder {color: <?php echo esc_html($placeholderColor); ?>;font-weight: lighter;}
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> .js-subs-text-inputs::-moz-placeholder {color:<?php echo esc_html($placeholderColor); ?>;font-weight: lighter;}
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> .js-subs-text-inputs:-ms-input-placeholder {color:<?php echo esc_html($placeholderColor); ?>;font-weight: lighter;} /* ie */
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> .js-subs-text-inputs:-moz-placeholder {color:<?php echo esc_html($placeholderColor); ?>;font-weight: lighter;}
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> input[type="checkbox"] {-webkit-appearance: checkbox;!important}
				.sgpb-subs-form-<?php echo esc_attr($popupId); ?> input[type="checkbox"]:before {content: none!important; }
			</style>
		<?php
		$styles = ob_get_contents();
		ob_get_clean();

		return $styles;
	}

	public function getOptionValue($optionName, $forceDefaultValue = false)
	{
		return parent::getOptionValue($optionName, $forceDefaultValue);
	}

	public function getPopupTypeOptionsView()
	{
		$optionsViewData = array(
			'filePath' => SG_POPUP_TYPE_OPTIONS_PATH . 'subscription.php',
			'metaboxTitle' => 'Subscription Settings',
			'short_description' => 'Create subscription form, customize the fields and styles'
		);

		$isSubscriptionPlusActive = is_plugin_active(SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY);

		if ($isSubscriptionPlusActive) {
			return array();
		}

		return $optionsViewData;
	}

	private function getSubscriptionForm($subsFields)
	{
		$popupId = $this->getId();
		$form = '<div class="sgpb-subs-form-'.$popupId.' sgpb-subscription-form">';
		$form .= $this->getFormMessages();
		$form .= Functions::renderForm($subsFields);
		$form .= '</div>';

		return $form;
	}

	private function getFormMessages()
	{
		$successMessage = $this->getOptionValue('sgpb-subs-success-message');
		$errorMessage = $this->getOptionValue('sgpb-subs-error-message');
		if (empty($errorMessage)) {
			$errorMessage = SGPB_SUBSCRIPTION_ERROR_MESSAGE;
		}
		ob_start();
		?>
		<div class="subs-form-messages sgpb-alert sgpb-alert-success sg-hide-element">
			<p><?php echo $successMessage; ?></p>
		</div>
		<div class="subs-form-messages sgpb-alert sgpb-alert-danger sg-hide-element">
			<p><?php echo $errorMessage; ?></p>
		</div>
		<?php
		$messages = ob_get_contents();
		ob_end_clean();

		return $messages;
	}

	public function renderOptions($options)
	{
		// for old popups
		if (isset($options['sgpb-subs-success-popup']) && function_exists('sgpb\sgpGetCorrectPopupId')) {
			$options['sgpb-subs-success-popup'] = sgpGetCorrectPopupId($options['sgpb-subs-success-popup']);
		}

		return $options;
	}

	public function getPopupTypeContent()
	{
		$this->frontendFilters();

		apply_filters('sgpbSubscriptionForm', $this);
		$popupContent = $this->getContent();
		$formContent = $this->getFormContent();
		$showToTop = $this->getOptionValue('sgpb-subs-show-form-to-top');
		$content = $popupContent.$formContent;

		if ($showToTop) {
			$content = $formContent.$popupContent;
		}
		return $content;
	}

	public function subscriptionForm($popupObj)
	{
		if (!is_object($popupObj)) {
			return '';
		}
		$popupContent = '';
		$popupOptions = $popupObj->getOptions();
		$subsFields = $popupObj->getOptionValue('sgpb-subs-fields');
		$isSubscriptionPlusActive = is_plugin_active(SGPB_POPUP_SUBSCRIPTION_PLUS_EXTENSION_KEY);

		if (empty($subsFields) || !$isSubscriptionPlusActive) {
			$subsFields = $popupObj->createFormFieldsData();
		}

		$subsRequiredMessages = '';
		if (!empty($popupOptions['sgpb-subs-validation-message'])) {
			$subsRequiredMessages = $popupOptions['sgpb-subs-validation-message'];
		}

		$validationMessages = array(
			'requiredMessage' => $subsRequiredMessages
		);

		$styleData = array(
			'placeholderColor' => $popupOptions['sgpb-subs-text-placeholder-color'],
			'formColor' => $popupOptions['sgpb-subs-form-bg-color'],
			'formPadding' => isset($popupOptions['sgpb-subs-form-padding'])?$popupOptions['sgpb-subs-form-padding']:'',
			'formBackgroundOpacity' => isset($popupOptions['sgpb-subs-form-bg-opacity'])?$popupOptions['sgpb-subs-form-bg-opacity']:''
		);

		$validateScript = $popupObj->createValidateObj($subsFields, $validationMessages);
		$popupContent .= $popupObj->getSubscriptionForm($subsFields);
		$popupContent .= $popupObj->getSubscriptionValidationScripts($validateScript);
		$popupContent .= $popupObj->getFormCustomStyles($styleData);

		$popupObj->setFormContent($popupContent);

		return $popupObj;
	}

	public function getSubPopupObj()
	{
		$options = $this->getOptions();
		$subPopups = parent::getSubPopupObj();
		if ($options['sgpb-subs-success-behavior'] == 'openPopup') {
			$subPopupId = (!empty($options['sgpb-subs-success-popup'])) ? (int)$options['sgpb-subs-success-popup']: null;

			if (empty($subPopupId)) {
				return $subPopups;
			}

			// for old popups
			if (function_exists('sgpb\sgpGetCorrectPopupId')) {
				$subPopupId = sgpGetCorrectPopupId($subPopupId);
			}

			$subPopupObj = SGPopup::find($subPopupId);
			if (!empty($subPopupObj) && ($subPopupObj instanceof SGPopup)) {
				// We remove all events because this popup will be open after successful subscription
				$subPopupObj->setEvents(array('param' => 'click', 'value' => ''));
				$subPopups[] = $subPopupObj;
			}
		}

		return $subPopups;
	}

	public function getExtraRenderOptions()
	{
		return array();
	}

	public static function getSubscribersCount()
	{
		global $wpdb;
		$count = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME);

		return (int)$count;
	}

	public static function getAllSubscriptions()
	{
		$popupArgs = array();
		$popupArgs['type'][] = 'subscription';

		$popupArgs = apply_filters('sgpbGetAllSubscriptionArgs', $popupArgs);
		$allPopups = SGPopup::getAllPopups($popupArgs);

		return $allPopups;
	}

	public static function getAllSubscriptionForms()
	{
		$subsFormList = array();
		$subscriptionForms = self::getAllSubscriptions();

		foreach ($subscriptionForms as $subscriptionForm) {
			$title = $subscriptionForm->getTitle();
			$id = $subscriptionForm->getId();
			if ($title == '') {
				$title = '('.__('no title', SG_POPUP_TEXT_DOMAIN).')';
			}
			$subsFormList[$id] = $title;
		}

		return $subsFormList;
	}

	public static function getAllSubscribersDate()
	{
		$subsDateList = array();
		global $wpdb;
		$subscriptionPopups = $wpdb->get_results('SELECT id, cDate FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME, ARRAY_A);

		foreach ($subscriptionPopups as $subscriptionForm) {
			$id = $subscriptionForm['id'];
			$date = substr($subscriptionForm['cDate'], 0, 7);
			$subsDateList[$id]['date-value'] = $date;
			$subsDateList[$id]['date-title'] = AdminHelper::getFormattedDate($date);
		}

		return $subsDateList;
	}
}
