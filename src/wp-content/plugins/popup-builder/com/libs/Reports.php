<?php
namespace sgpb;

class SGPBReports
{
	private $type     = 'debug';
	private $reports  = array();

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$debugModeEnabled = AdminHelper::getOption('sgpb-enable-debug-mode');
		if ($debugModeEnabled == 1) {
			add_action('sgpbDebugReportUpdate', array($this, 'updateReport'), 10, 3);
			$this->render();
		}
	}

	public function updateReport($key = '', $value = '', $popupId = 0)
	{
		global $SGPB_DEBUG_POPUP_BUILDER_DETAILS;
		$value = $this->filterOptions($key, $value);
		$SGPB_DEBUG_POPUP_BUILDER_DETAILS[$popupId][$key] = $value;
		$this->updateCookiesReportForCurrentPopup($popupId);
	}

	public function render()
	{
		add_action('wp_footer', array($this, 'renderReportData'));
	}

	public function renderReportData()
	{
		global $wp_version;
		global $SGPB_DEBUG_POPUP_BUILDER_DETAILS;
		ScriptsIncluder::registerScript('DebugReport.js', array(
				'dirUrl' => SG_POPUP_JS_URL,
				'dep' => array('jquery'),
				'ver' => SG_POPUP_VERSION,
				'inFooter' => true
			)
		);
		ScriptsIncluder::enqueueScript('DebugReport.js');
		if (version_compare($wp_version, '4.5', '>')){
			/* after wp 4.5 version */
			ScriptsIncluder::addInlineScripts('DebugReport.js', 'var SGPB_DEBUG_POPUP_BUILDER_DETAILS = ' .json_encode($SGPB_DEBUG_POPUP_BUILDER_DETAILS).';');
		} else {
			/* since wp 4.5 version */
			ScriptsIncluder::localizeScript('DebugReport.js', 'SGPB_DEBUG_POPUP_BUILDER_DETAILS', $SGPB_DEBUG_POPUP_BUILDER_DETAILS);
		}
	}

	public function filterOptions($key, $options)
	{
		$result = array();
		if (empty($options)) {
			return $result;
		}

		$optionsShouldBeConverted = array(
			'options',
			'events',
			'conditions',
			'targets'
		);

		if (!in_array($key, $optionsShouldBeConverted)) {
			return $options;
		}
		foreach ($options as $optionKey => $optionValue) {
			// when $key is 'events' or 'conditions'
			if (!is_array($optionValue) && $key != 'options') {
				$result[$optionKey]['name'] = $optionValue;
			}
			if (isset($optionValue['param'])) {
				$result[$optionKey]['name'] = $optionValue['param'];
			}
			if (isset($optionValue['value'])) {
				$result[$optionKey]['value'] = $optionValue['value'];
			}
			if (isset($optionValue['operator'])) {
				if ($optionValue['operator'] == '==') {
					$optionValue['operator'] = 'is';
				}
				else if ($optionValue['operator'] == '!=') {
					$optionValue['operator'] == 'is not';
				}
				$result[$optionKey]['operator'] = $optionValue['operator'];
			}

			if ($key == 'options') {
				// popup limitation
				if ($optionKey == 'sgpb-show-popup-same-user') {
					$result[]['popupLimitationCount'] = $options['sgpb-show-popup-same-user-count'];
					$result[]['popupLimitatioExpiry'] = $options['sgpb-show-popup-same-user-expiry'];
					if (isset($options['sgpb-show-popup-same-user-page-level'])) {
						$result[]['pageLevelCookie'] = $options['sgpb-show-popup-same-user-page-level'];
					}
				}
			}
		}

		return $result;
	}

	public function updateCookiesReportForCurrentPopup($popupId = 0)
	{
		global $SGPB_DEBUG_POPUP_BUILDER_DETAILS;

		foreach ($_COOKIE as $cookieName => $cookieValue) {
			if (strpos($cookieName, (string)$popupId) != false) {
				if (isset($SGPB_DEBUG_POPUP_BUILDER_DETAILS[$popupId]['cookies'][0]['name']) && $SGPB_DEBUG_POPUP_BUILDER_DETAILS[$popupId]['cookies'][0]['name'] == $cookieName) {
					continue;
				}
				$SGPB_DEBUG_POPUP_BUILDER_DETAILS[$popupId]['cookies'][] = array('name' => $cookieName, 'value' => 'exists');
			}
		}
	}

}
