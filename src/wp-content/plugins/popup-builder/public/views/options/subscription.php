<?php
	use sgpb\AdminHelper;
	use sgpb\MultipleChoiceButton;
	use sgpb\Functions;

	$allowed_html = AdminHelper::allowed_html_tags();
	$defaultData = ConfigDataHelper::defaultData();
	$placeholderColor = $popupTypeObj->getOptionValue('sgpb-subs-text-placeholder-color');
	$popupId = 0;

	if (!empty($_GET['post'])) {
		$popupId = (int)sanitize_text_field($_GET['post']);
        $popupTypeObj->setSubsFormData($popupId);
	}

	$formData = $popupTypeObj->createFormFieldsData();
	$subscriptionSubPopups = $popupTypeObj->getPopupsIdAndTitle();
	$successPopup = $popupTypeObj->getOptionValue('sgpb-subs-success-popup');

	// for old popups
	if (function_exists('sgpb\sgpGetCorrectPopupId')) {
		$successPopup = sgpb\sgpGetCorrectPopupId($successPopup);
	}
	$forceRtlClass = '';
	$forceRtl = $popupTypeObj->getOptionValue('sgpb-force-rtl');
	if ($forceRtl) {
		$forceRtlClass = ' sgpb-forms-preview-direction';
	}
?>

<div class="sgpb sgpb-wrapper">
	<div class="sgpb-subscription-popup-options sgpb-display-flex">
		<div class="sgpb-width-70 sgpb-padding-x-20">
			<div class="formItem sgpb-margin-top-0">
				<div class="sgpb-width-100 sgpb-margin-bottom-40">
					<p class="formItem__title"><?php esc_html_e('Form background options', SG_POPUP_TEXT_DOMAIN); ?></p>
				</div>
				<div class="subForm sgpb-bg-black__opacity-02 sgpb-padding-30 sgpb-width-100">
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-y-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Form background color', SG_POPUP_TEXT_DOMAIN); ?>:</span>
						<div class="colorPicker">
							<div class="sgpb-color-picker-wrapper unhideColorPicker">
								<input class="sgpb-color-picker js-subs-color-picker" data-subs-rel="sgpb-subscription-admin-wrapper" data-style-type="background-color" type="text" name="sgpb-subs-form-bg-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-form-bg-color')); ?>" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-y-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Form background opacity', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-slider-wrapper range-wrap">
							<div class="sgpb-slider-wrapper sgpb-range-wrap sgpb-display-inline-flex">
								<?php $overlayOpacity = $popupTypeObj->getOptionValue('sgpb-overlay-opacity'); ?>
								<input type="range" name="sgpb-subs-form-bg-opacity" class="sgpb-range-input js-subs-bg-opacity sgpb-cursor-pointer"
								       value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-form-bg-opacity')); ?>"
								       min="0.0" step="0.1" max="1">
								<span class="js-subs-bg-opacity-value sgpb-margin-left-10"><?php echo esc_html($overlayOpacity)?></span>
							</div>
						</div>
					</div>
					<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-margin-y-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Form padding', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input type="number" min="0" data-default="<?php echo esc_attr($popupTypeObj->getOptionDefaultValue('sgpb-subs-form-padding'))?>" class="js-sgpb-form-padding formItem__input" id="sgpb-subs-form-padding" name="sgpb-subs-form-padding" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-form-padding'))?>" autocomplete="off">
						<div class="formItem__inputValueType">px</div>
					</div>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Email placeholder', SG_POPUP_TEXT_DOMAIN); ?></span>
				<input type="text" name="sgpb-subs-email-placeholder" id="subs-email-placeholder" class="formItem__input formItem__input_sgpb-popup-overlay js-subs-field-placeholder" data-subs-rel="js-subs-email-input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-email-placeholder')); ?>">
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Enable GDPR', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="js-checkbox-accordion js-checkbox-field-status sgpb-onOffSwitch-checkbox" id="subs-gdpr-status" data-subs-field-wrapper="js-gdpr-wrapper" name="sgpb-subs-gdpr-status" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-gdpr-status')); ?>>
					<label class="sgpb-onOffSwitch__label" for="subs-gdpr-status">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
			<div class="formItem sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-padding-20">
				<div class="subFormItem sgpb-margin-bottom-20">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Label', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="text" name="sgpb-subs-gdpr-label" id="sgpb-subs-gdpr-label" class="js-subs-field-placeholder formItem__input formItem__input_sgpb-popup-overlay" data-subs-rel="js-subs-gdpr-label" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-gdpr-label')); ?>">
				</div>
				<div class="subFormItem sgpb-display-flex">
					<span class="subFormItem__title sgpb-margin-right-10 sgpb-text-nowrap"><?php esc_html_e('Confirmation text', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<textarea name="sgpb-subs-gdpr-text" id="sgpb-subs-gdpr-text" class="formItem__textarea"><?php echo esc_html($popupTypeObj->getOptionValue('sgpb-subs-gdpr-text')); ?></textarea>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('First name', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="js-checkbox-accordion js-checkbox-field-status sgpb-onOffSwitch-checkbox" id="subs-first-name-status" data-subs-field-wrapper="js-first-name-wrapper" name="sgpb-subs-first-name-status" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-first-name-status')); ?>>
					<label class="sgpb-onOffSwitch__label" for="subs-first-name-status">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
			<div class="formItem sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-padding-20">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="text" name="sgpb-subs-first-placeholder" id="subs-first-placeholder" class="formItem__input formItem__input_sgpb-popup-overlay js-subs-field-placeholder" data-subs-rel="js-subs-first-name-input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-first-placeholder')); ?>">
				</div>
				<div class="subFormItem sgpb-align-item-center sgpb-display-flex sgpb-margin-top-10">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Required field', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-subs-first-name-required" class="sgpb-onOffSwitch-checkbox" id="subs-first-name-required" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-first-name-required')); ?>>
						<label class="sgpb-onOffSwitch__label" for="subs-first-name-required">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Last name', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" class="js-checkbox-accordion js-checkbox-field-status sgpb-onOffSwitch-checkbox" id="subs-last-name-status" data-subs-field-wrapper="js-last-name-wrapper" name="sgpb-subs-last-name-status" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-last-name-status')); ?>>
					<label class="sgpb-onOffSwitch__label" for="subs-last-name-status">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
			<div class="formItem sgpb-width-100 sgpb-bg-black__opacity-02 sgpb-padding-20">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Placeholder', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input id="subs-last-placeholder" class="formItem__input formItem__input_sgpb-popup-overlay " data-subs-rel="js-subs-last-name-input"  type="text" name="sgpb-subs-last-placeholder" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-last-placeholder')); ?>">
				</div>
				<div class="subFormItem sgpb-align-item-center sgpb-display-flex sgpb-margin-top-10">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Required field', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-subs-last-name-required" class="sgpb-onOffSwitch-checkbox" id="subs-last-name-required" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-last-name-required')); ?>>
						<label class="sgpb-onOffSwitch__label" for="subs-last-name-required">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Required field message', SG_POPUP_TEXT_DOMAIN); ?></span>
				<input type="text" name="sgpb-subs-validation-message" id="subs-validation-message" class="formItem__input formItem__input_sgpb-popup-overlay sgpb-full-width-events" maxlength="90" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-validation-message')); ?>">
			</div>
			<div class="formItem">
				<div class="sgpb-width-100">
					<span class="formItem__title"><?php esc_html_e('Inputs\' style', SG_POPUP_TEXT_DOMAIN); ?></span>
				</div>
				<div class="subForm sgpb-bg-black__opacity-02 sgpb-padding-30 sgpb-width-100">
					<div class="subFormItem bottom">
						<div class="maxWidth sgpb-margin-bottom-20">
							<span class="subFormItem__title formItem__title_equals sgpb-margin-right-10"><?php esc_html_e('Width', SG_POPUP_TEXT_DOMAIN); ?>:</span>
							<input type="text" class="js-subs-dimension subFormItem__input" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="width" name="sgpb-subs-text-width" id="subs-text-width" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-width')); ?>">
						</div>
						<div class="maxHeight sgpb-margin-bottom-20">
							<span class="subFormItem__title formItem__title_equals sgpb-margin-right-10"><?php esc_html_e('Height', SG_POPUP_TEXT_DOMAIN); ?>:</span>
							<input class="js-subs-dimension subFormItem__input" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="height" type="text" name="sgpb-subs-text-height" id="subs-text-height" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-height')); ?>">
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Border width', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input class="js-subs-dimension formItem__input formItem__input_sgpb-popup-overlay" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="border-width" type="text" name="sgpb-subs-text-border-width" id="subs-text-border-width" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-border-width')); ?>">
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Background color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="background-color" type="text" name="sgpb-subs-text-bg-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-bg-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Border color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="border-color" type="text" name="sgpb-subs-text-border-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-border-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Text color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="color" type="text" name="sgpb-subs-text-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-text-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Placeholder color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="input" data-subs-rel="js-subs-text-inputs" data-style-type="placeholder" type="text" name="sgpb-subs-text-placeholder-color" value="<?php echo esc_attr($placeholderColor); ?>" >
						</div>
					</div>
				</div>
			</div>

			<div class="formItem">
				<div class="sgpb-width-100">
					<span class="formItem__title"><?php esc_html_e('Submit button styles', SG_POPUP_TEXT_DOMAIN); ?></span>
				</div>
				<div class="subForm sgpb-bg-black__opacity-02 sgpb-padding-30 sgpb-width-100">
					<div class="subFormItem bottom">
						<div class="maxWidth sgpb-margin-bottom-20">
							<span class="subFormItem__title formItem__title_equals sgpb-margin-right-10"><?php esc_html_e('Width', SG_POPUP_TEXT_DOMAIN); ?>:</span>
							<input class="js-subs-dimension subFormItem__input" data-subs-rel="js-subs-submit-btn" data-field-type="submit" data-style-type="width" type="text" name="sgpb-subs-btn-width" id="subs-btn-width" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-width')); ?>">
						</div>
						<div class="maxHeight sgpb-margin-bottom-20">
							<span class="subFormItem__title formItem__title_equals sgpb-margin-right-10"><?php esc_html_e('Height', SG_POPUP_TEXT_DOMAIN); ?>:</span>
							<input class="js-subs-dimension subFormItem__input" data-subs-rel="js-subs-submit-btn" data-field-type="submit" data-style-type="height" type="text" name="sgpb-subs-btn-height" id="subs-btn-height" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-height')); ?>">
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Border width', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input class="js-subs-dimension formItem__input" data-field-type="submit" data-subs-rel="js-subs-submit-btn" data-style-type="border-width" type="text" name="sgpb-subs-btn-border-width" id="sgpb-subs-btn-border-width" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-border-width')); ?>">
					</div>
					<div class="subFormItem sgpb-margin-bottom-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Border radius', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input class="js-subs-dimension formItem__input" data-subs-rel="js-subs-submit-btn" data-field-type="submit" data-style-type="border-radius" type="text" name="sgpb-subs-btn-border-radius" id="sgpb-subs-btn-border-radius" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-border-radius')); ?>">
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Border color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input id="sgpb-subs-btn-border-color" class="sgpb-color-picker js-subs-color-picker" data-field-type="submit" data-subs-rel="js-subs-submit-btn" data-style-type="border-color" type="text" name="sgpb-subs-btn-border-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-border-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Title', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input type="text" name="sgpb-subs-btn-title" id="subs-btn-title" class="formItem__input formItem__input_sgpb-popup-overlay js-subs-btn-title" data-field-type="submit" data-subs-rel="js-subs-submit-btn" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-title')); ?>">
					</div>
					<div class="subFormItem sgpb-margin-bottom-20">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Title (in progress)', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input type="text" name="sgpb-subs-btn-progress-title" id="btn-progress-title" class="formItem__input formItem__input_sgpb-popup-overlay" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-progress-title')); ?>">
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Background color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="submit" data-subs-rel="js-subs-submit-btn" data-style-type="background-color" type="text" name="sgpb-subs-btn-bg-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-bg-color')); ?>" >
						</div>
					</div>
					<div class="subFormItem sgpb-margin-bottom-20 sgpb-display-flex sgpb-align-item-center">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Text color', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<div class="sgpb-color-picker-wrapper unhideColorPicker">
							<input class="sgpb-color-picker js-subs-color-picker" data-field-type="submit" data-subs-rel="js-subs-submit-btn" data-style-type="color" type="text" name="sgpb-subs-btn-text-color" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-btn-text-color')); ?>" >
						</div>
					</div>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Error message', SG_POPUP_TEXT_DOMAIN); ?></span>
				<input type="text" class="formItem__input formItem__input_sgpb-popup-overlay" name="sgpb-subs-error-message" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-error-message')); ?>">
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Invalid email message', SG_POPUP_TEXT_DOMAIN); ?></span>
				<input type="text" class="formItem__input formItem__input_sgpb-popup-overlay" name="sgpb-subs-invalid-message" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-invalid-message')); ?>">
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Show form on the Top', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" id="sgpb-subs-show-form-to-top" class="sgpb-onOffSwitch-checkbox" name="sgpb-subs-show-form-to-top" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-show-form-to-top')); ?>>
					<label class="sgpb-onOffSwitch__label" for="sgpb-subs-show-form-to-top">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Hide for already subscribed users', SG_POPUP_TEXT_DOMAIN)?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" id="sgpb-subs-hide-subs-users" class="sgpb-onOffSwitch-checkbox" name="sgpb-subs-hide-subs-users" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-hide-subs-users')); ?>>
					<label class="sgpb-onOffSwitch__label" for="sgpb-subs-hide-subs-users">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
			</div>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('After successful subscription', SG_POPUP_TEXT_DOMAIN)?>:</span>
			</div>
			<div class="formItem sgpb-bg-black__opacity-02 sgpb-width-100 sgpb-padding-20 sgpb-margin-bottom-20">
				<?php
				$multipleChoiceButton = new MultipleChoiceButton($defaultData['subscriptionSuccessBehavior'], $popupTypeObj->getOptionValue('sgpb-subs-success-behavior'));
				echo wp_kses($multipleChoiceButton, $allowed_html);;
				?>
			</div>

			<div class="sg-hide sgpb-bg-black__opacity-02 sgpb-width-100 sgpb-padding-20 sgpb-margin-bottom-20" id="subs-show-success-message">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Success message', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input name="sgpb-subs-success-message" type="text" id="sgpb-subs-success-message" class="grayFormItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-success-message')); ?>">
				</div>
			</div>
			<div class="sg-hide sgpb-bg-black__opacity-02 sgpb-width-100 sgpb-padding-20 sgpb-margin-bottom-20" id="subs-redirect-to-URL">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Redirect URL', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="url" name="sgpb-subs-success-redirect-URL" id="sgpb-subs-success-redirect-URL" placeholder="https://www.example.com" class="grayFormItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-success-redirect-URL')); ?>">
				</div>
				<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Redirect to new tab', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-subs-success-redirect-new-tab" id="subs-success-redirect-new-tab" class="sgpb-onOffSwitch-checkbox" placeholder="https://www.example.com" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-subs-success-redirect-new-tab')); ?>>
						<label class="sgpb-onOffSwitch__label" for="subs-success-redirect-new-tab">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			</div>
			<div class="sg-hide sgpb-bg-black__opacity-02 sgpb-width-100 sgpb-padding-20 sgpb-margin-bottom-20" id="subs-open-popup">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Select popup', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<?php echo wp_kses(AdminHelper::createSelectBox($subscriptionSubPopups, $successPopup, array('name' => 'sgpb-subs-success-popup', 'class'=>'js-sg-select2 select__select')), $allowed_html); ?>
				</div>
			</div>
		</div>
		<div class="sgpb-width-30">
			<div class="sgpb-position-sticky sgpb-overflow-auto">
				<div class="livePreview livePreview_centered sgpb-margin-auto sgpb-align-item-center sgpb-btn sgpb-btn-gray-light sgpb-btn--rounded sgpb-display-flex sgpb-justify-content-center sgpb-no-hover sgpb-cursor-default">
					<img class="sgpb-margin-right-10" src="<?php echo esc_attr(SG_POPUP_PUBLIC_URL.'icons/Black/eye.svg'); ?>" alt="Eye icon">
					<span class="livePreview__text"><?php esc_html_e('Live Preview', SG_POPUP_TEXT_DOMAIN)?></span>
				</div>
				<div class="sgpb-margin-top-10 sgpb-subs-form-<?php echo esc_attr($popupId); ?> sgpb-subscription-admin-wrapper<?php echo esc_attr($forceRtlClass); ?>">
					<?php echo wp_kses(Functions::renderForm($formData), $allowed_html); ?>
				</div>
				<?php
				$styleData = array(
					'placeholderColor' => $placeholderColor
				);
				echo wp_kses($popupTypeObj->getFormCustomStyles($styleData), $allowed_html)
				?>
				<div style="max-width: 300px;margin: 0 auto;">
					<span class="sgpb-align-center"><?php _e('Get the <a href="'.SG_POPUP_SUBSCRIPTION_PLUS_URL.'">Subscription Plus</a> extension to add or customize the form fields.', SG_POPUP_TEXT_DOMAIN);?></span>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery('.js-subs-bg-opacity').on('change', function () {
		jQuery(this).siblings('span').text(this.value);
	})
</script>
