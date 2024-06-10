<?php
namespace sgpb;
use \WP_Query;

class PopupLoader
{
	private static $instance;
	private $loadablePopups = array();
	private $loadablePopupsIds = array();

	private function __construct()
	{
		require_once(ABSPATH.'wp-admin/includes/plugin.php');
	}

	public static function instance() {

		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function setLoadablePopupsIds($loadablePopupsIds)
	{
		$this->loadablePopupsIds = $loadablePopupsIds;
	}

	public function getLoadablePopupsIds()
	{
		return $this->loadablePopupsIds;
	}

	public function addLoadablePopup($popupObj)
	{
		$this->loadablePopups[] = $popupObj;
	}

	public function setLoadablePopups($loadablePopups)
	{
		$this->loadablePopups = $loadablePopups;
	}

	public function getLoadablePopups()
	{
		return $this->loadablePopups;
	}

	public function addPopupFromUrl($popupsToLoad)
	{
		global $wp;
		$currentUrl = home_url( $wp->request );
		$currentUrl = strpos($currentUrl, '/popupbuilder/');
		if (isset($_GET['sg_popup_id']) || isset($_GET['sg_popup_preview_id']) || $currentUrl !== false) {
			$args = array();
			$previewPopups = array();
			$getterId = isset($_GET['sg_popup_id']) ? (int)sanitize_text_field($_GET['sg_popup_id']) : 0;
			$previewedPopupId = isset($_GET['sg_popup_preview_id']) ? (int)sanitize_text_field($_GET['sg_popup_preview_id']) : 0;
			if (isset($_GET['sg_popup_preview_id'])) {
				$getterId = $previewedPopupId;
				$args['is-preview'] = true;
			}
			if (function_exists('sgpb\sgpGetCorrectPopupId')) {
				$getterId = sgpGetCorrectPopupId($getterId);
			}
			if ($currentUrl !== false) {
				$getterId = $previewedPopupId;
				if (isset($_GET['preview_id'])) {
					$getterId = (int)sanitize_text_field($_GET['preview_id']);
				}
			}

			$popupFromUrl = SGPopup::find($getterId, $args);
			if (!empty($popupFromUrl)) {
				$defaultEvent = array();
				$customDelay = $popupFromUrl->getOptionValue('sgpb-popup-delay');
				$defaultEvent[] = \SgpbDataConfig::websiteDefaultConfigs()['events'][0];
				$defaultEvent[0]['value'] = 0;
				if ($customDelay) {
					$defaultEvent[0]['value'] = $customDelay;
				}
				$popupFromUrl->setEvents($defaultEvent);
				$popupsToLoad[] = $popupFromUrl;
				$previewPopups[] = $popupFromUrl;
				if (isset($_GET['sg_popup_preview_id'])) {
					$popupsToLoad = $previewPopups;
				}
			}
		}

		return $popupsToLoad;
	}

	public function loadPopups()
	{
		$foundPopup = array();
		if (is_preview()) {
			global $post;
			$foundPopup = $post;
		}
		if (!empty($foundPopup)) {
			if (isset($foundPopup->post_type) && $foundPopup->post_type == SG_POPUP_POST_TYPE) {
				$events = \SgpbDataConfig::websiteDefaultConfigs()['events'][0];
				$targets = array(\SgpbDataConfig::websiteDefaultConfigs()['target']);
				// for any targets preview popup should open
				if (!empty($targets[0][0])) {
					$targets[0][0]['param'] = 'post_all';
				}

				$popup = SGPopup::find($foundPopup);
				if (empty($popup)) {
					return;
				}
				$popup->setTarget($targets);
				$popup->setEvents($events);

				$this->addLoadablePopup($popup);
				$this->doGroupFiltersPopups();
				$popups = $this->getLoadablePopups();
				$scriptsLoader = new ScriptsLoader();
				$scriptsLoader->setLoadablePopups($popups);
				$scriptsLoader->loadToFooter();
				return;

			}
		}

		$popupBuilderPosts = new WP_Query(
			array(
				'post_type'      => SG_POPUP_POST_TYPE,
				'posts_per_page' => -1
			)
		);

		// We check all the popups one by one to realize whether they might be loaded or not.
		while ($popupBuilderPosts->have_posts()) {
			$popupBuilderPosts->next_post();
			$popupPost = $popupBuilderPosts->post;
			$popup = SGPopup::find($popupPost);
			if (empty($popup) || !is_object($popup)) {
				continue;
			}

			if (($popup->getOptions()['sgpb-is-active'] && $popup->allowToLoad()) || (is_preview() && get_post_type() == SG_POPUP_POST_TYPE)) {
				$this->addLoadablePopup($popup);
			}
		}

		$this->doGroupFiltersPopups();
		$popups = $this->getLoadablePopups();

		$scriptsLoader = new ScriptsLoader();
		$scriptsLoader->setLoadablePopups($popups);
		$scriptsLoader->loadToFooter();
	}
	public function loadPopupAjax($popup)
	{
		$scriptsLoader = new ScriptsLoader();
		$scriptsLoader->setLoadablePopups([$popup]);
		$scriptsLoader->setIsAjax(true);
		$scriptsLoader->loadToFooter(true);
		$data = [
			'scriptsAndStyles' => $scriptsLoader->getScriptsAndStylesForAjax(),
			'footerContent' => $scriptsLoader->getFooterContentAjax(),
		];
		return $data;
	}

	private function doGroupFiltersPopups()
	{
		$popups = $this->getLoadablePopups();
		$popups = $this->addPopupFromUrl($popups);
		$groupObj = new PopupGroupFilter();
		$groupObj->setPopups($popups);
		$popups = $groupObj->filter();
		$this->setLoadablePopups($popups);
	}
}
