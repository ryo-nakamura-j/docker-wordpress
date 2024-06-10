<?php
use sgpb\AdminHelper;
?>
<div class="sgpb sgpb-header">
    <h1 class="sgpb-header-h1 sgpb-margin-bottom-30"><?php esc_html_e( 'Popups', SG_POPUP_TEXT_DOMAIN ) ?></h1>
    <div class="sgpb-margin-bottom-50 sgpb-display-flex sgpb-justify-content-between">
        <div>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30" href="<?php echo esc_url_raw(AdminHelper::getPopupTypesPageURL()); ?>">
		        <?php esc_html_e( 'Add New', SG_POPUP_TEXT_DOMAIN ); ?>
            </a>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30" href="<?php echo esc_url_raw(AdminHelper::getPopupExportURL()); ?>">
		        <?php esc_html_e( 'Export', SG_POPUP_TEXT_DOMAIN ); ?>
            </a>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30"
               id="sgpbImportSettings"
               href="javascript:void(0)">
		        <?php esc_html_e( 'Import', 'easy-digital-downloads' ); ?>
            </a>
        </div>
        <div style="text-align: right" id="sgpbPostSearch">
            <div class="sgpb--group">
                <input type="text" id="sgpbSearchInPosts" placeholder="Search Popup" class="sgpb-input">
                <input type="submit" value="GO!" id="sgpbSearchInPostsSubmit" class="sgpb-btn sgpb-btn-blue">
            </div>
        </div>
    </div>
</div>
<style>
    #wpbody-content > div.wrap > h1,
    .notice,
    #wpbody-content > div.wrap > a {
        display: none !important;
    }
</style>
