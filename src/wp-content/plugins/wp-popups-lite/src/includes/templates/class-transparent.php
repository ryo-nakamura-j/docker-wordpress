<?php

/**
 * Contact form template.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Template_transparent extends WPPopups_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->name = esc_html__( 'Transparent Popup', 'wp-popups-lite' );
		$this->slug        = 'transparent';
		$this->description = esc_html__( 'No background and no overlay with transparent border image example popup', 'wp-popups-lite' );
		$this->includes    = '';
		$this->icon        = '';
		$this->modal       = '';
		$this->core        = true;
		$this->data        = [
			'content'   => [
				'popup_content' => '<h3 style="text-align: center;">WP Popups</h3>
<p style="text-align: center;">Add you text in here</p>',
			],
			'position'  => [
				'position' => 'centered',
			],
			'animation' => [
				'animation' => 'fade',
			],
			'popup_box' => [
				'width'       => '750px',
				'padding'     => '150',
				'auto_height' => '1',
				'height'      => '430px',
			],
			'colors'    => [
				'overlay_color' => 'rgba(0, 0, 0, 0)',
				'bg_color'      => 'rgba(0, 0, 0, 0)',
				'bg_img'        => WPPOPUPS_PLUGIN_URL . 'assets/images/transparent-border.png',
				'bg_img_repeat' => 'no-repeat',
				'bg_img_size'   => 'contains',
			],
			'border'    => [
				'border_type'   => 'none',
				'border_color'  => 'rgb(0, 0, 0)',
				'border_width'  => '10',
				'border_radius' => '0',
				'border_margin' => '14',
			],
			'shadow'    => [
				'shadow_color'    => '#ccc',
				'shadow_type'     => 'outset',
				'shadow_x_offset' => '0',
				'shadow_y_offset' => '0',
				'shadow_blur'     => '0',
				'shadow_spread'   => '0',
			],
			'close'     => [
				'close_color'        => '#666',
				'close_hover_color'  => '#000',
				'close_shadow_color' => '#000',
				'close_size'         => '30',
				'close_position'     => 'top_right',
			],
			'css'       => [
				'custom_css' => '',
			],
			'settings'    => [
				'popup_hidden_class'  => 'spu-theme-transparent'
			]
		];
	}
}

new WPPopups_Template_transparent;
