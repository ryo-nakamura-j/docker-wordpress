<?php
namespace sgpb;

class Updates
{
	private $licenses = array();
	private $licenseClass;

	public function setLicenses($licenses)
	{
		$this->licenses = $licenses;
	}

	public function getLicenses()
	{
		return $this->licenses;
	}

	public function __construct()
	{
		$this->licenseClass = new License();
		$this->init();
	}

	private function init()
	{
		$this->setLicenses($this->licenseClass->getLicenses());
		$licenses = $this->getLicenses();

		if (empty($licenses)) {
			return false;
		}
		add_action('admin_menu', array($this, 'menu'), 22);
		add_action('admin_init', array($this, 'sgpbActivateLicense'));
		add_action('admin_notices', array($this, 'sgpbAdminNotices'));

		return true;
	}

	public function menu()
	{
		add_submenu_page('edit.php?post_type='.SG_POPUP_POST_TYPE, __('License', SG_POPUP_TEXT_DOMAIN), __('License', SG_POPUP_TEXT_DOMAIN), 'sgpb_manage_options', SGPB_POPUP_LICENSE, array($this, 'pluginLicense'));
	}

	public function sanitizeLicense($new)
	{
		$old = get_option('sgpb-license-key-'.$this->licenseKey);

		if ($old && $old != $new) {
			delete_option('sgpb-license-status-'.$this->licenseKey); // new license has been entered, so must reactivate
		}
		update_option('sgpb-license-key-'.$this->licenseKey, $new);

		return $new;
	}

	public function pluginLicense()
	{
		require_once(SG_POPUP_VIEWS_PATH.'license.php');
	}

	private function activateLicense($license, $itemId)
	{
		$params = array(
			'woo_sl_action'     => 'activate',
			'licence_key'       => $license,
			'product_unique_id' => $itemId,
			'domain'            => home_url()
		);

		$requestUri = SGPB_REQUEST_URL.'?'.http_build_query($params);

		return wp_remote_get($requestUri);
	}

	private function deactivateLicense($license, $itemId)
	{
		$params = array(
			'woo_sl_action'     => 'deactivate',
			'licence_key'       => $license,
			'product_unique_id' => $itemId,
			'domain'            => home_url()
		);

		$requestUri = SGPB_REQUEST_URL.'?'.http_build_query($params);

		return wp_remote_get($requestUri);
	}

	public function sgpbActivateLicense()
	{
		$licenses = $this->getLicenses();

		foreach ($licenses as $license) {
			$key = isset($license['key']) ? $license['key'] : '';
			$itemId = isset($license['itemId']) ? $license['itemId'] : '';
			$this->licenseKey = $key;

			if (isset($_POST['sgpb-license-key-'.$key])) {
				$this->sanitizeLicense(sanitize_key($_POST['sgpb-license-key-'.$key]));
			}

			// listen for our activate button to be clicked
			if (isset($_POST['sgpb-license-activate-'.$key])) {
				// run a quick security check
				if (!check_admin_referer('sgpb_nonce', 'sgpb_nonce')) {
					return; // get out if we didn't click the Activate button
				}
				// retrieve the license from the database
				$license = trim(get_option('sgpb-license-key-'.$key));
				$data = $this->activateLicense($license, $itemId);
				if (!is_wp_error($data) && $data['response']['code'] == 200) {
					$dataBody = json_decode($data['body']);
					if (isset($dataBody[0]->status)) {
						if ($dataBody[0]->status == 'success' && ($dataBody[0]->status_code == 's100' || $dataBody[0]->status_code == 's101')) {
							update_option('sgpb-license-status-'.$key, 'valid');
							$hasInactiveExtensions = AdminHelper::hasInactiveExtensions();

							if (empty($hasInactiveExtensions)) {
								delete_option('SGPB_INACTIVE_EXTENSIONS');
							}

							wp_redirect(admin_url('edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SGPB_POPUP_LICENSE));
							exit();
						}
					}
				}

				$message = __('An error occurred, please try again.', SG_POPUP_TEXT_DOMAIN);
				$baseUrl = admin_url('edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SGPB_POPUP_LICENSE);
				$redirect = add_query_arg(array('sl_activation' => 'false', 'message' => urlencode($message)), $baseUrl);
				wp_redirect($redirect);
				exit();
			}

			if (isset($_POST['sgpb-license-deactivate'.$key])) {
				$license = trim(get_option('sgpb-license-key-'.$key));
				// data to send in our API request
				$response = $this->deactivateLicense($license, $itemId);
				if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
					$message = __('An error occurred, please try again.', SG_POPUP_TEXT_DOMAIN);
					$baseUrl = admin_url('edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SGPB_POPUP_LICENSE);
					$redirect = add_query_arg(array('message' => urlencode($message)), $baseUrl);
					wp_redirect($redirect);
					exit();
				}
				else {
					$status = false;
					$licenseData = json_decode(wp_remote_retrieve_body($response));
					if (isset($licenseData->success)) {
						$status = $licenseData->success;
					}
					update_option('sgpb-license-status-'.$key, $status);
					update_option('SGPB_INACTIVE_EXTENSIONS', 'inactive');
					wp_redirect(admin_url('edit.php?post_type='.SG_POPUP_POST_TYPE.'&page='.SGPB_POPUP_LICENSE));
					exit();
				}
			}
		}
	}

	public function sgpbAdminNotices()
	{
		if (isset($_GET['sl_activation']) && !empty($_GET['message'])) {
			switch (sanitize_text_field($_GET['sl_activation'])) {
				case 'false':
					$message = urldecode(sanitize_text_field($_GET['message']));
					?>
					<div class="error">
						<h3><?php echo esc_html($message); ?></h3>
					</div>
					<?php
					break;
				case 'true':
					break;
			}
		}
	}
}
