<?php
use sgpb\AdminHelper;

class SgpbPopupExtensionRegister
{
	public static function register($pluginName, $classPath, $className, $options = array())
	{
		if (is_multisite() && is_network_admin()) {
			global $wp_version;

			if ($wp_version > '4.6.0') {
				$blogs = get_sites();
			}
			else {
				$blogs = wp_get_sites();
			}

			foreach ($blogs as $blog) {
				/*  $blog -> List of WP_Site objects for wp > 4.6 otherwise, an associative array of WP_Site data as arrays. */
				$blogId = is_object($blog)?$blog->blog_id:$blog['blog_id'];
				switch_to_blog($blogId);
				self::registerPlugin($pluginName, $classPath, $className, $options);
			}
			return;
		}

		self::registerPlugin($pluginName, $classPath, $className, $options);
	}

	private static function registerPlugin($pluginName, $classPath, $className, $options = array())
	{
		$registeredData = array();
		$registeredPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);

		if (!empty($registeredPlugins)) {
			$registeredData = $registeredPlugins;
			$registeredData = json_decode($registeredData, true);
		}

		if (empty($classPath) || empty($className)) {
			if(!empty($registeredData[$pluginName])) {
				/*Delete the plugin from the registered plugins' list if the class name or the class path is empty.*/
				unset($registeredData[$pluginName]);
				AdminHelper::updateOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS, $registeredData);
			}

			return;
		}
		$classPath = str_replace(SG_POPUP_PLUGIN_PATH, '', $classPath);
		$pluginData['classPath'] = $classPath;
		$pluginData['className'] = $className;
		$pluginData['options'] = $options;

		$registeredData[$pluginName] = $pluginData;
		$registeredData = json_encode($registeredData);

		AdminHelper::updateOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS, $registeredData);
		// it seems we have an inactive extension now
		AdminHelper::updateOption('SGPB_INACTIVE_EXTENSIONS', 'inactive');

		do_action('sgpb_extension_activation_hook', $pluginData);
	}

	private static function isPluginActive($plugin)
	{
		$activePlugins = (array)AdminHelper::getOption('active_plugins', array(1));
		return in_array($plugin, $activePlugins, true);
	}

	public static function remove($pluginName)
	{
		if (is_multisite() && is_network_admin()) {
			global $wp_version;

			if ($wp_version > '4.6.0') {
				$blogs = get_sites();
			}
			else {
				$blogs = wp_get_sites();
			}

			foreach ($blogs as $blog) {
				/*  $blog -> List of WP_Site objects for wp > 4.6 otherwise, an associative array of WP_Site data as arrays. */
				$blogId = is_object($blog)?$blog->blog_id:$blog['blog_id'];
				switch_to_blog($blogId);
				if (!self::isPluginActive($pluginName)) {
					self::removePlugin($pluginName);
				}
				restore_current_blog();
			}
			return;
		}

		self::removePlugin($pluginName);
	}

	private static function removePlugin($pluginName)
	{
		$registeredPlugins = AdminHelper::getOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS);

		if (!$registeredPlugins) {
			return false;
		}

		$registeredData = json_decode($registeredPlugins, true);

		if(empty($registeredData)) {
			return false;
		}

		if (empty($registeredData[$pluginName])) {
			return false;
		}
		unset($registeredData[$pluginName]);
		$registeredData = json_encode($registeredData);

		AdminHelper::updateOption(SGPB_POPUP_BUILDER_REGISTERED_PLUGINS, $registeredData);

		return true;
	}
}
