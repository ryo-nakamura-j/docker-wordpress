<?php
namespace sgpb;

class SGPBFeedback
{
	public function __construct()
	{
		add_action('current_screen', array($this, 'actionToCurrentScreen'));
		add_action('wp_ajax_sgpb_deactivate_feedback', array($this, 'sgpbDeactivateFeedback'));
	}

	public function actionToCurrentScreen() {
		if (!$this->isPluginsScreen()) {
			return;
		}

		add_filter('sgpbAdminJsFiles', array($this, 'adminJsFilter'), 1, 1);
		add_action('admin_footer', array($this, 'renderDeactivateFeedbackDialog'));
	}

	public function adminJsFilter($jsFiles)
	{
		$jsFiles[] = array(
			'folderUrl' => SG_POPUP_JS_PATH,
			'filename' => 'Banner.js'
		);

		return $jsFiles;
	}

	public function sgpbDeactivateFeedback()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!empty($_POST['formData'])) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str($_POST['formData'],$submissionData);
		}
		array_walk_recursive($submissionData, function(&$item){
			$item = sanitize_text_field($item);
		});
		$feedbackKey = $feedbackText = 'Skipped';
		if (!empty($submissionData['reasonKey'])) {
			$feedbackKey = $submissionData['reasonKey'];
		}

		if (!empty($submissionData["reason_{$feedbackKey}"])) {
			$feedbackText = $submissionData["reason_{$feedbackKey}"];
		}
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'From: feedbackpopupbuilder@gmail.com'."\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n"; //set UTF-8

		$receiver = 'feedbackpopupbuilder@gmail.com';
		$title = 'Popup Builder Deactivation Feedback From Customer';
		$message .= 'Feedback key - '.$feedbackKey.'<br>'."\n";
		$message .= 'Feedback text - '.$feedbackText."\n";

		wp_mail($receiver, $title, $message, $headers);

		wp_die(1);
	}

	public function renderDeactivateFeedbackDialog() {
		$deactivateReasons = array(
			'no_longer_needed' => array(
				'title' => __('I no longer need the plugin', SG_POPUP_TEXT_DOMAIN),
				'input_placeholder' => ''
			),
			'found_a_better_plugin' => array(
				'title' => __('I found a better plugin', SG_POPUP_TEXT_DOMAIN),
				'input_placeholder' => __( 'Please share which plugin', SG_POPUP_TEXT_DOMAIN)
			),
			'couldnt_get_the_plugin_to_work' => array(
				'title' => __('I couldn\'t get the plugin to work', SG_POPUP_TEXT_DOMAIN),
				'input_placeholder' => '',
				'extra_help' => __('Having troubles? You can always count on us. Please try to contact us via <a href="https://popup-builder.com/">Live chat</a> or send a message to <a href="mailto:support@popup-builder.com">support@popup-builder.com</a>', SG_POPUP_TEXT_DOMAIN)
			),
			'temporary_deactivation' => array(
				'title' => __('It\'s a temporary deactivation', SG_POPUP_TEXT_DOMAIN),
				'input_placeholder' => ''
			),
			'other' => array(
				'title' => __('Other', SG_POPUP_TEXT_DOMAIN),
				'input_placeholder' => __('Please share the reason', SG_POPUP_TEXT_DOMAIN),
			)
		);

		?>
		<div id="sgpb-feedback-popup">
			<div class="sgpb-feedback-popup-wrapper">
				<div class="sgpb-wrapper">
					<div class="row sgpb-feedback-popup-header sgpb-position-relative">
						<div class="col-sm-3 sgpb-add-subscriber-header-column">
							<h4>
								<?php esc_html_e('Quick Feedback', SG_POPUP_TEXT_DOMAIN)?>
							</h4>
						</div>
						<div class="col-sm-1 sgpb-add-subscriber-header-spinner-column">
							<img src="<?php echo esc_attr(SG_POPUP_IMG_URL.'ajaxSpinner.gif'); ?>" alt="gif" class="sgpb-subscribers-add-spinner js-sg-spinner js-sgpb-add-spinner sg-hide-element js-sg-import-gif" width="20px">
						</div>
						<img src="<?php echo esc_attr(SG_POPUP_IMG_URL.'subscribers_close.png'); ?>" alt="gif" class="sgpb-add-subscriber-popup-close-btn sgpb-subscriber-data-popup-close-btn-js" width="20px">
					</div>
					<div class="row">
						<div class="col-md-12">
							<h4 class="sgpb-feedback-descritpion">
								<?php _e('If you have a moment, please share why you are deactivating <b>Popup Builder</b>', SG_POPUP_TEXT_DOMAIN)?>:
							</h4>
							<p class="sgpb-feedback-error-message sg-hide-element"><?php esc_html_e('Please, select an option.', SG_POPUP_TEXT_DOMAIN)?></p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<form id="sgpb-deactivate-feedback-dialog-form" method="post">
								<?php foreach ($deactivateReasons as $reasonKey => $reason) : ?>
								<div class="row sgpb-feedback-each-reason-row">
									<div class="col-md-1">
										<input id="sgpb-deactivate-feedback-<?php echo esc_attr($reasonKey); ?>" class="sgpb-deactivate-feedback-dialog-input" type="radio" name="reasonKey" value="<?php echo esc_attr($reasonKey); ?>" />
									</div>
									<div class="col-md-11">
										<label for="sgpb-deactivate-feedback-<?php echo esc_attr($reasonKey); ?>" class="sgpb-deactivate-feedback-dialog-label"><?php echo esc_html($reason['title']); ?></label>
										<?php if (!empty($reason['input_placeholder'])) : ?>
											<input class="sgpb-feedback-text sgpb-feedback-text-input" style="display: none;" type="text" name="reason_<?php echo esc_attr( $reasonKey ); ?>" placeholder="<?php echo esc_attr($reason['input_placeholder']); ?>" />
										<?php endif; ?>
										<?php if (!empty($reason['extra_help'])) : ?>
											<p class="sgpb-feedback-text-input" style="display: none;"><?php echo wp_kses($reason['extra_help'], 'post'); ?></p>
										<?php endif; ?>
									</div>
								</div>
								<?php endforeach; ?>
								<div class="row sgpb-feedback-btns-wrapper">
									<div class="col-md-6">
										<input type="button" class="btn btn-sm btn-success sgpb-feedback-submit" name="sgpb-feedback-submit" value="<?php esc_html_e('Submit & Deactivate', SG_POPUP_TEXT_DOMAIN); ?>">
									</div>
									<div class="col-md-6">
										<input type="button" class="btn btn-sm sgpb-feedback-submit-skip" name="sgpb-feedback-submit-skip" value="<?php esc_html_e('Skip & Deactivate', SG_POPUP_TEXT_DOMAIN); ?>">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function isPluginsScreen() {
		return in_array(get_current_screen()->id, array('plugins', 'plugins-network'));
	}
}
