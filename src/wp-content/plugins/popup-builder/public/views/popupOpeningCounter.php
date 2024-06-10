<?php
	$popupId = $popupTypeObj->getOptionValue('sgpb-post-id');
	$count = $popupTypeObj->getPopupOpeningCountById($popupId);
	$counterReset = 'SGPBBackend.resetCount('.$popupId.', false)';
?>
<div class="sgpb-wrapper sgpb-popup-opening-analytics-container">
	<div class="subForm sgpb-padding-20 sgpb-bg-black__opacity-02">
		<div class="formItem">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Disable popup counting', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-onOffSwitch">
				<input id="sgpb-popup-counting-disabled" class="sgpb-onOffSwitch-checkbox" name="sgpb-popup-counting-disabled" type="checkbox" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-popup-counting-disabled'));?>>
				<label class="sgpb-onOffSwitch__label" for="sgpb-popup-counting-disabled">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
		</div>
		<div class="formItem">
			<span class="subFormItem__title"><?php esc_html_e('Views', SG_POPUP_TEXT_DOMAIN); ?>: </span>
			<span class="subFormItem__title sgpb-popup-opening-analytics-option-value-span"> <?php echo esc_html($count); ?></span>
		</div>
		<div class="formItem">
			<input onclick="SGPBBackend.resetCount(<?php echo esc_html($popupId); ?>, false)" type="button" class="button sgpb-reset-count-btn" value="<?php esc_html_e('Reset', SG_POPUP_TEXT_DOMAIN); ?>" <?php echo esc_attr(($popupId && $count != 0) ? '' : ' disabled') ; ?>>
		</div>
	</div>
</div>
