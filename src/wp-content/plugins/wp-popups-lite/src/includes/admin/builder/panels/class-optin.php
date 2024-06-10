<?php

/**
 * Class WPPopups_Optin
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Builder_Panel_Optin  extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Optin options', 'wppopups-pro' );
		$this->slug    = 'optin';
		$this->icon    = 'fa-envelope';
		$this->order   = 10;
		$this->sidebar = true;
		$this->display_panel = false;
		
		//add_filter( 'wppopups_builder_panels', [ $this, 'add_builder_panels' ] );

		// only if we have a connection
		if( ! empty( $this->popup_data['providers'] ) ) {
			$this->display_panel = true;
			//add_filter( 'wppopups_builder_panels', [ $this, 'optin_builder_panels' ] );
			
			// Builder fields
			add_action( 'wppopups_builder_fields', [ $this, 'fields' ] );
			add_action( 'wppopups_builder_fields_options', [ $this, 'fields_options' ] );
			add_action( 'wppopups_builder_preview', [ $this, 'preview' ] );

			// Template for form builder previews.
			add_action( 'wppopups_builder_print_footer_scripts', [ $this, 'field_preview_templates' ]);

			// Add structure
			add_action( 'wppopups_popup_content_after', [ $this, 'add_optin_fields' ] );
		}
	}
	/**
	 * Enqueue assets for the Optin panel.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {
		// CSS.
		wp_enqueue_style(
			'wppopups-builder-fields',
			WPPOPUPS_PLUGIN_URL . 'assets/css/admin-builder-fields.css',
			null,
			WPPOPUPS_VERSION
		);
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

		$sections = [
			'fields'			=> esc_html__( 'Fields', 'wppopups-pro' ),
			'optin_styles'		=> esc_html__( 'Appearance', 'wppopups-pro' ),
			'bottom_content'	=> esc_html__( 'Bottom Content', 'wppopups-pro' ),
			'success'			=> esc_html__( 'Success Message', 'wppopups-pro' ),
			'redirect'			=> esc_html__( 'Redirect', 'wppopups-pro' ),
		];

		$sections = apply_filters( 'wppopups_builder_optin_sections', $sections, $this->popup_data );
		
		foreach ( $sections as $slug => $section ) {
			$this->panel_sidebar_section( $section, $slug );
			$func = "panel_sidebar_{$slug}";
			echo sprintf( $this->panel_sidebar_content_section( $slug ), $this->$func( $slug ) ); // phpcs:ignore
		}
		do_action( 'wppopups_optin_panel_sidebar', $this->popup );
	}

	/**
	 * Optin fields and text strings
	 *
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function panel_sidebar_fields( $slug ) {
		ob_start();
		?>

		<ul class="wppopups-tabs wppopups-clear">

			<li class="wppopups-tab" id="add-fields">
				<a href="#" class="active">
					<?php esc_html_e( 'Add Fields', 'wp-popups-lite' ); ?>
					<i class="fa fa-angle-down"></i>
				</a>
			</li>

			<li class="wppopups-tab" id="field-options">
				<a href="#">
					<?php esc_html_e( 'Field Options', 'wp-popups-lite' ); ?>
					<i class="fa fa-angle-right"></i>
				</a>
			</li>

		</ul>

		<div class="wppopups-add-fields wppopups-tab-content">
			<?php do_action( 'wppopups_builder_fields', $this->popup_data ); ?>
		</div>

		<div id="wppopups-field-options" class="wppopups-field-options wppopups-tab-content">
			<?php do_action( 'wppopups_builder_fields_options', $this->popup_data ); ?>
		</div>

		<?php
		// Let others add fields.
		do_action( 'wppopups_sidebar_optin_fields', $this );

		return ob_get_clean();
	}

	/**
	 * Appeareance sidebar panel
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function panel_sidebar_optin_styles( $slug ) {
		ob_start();

		wppopups_panel_field(
			'text',
			$slug,
			'optin_form_css',
			$this->popup_data,
			esc_html__( 'Form CSS class', 'wppopups-pro' ),
			[
				'tooltip'     => esc_html__('Enter CSS class names for the optin form. Multiple names should be separated with spaces.', 'wppopups' ),
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_text',
			$this->popup_data,
			esc_html__( 'Submit Text', 'wppopups-pro' ),
			[
				'default'	=> esc_html__( 'Submit', 'wppopups-pro' ),
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_text_processing',
			$this->popup_data,
			esc_html__( 'Submit Text Processing', 'wppopups-pro' ),
			[
				'default'	=> esc_html__( 'Submitting', 'wppopups-pro' ),
				'tooltip'	=> esc_html__( 'Enter the submit button text you would like the button display while the form submit is processing.', 'wppopups-pro' ),
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_class',
			$this->popup_data,
			esc_html__( 'Submit Button CSS Class', 'wppopups-pro' ),
			[
				'default'	=> esc_html__( 'Submitting', 'wppopups-pro' ),
				'tooltip'	=> esc_html__( 'Enter CSS class names for the form submit button. Multiple names should be separated with spaces.', 'wppopups-pro' ),
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_text_color',
			$this->popup_data,
			esc_html__( 'Submit Text Color', 'wppopups-pro' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => wppopups_default_optin_submit_color( $this->popup_data['settings'] )[3],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_bg_color',
			$this->popup_data,
			esc_html__( 'Submit Background Color', 'wppopups-pro' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => wppopups_default_optin_submit_color( $this->popup_data['settings'] )[0],
			]
		);


		wppopups_panel_field(
			'text',
			$slug,
			'submit_bg_color_hover',
			$this->popup_data,
			esc_html__( 'Submit Hover Color', 'wppopups-pro' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => wppopups_default_optin_submit_color( $this->popup_data['settings'] )[1],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'submit_border_color',
			$this->popup_data,
			esc_html__( 'Submit Border Color', 'wppopups-pro' ),
			[
				'input_class' => 'wppopups-color-picker',
				'default'     => wppopups_default_optin_submit_color( $this->popup_data['settings'] )[2],
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_optin_styles', $this );

		return ob_get_clean();
	}

	/**
	 * Bottom content message sidebar panel
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function panel_sidebar_bottom_content( $slug ) {
		ob_start();
		wppopups_panel_field(
			'tinymce',
			'bottom_content',
			'bottom_content',
			$this->popup_data,
			esc_html__( 'Content', 'wppopups-pro' ),
			[
				'default' => '',
				'tinymce' => [
					'media_buttons' => true,
				],
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_optin_bottom_content', $this );

		return ob_get_clean();
	}
	/**
	 * Success message sidebar panel
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function panel_sidebar_success( $slug ) {
		ob_start();
		wppopups_panel_field(
			'tinymce',
			'success',
			'optin_success',
			$this->popup_data,
			esc_html__( 'Content', 'wppopups-pro' ),
			[
				'default' => esc_html__( 'Thanks for subscribing! Please check your email for further instructions.', 'wppopups-pro' ),
				'tinymce' => [
					'media_buttons' => true,
				],
			]
		);

		wppopups_panel_field(
			'text',
			$slug,
			'optin_success_seconds',
			$this->popup_data,
			esc_html__( 'Automatically close popup after success', 'wppopups-pro' ),
			[
				'default'     => '0',
				'type'      => 'number',
				'after'    => esc_html__( 'seconds', 'wppopups-pro' ),
				'tooltip' => esc_html__( 'Leave 0 seconds to keep popup open', 'wppopups-pro' ),
			]
		);

		// Let others add fields.
		do_action( 'wppopups_sidebar_optin_success', $this );

		return ob_get_clean();
	}

	/**
	 * Redirect sidebar panel
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function panel_sidebar_redirect( $slug ) {
		ob_start();
		wppopups_panel_field(
			'text',
			$slug,
			'optin_redirect',
			$this->popup_data,
			esc_html__( 'Redirect URL', 'wppopups-pro' ),
			[
				'tooltip'     => esc_html__('Enter a URL to redirect users after success submission.', 'wppopups-pro' ),
			]
		);
		wppopups_panel_field(
			'select',
			$slug,
			'pass_lead_data',
			$this->popup_data,
			esc_html__( 'Pass lead data to redirect url ?', 'wppopups-pro' ),
			[
				'default' => '0',
				'options' => [
					'1' => 'Yes',
					'0'  => 'No',
				],
				'tooltip'     => esc_html__('You can pass email and name as query string data to the redirect url.', 'wppopups-pro' ),
			]
		);
		// Let others add fields.
		do_action( 'wppopups_sidebar_optin_redirect', $this );

		return ob_get_clean();
	}



	/*
	
		FIELDS METHODS
	
	 */
	

	/**
	 * Builder field buttons.
	 *
	 * @since 1.0.0
	 */
	public function fields() {

		$fields = [
			'standard' => [
				'group_name' => esc_html__( 'Standard Fields', 'wp-popups-lite' ),
				'fields'     => [],
			],
		];

		$fields = apply_filters( 'wppopups_builder_fields_buttons', $fields );

		// Output the buttons.
		foreach ( $fields as $id => $group ) {

			usort( $group['fields'], [ $this, 'field_order' ] );

			echo '<div class="wppopups-add-fields-group">';
			echo '<a href="#" class="wppopups-add-fields-heading" data-group="'. esc_attr( $id ) .'">';
			echo '<span>' . esc_html( $group['group_name'] ) . '</span>';
			echo '<i class="fa fa-angle-down"></i>';
			echo '</a>';
			echo '<div class="wppopups-add-fields-buttons">';

			foreach ( $group['fields'] as $field ) {

				$atts = apply_filters( 'wppopups_builder_field_button_attributes', [
					'id'    => 'wppopups-add-fields-' . $field['type'],
					'class' => [ 'wppopups-add-fields-button' ],
					'data'  => [ 'field-type' => $field['type'], ],
					'atts'  => [],
				], $field, $this->popup_data );

				if ( ! empty( $field['class'] ) ) {
					$atts['class'][] = $field['class'];
				}

				echo '<button ' . wppopups_html_attributes( $atts['id'], $atts['class'], $atts['data'], $atts['atts'] ) . '>';
					if ( $field['icon'] ) {
						echo '<i class="fa ' . esc_attr( $field['icon'] ) . '"></i> ';
					}
					echo esc_html( $field['name'] );
				echo '</button>';
			}

			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Editor Field Options.
	 *
	 * @since 1.0.0
	 */
	public function fields_options() {

		// Check to make sure the form actually has fields created already.
		if ( empty( $this->popup_data['fields'] ) ) {
			$this->no_fields_options();

			return;
		}

		$fields = $this->popup_data['fields'];

		foreach ( $fields as $field ) {

			$class = apply_filters( 'wppopups_builder_field_option_class', '', $field );

			printf( '<div class="wppopups-field-option wppopups-field-option-%s %s" id="wppopups-field-option-%d" data-field-id="%d">', sanitize_html_class( $field['type'] ), sanitize_html_class( $class ), (int) $field['id'], (int) $field['id'] );

			printf( '<input type="hidden" name="fields[%d][id]" value="%d" class="wppopups-field-option-hidden-id">', $field['id'], $field['id'] );

			printf( '<input type="hidden" name="fields[%d][type]" value="%s" class="wppopups-field-option-hidden-type">', $field['id'], esc_attr( $field['type'] ) );

			do_action( "wppopups_builder_fields_options_{$field['type']}", $field );

			echo '</div>';
		}
	}
	
	/**
	 * Editor preview (right pane).
	 *
	 * @since 1.0.0
	 */
	public function preview() {

		// Check to make sure the form actually has fields created already.
		if ( empty( $this->popup_data['fields'] ) ) {
			$this->no_fields_preview();

			return;
		}

		$fields = $this->popup_data['fields'];

		foreach ( $fields as $field ) {

			$css  = ! empty( $field['size'] ) ? 'size-' . esc_attr( $field['size'] ) : '';
			$css .= ! empty( $field['label_hide'] ) && $field['label_hide'] == '1' ? ' label_hide' : '';
			$css .= ! empty( $field['sublabel_hide'] ) && $field['sublabel_hide'] == '1' ? ' sublabel_hide' : '';
			$css .= ! empty( $field['required'] ) && $field['required'] == '1' ? ' required' : '';
			$css .= ! empty( $field['input_columns'] ) && $field['input_columns'] === '2' ? ' wppopups-list-2-columns' : '';
			$css .= ! empty( $field['input_columns'] ) && $field['input_columns'] === '3' ? ' wppopups-list-3-columns' : '';
			$css .= ! empty( $field['input_columns'] ) && $field['input_columns'] === 'inline' ? ' wppopups-list-inline' : '';
			$css .= isset( $field['meta']['delete'] ) && $field['meta']['delete'] === false ? ' no-delete' : '';
			$css .= isset( $field['meta']['duplicate'] ) && $field['meta']['duplicate'] === false ? ' no-duplicate' : '';

			$css = apply_filters( 'wppopups_field_preview_class', $css, $field );

			printf( '<div class="wppopups-field wppopups-field-%s %s" id="wppopups-field-%d" data-field-id="%d" data-field-type="%s">', $field['type'], $css, $field['id'], $field['id'], $field['type'] );

			if ( apply_filters( 'wppopups_field_preview_display_duplicate_button', true, $field, $this->popup_data ) ) {
				printf( '<a href="#" class="wppopups-field-duplicate" title="%s"><i class="fa fa-files-o" aria-hidden="true"></i></a>', esc_html__( 'Duplicate Field', 'wp-popups-lite' ) );
			}

			printf( '<a href="#" class="wppopups-field-delete" title="%s"><i class="fa fa-trash" aria-hidden="true"></i></a>', esc_html__( 'Delete Field', 'wp-popups-lite' ) );

			printf( '<span class="wppopups-field-helper">%s</span>', esc_html__( 'Click to edit. Drag to reorder.', 'wp-popups-lite' ) );

			do_action( "wppopups_builder_fields_previews_{$field['type']}", $field );

			echo '</div>';
		}
	}


	/**
	 * [add_optin_fields description]
	 * @param [type] $popup [description]
	 */
	public function add_optin_fields() {
		if( empty( $this->popup_data['providers'] ) ) {
			return;
		}
		// load defaults if no theme
		$optin = isset( $this->popup_data['optin_styles'] ) ? $this->popup_data['optin_styles'] : [
			'submit_bg_color' => '#50bbe8',
			'submit_bg_color_hover' => '#41A2CA',
			'submit_border_color' => '#429bc0',
			'submit_text_color' => '#FFF',
		];
		?>
		<div class="wppopups-field-container">
			<div class="wppopups-no-fields-holder wppopups-hidden">
				<?php $this->no_fields_options(); ?>
				<?php $this->no_fields_preview(); ?>
			</div>

			<div class="wppopups-field-wrap">
				<?php do_action( 'wppopups_builder_preview', $this->popup ); ?>
			</div>

			<?php
			$submit = ! empty( $optin['submit_text'] ) ? $optin['submit_text'] : wppopups_default_optin_submit_text();
			
			printf( '<p class="wppopups-field-submit"><input type="submit" value="%s" class="wppopups-submit-button"></p>', esc_attr( $submit ) );
			?>
			<?php wppopups_debug_data( $this->popup_data ); ?>
		</div>

		<?php
			// optin style
			$submit_bg = isset( $optin['submit_bg_color'] ) ? $optin['submit_bg_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[0];
			$submit_hover = isset( $optin['submit_bg_color_hover'] ) ? $optin['submit_bg_color_hover'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[1];
			$submit_border = isset( $optin['submit_border_color'] ) ? $optin['submit_border_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[2];
			$submit_color = isset( $optin['submit_text_color'] ) ? $optin['submit_text_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[3];
		?>
		<style type="text/css">
			#spu-<?php echo esc_attr( $this->popup_data['id'] ); ?> .wppopups-field-submit input.wppopups-submit-button {
				background-color: <?= esc_attr( $submit_bg );?>;
				border-color: <?= esc_attr( $submit_border );?>;
				color: <?= esc_attr( $submit_color );?>;
			}
			#spu-<?php echo esc_attr( $this->popup_data['id'] ); ?> .wppopups-field-submit input.wppopups-submit-button:hover {
				background-color: <?= esc_attr( $submit_hover );?>;
			}

			#spu-<?php echo esc_attr( $this->popup_data['id'] ); ?> .wppopups-field-submit input.wppopups-submit-button svg path,
			#spu-<?php echo esc_attr( $this->popup_data['id'] ); ?> .wppopups-field-submit input.wppopups-submit-button svg rect{
				fill: <?= esc_attr( $submit_color );?>;
			}
		</style>

		<?php
	}


	/**
	 * No fields options markup.
	 *
	 * @since 1.6.0
	 */
	public function no_fields_options() {
		printf(
			'<p class="no-fields">%s</p>',
			esc_html__( 'You don\'t have any fields yet.', 'wp-popups-lite' )
		);
	}

	/**
	 * No fields preview placeholder markup.
	 *
	 * @since 1.6.0
	 */
	public function no_fields_preview() {
		printf(
			'<p class="no-fields-preview">%s</p>',
			esc_html__( 'You don\'t have any fields yet. Add some!', 'wp-popups-lite' )
		);
	}

	/**
	 * Sort Add Field buttons by order provided.
	 *
	 * @since 1.0.0
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 */
	public function field_order( $a, $b ) {
		return $a['order'] - $b['order'];
	}

	/**
	 * Template for form builder preview.
	 *
	 * @since 1.4.5
	 */
	public function field_preview_templates() {

		// Checkbox, Radio, and Payment Multiple/Checkbox field choices.
		?>
		<script type="text/html" id="tmpl-wppopups-field-preview-checkbox-radio-payment-multiple">
			<# if ( data.settings.choices_images ) { #>
			<ul class="primary-input wppopups-image-choices wppopups-image-choices-{{ data.settings.choices_images_style }}">
				<# _.each( data.order, function( choiceID, key ) {  #>
				<li class="wppopups-image-choices-item<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' wppopups-selected' ); } #>">
					<label>
						<span class="wppopups-image-choices-image">
							<# if ( ! _.isEmpty( data.settings.choices[choiceID].image ) ) { #>
							<img src="{{ data.settings.choices[choiceID].image }}" alt="{{ data.settings.choices[choiceID].label }}"<# if ( data.settings.choices[choiceID].label ) { print( ' title="{{ data.settings.choices[choiceID].label }}"' ); } #>>
							<# } else { #>
							<img src="{{ wppopups_builder.image_placeholder }}" alt="{{ data.settings.choices[choiceID].label }}"<# if ( data.settings.choices[choiceID].label ) { print( ' title="{{ data.settings.choices[choiceID].label }}"' ); } #>>
							<# } #>
						</span>
						<# if ( 'none' === data.settings.choices_images_style ) { #>
							<br>
							<input type="{{ data.type }}" disabled<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } else { #>
							<input class="wppopups-screen-reader-element" type="{{ data.type }}" disabled<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>
						<# } #>
						<span class="wppopups-image-choices-label">{{{ wpp.sanitizeHTML( data.settings.choices[choiceID].label ) }}}</span>
					</label>
				</li>
				<# }) #>
			</ul>
			<# } else { #>
			<ul class="primary-input">
				<# _.each( data.order, function( choiceID, key ) {  #>
				<li>
					<input type="{{ data.type }}" disabled<# if ( 1 === data.settings.choices[choiceID].default ) { print( ' checked' ); } #>>{{{ wpp.sanitizeHTML( data.settings.choices[choiceID].label ) }}}
				</li>
				<# }) #>
			</ul>
			<# } #>
		</script>
		<?php
	}
}

new WPPopups_Builder_Panel_Optin();