<?php
	use sgpb\AdminHelper;
	$defaultData = ConfigDataHelper::defaultData();

	$deleteData = '';
	if (get_option('sgpb-dont-delete-data')) {
		$deleteData = 'checked';
	}

	$userSavedRoles = get_option('sgpb-user-roles');
?>
<div class="sgpb sgpb-wrapper">
	<div class="sgpb-import-popups">
		<h3 class="sgpb-header-h3">
			<span><?php esc_html_e('Import popups', SG_POPUP_TEXT_DOMAIN); ?></span>
		</h3>
		<div class="sgpb-import-popups-form">
			<?php
			wp_import_upload_form('admin.php?import='.SG_POPUP_POST_TYPE.'&amp;step=1');
			?>
		</div>
	</div>
</div>
<style>
	.sgpb .sgpb-import-popups .sgpb-import-popups-form p,
	.sgpb .sgpb-import-popups .sgpb-import-popups-form input[type="file"] {
		margin: 20px 0;
	}
	.sgpb .sgpb-import-popups .sgpb-import-popups-form p.submit {
		display: none;
	}
</style>
