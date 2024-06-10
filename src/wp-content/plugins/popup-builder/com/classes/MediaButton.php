<?php
namespace sgpb;

class MediaButton
{
	private $hideMediaButton = true;

	public function __toString()
	{
		return $this->render();
	}

	public function __construct($hideMediaButton = true)
	{
		$this->hideMediaButton = $hideMediaButton;
	}

	public static function allowToShow()
	{
		global $pagenow, $typenow;

		$allowToShow = false;
		$pages = array(
			'page.php',
			'post-new.php',
			'post-edit.php',
			'widgets.php'
		);

		$checkPage = in_array(
			$pagenow,
			$pages
		);

		// for show in plugins page when package is pro
		if (SGPB_POPUP_PKG !== SGPB_POPUP_PKG_FREE) {
			array_push($pages, 'post.php');
		}

		return ($pages && $typenow != 'download');
	}

	private function allowToShowJsVariable()
	{
		return get_post_type() == SG_POPUP_POST_TYPE;
	}

	public function render()
	{
		if (!$this->hideMediaButton && $this->allowToShowJsVariable()) {
			return '';
		}
		$output = $this->mediaButton();
		$output .= $this->insertJsVariable();

		return $output;
	}

	private function insertJsVariable()
	{
		if (!$this->allowToShowJsVariable()){
			return '';
		}

		$buttonTitle = __('Insert custom JS variable', SG_POPUP_TEXT_DOMAIN);
		ob_start();
			@include(SG_POPUP_VIEWS_PATH.'jsVariableView.php');
		$jsVariableContent = ob_get_contents();
		ob_end_clean();

		$img = '<span class="dashicons dashicons-welcome-widgets-menus" style="padding: 3px 2px 0px 0px"></span>';
		$output = '<a data-id="sgpb-js-variable-wrapper" href="javascript:void(0);" class="button sgpb-insert-js-variable" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';
		if (!$this->hideMediaButton) {
			$output = '';
		}

		return $output.$jsVariableContent;
	}

	private function mediaButton()
	{
		$allowToShow = MediaButton::allowToShow();
		if (!$allowToShow) {
			$output = '';
			return $output;
		}
		$currentPostType = AdminHelper::getCurrentPostType();
		$mediaButtonContent = '';
		if (!empty($currentPostType) && $currentPostType == SG_POPUP_POST_TYPE) {
			global $post;
			$elementorContent = get_post_meta($post->ID, '_elementor_edit_mode', true);
			if (!empty($elementorContent) && $elementorContent == 'builder'){
				ob_start();
					@include(SG_POPUP_VIEWS_PATH.'htmlCustomButtonElement.php');
				$mediaButtonContent = ob_get_contents();
				ob_end_clean();
			}else {
				add_action('admin_footer', function() {
					require_once(SG_POPUP_VIEWS_PATH.'htmlCustomButtonElement.php');
				});
			}
		}

		ob_start();
			@include(SG_POPUP_VIEWS_PATH.'mediaButton.php');
		$mediaButtonContent .= ob_get_contents();
		ob_end_clean();

		$showCurrentUser = AdminHelper::showMenuForCurrentUser();
		$screen = get_current_screen();
		$hideInNewsletter = isset($screen) ? $screen->id === 'popupbuilder_page_sgpbNewsletter' : false;
		if (!$showCurrentUser || $hideInNewsletter) {
			return '';
		}

		$buttonTitle = __('Insert popup', SG_POPUP_TEXT_DOMAIN);

		$img = '<span class="dashicons dashicons-welcome-widgets-menus" style="padding: 3px 2px 0px 0px"></span>';
		$output = '<a data-id="sgpb-hidden-media-popup" href="javascript:void(0);" class="button sgpb-insert-media-button-js" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';
		if (!$this->hideMediaButton) {
			$output = '';
		}

		return $output.$mediaButtonContent;
	}
}
