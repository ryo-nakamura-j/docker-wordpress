<?php
use sgpb\AdminHelper;
use sgpb\MultipleChoiceButton;
use sgpb\PopupBuilderActivePackage;

$allowed_html = AdminHelper::allowed_html_tags();
$removedOptions = $popupTypeObj->getRemoveOptions();
$defaultData = ConfigDataHelper::defaultData();
$defaultAnimation = esc_attr($popupTypeObj->getOptionValue('sgpb-open-animation-effect'));
if (!empty($_GET['sgpb_type'])) {
	if (defined('SGPB_POPUP_TYPE_RECENT_SALES')) {
		if (sanitize_text_field($_GET['sgpb_type']) == defined('SGPB_POPUP_TYPE_RECENT_SALES') && !$popupTypeObj->getOptionValue('sgpb-open-animation-effect')) {
			$defaultAnimation = 'sgpb-fadeIn';
		}
	}
}

$afterXpagesUseOption = PopupBuilderActivePackage::canUseOption('sgpb-show-popup-after-x-pages');
if (!empty($removedOptions['content-copy-to-clipboard'])) {
	if (isset($defaultData['contentClickOptions']['fields'])) {
		// where 2 is copy to clipboard index
		unset($defaultData['contentClickOptions']['fields'][2]);
	}
}
?>
<div class="sgpb sgpb-wrapper popupOptions">
	<?php if(empty($removedOptions['sgpb-content-click'])): ?>
		<div class="formItem">
			<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Action on popup content click', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" id="sgpb-content-click" name="sgpb-content-click" class="sgpb-onOffSwitch-checkbox js-checkbox-accordion" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-content-click')); ?>>
				<label class="sgpb-onOffSwitch__label" for="sgpb-content-click">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
		</div>
		<div class="sg-full-width sgpb-bg-black__opacity-02 sgpb-padding-x-20 formItem">
			<?php
			$multipleChoiceButton = new MultipleChoiceButton($defaultData['contentClickOptions'], $popupTypeObj->getOptionValue('sgpb-content-click-behavior'));
			echo wp_kses($multipleChoiceButton, $allowed_html);;
			?>
			<div class="sgpb-bg-black__opacity-02 sg-hide sg-full-width sgpb-padding-20" id="content-click-redirect">
				<div class="subFormItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('URL', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="url" name="sgpb-click-redirect-to-url" id="redirect-to-url" class="grayFormItem__input" placeholder="http://" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-click-redirect-to-url')); ?>">
				</div>
				<div class="formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Redirect to new tab', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input id="redirect" class="sgpb-onOffSwitch-checkbox" type="checkbox" name="sgpb-redirect-to-new-tab" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-redirect-to-new-tab'));?>>
						<label class="sgpb-onOffSwitch__label" for="redirect">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
			</div>

			<div class="sgpb-bg-black__opacity-02 sg-hide sg-full-width sgpb-padding-20" id="content-copy-to-clipboard">
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Text', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="text" name="sgpb-copy-to-clipboard-text" id="sgpb-copy-to-clipboard-text" class="subFormItem__input" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-copy-to-clipboard-text')); ?>">
				</div>
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Close popup', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input class="sgpb-onOffSwitch-checkbox" type="checkbox" name="sgpb-copy-to-clipboard-close-popup" id="sgpb-copy-to-clipboard-close-popup" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-copy-to-clipboard-close-popup')); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-copy-to-clipboard-close-popup">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Show alert', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" id="sgpb-copy-to-clipboard-alert" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-copy-to-clipboard-alert" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-copy-to-clipboard-alert')); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-copy-to-clipboard-alert">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="sg-full-width">
					<div class="subFormItem formItem">
						<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Message', SG_POPUP_TEXT_DOMAIN)?>:</span>
						<input type="text" id="sgpb-copy-to-clipboard-message" class="subFormItem__input" name="sgpb-copy-to-clipboard-message" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-copy-to-clipboard-message')); ?>">
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if (empty($removedOptions['sgpb-show-popup-same-user'])): ?>
		<div class="formItem">
			<span class="formItem__title"><?php esc_html_e('Popup showing limitation', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" id="sgpb-show-popup-same-user" name="sgpb-show-popup-same-user" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-show-popup-same-user')); ?>>
				<label class="sgpb-onOffSwitch__label" for="sgpb-show-popup-same-user">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('Estimate the popup showing frequency to the same user.', SG_POPUP_TEXT_DOMAIN);?>
				</span>
			</div>
		</div>
		<div class="sg-full-width sgpb-bg-black__opacity-02 sgpb-padding-x-20 formItem">
			<div class="subForm noPadding">
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Popup showing count', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="number" min="1" disabled required id="sgpb-show-popup-same-user-count" class="subFormItem__input" name="sgpb-show-popup-same-user-count" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-show-popup-same-user-count')); ?>" placeholder="e.g.: 1">
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('Select how many times the popup will be shown for the same user.', SG_POPUP_TEXT_DOMAIN);?>
						</span>
					</div>
				</div>
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Popup showing expiry', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<input type="number" min="0" disabled required id="sgpb-show-popup-same-user-expiry" class="subFormItem__input" name="sgpb-show-popup-same-user-expiry" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-show-popup-same-user-expiry')); ?>" placeholder="e.g.: 1">
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('Select the count of the days after which the popup will be shown to the same user, or set the value "0" if you want to save cookies by session.', SG_POPUP_TEXT_DOMAIN);?>
						</span>
					</div>
				</div>
				<div class="subFormItem formItem">
					<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Apply option on each page', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="checkbox-wrapper">
						<input type="checkbox" disabled id="sgpb-show-popup-same-user-page-level" name="sgpb-show-popup-same-user-page-level" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-show-popup-same-user-page-level')); ?>>
						<label class="checkboxLabel" for="sgpb-show-popup-same-user-page-level"></label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('If this option is checked the popup showing limitation will be saved for the current page. Otherwise, the limitation will refer site wide, and the popup will be shown for specific times on each page selected.The previously specified count of days will be reset every time you check/uncheck this option.', SG_POPUP_TEXT_DOMAIN);?>
						</span>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="formItem">
		<span class="formItem__title">
			<?php esc_html_e('Popup opening sound', SG_POPUP_TEXT_DOMAIN); ?>:
		</span>
		<div class="sgpb-onOffSwitch">
			<input type="checkbox" id="open-sound" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-open-sound" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-open-sound')); ?>>
			<label class="sgpb-onOffSwitch__label" for="open-sound">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
		<div class="question-mark">B</div>
		<div class="sgpb-info-wrapper">
			<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
				<?php esc_html_e('If this option is enabled the popup will appear with a sound. The sound option is not available on mobile devices, as there are restrictions on sound auto-play options for mobile devices.', SG_POPUP_TEXT_DOMAIN)?>
			</span>
		</div>
	</div>
	<div class="subForm formItem sgpb-padding-x-20 sgpb-width-100">
		<div class="musicForm">
			<div class="musicFormItem sgpb-display-flex">
				<input type="text" id="js-sound-open-url" readonly class="musicFormItem__input sgpb-margin-right-20 sgpb-width-50" name="sgpb-sound-url" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-sound-url')); ?>">
				<div class="sgpb-icons icons_blue sgpb-js-preview-sound">J</div>
			</div>
			<div class="musicFormItem sgpb-width-50 sgpb-margin-top-10 sgpb-display-flex sgpb-justify-content-center musicFormItem_last">
				<div class="sgpb-icons icons__item_first icons_blue" id="js-upload-open-sound-button">K</div>
				<div class="sgpb-icons icons_pink" data-default-song="<?php echo esc_attr($popupTypeObj->getOptionDefaultValue('sgpb-sound-url')); ?>" id="js-reset-to-default-song">I</div>
			</div>
		</div>
	</div>
	<div class="formItem">
		<span class="formItem__title"><?php esc_html_e('Popup opening animation', SG_POPUP_TEXT_DOMAIN); ?>:</span>
		<div class="sgpb-onOffSwitch">
			<input type="checkbox" id="open-animation" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-open-animation" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-open-animation')); ?>>
			<label class="sgpb-onOffSwitch__label" for="open-animation">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
	</div>
	<div class="subForm sgpb-padding-x-20 sgpb-width-100 sgpb-bg-black__opacity-02">
		<div class="subForm formItem sgpb-align-item-baseline sgpb-flex-direction-column sgpb-select2-input-styles-animation-effect">
			<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-position-relative sgpb-margin-bottom-20">
				<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Type', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['openAnimationEfects'], $defaultAnimation, array('name' => 'sgpb-open-animation-effect', 'class'=>'js-sg-select2 sgpb-open-animation-effects select__select')), AdminHelper::allowed_html_tags()); ?>
				<div class="sgpb-icons icons_blue sgpb-preview-animation sgpb-margin-x-20 sgpb-preview-open-animation">A</div>
				<div id="js-open-animation-effect" class="sgpb-js-open-animation-effect"></div>
			</div>
			<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
				<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Speed', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				<input type="number"
				       id="sgpb-open-animation-speed" name="sgpb-open-animation-speed" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-open-animation-speed'))?>"
				       data-default="<?php echo esc_attr($popupTypeObj->getOptionDefaultValue('sgpb-open-animation-speed'))?>"
				       class="subFormItem__input subFormItem__input_leftRounded" />
				<div class="sgpb-margin-left-10 subFormItem__text__rightRounded">
					<?php esc_html_e('Second(s)', SG_POPUP_TEXT_DOMAIN); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="formItem">
		<span class="formItem__title"><?php esc_html_e('Popup closing animation', SG_POPUP_TEXT_DOMAIN); ?>:</span>
		<div class="sgpb-onOffSwitch">
			<input type="checkbox" id="close-animation" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-close-animation" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-close-animation')); ?>>
			<label class="sgpb-onOffSwitch__label" for="close-animation">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
	</div>
	<div class="subForm sgpb-padding-x-20 sgpb-width-100 sgpb-bg-black__opacity-02">
		<div class="subForm formItem sgpb-align-item-baseline sgpb-flex-direction-column sgpb-select2-input-styles-animation-effect">
			<div class="subFormItem sgpb-display-flex sgpb-align-item-center sgpb-position-relative sgpb-margin-bottom-20">
				<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Type', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['closeAnimationEfects'], $popupTypeObj->getOptionValue('sgpb-close-animation-effect'), array('name' => 'sgpb-close-animation-effect', 'class'=>'js-sg-select2 sgpb-close-animation-effects select__select')), $allowed_html); ?>
				<div class="sgpb-icons icons_blue sgpb-preview-animation sgpb-margin-x-20 sgpb-preview-close-animation">A</div>
				<div id="js-close-animation-effect" class="sgpb-js-close-animation-effect"></div>
			</div>
			<div class="subFormItem sgpb-display-flex sgpb-align-item-center">
				<span class="subFormItem__title sgpb-margin-right-20"><?php esc_html_e('Speed', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				<input type="number"
				       min="0" step="0.1" data-default="<?php echo esc_attr($popupTypeObj->getOptionDefaultValue('sgpb-close-animation-speed'))?>"
				       id="sgpb-close-animation-speed" name="sgpb-close-animation-speed" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-close-animation-speed'))?>"
				       class="subFormItem__input subFormItem__input_leftRounded" />
				<div class="sgpb-margin-left-10 subFormItem__text__rightRounded">
					<?php esc_html_e('Second(s)', SG_POPUP_TEXT_DOMAIN); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="formItem">
		<span class="formItem__title"><?php esc_html_e('Popup location', SG_POPUP_TEXT_DOMAIN); ?>:</span>
		<div class="sgpb-onOffSwitch">
			<input type="checkbox" id="popup-fixed" class="js-checkbox-accordion sgpb-onOffSwitch-checkbox" name="sgpb-popup-fixed" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-popup-fixed')); ?>>
			<label class="sgpb-onOffSwitch__label" for="popup-fixed">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
	</div>
	<div class="sgpb-padding-20 sgpb-width-70 sgpb-bg-black__opacity-02">
		<div class="subForm subForm_squared">
			<div class="sgpb-squares">
				<div id="fixed-position1" data-sgvalue="1" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_leftRounded"></div>
				<div id="fixed-position2" data-sgvalue="2" class="js-fixed-position-style sgpb-squares__square"></div>
				<div id="fixed-position3" data-sgvalue="3" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_rightRounded"></div>
				<div id="fixed-position4" data-sgvalue="4" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_leftRounded"></div>
				<div id="fixed-position5" data-sgvalue="5" class="js-fixed-position-style sgpb-squares__square"></div>
				<div id="fixed-position6" data-sgvalue="6" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_rightRounded"></div>
				<div id="fixed-position7" data-sgvalue="7" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_leftRounded"></div>
				<div id="fixed-position8" data-sgvalue="8" class="js-fixed-position-style sgpb-squares__square"></div>
				<div id="fixed-position9" data-sgvalue="9" class="js-fixed-position-style sgpb-squares__square sgpb-squares__square_rightRounded"></div>
			</div>
			<input type="hidden" name="sgpb-popup-fixed-position" class="js-fixed-position" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-popup-fixed-position'));?>">
		</div>
	</div>
	<?php if (empty($removedOptions['sgpb-disable-page-scrolling'])): ?>
		<div class="formItem">
			<span class="formItem__title"><?php esc_html_e('Disable page scrolling', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" id="disable-page-scrolling" class="sgpb-onOffSwitch-checkbox" name="sgpb-disable-page-scrolling" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-disable-page-scrolling')); ?>>
				<label class="sgpb-onOffSwitch__label" for="disable-page-scrolling">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('If this option is checked, the page won\'t scroll until the popup is open.', SG_POPUP_TEXT_DOMAIN)?>
				</span>
			</div>
		</div>
	<?php endif; ?>
	<?php if (empty($removedOptions['sgpb-enable-content-scrolling'])): ?>
		<div class="formItem">
			<span class="formItem__title"><?php esc_html_e('Enable content scrolling', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" id="content-scrolling" class="sgpb-onOffSwitch-checkbox" name="sgpb-enable-content-scrolling" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-enable-content-scrolling')); ?>>
				<label class="sgpb-onOffSwitch__label" for="content-scrolling">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('If the content is larger than the specified dimensions, then the content will be scrollable.', SG_POPUP_TEXT_DOMAIN)?>
				</span>
			</div>
		</div>
	<?php endif; ?>

	<?php if (empty($removedOptions['sgpb-reopen-after-form-submission'])): ?>
		<div class="formItem">
			<span class="formItem__title"><?php esc_html_e('Reopen after form submission', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<div class="sgpb-onOffSwitch">
				<input type="checkbox" class="sgpb-onOffSwitch-checkbox" id="reopen-after-form-submission"  name="sgpb-reopen-after-form-submission" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-reopen-after-form-submission')); ?>>
				<label class="sgpb-onOffSwitch__label" for="reopen-after-form-submission">
					<span class="sgpb-onOffSwitch-inner"></span>
					<span class="sgpb-onOffSwitch-switch"></span>
				</label>
			</div>
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('If this option is enabled, the popup will reopen after the form submission.', SG_POPUP_TEXT_DOMAIN)?>
				</span>
			</div>
		</div>
	<?php endif; ?>
	<?php if (empty($removedOptions['sgpb-popup-order'])): ?>
		<div class="formItem formItem_itemsCentered">
			<span class="formItem__title"><?php esc_html_e('Popup order', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<input type="number" min="0" name="sgpb-popup-order" id="sgpb-popup-order" class="formItem__input" value="<?php echo esc_attr((int)$popupTypeObj->getOptionValue('sgpb-popup-order')); ?>">
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('Select the priority number for your popup to appear on the page, along with other popups. The popup with a higher-order number will be behind the others with a lower-order number.', SG_POPUP_TEXT_DOMAIN); ?>
				</span>
			</div>
		</div>
	<?php endif; ?>
	<?php if (empty($removedOptions['sgpb-popup-delay'])): ?>
		<div class="formItem formItem_itemsCentered">
			<span class="formItem__title"><?php esc_html_e('Custom event delay', SG_POPUP_TEXT_DOMAIN)?>:</span>
			<input type="number" min="0" name="sgpb-popup-delay" id="sgpb-popup-delay" class="formItem__input" value="<?php echo (int)esc_attr($popupTypeObj->getOptionValue('sgpb-popup-delay')); ?>">
			<div class="question-mark">B</div>
			<div class="sgpb-info-wrapper">
				<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
					<?php esc_html_e('You can add an opening delay for the popup, in seconds. This will refer to custom events, like:
										Shortcodes, custom CSS classes, HTML attributes, or JS called custom events.', SG_POPUP_TEXT_DOMAIN)?>
				</span>
			</div>
		</div>
	<?php endif; ?>
</div>
