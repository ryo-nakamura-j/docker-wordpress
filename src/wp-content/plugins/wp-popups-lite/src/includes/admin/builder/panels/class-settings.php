<?php

/**
 * Settings management panel.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Settings extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Settings', 'wp-popups-lite' );
		$this->slug    = 'settings';
		$this->icon    = 'fa-sliders';
		$this->order   = 10;
		$this->sidebar = true;
	}

	/**
	 * Outputs the Settings panel sidebar.
	 *
	 * @since 2.0.0
	 */
	public function panel_sidebar() {

		// Sidebar contents are not valid unless we have a popup.
		if ( ! $this->popup ) {
			return;
		}

		$sections = [
			'general'  => esc_html__( 'General', 'wp-popups-lite' ),
			'triggers' => esc_html__( 'Triggers', 'wp-popups-lite' ),
			'cookies'  => esc_html__( 'Cookies/Close', 'wp-popups-lite' ),
		];
		$sections = apply_filters( 'wppopups_builder_settings_sections', $sections, $this->popup_data );
		foreach ( $sections as $slug => $section ) {
			$this->panel_sidebar_section( $section, $slug );
		}
		do_action( 'wppopups_settings_panel_sidebar', $this->popup );
	}

	/**
	 * Outputs the Settings panel primary content.
	 *
	 * @since 2.0.0
	 */
	public function panel_content() {

		if ( ! $this->mandatory_popup_exist() ) {
			return;
		}

		// --------------------------------------------------------------------//
		// General.
		// --------------------------------------------------------------------//
		echo '<div class="wppopups-panel-content-section wppopups-panel-content-section-general">';
		echo '<div class="wppopups-panel-content-section-title">';
		esc_html_e( 'General', 'wp-popups-lite' );
		echo '</div>';
		wppopups_panel_field(
			'text',
			'settings',
			'popup_title',
			$this->popup_data,
			esc_html__( 'Popup Name', 'wp-popups-lite' ),
			[
				'default' => $this->popup->title,
			]
		);
		wppopups_panel_field(
			'textarea',
			'settings',
			'popup_desc',
			$this->popup_data,
			esc_html__( 'Popup Description', 'wp-popups-lite' )
		);
		wppopups_panel_field(
			'radio',
			'settings',
			'test_mode',
			$this->popup_data,
			esc_html__( 'Test Mode', 'wp-popups-lite' ),
			[
				'options' => [
					'1' => [ 'label' => esc_html__( 'Yes', 'wp-popups-lite' ) ],
					'0' => [ 'label' => esc_html__( 'No', 'wp-popups-lite' ) ],
				],
				'default' => '0',
				'tooltip' => esc_html__( 'If test mode is enabled, the box will show up regardless of whether a cookie has been set. (To admins only)', 'wp-popups-lite' ),
			]
		);

		wppopups_panel_field(
			'radio',
			'settings',
			'powered_link',
			$this->popup_data,
			esc_html__( 'Show Powered by link?', 'wp-popups-lite' ),
			[
				'options' => [
					'1' => [ 'label' => esc_html__( 'Yes', 'wp-popups-lite' ) ],
					'0' => [ 'label' => esc_html__( 'No', 'wp-popups-lite' ) ],
				],
				'default' => '0',
				'tooltip' => sprintf( esc_html__( 'Shows a "powered by" link below your popup. If your affiliate link is set in the Plugin settings page, it will be used.', 'wp-popups-lite' ), admin_url( 'admin.php?page=wppopups-settings' ) ),
			]
		);
		wppopups_panel_field(
			'text',
			'settings',
			'popup_class',
			$this->popup_data,
			esc_html__( 'Popup CSS Class', 'wp-popups-lite' ),
			[
				'tooltip' => esc_html__( 'Enter CSS class names for the popup wrapper. Multiple class names should be separated with spaces.', 'wp-popups-lite' ),
			]
		);
		wppopups_panel_field(
			'text',
			'settings',
			'popup_hidden_class',
			$this->popup_data,
			'',
			[
				'type' => 'hidden',
			]
		);

		do_action( 'wppopups_popup_settings_general', $this );
		echo '</div>';

		// --------------------------------------------------------------------//
		// Triggers.
		// --------------------------------------------------------------------//
		echo '<div class="wppopups-panel-content-section wppopups-panel-content-section-triggers">';
		echo '<div class="wppopups-panel-content-section-title">';
		esc_html_e( 'Triggers', 'wp-popups-lite' );
		echo '</div>';
		echo '<div class="trigger-group">';
		$triggers = ! empty( $this->popup_data['triggers'] ) ? $this->popup_data['triggers'] : WPPopups_Triggers::defaults();
		if ( is_array( $triggers ) ) {
			foreach ( $triggers as $key => $trigger ) {
				echo '<div class="trigger-tr" data-key="' . esc_attr( $key ) . '">';
				echo '<div class="trigger-td trigger-option">';
				wppopups_triggers_field(
					'select',
					$key,
					'trigger',
					$this->popup_data,
					'',
					[
						'default' => 'seconds',
						'options' => WPPopups_Triggers::options(),
					]
				);
				echo '</div>';// trigger_td
				echo '<div class="trigger-td trigger-value">';
				wppopups_triggers_field(
					WPPopups_Triggers::field_type( $trigger['trigger'] ),
					$key,
					'value',
					$this->popup_data,
					'',
					[
						'default' => '3',
					]
				);
				echo '</div>';// trigger_td
				echo '<div class="trigger-td trigger-actions">';
				echo '<a class="add button-primary" title="' . esc_html__( 'Add a new trigger', 'wp-popups-lite' ) . '" href="#"><i class="fa fa-plus-circle"></i> ADD</a>';
				echo '<a class="remove button"  title="' . esc_html__( 'Delete trigger', 'wp-popups-lite' ) . '" href="#">&times;</a>';
				echo '</div>';// trigger_td
				echo '</div>'; //trigger_tr

			}
		}
		echo '</div>';// trigger_group
		// auto hide on scroll
		wppopups_panel_field(
			'checkbox',
			'settings',
			'auto_hide',
			$this->popup_data,
			esc_html__( 'Hide on scroll ?', 'wp-popups-lite' ),
			[
				'class'   => 'auto_hide',
				'default' => '0',
				'tooltip' => esc_html__( 'Automatically hide when user scroll up', 'wp-popups-lite' ),
			]
		);
		// Can't find a way to clone existing choicesjs.
		$this->print_clone_group();

		do_action( 'wppopups_popup_settings_triggers', $this );
		echo '</div>';

		// --------------------------------------------------------------------//
		// Cookies.
		// --------------------------------------------------------------------//
		echo '<div class="wppopups-panel-content-section wppopups-panel-content-section-cookies">';
		echo '<div class="wppopups-panel-content-section-title">';
		esc_html_e( 'Close', 'wp-popups-lite' );
		echo '</div>';

		wppopups_panel_field(
			'radio',
			'settings',
			'close_on_conversion',
			$this->popup_data,
			esc_html__( 'Close on conversion', 'wp-popups-lite' ),
			[
				'options' => [
					'1' => [ 'label' => esc_html__( 'Yes', 'wp-popups-lite' ) ],
					'0' => [ 'label' => esc_html__( 'No', 'wp-popups-lite' ) ],
				],
				'default' => '1',
				'tooltip' => esc_html__( 'Popup will close on conversion. Eg: When form is submitted or link is clicked', 'wp-popups-lite' ),
			]
		);


		do_action( 'wppopups_popup_settings_close', $this );

		echo '<div class="wppopups-panel-content-section-title">';
		esc_html_e( 'Cookies', 'wp-popups-lite' );
		echo '</div>';

		wppopups_panel_field(
			'text',
			'settings',
			'conversion_cookie_name',
			$this->popup_data,
			esc_html__( 'Conversion cookie name', 'wp-popups-lite' ),
			[
				'default' => 'spu_conversion_' . $this->popup->id,
				'tooltip' => esc_html__( 'The name that the popup will use for convertion cookie. Changing this name will reset the cookie, so all users will see popup again.', 'wp-popups-lite' ),
			]
		);
		wppopups_panel_field(
			'text',
			'settings',
			'conversion_cookie_duration',
			$this->popup_data,
			esc_html__( 'Conversion cookie duration', 'wp-popups-lite' ),
			[
				'type'    => 'number',
				'default' => '0',
				'tooltip' => esc_html__( 'When a user do a conversion like for example a click or form submission, how many days the popup should it stay hidden?', 'wp-popups-lite' ),
			]
		);
		wppopups_panel_field(
			'select',
			'settings',
			'conversion_cookie_type',
			$this->popup_data,
			esc_html__( 'Conversion in minutes, hours or days ?', 'wp-popups-lite' ),
			[
				'options' => [
					'd' => esc_html__( 'Days', 'wp-popups-lite' ),
					'h' => esc_html__( 'Hours', 'wp-popups-lite' ),
					'm' => esc_html__( 'Minutes', 'wp-popups-lite' ),
				],
				'default' => 'd',
			]
		);
		wppopups_panel_field(
			'text',
			'settings',
			'closing_cookie_name',
			$this->popup_data,
			esc_html__( 'Closing cookie name', 'wp-popups-lite' ),
			[
				'default' => 'spu_closing_' . $this->popup->id,
				'tooltip' => esc_html__( 'The name that the popup will use for closing cookie. Changing this name will reset the cookie, so all users will see popup again.', 'wp-popups-lite' ),
			]
		);
		wppopups_panel_field(
			'text',
			'settings',
			'closing_cookie_duration',
			$this->popup_data,
			esc_html__( 'Closing cookie duration', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'tooltip' => esc_html__( 'After closing the popup, how many days should it stay hidden?', 'wp-popups-lite' ),
			]
		);
		wppopups_panel_field(
			'select',
			'settings',
			'closing_cookie_type',
			$this->popup_data,
			esc_html__( 'Closing in minutes, hours or days ?', 'wp-popups-lite' ),
			[
				'options' => [
					'd' => esc_html__( 'Days', 'wp-popups-lite' ),
					'h' => esc_html__( 'Hours', 'wp-popups-lite' ),
					'm' => esc_html__( 'Minutes', 'wp-popups-lite' ),
				],
				'default' => 'd',
			]
		);
		do_action( 'wppopups_popup_settings_cookies', $this );
		echo '</div>';

		do_action( 'wppopups_popup_settings_panel_content', $this );
	}

	/**
	 * Helper function to print select fields for triggers
	 * @since 2.0.0
	 *
	 */
	private function print_clone_group() {

		echo '<div class="trigger-group-clone trigger-tr" data-key="trigger_clone_key" style="display: none;">';
		echo '<div class="trigger-td trigger-option">';
		wppopups_triggers_field(
			'select',
			'trigger_id',
			'trigger',
			'',
			'',
			[
				'clean_select' => true,
				'default'      => 'seconds',
				'attributes'   => [ 'disabled' => 'disabled' ],
				'options'      => WPPopups_Triggers::options(),
			]
		);
		echo '</div>';// trigger_td
		echo '<div class="trigger-td trigger-value">';
		wppopups_triggers_field(
			'number',
			'trigger_id',
			'value',
			'',
			'',
			[
				'default'    => '',
				'attributes' => [ 'disabled' => 'disabled' ],
			]
		);
		echo '</div>';// trigger_td
		echo '<div class="trigger-td trigger-actions">';
		echo '<a class="add button-primary" title="' . esc_html__( 'Add a new trigger', 'wp-popups-lite' ) . '" href="#"><i class="fa fa-plus-circle"></i> AND</a>';
		echo '<a class="remove button"  title="' . esc_html__( 'Delete trigger', 'wp-popups-lite' ) . '" href="#">&times;</a>';
		echo '</div>';// trigger_td
		echo '</div>'; //trigger_tr
	}
}

new WPPopups_Builder_Panel_Settings();
