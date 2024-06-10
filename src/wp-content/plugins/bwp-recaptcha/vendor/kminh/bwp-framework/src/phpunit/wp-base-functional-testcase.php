<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

/**
 * @author Khang Minh <contact@betterwp.net>
 */
abstract class BWP_Framework_PHPUnit_WP_Base_Functional_TestCase extends WP_UnitTestCase
{
	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Get the main plugin under test
	 *
	 * @return array
	 */
	abstract public function get_plugin_under_test();

	/**
	 * Get a list of extra plugins that should be loaded and activated for testing
	 *
	 * This can include fxitures if needed.
	 *
	 * @return array
	 */
	public function get_extra_plugins()
	{
		return array();
	}

	/**
	 * Get a list of all plugins, including the plugin under test and extra ones
	 *
	 * @return array
	 */
	public function get_all_plugins()
	{
		return array_merge(
			$this->get_plugin_under_test(),
			$this->get_extra_plugins()
		);
	}

	/**
	 * Prepare plugin directories
	 *
	 * This should create symlinks to the plugins' folders from
	 * `wp-content/plugins` directory so that they can be activated and used
	 * later on
	 */
	public function prepare_plugin_directories()
	{
		global $_core_dir;

		$plugins = $this->get_all_plugins();

		foreach ($plugins as $plugin_file => $plugin_path) {
			$target  = dirname($plugin_file);
			$symlink = $_core_dir . '/wp-content/plugins/' . dirname($plugin_path);

			exec('ln -sfn ' . escapeshellarg($target) . ' ' . escapeshellarg($symlink));
		}
	}

	/**
	 * Load all required plugins for current process
	 *
	 * This should be called by extending testcases explicitly.
	 * This only loads actual plugins, not fixtures.
	 */
	public function load_plugins_for_current_process()
	{
		$plugins = $this->get_all_plugins();

		foreach ($plugins as $plugin_file => $plugin_path) {
			// dont include fixtures because they are not actually plugins
			if (stripos($plugin_file, 'fixtures') !== false) {
				continue;
			}

			include_once $plugin_file;
		}
	}

	/**
	 * This should be used explicitly by extending testcases
	 */
	protected function load_fixtures($file_name = null)
	{
		$plugins = $this->get_extra_plugins();

		foreach ($plugins as $plugin_file => $plugin_path) {
			// only load fixtures
			if (stripos($plugin_file, 'fixtures') === false) {
				continue;
			}

			// only load correct fixture, if specified
			if ($file_name && stripos($plugin_file, $file_name) === false) {
				continue;
			}

			include_once $plugin_file;
		}
	}

	protected function bootstrap_plugin()
	{
		global $_tests_dir;

		// prepare plugin directories for the current session and any following requests
		$this->prepare_plugin_directories();

		if (!function_exists('tests_add_filter')) {
			require_once $_tests_dir . '/includes/functions.php';

			// load all plugins to use within this process
			// we need to do this here to make sure loaded plugins can make
			// use of WordPress's init action. If a testcase needs a different
			// set of plugins it should be run in a separate process because
			// this is called only once.
			tests_add_filter('pre_option_active_plugins', array($this, 'get_all_plugins'));

			// bootstrap WordPress itself, this should provide the WP environment and
			// drop/recreate tables
			require $_tests_dir . '/includes/bootstrap.php';

			// mark as installed
			touch($_tests_dir . '/installed.lock');
		}
	}

	/**
	 * Get current WP version
	 *
	 * If a WP version is provided as the first parameter, check if the
	 * current WP version is greater than or equal to that provided version
	 *
	 * @return mixed
	 */
	protected static function get_wp_version($version = '')
	{
		$current_version = get_bloginfo('version');
		return !$version ? $current_version : version_compare($current_version, $version, '>=');
	}

	/**
	 * @return string
	 */
	protected static function uniqid()
	{
		return md5(uniqid(rand(), true));
	}

	/**
	 * Prepare default values including options and active plugins
	 */
	protected function prepare_default_values()
	{
		// to be implemented in child testcases
	}
}
