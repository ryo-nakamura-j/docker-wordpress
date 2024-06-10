<?php
require_once(SG_POPUP_HELPERS_PATH.'ConfigDataHelper.php');
use sgpb\PopupBuilderActivePackage;
class SgpbDataConfig
{
	public static function init()
	{
		self::addFilters();
		self::conditionInit();
		self::transientConfig();
		self::popupDefaultOptions();
	}

	public static function conditionInit()
	{
		global $SGPB_DATA_CONFIG_ARRAY;

		/*Target condition config*/
		$targetData = array('param' => 'Pages', 'operator' => 'Is not', 'value' => 'Value');
		$targetElementTypes = array(
			'param' => 'select',
			'operator' => 'select',
			'value' => 'select',
			'post_selected' => 'select',
			'page_selected' => 'select',
			'post_type' => 'select',
			'post_category' => 'select',
			'page_type' => 'select',
			'page_template' => 'select',
			'post_tags_ids' => 'select'
		);

		$targetParams = array(
			'not_rule' => __('Select rule', SG_POPUP_TEXT_DOMAIN),
			'everywhere' => __('Everywhere', SG_POPUP_TEXT_DOMAIN),
			'Post' => array(
				'post_all' => __('All posts', SG_POPUP_TEXT_DOMAIN),
				'post_selected' => __('Selected posts', SG_POPUP_TEXT_DOMAIN),
				'post_type' => __('Post type', SG_POPUP_TEXT_DOMAIN),
				'post_category' => __('Post category', SG_POPUP_TEXT_DOMAIN)
			),
			'Page' => array(
				'page_all' => __('All pages', SG_POPUP_TEXT_DOMAIN),
				'page_selected' => __('Selected pages', SG_POPUP_TEXT_DOMAIN),
				'page_type' => __('Page type', SG_POPUP_TEXT_DOMAIN),
				'page_template' => __('Page template', SG_POPUP_TEXT_DOMAIN)
			),
			'Tags' => array(
				'post_tags' => __('All tags', SG_POPUP_TEXT_DOMAIN),
				'post_tags_ids' => __('Selected tags', SG_POPUP_TEXT_DOMAIN)
			)
		);

		$targetOperators = array(
			array('operator' => 'add', 'name' => __('Add', SG_POPUP_TEXT_DOMAIN)),
			array('operator' => 'delete', 'name' => __('Delete', SG_POPUP_TEXT_DOMAIN))
		);

		$targetDataOperator = array(
			'==' => __('Is', SG_POPUP_TEXT_DOMAIN),
			'!=' => __('Is not', SG_POPUP_TEXT_DOMAIN)
		);
		$targetInitialData = array(
			array('param' => 'everywhere')
		);

		$targetDataParams['param'] = apply_filters('sgPopupTargetParams', $targetParams);
		$targetDataParams['operator'] = apply_filters('sgPopupTargetOperator', $targetDataOperator);
		$targetDataParams['post_selected'] = apply_filters('sgPopupTargetPostData', array());
		$targetDataParams['page_selected'] = apply_filters('sgPopupTargetPageSelected', array());
		$targetDataParams['post_type'] = apply_filters('sgPopupTargetPostType', array());
		$targetDataParams['post_category'] = apply_filters('sgPopupTargetPostCategory', array());
		$targetDataParams['page_type'] = apply_filters('sgPopupTargetPageType', array());
		$targetDataParams['page_template'] = apply_filters('sgPopupPageTemplates', array());
		$targetDataParams['post_tags_ids'] = apply_filters('sgPopupTags', array());
		$targetDataParams['everywhere'] = null;
		$targetDataParams['not_rule'] = null;
		$targetDataParams['post_all'] = null;
		$targetDataParams['page_all'] = null;
		$targetDataParams['post_tags'] = null;

		$targetAttrs = array(
			'param' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic',
					'autocomplete' => 'off'
				),
				'infoAttrs' => array(
					'label' => 'Display rule',
					'info' => __('Specify where the popup should be shown on your site.', SG_POPUP_TEXT_DOMAIN)
				)

			),
			'operator' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Is or is not',
					'info' => __('Allow or Disallow popup showing for the selected rule.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'post_selected' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'ajax',
					'data-value-param' => 'post',
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select Your Posts',
					'info' => __('Select your specific posts where the popup should be shown.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'page_selected' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'ajax',
					'data-value-param' => 'page',
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select Your Pages',
					'info' => __('Select the pages on your site where the specific popup will be shown.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'post_type' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'multiple',
					'data-value-param' => 'postTypes',
					'isNotPostType' => true,
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select Your post types',
					'info' => __('Specify the post types on your site to show the popup.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'post_category' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'ajax',
					'data-value-param' => 'postCategories',
					'isNotPostType' => true,
					'isPostCategory' => true,
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select post categories',
					'info' => __('Select the post categories on which the popup should be shown.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'page_type' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'multiple',
					'data-value-param' => 'pageTypes',
					'isNotPostType' => true,
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select specific page types',
					'info' => __('Specify the page types where the popup will be shown.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'page_template' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'multiple',
					'data-value-param' => 'pageTemplate',
					'isNotPostType' => true,
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select page template',
					'info' => __('Select the page templates on which the popup will be shown.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'post_tags_ids' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'ajax',
					'data-value-param' => 'postTags',
					'isNotPostType' => true,
					'isPostTag' => true,
					'multiple' => 'multiple'
				),
				'infoAttrs' => array(
					'label' => 'Select tags',
					'info' => __('Select the tags on your site for popup showing', SG_POPUP_TEXT_DOMAIN)
				)
			)
		);

		$popupTarget['columns'] = apply_filters('sgPopupTargetColumns', $targetData);
		$popupTarget['columnTypes'] = apply_filters('sgPopupTargetTypes', $targetElementTypes);
		$popupTarget['paramsData'] = apply_filters('sgPopupTargetData', $targetDataParams);
		$popupTarget['initialData'] = apply_filters('sgPopupTargetInitialData', $targetInitialData);
		$popupTarget['operators'] = apply_filters('sgpbPopupEventsOperators', $targetOperators);
		$popupTarget['attrs'] = apply_filters('sgPopupTargetAttrs', $targetAttrs);

		$SGPB_DATA_CONFIG_ARRAY['target'] = $popupTarget;

		/*Target condition config*/

		/*
		 *
		 * Events data
		 *
		 **/
		$eventsData = array('param' => 'Event name', 'value' => 'Delay');
		$hiddenOptionData = array();

		$eventsRowTypes = array(
			'param' => 'select',
			'operator' => 'select',
			'value' => 'text',
			'load' => 'number',
			'repetitive' => 'checkbox',
			'repetitivePeriod' => 'text',
			SGPB_CLICK_ACTION_KEY => 'select',
			'clickActionCustomClass' => 'text',
			'hoverActionCustomClass' => 'text',
			'defaultClickClassName' => 'conditionalText',
			'defaultHoverClassName' => 'conditionalText'
		);

		$params = array(
			'load' => 'On load',
			SGPB_CSS_CLASS_ACTIONS_KEY => __('Set by CSS class', SG_POPUP_TEXT_DOMAIN),
			SGPB_CLICK_ACTION_KEY => __('On Click', SG_POPUP_TEXT_DOMAIN),
			SGPB_HOVER_ACTION_KEY => __('On Hover', SG_POPUP_TEXT_DOMAIN),
			'inactivity' => __('Inactivity', SG_POPUP_TEXT_DOMAIN),
			'onScroll' => __('On Scroll', SG_POPUP_TEXT_DOMAIN)
		);

		$hiddenOptionData['load'] = array(
			'options' => array(
				'repetitive' => 'Repetitive popup'
			)
		);

		$onLoadData = 0;

		$eventsDataParams['param'] = $params;
		$eventsDataParams['operator'] = array();
		$eventsDataParams['load'] = $onLoadData;
		$eventsDataParams['clickActionCustomClass'] = '';
		$eventsDataParams['hoverActionCustomClass'] = '';
		$eventsDataParams['defaultClickClassName'] = 'sg-popup-id-';
		$eventsDataParams['defaultHoverClassName'] = 'sg-popup-hover-';
		$eventsDataParams[SGPB_CSS_CLASS_ACTIONS_KEY] = null;
		$eventsDataParams[SGPB_CLICK_ACTION_KEY.'Operator'] = ConfigDataHelper::getClickActionOptions();
		$eventsDataParams[SGPB_HOVER_ACTION_KEY.'Operator'] = ConfigDataHelper::getHoverActionOptions();
		/*Hidden params data*/
		$eventsDataParams['repetitive'] = '';
		$eventsDataParams['repetitivePeriod'] = 0;

		$eventOperators = array(
			array('operator' => 'add', 'name' => 'Add'),
			array('operator' => 'edit', 'name' => 'Edit'),
			array('operator' => 'delete', 'name' => 'Delete')
		);

		$eventsInitialData = array(
			array('param' => 'load', 'value' => '', 'hiddenOption' => array())
		);

		$eventsAttrs = array(
			'param' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic sgpb-selectbox-settings',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Event',
					'info' => __('Select when the popup should appear on the page.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'operator' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Options',
					'info' => __('Select the condition for the current event.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'load' => array(
				'htmlAttrs' => array('class' => 'js-sg-onload-text formItem__input formItem__input_select2_num_input', 'placeholder' => __('default custom delay will be used', SG_POPUP_TEXT_DOMAIN), 'min' => 0),
				'infoAttrs' => array(
					'label' => 'Delay',
					'info' => __('Specify how long the popup appearance should be delayed after loading the page (in sec).', SG_POPUP_TEXT_DOMAIN)
				)
			),
			SGPB_CLICK_ACTION_KEY => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic formItem__input formItem__input_select2_num_input',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Click Event',
					'info' => __('Specify the part of the page, in percentages, where the popup should appear after scrolling.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			SGPB_HOVER_ACTION_KEY => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic formItem__input formItem__input_select2_num_input',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Hover Event',
					'info' => __('Specify the part of the page, in percentages, where the popup should appear after scrolling.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'clickActionCustomClass' => array(
				'htmlAttrs' => array('class' => 'js-sg-inactivity-text formItem__input formItem__input_select2_num_input', 'min' => 0),
				'infoAttrs' => array(
					'label' => 'Custom Class',
					'info' => __('Add the CSS class name of your HTML element which will trigger this popup after click.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'hoverActionCustomClass' => array(
				'htmlAttrs' => array('class' => 'js-sg-inactivity-text formItem__input formItem__input_select2_num_input', 'min' => 0),
				'infoAttrs' => array(
					'label' => 'Custom Class',
					'info' => __('Add the CSS class name of your HTML element which will trigger this popup after click.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'defaultClickClassName' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-click-event formItem__input formItem__input_select2_num_input',
					'min' => 0,
					'readonly' => '',
					'value' => 'sg-popup-id-',
					'beforeSaveLabel' => __('Please save popup to generate class name.', SG_POPUP_TEXT_DOMAIN)
					),
				'infoAttrs' => array(
					'label' => 'Default Class',
					'info' => __('Add the following CSS class into your HTML element.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'defaultHoverClassName' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-hover-event formItem__input formItem__input_select2_num_input',
					'min' => 0,
					'readonly' => '',
					'value' => 'sg-popup-hover-',
					'beforeSaveLabel' => __('Please save popup to generate class name.', SG_POPUP_TEXT_DOMAIN)
					),
				'infoAttrs' => array(
					'label' => 'Default Class',
					'info' => __('Add the following CSS class into your HTML element.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'repetitive' => array(
				'htmlAttrs' => array(
					'class' => 'sgpb-popup-option sgpb-popup-accordion',
					'data-name' => 'repetitive',
					'autocomplete' => 'off'
				),
				'infoAttrs' => array(
					'label' => 'Repetitive open popup',
					'info' => __('If this option is enabled the same popup will open up after every X seconds you have defined (after closing it).', SG_POPUP_TEXT_DOMAIN)
				),
				'childOptions' => array('repetitivePeriod')
			),
			'repetitivePeriod' => array(
				'htmlAttrs' => array(
					'class' => 'sgpb-popup-option',
					'autocomplete' => 'off'
				),
				'infoAttrs' => array(
					'label' => 'period',
					'info' => __('This is info', SG_POPUP_TEXT_DOMAIN)
				)
			)
		);

		$popupEvents['columns'] = apply_filters('sgPopupEventColumns', $eventsData);
		$popupEvents['columnTypes'] = apply_filters('sgPopupEventTypes', $eventsRowTypes);
		$popupEvents['paramsData'] = apply_filters('sgPopupEventsData', $eventsDataParams);
		$popupEvents['initialData'] = apply_filters('sgPopupEventsInitialData', $eventsInitialData);
		$popupEvents['operators'] = apply_filters('sgPopupEventOperators', $eventOperators);
		$popupEvents['hiddenOptionData'] = apply_filters('sgEventsHiddenData', $hiddenOptionData);
		$popupEvents['attrs'] = apply_filters('sgPopupEventAttrs', $eventsAttrs);

		$popupEvents['specialDefaultOperator'] = apply_filters('sgPopupEventsOperators', ' ');
		$popupEvents['operatorAllowInConditions'] = apply_filters('sgPopupEventsOperatorAllowInConditions', array(SGPB_CLICK_ACTION_KEY, SGPB_HOVER_ACTION_KEY));

		$SGPB_DATA_CONFIG_ARRAY['events'] = $popupEvents;

		/*Target condition config*/
		$targetData = array('param' => 'Pages', 'operator' => 'Is not', 'value' => 'Value');
		$targetElementTypes = array(
			'param' => 'select',
			'operator' => 'select',
			'value' => 'select',
			'select_role' => 'select',
		);

		$targetParams = array(
			'select_role' => __('Select Conditions', SG_POPUP_TEXT_DOMAIN)
		);

		$targetOperators = array(
			array('operator' => 'add', 'name' => __('Add', SG_POPUP_TEXT_DOMAIN)),
			array('operator' => 'delete', 'name' => __('Delete', SG_POPUP_TEXT_DOMAIN))
		);

		$targetDataOperator = array(
			'==' => __('Is', SG_POPUP_TEXT_DOMAIN),
			'!=' => __('Is not', SG_POPUP_TEXT_DOMAIN)
		);

		$targetInitialData = array(
			array('param' => 'select_role', 'operator' => '==', 'value' => '')
		);

		$targetDataParams['param'] = apply_filters('sgpbPopupSpecialEventsParams', $targetParams);
		$targetDataParams['operator'] = apply_filters('sgpbPopupConditionsOperator', $targetDataOperator);

		$targetDataParams['select_role'] = null;

		$targetAttrs = array(
			'param' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic sgpb-selectbox-settings',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic',
					'autocomplete' => 'off'
				),
				'infoAttrs' => array(
					'label' => 'Condition',
					'info' => __('Target visitors to show the popup by different conditions.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'operator' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => 'Rule',
					'info' => __('Allow or Disallow popup showing for the selected conditions.', SG_POPUP_TEXT_DOMAIN)
				)
			)
		);

		$popupConditions['columns'] = apply_filters('sgPopupConditionsColumns', $targetData);
		$popupConditions['columnTypes'] = apply_filters('sgPopupConditionsTypes', $targetElementTypes);
		$popupConditions['paramsData'] = apply_filters('sgPopupConditionsData', $targetDataParams);
		$popupConditions['initialData'] = apply_filters('sgPopupConditionsInitialData', $targetInitialData);
		$popupConditions['operators'] = apply_filters('sgPopupConditionsOperators', $targetOperators);
		$popupConditions['attrs'] = apply_filters('sgPopupConditionsAttrs', $targetAttrs);

		$popupConditions['specialDefaultOperator'] = apply_filters('sgPopupConditionsOperators', $targetDataOperator);
		$popupConditions['operatorAllowInConditions'] = apply_filters('sgPopupConditionsOperatorAllowInConditions', array());

		$SGPB_DATA_CONFIG_ARRAY['conditions'] = $popupConditions;

		$SGPB_DATA_CONFIG_ARRAY['behavior-after-special-events'] = self::getBehaviorAfterSpecialEventsConfig();
		$SGPB_DATA_CONFIG_ARRAY = apply_filters('sgpbConfigArray', $SGPB_DATA_CONFIG_ARRAY);
		/*Target condition config*/
	}

	public static function allFreeExtensionsKeys()
	{
		$keys = array();

		return $keys;
	}

	public static function allExtensionsKeys()
	{
		$keys = array();
		$keys[] = array(
			'label' => __('AdBlock', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-adblock/PopupBuilderAdBlock.php',
			'key' => 'sgpbAdBlock',
			'url' => SG_POPUP_AD_BLOCK_URL
		);
		$keys[] = array(
			'label' => __('Advanced Closing', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-advanced-closing/PopupBuilderAdvancedClosing.php',
			'key' => 'advancedClosing',
			'url' => SG_POPUP_ADVANCED_CLOSING_URL
		);
		$keys[] = array(
			'label' => __('Advanced Targeting', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' =>  'popupbuilder-advanced-targeting/PopupBuilderAdvancedTargeting.php',
			'key' => 'sgpbAdvancedTargeting',
			'url' => SG_POPUP_ADVANCED_TARGETING_URL
		);
		$keys[] = array(
			'label' => __('Age Restriction', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-age-verification/PopupBuilderAgeverification.php',
			'key' => 'ageVerification',
			'url' => SGPB_AGE_VERIFICATION_PLUGIN_URL
		);
		$keys[] = array(
			'label' => __('Analytics', SG_POPUP_TEXT_DOMAIN),
			'version' => '4.0',
			'stable_version' => '4.0',
			'pluginKey' => 'popupbuilder-analytics/PopupBuilderAnalytics.php',
			'key' => 'sgpbAnalitics',
			'url' => SG_POPUP_ANALYTICS_URL
		);
		$keys[] = array(
			'label' => __('AWeber', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' =>  'popupbuilder-aweber/PopupBuilderAWeber.php',
			'key' => 'sgpbAWeber',
			'url' => SG_POPUP_AWEBER_URL
		);
		$keys[] = array(
			'label' => __('Contact Form', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-contact-form/PopupBuilderContactForm.php',
			'key' => 'contactForm',
			'url' => SG_POPUP_CONTACT_FORM_URL
		);
		$keys[] = array(
			'label' => __('Countdown', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-countdown/PopupBuilderCountdown.php',
			'key' => 'countdown',
			'url' => SG_POPUP_COUNTDOWN_URL
		);
		$keys[] = array(
			'label' => __('EDD', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-edd/PopupBuilderEdd.php',
			'key' => 'edd',
			'url' => SGPB_EDD_PLUGIN_URL
		);
		$keys[] = array(
			'label' => __('Exit Intent',SG_POPUP_TEXT_DOMAIN),
			'version' => '4.0',
			'stable_version' => '4.0',
			'pluginKey' => 'popupbuilder-exit-intent/PopupBuilderExitIntent.php',
			'key' => 'sgpbExitIntent',
			'url' => SG_POPUP_EXIT_INTENT_URL
		);
		$keys[] = array(
			'label' => __('Gamification', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-gamification/PopupBuilderGamification.php',
			'key' => 'gamification',
			'url' => SGPB_GAMIFICATION_PLUGIN_URL
		);
		$keys[] = array(
			'label' => __('Geo Targeting', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-geo-targeting/PopupBuilderGeoTargeting.php',
			'key' => 'geo-targeting',
			'url' => SG_POPUP_GEO_TARGETING_URL
		);
		$keys[] = array(
			'label' => __('Iframe', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-iframe/PopupBuilderIframe.php',
			'key' => 'iframe',
			'url' => SG_POPUP_IFRAME_URL
		);
		$keys[] = array(
			'label' => __('Inactivity', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-inactivity/PopupBuilderInactivity.php',
			'key' => 'sgpbInactivity',
			'url' => SG_POPUP_INACTIVITY_URL
		);
		$keys[] = array(
			'label' => __('Log In', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' =>  'popupbuilder-login/PopupBuilderLogin.php',
			'key' => 'login',
			'url' => SG_POPUP_LOGIN_URL
		);
		$keys[] = array(
			'label' => __('Mailchimp', SG_POPUP_TEXT_DOMAIN),
			'version' => '4.0',
			'stable_version' => '4.0',
			'pluginKey' => 'popupbuilder-mailchimp/PopupBuilderMailchimp.php',
			'key' => 'sgpbMailchimp',
			'url' => SG_POPUP_MAILCHIMP_URL
		);
		$keys[] = array(
			'label' => __('PDF', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-pdf/PopupBuilderPdf.php',
			'key' => 'pdf',
			'url' => SGPB_PDF_PLUGIN_URL
		);
        $keys[] = array(
			'label' => __('Push Notification', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-push-notification/PopupBuilderPushNotification.php',
			'key' => 'pushNotification',
			'url' => SG_POPUP_PUSH_NOTIFICATION_URL
		);
		$keys[] = array(
			'label' => __('Random', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-random/PopupBuilderRandom.php',
			'key' => 'sgpbRandom',
			'url' => SG_POPUP_RANDOM_URL
		);
		$keys[] = array(
			'label' => __('Recent Sales', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-recent-sales/PopupBuilderRecentSales.php',
			'key' => 'sgpbRecentSales',
			'url' => SG_POPUP_RECENT_SALES_URL
		);
		$keys[] = array(
			'label' => __('Registration', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' =>  'popupbuilder-registration/PopupBuilderRegistration.php',
			'key' => 'registration',
			'url' => SG_POPUP_REGISTRATION_URL
		);
		$keys[] = array(
			'label' => __('Restriction', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-restriction/PopupBuilderAgerestriction.php',
			'key' => 'ageRestriction',
			'url' => SG_POPUP_RESTRICTION_URL
		);
		$keys[] = array(
			'label' => __('Scheduling', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-scheduling/PopupBuilderScheduling.php',
			'key' => 'scheduling',
			'url' => SG_POPUP_SCHEDULING_URL
		);
		$keys[] = array(
			'label' => __('Scroll', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' => 'popupbuilder-scroll/PopupBuilderScroll.php',
			'key' => 'sgpbScroll',
			'url' => SG_POPUP_SCROLL_URL
		);
		$keys[] = array(
			'label' => __('Social', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-social/PopupBuilderSocial.php',
			'key' => 'social',
			'url' => SG_POPUP_SOCIAL_URL
		);
		$keys[] = array(
			'label' => __('Subscription Plus', SG_POPUP_TEXT_DOMAIN),
			'version' => '4.0',
			'stable_version' => '4.0',
			'pluginKey' =>  'popupbuilder-subscription-plus/PopupBuilderSubscriptionPlus.php',
			'key' => 'subscriptionPlus',
			'url' => SG_POPUP_SUBSCRIPTION_PLUS_URL
		);
		$keys[] = array(
			'label' => __('Video', SG_POPUP_TEXT_DOMAIN),
			'version' => '2.0',
			'stable_version' => '2.0',
			'pluginKey' => 'popupbuilder-video/PopupBuilderVideo.php',
			'key' => 'video',
			'url' => SG_POPUP_VIDEO_URL
		);
		$keys[] = array(
			'label' => __('WooCommerce', SG_POPUP_TEXT_DOMAIN),
			'version' => '3.0',
			'stable_version' => '3.0',
			'pluginKey' =>  'popupbuilder-woocommerce/popupbuilderWoocommerce.php',
			'key' => 'sgpbWOO',
			'url' => SG_POPUP_WOOCOMMERCE_URL
		);

		return apply_filters('sgpbExtensionsKeys', $keys);
	}

	private static function getBehaviorAfterSpecialEventsConfig()
	{
		$columns = array(
			'param' => 'Event',
			'operator' => 'Behavior',
			'value' => 'Value'
		);

		$columnTypes = array(
			'param' => 'select',
			'operator' => 'select',
			'value' => 'select',
			'select_event' => 'select',
			'select_behavior' => 'select',
			'redirect-url' => 'url',
			'open-popup' => 'select',
			'close-popup' => 'number'
		);

		$params = array(
			'param' => array(
				'select_event' => __('Select event', SG_POPUP_TEXT_DOMAIN),
				__('Special events', SG_POPUP_TEXT_DOMAIN) => array(
					SGPB_CONTACT_FORM_7_BEHAVIOR_KEY => __('Contact Form 7 submission', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'operator' => array(
				'select_behavior' => __('Select behavior', SG_POPUP_TEXT_DOMAIN),
				__('Behaviors', SG_POPUP_TEXT_DOMAIN) => array(
					'redirect-url' => __('Redirect to URL', SG_POPUP_TEXT_DOMAIN),
					'open-popup' => __('Open another popup', SG_POPUP_TEXT_DOMAIN),
					'close-popup' => __('Close current popup', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'redirect-url' => '',
			'open-popup' => array(),
			'close-popup' => '',
			'select_event' => null,
			'select_behavior' => null
		);

		$initialData = array(
			array(
				'param' => 'select_event',
				'operator' => ''
			)
		);

		$attrs = array(
			'param' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => __('Event', SG_POPUP_TEXT_DOMAIN),
					'info' => __('Select the special event you want to catch.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'operator' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-basic',
					'data-select-class' => 'js-select-basic',
					'data-select-type' => 'basic'
				),
				'infoAttrs' => array(
					'label' => __('Behavior', SG_POPUP_TEXT_DOMAIN),
					'info' => __('Select what should happen after the special event.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'redirect-url' => array(
				'htmlAttrs' => array(
					'class' => 'sg-full-width formItem__input formItem__input_select2_num_input',
					'placeholder' => 'https://www.example.com',
					'required' => 'required'
				),
				'infoAttrs' => array(
					'label' => __('URL', SG_POPUP_TEXT_DOMAIN),
					'info' => __('Enter the URL of the page should be redirected to.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'open-popup' => array(
				'htmlAttrs' => array(
					'class' => 'js-sg-select2 js-select-ajax',
					'data-select-class' => 'js-select-ajax',
					'data-select-type' => 'ajax',
					'data-value-param' => SG_POPUP_POST_TYPE,
					'required' => 'required'
				),
				'infoAttrs' => array(
					'label' => __('Select popup', SG_POPUP_TEXT_DOMAIN),
					'info' => __('Select the popup that should be opened.', SG_POPUP_TEXT_DOMAIN)
				)
			),
			'close-popup' => array(
				'htmlAttrs' => array(
					'class' => 'sg-full-width formItem__input formItem__input_select2_num_input',
					'required' => 'required',
					'value' => 0,
					'min' => 0
				),
				'infoAttrs' => array(
					'label' => __('Delay', SG_POPUP_TEXT_DOMAIN),
					'info' => __('After how many seconds the popup should close.', SG_POPUP_TEXT_DOMAIN)
				)
			)
		);

		$config = array();
		$config['columns'] = apply_filters('sgPopupSpecialEventsColumns', $columns);
		$config['columnTypes'] = apply_filters('sgPopupSpecialEventsColumnTypes', $columnTypes);
		$config['paramsData'] = apply_filters('sgPopupSpecialEventsParams', $params);
		$config['initialData'] = apply_filters('sgPopupSpecialEventsInitialData', $initialData);
		$config['attrs'] = apply_filters('sgPopupSpecialEventsAttrs', $attrs);
		$config['operators'] = apply_filters('sgpbPopupSpecialEventsOperators', array());
		$config['specialDefaultOperator'] = apply_filters('sgpbPopupSpecialEventsDefaultOperators', ' ');

		return $config;
	}

	public static function popupDefaultOptions()
	{
		global $SGPB_OPTIONS;
		global $SGPB_DATA_CONFIG_ARRAY;

		$targetDefaultValue = array($SGPB_DATA_CONFIG_ARRAY['target']['initialData']);

		$eventsDefaultData = array($SGPB_DATA_CONFIG_ARRAY['events']['initialData']);
		$conditionsDefaultData = array($SGPB_DATA_CONFIG_ARRAY['conditions']['initialData']);
		$specialEventsDefaultData = array($SGPB_DATA_CONFIG_ARRAY['behavior-after-special-events']['initialData']);

		$options = array();

		$options[] = array('name' => 'sgpb-target', 'type' => 'array', 'defaultValue' => $targetDefaultValue);
		$options[] = array('name' => 'sgpb-events', 'type' => 'array', 'defaultValue' => $eventsDefaultData);
		$options[] = array('name' => 'sgpb-conditions', 'type' => 'array', 'defaultValue' => $conditionsDefaultData, 'min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER);
		$options[] = array('name' => 'sgpb-behavior-after-special-events', 'type' => 'array', 'defaultValue' => $specialEventsDefaultData);
		$options[] = array('name' => 'sgpb-type', 'type' => 'text', 'defaultValue' => 'html');
		$options[] = array('name' => 'sgpb-popup-counting-disabled', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-esc-key', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-enable-close-button', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-enable-content-scrolling', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-overlay-click', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-content-click', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-subs-hide-subs-users', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-content-click-behavior', 'type' => 'text', 'defaultValue' => 'close');
		$options[] = array('name' => 'sgpb-click-redirect-to-url', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-redirect-to-new-tab', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-copy-to-clipboard-text', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-copy-to-clipboard-close-popup', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-copy-to-clipboard-alert', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-copy-to-clipboard-message', 'type' => 'text', 'defaultValue' => __('Copied to Clipboard!', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-disable-popup-closing', 'type' => 'checkbox', 'defaultValue' => '', 'min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER);
		$options[] = array('name' => 'sgpb-popup-dimension-mode', 'type' => 'text', 'defaultValue' => 'responsiveMode');
		$options[] = array('name' => 'sgpb-popup-dimension-mode', 'type' => 'text', 'defaultValue' => '100');
		$options[] = array('name' => 'sgpb-width', 'type' => 'text', 'defaultValue' => '640px');
		$options[] = array('name' => 'sgpb-height', 'type' => 'text', 'defaultValue' => '480px');
		$options[] = array('name' => 'sgpb-max-width', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-max-height', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-min-width', 'type' => 'text', 'defaultValue' => '120px');
		$options[] = array('name' => 'sgpb-min-height', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-timer-status', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-start-timer', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-end-timer', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-fixed', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-fixed-position', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-popup-delay', 'type' => 'text', 'defaultValue' => '0');
		$options[] = array('name' => 'sgpb-popup-order', 'type' => 'text', 'defaultValue' => '0');
		$options[] = array('name' => 'sgpb-disable-page-scrolling', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-content-padding', 'type' => 'text', 'defaultValue' => 7);
		$options[] = array('name' => 'sgpb-popup-z-index', 'type' => 'text', 'defaultValue' => 9999);
		$options[] = array('name' => 'sgpb-content-custom-class', 'type' => 'text', 'defaultValue' => 'sg-popup-content');
		$options[] = array('name' => 'sgpb-close-after-page-scroll', 'type' => 'checkbox', 'defaultValue' => '', 'min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER);
		$options[] = array('name' => 'sgpb-auto-close', 'type' => 'checkbox', 'defaultValue' => '', 'min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER);
		$options[] = array('name' => 'sgpb-auto-close-time', 'type' => 'number', 'defaultValue' => 0);
		$options[] = array('name' => 'sgpb-reopen-after-form-submission', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-open-sound', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-sound-url', 'type' => 'text', 'defaultValue' => SG_POPUP_SOUND_URL.SGPB_POPUP_DEFAULT_SOUND);
		$options[] = array('name' => 'sgpb-open-animation', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-close-animation', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-open-animation-speed', 'type' => 'text', 'defaultValue' => 1);
		$options[] = array('name' => 'sgpb-close-animation-speed', 'type' => 'text', 'defaultValue' => 1);
		$options[] = array('name' => 'sgpb-popup-themes', 'type' => 'text', 'defaultValue' => 'sgpb-theme-1');
		$options[] = array('name' => 'sgpb-enable-popup-overlay', 'type' => 'checkbox', 'defaultValue' => 'on', 'min-version' => SGPB_POPUP_PRO_MIN_VERSION, 'min-pkg' => SGPB_POPUP_PKG_SILVER);
		$options[] = array('name' => 'sgpb-overlay-custom-class', 'type' => 'text', 'defaultValue' => 'sgpb-popup-overlay');
		$options[] = array('name' => 'sgpb-overlay-color', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-background-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-overlay-opacity', 'type' => 'text', 'defaultValue' => 0.8);
		$options[] = array('name' => 'sgpb-content-opacity', 'type' => 'text', 'defaultValue' => 0.8);
		$options[] = array('name' => 'sgpb-background-image', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-show-background', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-force-rtl', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-disable-border', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-background-image-mode', 'type' => 'text', 'defaultValue' => 'no-repeat');
		$options[] = array('name' => 'sgpb-image-url', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-close-button-delay', 'type' => 'number', 'defaultValue' => 0);
		$options[] = array('name' => 'sgpb-button-position-bottom', 'type' => 'number', 'defaultValue' => 9);
		$options[] = array('name' => 'sgpb-button-position-right', 'type' => 'number', 'defaultValue' => 9);
		$options[] = array('name' => 'sgpb-button-image', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-button-image-width', 'type' => 'text', 'defaultValue' => 21);
		$options[] = array('name' => 'sgpb-button-image-height', 'type' => 'text', 'defaultValue' => 21);
		$options[] = array('name' => 'sgpb-is-active', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-subs-form-bg-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-subs-form-bg-opacity', 'type' => 'text', 'defaultValue' => 0.8);
		$options[] = array('name' => 'sgpb-subs-form-padding', 'type' => 'number', 'defaultValue' => 2);
		$options[] = array('name' => 'sgpb-subs-email-placeholder', 'type' => 'text', 'defaultValue' => __('Email *', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-first-name-status', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-subs-first-placeholder', 'type' => 'text', 'defaultValue' => __('First name', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-first-name-required', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-subs-last-name-status', 'type' => 'checkbox', 'defaultValue' => 'on');
		$options[] = array('name' => 'sgpb-subs-last-placeholder', 'type' => 'text', 'defaultValue' => __('Last name', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-last-name-required', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-subs-validation-message', 'type' => 'text', 'defaultValue' => __('This field is required.', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-text-width', 'type' => 'text', 'defaultValue' => '300px');
		$options[] = array('name' => 'sgpb-subs-text-height', 'type' => 'text', 'defaultValue' => '40px');
		$options[] = array('name' => 'sgpb-subs-text-border-width', 'type' => 'text', 'defaultValue' => '2px');
		$options[] = array('name' => 'sgpb-subs-text-border-color', 'type' => 'text', 'defaultValue' => '#e8e8e8');
		$options[] = array('name' => 'sgpb-subs-text-bg-color', 'type' => 'text', 'defaultValue' => '#f1f1f1');
		$options[] = array('name' => 'sgpb-subs-text-color', 'type' => 'text', 'defaultValue' => '#000000');
		$options[] = array('name' => 'sgpb-subs-text-placeholder-color', 'type' => 'text', 'defaultValue' => '#757575');
		$options[] = array('name' => 'sgpb-subs-btn-width', 'type' => 'text', 'defaultValue' => '300px');
		$options[] = array('name' => 'sgpb-subs-btn-height', 'type' => 'text', 'defaultValue' => '40px');
		$options[] = array('name' => 'sgpb-subs-btn-border-radius', 'type' => 'text', 'defaultValue' => '4px');
		$options[] = array('name' => 'sgpb-subs-btn-border-width', 'type' => 'text', 'defaultValue' => '0px');
		$options[] = array('name' => 'sgpb-subs-btn-border-color', 'type' => 'text', 'defaultValue' => '#007fe1');
		$options[] = array('name' => 'sgpb-subs-btn-title', 'type' => 'text', 'defaultValue' => __('Subscribe', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-btn-progress-title', 'type' => 'text', 'defaultValue' => __('Please wait...', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-btn-bg-color', 'type' => 'text', 'defaultValue' => '#007fe1');
		$options[] = array('name' => 'sgpb-subs-btn-text-color', 'type' => 'text', 'defaultValue' => '#FFFFFF');
		$options[] = array('name' => 'sgpb-subs-error-message', 'type' => 'text', 'defaultValue' => SGPB_SUBSCRIPTION_ERROR_MESSAGE);
		$options[] = array('name' => 'sgpb-subs-invalid-message', 'type' => 'text', 'defaultValue' => __('Please enter a valid email address', SG_POPUP_TEXT_DOMAIN).'.');
		$options[] = array('name' => 'sgpb-subs-success-behavior', 'type' => 'text', 'defaultValue' => 'showMessage');
		$options[] = array('name' => 'sgpb-subs-success-message', 'type' => 'text', 'defaultValue' =>  __('You have successfully subscribed to the newsletter', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-success-redirect-URL', 'type' => 'text', 'defaultValue' =>  '');
		$options[] = array('name' => 'sgpb-subs-success-redirect-new-tab', 'type' => 'checkbox', 'defaultValue' =>  '');
		$options[] = array('name' => 'sgpb-subs-gdpr-status', 'type' => 'checkbox', 'defaultValue' =>  '');
		$options[] = array('name' => 'sgpb-subs-show-form-to-top', 'type' => 'checkbox', 'defaultValue' =>  '');
		$options[] = array('name' => 'sgpb-subs-gdpr-label', 'type' => 'text', 'defaultValue' =>  __('Accept Terms', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-gdpr-text', 'type' => 'text', 'defaultValue' =>  __(get_bloginfo().' will use the information you provide on this form to be in touch with you and to provide updates and marketing.', SG_POPUP_TEXT_DOMAIN));
		$options[] = array('name' => 'sgpb-subs-fields', 'type' => 'sgpb', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-fblike-like-url', 'type' => 'text', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-fblike-layout', 'type' => 'text', 'defaultValue' => 'standard');
		$options[] = array('name' => 'sgpb-fblike-dont-show-share-button', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-border-color', 'type' => 'text', 'defaultValue' => '#000000');
		$options[] = array('name' => 'sgpb-border-radius', 'type' => 'text', 'defaultValue' => 0);
		$options[] = array('name' => 'sgpb-show-popup-same-user', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-show-popup-same-user-count', 'type' => 'number', 'defaultValue' => 1);
		$options[] = array('name' => 'sgpb-show-popup-same-user-expiry', 'type' => 'number', 'defaultValue' => 1);
		$options[] = array('name' => 'sgpb-show-popup-same-user-page-level', 'type' => 'checkbox', 'defaultValue' => '');

		$options[] = array('name' => 'sgpb-enable-floating-button', 'type' => 'checkbox', 'defaultValue' => '');
		$options[] = array('name' => 'sgpb-floating-button-style', 'type' => 'text', 'defaultValue' => 'corner');
		$options[] = array('name' => 'sgpb-floating-button-position', 'type' => 'text', 'defaultValue' => 'bottom-right');
		$options[] = array('name' => 'sgpb-floating-button-position-top', 'type' => 'text', 'defaultValue' => '40');
		$options[] = array('name' => 'sgpb-floating-button-position-right', 'type' => 'text', 'defaultValue' => '50');
		$options[] = array('name' => 'sgpb-floating-button-font-size', 'type' => 'number', 'defaultValue' => 16);
		$options[] = array('name' => 'sgpb-floating-button-border-size', 'type' => 'number', 'defaultValue' => 5);
		$options[] = array('name' => 'sgpb-floating-button-border-radius', 'type' => 'number', 'defaultValue' => 5);
		$options[] = array('name' => 'sgpb-floating-button-border-color', 'type' => 'text', 'defaultValue' => '#5263eb');
		$options[] = array('name' => 'sgpb-floating-button-bg-color', 'type' => 'text', 'defaultValue' => '#5263eb');
		$options[] = array('name' => 'sgpb-floating-button-text-color', 'type' => 'text', 'defaultValue' => '#ffffff');
		$options[] = array('name' => 'sgpb-floating-button-text', 'type' => 'text', 'defaultValue' => __('Click it!'));

		$SGPB_OPTIONS = apply_filters('sgpbPopupDefaultOptions', $options);
	}

	public static function getOldExtensionsInfo()
	{
		$data = array(
			array(
				'folderName' => 'popup-builder-ad-block',
				'label' => __('AdBlock', SG_POPUP_TEXT_DOMAIN)
			),
			array(
				'folderName' => 'popup-builder-analytics',
				'label' => __('Analytics', SG_POPUP_TEXT_DOMAIN)
			),
			array(
				'folderName' => 'popup-builder-exit-intent',
				'label' => __('Exit intent', SG_POPUP_TEXT_DOMAIN)
			),
			array(
				'folderName' => 'popup-builder-mailchimp',
				'label' => __('Mailchimp', SG_POPUP_TEXT_DOMAIN)
			),
			array(
				'folderName' => 'popup-builder-aweber',
				'label' => __('AWeber', SG_POPUP_TEXT_DOMAIN)
			)
		);

		return $data;
	}

	public static function addFilters()
	{
		ConfigDataHelper::addFilters();
	}

	public static function transientConfig()
	{
		global $SGPB_TRANSIENT_CONFIG;

		$SGPB_TRANSIENT_CONFIG = array(
			SGPB_TRANSIENT_POPUPS_LOAD,
			SGPB_TRANSIENT_POPUPS_TERMS,
			SGPB_TRANSIENT_POPUPS_ALL_CATEGORIES
		);

		$SGPB_TRANSIENT_CONFIG = apply_filters('sgpbAllTransients', $SGPB_TRANSIENT_CONFIG);
	}

	/**
	 * method to get popup default conditions or other configs
	 *
	*/
	public static function websiteDefaultConfigs() {
		$configs = array();
		$eventsInitialData = array(
			array('param' => 'load', 'value' => '', 'hiddenOption' => array())
		);
		$targetInitialData = array(
			array('param' => 'everywhere')
		);
		$configs['events'] = apply_filters('sgPopupEventsInitialData', $eventsInitialData);
		$configs['target'] = apply_filters('sgPopupTargetInitialData', $targetInitialData);

		return $configs;
	}
}
