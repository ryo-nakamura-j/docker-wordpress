<?php
require_once(SG_POPUP_CLASSES_PATH.'/Ajax.php');
require_once(SG_POPUP_HELPERS_PATH.'AdminHelper.php');

use sgpb\SGPopup;
use sgpb\AdminHelper;
use sgpbDataTable\SGPBTable;
use sgpb\SubscriptionPopup;

class Subscribers extends SGPBTable
{
	public function __construct()
	{
		global $wpdb;
		parent::__construct('sgpbAllSubscribers');

		$this->setRowsPerPage(SGPB_APP_POPUP_TABLE_LIMIT);
		$this->setTablename($wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME);

		$columns = array(
			$this->tablename.'.id',
			'firstName',
			'lastName',
			'email',
			'cDate',
			'subscriptionType'
		);

		$selectAllCheckbox = '<div class="sgpb-wrapper checkbox-wrapper">
								<input class="subs-bulk" type="checkbox" id="checkbox-all">
								<label class="checkboxLabel" for="checkbox-all"></label>
							</div>';

		$displayColumns = array(
			'bulk' => $selectAllCheckbox,
			'id' => 'ID',
			'firstName' => __('First name', SG_POPUP_TEXT_DOMAIN),
			'lastName' => __('Last name', SG_POPUP_TEXT_DOMAIN),
			'email' => __('Email', SG_POPUP_TEXT_DOMAIN),
			'cDate' => __('Date', SG_POPUP_TEXT_DOMAIN),
			'subscriptionType' => __('Popup', SG_POPUP_TEXT_DOMAIN)
			//'options' => __('Actions', SG_POPUP_TEXT_DOMAIN)
		);

		$filterColumnsDisplaySettings = array(
			'columns' => $columns,
			'displayColumns' => $displayColumns
		);

		$filterColumnsDisplaySettings = apply_filters('sgpbAlterColumnIntoSubscribers', $filterColumnsDisplaySettings);

		$this->setColumns((isset($filterColumnsDisplaySettings['columns']) ? $filterColumnsDisplaySettings['columns'] : ''));
		$this->setDisplayColumns((isset($filterColumnsDisplaySettings['displayColumns']) ? $filterColumnsDisplaySettings['displayColumns'] : ''));
		$this->setSortableColumns(array(
			'id' => array('id', false),
			'firstName' => array('firstName', true),
			'lastName' => array('lastName', true),
			'email' => array('email', true),
			'cDate' => array('cDate', true),
			'subscriptionType' => array('subscriptionType', true),
			$this->setInitialSort(array(
				'id' => 'DESC'
			))
		));
	}

	public function customizeRow(&$row)
	{
		$popupId = (int)$row[5];
		$row = apply_filters('sgpbEditSubscribersTableRowValues', $row, $popupId);
		$row[6] = get_the_title($popupId);
		$row[5] = $row[4];
		$row[4] = $row[3];
		$row[3] = $row[2];
		$row[2] = $row[1];
		$row[1] = $row[0];

		// show date more user friendly
		$row[5] = date('d F Y', strtotime($row[5]));

		$id = $row[0];
		$row[0] = '<div class="sgpb-wrapper checkbox-wrapper">
								<input class="subs-delete-checkbox" type="checkbox" id="checkbox-'.esc_attr($id).'" data-delete-id="'.esc_attr($id).'">
								<label class="checkboxLabel" for="checkbox-'.esc_attr($id).'"></label>
							</div>';
	}

	public function customizeQuery(&$query)
	{
		$query = AdminHelper::subscribersRelatedQuery($query);
	}

	public function getNavPopupsConditions()
	{
		$subscriptionPopups = SubscriptionPopup::getAllSubscriptionForms();
		$list = '';
		$selectedPopup = '';

		if (isset($_GET['sgpb-subscription-popup-id'])) {
			$selectedPopup = (int)sanitize_text_field($_GET['sgpb-subscription-popup-id']);
		}
		$allowed_html = AdminHelper::allowed_html_tags();
		ob_start();
		?>
		<input type="hidden" class="sgpb-subscription-popup-id" name="sgpb-subscription-popup-id" value="<?php echo esc_attr($selectedPopup);?>">
		<input type="hidden" name="page" value="<?php echo esc_attr(SG_POPUP_SUBSCRIBERS_PAGE); ?>" >

		<select class="select__select sgpb-margin-right-10" name="sgpb-subscription-popup" id="sgpb-subscription-popup">
			<?php
			$list .= '<option value="all">'.__('All', SG_POPUP_TEXT_DOMAIN).'</option>';
			foreach ($subscriptionPopups as $popupId => $popupTitle) {
				if ($selectedPopup == $popupId) {
					$selected = ' selected';
				}
				else {
					$selected = '';
				}
				$list .= '<option value="'.esc_attr($popupId).'"'.esc_attr($selected).'>'.esc_html($popupTitle).'</option>';
			}
			echo wp_kses($list, $allowed_html);
			?>
		</select>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function getNavDateConditions() {
		$subscribersDates = SubscriptionPopup::getAllSubscribersDate();
		$uniqueDates = array();

		foreach ($subscribersDates as $arr) {
			$uniqueDates[] = $arr;
		}
		$uniqueDates = array_unique($uniqueDates, SORT_REGULAR);

		$selectedDate = '';
		$dateList = '';
		$selected = '';

		if (isset($_GET['sgpb-subscribers-date'])) {
			$selectedDate = sanitize_text_field($_GET['sgpb-subscribers-date']);
		}
		$allowed_html = AdminHelper::allowed_html_tags();

		ob_start();
		?>
		<input type="hidden" class="sgpb-subscribers-date" name="sgpb-subscribers-date" value="<?php echo esc_attr($selectedDate);?>">
		<select class="select__select sgpb-margin-right-10" name="sgpb-subscribers-dates" id="sgpb-subscribers-dates">
			<?php
			$gotDateList = '<option value="all">'.__('All dates', SG_POPUP_TEXT_DOMAIN).'</option>';
			foreach ($uniqueDates as $date) {
				if ($selectedDate == $date['date-value']) {
					$selected = ' selected';
				}
				else {
					$selected = '';
				}
				$gotDateList .= '<option value="'.$date['date-value'].'"'.$selected.'>'.$date['date-title'].'</option>';
			}
			if (empty($subscribersDates)) {
				$dateValue = isset($date) && isset($date['date-value']) ? $date['date-value'] : '';
				$gotDateList = '<option value="'.$dateValue.'"'.$selected.'>'.__('Date', SG_POPUP_TEXT_DOMAIN).'</option>';
			}
			echo wp_kses($dateList.$gotDateList, $allowed_html);
			?>
		</select>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	// parent class method overriding
	public function extra_tablenav($which)
	{
		$isVisibleExtraNav = $this->getIsVisibleExtraNav();

		if (!$isVisibleExtraNav) {
			return '';
		}
		$allowed_html = AdminHelper::allowed_html_tags();

		?>
		<div class="sgpb-display-flex sgpb-justify-content-between actions">
			<div>
				<label class="screen-reader-text" for="sgpb-subscription-popup"><?php esc_html_e('Filter by popup', SG_POPUP_TEXT_DOMAIN)?></label>
				<?php echo wp_kses($this->getNavPopupsConditions(), $allowed_html); ?>
				<label class="screen-reader-text" for="sgpb-subscribers-dates"><?php esc_html_e('Filter by date', SG_POPUP_TEXT_DOMAIN)?></label>
				<?php  echo wp_kses($this->getNavDateConditions(), $allowed_html); ?>
				<input name="filter_action" id="post-query-submit" class="buttonGroup__button buttonGroup__button_blueBg buttonGroup__button_unrounded" value="<?php esc_html_e('Filter', SG_POPUP_TEXT_DOMAIN)?>" type="submit">
			</div>
			<div>
				<?php
				if ($which == 'top') {
					?>
						<button type="button" class="sgpb-btn sgpb-btn-danger sgpb-btn-disabled sgpb-btn--rounded sg-subs-delete-button" data-ajaxNonce="<?php echo esc_attr(SG_AJAX_NONCE);?>">
							<?php esc_html_e('Delete subscriber(s)', SG_POPUP_TEXT_DOMAIN)?>
						</button>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
}
