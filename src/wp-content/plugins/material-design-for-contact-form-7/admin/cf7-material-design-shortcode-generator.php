<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Shortcode_Generator {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {}


	/**
	 * Get shortcode generator state array
	 */
	public function get_state() {
		global $cf7md_fs;
		
		// Reusable attributes
		$label_attr = array(
			'name'     => 'label',
			'required' => true,
			'default'  => '',
			'label'    => __( 'Label', 'material-design-for-contact-form-7' ),
			'renderer' => 'text'
		);
		$help_attr = array(
			'name'     => 'help',
			'default'  => '',
			'label'    => __( 'Help text', 'material-design-for-contact-form-7' ),
			'help'     => __( 'This will appear below the field', 'material-design-for-contact-form-7' ),
			'renderer' => 'text'
		);
		$icon_attr = array(
			'name'     => 'icon',
			'default'  => '',
			'label'    => __( 'Icon', 'material-design-for-contact-form-7' ),
			'icon'     => 'sentiment_satisfied_alt',
			'help'     => sprintf(
							/* translators: %1$s hyperlink, %2$s: "Material Design", %3$s: end of hyperlink, %4$s: Icon name */
							__( 'Optionally enter the name of a %1$s %2$s Icon%3$s. E.g. <code>%4$s</code>.', 'material-design-for-contact-form-7' ),
							'<a href="https://material.io/tools/icons/" target="_blank">',
							'Material Design',
							'</a>',
							'person'
						  ),
			'renderer' => 'text',
			'locked'   => $cf7md_fs->is_free_plan()
		);
		$outlined_attr = array(
			'name'     => 'outlined',
			'default'  => '',
			'label'    => __( 'Outlined', 'material-design-for-contact-form-7' ),
			'options'  => array( 'yes' => __( 'Use outlined variant', 'material-design-for-contact-form-7' ) ),
			'renderer' => 'toggle'
		);
		$display_attr = array(
			'name'     => 'display',
			'default'  => 'stacked',
			'options'  => array(
				'stacked'   => /* translators: As in vertical */ __( 'Stacked', 'material-design-for-contact-form-7' ),
				'inline'    => /* translators: As in side-by-side */ __( 'Inline', 'material-design-for-contact-form-7' ),
				'columns-2' => '2 ' . __( 'Columns', 'material-design-for-contact-form-7' ),
				'columns-3' => '3 ' . __( 'Columns', 'material-design-for-contact-form-7' ),
				'columns-4' => '4 ' . __( 'Columns', 'material-design-for-contact-form-7' )
			),
			'label'    => /* translators: As in layout */ __( 'Display', 'material-design-for-contact-form-7' ),
			'renderer' => 'display'
		);
		$desktopwidth_attr = array(
			'name'     => 'desktopwidth',
			'default'  => '12',
			'label'    => /* translators: As in laptop */ __( 'Desktop width', 'material-design-for-contact-form-7' ),
			'help'     => __( 'How many columns out of 12 should this element occupy on large screens?', 'material-design-for-contact-form-7' ),
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);
		$tabletwidth_attr = array(
			'name'     => 'tabletwidth',
			'default'  => '8',
			'label'    => /* translators: As in iPad */ __( 'Tablet width', 'material-design-for-contact-form-7' ),
			'help'     => __( 'How many columns out of 8 should this element occupy on tablet-sized screens?', 'material-design-for-contact-form-7' ),
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);
		$mobilewidth_attr = array(
			'name'     => 'mobilewidth',
			'default'  => '4',
			'label'    => __( 'Mobile width', 'material-design-for-contact-form-7' ),
			'help'     => __( 'How many columns out of 4 should this element occupy on mobile-sized screens?', 'material-design-for-contact-form-7' ),
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);

		// Main outer array
		$state = array();

		// Inner shortcodes array
		$shortcodes = array();

		// [md-form]
		$md_form = array(
			'name'        => __( 'Form', 'material-design-for-contact-form-7' ),
			'description' => __( 'Wraps your entire form.', 'material-design-for-contact-form-7' ),
			'type'        => 'md-form',
			'wraps'       => array(),
			'replace'     => __( 'all your other form tags and shortcodes go here', 'material-design-for-contact-form-7' ),
			'attributes'  => array(
				array(
					'name'     => 'theme',
					'default'  => '',
					'label'    => __( 'Theme', 'material-design-for-contact-form-7' ),
					'options'  => array( 'dark' => __( 'Dark', 'material-design-for-contact-form-7' ) ),
					'renderer' => 'toggle',
					'help'     => __( 'The dark theme is useful when your form is on a dark background', 'material-design-for-contact-form-7' )
				),
				array(
					'name'     => 'spacing',
					'default'  => '',
					'label'    => __( 'Spacing', 'material-design-for-contact-form-7' ),
					'options'  => array( 'tight' => /* translators: As in close-together */ __( 'Tight', 'material-design-for-contact-form-7' ) ),
					'renderer' => 'toggle',
					'help'     => __( 'Reduce the vertical spacing between items?', 'material-design-for-contact-form-7' )
				)
			)
		);
		$shortcodes['md-form'] = $md_form;

		// [md-text]
		$md_text = array(
			'name'        => __( 'Text', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
								/* translators: %s: form tag type */
								__( 'Wraps %s, %s, %s, %s, %s and %s form tags.', 'material-design-for-contact-form-7' ),
								'text', 'email', 'url', 'tel', 'number', 'date'
							 ),
			'type'        => 'md-text',
			'attributes'  => array(),
			'wraps'       => array( 'text', 'email', 'url', 'tel', 'number', 'date' )
		);
		array_push( $md_text['attributes'], $label_attr, $help_attr, $icon_attr, $outlined_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-text'] = $md_text;

		// [md-textarea]
		$md_textarea = array(
			'name'        => __( 'Text area', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'textarea'
			 ),
			'type'        => 'md-textarea',
			'attributes'  => array(
				array(
					'name'     => 'autosize',
					'default'  => '1',
					'label'    => __( 'Autosize', 'material-design-for-contact-form-7' ),
					'options'  => array( '1' => __( 'Automatically expand as the user types', 'material-design-for-contact-form-7' ) ),
					'renderer' => 'toggle'
				)
			),
			'wraps'       => array( 'textarea' )
		);
		array_unshift( $md_textarea['attributes'], $label_attr, $help_attr );
		array_push( $md_textarea['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-textarea'] = $md_textarea;

		// [md-select]
		$md_select = array(
			'name'        => __( 'Select', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s and %s form tags.', 'material-design-for-contact-form-7' ),
				'select', 'drop-down menu'
			 ),
			'type'        => 'md-select',
			'attributes'  => array(),
			'wraps'       => array( 'select', 'drop-down menu' )
		);
		array_push( $md_select['attributes'], $label_attr, $help_attr, $icon_attr, $outlined_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-select'] = $md_select;
		
		// [md-checkbox]
		$md_checkbox = array(
			'name'        => __( 'Checkbox', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'checkbox'
			 ),
			'type'        => 'md-checkbox',
			'attributes'  => array(),
			'wraps'       => array( 'checkbox' )
		);
		array_push( $md_checkbox['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-checkbox'] = $md_checkbox;
		
		// [md-radio]
		$md_radio = array(
			'name'        => /* translators: As in radio button form control */ __( 'Radio', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'radio'
			 ),
			'type'        => 'md-radio',
			'attributes'  => array(),
			'wraps'       => array( 'radio' )
		);
		array_push( $md_radio['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-radio'] = $md_radio;

		// [md-switch]
		$md_switch = array(
			'name'        => /* translators: As in switch form control */ __( 'Switch', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Turn %s into switches.', 'material-design-for-contact-form-7' ),
				'checkboxes'
			 ),
			'type'        => 'md-switch',
			'attributes'  => array(),
			'wraps'       => array( 'checkbox' ),
			'locked'      => $cf7md_fs->is_free_plan()
		);
		array_push( $md_switch['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-switch'] = $md_switch;

		// [md-accept]
		$md_accept = array(
			'name'        => __( 'Acceptance', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'acceptance'
			 ),
			'type'        => 'md-accept',
			'attributes'  => array(
				array(
					'name'     => 'terms',
					'default'  => '',
					'label'    => /* translators: As in terms and conditions */ __( 'Terms', 'material-design-for-contact-form-7' ),
					'renderer' => 'text',
					'help'     => __( 'The terms to which the user must agree. NOTE: If the CF7 acceptance tag has content, that content will override this terms attribute.', 'material-design-for-contact-form-7' )
				)
			),
			'wraps'       => array( 'acceptance' )
		);
		$accept_label_attr = $label_attr;
		unset($accept_label_attr['required']);
		array_push( $md_accept['attributes'], $accept_label_attr, $help_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-accept'] = $md_accept;

		// [md-file]
		$md_file = array(
			'name'        => __( /* translators: As in a file on your computer */ 'File', 'material-design-for-contact-form-7' ),
			'description' => __( 'Wraps your file upload form tags.', 'material-design-for-contact-form-7' ),
			'type'        => 'md-file',
			'attributes'  => array(
				array(
					'name'     => 'nofile',
					'default'  => __( 'No file chosen', 'material-design-for-contact-form-7' ),
					'label'    => __( 'No file text', 'material-design-for-contact-form-7' ),
					'renderer' => 'text',
					'help'     => __( 'The text shown before a file is chosen.', 'material-design-for-contact-form-7' )
				),
				array(
					'name'     => 'btn_text',
					'default'  => __( 'Choose file', 'material-design-for-contact-form-7' ),
					'label'    => __( 'Button text', 'material-design-for-contact-form-7' ),
					'renderer' => 'text'
				)
			),
			'wraps'       => array( 'file' )
		);
		array_unshift( $md_file['attributes'], $label_attr );
		array_push( $md_file['attributes'], $help_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-file'] = $md_file;

		// [md-quiz]
		$md_quiz = array(
			'name'        => __( 'Quiz', 'material-design-for-contact-form-7' ),
			'description' => __( 'Wraps your quiz form tags.', 'material-design-for-contact-form-7' ),
			'type'        => 'md-quiz',
			'attributes'  => array(
				array (
					'name'     => 'label',
					'required' => false,
					'default'  => '',
					'label'    => __( 'Label', 'material-design-for-contact-form-7' ),
					'renderer' => 'text',
					'help'     => __( 'This label will appear above the field. The quiz question will appear in the floating label.', 'material-design-for-contact-form-7' )
				)
			),
			'wraps'       => array( 'quiz' )
		);
		array_push( $md_quiz['attributes'], $help_attr, $outlined_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-quiz'] = $md_quiz;
		
		// [md-captcha]
		$md_captcha = array(
			'name'        => __( 'Captcha', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'captcha'
			 ),
			'type'        => 'md-captcha',
			'attributes'  => array(),
			'wraps'       => array( 'captcha' )
		);
		array_push( $md_captcha['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-captcha'] = $md_captcha;

		// [md-submit]
		$md_submit = array(
			'name'        => __( 'Submit', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Wraps your %s form tags.', 'material-design-for-contact-form-7' ),
				'submit'
			 ),
			'type'        => 'md-submit',
			'attributes'  => array(
				array(
					'name'     => 'style',
					'default'  => 'raised',
					'label'    => __( 'Button style', 'material-design-for-contact-form-7' ),
					'renderer' => 'radio',
					'options'  => array(
									'raised' => __( 'Raised', 'material-design-for-contact-form-7' ),
									'unelevated' => __( 'Unelevated', 'material-design-for-contact-form-7' ),
									'outlined' => __( 'Outlined', 'material-design-for-contact-form-7' )
								),
					'help'     => ''
				)
			),
			'wraps'       => array( 'submit', 'previous' ),
			'replace'     => sprintf(
								/* translators: %s: form tag type */
								__( 'one or more %s or %s form tags go here', 'material-design-for-contact-form-7' ),
								'submit', 'previous'
							)
		);
		array_push( $md_submit['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-submit'] = $md_submit;

		// [md-card]
		$md_card = array(
			'name'        => __( 'Card', 'material-design-for-contact-form-7' ),
			'description' => __( 'Group elements into sections with cards.', 'material-design-for-contact-form-7' ),
			'type'        => 'md-card',
			'attributes'  => array(
				array(
					'name'     => 'title',
					'default'  => '',
					'label'    => __( 'Title', 'material-design-for-contact-form-7' ),
					'renderer' => 'text'
				),
				array(
					'name'     => 'subtitle',
					'default'  => '',
					'label'    => __( 'Subtitle', 'material-design-for-contact-form-7' ),
					'renderer' => 'text'
				),
				array(
					'name'     => 'titlesize',
					'default'  => '',
					'label'    => __( 'Title size', 'material-design-for-contact-form-7' ),
					'options'  => array( 'large' => __( 'Larger title', 'material-design-for-contact-form-7' ) ),
					'renderer' => 'toggle'
				)
			),
			'wraps'       => array( '*' ),
			'replace'     => sprintf(
								/* translators: %s: shortcode type */
								__( 'one or more %s shortcodes or other elements go here', 'material-design-for-contact-form-7' ),
								'[md-*]'
							),
			'locked'      => $cf7md_fs->is_free_plan()
		);
		array_push( $md_card['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-card'] = $md_card;

		// [md-raw]
		$md_raw = array(
			'name'        => __( 'Raw', 'material-design-for-contact-form-7' ),
			'description' => __( 'Wraps html or any miscellaneous elements.', 'material-design-for-contact-form-7' ),
			'type'        => 'md-raw',
			'wraps'       => array(),
			'replace'     => __( 'any raw html or miscellaneous elements go here', 'material-design-for-contact-form-7' ),
			'attributes'  => array()
		);
		array_push( $md_raw['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-raw'] = $md_raw;

		// [md-grid]
		$md_grid = array(
			'name'        => __( 'Grid', 'material-design-for-contact-form-7' ),
			'description' => sprintf(
				/* translators: %s: form tag type */
				__( 'Fixes layout for non-immediate children of %s or %s.', 'material-design-for-contact-form-7' ),
				'<code>[md-form]</code>', '<code>[md-card]</code>'
			 ),
			'type'        => 'md-grid',
			'wraps'       => array(),
			'replace'     => sprintf(
								/* translators: %s: form tag type */
								__( '%s shortcodes go here', 'material-design-for-contact-form-7' ),
								'[md-*]'
							),
			'attributes'  => array(
				array(
					'name'     => 'help',
					'label'    => __( 'Help', 'material-design-for-contact-form-7' ),
					'renderer' => 'html',
					'html'     => '<p>Any <code>[md-*]</code> shortcodes that are not direct descendants of <code>[md-form]</code> or <code>[md-card]</code> (E.g. if they are nested inside a <code>&lt;div></code> or another shortcode) need to be wrapped in <code>[md-grid][/md-grid]</code> to be displayed correctly. A single <code>[md-grid][/md-grid]</code> should be the only direct descendant of the <code>&lt;div></code> (or whatever the wrapper is, e.g. a shortcode), and the other elements should be direct descendants of the <code>[md-grid]</code>.</p>'
				)
			)
		);
		$shortcodes['md-grid'] = $md_grid;

		// Finish building state array and return
		$state['shortcodes'] = $shortcodes;
		return $state;
	}
	

	/**
	 * Render a html attribute
	 */
	public static function render_html_field( $field, $index ) {
		?>
		<div class="cf7md-item cf7md-html">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<?php echo $field['html']; ?>
		</div>
		<?php
	}


	/**
	 * Render an attribute as a text field
	 */
	public static function render_text_field( $field, $index ) {
		global $cf7md_fs;
		$upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
		$locked = isset($field['locked']) && $field['locked'];
		$label = 'mdc-floating-label';
		if( $field['default'] !== '' ) {
			$label .= ' mdc-floating-label--float-above';
		}
		$help = isset( $field['help'] ) ? $field['help'] : '';
		$help = $locked ? '<a href="' . $upgrade_url . '">' . __( 'Upgrade to pro', 'material-design-for-contact-form-7' ) . '</a> ' . __( 'to use this feature.', 'material-design-for-contact-form-7' ) : $help;
		$modifiers = '';
		$modifiers .= $locked ? ' mdc-text-field--disabled' : '';
		$modifiers .= isset( $field['icon'] ) ? ' mdc-text-field--with-leading-icon' : '';
		?>
		<div class="cf7md-item">
			<div class="mdc-text-field <?php echo $modifiers; ?>">
				<?php if( isset( $field['icon'] ) ) : ?>
					<i class="material-icons mdc-text-field__icon"><?php echo $field['icon']; ?></i>
				<?php endif; ?>
				<input type="text" id="cf7md-<?php echo esc_attr($field['name']); ?>" class="mdc-text-field__input" <?php echo $locked ? 'disabled' : ''; ?> data-attr-index="<?php echo esc_attr($index); ?>" autocomplete="off" <?php echo (isset($field['required']) && $field['required']) ? 'required' : ''; ?>>
				<label class="<?php echo $label; ?>" for="cf7md-<?php echo esc_attr($field['name']); ?>"><?php echo $field['label']; ?></label>
				<div class="mdc-line-ripple"></div>
			</div>
			<?php if( $help ) : ?>
				<p class="cf7md-help-text"><?php echo $help; ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
	

	/**
	 * Render an attribute as a toggle field
	 */
	public static function render_toggle_field( $field, $index, $id ) {
		?>

		<div class="cf7md-item mdc-layout-grid__cell">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<?php foreach( $field['options'] as $value => $label ) : ?>
				<span class="cf7md-switch-item cf">
					<div class="mdc-form-field">
						<div class="mdc-switch <?php echo $value == $field['default'] ? 'mdc-switch--checked' : ''; ?>">
							<div class="mdc-switch__track"></div>
							<div class="mdc-switch__thumb-underlay">
								<div class="mdc-switch__thumb">
									<input type="checkbox" value="<?php echo esc_attr($value); ?>" class="mdc-switch__native-control" id="cf7md-<?php echo $id; ?>" data-attr-index="<?php echo esc_attr($index); ?>" <?php echo $value == $field['default'] ? 'checked' : ''; ?>>
								</div>
							</div>
						</div>	
						<label for="cf7md-<?php echo $id; ?>" class="cf7md-switch-label"><?php echo $label; ?></label>
					</div>
				</span>
			<?php endforeach; ?>
			<?php if( isset( $field['help'] ) ) : ?>
				<p class="cf7md-help-text"><?php echo $field['help']; ?></p>
			<?php endif; ?>
		</div>

		<?php
	}


	/**
	 * Render an attribute as a radio field
	 */
	public static function render_radio_field( $field, $index, $id ) {
		?>

		<div class="cf7md-item mdc-layout-grid__cell">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<div class="wpcf7-form-control-wrap cf7md-list cf7md-list__inline">
				<?php foreach( $field['options'] as $value => $label ) : ?>
					<span class="cf7md-radio-item mdc-form-field wpcf7-list-item">
						<div class="mdc-radio">
							<input type="radio" name="<?php echo 'cf7md-' . $id . '-' . esc_attr($field['name']); ?>" value="<?php echo esc_attr($value); ?>" class="mdc-radio__native-control" id="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" data-attr-index="<?php echo esc_attr($index); ?>">
							<div class="mdc-radio__background">
								<div class="mdc-radio__outer-circle"></div>
								<div class="mdc-radio__inner-circle"></div>
							</div>
						</div>
						<label for="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" class="cf7md-radio-label"><?php echo $label; ?></label>
					</span>
				<?php endforeach; ?>
			</div>
			<?php if( isset( $field['help'] ) ) : ?>
				<p class="cf7md-help-text"><?php echo $field['help']; ?></p>
			<?php endif; ?>
		</div>

		<?php
	}


	/**
	 * Renders the display attribute
	 */
	public static function render_display_field( $field, $index, $id ) {
		?>

		<div class="cf7md-item cf7md-display">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<div class="cf7md-display-field">
				<?php foreach( $field['options'] as $value => $label ) : ?>
					<div class="cf7md-display-field--option">
						<input type="radio" name="<?php echo 'cf7md-' . $id . '-' . esc_attr($field['name']); ?>" value="<?php echo esc_attr($value); ?>" class="cf7md-display-field--radio" id="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" data-attr-index="<?php echo esc_attr($index); ?>">
						<label for="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" class="cf7md-display-field--label">
							<?php echo '<img src="' . CF7MD_PLUGIN_DIR . 'assets/images/display-' . esc_attr($value) . '.png" alt="' . esc_attr( $label) . '">'; ?>
							<span><?php echo $label; ?></span>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if( isset( $field['help'] ) ) : ?>
				<p class="cf7md-help-text"><?php echo $field['help']; ?></p>
			<?php endif; ?>
		</div>

		<?php
	}
	

	/**
	 * Render layout fields
	 */
	public static function render_layout_field( $field, $index ) {
		global $cf7md_fs;
		$upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
		$locked = isset($field['locked']) && $field['locked'];
		$max = 12;
		$sizename = 'large';
		switch( $field['name'] ) {
			case 'mobilewidth':
				$max = 4;
				$sizename = /* translators: mobile as in cell phone */ __( 'mobile-sized', 'material-design-for-contact-form-7' );
				break;
			case 'tabletwidth':
				$max = 8;
				$sizename = /* translators: tablet as in ipad */ __( 'tablet-sized', 'material-design-for-contact-form-7' );
				break;
			case 'desktopwidth':
			default:
				$max = 12;
				$sizename = __( 'large', 'material-design-for-contact-form-7' );
				break;
		}
		?>

		<?php if( $field['name'] === 'desktopwidth' ) : ?>
			<div class="cf7md-scg--layout-field-group">
				<h4 class="cf7md-scg--field-group-title"><?php _e( 'Layout Attributes', 'material-design-for-contact-form-7' ); ?></h4>
				<p><?php _e( 'Layout attributes let you organize your form fields into columns. You can control how wide each field is on mobile, tablet and large screens.', 'material-design-for-contact-form-7' ); ?></p>
				<div class="cf7md-scg--layout-fields <?php echo $locked ? 'cf7md-scg--layout-fields__locked' : ''; ?>">
					<?php if( $locked ) : ?>
						<div class="cf7md-scg--layout-fields-overlay-content">
							<p><?php _e( 'Layout attributes are a pro feature.', 'material-design-for-contact-form-7' ); ?></p>
							<a class="mdc-button mdc-button--primary mdc-button--raised" data-mdc-auto-init="MDCRipple" href="<?php echo $upgrade_url; ?>"><?php _e( 'Upgrade Now', 'material-design-for-contact-form-7' ); ?></a>
						</div>
					<?php endif; ?>
		<?php endif; ?>

		<div class="cf7md-item cf7md-layout">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<div class="cf7md-layout-slider" data-max="<?php echo esc_attr( $max ); ?>"></div>
			<input type="number" style="display: none;" class="cf7md-layout-slider-input" data-attr-index="<?php echo esc_attr($index); ?>">
			<p class="cf7md-help-text">
				<?php printf(
					/* translators: %1$s: "all" or a fraction like "half", %2$s: screen size (mobile, tablet, desktop) */
					__( 'This element will occupy %1$s of the available horizontal space on %2$s screens.', 'material-design-for-contact-form-7' ),
					'<span class="cf7md-layout-slider-value">all</span>',
					$sizename
				); ?>
			</p>
		</div>

		<?php if( $field['name'] === 'mobilewidth' ) : ?>
			</div></div>
		<?php endif; ?>

		<?php
	}
	
}
