<?php

/**
 * Represents a single popup
 * Class WPPopups_Popup
 */
class WPPopups_Popup {
	/**
	 * Give a default value for builder
	 * @var int
	 */
	public $id = 0;
	public $title;
	public $data;
	public $status;
	public $parent;
	public $date;
	public $date_gtm;
	public $post_modified;
	public $post_modified_gtm;
	public $childs;

	/**
	 * WPPopups_Popup constructor.
	 *
	 * @param $popup
	 */
	public function __construct( $popup = null ) {
		// Pass values if a valid popup
		if ( $popup ) {
			$this->id                = $popup->ID;
			$this->title             = $popup->post_title;
			$this->data              = wppopups_decode( $popup->post_content );
			$this->status            = $popup->post_status;
			$this->parent            = $popup->post_parent;
			$this->date              = $popup->post_date;
			$this->date_gtm          = $popup->post_date_gtm;
			$this->post_modified     = $popup->post_modified;
			$this->post_modified_gtm = $popup->post_modified_gmt;
			$this->childs            = $this->get_childs();
		}
	}


	/**
	 * Get popup data
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function get_data( $key = '' ) {
		if ( isset( $this->data ) ) {
			if ( empty( $key ) ) {
				return $this->data;
			} elseif ( isset( $this->data[ $key ] ) ) {
				return $this->data[ $key ];
			}
		}

		return false;
	}

	/**
	 * Popup default values
	 *
	 * @param bool $key
	 *
	 * @return array|mixed
	 */
	public function get_defaults( $key = false ) {
		$defaults = [

			'position'  => [
				'position' => 'centered',
			],
			'animation' => [
				'animation' => 'fade',
			],
			'colors'    => [
				'show_overlay'  => 'yes-color',
				'overlay_color' => 'rgba(0,0,0,0.5)',
				'overlay_blur'	=> '2',
				'bg_color'      => 'rgb(255,255,255)',
			],
			'close'     => [
				'close_color'        => '#666',
				'close_hover_color'  => '#000',
				'close_size'         => '30',
				'close_position'     => 'top_right',
				'close_shadow_color' => '#fff',
			],
			'popup_box' => [
				'width'       => '600px',
				'auto_height' => 'yes',
				'height'      => '350',
				'padding'     => '20',
			],
			'border'    => [
				'border_color'  => '#eee',
				'border_width'  => '8',
				'border_radius' => '0',
				'border_type'   => 'none',
				'border_margin' => '0',
			],
			'shadow'    => [
				'shadow_color'    => '#666',
				'shadow_type'     => 'outset',
				'shadow_x_offset' => '0',
				'shadow_y_offset' => '0',
				'shadow_blur'     => '0',
				'shadow_spread'   => '0',
			],
			'css'       => [
				'custom_css' => '',
			],

		];

		if ( $key && isset( $defaults[ $key ] ) ) {
			return $defaults[ $key ];
		}

		return $defaults;
	}

	/**
	 * Get childs of popup
	 * @return array
	 */
	public function get_childs() {
		if( empty( $this->id ) || $this->parent != 0 ) {
			return [];
		}
		$args = [
			'post_parent' => $this->id,
			'post_type'   => 'wppopups',
			'numberposts' => -1,
			'post_status' => 'any'
		];
		$children = get_children( $args );
		if( empty( $children ) ) {
			return [];
		}
		$childs = [];
		foreach ( $children as $child ){
			$childs[] = new WPPopups_Popup( $child );
		}
		return $childs;
	}
}
