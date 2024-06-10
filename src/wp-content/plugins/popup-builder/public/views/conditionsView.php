<?php
use sgpb\AdminHelper;
$defaultData = ConfigDataHelper::defaultData();
$defaultConditionsAdvancedTargeting = $defaultData['freeConditionsAdvancedTargeting'];
$defaultConditionsGeoTargeting = $defaultData['freeConditionsGeoTargeting'];
?>

<div class="sgpb-wrapper">
	<div class="formItem sgpb-pro-conditions-main-wrapper sgpb-display-flex">
		<div class="sgpb-pro-conditions-box sgpb-margin-right-20">
			<img class="sgpb-pro-conditions-extension-icon sgpb-margin-y-20" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/geo-targeting.svg" width="50" height="50">
			<span class="sgpb-pro-conditions-title">
				<?php esc_html_e('Geo Targeting', SG_POPUP_TEXT_DOMAIN);?>
			</span>
			<span class="sgpb-pro-conditions-text">
				<?php esc_html_e('If you want to unlock Geo Targeting you need to activate this extension', SG_POPUP_TEXT_DOMAIN);?>.
			</span>
			<a href="<?php echo esc_url_raw(SG_POPUP_GEO_TARGETING_URL); ?>" class="sgpb-btn sgpb-btn-blue sgpb-btn--rounded sgpb-margin-y-30">
				<?php esc_html_e('Buy Now', SG_POPUP_TEXT_DOMAIN);?>
			</a>
			<div class="sgpb-pro-conditions-inline-border sgpb-margin-bottom-20"></div>
			<a class="sgpb-pro-conditions-pro-url sgpb-pro-conditions-pro-url-show-js" href="javascript:void(0)">
				<?php esc_html_e('More Details', SG_POPUP_TEXT_DOMAIN);?>
				<img class="sgpb-pro-conditions-extension-icon" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/arrow-down.png" width="20" height="20">
			</a>
			<div class="sgpb-pro-conditions-list" style="display: none;">
				<?php foreach ($defaultConditionsGeoTargeting as $conditionIndex => $conditionName) : ?>
					<span class="formItem__direction sgpb-pro-conditions-list-item"><?php echo esc_html($conditionName); ?></span>
					<div class="sgpb-pro-conditions-inline-border sgpb-margin-y-10"></div>
				<?php endforeach; ?>
			</div>
			<a class="sgpb-pro-conditions-pro-url sgpb-pro-conditions-pro-url-hide-js sgpb-margin-top-20" href="javascript:void(0)" style="display: none;">
				<?php esc_html_e('See Less', SG_POPUP_TEXT_DOMAIN);?>
				<img class="sgpb-pro-conditions-extension-icon" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/arrow-up.png" width="20" height="20">
			</a>
		</div>
		<div class="sgpb-pro-conditions-box">
			<img class="sgpb-pro-conditions-extension-icon sgpb-margin-y-20" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/sgpbAdvancedTargeting.svg" width="50" height="50">
			<span class="sgpb-pro-conditions-title">
				<?php esc_html_e('Advanced Targeting', SG_POPUP_TEXT_DOMAIN);?>
			</span>
			<span class="sgpb-pro-conditions-text ">
				<?php esc_html_e('If you want to unlock Advanced Targeting you need to activate this extension', SG_POPUP_TEXT_DOMAIN);?>.
			</span>
			<a href="<?php echo esc_url_raw(SG_POPUP_ADVANCED_TARGETING_URL); ?>" class="sgpb-btn sgpb-btn-blue sgpb-btn--rounded sgpb-margin-y-30">
				<?php esc_html_e('Buy Now', SG_POPUP_TEXT_DOMAIN);?>
			</a>
			<div class="sgpb-pro-conditions-inline-border sgpb-margin-bottom-20"></div>
			<a class="sgpb-pro-conditions-pro-url sgpb-pro-conditions-pro-url-show-js" href="javascript:void(0)">
				<?php esc_html_e('More Details', SG_POPUP_TEXT_DOMAIN);?>
				<img class="sgpb-pro-conditions-extension-icon" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/arrow-down.png" width="20" height="20">
			</a>
			<div class="sgpb-pro-conditions-list" style="display: none;">
				<?php foreach ($defaultConditionsAdvancedTargeting as $conditionIndex => $conditionName) : ?>
					<span class="formItem__direction sgpb-pro-conditions-list-item"><?php echo esc_html($conditionName); ?></span>
					<div class="sgpb-pro-conditions-inline-border sgpb-box-conditions-separator sgpb-margin-y-10"></div>
				<?php endforeach; ?>
			</div>
			<a class="sgpb-pro-conditions-pro-url sgpb-pro-conditions-pro-url-hide-js sgpb-margin-top-20" href="javascript:void(0)" style="display: none;">
				<?php esc_html_e('See Less', SG_POPUP_TEXT_DOMAIN);?>
				<img class="sgpb-pro-conditions-extension-icon" src="<?php echo esc_url_raw(SG_POPUP_PUBLIC_URL); ?>icons/arrow-up.png" width="20" height="20">
			</a>
		</div>
	</div>
<script type="text/javascript">
	jQuery('.sgpb-pro-conditions-pro-url-show-js').click(function() {
		jQuery(this).next('.sgpb-pro-conditions-list').show();
		jQuery(this).hide();
		jQuery(this).parent().find('.sgpb-pro-conditions-pro-url-hide-js').show();
	});
	jQuery('.sgpb-pro-conditions-pro-url-hide-js').click(function() {
		jQuery(this).prev('.sgpb-pro-conditions-list').hide();
		jQuery(this).hide();
		jQuery(this).parent().find('.sgpb-pro-conditions-pro-url-show-js').show();
	});
</script>
</div>
