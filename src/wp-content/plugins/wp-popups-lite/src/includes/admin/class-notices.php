<?php

/**
 * Admin notices, on the fly.
 *
 * @example
 * WPPopups_Admin_Notice::success( 'All is good!' );
 *
 * @example
 * WPPopups_Admin_Notice::warning( 'Do something please.' );
 *
 * @todo       Persistent, dismissible notices.
 * @link       https://gist.github.com/monkeymonk/2ea17e2260daaecd0049c46c8d6c85fd
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017, WP Popups LLC
 */
class WPPopups_Admin_Notice {

	/**
	 * Single instance holder.
	 *
	 * @since 2.0.0
	 * @var mixed
	 */
	private static $_instance = null;

	/**
	 * Added notices.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $notices = [];

	/**
	 * Get the instance.
	 *
	 * @return WPPopups_Admin_Notice
	 * @since 2.0.0
	 */
	public static function getInstance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new WPPopups_Admin_Notice();
		}

		return self::$_instance;
	}

	/**
	 * Hook when called.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', [ &$this, 'display' ] );
	}

	/**
	 * Display the notices.
	 *
	 * @since 2.0.0
	 */
	public function display() {

		if ( ! wppopups_current_user_can() ) {
			return;
		}

		echo implode( ' ', $this->notices );
	}

	/**
	 * Add notice to instance property.
	 *
	 * @param string $message Message to display.
	 * @param string $type Type of the notice (default: '').
	 *
	 * @since 2.0.0
	 *
	 */
	public static function add( $message, $type = '' ) {

		$instance = self::getInstance();
		$id       = 'wppopups-notice-' . ( count( $instance->notices ) + 1 );
		$type     = ! empty( $type ) ? 'notice-' . $type : '';
		$notice   = sprintf( '<div class="notice wppopups-notice %s" id="%s">%s</div>', $type, $id, wpautop( $message ) );

		$instance->notices[] = $notice;
	}

	/**
	 * Add Info notice.
	 *
	 * @param string $message Message to display.
	 *
	 * @since 2.0.0
	 *
	 */
	public static function info( $message ) {
		self::add( $message, 'info' );
	}

	/**
	 * Add Error notice.
	 *
	 * @param string $message Message to display.
	 *
	 * @since 2.0.0
	 *
	 */
	public static function error( $message ) {
		self::add( $message, 'error' );
	}

	/**
	 * Add Success notice.
	 *
	 * @param string $message Message to display.
	 *
	 * @since 2.0.0
	 *
	 */
	public static function success( $message ) {
		self::add( $message, 'success' );
	}

	/**
	 * Add Warning notice.
	 *
	 * @param string $message Message to display.
	 *
	 * @since 2.0.0
	 *
	 */
	public static function warning( $message ) {
		self::add( $message, 'warning' );
	}
}
