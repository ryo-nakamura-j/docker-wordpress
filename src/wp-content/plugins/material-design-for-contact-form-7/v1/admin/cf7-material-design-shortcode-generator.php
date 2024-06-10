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
			'label'    => 'Label',
			'renderer' => 'text'
		);
		$help_attr = array(
			'name'     => 'help',
			'default'  => '',
			'label'    => 'Help text',
			'help'     => 'This will appear below the field',
			'renderer' => 'text'
		);
		$display_attr = array(
			'name'     => 'display',
			'default'  => 'stacked',
			'options'  => array(
				'stacked'   => 'Stacked',
				'inline'    => 'Inline',
				'columns-2' => '2 Columns',
				'columns-3' => '3 Columns',
				'columns-4' => '4 Columns'
			),
			'label'    => 'Display',
			'renderer' => 'display'
		);
		$desktopwidth_attr = array(
			'name'     => 'desktopwidth',
			'default'  => '12',
			'label'    => 'Desktop width',
			'help'     => 'How many columns out of 12 should this element occupy on large screens?',
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);
		$tabletwidth_attr = array(
			'name'     => 'tabletwidth',
			'default'  => '8',
			'label'    => 'Tablet width',
			'help'     => 'How many columns out of 8 should this element occupy on tablet-sized screens?',
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);
		$mobilewidth_attr = array(
			'name'     => 'mobilewidth',
			'default'  => '4',
			'label'    => 'Mobile width',
			'help'     => 'How many columns out of 4 should this element occupy on mobile-sized screens?',
			'renderer' => 'layout',
			'locked'   => $cf7md_fs->is_free_plan()
		);

		// Main outer array
		$state = array();

		// Inner shortcodes array
		$shortcodes = array();

		// [md-form]
		$md_form = array(
			'name'        => 'Form',
			'description' => 'Wraps your entire form.',
			'type'        => 'md-form',
			'wraps'       => array(),
			'replace'     => 'all your other form tags and shortcodes go here',
			'attributes'  => array(
				array(
					'name'     => 'theme',
					'default'  => '',
					'label'    => 'Theme',
					'options'  => array( 'dark' => 'Dark' ),
					'renderer' => 'toggle',
					'help'     => 'The dark theme is useful when your form is on a dark background'
				),
				array(
					'name'     => 'spacing',
					'default'  => '',
					'label'    => 'Spacing',
					'options'  => array( 'tight' => 'Tight' ),
					'renderer' => 'toggle',
					'help'     => 'Reduce the vertical spacing between items?'
				)
			)
		);
		$shortcodes['md-form'] = $md_form;

		// [md-text]
		$md_text = array(
			'name'        => 'Text',
			'description' => 'Wraps text, email, url, tel, number and date form tags.',
			'type'        => 'md-text',
			'attributes'  => array(),
			'wraps'       => array( 'text', 'email', 'url', 'tel', 'number', 'date' )
		);
		array_push( $md_text['attributes'], $label_attr, $help_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-text'] = $md_text;

		// [md-textarea]
		$md_textarea = array(
			'name'        => 'Text area',
			'description' => 'Wraps your textarea form tags.',
			'type'        => 'md-textarea',
			'attributes'  => array(
				array(
					'name'     => 'autosize',
					'default'  => '1',
					'label'    => 'Autosize',
					'options'  => array( '1' => 'Automatically expand as the user types' ),
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
			'name'        => 'Select',
			'description' => 'Wraps your select and drop-down menu form tags.',
			'type'        => 'md-select',
			'attributes'  => array(),
			'wraps'       => array( 'select', 'drop-down menu' )
		);
		array_push( $md_select['attributes'], $label_attr, $help_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-select'] = $md_select;
		
		// [md-checkbox]
		$md_checkbox = array(
			'name'        => 'Checkbox',
			'description' => 'Wraps your checkbox form tags.',
			'type'        => 'md-checkbox',
			'attributes'  => array(),
			'wraps'       => array( 'checkbox' )
		);
		array_push( $md_checkbox['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-checkbox'] = $md_checkbox;
		
		// [md-radio]
		$md_radio = array(
			'name'        => 'Radio',
			'description' => 'Wraps your radio form tags.',
			'type'        => 'md-radio',
			'attributes'  => array(),
			'wraps'       => array( 'radio' )
		);
		array_push( $md_radio['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-radio'] = $md_radio;

		// [md-switch]
		$md_switch = array(
			'name'        => 'Switch',
			'description' => 'Turn checkboxes and radios into switches.',
			'type'        => 'md-switch',
			'attributes'  => array(),
			'wraps'       => array( 'checkbox', 'radio' ),
			'locked'      => $cf7md_fs->is_free_plan()
		);
		array_push( $md_switch['attributes'], $label_attr, $help_attr, $display_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-switch'] = $md_switch;

		// [md-accept]
		$md_accept = array(
			'name'        => 'Acceptance',
			'description' => 'Wraps your acceptance form tags.',
			'type'        => 'md-accept',
			'attributes'  => array(
				array(
					'name'     => 'terms',
					'default'  => '',
					'label'    => 'Terms',
					'renderer' => 'text',
					'help'     => 'The terms to which the user must agree. NOTE: If the CF7 acceptance tag has content, that content will override this terms attribute.'
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
			'name'        => 'File',
			'description' => 'Wraps your file upload form tags.',
			'type'        => 'md-file',
			'attributes'  => array(
				array(
					'name'     => 'nofile',
					'default'  => 'No file chosen',
					'label'    => 'No file text',
					'renderer' => 'text',
					'help'     => 'The text shown before a file is chosen.'
				),
				array(
					'name'     => 'btn_text',
					'default'  => 'Choose file',
					'label'    => 'Button text',
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
			'name'        => 'Quiz',
			'description' => 'Wraps your quiz form tags.',
			'type'        => 'md-quiz',
			'attributes'  => array(),
			'wraps'       => array( 'quiz' )
		);
		array_push( $md_quiz['attributes'], $label_attr, $help_attr, $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-quiz'] = $md_quiz;
		
		// [md-captcha]
		$md_captcha = array(
			'name'        => 'Captcha',
			'description' => 'Wraps your captcha form tags.',
			'type'        => 'md-captcha',
			'attributes'  => array(),
			'wraps'       => array( 'captcha' )
		);
		array_push( $md_captcha['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-captcha'] = $md_captcha;

		// [md-submit]
		$md_submit = array(
			'name'        => 'Submit',
			'description' => 'Wraps your submit button form tags.',
			'type'        => 'md-submit',
			'attributes'  => array(),
			'wraps'       => array( 'submit', 'previous' ),
			'replace'     => 'one or more submit or previous form tags go here'
		);
		array_push( $md_submit['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-submit'] = $md_submit;

		// [md-card]
		$md_card = array(
			'name'        => 'Card',
			'description' => 'Group elements into sections with cards.',
			'type'        => 'md-card',
			'attributes'  => array(
				array(
					'name'     => 'title',
					'default'  => '',
					'label'    => 'Title',
					'renderer' => 'text'
				),
				array(
					'name'     => 'subtitle',
					'default'  => '',
					'label'    => 'Subtitle',
					'renderer' => 'text'
				),
				array(
					'name'     => 'titlesize',
					'default'  => '',
					'label'    => 'Title size',
					'options'  => array( 'large' => 'Larger title' ),
					'renderer' => 'toggle'
				)
			),
			'wraps'       => array( '*' ),
			'replace'     => 'one or more [md-*] shortcodes or other elements go here',
			'locked'      => $cf7md_fs->is_free_plan()
		);
		array_push( $md_card['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-card'] = $md_card;

		// [md-raw]
		$md_raw = array(
			'name'        => 'Raw',
			'description' => 'Wraps html or any miscellaneous elements.',
			'type'        => 'md-raw',
			'wraps'       => array(),
			'replace'     => 'any raw html or miscellaneous elements go here',
			'attributes'  => array()
		);
		array_push( $md_raw['attributes'], $desktopwidth_attr, $tabletwidth_attr, $mobilewidth_attr );
		$shortcodes['md-raw'] = $md_raw;

		// [md-grid]
		$md_grid = array(
			'name'        => 'Grid',
			'description' => 'Fixes layout for non-immediate children of <code>[md-form]</code> or <code>[md-card]</code>.',
			'type'        => 'md-grid',
			'wraps'       => array(),
			'replace'     => '[md-*] shortcodes go here',
			'attributes'  => array(
				array(
					'name'     => 'help',
					'label'    => 'Help',
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
		$label = 'mdc-textfield__label';
		if( $field['default'] !== '' ) {
			$label .= ' mdc-textfield__label--float-above';
		}
		?>
		<div class="cf7md-item cf7md-text">
			<div class="mdc-textfield" data-mdc-auto-init="MDCTextfield">
				<input type="text" id="<?php echo esc_attr($field['name']); ?>" class="mdc-textfield__input" data-attr-index="<?php echo esc_attr($index); ?>" autocomplete="off" <?php echo isset($field['required']) ? 'required' : ''; ?>>
				<label class="<?php echo $label; ?>" for="<?php echo esc_attr($field['name']); ?>"><?php echo $field['label']; ?></label>
			</div>
			<?php if( isset( $field['help'] ) ) : ?>
				<p class="cf7md-help-text"><?php echo $field['help']; ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
	

	/**
	 * Render an attribute as a toggle field
	 */
	public static function render_toggle_field( $field, $index, $id ) {
		?>

		<div class="cf7md-item cf7md-switch mdc-layout-grid__cell">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<?php foreach( $field['options'] as $value => $label ) : ?>
				<span class="cf7md-switch-item cf">
					<div class="mdc-form-field">
						<div class="mdc-switch">
							<input type="checkbox" value="<?php echo esc_attr($value); ?>" class="mdc-switch__native-control" id="cf7md-<?php echo $id; ?>" data-attr-index="<?php echo esc_attr($index); ?>">
							<div class="mdc-switch__background">
								<div class="mdc-switch__knob"></div>
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

		<div class="cf7md-item cf7md-radio mdc-layout-grid__cell">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<div class="wpcf7-form-control-wrap">
				<?php foreach( $field['options'] as $value => $label ) : ?>
					<span class="cf7md-radio-item cf">
						<div class="mdc-form-field">
							<div class="mdc-radio">
								<input type="radio" name="<?php echo 'cf7md-' . $id . '-' . esc_attr($field['name']); ?>" value="<?php echo esc_attr($value); ?>" class="mdc-radio__native-control" id="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" data-attr-index="<?php echo esc_attr($index); ?>">
								<div class="mdc-radio__background">
									<div class="mdc-radio__outer-circle"></div>
									<div class="mdc-radio__inner-circle"></div>
								</div>
							</div>
							<label for="cf7md-<?php echo $id . '-' . esc_attr($value); ?>" class="cf7md-radio-label"><?php echo $label; ?></label>
						</div>
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
				$sizename = 'mobile-sized';
				break;
			case 'tabletwidth':
				$max = 8;
				$sizename = 'tablet-sized';
				break;
			case 'desktopwidth':
			default:
				$max = 12;
				$sizename = 'large';
				break;
		}
		?>

		<?php if( $field['name'] === 'desktopwidth' ) : ?>
			<div class="cf7md-scg--layout-field-group">
				<h4 class="cf7md-scg--field-group-title">Layout Attributes</h4>
				<p>Layout attributes let you organise your form fields into columns. You can control how wide each field is on mobile, tablet and large screens.</p>
				<div class="cf7md-scg--layout-fields <?php echo $locked ? 'cf7md-scg--layout-fields__locked' : ''; ?>">
					<?php if( $locked ) : ?>
						<div class="cf7md-scg--layout-fields-overlay-content">
							<p>Layout attributes are a pro feature.</p>
							<a class="mdc-button mdc-button--primary mdc-button--raised" data-mdc-auto-init="MDCRipple" href="<?php echo $upgrade_url; ?>">Upgrade Now</a>
						</div>
					<?php endif; ?>
		<?php endif; ?>

		<div class="cf7md-item cf7md-layout">
			<label class="cf7md-label cf7md-label--static"><?php echo $field['label']; ?></label>
			<div class="cf7md-layout-slider" data-max="<?php echo esc_attr( $max ); ?>"></div>
			<input type="number" style="display: none;" class="cf7md-layout-slider-input" data-attr-index="<?php echo esc_attr($index); ?>">
			<p class="cf7md-help-text">This element will occupy <span class="cf7md-layout-slider-value">all</span> of the available horizontal space on <?php echo $sizename; ?> screens.</p>
		</div>

		<?php if( $field['name'] === 'mobilewidth' ) : ?>
			</div></div>
		<?php endif; ?>

		<?php
	}
	
}
