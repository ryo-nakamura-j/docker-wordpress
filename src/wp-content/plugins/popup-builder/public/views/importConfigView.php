<?php
use sgpb\AdminHelper;
use sgpb\SubscriptionPopup;
@ini_set('auto_detect_line_endings', '1');

$fileContent = AdminHelper::getFileFromURL($fileURL);
$csvFileArray = array_map('str_getcsv', file($fileURL));

$ourFieldsArgs = array(
	'class' => 'js-sg-select2 sgpb-our-fields-keys select__select'
);

$formData =  array('' => 'Select Field') + AdminHelper::getSubscriptionColumnsById($formId);
?>

<div id="importSubscribersSecondStep">
	<h1 id="importSubscriberHeader"><?php esc_html_e('Match Your Fields', SG_POPUP_TEXT_DOMAIN); ?></h1>
	<div id="importSubscriberBody">
		<div class="formItem sgpb-justify-content-around">
			<div class="formItem__title">
				<?php esc_html_e('Available fields', SG_POPUP_TEXT_DOMAIN); ?>
			</div>
			<div class="formItem__title">
				<?php esc_html_e('Our list fields', SG_POPUP_TEXT_DOMAIN); ?>
			</div>
		</div>
		<?php foreach($csvFileArray[0] as $index => $current): ?>
			<?php if (empty($current) || $current == 'popup'): ?>
				<?php continue; ?>
			<?php endif; ?>
			<div class="formItem sgpb-justify-content-between">
				<div class="subFormItem__title">
					<?php echo esc_html($current); ?>
				</div>
				<div>
					<?php
					$ourFieldsArgs['data-index'] = $index;
					echo wp_kses(AdminHelper::createSelectBox($formData, '', $ourFieldsArgs), AdminHelper::allowed_html_tags());
					?>
				</div>
			</div>
		<?php endforeach;?>
		<input type="hidden" class="sgpb-to-import-popup-id" value="<?php echo esc_attr($formId)?>">
		<input type="hidden" class="sgpb-imported-file-url" value="<?php echo esc_attr($fileURL)?>">
	</div>

	<div id="importSubscriberFooter">
		<input type="button" value="<?php esc_html_e('Save', SG_POPUP_TEXT_DOMAIN); ?>" class="sgpb-btn sgpb-btn-blue sgpb-save-subscriber" data-ajaxnonce="popupBuilderAjaxNonce">
	</div>

</div>

