<?php

namespace sgpb;
/**
 * Allows plugins to use their own update API.
 * Note: This updater is not used for Community/Hosted version of the plugin.
 * This class is included in addons to have an update system in a single file rather than including it in each extension.
*/
class WOOSL_CodeAutoUpdate
{

	// URL to check for updates, this is where the index.php script goes
	private $plugin;
	private $api_url;
	private $slug;
	private $API_VERSION = 1.1;
	private $product_unique_id;
	private $license;
	private $name;
	private $version;

	private $cache_key   = '';
	private $beta        = '';

	function __construct($api_url, $plugin, $product_unique_id, $license, $version)
	{
		$this->api_url           = $api_url;
		$this->slug              = basename($plugin, '.php');
		$this->plugin            = $plugin;
		$this->product_unique_id = $product_unique_id;
		$this->license           = $license;
		$this->name              = plugin_basename($plugin);
		$this->cache_key         = md5(serialize($this->slug.$this->license));
		$this->version           = $version;

		$this->init();
	}

	public function init()
	{
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
		add_filter('plugins_api', array($this, 'plugins_api_filter'), 10, 3);

		remove_action('after_plugin_row_'.$this->name, 'wp_plugin_update_row', 10);
		add_action('after_plugin_row_'.$this->name, array($this, 'show_update_notification'), 10, 2);
	}

	public function getVersionInfoFromCache($cache_key = '')
	{
		if (empty($cache_key)) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option($cache_key);
		if (empty($cache['timeout']) || current_time('timestamp') > $cache['timeout']) {
			return false;
		}

		return json_decode($cache['value']);
	}

	public function updateVersionInfoInCache($value = '', $cache_key = '')
	{
		if (empty($cache_key)) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime('+3 hours', current_time('timestamp')),
			'value'   => json_encode($value)
		);

		update_option($cache_key, $data, 'no');
	}

	private function getVersionInfoFromApi()
	{
		global $wp_version;

		$request = $this->prepare_request('plugin_information');
		$request_uri = $this->api_url.'?'.http_build_query($request , '', '&');

		$response = wp_remote_get($request_uri, array(
			'timeout'     => 20,
			'user-agent'  => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
		));

		$responseBody = wp_remote_retrieve_body($response);
		if (empty($responseBody)) {
			return [];
		}

		$response = json_decode($responseBody)[0];
		if (empty($response)) {
			return [];
		}

		return $this->postprocess_response($response->message);
	}

	private function getVersionInfo()
	{
		$versionInfo = $this->getVersionInfoFromCache();
		if (empty($versionInfo)) {
			$versionInfo = $this->getVersionInfoFromApi();
			$this->updateVersionInfoInCache($versionInfo);
		}

		return $versionInfo;
	}

	public function show_update_notification($file, $plugin)
	{
		if (!current_user_can('update_plugins') || $this->name != $file) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'), 10);
		$update_cache = get_site_transient('update_plugins');
		$update_cache = is_object( $update_cache ) ? $update_cache : new \stdClass();
		if (empty($update_cache->response) || empty($update_cache->response[$this->name])) {
			$version_info = $this->getVersionInfo();
			if (!is_object($version_info)) {
				return;
			}

			if (version_compare($this->version, $version_info->new_version, '<')) {
				$update_cache->response[$this->name] = $version_info;
			}

			$update_cache->last_checked = current_time('timestamp');
			$update_cache->checked[$this->name] = $this->version;
			set_site_transient('update_plugins', $update_cache);
		} else {
			$version_info = $update_cache->response[$this->name];
		}

		// Restore our filter
		add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
		if (!empty($update_cache->response[$this->name]) && version_compare($this->version, $version_info->new_version, '<')) {
			do_action("in_plugin_update_message-{$file}", $plugin, $version_info);
		} else {
			set_site_transient('update_plugins', $update_cache);
		}
	}

	public function check_update($checked_data)
	{
		if (!is_object($checked_data) || !isset($checked_data->response)) {
			return $checked_data;
		}

		$version_info = $this->getVersionInfo();
		if (version_compare($this->version, $version_info->new_version, '<')) {
			$checked_data->last_checked          = current_time('timestamp');
			$checked_data->checked[$this->name]  = $this->version;
			$checked_data->response[$this->name] = $version_info;
		}

		return $checked_data;
	}

	public function plugins_api_filter($def, $action, $args)
	{
		if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug) {
			return $def;
		}

		$request_string = $this->prepare_request($action, $args);
		if ($request_string === FALSE) {
			return new WP_Error('plugins_api_failed', __('An error occour when try to identify the pluguin.' , 'woo-global-cart') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'woo-global-cart' ) .'&lt;/a>');;
		}

		global $wp_version;

		$request_uri = $this->api_url.'?'.http_build_query($request_string , '', '&');
		$data = wp_remote_get($request_uri, array(
			'timeout'     => 20,
			'user-agent'  => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
		));

		if (is_wp_error($data) || $data['response']['code'] != 200) {
			return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'woo-global-cart') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'woo-global-cart' ) .'&lt;/a>', $data->get_error_message());
		}

		$response_block = json_decode($data['body']);
		//retrieve the last message within the $response_block
		$response_block = $response_block[count($response_block) - 1];
		$response = $response_block->message;

		if (is_object($response) && !empty($response)) {
			$response = $this->postprocess_response($response);
			return $response;
		}
	}

	public function prepare_request($action, $args = array())
	{
		global $wp_version;

		return array(
			'woo_sl_action'     => $action,
			'version'           => '1.4.2',
			'product_unique_id' => $this->product_unique_id,
			'licence_key'       => $this->license,
			'domain'            => home_url(),
			'wp-version'        => $wp_version,
			'api_version'       => $this->API_VERSION
		);
	}

	private function postprocess_response($response)
	{
		//include slug and plugin data
		$response->slug   = $this->slug;
		$response->plugin = $this->plugin;
		$response->new_version = $response->version;

		//if sections are being set
		if (isset($response->sections)) {
			$response->sections = (array)$response->sections;
		}

		//if banners are being set
		if (isset($response->banners)) {
			$response->banners = (array)$response->banners;
		}

		//if icons being set, convert to array
		if (isset($response->icons)) {
			$response->icons = (array)$response->icons;
		}

		return $response;
	}
}
