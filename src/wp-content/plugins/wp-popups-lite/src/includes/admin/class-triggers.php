<?php

/**
 * Triggers class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Triggers {


	/**
	 * @param string $trigger
	 *
	 * @return string
	 */
	public static function field_type( $trigger = 'seconds' ) {

		switch ( $trigger ) {
			case 'seconds':
			case 'percentage':
			case 'pixels':
				$type = 'number';
				break;
			case 'class':
				$type = 'text';
				break;
			default:
				$type = '';
				break;
		}

		return apply_filters( 'wppopups/triggers/field_type', $type, $trigger );
	}

	/**
	 * Triggers main options
	 * @return array
	 */
	public static function options() {
		return apply_filters( 'wppopups/triggers/options', [
			'seconds'    => esc_html__( 'Seconds after page load', 'wp-popups-lite' ),
			'percentage' => '% ' . esc_html__( 'of page height', 'wp-popups-lite' ),
			'pixels'     => esc_html__( 'Scrolled down pixels', 'wp-popups-lite' ),
			'class'      => esc_html__( 'Class Triggering', 'wp-popups-lite' ),
		] );
	}

	/**
	 * Empty triggers
	 * @return array
	 */
	public static function defaults() {
		return [
			'trigger_0' => [
				'trigger' => 'seconds',
			],
		];
	}
}
