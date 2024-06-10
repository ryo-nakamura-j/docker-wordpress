<?php

use \Guzzle\Plugin\Cache\CachePlugin;
use \Symfony\Component\DomCrawler\Crawler;
use \Goutte\Client;

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

/**
 * @author Khang Minh <contact@betterwp.net>
 */
abstract class BWP_Framework_PHPUnit_WP_Functional_TestCase extends BWP_Framework_PHPUnit_WP_Base_Functional_TestCase
{
	/**
	 * @var \Goutte\Client
	 */
	protected static $client;

	/**
	 * @var string
	 */
	protected static $cache_dir;

	/**
	 * @var array
	 */
	protected static $wp_original_options = array();

	/**
	 * @var array
	 */
	protected static $wp_options = array();

	/**
	 * Prepare the WP environment
	 *
	 * This will prepare the environment for the current session as well as any
	 * following requests made to the current test installation using a client
	 * such as a crawler or a browser
	 */
	public function setUp()
	{
		$this->bootstrap_plugin();

		static::prepare_wp_config();
		static::prepare_htaccess_file();
		static::prepare_cache_directory();
		static::prepare_asset_directories();

		parent::setUp();

		// parent::setUp() must be called before this
		$this->prepare_default_values();
	}

	/**
	 * Prepares the WP environment, but for current request only
	 *
	 * This means we don't need to prepare config and htaccess stuff
	 */
	public function setUpForCurrentRequest()
	{
		$this->bootstrap_plugin();

		static::prepare_cache_directory();
		static::prepare_asset_directories();

		parent::setUp();

		// parent::setUp() must be called before this
		$this->prepare_default_values();
	}

	public function tearDown()
	{
		// reset wp options to their original values
		static::set_wp_options(self::$wp_original_options);

		parent::tearDown();
	}

	protected static function prepare_wp_config()
	{
		global $_tests_dir, $_core_dir;

		$wp_config_file          = $_core_dir . '/wp-config.php';
		$wp_config_file_original = $_core_dir . '/wp-config-original.php';

		$wp_config_need_update = false;

		// if config file is missing or its contents are missing
		// wp-settings.php OR the its current contents are for multisite
		// installation add/adjust it so we can browse the test WP installation
		// later on
		if (!file_exists($wp_config_file)) {
			$wp_config_need_update = true;
		} else {
			$wp_config_file_content  = file_get_contents($wp_config_file);

			if (stripos($wp_config_file_content, 'wp-settings.php') === false
				|| stripos($wp_config_file_content, 'WP_ALLOW_MULTISITE') !== false
			) {
				$wp_config_need_update = true;
			}
		}

		if ($wp_config_need_update) {
			$root_dir  = dirname(dirname(__DIR__));
			$wp_config = file_get_contents($root_dir . '/tests/functional/data/wp-config');

			exec('cp -f ' . escapeshellarg($wp_config_file_original) . ' ' . escapeshellarg($wp_config_file));
			exec('echo ' . escapeshellarg($wp_config) . ' >> ' . escapeshellarg($wp_config_file));
		}
	}

	protected static function prepare_htaccess_file()
	{
		global $_core_dir;

		$htaccess_file = $_core_dir . '/.htaccess';

		// remove current htaccess file if any to force tests to manually
		// create htaccess file when needed
		if (file_exists($htaccess_file)) {
			unlink($htaccess_file);
		}
	}

	/**
	 * Prepare a blank cache folder for every test
	 *
	 * @param string $cache_dir
	 */
	protected static function prepare_cache_directory($cache_dir = null)
	{
		global $_core_dir;

		self::$cache_dir = !$cache_dir ? $_core_dir . '/wp-content/cache' : $cache_dir;

		exec('rm -rf ' . self::$cache_dir);
		mkdir(self::$cache_dir);
	}

	/**
	 * Prepare js and css directories if not existed
	 */
	protected static function prepare_asset_directories()
	{
		global $_core_dir;

		$dirs = array(
			'/js', '/css'
		);

		foreach ($dirs as $dir) {
			if (!file_exists($_core_dir . $dir)) {
				mkdir($_core_dir . $dir);
			}
		}
	}

	/**
	 * Set WP options that are used for all tests
	 *
	 * We backup the orignal WP options so we can reset the options in db
	 * after every test
	 */
	protected static function set_wp_default_options()
	{
		$options = static::$wp_options;

		foreach ($options as $key => $value) {
			self::$wp_original_options[$key] = get_option($key);
			self::update_option($key, $value);
		}
	}

	/**
	 * Set WP options that are used for a specific test
	 *
	 * @param array $options an associative array of key => value
	 */
	protected static function set_wp_options(array $options)
	{
		foreach ($options as $key => $value) {
			self::update_option($key, $value);
		}
	}

	/**
	 * Set default options that are used for all tests
	 */
	protected static function set_plugin_default_options()
	{
		// to be implemented by child classes
	}

	/**
	 * Set options used with a specific testcase
	 *
	 * @param string $option_key
	 * @param array $options a subset of options to be set
	 */
	protected static function set_options($option_key, array $options)
	{
		$current_options = get_option($option_key);
		$current_options = $current_options ? $current_options : array();

		self::update_option($option_key, array_merge($current_options, $options));
	}

	/**
	 * Activate specific plugins
	 *
	 * This only affect actual requests made to the test site
	 *
	 * @param array $plugins
	 */
	protected static function activate_plugins(array $plugins)
	{
		self::update_option('active_plugins', $plugins);
	}

	/**
	 * Deactivate plugins
	 *
	 * This only affect actual requests made to the test site
	 *
	 * @param array $plugins optional, default is to deactivate all plugins
	 */
	protected static function deactivate_plugins(array $plugins = array())
	{
		// only deactivate some plugins
		if ($plugins) {
			$active_plugins = get_option('active_plugins');
			$active_plugins = array_diff($active_plugins, $plugins);
			self::update_option('active_plugins', $active_plugins);
			return;
		}

		// deactivate all plugins
		self::update_option('active_plugins', array());
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	protected static function update_option($key, $value)
	{
		update_option($key, $value);
		self::commit_transaction();
	}

	/**
	 * @param string $key
	 */
	protected static function delete_option($key)
	{
		delete_option($key);
		self::commit_transaction();
	}

	/**
	 * Reset users to installed state
	 *
	 * This should not reset the initial 'admin' user
	 */
	protected static function reset_users()
	{
		global $wpdb;

		$wpdb->query("DELETE FROM $wpdb->users WHERE user_login != 'admin'");
		$wpdb->query("ALTER TABLE $wpdb->users AUTO_INCREMENT = 2");

		self::commit_transaction();
	}

	protected static function reset_posts()
	{
		global $wpdb;

		$wpdb->query("TRUNCATE $wpdb->posts");
		$wpdb->query("TRUNCATE $wpdb->postmeta");

		self::commit_transaction();
	}

	protected static function reset_terms()
	{
		global $wpdb;

		$wpdb->query("TRUNCATE $wpdb->term_relationships");
		$wpdb->query("TRUNCATE $wpdb->term_taxonomy");
		$wpdb->query("TRUNCATE $wpdb->terms");

		self::commit_transaction();
	}

	protected static function reset_posts_terms()
	{
		self::reset_terms();
		self::reset_posts();
	}

	protected static function reset_comments()
	{
		global $wpdb;
		$wpdb->query("TRUNCATE $wpdb->comments");
		self::commit_transaction();
	}

	/**
	 * @param bool $use_existing whether to create a new client or use existing if any,
	 *                           default to true
	 * @param bool $use_cache whether to use http cache
	 * @return \Goutte\Client
	 */
	protected static function get_client($use_existing = true, $use_cache = false)
	{
		$client = self::$client && $use_existing ? self::$client : new Client();

		// reset the existing client if not using it
		if (!$use_existing) {
			// use http cache if needed
			if ($use_cache) {
				$cache_plugin = new CachePlugin();
				$client->getClient()->addSubscriber($cache_plugin);
			}

			self::$client = $client;
		}

		// do not verify SSL certificate
		$client->getClient()->setDefaultOption('verify', false);

		return $client;
	}

	/**
	 * @return \Goutte\Client
	 */
	protected static function get_client_clone()
	{
		if (!self::$client) {
			throw new Exception('Must have an existing client first before cloning');
		}

		return clone self::$client;
	}

	/**
	 * Get a Crawler instance from a URL.
	 *
	 * @param string $url
	 * @param array $headers
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected static function get_crawler_from_url($url, array $headers = array())
	{
		$client = self::get_client(false);

		foreach ($headers as $name => $value) {
			$client->setHeader($name, $value);
		}

		return $client->request('GET', $url);
	}

	/**
	 * @param WP_Post $post
	 * @return \Symfony\Component\DomCrawler\Crawler
	 */
	protected static function get_crawler_from_post(WP_Post $post)
	{
		return self::get_crawler_from_url(get_permalink($post));
	}

	/**
	 * Get the requested uri from a Client object.
	 *
	 * @param Client $client
	 * @return string
	 */
	protected static function get_uri_from_client(Client $client)
	{
		return $client->getRequest()->getUri();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function prepare_default_values()
	{
		// activate required plugins for any following requests
		static::activate_plugins(array_values($this->get_all_plugins()));

		static::set_wp_default_options();
		static::set_plugin_default_options();
	}
}
