<?php

/**
 * Base panel class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
abstract class WPPopups_Builder_Panel {

	/**
	 * Full name of the panel.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $name;

	/**
	 * Slug.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $slug;

	/**
	 * Font Awesome Icon used for the editor button, eg "fa-list".
	 *
	 * @since 2.0.0
	 * @var mixed
	 */
	public $icon = false;

	/**
	 * Priority order the field button should show inside the "Add Fields" tab.
	 *
	 * @since 2.0.0
	 * @var integer
	 */
	public $order = 50;

	/**
	 * If panel contains a sidebar element or is full width.
	 *
	 * @since 2.0.0
	 * @var boolean
	 */
	public $sidebar = false;

	/**
	 * Contains popup object if we have one.
	 *
	 * @since 2.0.0
	 * @var object
	 */
	public $popup;

	/**
	 * Contains array of the popup data (post_content).
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $popup_data;

	/**
	 * We set if the panel needs to have a popup created before hand.
	 *
	 * @var boolean
	 */
	public $need_popup_created = true;

	/**
	 * Variable to hide panel from view
	 *
	 * @var boolean
	 */
	public $display_panel = true;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Load popup if found.
		$popup_id         = isset( $_GET['popup_id'] ) ? absint( $_GET['popup_id'] ) : false;
		$this->popup      = wppopups()->popups->get( $popup_id );
		$this->popup_data = $this->popup ? $this->popup->data : false;

		// Bootstrap.
		$this->init();

		do_action( 'wppopups_builder_init', $this );

		if( $this->display_panel ) {

			// Load panel specific enqueues.
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ], 15 );

			// Primary panel button.
			add_action( 'wppopups_builder_panel_buttons', [ $this, 'button' ], $this->order, 2 );

			// Output.
			add_action( 'wppopups_builder_panel', [ $this, 'panel_output' ], $this->order, 2 );
		}
	}

	/**
	 * All systems go. Used by children.
	 *
	 * @since 2.0.0
	 */
	public function init() {
	}

	/**
	 * Enqueue assets for the builder. Used by children.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {
	}

	/**
	 * Primary panel button in the left panel navigation.
	 *
	 * @param mixed $popup
	 * @param string $view
	 *
	 * @since 2.0.0
	 *
	 */
	public function button( $popup, $view ) {

		$active = $view === $this->slug ? 'active' : '';
		?>

		<button class="wppopups-panel-<?php echo esc_attr( $this->slug ); ?>-button <?php echo $active; ?>"
		        data-panel="<?php echo esc_attr( $this->slug ); ?>">
			<i class="fa <?php echo esc_attr( $this->icon ); ?>"></i>
			<span><?php echo esc_html( $this->name ); ?></span>
		</button>

		<?php
	}

	/**
	 * Outputs the contents of the panel.
	 *
	 * @param object $popup
	 * @param string $view
	 *
	 * @since 2.0.0
	 *
	 */
	public function panel_output( $popup, $view ) {

		$active = $view === $this->slug ? 'active' : '';
		$wrap   = $this->sidebar ? 'wppopups-panel-sidebar-content' : 'wppopups-panel-full-content';

		printf( '<div class="wppopups-panel %s" id="wppopups-panel-%s">', $active, esc_attr( $this->slug ) );

		printf( '<div class="wppopups-panel-name">%s</div>', $this->name );

		printf( '<div class="%s">', $wrap );

		if ( true === $this->sidebar ) {

			echo '<div class="wppopups-panel-sidebar">';

			do_action( 'wppopups_builder_before_panel_sidebar', $this->popup, $this->slug );

			$this->panel_sidebar();

			do_action( 'wppopups_builder_after_panel_sidebar', $this->popup, $this->slug );

			echo '</div>';

		}

		echo '<div class="wppopups-panel-content-wrap">';

		echo '<div class="wppopups-panel-content">';

		do_action( 'wppopups_builder_before_panel_content', $this->popup, $this->slug );

		$this->panel_content();

		do_action( 'wppopups_builder_after_panel_content', $this->popup, $this->slug );

		echo '</div>';

		echo '</div>';

		echo '</div>';

		echo '</div>';
	}

	/**
	 * Outputs the panel's sidebar if we have one.
	 *
	 * @since 2.0.0
	 */
	public function panel_sidebar() {
	}

	/**
	 * Outputs panel sidebar sections.
	 *
	 * @param string $name
	 * @param string $slug
	 * @param string $icon
	 * @param bool $clickable
	 *
	 * @since 2.0.0
	 *
	 */
	public function panel_sidebar_section( $name, $slug, $icon = '', $clickable = true ) {

		$class = '';
		$class .= 'default' === $slug ? ' default' : '';
		$class .= ! empty( $icon ) ? ' icon' : '';
		$class .= $clickable ? '' : ' not-clickable';

		echo '<a href="#" class="wppopups-panel-sidebar-section wppopups-panel-sidebar-section-' . esc_attr( $slug ) . $class . '" data-section="' . esc_attr( $slug ) . '">';

		if ( ! empty( $icon ) ) {
			echo '<img src="' . esc_url( $icon ) . '">';
		}

		echo esc_html( $name );

		echo '<i class="fa fa-angle-right wppopups-toggle-arrow"></i>';

		echo '</a>';
	}

	/**
	 * Outputs panel content sections.
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_content_section( $slug ) {

		$output = '<div class="wppopups-panel-content-section padding-20 wppopups-panel-content-section-' . esc_attr( $slug ) . '">';
		$output .= '%s';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Outputs the panel's primary content.
	 *
	 * @since 2.0.0
	 */
	public function panel_content() {
		if ( ! $this->mandatory_popup_exist() ) {
			return;
		}
		do_action( 'wpopopups_builder_popup', $this->popup );
	}

	/**
	 * Function that check if popup is needed before showing the section
	 */
	public function check_if_popup_created() {
		// Check if there is a popup created.
		if ( ! $this->popup && $this->need_popup_created ) {
			echo '<div class="wppopups-alert wppopups-alert-info">';
			echo wp_kses(
				sprintf( __( 'You need to <a href="#" class="wppopups-panel-switch" data-panel="setup">setup your popup</a> before you can edit the %s section.', 'wp-popups-lite' ), $this->name ),
				[
					'a' => [
						'href'       => [],
						'class'      => [],
						'data-panel' => [],
					],
				]
			);
			echo '</div>';

			return;
		}
	}

	/**
	 * Helper function to print alert if popup don't exist
	 */
	public function mandatory_popup_exist() {
		// Check if there is a popup created.
		if ( ! $this->popup || empty( $this->popup->id ) ) {
			echo '<div class="wppopups-alert wppopups-alert-info">';
			echo wp_kses(
				__( 'You need to <a href="#" class="wppopups-panel-switch" data-panel="setup">setup your popup</a> before you can start making changes.', 'wp-popups-lite' ),
				[
					'a' => [
						'href'       => [],
						'class'      => [],
						'data-panel' => [],
					],
				]
			);
			echo '</div>';

			return false;
		}

		return true;
	}
}
