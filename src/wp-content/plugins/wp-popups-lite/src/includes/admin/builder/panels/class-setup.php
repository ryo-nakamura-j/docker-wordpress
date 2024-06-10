<?php

/**
 * Setup panel.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Setup extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name               = esc_html__( 'Setup', 'wp-popups-lite' );
		$this->slug               = 'setup';
		$this->icon               = 'fa-cog';
		$this->need_popup_created = false;
		$this->order              = 5;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {

		$detect = new Mobile_Detect;

		if( $detect->isMobile() ) {
			$admin_builder = 'admin-builder-setup-mobile.css';
		} else {
			$admin_builder = 'admin-builder-setup.css';
		}

		// CSS.
		wp_enqueue_style(
			'wppopups-builder-setup',
			WPPOPUPS_PLUGIN_URL . 'assets/css/'.$admin_builder,
			null,
			WPPOPUPS_VERSION
		);
	}

	/**
	 * Outputs the Settings panel primary content.
	 *
	 * @since 2.0.0
	 */
	public function panel_content() {

		$core_templates       = apply_filters( 'wppopups_popup_templates_core', [] );
		$additional_templates = apply_filters( 'wppopups_popup_templates', [] );
		$additional_count     = count( $additional_templates );
		?>
		<div id="wppopups-setup-popup-name">
			<span><?php esc_html_e( 'Popup Name', 'wp-popups-lite' ); ?></span>
			<input type="text" id="wppopups-setup-name"
			       placeholder="<?php esc_attr_e( 'Enter your popup name here&hellip;', 'wp-popups-lite' ); ?>">
		</div>

		<div class="wppopups-setup-title core">
			<?php esc_html_e( 'Select a Template', 'wp-popups-lite' ); ?>
		</div>

		<p class="wppopups-setup-desc core">
			<?php
			echo wp_kses(
				__( 'To speed up the process, you can select from one of our pre-made templates or start with a <strong><a href="#" class="wppopups-trigger-blank">blank popup.</a></strong>', 'wp-popups-lite' ),
				[
					'strong' => [],
					'a'      => [
						'href'  => [],
						'class' => [],
					],
				]
			);
			?>
		</p>

		<?php $this->template_select_options( $core_templates, 'core' ); ?>

		<div class="wppopups-setup-title additional">
			<?php esc_html_e( 'Additional Templates', 'wp-popups-lite' ); ?>
			<?php echo ! empty( $additional_count ) ? '<span class="count">(' . $additional_count . ')</span>' : ''; ?>
		</div>

		<p class="wppopups-setup-desc additional">
			<?php
			printf(
				wp_kses(
				/* translators: %1$s - WPPopups.com URL to a template suggestion, %2$s - WPPopups.com URL to a doc about custom templates. */
					__( 'Have a suggestion for a new template? <a href="%1$s" target="_blank" rel="noopener noreferrer">We\'d love to hear it</a>. Also, you can <a href="%2$s" target="_blank" rel="noopener noreferrer">create your own templates</a>!', 'wp-popups-lite' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				'https://wppopups.com/popup-template-suggestion/',
				'https://wppopups.com/docs/how-to-create-a-custom-popup-template/'
			);
			?>
		</p>

		<?php if ( ! empty( $additional_count ) ) : ?>

			<div class="wppopups-setup-template-search-wrap">
				<i class="fa fa-search" aria-hidden="true"></i>
				<input type="text" id="wppopups-setup-template-search" value=""
				       placeholder="<?php esc_attr_e( 'Search additional templates...', 'wp-popups-lite' ); ?>">
			</div>

			<?php $this->template_select_options( $additional_templates, 'additional' ); ?>

		<?php else : ?>


		<?php
		endif;
		do_action( 'wppopups_setup_panel_after' );
	}

	/**
	 * Generate a block of templates to choose from.
	 *
	 * @param array $templates
	 * @param string $slug
	 *
	 * @since 2.0.0
	 *
	 */
	public function template_select_options( $templates, $slug ) {

		if ( ! empty( $templates ) ) {

			echo '<div id="wppopups-setup-templates-' . $slug . '" class="wppopups-setup-templates ' . $slug . ' wppopups-clear">';

			echo '<div class="list">';

			// Loop through each available template.
			foreach ( $templates as $template ) {

				$selected = ! empty( $this->popup_data['meta']['template'] ) && $this->popup_data['meta']['template'] === $template['slug'] ? true : false;
				?>
				<div class="wppopups-template <?php echo $selected ? 'selected' : ''; ?>"
				     id="wppopups-template-<?php echo sanitize_html_class( $template['slug'] ); ?>">

					<div class="wppopups-template-inner">

						<div class="wppopups-template-name wppopups-clear">
							<?php echo esc_html( $template['name'] ); ?>
							<?php echo $selected ? '<span class="selected">' . esc_html__( 'Selected', 'wp-popups-lite' ) . '</span>' : ''; ?>
						</div>

						<?php if ( ! empty( $template['description'] ) ) : ?>
							<div class="wppopups-template-details">
								<p class="desc"><?php echo esc_html( $template['description'] ); ?></p>
							</div>
						<?php endif; ?>

						<?php
						$template_name = sprintf(
						/* translators: %s - Popup template name. */
							esc_html__( '%s template', 'wp-popups-lite' ),
							$template['name']
						);
						?>

						<div class="wppopups-template-overlay">
							<a href="#" class="wppopups-template-select"
							   data-template-name-raw="<?php echo esc_attr( $template['name'] ); ?>"
							   data-template-name="<?php echo esc_attr( $template_name ); ?>"
							   data-template="<?php echo esc_attr( $template['slug'] ); ?>">
								<?php
								printf(
								/* translators: %s - Popup template name. */
									esc_html__( 'Create a %s', 'wp-popups-lite' ),
									$template['name']
								);
								?>
							</a>
						</div>

					</div>

				</div>
				<?php
			}

			echo '</div>';

			echo '</div>';
		}
	}
}

new WPPopups_Builder_Panel_Setup();
