<?php

/**
 * Blank form template.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Template_Blank extends WPPopups_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'Blank Popup', 'wp-popups-lite' );
		$this->slug        = 'blank';
		$this->description = esc_html__( 'The blank popup allows you to create any style you want from scratch.', 'wp-popups-lite' );
		$this->includes    = '';
		$this->icon        = '';
		$this->modal       = '';
		$this->core        = true;
		$this->data        = [
			'colors'    => [
				'overlay_color' => 'rgba(0,0,0,0.5)',
				'bg_color'      => 'rgb(255, 255, 255)',
			],
		];
	}
}

new WPPopups_Template_Blank();
