<?php

/**
 * Appearance management panel.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Appearance extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Appearance', 'wp-popups-lite' );
		$this->slug    = 'appearance';
		$this->icon    = 'fa-paint-brush';
		$this->order   = 10;
		$this->sidebar = true;
	}

	/**
	 * Outputs the Appearance panel sidebar.
	 *
	 * @since 2.0.0
	 */
	public function panel_sidebar() {

		// Sidebar contents are not valid unless we have a popup.
		if ( ! $this->popup ) {
			return;
		}

		$sections = [
			'position'  => esc_html__( 'Popup Position', 'wp-popups-lite' ),
			'animation' => esc_html__( 'Popup Animation', 'wp-popups-lite' ),
			'popup_box' => esc_html__( 'Popup Box', 'wp-popups-lite' ),
			'colors'    => esc_html__( 'Background/Colors', 'wp-popups-lite' ),
			'border'    => esc_html__( 'Popup Border', 'wp-popups-lite' ),
			'shadow'    => esc_html__( 'Popup Shadow', 'wp-popups-lite' ),
			'close'     => esc_html__( 'Close Button', 'wp-popups-lite' ),
			'css'       => esc_html__( 'Custom Css', 'wp-popups-lite' ),
		];
		$sections = apply_filters( 'wppopups_builder_appearance_sections', $sections, $this->popup_data );
		foreach ( $sections as $slug => $section ) {
			$this->panel_sidebar_section( $section, $slug );
			$func = "panel_sidebar_{$slug}";
			echo sprintf( $this->panel_sidebar_content_section( $slug ), $this->$func( $slug ) ); // phpcs:ignore
		}
		do_action( 'wppopups_appearance_panel_sidebar', $this->popup, $this );
	}

	/**
	 * Print position sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_position( $slug ) {

		ob_start();

		wppopups_panel_field(
			'select',
			$slug,
			'position',
			$this->popup_data,
			esc_html__( 'Popup Position', 'wp-popups-lite' ),
			[
				'default' => 'centered',
				'options' => apply_filters( 'wppopups_popup_positions', [
					'centered'     => esc_html__( 'Centered', 'wp-popups-lite' ),
					'top-left'     => esc_html__( 'Top Left', 'wp-popups-lite' ),
					'top-right'    => esc_html__( 'Top Right', 'wp-popups-lite' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'wp-popups-lite' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'wp-popups-lite' ),
					'top-bar'      => esc_html__( 'Top Bar', 'wp-popups-lite' ),
					'bottom-bar'   => esc_html__( 'Bottom Bar', 'wp-popups-lite' ),
				] ),
			]
		);
		// Let others add fields.
		do_action( 'wppopups_sidebar_content_position', $this );

		return ob_get_clean();
	}

	/**
	 * Print animation sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_animation( $slug ) {

		ob_start();

		wppopups_panel_field(
			'select',
			$slug,
			'animation',
			$this->popup_data,
			esc_html__( 'Popup Animation', 'wp-popups-lite' ),
			[
				'default' => 'fade',
				'options' => apply_filters( 'wppopups_popup_animations', [
					'fade'     => esc_html__( 'Fade in', 'wp-popups-lite' ),
					'slide'    => esc_html__( 'Slide in', 'wp-popups-lite' ),
					'disable' => esc_html__( 'Disable animations', 'wp-popups-lite' ),
				] ),
				'data' => apply_filters( 'wppopups_animation_field_data', [] )
			]
		);
		// Let others add fields.
		do_action( 'wppopups_sidebar_content_animation', $this );

		return ob_get_clean();
	}

	/**
	 * Print popup box sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_popup_box( $slug ) {

		ob_start();

		wppopups_panel_field(
			'text',
			$slug,
			'width',
			$this->popup_data,
			esc_html__( 'Width', 'wp-popups-lite' ),
			[
				'default' => '650px',
				'tooltip' => esc_html__( 'You can use px,% or any other valid css width.', 'wp-popups-lite' ),
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'padding',
			$this->popup_data,
			esc_html__( 'Padding', 'wp-popups-lite' ),
			[
				'default' => '20',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		wppopups_panel_field(
			'text',
			$slug,
			'radius',
			$this->popup_data,
			esc_html__( 'Border Radius', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		wppopups_panel_field(
			'select',
			$slug,
			'auto_height',
			$this->popup_data,
			esc_html__( 'Auto Height', 'wp-popups-lite' ),
			[
				'tooltip' => esc_html__('If choose NO you can set your own fixed height', 'wp-popups-lite'),
				'default' => 'yes',
				'options' => [
					'yes'		=> __('Yes', 'wp-popups-lite'),
					'no'		=> __('No', 'wp-popups-lite'),
				],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'height',
			$this->popup_data,
			esc_html__( 'Height', 'wp-popups-lite' ),
			[
				'default' => '350px',
				'data'    => [
					'depend'       => 'wppopups-panel-field-popup_box-auto_height',
					'depend-value' => 'no',
				],
				'tooltip' => esc_html__( 'If your content is higher than this value you will get scroll bars inside the popup', 'wp-popups-lite' ),
			]
		);
		// Let others add fields.
		do_action( 'wppopups_sidebar_content_popup_box', $this );

		return ob_get_clean();
	}

	/**
	 * Print colors sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_colors( $slug ) {

		ob_start();

		wppopups_panel_field(
			'select',
			$slug,
			'show_overlay',
			$this->popup_data,
			esc_html__( 'Show popup overlay ?', 'wp-popups-lite' ),
			[
				'tooltip' => esc_html__('If choose Yes you can select color an opacity of the overlay background', 'wp-popups-lite'),
				'default' => 'yes',
				'options' => [
					'yes-color'	=> __('Yes (Color overlay)', 'wp-popups-lite'),
					'yes-blur'	=> __('Yes (Blur overlay)', 'wp-popups-lite'),
					'no'		=> __('No', 'wp-popups-lite'),
				],
			]
		);
		
		wppopups_panel_field(
			'text',
			$slug,
			'overlay_color',
			$this->popup_data,
			esc_html__( 'Overlay color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => 'rgba(0,0,0,0.5)',
				'data'    => [
					'depend'       => 'wppopups-panel-field-colors-show_overlay',
					'depend-value' => 'yes-color',
				],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'overlay_blur',
			$this->popup_data,
			esc_html__( 'Overlay Blur', 'wp-popups-lite' ),
			[
				'default' => '2',
				'type'    => 'number',
				'after'   => '<span>px</span>',
				'data'    => [
					'depend'       => 'wppopups-panel-field-colors-show_overlay',
					'depend-value' => 'yes-blur',
				],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'bg_color',
			$this->popup_data,
			esc_html__( 'Popup background', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => 'rgb(255,255,255)',
			]
		);


		wppopups_panel_field(
			'img_upload',
			$slug,
			'bg_img',
			$this->popup_data,
			esc_html__( 'Background Image', 'wp-popups-lite' )
		);

		wppopups_panel_field(
			'select',
			$slug,
			'bg_img_repeat',
			$this->popup_data,
			esc_html__( 'Background Repeat', 'wp-popups-lite' ),
			[
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => 'No Repeat',
					'repeat-x'  => 'Repeat X',
					'repeat-y'  => 'Repeat Y',
				],
			]
		);
		wppopups_panel_field(
			'select',
			$slug,
			'bg_img_size',
			$this->popup_data,
			esc_html__( 'Background Size', 'wp-popups-lite' ),
			[
				'default' => 'auto',
				'options' => [
					'auto'    => 'Auto',
					'cover'   => 'Cover',
					'contain' => 'Contain',
				],
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_content_colors', $this );

		return ob_get_clean();
	}

	/**
	 * Print popup border sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_border( $slug ) {

		ob_start();

		wppopups_panel_field(
			'select',
			$slug,
			'border_type',
			$this->popup_data,
			esc_html__( 'Type', 'wp-popups-lite' ),
			[
				'default' => 'none',
				'options' => [
					'none'   => 'None',
					'solid'  => 'Solid',
					'dotted' => 'Dotted',
					'dashed' => 'Dashed',
					'double' => 'Double',
					'groove' => 'Groove',
					'inset'  => 'Inset',
					'outset' => 'Outset',
					'ridge'  => 'Ridge',
				],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'border_color',
			$this->popup_data,
			esc_html__( 'Color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => '#000',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'border_width',
			$this->popup_data,
			esc_html__( 'Width', 'wp-popups-lite' ),
			[
				'default' => '3',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		wppopups_panel_field(
			'text',
			$slug,
			'border_radius',
			$this->popup_data,
			esc_html__( 'Radius', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'border_margin',
			$this->popup_data,
			esc_html__( 'Margin', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_content_border', $this );

		return ob_get_clean();
	}

	/**
	 * Print popup shadow sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_shadow( $slug ) {

		ob_start();

		wppopups_panel_field(
			'text',
			$slug,
			'shadow_color',
			$this->popup_data,
			esc_html__( 'Color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => '#ccc',
				'data'        => [
					'show-alpha'       => 'true',
				],
			]
		);

		wppopups_panel_field(
			'select',
			$slug,
			'shadow_type',
			$this->popup_data,
			esc_html__( 'Type', 'wp-popups-lite' ),
			[
				'default' => 'outset',
				'options' => [
					'none'   => esc_html__( 'None', 'wp-popups-lite' ),
					'outset' => 'Outset',
					'inset'  => 'Inset',
				],
			]
		);
		wppopups_panel_field(
			'text',
			$slug,
			'shadow_x_offset',
			$this->popup_data,
			esc_html__( 'X Offset', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		wppopups_panel_field(
			'text',
			$slug,
			'shadow_y_offset',
			$this->popup_data,
			esc_html__( 'Y Offset', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'shadow_blur',
			$this->popup_data,
			esc_html__( 'Blur', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'shadow_spread',
			$this->popup_data,
			esc_html__( 'Spread', 'wp-popups-lite' ),
			[
				'default' => '0',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		// Let others add fields.
		do_action( 'wppopups_sidebar_content_shadow', $this );

		return ob_get_clean();
	}

	/**
	 * Print popup close sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_close( $slug ) {

		ob_start();

		wppopups_panel_field(
			'text',
			$slug,
			'close_color',
			$this->popup_data,
			esc_html__( 'Color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => '#666',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'close_hover_color',
			$this->popup_data,
			esc_html__( 'Hover Color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => '#000',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'close_shadow_color',
			$this->popup_data,
			esc_html__( 'Shadow Color', 'wp-popups-lite' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => '#000',
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'close_size',
			$this->popup_data,
			esc_html__( 'Size', 'wp-popups-lite' ),
			[
				'default' => '30',
				'type'    => 'number',
				'after'   => '<span>px</span>',
			]
		);
		wppopups_panel_field(
			'select',
			$slug,
			'close_position',
			$this->popup_data,
			esc_html__( 'Position', 'wp-popups-lite' ),
			[
				'default' => 'top_right',
				'options' => [
					'top_right'    => 'Top right',
					'top_left'     => 'Top Left',
					'bottom_right' => 'Bottom Right',
					'bottom_left'  => 'Bottom Left',
				],
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_content_close', $this );

		return ob_get_clean();
	}


	/**
	 * Print popup css sidebar
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function panel_sidebar_css( $slug ) {

		ob_start();

		wppopups_panel_field(
			'textarea',
			$slug,
			'custom_css',
			$this->popup_data,
			esc_html__( 'CSS', 'wp-popups-lite' ),
			[
				'tooltip' => sprintf( esc_html__( 'Add custom CSS for this popup. Be sure to start your rules with %s and use !important when needed to override plugin rules. Refresh screen in order to see changes.', 'wp-popups-lite' ), '#spu-' . $this->popup->id . ' {...}' ),
				'default' => '',
				'rows'    => '8',
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_content_css', $this );

		return ob_get_clean();
	}

}

new WPPopups_Builder_Panel_Appearance;
