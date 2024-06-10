<?php
use sgpb\AdminHelper;

$excludePostId = !empty($_GET['post']) ? (int)sanitize_text_field($_GET['post']) : 0;
$excludedPopups = array($excludePostId);
$allPopups = AdminHelper::getPopupsIdAndTitle($excludedPopups);
?>
<div class="sgpb-hide">
<div id="sgpb-custom-button-wrapper">
    <?php
    $defaultData = ConfigDataHelper::defaultData();
    $buttonDefaultStyles = array(
        'title' => __('Button',SG_POPUP_TEXT_DOMAIN),
        'width' => '300px',
        'height' => '60px',
        'borderWidth' => '0px',
        'borderRadius' => '2px',
        'borderColor' => '#1a48a5',
        'backgroundColor' => '#1d6ab7',
        'backgroundHoverColor' => '#2184d1',
        'textColor' => '#FFFFFF'
    );
    ?>
    <div class="sgpb-wrapper">
        <img class="sgpb-add-subscriber-popup-close-btn sgpb-custom-button-close-popup sgpb-close-media-popup-js" src="<?php echo esc_url_raw(SG_POPUP_IMG_URL.'subscribers_close.png'); ?>" width='15px'>
        <div class="row form-group">
            <label for="sgpb-custom-btn-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
                <?php esc_html_e('Title', SG_POPUP_TEXT_DOMAIN); ?>:
            </label>
            <div class="col-md-6">
                <input class="form-control sgpb-full-width-events" data-contact-rel="js-contact-submit-btn" data-field-type="button" data-style-type="title" type="text" name="sgpb-custom-btn-title" id="sgpb-custom-btn-title" value="<?php echo esc_attr($buttonDefaultStyles['title']); ?>">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6">
                <div class="row form-group">
                    <label class="col-md-12 control-label">
                        <?php esc_html_e('Custom Button Styles', SG_POPUP_TEXT_DOMAIN); ?>
                    </label>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-btn-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
                        <?php esc_html_e('Width', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <input class="form-control js-contact-dimension sgpb-full-width-events sgpb-custom-button-settings" data-contact-rel="js-contact-submit-btn" data-field-type="button" data-style-type="width" type="text" name="sgpb-custom-btn-width" id="sgpb-custom-btn-width" value="<?php echo esc_attr($buttonDefaultStyles['width']); ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-btn-height" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
                        <?php esc_html_e('Height', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <input class="form-control js-contact-dimension sgpb-full-width-events sgpb-custom-button-settings" data-contact-rel="js-contact-submit-btn" data-field-type="button" data-style-type="height" type="text" name="sgpb-custom-btn-height" id="sgpb-custom-btn-height" value="<?php echo esc_attr($buttonDefaultStyles['height']); ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-btn-border-width" class="col-md-6 control-label sgpb-static-padding-top sgpb-sub-option">
                        <?php esc_html_e('Border width', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <input class="form-control js-contact-dimension sgpb-full-width-events sgpb-custom-button-settings" data-field-type="button" data-contact-rel="js-contact-submit-btn" data-style-type="border-width" type="text" name="sgpb-custom-btn-border-width" id="sgpb-custom-btn-border-width" value="<?php echo esc_attr($buttonDefaultStyles['borderWidth']); ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-btn-border-radius" class="col-md-6 control-label sgpb-sub-option">
                        <?php esc_html_e('Border radius', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <input class="form-control js-contact-dimension sgpb-full-width-events sgpb-custom-button-settings" data-contact-rel="js-contact-submit-btn" data-field-type="button" data-style-type="border-radius" type="text" name="sgpb-custom-btn-border-radius" id="sgpb-custom-btn-border-radius" value="<?php echo esc_attr($buttonDefaultStyles['borderRadius']); ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-btn-border-color" class="col-md-6 control-label sgpb-sub-option">
                        <?php esc_html_e('Border color', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <div class="sgpb-color-picker-wrapper">
                            <input id="sgpb-custom-btn-border-color" class="sgpb-custom-button-color-picker sgpb-custom-button-settings" data-field-type="button" data-contact-rel="js-contact-submit-btn" data-style-type="border-color" type="text" name="sgpb-custom-btn-border-color" value="<?php echo esc_attr($buttonDefaultStyles['borderColor']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-6 control-label sgpb-sub-option">
                        <?php esc_html_e('Background color', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <div class="sgpb-color-picker-wrapper">
                            <input class="sgpb-custom-button-color-picker sgpb-custom-button-settings" data-field-type="button" data-contact-rel="js-contact-submit-btn" data-style-type="background-color" type="text" name="sgpb-custom-btn-bg-color" value="<?php echo esc_attr($buttonDefaultStyles['backgroundColor']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-6 control-label sgpb-sub-option">
                        <?php esc_html_e('Hover background color', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <div class="sgpb-color-picker-wrapper">
                            <input class="sgpb-custom-button-color-picker sgpb-custom-button-settings" data-field-type="button" data-contact-rel="js-contact-submit-btn" data-style-type="hover-color" type="text" name="sgpb-custom-btn-bg-color" value="<?php echo esc_attr($buttonDefaultStyles['backgroundHoverColor']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-6 control-label sgpb-sub-option">
                        <?php esc_html_e('Text color', SG_POPUP_TEXT_DOMAIN); ?>:
                    </label>
                    <div class="col-md-6">
                        <div class="sgpb-color-picker-wrapper">
                            <input class="sgpb-custom-button-color-picker sgpb-custom-button-settings" data-field-type="button" data-contact-rel="js-contact-submit-btn" data-style-type="color" type="text" name="sgpb-custom-btn-text-color" value="<?php echo esc_attr($buttonDefaultStyles['textColor']); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row form-group">
                    <label class="col-md-12 control-label">
                        <?php esc_html_e('Button events', SG_POPUP_TEXT_DOMAIN); ?>
                    </label>
                </div>
            <?php
                $multipleChoiceButton = new \sgpb\MultipleChoiceButton($defaultData['htmlCustomButtonArgs'], 'hidePopup');
                echo wp_kses($multipleChoiceButton, AdminHelper::allowed_html_tags());
            ?>
            <div class="sg-hide sg-full-width" id="sgpb-custom-button-copy">
                <div class="row form-group">
                    <label for="sgpb-custom-button-copy-to-clipboard-text" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Text', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6"><input name="sgpb-custom-button-copy-to-clipboard-text" id="sgpb-custom-button-copy-to-clipboard-text" class="form-control sgpb-full-width-events" value="some text here"></div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-button-copy-to-clipboard-close-popup" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Close popup', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6"><input type="checkbox" name="sgpb-custom-button-copy-to-clipboard-close-popup" id="sgpb-custom-button-copy-to-clipboard-close-popup"></div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-button-copy-to-clipboard-alert" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Show alert', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6">
                        <input type="checkbox" id="sgpb-custom-button-copy-to-clipboard-alert" class="js-checkbox-accordion" name="sgpb-custom-button-copy-to-clipboard-alert">
                    </div>
                </div>
                <div class="sg-full-width form-group">
                    <div class="row">
                        <label for="col-md-5 sgpb-copy-to-clipboard-message" class="col-md-6 control-label sgpb-double-sub-option">
                            <?php esc_html_e('Message', SG_POPUP_TEXT_DOMAIN)?>:
                        </label>
                        <div class="col-md-6">
                            <input type="text" id="sgpb-custom-button-copy-to-clipboard-message" class="form-control sgpb-full-width-events " name="sgpb-custom-button-copy-to-clipboard-message" value="Copied to clipboard!">
                        </div>
                    </div>
                </div>
            </div>
            <div class="sg-hide sg-full-width" id="sgpb-custom-button-redirect-to-URL">
                <div class="row form-group">
                    <label for="sgpb-custom-button-redirect-URL" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Redirect URL', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6"><input type="url" name="sgpb-custom-button-redirect-URL" id="sgpb-custom-button-redirect-URL" placeholder="https://www.example.com" class="form-control sgpb-full-width-events" value=""></div>
                </div>
                <div class="row form-group">
                    <label for="sgpb-custom-button-redirect-new-tab" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Redirect to new tab', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6"><input type="checkbox" name="sgpb-custom-button-redirect-new-tab" id="sgpb-custom-button-redirect-new-tab" placeholder="https://www.example.com" ></div>
                </div>
            </div>
            <div class="sg-hide sg-full-width" id="sgpb-custom-button-open-popup">
                <div class="row form-group">
                    <label for="sgpb-subs-success-redirect-URL" class="col-md-6 control-label sgpb-double-sub-option">
                        <?php esc_html_e('Select popup', SG_POPUP_TEXT_DOMAIN)?>:
                    </label>
                    <div class="col-md-6">
                        <?php echo wp_kses(sgpb\AdminHelper::createSelectBox($allPopups, '', array('name' => 'sgpb-custom-button-popup', 'class'=>'js-sg-select2 sgpb-full-width-events sgpb-custom-button-popup')), AdminHelper::allowed_html_tags()); ?>
                    </div>
                </div>
            </div>
            <div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    <input type="button" value="<?php esc_html_e('Insert', SG_POPUP_TEXT_DOMAIN); ?>" class="btn btn-sm btn-success sgpb-subscriber-popup-btns sgpb-insert-custom-button-to-editor sgpb-close-media-popup-js" data-ajaxnonce="popupBuilderAjaxNonce">
                </div>
                <div class="col-md-6">
                    <input type="button" value="<?php esc_html_e('Cancel', SG_POPUP_TEXT_DOMAIN); ?>" class="btn btn-sm btn-default sgpb-add-subscriber-popup-close-btn-js sgpb-subscriber-popup-btns sgpb-subscriber-popup-close-btn-js sgpb-close-media-popup-js" data-ajaxnonce="popupBuilderAjaxNonce">
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
</div>
<style type="text/css">
    .sgpb-popup-dialog-main-div-theme-wrapper-1 {
        top: 86px !important;
        z-index: 999999 !important;
    }
</style>
