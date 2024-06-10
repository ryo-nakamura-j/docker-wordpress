<?php

/**
 * Addons panel.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Addons extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Addons', 'wppopups-pro' );
		$this->slug    = 'addons';
		$this->icon    = 'fa-magic';
		$this->order   = 10;
		$this->sidebar = true;
		$this->display_panel = false;

		//add_filter( 'wppopups_builder_panels', [ $this, 'add_builder_panels' ] );
		
		// only if we have a connection
		$addons = wppopups_get_addons_available();
	
		if( ! empty( $addons ) ) {
			$this->display_panel = true;
		}
	}

	/**
	 * Enqueue assets for the Addons panel.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {


	}

	/**
	 * Builder optin panel
	 * @param $panels
	 *
	 * @return array
	 */
	public function add_builder_panels( $panels ) {

		$addons = wppopups_get_addons_available();

		if( empty( $addons ) )
			return $panels;
		
		$this->display_panel = true;
		$panels[] = 'addons';

		return $panels;
	}

	/**
	 * Outputs the Provider panel sidebar.
	 *
	 * @since 2.0.0
	 */
	public function panel_sidebar() {

		// Sidebar contents are not valid unless we have a popup.
		if ( ! $this->popup ) {
			return;
		}

		$this->panel_sidebar_section( 'Default', 'default' );

		do_action( 'wppopups_addons_panel_sidebar', $this->popup );
	}

	/**
	 * Outputs the Provider panel primary content.
	 *
	 * @since 2.0.0
	 */
	public function panel_content() {

		if ( ! $this->mandatory_popup_exist() ) {
			return;
		}

		// An array of all the active provider addons.
		$addons_active = wppopups_get_addons_available();

		if ( empty( $addons_active ) ) {

			// Check for active provider addons. When no provider addons are
			// activated let the user know they need to install/activate an
			// addon to setup a provider.
			echo '<div class="wppopups-panel-content-section wppopups-panel-content-section-info">';
			echo '<h5>' . esc_html__( 'Install Your Addons Integration', 'wppopups-pro' ) . '</h5>';
			echo '<p>' .
				sprintf(
					wp_kses(
						/* translators: %s - plugin admin area Addons page. */
						__( 'It seems you do not have any addons activated. You can head over to the <a href="%s">Addons page</a> to install and activate the addon for your section.', 'wppopups-pro' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					'https://wppopups.com/account/downloads/'
				) .
				'</p>';
			echo '</div>';
		} else {

			// Everything is good - display default instructions.
			echo '<div class="wppopups-panel-content-section ppopups-panel-content-section-default">';
			echo '<h5>' . esc_html__( 'Select Your Addon Integration', 'wppopups-pro' ) . '</h5>';
			echo '<p>' . esc_html__( 'Select your addon from the options on the left. If you don\'t see your addon listed, then let us know and we\'ll do our best to get it added as fast as possible.', 'wppopups-pro' ) . '</p>';
			echo '</div>';
		}

		do_action( 'wppopups_addons_panel_content', $this->popup );
	}
}

new WPPopups_Builder_Panel_Addons();
