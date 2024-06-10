<?php

/**
 * Elegant template.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Template_elegant extends WPPopups_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {


		$this->name        = esc_html__( 'Elegant Popup', 'wp-popups-lite' );
		$this->slug        = 'elegant';
		$this->description = esc_html__( 'Soft background with inner border. Classy and elegant', 'wp-popups-lite' );
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
				'padding'     => '50',
				'auto_height' => '1',
				'height'      => '430px',
			],
			'colors'    => [
				'overlay_color' => 'rgba(205, 205, 205, 0.33)',
				'bg_color'      => 'rgb(141, 177, 172)',
				'bg_img'        => '',
				'bg_img_repeat' => 'no-repeat',
				'bg_img_size'   => 'cover',
			],
			'border'    => [
				'border_type'   => 'solid',
				'border_color'  => 'rgb(6, 94, 79)',
				'border_width'  => '5',
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
				'close_color'        => 'rgb(255, 255, 255)',
				'close_hover_color'  => '#000',
				'close_shadow_color' => '#000',
				'close_size'         => '40',
				'close_position'     => 'top_right',
			],
			'css'       => [
				'custom_css' => '',
			],
			'settings'    => [
				'popup_hidden_class'  => 'spu-theme-elegant'
			]
		];
	}
}

new WPPopups_Template_elegant;
