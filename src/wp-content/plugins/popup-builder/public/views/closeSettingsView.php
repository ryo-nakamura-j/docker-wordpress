<?php

use sgpb\AdminHelper;
use sgpb\PopupBuilderActivePackage;

$defaultData          = ConfigDataHelper::defaultData();
$removedOptions       = $popupTypeObj->getRemoveOptions();
$autoClose            = PopupBuilderActivePackage::canUseOption('sgpb-auto-close');
$closeAfterPageScroll = PopupBuilderActivePackage::canUseOption('sgpb-close-after-page-scroll');
$closeButtonPosition  = AdminHelper::themeRelatedSettings(
	$popupTypeObj->getOptionValue('sgpb-post-id'),
	$popupTypeObj->getOptionValue('sgpb-close-button-position'),
	$popupTypeObj->getOptionValue('sgpb-popup-themes')
);

$hideTopPosition = '';
if($closeButtonPosition == 'bottomRight' || $closeButtonPosition == 'bottomLeft') {
	$hideTopPosition = ' sgpb-display-none';
}
$hideBottomPosition = '';
if($closeButtonPosition == 'topRight' || $closeButtonPosition == 'topLeft') {
	$hideBottomPosition = ' sgpb-display-none';
}
$hideRightPosition = '';
if($closeButtonPosition == 'topLeft' || $closeButtonPosition == 'bottomLeft') {
	$hideRightPosition = ' sgpb-display-none';
}
$hideLeftPosition = '';
if($closeButtonPosition == 'topRight' || $closeButtonPosition == 'bottomRight') {
	$hideLeftPosition = ' sgpb-display-none';
}

$defaultCloseButtonPositions = $defaultData['closeButtonPositions'];
if($popupTypeObj->getOptionValue('sgpb-popup-themes') == 'sgpb-theme-1' ||
   $popupTypeObj->getOptionValue('sgpb-popup-themes') == 'sgpb-theme-4' ||
   $popupTypeObj->getOptionValue('sgpb-popup-themes') == 'sgpb-theme-5') {
	$defaultCloseButtonPositions = $defaultData['closeButtonPositionsFirstTheme'];
}

$borderRadiusType = $popupTypeObj->getOptionValue('sgpb-border-radius-type');
if(!$popupTypeObj->getOptionValue('sgpb-border-radius-type')) {
	$borderRadiusType = '%';
}
$buttonImage = AdminHelper::defaultButtonImage(
	$popupTypeObj->getOptionValue('sgpb-popup-themes'),
	$popupTypeObj->getOptionValue('sgpb-button-image')
);
if(strpos($buttonImage, 'http') === false) {
	$buttonImage = 'data:image/png;base64,'.$buttonImage;
}
$disablePopupClosing = PopupBuilderActivePackage::canUseOption('sgpb-disable-popup-closing');
?>
<div class="sgpb sgpb-wrapper">
	<div class="sgpb-close-settings">
		<?php if(empty($removedOptions['sgpb-esc-key'])) : ?>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Dismiss on "esc" key', SG_POPUP_TEXT_DOMAIN) ?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="esc-key"
					       name="sgpb-esc-key" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-esc-key')); ?>>
					<label class="sgpb-onOffSwitch__label" for="esc-key">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
				<div class="question-mark">B</div>
				<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('The popup will close if the "Esc" key of your keyboard is clicked.', SG_POPUP_TEXT_DOMAIN) ?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<?php if(empty($removedOptions['sgpb-enable-close-button'])) : ?>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Show "close" button', SG_POPUP_TEXT_DOMAIN) ?>:</span>
				<div class="sgpb-onOffSwitch onOffswitch_smallMargin">
					<input class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" type="checkbox" id="close-button"
					       name="sgpb-enable-close-button" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-enable-close-button')); ?>>
					<label class="sgpb-onOffSwitch__label" for="close-button">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
				<div class="question-mark">B</div>
				<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('Uncheck this option if you don\'t want to show a "close" button on your popup.', SG_POPUP_TEXT_DOMAIN) ?>
				</span>
				</div>
			</div>
			<div class="formItem sg-full-width sgpb-padding-20 sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-border-radius-5px">
				<div class="subForm">
					<?php if(empty($removedOptions['sgpb-close-button-delay'])) : ?>
						<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-bottom-20">
							<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Button delay', SG_POPUP_TEXT_DOMAIN) ?>:</span>
							<input type="number" min="0" id="sgpb-close-button-delay" class="subFormItem__input"
							       name="sgpb-close-button-delay"
							       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-close-button-delay')); ?>"
							       placeholder="e.g.: 1">
							<div class="question-mark">B</div>
							<div class="sgpb-info-wrapper">
								<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
									<?php esc_html_e('Specify the time (in seconds) after which the close button will appear. The close button will be shown by default without any delay if no time is specified.', SG_POPUP_TEXT_DOMAIN) ?>
								</span>
							</div>
						</div>
					<?php endif; ?>
					<?php if(empty($removedOptions['sgpb-close-button-position'])) : ?>
						<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-bottom-20">
							<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Button position', SG_POPUP_TEXT_DOMAIN) ?>:</span>
							<?php echo wp_kses(AdminHelper::createSelectBox($defaultCloseButtonPositions, $closeButtonPosition, array('name'  => 'sgpb-close-button-position',
							                                                                                                  'class' => 'js-sg-select2 sgpb-close-button-position'
							)), AdminHelper::allowed_html_tags()); ?>
						</div>

						<div class="formItem formItem">
							<div class="buttonPosition sgpb-button-position-top-js sgpb-display-flex sgpb-align-item-center sgpb-margin-right-20 <?php echo esc_attr($hideTopPosition); ?>">
								<span class="formItem__direction sgpb-margin-right-30"><?php esc_html_e('Top', SG_POPUP_TEXT_DOMAIN) ?></span>
								<div class="inputPxWrapper">
									<input id="sgpb-button-position-top" class="formItem__input" step="0.5"
									       type="number" name="sgpb-button-position-top"
									       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-position-top')); ?>">
									<span class="formItem__inputValueType">px</span>
								</div>
							</div>
							<div class="buttonPosition sgpb-button-position-right-js sgpb-display-flex sgpb-align-item-center sgpb-margin-right-20 <?php echo esc_attr($hideRightPosition); ?>">
								<span class="formItem__direction sgpb-margin-right-20"><?php esc_html_e('Right', SG_POPUP_TEXT_DOMAIN) ?></span>
								<div class="inputPxWrapper ">
									<input id="sgpb-button-position-right" class="formItem__input" step="0.5"
									       type="number" name="sgpb-button-position-right"
									       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-position-right')); ?>">
									<span class="formItem__inputValueType">px</span>
								</div>
							</div>
							<div class="buttonPosition sgpb-button-position-bottom-js sgpb-display-flex sgpb-align-item-center sgpb-margin-right-20 <?php echo esc_attr($hideBottomPosition); ?>">
								<span class="formItem__direction sgpb-margin-right-20"><?php esc_html_e('Bottom', SG_POPUP_TEXT_DOMAIN) ?></span>
								<div class="inputPxWrapper">
									<input id="sgpb-button-position-bottom" class="formItem__input" step="0.5"
									       type="number" name="sgpb-button-position-bottom"
									       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-position-bottom')); ?>">
									<span class="formItem__inputValueType">px</span>
								</div>
							</div>
							<div class="buttonPosition sgpb-button-position-left-js sgpb-display-flex sgpb-align-item-center sgpb-margin-right-20 <?php echo esc_attr($hideLeftPosition); ?>">
								<span class="formItem__direction sgpb-margin-right-40"><?php esc_html_e('Left', SG_POPUP_TEXT_DOMAIN) ?></span>
								<div class="inputPxWrapper">
									<input id="sgpb-button-position-left" class="formItem__input" step="0.5"
									       type="number" name="sgpb-button-position-left"
									       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-position-left')); ?>">
									<span class="formItem__inputValueType">px</span>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-popup-themes') == 'sgpb-theme-4') ? 'sgpb-display-none ' : ''); ?>sgpb-close-button-image-option-wrapper">
						<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-bottom-20">
							<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Button image', SG_POPUP_TEXT_DOMAIN) ?>:</span>
							<div type="text" class="subFormItem__x sgpb-margin-right-10">
								<div class="sgpb-show-button-image-container"
								     style="background-image: url(<?php echo esc_url($buttonImage); ?>);">
									<span class="sgpb-no-image"></span>
								</div>
							</div>
							<div class="easy-icons-wrapper sgpb-display-inline-flex">
								<div class="icons__item icons_blue sgpb-margin-right-10">
									<img id="js-button-upload-image-button"
									     src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/cloud.svg'); ?>"
									     alt="<?php esc_html_e('Change image', SG_POPUP_TEXT_DOMAIN) ?>">
								</div>
								<div class="icons__item icons_pink js-sgpb-remove-close-button-image<?php echo esc_attr((!$popupTypeObj->getOptionValue('sgpb-button-image')) ? ' sg-hide' : ''); ?>">
									<img id="js-button-upload-image-remove-button"
									     src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/recycle-bin.svg'); ?>"
									     alt="<?php esc_html_e('Remove', SG_POPUP_TEXT_DOMAIN) ?>">
								</div>
								<div class="sgpb-button-image-uploader-wrapper">
									<input class="sg-hide" id="js-button-upload-image" type="text" size="36"
									       name="sgpb-button-image"
									       value="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-button-image'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-button-image')) : ''; ?>">
								</div>
							</div>
						</div>
						<div class="formItem_aligne_bottom formItem_itemsCentered">
							<div class="buttonPosition__wrapper">
								<div class="buttonPosition sgpb-display-flex sgpb-align-item-center sgpb-margin-bottom-20">
									<span class="formItem__direction sgpb-margin-right-20"><?php esc_html_e('Width&nbsp;', SG_POPUP_TEXT_DOMAIN) ?></span>
									<div class="inputPxWrapper">
										<input class="formItem__input" type="number" min="0"
										       name="sgpb-button-image-width"
										       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-image-width')); ?>"
										       required>
										<span class="formItem__inputValueType">px</span>
									</div>
								</div>
								<div class="buttonPosition sgpb-display-flex sgpb-align-item-center sgpb-margin-bottom-20">
									<span class="formItem__direction sgpb-margin-right-20"><?php esc_html_e('Height', SG_POPUP_TEXT_DOMAIN) ?></span>
									<div class="inputPxWrapper ">
										<input class="formItem__input" type="number" min="0"
										       name="sgpb-button-image-height"
										       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-button-image-height')); ?>"
										       required>
										<span class="formItem__inputValueType">px</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="formItem sgpb-close-button-border-options<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-popup-themes') != 'sgpb-theme-3') ? ' sg-hide' : ''); ?>">
					<span class="formItem__title"><?php esc_html_e('Popup border color', SG_POPUP_TEXT_DOMAIN) ?>:</span>
					<div class="sgpb-color-picker-wrapper sgpb-overlay-color unhideColorPicker subFormItem">
						<input class="sgpb-color-picker sgpb-border-color" type="text" name="sgpb-border-color"
						       value="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-border-color'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-border-color')) : '#000000'; ?>">
					</div>
				</div>
				<div class="formItem sgpb-close-button-border-options<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-popup-themes') != 'sgpb-theme-3') ? ' sg-hide' : ''); ?>">
					<span class="formItem__title"><?php esc_html_e('Popup border radius', SG_POPUP_TEXT_DOMAIN) ?>:</span>
					<input class="formItem__input sgpb-margin-right-10" type="number" min="0" name="sgpb-border-radius"
					       value="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-border-radius'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-border-radius')) : '0'; ?>">
					<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['pxPercent'], $borderRadiusType, array('name'  => 'sgpb-border-radius-type',
					                                                                                            'class' => 'sgpb-border-radius-type js-sg-select2'
					)), AdminHelper::allowed_html_tags()); ?>
				</div>
				<div class="<?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-popup-themes') != 'sgpb-theme-4') ? 'sg-hide ' : ''); ?>sgpb-close-button-text-option-wrapper">
					<div class="formItem formItem_itemsCentered">
						<span class="formItem__title"><?php esc_html_e('Button text', SG_POPUP_TEXT_DOMAIN); ?>:</span>
						<input class="formItem__input formItem__input_sgpb-button-text" type="text" name="sgpb-button-text"
						       value="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-button-text'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-button-text')) : esc_html__('Close', SG_POPUP_TEXT_DOMAIN); ?>"
						       autocomplete="off">
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if(empty($removedOptions['sgpb-disable-page-scrolling'])): ?>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Dismiss on overlay click', SG_POPUP_TEXT_DOMAIN) ?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="overlay-click"
					       name="sgpb-overlay-click" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-overlay-click')); ?>>
					<label class="sgpb-onOffSwitch__label" for="overlay-click">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
				<div class="question-mark">B</div>
				<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('The popup will close when clicked on the overlay of the popup.', SG_POPUP_TEXT_DOMAIN) ?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<?php if(empty($removedOptions['sgpb-disable-popup-closing'])): ?>
			<?php if ($disablePopupClosing): ?>
				<div class="formItem">
					<span class="formItem__title"><?php esc_html_e('Disable popup closing', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="popup-closing" name="sgpb-disable-popup-closing" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-disable-popup-closing')); ?>>
						<label class="sgpb-onOffSwitch__label" for="popup-closing">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('The users will not be able to close the popup, if this option is checked.', SG_POPUP_TEXT_DOMAIN)?>
						</span>
					</div>
				</div>
			<?php else: ?>
				<div class="formItem sgpb-padding-20 sgpb-option-disable" onclick="window.open('<?php echo esc_url_raw(SG_POPUP_ADVANCED_CLOSING_URL);?>', '_blank')">
					<span class="formItem__title"><?php esc_html_e('Disable popup closing', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="popup-closing" name="sgpb-disable-popup-closing" disabled>
						<label class="sgpb-onOffSwitch__label" for="popup-closing">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('The users will not be able to close the popup, if this option is checked.', SG_POPUP_TEXT_DOMAIN)?>
						</span>
					</div>
					<div class="sgpb-unlock-options">
						<div class="sgpb-unlock-options__icon">
							<img src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/time-is-money.svg');?>" alt="Time icon" width="45" height="45" />
						</div>
						<span class="sgpb-unlock-options__title"><?php esc_html_e('Unlock Option', SG_POPUP_TEXT_DOMAIN); ?></span>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (empty($removedOptions['sgpb-auto-close'])): ?>
			<?php if ($autoClose): ?>
				<div class="formItem">
					<span class="formItem__title"><?php esc_html_e('Auto close popup', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" id="auto-close" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-auto-close" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-auto-close')); ?>>
						<label class="sgpb-onOffSwitch__label" for="auto-close">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			<?php else: ?>
				<div class="formItem sgpb-padding-20 sgpb-option-disable" onclick="window.open('<?php echo esc_url_raw(SG_POPUP_ADVANCED_CLOSING_URL);?>', '_blank')">
					<span class="formItem__title"><?php esc_html_e('Auto close popup', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" id="auto-close" name="sgpb-auto-close" disabled class="sgpb-onOffSwitch-checkbox">
						<label class="sgpb-onOffSwitch__label" for="auto-close">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="sgpb-unlock-options sgpb-margin-left-20">
						<div class="sgpb-unlock-options__icon">
							<img src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/time-is-money.svg');?>" alt="Time icon" width="45" height="45" />
						</div>
						<span class="sgpb-unlock-options__title"><?php esc_html_e('Unlock Option', SG_POPUP_TEXT_DOMAIN); ?></span>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($autoClose && empty($removedOptions['sgpb-auto-close-time'])): ?>
			<?php do_action('autoCloseOptions', $popupTypeObj); ?>
		<?php endif; ?>

		<?php if (empty($removedOptions['sgpb-close-after-page-scroll'])): ?>
			<?php if ($closeAfterPageScroll): ?>
				<div class="formItem">
					<span class="formItem__title"><?php esc_html_e('Close popup after the page scroll', SG_POPUP_TEXT_DOMAIN); ?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="sgpb-close-after-page-scroll" class="" name="sgpb-close-after-page-scroll" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-close-after-page-scroll')); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-close-after-page-scroll">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			<?php else: ?>
				<div class="formItem sgpb-padding-20 sgpb-option-disable" onclick="window.open('<?php echo esc_url_raw(SG_POPUP_ADVANCED_CLOSING_URL);?>', '_blank')">
					<span class="formItem__title"><?php esc_html_e('Close popup after the page scroll', SG_POPUP_TEXT_DOMAIN); ?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" id="sgpb-close-after-page-scroll" name="sgpb-close-after-page-scroll" disabled>
						<label class="sgpb-onOffSwitch__label" for="sgpb-close-after-page-scroll">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="sgpb-unlock-options sgpb-margin-left-20">
						<div class="sgpb-unlock-options__icon">
							<img src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/time-is-money.svg');?>" alt="Time icon" width="45" height="45" />
						</div>
						<span class="sgpb-unlock-options__title"><?php esc_html_e('Unlock Option', SG_POPUP_TEXT_DOMAIN); ?></span>
					</div>
				</div>
			<?php endif; ?>

		<?php endif; ?>

	</div>

</div>
