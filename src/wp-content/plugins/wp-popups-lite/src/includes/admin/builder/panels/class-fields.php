<?php

/**
 * Fields management panel.
 *
 * @since 1.0.0
 */
class WPPopups_Builder_Panel_Fields extends WPPopups_Builder_Panel {

	/**
	 * All systems go.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define panel information.
		$this->name    = esc_html__( 'Fields', 'wp-popups-lite' );
		$this->slug    = 'fields';
		$this->icon    = 'fa-list-alt';
		$this->order   = 10;
		$this->sidebar = true;

		if ( ! empty( $this->popup_data['providers'] ) ) {
			
			$this->display_panel = true;

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
	 * Enqueue assets for the Fields panel.
	 *
	 * @since 1.0.0
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
	 * Output the Field panel sidebar.
	 *
	 * @since 1.0.0
	 */
	public function panel_sidebar() {

		// Sidebar contents are not valid unless we have a form.
		if ( ! $this->display_panel ) {
			return;
		}
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
	}


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

		?>
		<div class="wppopups-no-fields-holder wppopups-hidden">
			<?php $this->no_fields_options(); ?>
			<?php $this->no_fields_preview(); ?>
		</div>

		<div class="wppopups-field-wrap">
			<?php do_action( 'wppopups_builder_preview', $this->popup ); ?>
		</div>

		<?php
		$submit = ! empty( $this->popup_data['settings']['submit_text'] ) ? $this->popup_data['settings']['submit_text'] : esc_html__( 'Submit', 'wp-popups-lite' );
		printf( '<p class="wppopups-field-submit"><input type="submit" value="%s" class="wppopups-field-submit-button"></p>', esc_attr( $submit ) );
		?>
		<?php wppopups_debug_data( $this->popup_data ); ?>

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

//new WPPopups_Builder_Panel_Fields();
