<?php
use sgpb\AdminHelper;
$defaultData = ConfigDataHelper::defaultData();
$defaultPositions = $defaultData['floatingButtonPositionsCorner'];
if ($popupTypeObj->getOptionValue('sgpb-floating-button-style') == 'basic') {
	$defaultPositions = $defaultData['floatingButtonPositionsBasic'];
}
?>
<div class="sgpb sgpb-wrapper sgpb-floating-btn-wrapper" id="sgpb-floating-btn-wrapper">
	<div class="formItem">
		<p class="formItem__title"><?php esc_html_e('Enable', SG_POPUP_TEXT_DOMAIN)?>:</p>
		<div class="sgpb-onOffSwitch">
			<input id="sgpb-enable-floating-button" onchange="SGPBFloatingButton.prototype.adminInit()" type="checkbox" class="sgpb-onOffSwitch-checkbox js-checkbox-accordion" id="sgpb-enable-floating-button" name="sgpb-enable-floating-button" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-enable-floating-button')); ?>>
			<label class="sgpb-onOffSwitch__label" for="sgpb-enable-floating-button">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
	</div>
	<div class="sgpb-width-100">
		<div class="subFormItem noMargin">
			<p class="formItem__title sgpb-margin-y-20"><?php esc_html_e('Style', SG_POPUP_TEXT_DOMAIN)?>:</p>
			<div class="subForm subForm_squared">
				<input type="button" class="sgpb-input-button sgpb-margin-right-20 sgpb-floating-button-style-corner js-floating-button-style squares__square buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-style') == 'corner') ? ' sgpb-input-button-active' : '');?>" value="<?php esc_html_e('Corner', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="corner">
				<input type="button" class="sgpb-input-button sgpb-floating-button-style-basic js-floating-button-style squares__square buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-style') == 'basic') ? ' sgpb-input-button' : '');?>" value="<?php esc_html_e('Basic', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="basic">

				<input type="hidden" name="sgpb-floating-button-style" id="sgpb-floating-button-style" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-style'));?>">
			</div>
		</div>
		<div class="subFormItem sgpb-margin-bottom-30 sgpb-floating-btn-position-boxes-container">
			<p class="formItem__title sgpb-margin-y-20"><?php esc_html_e('Position', SG_POPUP_TEXT_DOMAIN)?>:</p>
			<div class="subForm subForm_squared">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-top-left js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'corner') ? ' active' : '');?>" value="<?php esc_html_e('Top Left', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="top-left">
				<input type="button" class="sgpb-input-button sgpb-margin-x-10 sgpb-floating-button-position-top-center js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Top Center', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="top-center">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-top-right js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Top Right', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="top-right">
			</div>
			<div class="subForm subForm_squared sgpb-margin-y-10">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-left-center js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Left center', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="left-center">
				<input type="button" class="sgpb-input-button sgpb-input-button-bg-arrows sgpb-margin-x-10 sgpb-floating-button-position-empty-box sgpb-input-button-disabled buttonGroup__button" disabled="disabled" value="" data-sgvalue="empty-box">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-right-center js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Right Center', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="right-center">
			</div>
			<div class="subForm subForm_squared">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-bottom-left js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Bottom Left', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="bottom-left">
				<input type="button" class="sgpb-input-button sgpb-margin-x-10 sgpb-floating-button-position-bottom-center js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Bottom Center', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="bottom-center">
				<input type="button" class="sgpb-input-button sgpb-floating-button-position-bottom-right js-floating-button-position buttonGroup__button<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'basic') ? ' active' : '');?>" value="<?php esc_html_e('Bottom Right', SG_POPUP_TEXT_DOMAIN)?>" data-sgvalue="bottom-right">
			</div>
			<input type="hidden" name="sgpb-floating-button-position" id="sgpb-floating-button-position" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-position'));?>">
		</div>
		<div class="formItem formItem_itemsCentered">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Font size', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<input type="number" min="0" name="sgpb-floating-button-font-size" id="sgpb-floating-button-font-size" class="formItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-font-size')); ?>">
			<div class="formItem__inputValueType sgpb-margin-left-10">px</div>
		</div>
		<div class="sgpb-basic-button-style-options-wrapper-js<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-floating-button-position') == 'corner') ? ' sgpb-hide' : ''); ?>">
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Position top', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<input type="number" min="0" name="sgpb-floating-button-position-top" id="sgpb-floating-button-position-top" class="formItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-position-top')); ?>">
				<div class="formItem__inputValueType sgpb-margin-left-10">%</div>
			</div>
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Position right', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<input type="number" min="0" name="sgpb-floating-button-position-right" id="sgpb-floating-button-position-right" class="formItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-position-right')); ?>">
				<div class="formItem__inputValueType sgpb-margin-left-10">%</div>
			</div>
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Border size', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<input type="number" min="0" name="sgpb-floating-button-border-size" id="sgpb-floating-button-border-size" class="formItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-border-size')); ?>">
				<div class="formItem__inputValueType sgpb-margin-left-10">px</div>
			</div>
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Border radius', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<input type="number" min="0" name="sgpb-floating-button-border-radius" id="sgpb-floating-button-border-radius" class="formItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-border-radius')); ?>">
				<div class="formItem__inputValueType sgpb-margin-left-10">px</div>
			</div>
			<div class="formItem">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Border color', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-color-picker-wrapper sgpb-overlay-color unhideColorPicker">
					<input id="sgpb-floating-button-border-color" data-type="borderColor" class="sgpb-color-picker sgpb-floating-button-border-color" type="text" name="sgpb-floating-button-border-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-border-color')); ?>" >
				</div>
			</div>
		</div>
		<div class="formItem">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Background color', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-color-picker-wrapper sgpb-overlay-color unhideColorPicker">
				<input id="sgpb-floating-button-bg-color" data-type="backgroundColor" class="sgpb-color-picker sgpb-floating-button-bg-color" type="text" name="sgpb-floating-button-bg-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-bg-color')); ?>" >
			</div>
		</div>
		<div class="formItem">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Text color', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-color-picker-wrapper sgpb-overlay-color unhideColorPicker">
				<input id="sgpb-floating-button-text-color" data-type="color" class="sgpb-color-picker sgpb-floating-button-text-color" type="text" name="sgpb-floating-button-text-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-text-color')); ?>" >
			</div>
		</div>
		<div class="formItem">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Text', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<input id="sgpb-floating-button-text" class="subFormItem__input" type="text" name="sgpb-floating-button-text" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-floating-button-text')); ?>" >
		</div>
	</div>
</div>
