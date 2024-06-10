<?php

/**
 * Base form template.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
abstract class WPPopups_Template {

	/**
	 * Full name of the template, eg "Contact Popup".
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $name;

	/**
	 * Slug of the template, eg "contact-form" - no spaces.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $slug;

	/**
	 * Short description the template.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Short description of the fields included with the template.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $includes = '';

	/**
	 * URL of the icon to display in the admin area.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $icon = '';

	/**
	 * Array of data that is assigned to the post_content on form creation.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $data;

	/**
	 * Priority to show in the list of available templates.
	 *
	 * @since 2.0.0
	 * @var int
	 */
	public $priority = 20;

	/**
	 * Core or additional template.
	 *
	 * @since 2.0.0
	 * @var bool
	 */
	public $core = false;

	/**
	 * Modal message to display when the template is applied.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $modal = '';

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Bootstrap.
		$this->init();

		$type = $this->core ? '_core' : '';

		add_filter( "wppopups_popup_templates{$type}", [ $this, 'template_details' ], $this->priority );
		add_filter( 'wppopups_create_popup_args', [ $this, 'template_data' ], 10, 2 );
		add_filter( 'wppopups_save_popup_args', [ $this, 'template_replace' ], 30, 4 );
		add_filter( 'wppopups_builder_template_active', [ $this, 'template_active' ], 10, 2 );
	}

	/**
	 * Let's get started.
	 *
	 * @since 2.0.0
	 */
	public function init() {
	}

	/**
	 * Add basic template details to the Add New Popup admin screen.
	 *
	 * @param array $templates
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function template_details( $templates ) {

		$templates[] = [
			'name'        => $this->name,
			'slug'        => $this->slug,
			'description' => $this->description,
			'includes'    => $this->includes,
			'icon'        => $this->icon,
		];

		return $templates;
	}

	/**
	 * Add template data when form is created.
	 *
	 * @param array $args
	 * @param array $data
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function template_data( $args, $data ) {

		if ( ! empty( $data ) && ! empty( $data['template'] ) ) {
			if ( $data['template'] === $this->slug ) {
				$args['post_content'] = wppopups_encode( $this->data );
			}
		}

		return $args;
	}

	/**
	 * Replace template on post update if triggered.
	 *
	 * @param array $popup
	 * @param array $data
	 * @param array $args
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function template_replace( $popup, $data, $args ) {

		if ( ! empty( $args['template'] ) ) {
			if ( $args['template'] === $this->slug ) {
				$new = $this->data;

				// leave settings intact and merge with new values
				$new['settings'] = ! empty( $data['settings'] ) ? wp_parse_args( $new['settings'], $data['settings'] ) : [];

				// same for providers connections if exists
				if ( ! empty( $data['providers'] ) ) {
					$new['providers'] = $data['providers'];
					$new['fields'] = $data['fields'];
				}
				// and triggers and rules
				$new['triggers']       = ! empty( $data['triggers'] ) ? $data['triggers'] : [];
				$new['rules']          = ! empty( $data['rules'] ) ? $data['rules'] : [];
				$popup['post_content'] = wppopups_encode( $new );
				// save as draft until it's published
				$popup['post_status'] = 'draft';
			}
		}

		return $popup;
	}

	/**
	 * Pass information about the active template back to the builder.
	 *
	 * @param array $details
	 * @param object $popup
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function template_active( $details, $popup ) {

		if ( empty( $popup ) ) {
			return;
		}

		$popup_data = wppopups_decode( $popup->data );

		if ( empty( $this->modal ) || empty( $popup_data['meta']['template'] ) || $this->slug !== $popup_data['meta']['template'] ) {
			return $details;
		} else {
			$display = $this->template_modal_conditional( $popup_data );
		}

		$template = [
			'name'          => $this->name,
			'slug'          => $this->slug,
			'description'   => $this->description,
			'includes'      => $this->includes,
			'icon'          => $this->icon,
			'modal'         => $this->modal,
			'modal_display' => $display,
		];

		return $template;
	}

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @param array $popup_data
	 *
	 * @return boolean
	 * @since 2.0.0
	 *
	 */
	public function template_modal_conditional( $popup_data ) {

		return false;
	}
}
