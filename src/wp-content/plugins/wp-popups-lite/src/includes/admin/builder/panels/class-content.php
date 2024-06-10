<?php

/**
 * Content management panel.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Content extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Content', 'wp-popups-lite' );
		$this->slug    = 'content';
		$this->icon    = 'fa-font';
		$this->order   = 5;
		$this->sidebar = true;
	}

	/**
	 * Outputs the Content panel sidebar.
	 *
	 * @since 2.0.0
	 */
	public function panel_sidebar() {

		// Sidebar contents are not valid unless we have a popup.
		if ( ! $this->popup ) {
			return;
		}
		echo '<div class="wppopups-fields-wrap">';
		echo '<div class="wppopups-panel-content-section-title">';
		esc_html_e( 'General', 'wp-popups-lite' );
		echo '</div>';

		wppopups_panel_field(
			'tinymce',
			'content',
			'popup_content',
			$this->popup_data,
			esc_html__( 'Content', 'wp-popups-lite' ),
			[
				'default' => wppopups_welcome_text(),
				'tinymce' => [
					'editor_height' => '250',
					'media_buttons' => true,
				],
			]
		);
		echo '<h3>Available shortcodes</h3>';
		?>
		<div class="inside">
		<p><strong><?php esc_html_e( 'Close Button', 'wp-popups-lite' ); ?>:</strong></p>
		<p>
			[spu-close class="" text="" align="" conversion="false"]
		</p>
		<a href="close-opts"
		   onclick="jQuery('#close-opts').slideToggle();return false;"><?php esc_html_e( 'View Close shortcode Options', 'wp-popups-lite' ); ?></a>
		<ul id="close-opts" style="display:none;">
			<li><b>class:</b> <?php esc_html_e( 'Pass a custom class to style your button', 'wp-popups-lite' ); ?></li>
			<li><b>text:</b> <?php esc_html_e( 'Button label - - Default value: Close', 'wp-popups-lite' ); ?></li>
			<li><b>conversion:</b> <?php esc_html_e( 'Conversion button?- - Default value: False', 'wp-popups-lite' ); ?></li>
			<li><b>align:</b> <?php esc_html_e( 'left|center|right - Default value: "center"', 'wp-popups-lite' ); ?> </li>
		</ul>
		<?php if ( class_exists( '\Elementor\Plugin' ) ) : ?>
			<p><strong><?php esc_html_e( 'Elementor template', 'wp-popups-lite' ); ?>:</strong></p>
			<p>
				[wpp-elementor id="" css="false"]
			</p>
		<?php endif; ?>
		<p><strong><?php esc_html_e( 'Facebook Page', 'wp-popups-lite' ); ?>:</strong></p>
		<p>
			[spu-facebook-page href="" name="" show_faces="" hide_cover="" width=""]
		</p>
		<a href="fb-opts"
		   onclick="jQuery('#fbpage-opts').slideToggle();return false;"><?php esc_html_e( 'View Facebook Page Options', 'wp-popups-lite' ); ?></a>
		<ul id="fbpage-opts" style="display:none;">
			<li><b>href:</b> <?php esc_html_e( 'Your Facebook page url', 'wp-popups-lite' ); ?></li>
			<li><b>name:</b> <?php esc_html_e( 'Your page name', 'wp-popups-lite' ); ?></li>
			<li><b>show_faces:</b> <?php esc_html_e( 'true|false - Default value: true', 'wp-popups-lite' ); ?></li>
			<li><b>hide_cover:</b> <?php esc_html_e( 'true|false - Default value: false', 'wp-popups-lite' ); ?></li>
			<li><b>width:</b> <?php esc_html_e( 'number - Default value: 500', 'wp-popups-lite' ); ?></b></li>
			<li><b>align:</b> <?php esc_html_e( 'left|center|right - Default value: "center"', 'wp-popups-lite' ); ?> </li>
		</ul>

		<p><strong><?php esc_html_e( 'Facebook Button', 'wp-popups-lite' ); ?>:</strong></p>
		<p>
			[spu-facebook href="" layout="" show_faces="" share="" action="" width=""]
		</p>
		<a href="fb-opts"
		   onclick="jQuery('#fb-opts').slideToggle();return false;"><?php esc_html_e( 'View Facebook Options', 'wp-popups-lite' ); ?></a>
		<ul id="fb-opts" style="display:none;">
			<li><b>href:</b> <?php esc_html_e( 'Your Facebook page url', 'wp-popups-lite' ); ?></li>
			<li>
				<b>layout:</b> <?php esc_html_e( 'standard, box_count, button - Default value: button_count', 'wp-popups-lite' ); ?>
			</li>
			<li><b>show_faces:</b> <?php esc_html_e( 'true - Default value: false', 'wp-popups-lite' ); ?></li>
			<li><b>share:</b> <?php esc_html_e( 'true - Default value: false', 'wp-popups-lite' ); ?></li>
			<li><b>action:</b> <?php esc_html_e( 'recommend - Default value: like', 'wp-popups-lite' ); ?></li>
			<li><b>width:</b> <?php esc_html_e( 'number - Default value:', 'wp-popups-lite' ); ?></li>
			<li><b>align:</b> <?php esc_html_e( 'left|center|right - Default value: "center"', 'wp-popups-lite' ); ?> </li>
		</ul>
		<p><strong><?php esc_html_e( 'Twitter Button', 'wp-popups-lite' ); ?>:</strong></p>
		<p>
			[spu-twitter user="" show_count="" size="" lang=""]
		</p>
		<a href="tw-opts"
		   onclick="jQuery('#tw-opts').slideToggle();return false;"><?php esc_html_e( 'View Twitter Options', 'wp-popups-lite' ); ?></a>
		<ul id="tw-opts" style="display:none;">
			<li><b>user:</b> <?php esc_html_e( 'Your Twitter user', 'wp-popups-lite' ); ?></li>
			<li><b>show_count:</b> <?php esc_html_e( 'false - Default value: true', 'wp-popups-lite' ); ?></li>
			<li><b>size:</b> <?php esc_html_e( 'large - Default value: ""', 'wp-popups-lite' ); ?></li>
			<li><b>lang:</b></li>
			<li><b>align:</b> <?php esc_html_e( 'left|center|right - Default value: "center"', 'wp-popups-lite' ); ?> </li>
		</ul>
		<?php do_action( 'wppopups_content_shortcodes', $this->popup );?>

		<h3><?php esc_html_e( 'Looking for using a form in your popup?', 'wp-popups-lite' ); ?></h3>
		<p><?php esc_html_e( 'We love so much WP Forms that we even copied part of their code into our plugin, so if you are looking for a top and easy form plugin, we can just recommend:', 'wp-popups-lite' ); ?></p>
		<p style="text-align: center">
			<a target="_blank" href="https://timersys.com/recommends/wpforms"><img
						src="<?php echo WPPOPUPS_PLUGIN_URL . 'assets/images/wpforms-250x250.png'; ?>" border="0"
						alt="WPForms"/></a>
		</p>
		</div><?php
		echo '</div>';
		do_action( 'wppopups_content_panel_sidebar', $this->popup );
	}
}

new WPPopups_Builder_Panel_Content();
