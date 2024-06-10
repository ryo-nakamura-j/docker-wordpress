<?php
namespace sgpb;
/**
 * Popup Builder Style
 *
 * @since 2.5.6
 *
 * detect and include popup styles to the admin pages
 *
 */
class Style
{
	public static function enqueueStyles($hook)
	{
		global $post;
		global $post_type;
		$pageName = $hook;
		$styles = array();
		$popupType = AdminHelper::getCurrentPopupType();
		$currentPostType = AdminHelper::getCurrentPostType();

		if($hook == SG_POPUP_POST_TYPE.'_page_'.SG_POPUP_POST_TYPE) {
			$pageName = 'popupType';
		}
		else if (($hook == 'post-new.php' || $hook == 'post.php') && $currentPostType == SG_POPUP_POST_TYPE) {
			$pageName = 'editpage';
		}
		else if ($hook == 'edit.php' && !empty($currentPostType) && $currentPostType == SG_POPUP_POST_TYPE) {
			$pageName = 'popupspage';
		}
		else if ($hook == SG_POPUP_POST_TYPE.'_page_'.SG_POPUP_SUBSCRIBERS_PAGE) {
			$pageName = SG_POPUP_SUBSCRIBERS_PAGE;
		}

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

			if (!class_exists($pluginData['className'])) {
				continue;
			}

			$classObj = new $pluginData['className']();

			if (!$classObj instanceof \SgpbIPopupExtension) {
				continue;
			}
			$args  = array(
				'popupType' => $popupType
			);
			$styleData = $classObj->getStyles($pageName , $args);
			if (!empty($styleData['cssFiles'])) {
				$styles[] = $styleData;
			}
		}

		if (empty($styles)) {
			return;
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
				$inFooter = (!empty($cssFile['inFooter'])) ? $cssFile['inFooter'] : '';;

				ScriptsIncluder::registerStyle($cssFile['filename'], array(
						'dirUrl'=> $dirUrl,
						'dep' => $dep,
						'ver' => $ver,
						'inFooter' => $inFooter
					)
				);
				ScriptsIncluder::enqueueStyle($cssFile['filename']);
			}
		}

		if ($hook == SG_POPUP_POST_TYPE.'_page_'.SG_POPUP_POST_TYPE) {
			ScriptsIncluder::enqueueStyle('popupAdminStyles.css');
		}
	}
}
