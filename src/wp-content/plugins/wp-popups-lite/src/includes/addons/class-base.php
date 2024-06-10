<?php

/**
 * Addons class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
abstract class WPPopups_Addon {

	/**
	 * Addon version.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * name.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * name in slug format.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Load priority.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Holds the API connections.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	public $api = false;

	/**
	 * Service icon.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Service icon.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $type;


	/**
	 * Popup Object
	 * @var WPPopups_Popup|bool|null
	 */
	private $popup;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->init();

		// Add to list of available providers.
		add_filter( 'wppopups_addons_available', [ $this, 'register_addon' ], $this->priority, 1 );

		// Process builder AJAX requests.
		//add_action( "wp_ajax_wppopups_extras_ajax_{$this->slug}", [ $this, 'process_ajax' ] );

		// Process entry.
		//add_action( 'wppopups_process_complete', [ $this, 'process_entry' ] );

		// Fetch and store the current form data when in the builder.
		add_action( 'wppopups_builder_init', [ $this, 'builder_form_data' ] );

		// Output builder sidebar.
		add_action( 'wppopups_addons_panel_sidebar', [ $this, 'builder_sidebar' ], $this->priority );

		// Output builder content.
		add_action( 'wppopups_addons_panel_content', [ $this, 'builder_output' ], $this->priority );

		// Remove provider from Settings Integrations tab.
		//add_action( 'wp_ajax_wppopups_settings_extra_disconnect', [ $this, 'integrations_tab_disconnect' ] );

		// Add new provider from Settings Integrations tab.
		//add_action( 'wp_ajax_wppopups_settings_extra_add', [ $this, 'integrations_tab_add' ] );

		// Add providers sections to the Settings Integrations tab.
		//add_action( 'wppopups_settings_extras', [ $this, 'integrations_tab_options' ], $this->priority, 2 );
	}

	/**
	 * All systems go. Used by subclasses.
	 *
	 * @since 2.0.0
	 */
	public function init() {
	}

	/**
	 * Add to list of registered addons.
	 *
	 * @param array $addons Array of all active providers.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function register_addon( $addons = [] ) {

		$addons[ $this->slug ] = $this->name;

		return $addons;
	}

	/********************************************************
	 * Builder methods - these methods _build_ the Builder. *
	 ********************************************************/

	/**
	 * Fetch and store the current form data when in the builder.
	 *
	 * @since 2.0.0
	 */
	public function builder_form_data() {

		if ( ! empty( $_GET['popup_id'] ) && empty( $this->popup ) ) {
			$this->popup = wppopups()->popups->get(
				absint( $_GET['popup_id'] ),
				[
					'content_only' => true,
				]
			);
		}
	}


	/**
	 * Display content inside the panel content area.
	 *
	 * @since 2.0.0
	 */
	public function builder_content() {

		$popup_data  = $this->popup->data;
		$this->output_fields($popup_data);
	}

	/**
	 * Display content inside the panel sidebar area.
	 *
	 * @since 2.0.0
	 */
	public function builder_sidebar() {

		$popup_data  = $this->popup->data;

		$configured = ! empty( $popup_data['addons'][ $this->slug ]['enable'] ) ? 'configured' : '';
		$configured = apply_filters( 'wppopups_addons_' . $this->slug . '_configured', $configured );

		echo '<a href="#" class="wppopups-panel-sidebar-section icon ' . esc_attr( $configured ) . ' wppopups-panel-sidebar-section-' . esc_attr( $this->slug ) . '" data-section="' . esc_attr( $this->slug ) . '">';

		echo '<img src="' . esc_url( $this->icon ) . '">';

		echo esc_html( $this->name );

		echo '<i class="fa fa-angle-right wppopups-toggle-arrow"></i>';

		if ( ! empty( $configured ) ) {
			echo '<i class="fa fa-check-circle-o"></i>';
		}

		echo '</a>';
	}

	/**
	 * Wraps the builder content with the required markup.
	 *
	 * @since 2.0.0
	 */
	public function builder_output() {
		?>
		<div class="wppopups-panel-content-section wppopups-panel-content-section-<?php echo esc_attr( $this->slug ); ?>" id="<?php echo esc_attr( $this->slug ); ?>-addon">

			<?php $this->builder_output_before(); ?>

			<div class="wppopups-panel-content-section-title">

				<?php echo $this->name; ?>

			</div>

			<div class="wppopups-addon-connections-wrap wppopups-clear">

				<div class="wppopups-addon-connections">

					<?php $this->builder_content(); ?>

				</div>

			</div>

			<?php $this->builder_output_after(); ?>

		</div>
		<?php
	}

	/**
	 * Optionally output content before the main builder output.
	 *
	 * @since 2.0.0
	 */
	public function builder_output_before() {
	}

	/**
	 * Optionally output content after the main builder output.
	 *
	 * @since 2.0.0
	 */
	public function builder_output_after() {
	}
}
