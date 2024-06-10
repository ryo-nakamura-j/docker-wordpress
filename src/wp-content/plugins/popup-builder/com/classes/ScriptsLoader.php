<?php

namespace sgpb;

// load popups data's from popups object
class ScriptsLoader
{
	// all loadable popups objects
	private $loadablePopups = array();
	private $isAdmin = false;
	private $isAjax = false;
	private $scriptsAndStylesForAjax;
	private $footerContentAjax;
	private static $alreadyLoadedPopups = array();

	public function setLoadablePopups($loadablePopups)
	{
		$this->loadablePopups = $loadablePopups;
	}

	public function getLoadablePopups()
	{
		return apply_filters('sgpbLoadablePopups', $this->loadablePopups);
	}

	public function setIsAdmin($isAdmin)
	{
		$this->isAdmin = $isAdmin;
	}

	public function getIsAdmin()
	{
		return $this->isAdmin;
	}

	/* for ajax calls */
	public function setIsAjax($isAjax)
	{
		$this->isAjax = $isAjax;
	}

	public function getIsAjax()
	{
		return $this->isAjax;
	}

	public function setFooterContentAjax($footerContentAjax)
	{
		$this->footerContentAjax = $footerContentAjax;
	}

	public function getFooterContentAjax()
	{
		return $this->footerContentAjax;
	}

	/**
	 * Get encoded popup options
	 *
	 * @since 3.0.4
	 *
	 * @param object $popup
	 *
	 * @return array|mixed|string $popupOptions
	 */
	private function getEncodedOptionsFromPopup($popup)
	{
		$extraOptions = $popup->getExtraRenderOptions();
		$popupOptions = $popup->getOptions();
		$popupOptions = apply_filters('sgpbPopupRenderOptions', $popupOptions);
		$popupCondition = $popup->getConditions();

		$popupOptions = array_merge($popupOptions, $extraOptions);
		$popupOptions['sgpbConditions'] = apply_filters('sgpbRenderCondtions',  $popupCondition);
		// JSON_UNESCAPED_UNICODE does not exist since 5.4.0
		if (PHP_VERSION < '5.4.0'){
			$popupOptions = json_encode($popupOptions);
		} else {
			$popupOptions = json_encode($popupOptions,JSON_UNESCAPED_UNICODE);
		}
		return base64_encode($popupOptions);
	}

	// load popup scripts and styles and add popup data to the footer
	public function loadToFooter($isFromAjax = false)
	{
		$popups = $this->getLoadablePopups();
		$currentPostType = AdminHelper::getCurrentPostType();
		global $wp;
		$currentUrl = home_url( $wp->request );
		$currentUrl = strpos($currentUrl, '/popupbuilder/');

		// during preview wp request is empty that is why we check query_string
		if (is_preview()) {
			$currentUrl = strrpos($wp->query_string, 'popupbuilder');
		}

		if ($currentPostType == SG_POPUP_POST_TYPE && $currentUrl === false) {
			return false;
		}

		if (empty($popups)) {
			return false;
		}

		global $post;
		$postId = 0;

		if (!empty($post)) {
			$postId = $post->ID;
		}

		if ($this->getIsAdmin()) {
			$this->loadToAdmin();
			return true;
		}
		$footerContentAjax = '';

		foreach ($popups as $popup) {
			$isActive = $popup->isActive();
			if (!$isActive) {
				continue;
			}

			$popupId = $popup->getId();

			$popupContent = apply_filters('sgpbPopupContentLoadToPage', $popup->getPopupTypeContent(), $popupId);

			$events = $popup->getPopupAllEvents($postId, $popupId, $popup);

			// if popup's data has already loaded into the page with the same event
			if (isset(self::$alreadyLoadedPopups[$popupId])) {
				if (self::$alreadyLoadedPopups[$popupId] == $events) {
					continue;
				}
			}
			$canContinue = false;
			foreach ($events as $event) {
				if (isset($event['param'])) {
					if (isset(self::$alreadyLoadedPopups[$popupId])) {
						if (self::$alreadyLoadedPopups[$popupId] == $event['param']) {
							$canContinue = true;
						}
					}
				} else {
					if (isset(self::$alreadyLoadedPopups[$popupId])) {
						if (false !== array_search($event, array_column(self::$alreadyLoadedPopups[$popupId], 'param'))) {
							$canContinue = true;
						}
					}
				}
			}
			if ($canContinue) {
				continue;
			}
			self::$alreadyLoadedPopups[$popupId] = $events;
			$events = json_encode($events);
			$currentUseOptions = $popup->getOptions();
			$extraContent = apply_filters('sgpbPopupExtraData', $popupId, $currentUseOptions);

			$popupOptions = $this->getEncodedOptionsFromPopup($popup);
			$popupOptions = apply_filters('sgpbLoadToFooterOptions', $popupOptions);
			if ($isFromAjax) {
				$footerPopupContent = '<div class="sgpb-main-popup-data-container-'.esc_attr($popupId).'" style="position:fixed;opacity: 0;filter: opacity(0%);transform: scale(0);">
							<div class="sg-popup-builder-content" id="sg-popup-content-wrapper-'.esc_attr($popupId).'" data-id="'.esc_attr($popupId).'" data-events="'.esc_attr($events).'" data-options="'.esc_attr($popupOptions).'">
								<div class="sgpb-popup-builder-content-'.esc_attr($popupId).' sgpb-popup-builder-content-html">'.$popupContent.'</div>
							</div>
						  </div>';
				$footerPopupContent .= $extraContent;
				$footerContentAjax .= $footerPopupContent;
			} else {
				add_action('wp_footer', function() use ($popupId, $events, $popupOptions, $popupContent, $extraContent) {
					$footerPopupContent = '<div class="sgpb-main-popup-data-container-'.esc_attr($popupId).'" style="position:fixed;opacity: 0;filter: opacity(0%);transform: scale(0);">
							<div class="sg-popup-builder-content" id="sg-popup-content-wrapper-'.esc_attr($popupId).'" data-id="'.esc_attr($popupId).'" data-events="'.esc_attr($events).'" data-options="'.esc_attr($popupOptions).'">
								<div class="sgpb-popup-builder-content-'.esc_attr($popupId).' sgpb-popup-builder-content-html">'.$popupContent.'</div>
							</div>
						  </div>';
					$footerPopupContent .= $extraContent;
					echo $footerPopupContent;
				});
			}
		}

		$this->includeScripts();
		$this->includeStyles();
		if ($isFromAjax){
			$this->setFooterContentAjax($footerContentAjax);
		}
	}

	public function loadToAdmin()
	{
		$popups = $this->getLoadablePopups();

		foreach ($popups as $popup) {
			$popupId = $popup->getId();

			$events = array();

			$events = json_encode($events);

			$popupOptions = $this->getEncodedOptionsFromPopup($popup);

			$popupContent = apply_filters('sgpbPopupContentLoadToPage', $popup->getPopupTypeContent(), $popupId);

			add_action('admin_footer', function() use ($popupId, $events, $popupOptions, $popupContent) {
				$footerPopupContent = '<div style="position:absolute;top: -999999999999999999999px;">
							<div class="sg-popup-builder-content" id="sg-popup-content-wrapper-'.$popupId.'" data-id="'.esc_attr($popupId).'" data-events="'.esc_attr($events).'" data-options="'.esc_attr($popupOptions).'">
								<div class="sgpb-popup-builder-content-'.esc_attr($popupId).' sgpb-popup-builder-content-html">'.$popupContent.'</div>
							</div>
						  </div>';

				echo wp_kses($footerPopupContent, AdminHelper::allowed_html_tags());
			});
		}
		$this->includeScripts();
		$this->includeStyles();

	}

	private function includeScripts()
	{
		global $post;
		global $wp_version;
		$popups = $this->getLoadablePopups();
		$registeredPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);

		if (!$registeredPlugins) {
			return;
		}
		$registeredPlugins = json_decode($registeredPlugins, true);

		if (empty($registeredPlugins)) {
			return;
		}

		foreach ($registeredPlugins as $pluginName => $pluginData) {

			if (!is_plugin_active($pluginName)) {
				continue;
			}

			if (empty($pluginData['classPath']) || empty($pluginData['className'])) {
				continue;
			}
			$classPath = $pluginData['classPath'];
			$classPath = SG_POPUP_PLUGIN_PATH.$classPath;

			if (!file_exists($classPath)) {
				continue;
			}

			require_once($classPath);

			$classObj = new $pluginData['className']();

			if (!$classObj instanceof \SgpbIPopupExtension) {
				continue;
			}

			$scriptData = $classObj->getFrontendScripts(
				$post, array(
					'popups' => $popups
				)
			);

			$scripts[] = $scriptData;
		}

		if (empty($scripts)) {
			return;
		}
		if ($this->getIsAjax()){
			$this->scriptsAndStylesForAjax['scripts'] = $scripts;
		}
		foreach ($scripts as $script) {
			if (empty($script['jsFiles'])) {
				continue;
			}

			foreach ($script['jsFiles'] as $jsFile) {

				if (empty($jsFile['folderUrl'])) {
					if(isset($jsFile['filename'])){
						wp_enqueue_script($jsFile['filename']);
					}
					continue;
				}

				$dirUrl = $jsFile['folderUrl'];
				$dep = (!empty($jsFile['dep'])) ? $jsFile['dep'] : '';
				$ver = (!empty($jsFile['ver'])) ? $jsFile['ver'] : '';
				$inFooter = (!empty($jsFile['inFooter'])) ? $jsFile['inFooter'] : '';

				ScriptsIncluder::registerScript($jsFile['filename'], array(
						'dirUrl' => $dirUrl,
						'dep' => $dep,
						'ver' => $ver,
						'inFooter' => $inFooter
					)
				);
				ScriptsIncluder::enqueueScript($jsFile['filename']);
			}

			if (empty($script['localizeData'])) {
				continue;
			}

			$localizeData = $script['localizeData'];

			if (!empty($localizeData[0])) {
				foreach ($localizeData as $valueData) {
					if (empty($valueData)) {
						continue;

					}
					if (version_compare($wp_version, '4.5', '>')){
						/* after wp 4.5 version */
						ScriptsIncluder::addInlineScripts($valueData['handle'], 'var '.$valueData['name'].' = ' .json_encode($valueData['data']).';');
					} else {
						/* since wp 4.5 version */
						ScriptsIncluder::localizeScript($valueData['handle'], $valueData['name'], $valueData['data']);
					}

				}
			}
		}
	}

	private function includeStyles()
	{
		global $post;
		$styles = array();
		$popups = $this->getLoadablePopups();
		$registeredPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);

		if (!$registeredPlugins) {
			return;
		}
		$registeredPlugins = json_decode($registeredPlugins, true);

		if (empty($registeredPlugins)) {
			return;
		}

		foreach ($registeredPlugins as $pluginName => $pluginData) {

			if (!is_plugin_active($pluginName)) {
				continue;
			}

			if (empty($pluginData['classPath']) || empty($pluginData['className'])) {
				continue;
			}

			$classPath = $pluginData['classPath'];
			$classPath = SG_POPUP_PLUGIN_PATH.$classPath;

			if (!file_exists($classPath))  {
				continue;
			}

			require_once($classPath);

			$classObj = new $pluginData['className']();

			if (!$classObj instanceof \SgpbIPopupExtension) {
				continue;
			}

			$scriptData = $classObj->getFrontendStyles(
				$post , array(
					'popups' => $popups
				)
			);

			$styles[] = $scriptData;
		}

		if (empty($styles)) {
			return;
		}
		if ($this->getIsAjax()){
			$this->scriptsAndStylesForAjax['styles'] = $styles;
		}
		foreach ($styles as $style) {

			if (empty($style['cssFiles'])) {
				continue;
			}

			foreach ($style['cssFiles'] as $cssFile) {

				if (empty($cssFile['folderUrl'])) {
					ScriptsIncluder::enqueueStyle($cssFile['filename']);
					continue;
				}

				$dirUrl = $cssFile['folderUrl'];
				$dep = (!empty($cssFile['dep'])) ? $cssFile['dep'] : '';
				$ver = (!empty($cssFile['ver'])) ? $cssFile['ver'] : '';
				$inFooter = (!empty($cssFile['inFooter'])) ? $cssFile['inFooter'] : '';

				ScriptsIncluder::registerStyle($cssFile['filename'], array(
						'dirUrl' => $dirUrl,
						'dep' => $dep,
						'ver' => $ver,
						'inFooter' => $inFooter
					)
				);
				ScriptsIncluder::enqueueStyle($cssFile['filename']);
			}
		}
	}

	public function getScriptsAndStylesForAjax() {
		return $this->scriptsAndStylesForAjax;
	}
}
