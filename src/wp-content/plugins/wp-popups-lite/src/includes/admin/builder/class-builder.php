<?php

/**
 * Popup builder that contains magic.
 *
 * @package    WPPopups
 * @author     WPPopup
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC <- THanks!!
 */
class WPPopups_Builder {

	/**
	 * One is the loneliest number that you'll ever do.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Current view (panel).
	 *
	 * @var string
	 */
	public $view;

	/**
	 * Available panels.
	 *
	 * @var array
	 */
	public $panels;

	/**
	 * Current popup.
	 *
	 * @var object
	 */
	public $popup;

	/**
	 * Current popup data.
	 *
	 * @var array
	 */
	public $popup_data;

	/**
	 * Current template information.
	 *
	 * @var array
	 */
	public $template;

	/**
	 * Main Instance.
	 *
	 * @return WPPopups_Builder
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPopups_Builder ) ) {

			self::$instance = new WPPopups_Builder();

			add_action( 'admin_init', [ self::$instance, 'init' ], 10 );
		}

		return self::$instance;
	}

	/**
	 * Determine if the user is viewing the builder, if so, party on.
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

		// Only load if we are actually on the builder.
		if ( 'wppopups-builder' === $page ) {

			// Load popup if found.
			$popup_id = isset( $_GET['popup_id'] ) ? absint( $_GET['popup_id'] ) : false;

			if ( $popup_id ) {
				// Default view for with an existing popup is fields panel.
				$this->view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'content';
			} else {
				// Default view for new field is the setup panel.
				$this->view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'setup';
			}

			// Preview page check.
			wppopups()->preview->popup_preview_check();

			// Fetch popup.
			$this->popup      = wppopups()->popups->get( $popup_id );

			$this->popup_data = $this->popup ? $this->popup->data : false;

			// Fetch template information.
			$this->template = apply_filters( 'wppopups_builder_template_active', [], $this->popup );

			// Load builder panels.
			$this->load_panels();

			add_action( 'admin_head', [ $this, 'admin_head' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
			add_action( 'admin_print_footer_scripts', [ $this, 'footer_scripts' ] );
			add_action( 'wppopups_admin_page', [ $this, 'output' ] );

			// Provide hook for addons.
			do_action( 'wppopups_builder_init', $this->view );

			add_filter( 'teeny_mce_plugins', [ $this, 'tinymce_buttons' ] );
			add_filter( 'mce_buttons_2', [ $this, 'font_buttons' ] );
		}
	}

	/**
	 * Define TinyMCE buttons to use with our fancy editor instances.
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public function tinymce_buttons( $buttons ) {

		$buttons = [ 'colorpicker', 'lists', 'wordpress', 'wpeditimage', 'wplink' ];

		return $buttons;
	}

	/**
	 * @return mixed
	 * @param array $buttons
	 */
	public function font_buttons( $buttons ) {
			array_unshift( $buttons, 'fontsizeselect' );
			array_unshift( $buttons, 'fontselect' );
			return $buttons;
	}

	/**
	 * Load panels.
	 */
	public function load_panels() {

		// Base class and functions.
		require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/builder/panels/class-base.php';

		$this->panels = apply_filters(
			'wppopups_builder_panels', [
				'setup',
				'content',
				'appearance',
				'rules',
				'settings',
				'fields',
				'optin',
				'addons',
				'providers',
			]
		);

		foreach ( $this->panels as $panel ) {
			$panel = sanitize_file_name( $panel );

			if ( file_exists( WPPOPUPS_PLUGIN_DIR . 'includes/admin/builder/panels/class-' . $panel . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/builder/panels/class-' . $panel . '.php';
			} elseif ( defined( 'WPPOPUPS_PLUGIN_PRO_DIR' ) && file_exists( WPPOPUPS_PLUGIN_PRO_DIR . 'pro/includes/admin/builder/panels/class-' . $panel . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_PRO_DIR . 'pro/includes/admin/builder/panels/class-' . $panel . '.php';
			}
		}
	}

	/**
	 * Admin head area inside the popup builder.
	 */
	public function admin_head() {
		do_action( 'wppopups_builder_admin_head', $this->view );
	}

	/**
	 * Enqueue assets for the builder.
	 */
	public function enqueues() {

		// Remove conflicting scripts.
		wp_deregister_script( 'serialize-object' );
		wp_deregister_script( 'wpclef-ajax-settings' );
		// engrave theme
		wp_deregister_script( 'confirm' );

		do_action( 'wppopups_builder_enqueues_before', $this->view );

		$detect = new Mobile_Detect;

		if( $detect->isMobile() ) {
			$admin_builder = 'admin-builder-mobile.css';
		} else {
			$admin_builder = 'admin-builder.css';
		}

		/*
		 * CSS.
		 */

		wp_enqueue_style(
			'wppopups-font-awesome',
			WPPOPUPS_PLUGIN_URL . 'assets/css/font-awesome.min.css',
			null,
			'4.4.0'
		);

		wp_enqueue_style(
			'tooltipster',
			WPPOPUPS_PLUGIN_URL . 'assets/css/tooltipster.css',
			null,
			'3.3.0'
		);

		wp_enqueue_style(
			'jquery-confirm',
			WPPOPUPS_PLUGIN_URL . 'assets/css/jquery-confirm.min.css',
			null,
			'3.3.2'
		);

		wp_enqueue_style(
			'spectrum',
			WPPOPUPS_PLUGIN_URL . 'assets/css/spectrum.min.css',
			null,
			'2.3.1'
		);

		wp_enqueue_style(
			'wppopups-builder-legacy',
			WPPOPUPS_PLUGIN_URL . 'assets/css/'.$admin_builder,
			null,
			WPPOPUPS_VERSION
		);

		wp_enqueue_style(
			'wppopups-builder',
			WPPOPUPS_PLUGIN_URL . 'assets/css/builder.css',
			null,
			WPPOPUPS_VERSION
		);

		wp_enqueue_style(
			'wppopups-choices',
			WPPOPUPS_PLUGIN_URL . 'assets/css/choices.min.css',
			null,
			WPPOPUPS_VERSION
		);

		wp_enqueue_style(
			'wppopups-base',
			WPPOPUPS_PLUGIN_URL . 'assets/css/wppopups-base.css',
			[],
			WPPOPUPS_VERSION
		);

		/*
		 * JavaScript.
		 */
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'wp-util' );

		wp_enqueue_script(
			'tooltipster',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.tooltipster.min.js',
			[ 'jquery' ],
			'3.3.0'
		);

		wp_enqueue_script(
			'jquery-confirm',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.jquery-confirm.min.js',
			[ 'jquery' ],
			'3.3.2'
		);

		wp_enqueue_script(
			'matchheight',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.matchHeight-min.js',
			[ 'jquery' ],
			'0.7.0'
		);

		wp_enqueue_script(
			'insert-at-caret',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.insert-at-caret.min.js',
			array( 'jquery' ),
			'1.1.4'
		);

		wp_enqueue_script(
			'spectrum',
			WPPOPUPS_PLUGIN_URL . 'assets/js/spectrum.min.js',
			[ 'jquery' ],
			'2.3.1'
		);

		wp_enqueue_script(
			'ace-editor',
			WPPOPUPS_PLUGIN_URL . 'assets/js/ace.js',
			[ 'jquery' ],
			'2.3.1'
		);

		wp_enqueue_script(
			'ace-editor-css',
			WPPOPUPS_PLUGIN_URL . 'assets/js/mode-css.js',
			[ 'jquery', 'ace-editor' ],
			'2.3.1'
		);

		wp_enqueue_script(
			'ace-editor-worker',
			WPPOPUPS_PLUGIN_URL . 'assets/js/worker-css.js',
			[ 'jquery', 'ace-editor' ],
			'2.3.1'
		);

		wp_enqueue_script(
			'choices',
			WPPOPUPS_PLUGIN_URL . 'assets/js/choices.min.js',
			[ 'jquery' ],
			'3.0.2'
		);

		wp_enqueue_script(
			'listjs',
			WPPOPUPS_PLUGIN_URL . 'assets/js/list.min.js',
			[ 'jquery' ],
			'1.5.0'
		);

		wppopups_wp_hooks();

		wp_enqueue_script(
			'wppopups-utils',
			WPPOPUPS_PLUGIN_URL . 'assets/js/admin-utils.js',
			[ 'jquery', 'wp-hooks' ],
			WPPOPUPS_VERSION
		);

		$es6 = defined( 'WPP_DEBUG' ) || isset( $_GET['WPP_DEBUG'] ) ? 'es6/' : '';
		
		wp_enqueue_script(
			'wppopups-builder',
			WPPOPUPS_PLUGIN_URL . 'assets/js/' . $es6 . 'admin-builder.js',
			[ 'wppopups-utils', 'tooltipster', 'jquery-confirm', 'jquery-ui-sortable', 'jquery-ui-draggable' ],
			WPPOPUPS_VERSION
		);

		$strings = [
			'and'                    => esc_html__( 'AND', 'wp-popups-lite' ),
			'bulk_add_button'                => esc_html__( 'Add New Choices', 'wp-popups-lite' ),
			'bulk_add_show'                  => esc_html__( 'Bulk Add', 'wp-popups-lite' ),
			'are_you_sure_to_close'          => esc_html__( 'Are you sure you want to leave? You have unsaved changes', 'wp-popups-lite' ),
			'bulk_add_hide'                  => esc_html__( 'Hide Bulk Add', 'wp-popups-lite' ),
			'bulk_add_heading'               => esc_html__( 'Add Choices (one per line)', 'wp-popups-lite' ),
			'bulk_add_placeholder'           => esc_html__( "Blue\nRed\nGreen", 'wp-popups-lite' ),
			'bulk_add_presets_show'          => esc_html__( 'Show presets', 'wp-popups-lite' ),
			'bulk_add_presets_hide'          => esc_html__( 'Hide presets', 'wp-popups-lite' ),
			'ajax_url'               => admin_url( 'admin-ajax.php' ),
			'debug'                  => wppopups_debug(),
			'cancel'                 => esc_html__( 'Cancel', 'wp-popups-lite' ),
			'ok'                     => esc_html__( 'OK', 'wp-popups-lite' ),
			'close'                  => esc_html__( 'Close', 'wp-popups-lite' ),
			'field'                          => esc_html__( 'Field', 'wp-popups-lite' ),
			'field_locked'                   => esc_html__( 'Field Locked', 'wp-popups-lite' ),
			'field_locked_msg'               => esc_html__( 'This field cannot be deleted or duplicated.', 'wp-popups-lite' ),
			'fields_available'               => esc_html__( 'Available Fields', 'wp-popups-lite' ),
			'fields_unavailable'             => esc_html__( 'No fields available', 'wp-popups-lite' ),
			'heads_up'               => esc_html__( 'Heads up!', 'wp-popups-lite' ),
			'image_placeholder'      => WPPOPUPS_PLUGIN_URL . 'assets/images/placeholder-200x125.png',
			'image_background'       => WPPOPUPS_PLUGIN_URL . 'assets/images/popup-builder-bg.jpg',
			'nonce'                  => wp_create_nonce( 'wppopups-builder' ),
			'save'                   => esc_html__( 'Save', 'wp-popups-lite' ),
			'saving'                 => esc_html__( 'Saving ...', 'wp-popups-lite' ),
			'saved'                  => esc_html__( 'Saved!', 'wp-popups-lite' ),
			'save_exit'              => esc_html__( 'Save and Exit', 'wp-popups-lite' ),
			'saved_state'            => '',
			'layout_selector_show'		=> esc_html__( 'Show Layouts', 'wp-popups-lite' ),
			'layout_selector_hide'		=> esc_html__( 'Hide Layouts', 'wp-popups-lite' ),
			'layout_selector_layout'	=> esc_html__( 'Select your layout', 'wp-popups-lite' ),
			'layout_selector_column'	=> esc_html__( 'Select your column', 'wp-popups-lite' ),
			'loading'                => esc_html__( 'Loading', 'wp-popups-lite' ),
			'template_name'          => ! empty( $this->template['name'] ) ? $this->template['name'] : '',
			'template_slug'          => ! empty( $this->template['slug'] ) ? $this->template['slug'] : '',
			'template_modal_title'   => ! empty( $this->template['modal']['title'] ) ? $this->template['modal']['title'] : '',
			'template_modal_msg'     => ! empty( $this->template['modal']['message'] ) ? $this->template['modal']['message'] : '',
			'template_modal_display' => ! empty( $this->template['modal_display'] ) ? $this->template['modal_display'] : '',
			'template_select'        => esc_html__( 'Use Template', 'wp-popups-lite' ),
			'template_confirm'       => esc_html__( 'Changing templates on an existing popup will DELETE popup content. Are you sure you want apply the new template?', 'wp-popups-lite' ),
			'exit'                   => esc_html__( 'Exit', 'wp-popups-lite' ),
			'exit_url'               => admin_url( 'admin.php?page=wppopups-overview' ),
			'exit_confirm'           => esc_html__( 'If you exit without saving, your changes will be lost.', 'wp-popups-lite' ),
			'delete_confirm'                 => esc_html__( 'Are you sure you want to delete this field?', 'wp-popups-lite' ),
			'duplicate_confirm'              => esc_html__( 'Are you sure you want to duplicate this field?', 'wp-popups-lite' ),
			'duplicate_copy'         => esc_html__( '(copy)', 'wp-popups-lite' ),
			'error_title'            => esc_html__( 'Please enter a popup name.', 'wp-popups-lite' ),
			'error_choice'           => esc_html__( 'This item must contain at least one choice.', 'wp-popups-lite' ),
			'off'                    => esc_html__( 'Off', 'wp-popups-lite' ),
			'on'                     => esc_html__( 'On', 'wp-popups-lite' ),
			'or'                     => esc_html__( 'or', 'wp-popups-lite' ),
			'other'                  => esc_html__( 'Other', 'wp-popups-lite' ),
			'previous'               => esc_html__( 'Previous', 'wp-popups-lite' ),
			'upload_image_title'     => esc_html__( 'Upload or Choose Your Image', 'wp-popups-lite' ),
			'upload_image_button'    => esc_html__( 'Use Image', 'wp-popups-lite' ),
			'upload_image_remove'    => esc_html__( 'Remove Image', 'wp-popups-lite' ),
			'xhr_failed'             => esc_html__( 'AJAX request failed, check if you still logged in or refresh the page.', 'wp-popups-lite' ),
			'is_mobile'				=> $detect->isMobile(),
		];
		$strings = apply_filters( 'wppopups_builder_strings', $strings, $this->popup );

		if ( ! empty( $_GET['popup_id'] ) ) {
			$strings['preview_url'] = add_query_arg(
				[
					'new_window' => 1,
				],
				wppopups()->preview->popup_preview_url( absint( $_GET['popup_id'] ) )
			);
			$strings['entries_url'] = esc_url_raw( admin_url( 'admin.php?page=wppopups-entries&view=list&popup_id=' . absint( $_GET['popup_id'] ) ) );
		}

		wp_localize_script(
			'wppopups-builder',
			'wppopups_builder_vars',
			$strings
		);

		wp_localize_script(
			'wppopups-builder',
			'wppopups_builder_providers',
			[
				'url'                => esc_url( add_query_arg( [ 'view' => '' ] ) ),
				'confirm_save'       => esc_html__( 'We need to save your progress to continue. Is that OK?', 'wppopups-pro' ),
				'confirm_connection' => esc_html__( 'Are you sure you want to delete this connection?', 'wppopups-pro' ),
				'prompt_connection'  => esc_html__( 'Enter a %type% nickname', 'wppopups-pro' ),
				'prompt_placeholder' => esc_html__( 'Eg: Newsletter Optin', 'wppopups-pro' ),
				'error_name'         => esc_html__( 'You must provide a connection nickname', 'wppopups-pro' ),
				'required_field'     => esc_html__( 'Field required', 'wppopups-pro' ),
			]
		);

		wp_localize_script(
			'wppopups-builder',
			'wppopups_builder_addons',
			[
				'url' => esc_url( add_query_arg( [ 'view' => 'addons' ] ) ),
			]
		);


		// Hook for addons.
		do_action( 'wppopups_builder_enqueues', $this->view );
	}

	/**
	 * Footer JavaScript.
	 */
	public function footer_scripts() {
		do_action( 'wppopups_builder_print_footer_scripts' );
	}

	/**
	 * Load the appropriate files to build the page.
	 */
	public function output() {

		$field_id = ! empty( $this->popup_data['field_id'] ) ? $this->popup_data['field_id'] : '';
		?>

		<div id="wppopups-builder" class="wppopups-admin-page">

			<div id="wppopups-builder-overlay">

				<div class="wppopups-builder-overlay-content">

					<i class="fa fa-spinner fa-spin"></i>

					<span class="msg"><?php esc_html_e( 'Loading', 'wp-popups-lite' ); ?></span>
				</div>

			</div>

			<form name="wppopups-builder" id="wppopups-builder-popup" method="post"
			      data-id="<?php echo esc_attr( $this->popup->id );?>">

				<input type="hidden" name="id" value="<?php echo esc_attr( $this->popup->id ); ?>">
				<input type="hidden" value="<?php echo absint( $field_id ); ?>" name="field_id" id="wppopups-field-id">

				<!-- Toolbar -->
				<div class="wppopups-toolbar">

					<div class="wppopups-left">

						<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/wppopups-logo.png" alt="WP Popups">

					</div>

					<div class="wppopups-center">

						<?php if ( $this->popup ) : ?>

							<?php esc_html_e( 'Now editing', 'wp-popups-lite' ); ?>
							<span class="wppopups-center-popup-name wppopups-popup-name"><?php echo esc_html( $this->popup->title ); ?></span>

						<?php endif; ?>

					</div>

					<div class="wppopups-right">

						<?php if ( $this->popup->id !== 0 ) : ?>
							<?php if( $this->popup->status == 'draft' ) : ?>
								<a href="#" id="wppopups-publish" title="<?php esc_attr_e( 'Publish Popup', 'wp-popups-lite' ) ; ?>">
									<span class="text"><?php esc_html_e( 'Publish', 'wp-popups-lite' ) ?></span>
								</a>

								<a href="#" id="wppopups-save" title="<?php  esc_attr_e( 'Save Popup', 'wp-popups-lite' ); ?>">
									<i class="fa fa-check"></i>
									<span class="text"><?php  esc_html_e( 'Save', 'wp-popups-lite' ); ?></span>
								</a>
							<?php else: ?>
								<a href="#" id="wppopups-save" title="<?php  esc_attr_e( 'Save Popup', 'wp-popups-lite' ); ?>">
									<i class="fa fa-check"></i>
									<span class="text"><?php  esc_html_e( 'Save', 'wp-popups-lite' ); ?></span>
								</a>
							<?php endif; ?>

						<?php endif; ?>

						<a href="#" id="wppopups-exit" title="<?php esc_attr_e( 'Exit', 'wp-popups-lite' ); ?>">
							<i class="fa fa-times"></i>
						</a>

					</div>

				</div>

				<!-- Panel toggle buttons. -->
				<div class="wppopups-panels-toggle" id="wppopups-panels-toggle">

					<?php do_action( 'wppopups_builder_panel_buttons', $this->popup, $this->view ); ?>

				</div>

				<div class="wppopups-panels">

					<?php do_action( 'wppopups_builder_panel', $this->popup, $this->view ); ?>

				</div>

			</form>

		</div>

		<?php
	}
}

WPPopups_Builder::instance();
