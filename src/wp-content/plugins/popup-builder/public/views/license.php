<div class="sgpb sgpb-wrapper sgpb-padding-30">
	<span class="sgpb-header-h1">License</span>
	<div class="formItem">
		<?php
		$licenses     = $this->getLicenses();
		$licenseCount = 0;
		foreach ( $licenses as $currentLicense ) : ?>
			<?php
			$key     = $currentLicense['key'];
			$license = get_option( 'sgpb-license-key-' . $key );
			$status  = get_option( 'sgpb-license-status-' . $key );
			?>
				<div class="sgpb-license sgpb-license-block sgpb-padding-20 sgpb-margin-bottom-30 sgpb-position-relative">
					<div class="sgpb-license-border <?= ( $status !== false && $status == 'valid' ) ? 'active' : 'inactive' ?>"></div>
					<h2 class="formItem__title sgpb-margin-top-10 sgpb-margin-bottom-30"><?php echo esc_html($currentLicense['boxLabel']) ?></h2>
					<div class="sgpb-license__status">
		                <?php if ( $status !== false && $status == 'valid' ): ?>
				            <div class="active"></div>
				            Active
			            <?php else: ?>
				            <div class="inactive"></div>
			                Inactive
		                <?php endif; ?>
	                </div>
					<div class="licenseKey__form sgpb-margin-top-10 sgpb-margin-bottom-30 sgpb-align-item-center sgpb-display-flex">
						<span class="sgpb-width-20"><?php esc_html_e( 'License Key', SG_POPUP_TEXT_DOMAIN ); ?></span>
						<form method="post" action="options.php" class="sgpb-width-80 sgpb-display-inline-flex">
							<input id="<?php echo esc_attr('sgpb-license-key-' . $key) ?>" type="text" class="sgpb-width-100"
							       value="<?php esc_attr_e( $license ); ?>"
							       name="<?php echo esc_attr('sgpb-license-key-' .  $key ) ?>">
							<?php if ( $status !== false && $status == 'valid' ): ?>
								<?php wp_nonce_field( 'sgpb_nonce', 'sgpb_nonce' ); ?>
								<input type="submit" class="sgpb-btn sgpb-btn-blue"
								       name="<?php echo esc_attr('sgpb-license-deactivate' .  $key ); ?>"
								       value="<?php esc_html_e( 'Deactivate', SG_POPUP_TEXT_DOMAIN ); ?>">
							<?php else: ?>
								<?php wp_nonce_field( 'sgpb_nonce', 'sgpb_nonce' ); ?>
								<input type="submit" class="sgpb-btn sgpb-btn-blue"
								       name="<?php echo esc_attr('sgpb-license-activate-' .  $key ); ?>"
								       value="<?php esc_html_e( 'Activate', SG_POPUP_TEXT_DOMAIN ); ?>">
							<?php endif; ?>
						</form>
					</div>
				</div>
		<?php endforeach; ?>
	</div>
</div>
