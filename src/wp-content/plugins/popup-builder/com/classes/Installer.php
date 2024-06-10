<?php
namespace sgpb;
use \SgpbPopupExtensionRegister;

class Installer
{
	public static function createTables($tables, $blogId = '')
	{
		global $wpdb;
		if (empty($tables)) {
			return false;
		}

		foreach ($tables as $table) {
			$createTable = 'CREATE TABLE IF NOT EXISTS ';
			$createTable .= $wpdb->prefix.$blogId;
			$createTable .= $table;
			$wpdb->query($createTable);
		}

		return true;
	}

	private static function getAllNeededTables()
	{
		$tables = array();
		global $SGPB_POPUP_TYPES;
		$popupTypes = $SGPB_POPUP_TYPES['typeName'];

		if (empty($popupTypes)) {
			return $tables;
		}

		foreach ($popupTypes as $popupTypeKey => $popupTypeLevel) {
			if (SGPB_POPUP_PKG >= $popupTypeLevel) {
				$className = ucfirst($popupTypeKey).'Popup';

				if (file_exists(SG_POPUP_CLASSES_POPUPS_PATH.$className.'.php')) {

					require_once(SG_POPUP_CLASSES_POPUPS_PATH.$className.'.php');

					$className = __NAMESPACE__.'\\'.$className;

					$popupTables = $className::getTablesSql();

					if (empty($popupTables)) {
						continue;
					}

					foreach ($popupTables as $tableSql) {
						$tables[] = $tableSql;
					}
				}
			}

		}

		return $tables;
	}

	public static function install()
	{
		$tables = self::getAllNeededTables();
		$filteredTables = apply_filters('sgpbTablesInstall', $tables);

		if (get_option('sgpb-dont-delete-data') === false) {
			// Initial option insert
			update_option('sgpb-dont-delete-data', 1);
		}

		self::createTables($filteredTables);

		self::setupInstallationsDateConfig($filteredTables);

		// get_current_blog_id() == 1 When plugin activated inside the child of multisite instance
		if (is_multisite() && get_current_blog_id() == 1) {
			global $wp_version;

			if ($wp_version > '4.6.0') {
				$sites = get_sites();
			}
			else {
				$sites = wp_get_sites();
			}

			foreach ($sites as $site) {

				if ($wp_version > '4.6.0') {
					$blogId = $site->blog_id.'_';
				}
				else {
					$blogId = $site['blog_id'].'_';
				}
				// blog Id 1 for multisite main site
				if ($blogId != 1) {
					self::createTables($filteredTables, $blogId);
				}
			}
		}

		// install extensions
		if (SGPB_POPUP_PKG != SGPB_POPUP_PKG_FREE) {
			$obj = new PopupExtensionActivator();
			$obj->install();
		}
	}

	public static function setupInstallationsDateConfig()
	{
		update_option('sgpbUnsubscribeColumnFixed', 1);
		$usageDays = get_option('SGPBUsageDays');
		if (!$usageDays) {
			update_option('SGPBUsageDays', 0);

			$timeDate = new \DateTime('now');
			$installTime = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('SGPBInstallDate', $installTime);
			$timeDate->modify('+'.SGPB_REVIEW_POPUP_PERIOD.' day');

			$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('SGPBOpenNextTime', $timeNow);
		}

		$maxPopupCount = get_option('SGPBMaxOpenCount');
		if (!$maxPopupCount) {
			update_option('SGPBMaxOpenCount', SGPB_ASK_REVIEW_POPUP_COUNT);
		}
	}

	public static function uninstall()
	{
		delete_option('sgpb-user-roles');

		// When don't delete data if don't delete data option was unchecked
		if (!get_option('sgpb-dont-delete-data')) {
			return false;
		}
		delete_option('sgpb-dont-delete-data');
		delete_option('sgpb-new-subscriber');
		delete_option('sgpbUnsubscribeColumnFixed');
		delete_option('sgpbActivateExtensions');
		delete_option('sgpbExtensionsInfo');
		delete_option('sgpb-enable-debug-mode');
		delete_option('sgpb-disable-analytics-general');

		// Trigger popup data delete action
		do_action('sgpbDeletePopupData');

		self::deletePopups();
		self::deleteCustomTables();

		if (is_multisite()) {
			global $wp_version;
			if ($wp_version > '4.6.0') {
				$sites = get_sites();
			}
			else {
				$sites = wp_get_sites();
			}

			foreach ($sites as $site) {
				if ($wp_version > '4.6.0') {
					$blogId = $site->blog_id.'_';
				}
				else {
					$blogId = $site['blog_id'].'_';
				}
				self::deleteCustomTables($blogId);
			}
		}

		return true;
	}

	/**
	 * Delete Taxonomy by name
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public static function deleteCustomTerms($taxonomy)
	{
		global $wpdb;

		$customTermsQuery = 'SELECT t.name, t.term_id
			FROM '.$wpdb->terms . ' AS t
			INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt
			ON t.term_id = tt.term_id
			WHERE tt.taxonomy = "'.esc_sql($taxonomy).'"';

		$terms = $wpdb->get_results($customTermsQuery);

		$terms = apply_filters('sgpbDeleteTerms', $terms);

		foreach ($terms as $term) {
			if (empty($term)) {
				continue;
			}
			wp_delete_term($term->term_id, $taxonomy);
		}
	}

	/**
	 * Delete all popup builder post types posts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	private static function deletePopups()
	{
		$popups = get_posts(
			array(
				'post_type' => SG_POPUP_POST_TYPE,
				'post_status' => array(
					'publish',
					'pending',
					'draft',
					'auto-draft',
					'future',
					'private',
					'inherit',
					'trash'
				)
			)
		);
		$popups = apply_filters('sgpbDeletePopups', $popups);

		foreach ($popups as $popup) {
			if (empty($popup)) {
				continue;
			}
			wp_delete_post($popup->ID, true);
		}
	}

	private static function deleteCustomTables($blogId = '')
	{
		$allTableNames = self::getAllTableNames();

		if (empty($allTableNames)) {
			return false;
		}
		global $wpdb;

		foreach ($allTableNames as $tableName) {
			$deleteTable = $wpdb->prefix.$blogId.$tableName;
			$deleteTableSql = 'DROP TABLE '.$deleteTable;

			$wpdb->query($deleteTableSql);
		}

		return true;
	}

	/**
	 * It's acquire all popup types installed table names
	 *
	 * @since 1.0.0
	 *
	 * @return array $popup types table names
	 *
	 */
	private static function getAllTableNames()
	{
		$tables = array();
		global $SGPB_POPUP_TYPES;
		$popupTypes = $SGPB_POPUP_TYPES['typeName'];

		if (empty($popupTypes)) {
			return $tables;
		}

		require_once(SG_POPUP_CONFIG_PATH.'configPackage.php');

		foreach ($popupTypes as $popupTypeKey => $popupTypeLevel) {
			if (SGPB_POPUP_PKG >= $popupTypeLevel) {
				$className = ucfirst($popupTypeKey).'Popup';

				if (file_exists(SG_POPUP_CLASSES_POPUPS_PATH.$className.'.php')) {

					require_once(SG_POPUP_CLASSES_POPUPS_PATH.$className.'.php');

					$className = __NAMESPACE__.'\\'.$className;

					$popupTables = $className::getTableNames();

					if (empty($popupTables)) {
						continue;
					}

					foreach ($popupTables as $tableName) {
						$tables[] = $tableName;
					}
				}
			}

		}

		return $tables;
	}

	public static function registerPlugin()
	{
		$pluginName = SG_POPUP_FILE_NAME;
		$classPath = SG_POPUP_EXTENSION_PATH.'SgpbPopupExtension.php';
		$className = 'SgpbPopupExtension';
		$options = array();

		if (SGPB_POPUP_PKG != SGPB_POPUP_PKG_FREE) {
			$options = array(
				'licence' => array(
					'key' => SG_POPUP_KEY,
					'storeURL' => SG_POPUP_STORE_URL,
					'file' => SG_POPUP_FILE_NAME,
					'itemId' => SGPB_ITEM_ID,
					'itemName' => __('Popup Builder', SG_POPUP_TEXT_DOMAIN),
					'autor' => SG_POPUP_AUTHOR,
					'boxLabel' => __('Popup Builder License', SG_POPUP_TEXT_DOMAIN)
				)
			);
			$options = apply_filters('sgpbRegisterOptions', $options);
		}

		@SgpbPopupExtensionRegister::register($pluginName, $classPath, $className, $options);
	}
}
