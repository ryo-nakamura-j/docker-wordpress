<div class="sgpb sgpb-wrapper sgpb-media-upload sgpb-display-flex sgpb-align-item-center sgpb-padding-20">
    <div class="formItem sgpb-display-inline-flex sgpb-flex-direction-column sgpb-margin-right-50">
        <span class="formItem__title sgpb-margin-0"><?php esc_html_e('Please choose your picture');?>:</span>
        <input class="formItem__input formItem__input_sgpb-popup-overlay"
               id="js-upload-image"
               type="text" size="36" name="sgpb-image-url"
               value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-image-url')); ?>" required>
    </div>
    <div class="formItem formItem_last">
        <div id="js-upload-image-button" class="sgpb-icons icons_blue">K</div>
        <div class="sgpb-show-image-container"></div>
    </div>
</div>
