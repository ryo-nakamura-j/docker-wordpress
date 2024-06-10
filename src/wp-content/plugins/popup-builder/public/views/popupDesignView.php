<?php
use sgpb\AdminHelper;
use sgpb\PopupBuilderActivePackage;
$defaultData = ConfigDataHelper::defaultData();
$enablePopupOverlay = PopupBuilderActivePackage::canUseOption('sgpb-enable-popup-overlay');
$removedOptions = $popupTypeObj->getRemoveOptions();
$popupTheme = $popupTypeObj->getOptionValue('sgpb-popup-themes');
$hidePopupBorderOption = ' sg-hide';
if ($popupTheme == 'sgpb-theme-2' || $popupTheme == 'sgpb-theme-3') {
	$hidePopupBorderOption = '';
}

?>
<div class="sgpb sgpb-wrapper">
	<div class="sgpb-design">
		<?php if (empty($removedOptions['sgpb-force-rtl'])) :?>
			<div class="formItem formItem_itemsCentered ">
				<label for="sgpb-force-rtl" class="sgpb-design-label formItem__title">
					<?php esc_html_e('Force RTL', SG_POPUP_TEXT_DOMAIN)?>:
				</label>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="sgpb-force-rtl" name="sgpb-force-rtl" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-force-rtl')); ?>>
					<label class="sgpb-onOffSwitch__label" for="sgpb-force-rtl">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
		<?php endif; ?>
		<?php if (empty($removedOptions['sgpb-content-padding'])) :?>
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title"><?php esc_html_e('Padding', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<input type="number" min="0" class="formItem__input" id="content-padding" name="sgpb-content-padding" value="<?php echo esc_attr((int)$popupTypeObj->getOptionValue('sgpb-content-padding')); ?>">
				<div class="formItem__inputValueType">px</div>
				<div class="question-mark">B</div>
				<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('Add some space, in pixels, around your popup content.', SG_POPUP_TEXT_DOMAIN);?>
					</span>
				</div>
			</div>
		<?php endif; ?>
		<?php if (empty($removedOptions['sgpb-popup-z-index'])) : ?>
			<div class="formItem formItem_itemsCentered">
				<span class="formItem__title"><?php esc_html_e('Popup z-index', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				<input type="number" min="1" name="sgpb-popup-z-index" id="sgpb-popup-z-index" class="formItem__input formItem__input_sgpb-pixels" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-popup-z-index')); ?>">
				<div class="question-mark sgpb-info-icon">B</div>
				<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('Increase or dicrease the value to set the priority of displaying the popup content in comparison of other elements on the page. The highest value of z-index is 2147483647.', SG_POPUP_TEXT_DOMAIN);?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<div class="formItem formItem_itemsCentered">
			<span class="formItem__title"><?php esc_html_e('Themes', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<?php AdminHelper::createRadioButtons($defaultData['theme'], "sgpb-popup-themes", esc_html($popupTheme), true, 'bg_img'); ?>
		</div>
		<div class="formItem sgpb-disable-border-wrapper<?php echo esc_attr($hidePopupBorderOption) ;?>">
			<span class="formItem__title"><?php esc_html_e('Disable popup border', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="sgpb-disable-border" name="sgpb-disable-border" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-disable-border', true)); ?>>
				<label class="sgpb-onOffSwitch__label" for="sgpb-disable-border">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
		</div>
		<?php if (empty($removedOptions['sgpb-enable-popup-overlay'])) :?>
			<?php if (!$enablePopupOverlay): ?>
				<div class="formItem formItem_lessMargin sgpb-padding-20 sgpb-option-disable"
				     onclick="window.open('<?php echo esc_url(SG_POPUP_ADVANCED_CLOSING_URL);?>', '_blank')">
					<span class="formItem__title "><?php esc_html_e('Enable popup overlay', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch sgpb-onOffSwitch_smallLeftMargin">
						<input type="checkbox" id="sgpb-enable-popup-overlay" name="sgpb-enable-popup-overlay" disabled>
						<label class="sgpb-onOffSwitch__label" for="sgpb-enable-popup-overlay">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('If this option is checked, the popup will appear with an overlay.', SG_POPUP_TEXT_DOMAIN);?>
					</span>
					</div>
					<div class="sgpb-unlock-options">
						<div class="sgpb-unlock-options__icon">
							<img src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/time-is-money.svg');?>" alt="Time icon" />
						</div>
						<span class="sgpb-unlock-options__title"><?php esc_html_e('Unlock Option', SG_POPUP_TEXT_DOMAIN); ?></span>
					</div>
				</div>
			<?php else: ?>
				<div class="formItem formItem_lessMargin">
					<span class="formItem__title "><?php esc_html_e('Enable popup overlay', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch sgpb-onOffSwitch_smallLeftMargin">
						<input type="checkbox" id="sgpb-enable-popup-overlay" name="sgpb-enable-popup-overlay" class="sgpb-onOffSwitch-checkbox js-checkbox-accordion" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-enable-popup-overlay')); ?> <?php echo esc_attr((!empty($removedOptions['sgpb-enable-popup-overlay'])) ? ' disabled' : '') ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-enable-popup-overlay">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
					<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
						<?php esc_html_e('If this option is checked, the popup will appear with an overlay.', SG_POPUP_TEXT_DOMAIN);?>
					</span>
					</div>
				</div>
			<?php endif; ?>
			<div class="formItem sgpb-padding-20 sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-border-radius-5px">
				<div class="subForm">
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Overlay custom css class', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input type="text" class="subFormItem__input formItem__input_sgpb-popup-overlay" value="sgpb-popup-overlay">
						<div class="question-mark">B</div>
						<div class="sgpb-info-wrapper">
							<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
								<?php esc_html_e('Add a custom class to the overlay for additional customization.', SG_POPUP_TEXT_DOMAIN);?>
							</span>
						</div>
					</div>
					<div class="subFormItem sgpb-margin-y-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Change color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper sgpb-position-relative">
							<input class="sgpb-color-picker sgpb-overlay-color" type="text" name="sgpb-overlay-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-overlay-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-20">
							<?php esc_html_e('Opacity', SG_POPUP_TEXT_DOMAIN)?>:
						</span>
						<div class="sgpb-slider-wrapper sgpb-range-wrap">
							<div class="slider-wrapper sgpb-display-inline-flex">
								<?php $overlayOpacity = $popupTypeObj->getOptionValue('sgpb-overlay-opacity'); ?>
								<input type="range" class="sgpb-range-input js-popup-overlay-opacity sgpb-margin-right-10"
								       name="sgpb-overlay-opacity"
								       id="js-popup-overlay-opacity" min="0.0" step="0.1" max="1" value="<?php echo esc_attr($overlayOpacity)?>">
								<span class="js-popup-overlay-opacity-value"><?php echo esc_html($overlayOpacity)?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="formItem formItem_itemsCentered">
			<span class="formItem__title"><?php esc_html_e('Content custom css class', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" class="formItem__input formItem__input_sgpb-popup-overlay" id="content-custom-class" name="sgpb-content-custom-class" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-content-custom-class'))?>">
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('Add a custom class to the content for additional customization', SG_POPUP_TEXT_DOMAIN);?>.
				</span>
			</div>
		</div>
		<?php if (empty($removedOptions['sgpb-show-background'])) :?>
			<div class="formItem formItem_lessMargin">
				<span class="formItem__title"><?php esc_html_e('Background settings', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch sgpb-onOffSwitch_smallLeftMargin">
					<input type="checkbox" class="sgpb-onOffSwitch-checkbox js-checkbox-accordion" id="sgpb-show-background" name="sgpb-show-background" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-show-background')); ?>>
					<label class="sgpb-onOffSwitch__label" for="sgpb-show-background">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>

			<div class="formItem sg-full-width sgpb-padding-20 sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-border-radius-5px">
				<div class="subForm">
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Color', SG_POPUP_TEXT_DOMAIN); ?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker" type="text" name="sgpb-background-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-background-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-y-20">
						<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Opacity', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-slider-wrapper sgpb-range-wrap">
							<div class="slider-wrapper sgpb-display-inline-flex">
								<?php $contentOpacity = $popupTypeObj->getOptionValue('sgpb-content-opacity'); ?>
								<input type="range" name="sgpb-content-opacity" class="sgpb-range-input js-popup-content-opacity sgpb-margin-right-10"
								       id="js-popup-content-opacity" min="0.0" step="0.1" max="1" value="<?php echo esc_attr($contentOpacity)?>">
								<span class="js-popup-content-opacity-value"><?php echo esc_html($contentOpacity)?></span>
							</div>
						</div>
					</div>
					<?php if (empty($removedOptions['sgpb-background-image'])) : ?>
						<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-y-20">
							<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Image', SG_POPUP_TEXT_DOMAIN);?>:</span>
							<div class="sgpb-background-image-block-1 sgpb-display-flex subFormBackground sgpb-padding-10 subFormItemImages<?php echo esc_attr((!$popupTypeObj->getOptionValue('sgpb-background-image')) ? ' sgpb-display-none' : '');?>">
								<div>
									<div class="sgpb-button-image-uploader-wrapper">
										<input class="sgpb-display-none" id="js-background-upload-image" type="text" size="36" name="sgpb-background-image" value="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-background-image'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-background-image')) : '' ; ?>" autocomplete="off">
									</div>
								</div>
								<img class="sgpb-show-background-image-container" src="<?php echo (esc_attr($popupTypeObj->getOptionValue('sgpb-background-image'))) ? esc_attr($popupTypeObj->getOptionValue('sgpb-background-image')) : '' ; ?>" width="200" height="150">
								<div class="sgpb-margin-left-10 subFormItemIcons">
									<div class="icons__item">
										<img class="js-background-upload-image-button" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/cloud.svg'); ;?>" alt="<?php esc_html_e('Cloud icon', SG_POPUP_TEXT_DOMAIN);?>">
									</div>
									<div class="icons__item icons_pink">
										<img id="js-background-upload-image-remove-button" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/recycle-bin.svg') ;?>" alt="<?php esc_html_e('Recycle Bin', SG_POPUP_TEXT_DOMAIN);?>">
									</div>
								</div>
							</div>
							<div class="sgpb-background-image-block-2 subFormItemImages <?php echo esc_attr(($popupTypeObj->getOptionValue('sgpb-background-image')) ? ' sgpb-display-none' : '');?>">
								<div class="subFormBackground sgpb-display-flex sgpb-align-item-center sgpb-padding-x-20">
									<div class="icons__item">
										<img class="js-background-upload-image-button" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'icons/cloud.svg') ;?>" alt="<?php esc_html_e('Change image', SG_POPUP_TEXT_DOMAIN);?>">
									</div>
									<img src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL.'images/NoImage.png') ;?>" alt="No Image">
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if (empty($removedOptions['sgpb-background-image-mode'])) : ?>

						<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
							<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Mode', SG_POPUP_TEXT_DOMAIN)?>:</span>
							<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['backroundImageModes'], $popupTypeObj->getOptionValue('sgpb-background-image-mode'), array('name' => 'sgpb-background-image-mode', 'class'=>'select__select js-sg-select2')), AdminHelper::allowed_html_tags()); ?>
							<div class="question-mark">B</div>
							<div class="sgpb-info-wrapper">
								<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
									<?php esc_html_e('Choose how the background image will be displayed with your content', SG_POPUP_TEXT_DOMAIN);?>.
								</span>
							</div>
						</div>
					<?php endif; ?>


				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<script>
	jQuery('.js-popup-overlay-opacity, .js-popup-content-opacity').on('change', function () {
		jQuery(this).siblings('span').text(this.value);
	})
</script>
