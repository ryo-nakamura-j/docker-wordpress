<?php
namespace sgpb;
use sgpb\AdminHelper;

class SGPBNotificationCenter
{
	private $requestUrl = SG_POPUP_BUILDER_NOTIFICATIONS_URL;
	private $cronTimeout = 'daily';

	public function __construct()
	{
		$this->addActions();
		$this->activateCron();
	}

	public function addActions()
	{
		add_filter('sgpbCronTimeoutSettings', array($this, 'cronAddMinutes'), 10, 1);
		add_action('sgpbGetNotifications', array($this, 'updateNotificationsArray'));
		add_action('wp_ajax_sgpb_dismiss_notification', array($this, 'dismissNotification'));
		add_action('wp_ajax_sgpb_remove_notification', array($this, 'removeNotification'));
		add_action('wp_ajax_sgpb_reactivate_notification', array($this, 'reactivateNotification'));
		add_action('admin_head', array($this, 'menuItemCounter'));
	}

	public function menuItemCounter()
	{
		$count = count(self::getAllActiveNotifications(true));
		$hidden = '';
		if (empty($count)) {
			$hidden = ' sgpb-hide-add-button';
		}
		$script = "<script>
				jQuery(document).ready(function() {
					jQuery('.sgpb-menu-item-notification').remove();
					jQuery('.dashicons-menu-icon-sgpb').next().append('<span class=\"sgpb-menu-item-notification".esc_attr($hidden)."\">".esc_html($count)."</span>');
				});
			</script>";
		echo wp_kses($script, AdminHelper::allowed_html_tags());
	}

	public function setCronTimeout($cronTimeout)
	{
		$this->cronTimeout = $cronTimeout;
	}

	public function getCronTimeout()
	{
		return $this->cronTimeout;
	}

	public function setRequestUrl($requestUrl)
	{
		$this->requestUrl = $requestUrl;
	}

	public function getRequestUrl()
	{
		return $this->requestUrl;
	}

	public function updateNotificationsArray()
	{
		$requestUrl = $this->getRequestUrl();
		$content = AdminHelper::getFileFromURL($requestUrl);
		$content = json_decode($content, true);
		$content = apply_filters('sgpbExtraNotifications', $content);
		// check later
		/*if (empty($content)) {
			update_option('sgpb-all-dismissed-notifications', array());
		}*/
		$content = json_encode($content);
		update_option('sgpb-all-notifications-data', $content);
	}

	public function sortNotifications($allNotifications)
	{
		$allNotifications = json_decode($allNotifications, true);
		if (empty($allNotifications)) {
			$allNotifications = array();
		}
		$dismissed = self::getAllDismissedNotifications();
		// for the first time dismissed and active arrays should be empty
		if (empty($dismissed) && empty($active)) {
			$notifications = array();
			foreach ($allNotifications as $notification) {
				$id = $notification['id'];
				$notifications[$id] = $id;
			}
			update_option('sgpb-all-active-notifications', json_encode($notifications));
		}
	}

	public function cronAddMinutes($schedules)
	{
		$schedules['sgpb_notifications'] = array(
			'interval' => SGPB_NOTIFICATIONS_CRON_REPEAT_INTERVAL * 3600,
			'display' => __('Twice Daily', SG_POPUP_TEXT_DOMAIN)
		);

		return $schedules;
	}

	public static function getAllActiveNotifications($hideDismissed = false)
	{
		$activeNotifications = array();
		$notifications = get_option('sgpb-all-notifications-data');
		$notifications = json_decode($notifications, true);
		if (empty($notifications)) {
			return array();
		}
		asort($notifications);

		$dismissedNotifications = get_option('sgpb-all-dismissed-notifications');
		$dismissedNotifications = json_decode($dismissedNotifications, true);
		$extensions = AdminHelper::getAllExtensions();
		$extensionsKeys = wp_list_pluck($extensions['active'], 'key');
		foreach ($notifications as $notification) {
			$id = isset($notification['id']) ? $notification['id'] : '';

			if (isset($notification['hideFor'])) {
				$hideForExtensions = explode(',', $notification['hideFor']);
				$arraysIntersect = array_intersect($extensionsKeys, $hideForExtensions);

				// If only one condition -> free, single extension, bundle
				if (count($hideForExtensions) == 1) {
					// Free
					if ($notification['hideFor'] == SGPB_POPUP_PKG_FREE && empty($extensionsKeys) && !class_exists('SGPBActivatorPlugin')) {
						continue;
					}
					// Single extension
					else if (in_array($notification['hideFor'], $extensionsKeys)) {
						continue;
					}
					// Pro, if it is a free user
                    else if ($notification['hideFor'] == 'pro' && count($extensionsKeys) >= 1) {
                        continue;
                    }
					// Bundle
					else if ($notification['hideFor'] == 'bundle') {
						if (class_exists('SGPBActivatorPlugin') || count($extensionsKeys) >= 10) {
							continue;
						}
					}
				}
				// if there is even one detected extension, don’t show notification
				else if (count($arraysIntersect) > 0) {
					continue;
				}
			}

			if ($hideDismissed && isset($dismissedNotifications[$id])) {
				continue;
			}

			$activeNotifications[] = $notification;
		}
		$removedNotifications = get_option('sgpb-all-removed-notifications');
		$removedNotifications = json_decode($removedNotifications, true);
		if (empty($removedNotifications)) {
			return $activeNotifications;
		}
		foreach ($removedNotifications as $removedNotificationId) {
			foreach ($activeNotifications as $key => $activeNotification) {
				if ($activeNotification['id'] == $removedNotificationId) {
					unset($activeNotifications[$key]);
				}
			}
		}

		return $activeNotifications;
	}

	public static function getAllDismissedNotifications()
	{
		$notifications = get_option('sgpb-all-dismissed-notifications');
		if (empty($notifications)) {
			$notifications = '';
		}

		return json_decode($notifications, true);
	}

	public static function getAllRemovedNotifications()
	{
		$notifications = get_option('sgpb-all-removed-notifications');
		if (empty($notifications)) {
			$notifications = '';
		}

		return json_decode($notifications, true);
	}

	public static function displayNotifications($withoutWrapper = false)
	{
		$content = '';
		$allNotifications = self::getAllActiveNotifications();
		if (empty($allNotifications)) {
			return $content;
		}

		$count = count(self::getAllActiveNotifications(true));

		foreach ($allNotifications as $notification) {
			$newNotification = new Notification();
			$newNotification->setId($notification['id']);
			$newNotification->setType($notification['type']);
			$newNotification->setPriority($notification['priority']);
			$newNotification->setMessage($notification['message']);
			$content .= $newNotification->render();
		}
		$count = '(<span class="sgpb-notifications-count-span">'.$count.'</span>)';

		if ($withoutWrapper) {
			return $content;
		}

		$content = self::prepareHtml($content, $count);

		return $content;
	}

	public static function prepareHtml($content = '', $count = 0)
	{
		$content = '<div class="sgpb-each-notification-wrapper-js">'.$content.'</div>';
		$content = '<div class="sgpb-notification-center-wrapper">
						<h3><span class="dashicons dashicons-flag"></span> Notifications '.$count.'</h3>'.$content.'
					</div>';

		return $content;
	}

	public function dismissNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');

		$notificationId = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
		$allDismissedNotifications = self::getAllDismissedNotifications();
		$allDismissedNotifications[$notificationId] = $notificationId;
		$allDismissedNotifications = json_encode($allDismissedNotifications);

		update_option('sgpb-all-dismissed-notifications', $allDismissedNotifications);
		$result = array();
		$result['content'] = self::displayNotifications(true);
		$result['count'] = count(self::getAllActiveNotifications(true));

		wp_send_json($result);
	}

	public function removeNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['id'])){
			wp_die(0);
		}
		$notificationId = sanitize_text_field($_POST['id']);
		$allRemovedNotifications = self::getAllRemovedNotifications();
		$allRemovedNotifications[$notificationId] = $notificationId;
		$allRemovedNotifications = json_encode($allRemovedNotifications);

		update_option('sgpb-all-removed-notifications', $allRemovedNotifications);

		wp_die(true);
	}

	public function reactivateNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['id'])){
			wp_die(0);
		}
		$notificationId = sanitize_text_field($_POST['id']);
		$allDismissedNotifications = self::getAllDismissedNotifications();
		if (isset($allDismissedNotifications[$notificationId])) {
			unset($allDismissedNotifications[$notificationId]);
		}
		$allDismissedNotifications = json_encode($allDismissedNotifications);

		update_option('sgpb-all-dismissed-notifications', $allDismissedNotifications);
		$result = array();
		$result['content'] = self::displayNotifications(true);
		$result['count'] = count(self::getAllActiveNotifications(true));

		wp_send_json($result);
	}

	public function activateCron()
	{
		if (!wp_next_scheduled('sgpbGetNotifications')) {
			wp_schedule_event(time(), 'daily', 'sgpbGetNotifications');
		}
	}
}

new SGPBNotificationCenter();
