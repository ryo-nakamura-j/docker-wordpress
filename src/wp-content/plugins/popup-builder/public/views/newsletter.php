<?php
	require_once SG_POPUP_CLASSES_POPUPS_PATH.'SubscriptionPopup.php';
	use sgpb\SubscriptionPopup;
	use sgpb\AdminHelper;
	$adminEmail = get_option('admin_email');
	$newsletterSavedOptions = get_option('SGPB_NEWSLETTER_DATA');
	$subscriptionIdTitle = SubscriptionPopup::getAllSubscriptionForms();

	$subscriptionSelectBox = AdminHelper::createSelectBox(
		$subscriptionIdTitle,
		'',
		array(
			'name' => 'sgpb-subscription-form',
			'class' => 'js-sg-select2 js-sg-newsletter-forms js-sg-select2 js-select-basic',
			'autocomplete' => 'off'
		)
	);

	reset($subscriptionIdTitle);
	$defaultSelectedPopupId = key($subscriptionIdTitle);
	$subscriptionPopupsCustomFields = AdminHelper::getCustomFormFieldsByPopupId($defaultSelectedPopupId);
?>
<!-- formItem__input_sgpb-popup-overlay -->
<div class="sgpb sgpb-wrapper ">
	<h2 class="sgpb-header-h1 sgpb-margin-y-20"><?php esc_html_e('Newsletter', SG_POPUP_TEXT_DOMAIN); ?></h2>
	<div class="sgpb-newsletter sgpb-display-flex sgpb-padding-20">
		<div class="sgpb-width-50 sgpb-padding-x-20">
			<div class="sgpb-alert sgpb-newsletter-notice sgpb-alert-info fade in sgpb-hide">
				<span class="sgpb-newsletter-success-message sgpb-hide"><?php esc_html_e('You will receive an email notification after all emails are sent', SG_POPUP_TEXT_DOMAIN); ?>.</span>
				<span class="sgpb-newsletter-test-success-message sgpb-hide"><?php esc_html_e('Test email was successfully sent', SG_POPUP_TEXT_DOMAIN); ?>.</span>
			</div>
			<div class="formItem sgpb-margin-top-0">
				<h3 class="formItem__title"><?php esc_html_e('Newsletter settings', SG_POPUP_TEXT_DOMAIN); ?></h3>
			</div>
			<div class="sgpb-bg-black__opacity-02 sgpb-padding-x-30 sgpb-padding-y-10">
				<div class="formItem">
					<div class="titleQuestionWrapper">
						<span class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Choose the popup', SG_POPUP_TEXT_DOMAIN); ?></span>
					</div>
					<?php echo wp_kses($subscriptionSelectBox, AdminHelper::allowed_html_tags()); ?>
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Emails to send in one flow per 1 minute', SG_POPUP_TEXT_DOMAIN); ?></div>
					<input type="number" id="sgpb-emails-in-flow" class="sgpb-emails-in-flow formItem__input" value="<?php echo esc_attr(SGPB_FILTER_REPEAT_INTERVAL); ?>">
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('From email', SG_POPUP_TEXT_DOMAIN); ?></div>
					<input type="email" id="sgpb-newsletter-from-email" class="sgpb-newsletter-from-email formItem__input formItem__input_sgpb-popup-overlay" value="<?php echo esc_attr($adminEmail); ?>">
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-margin-right-10"><?php esc_html_e('Email\'s subject', SG_POPUP_TEXT_DOMAIN); ?></div>
					<input type="email" id="sgpb-newsletter-subject" class="sgpb-newsletter-subject formItem__input formItem__input_sgpb-popup-overlay" value="<?php echo esc_attr((empty($newsletterSavedOptions['newsletterSubject'])) ? esc_html_e('Your subject here', SG_POPUP_TEXT_DOMAIN) : $newsletterSavedOptions['newsletterSubject']); ?>">
				</div>
			</div>

			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('Edit newsletter email template below', SG_POPUP_TEXT_DOMAIN); ?></span>
			</div>
			<div class="mediaEditor">
				<?php
				$editorId = 'sgpb-newsletter-text';
				$content = !empty($newsletterSavedOptions['messageBody'])?$newsletterSavedOptions['messageBody']:'';
				if (empty($content)) {
					$content = '<p>Hi [First name] [Last name],</p>
					<p>Super excited to have you on board, we know you’ll just love us.</p>
					<p>Sincerely,</p>
					<p>[Blog name]</p>
					<p>[Unsubscribe title="Unsubscribe"]</p>';
				}
				$settings = array(
					'wpautop' => false,
					'tinymce' => array(
						'width' => '100%'
					),
					'textarea_rows' => '18',
					'media_buttons' => true
				);
				wp_editor($content, $editorId, $settings);
				?>
			</div>

			<div class="sgpb-display-flex sgpb-justify-content-between">
				<div class="formItem">
					<input class="sgpb-newlsetter-test-emails sgpb-margin-right-10" type="text" name="sgpb-newlsetter-test-emails">
					<input type="submit" class="sgpb-btn sgpb-btn-blue js-send-newsletter" style="line-height: 2;" data-send-type="test" value="<?php esc_html_e('Send a Test', SG_POPUP_TEXT_DOMAIN)?>">
					<img src="<?php echo esc_url_raw(SG_POPUP_IMG_URL.'ajaxSpinner.gif'); ?>" width="20px" class="sgpb-hide sgpb-js-newsletter-spinner">
				</div>
				<div>
					<input type="submit" class="sgpb-btn sgpb-btn-blue sgpb-margin-top-20 js-send-newsletter sendButton" value="<?php esc_html_e('Send newsletter', SG_POPUP_TEXT_DOMAIN)?>">
					<img src="<?php echo esc_url_raw(SG_POPUP_IMG_URL.'ajaxSpinner.gif'); ?>" width="20px" class="sgpb-hide sgpb-js-newsletter-spinner">
				</div>
			</div>

		</div>
		<div class="sgpb-width-30">
			<div class="sgpb-position-sticky sgpb-border-radius-5px sgpb-padding-20 sgpb-shadow-black-10" style="top: 7%!important;">
				<h2 class="sgpb-header-h4"><?php esc_html_e('Newsletter Shortcodes', SG_POPUP_TEXT_DOMAIN); ?></h2>
				<div class="formItem">
					<span class="formItem__title"><?php esc_html_e('Default shortcodes', SG_POPUP_TEXT_DOMAIN); ?>:</span>
				</div>
				<input type="button" id="sgpb-newsletter-shortcode-firstName" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php esc_html_e('Subscriber First name', SG_POPUP_TEXT_DOMAIN); ?>" data-value="[First name]">
				<input type="button" id="sgpb-newsletter-shortcode-lastName" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php esc_html_e('Subscriber Last name', SG_POPUP_TEXT_DOMAIN); ?>" data-value="[Last name]">
				<input type="button" id="sgpb-newsletter-shortcode-blogName" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php esc_html_e('Your blog name', SG_POPUP_TEXT_DOMAIN); ?>" data-value="[Blog name]">
				<input type="button" id="sgpb-newsletter-shortcode-userName" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php esc_html_e('Your user name', SG_POPUP_TEXT_DOMAIN); ?>" data-value="[User name]">
				<input type="button" id="sgpb-newsletter-shortcode-unsubscribe" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php esc_html_e('Unsubscribe', SG_POPUP_TEXT_DOMAIN); ?>" data-value="[Unsubscribe]">

				<?php if (!empty($subscriptionPopupsCustomFields)) :?>
					<div class="formItem">
						<span class="formItem__title"><?php esc_html_e('Custom fields\' shortcodes', SG_POPUP_TEXT_DOMAIN); ?>:</span>
					</div>
					<img src="<?php echo esc_url_raw(SG_POPUP_IMG_URL.'ajaxSpinner.gif'); ?>" width="20px" class="sgpb-hide sgpb-js-newsletter-custom-fields-spinner">
					<?php
					foreach ($subscriptionPopupsCustomFields as $index => $field) {
						if (empty($field)) {
							continue;
						}
						$fieldName = isset($field['fieldName']) ? $field['fieldName'] : ''
						?>
						<input type="button" id="sgpb-newsletter-shortcode-<?php echo esc_attr($index); ?>" class="sgpb-btn sgpb-btn-blue-light sgpb-margin-5 buttonGroup__button_shortcodes" value="<?php echo esc_attr($fieldName); ?>" data-value="[<?php echo esc_attr($fieldName);?>]">
						<?php
					}
					?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
