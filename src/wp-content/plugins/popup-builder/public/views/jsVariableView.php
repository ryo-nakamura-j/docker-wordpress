<?php
namespace sgpb;
?>

<div class="sgpb-hide" xmlns="http://www.w3.org/1999/html">
	<div id="sgpb-js-variable-wrapper" class="sgpb-wrapper sgpb">
		<div class="row">
			<div class="col-sm-10">
				<label><?php esc_html_e('Insert JS variable inside the popup', SG_POPUP_TEXT_DOMAIN)?></label>
			</div>
			<div class="col-sm-2">
				<img class="sgpb-add-subscriber-popup-close-btn sgpb-close-media-popup-js" src="<?php echo esc_url_raw(SG_POPUP_IMG_URL.'subscribers_close.png'); ?>" width='15px'>
			</div>
		</div>
		<div class="sgpb-insert-popup-title-border"></div>
		<div class="row form-group">
			<div class="col-sm-2 sgpb-static-padding-top">
				<label for="sgpb-js-variable-selector"><?php esc_html_e('Selector', SG_POPUP_TEXT_DOMAIN)?>:</label>
			</div>
			<div class="col-sm-5">
				<input type="text" id="sgpb-js-variable-selector" class="sgpb-js-variable-selector form-control input-sm">
			</div>
			<div class="col-sm-5 sgpb-static-padding-top">
				<label><?php esc_html_e('Ex. #myselector or .myselector', SG_POPUP_TEXT_DOMAIN) ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="sg-hide-element sgpb-js-variable-errors sgpb-js-variable-selector-error">
					<label class="sgpb-label-error"><?php esc_html_e('This field is required', SG_POPUP_TEXT_DOMAIN) ?>.</label>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-2 sgpb-static-padding-top">
				<label for="sgpb-js-variable-attribute"><?php esc_html_e('Attribute', SG_POPUP_TEXT_DOMAIN)?>:</label>
			</div>
			<div class="col-sm-5">
				<input type="text" id="sgpb-js-variable-attribute" class="sgpb-js-variable-attribute form-control input-sm">
			</div>
			<div class="col-sm-5 sgpb-static-padding-top">
				<label><?php esc_html_e('Ex. value or data-name', SG_POPUP_TEXT_DOMAIN) ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="sg-hide-element sgpb-js-variable-errors sgpb-js-variable-attribute-error">
					<label class="sgpb-label-error"><?php esc_html_e('This field is required', SG_POPUP_TEXT_DOMAIN) ?>.</label>
				</div>
			</div>
		</div>
		<div class="row sgpb-static-padding-top">
			<div class="col-sm-3">
				<input type="button" class="sgpb-btn sgpb-btn-blue sgpb-insert-js-variable-to-editor sgpb-insert-popup-btns" value="<?php esc_html_e('Insert', SG_POPUP_TEXT_DOMAIN)?>">
			</div>
			<div class="col-sm-3">
				<input type="button" class="sgpb-btn sgpb-btn-gray-light sgpb-close-media-popup-js sgpb-insert-popup-btns" value="<?php esc_html_e('Cancel', SG_POPUP_TEXT_DOMAIN)?>">
			</div>
		</div>
	</div>
</div>
