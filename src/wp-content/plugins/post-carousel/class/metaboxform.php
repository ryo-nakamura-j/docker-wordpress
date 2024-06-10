<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This is file define the fields of the meta boxes
 *
 * @package post-carousel
 */
class SP_PC_MetaBoxForm {

	/**
	 * text
	 *
	 * @param array $args
	 */
	public function text( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value, $after ) = $this->field_common( $args );

		echo $this->field_before( $args );
		echo sprintf( '<input type="text" class="sp-pc-input-text" value="%1$s" id="%2$s" name="%3$s">%4$s', $value, $args['id'], $name, $after );
		echo $this->field_after();

	}

	/**
	 * text
	 *
	 * @param array $args
	 */
	public function url_disabled( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value, $after ) = $this->field_common( $args );

		echo $this->field_before( $args );
		echo sprintf( '<input type="text" class="sp-pc-input-text" value="%1$s" id="%2$s" name="%3$s" disabled>%4$s', $value, $args['id'], $name, $after );
		echo $this->field_after();

	}

	/**
	 * color
	 *
	 * @param array $args
	 */
	public function color( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value ) = $this->field_common( $args );
		$default_value        = isset( $args['default'] ) ? $args['default'] : '';

		echo $this->field_before( $args );
		echo sprintf( '<input type="text" class="sp-pc-color-picker" value="%1$s" id="%2$s" name="%3$s" data-default-color="%4$s">', $value, $args['id'], $name, $default_value );
		echo $this->field_after();
	}

	/**
	 * number
	 *
	 * @param array $args
	 */
	public function number( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value, $after ) = $this->field_common( $args );
		$min                          = isset( $args['min'] ) ? $args['min'] : null;
		$max                          = isset( $args['max'] ) ? $args['max'] : null;

		echo $this->field_before( $args );
		echo sprintf( '<input type="number" class="sp-pc-input-number" value="%1$s" id="%2$s" name="%3$s">%4$s', $value, $args['id'], $name, $after );
		echo $this->field_after();
	}

	/**
	 * checkbox
	 *
	 * @param array $args
	 */
	public function checkbox( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value, $after ) = $this->field_common( $args );
		$checked                      = ( $value == 'on' ) ? ' checked' : '';

		echo $this->field_before( $args );
		echo sprintf( '<input type="hidden" name="%1$s" value="off">', $name );
		echo sprintf( '<label for="%2$s"><input type="checkbox" %4$s value="on" id="%2$s" name="%1$s">%3$s</label>', $name, $args['id'], $after, $checked );
		echo $this->field_after();
	}

	/**
	 * select
	 *
	 * @param array $args
	 */
	public function select( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value ) = $this->field_common( $args );
		$multiple             = isset( $args['multiple'] ) ? 'multiple' : '';

		echo $this->field_before( $args );
		echo sprintf( '<select name="%1$s" id="%2$s" class="sp-pc-input-text sp-pc-select" %3$s>', $name, $args['id'], $multiple );
		foreach ( $args['options'] as $key => $option ) {
			$selected = ( $value == $key ) ? ' selected="selected"' : '';
			echo sprintf( '<option value="%1$s" %3$s>%2$s</option>', $key, $option, $selected );
		}
		echo '</select>';
		echo $this->field_after();
	}

	/**
	 * Typography for pro ad.
	 *
	 * @param array $args
	 */
	public function typography_type( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value ) = $this->field_common( $args );
		$multiple             = isset( $args['multiple'] ) ? 'multiple' : '';

		echo $this->field_before( $args );
		echo '<div class="sp_pc_font_field">
            <div class="sp-element sp-typography-family">
                Font Family <br>
                <select  disabled name="sp-typo-family" id="sp-typo-family" class="sp-pc-select">
                    <option selected="selected" value="Open Sans">Open Sans</option>
                    <option value="Libre Barcode 39 Extended Text">Libre Barcode 39 Extended Text</option>
                </select>
            </div>
            
            <div class="sp-element sp-typography-variant">
                Font Weight <br>
                <select disabled name="sp-typo-variant" id="sp-typo-variant" class="sp-pc-select">
                    <option value="regular">regular</option>
                </select>
            </div>
            
            <div class="sp-element sp-field-number sp-pseudo-field small-input sp-font-size">
                Font Size <br>
                <input disabled type="number" class="sp-pc-input-number" value="16" title="Font Size">
            </div>
            
            <div class="sp-element sp-field-number sp-pseudo-field small-input sp-font-height">
                Line Height <br>
                <input disabled type="number" class="sp-pc-input-number" value="20" title="Line Height">
            </div>
            <div class="sp-divider"></div>
            <div class="sp-element sp-field-select sp-pseudo-field small-input sp-font-alignment">
                Alignment <br>
                <select disabled name="sp-font-alignment" id="sp-font-alignment" class="sp-pc-select">
                    <option value="left">Left</option>
                </select>
            </div>
            
            <div class="sp-element sp-field-select sp-pseudo-field small-input sp-font-transform">
                Transform <br>
                <select disabled name="sp-font-transform" id="sp-font-transform" class="sp-pc-select">
                    <option value="none">None</option>
                </select>
            </div>
            
            <div class="sp-element sp-field-select sp-pseudo-field small-input sp-font-spacing">
                Letter Spacing <br>
                <select disabled name="sp-font-spacing" id="sp-font-spacing" class="sp-pc-select">
                    <option value="normal">Normal</option>
                </select>
            </div>
            <div class="sp-divider"></div>
            <div class="sp-element sp-typography-color">
                Color <br>
                <div disabled class="sp-element sp-field-color_picker sp-pseudo-field">
                    <input disabled type="text" class="sp-pc-color-picker" value="#444444" id="sp-field-color_picker" name="sp-field-color_picker">
                </div>
            </div>
            <div class="sp-font-preview">The Font Preview</div>
            
        </div>';

		echo $this->field_after();
	}

	/**
	 * Select layout for pro ad.
	 *
	 * @param array $args
	 */
	public function select_layout( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value ) = $this->field_common( $args );
		$multiple             = isset( $args['multiple'] ) ? 'multiple' : '';

		echo $this->field_before( $args );
		echo sprintf(
			'<select name="%1$s" id="%2$s" class="sp-pc-input-text sp-pc-select" %3$s>', $name, $args['id'], $multiple
		); ?>
		<option value="carousel">Carousel</option>
		<option value="gird" disabled>Grid (Pro)</option>
		<option value="masonry" disabled>Masonry (Pro)</option>
		<option value="filter_grid" disabled>Filter-Grid (Pro)</option>
		<option value="filter_masonry" disabled>Filter-Masonry (Pro)</option>
		<?php
		echo '</select>';
		echo $this->field_after();
	}

	/**
	 * Select posts from for pro ad.
	 *
	 * @param array $args
	 */
	public function select_posts_from( array $args ) {
		if ( ! isset( $args['id'], $args['name'] ) ) {
			return;
		}

		list( $name, $value ) = $this->field_common( $args );
		$multiple             = isset( $args['multiple'] ) ? 'multiple' : '';

		echo $this->field_before( $args );
		echo sprintf(
			'<select name="%1$s" id="%2$s" class="sp-pc-input-text sp-pc-select" %3$s>', $name, $args['id'], $multiple
		);
		?>
		<option value="latest">Latest</option>
		<option value="taxonomy" disabled>Taxonomy (Pro)</option>
		<option value="specific_post" disabled>Specific Posts (Pro)</option>
		<option value="most_viewed" disabled>Most Viewed (Pro)</option>
		<option value="most_liked" disabled>Most Liked (Pro)</option>
		<?php
		echo '</select>';
		echo $this->field_after();
	}

	/**
	 * field common
	 *
	 * @param $args
	 *
	 * @return array
	 */
	private function field_common( $args ) {
		global $post;

		// Meta Name
		$group    = isset( $args['group'] ) ? $args['group'] : 'sp_pc_meta_box';
		$multiple = isset( $args['multiple'] ) ? '[]' : '';
		$name     = sprintf( '%s[%s]%s', $group, $args['id'], $multiple );
		$after    = isset( $args['after'] ) ? '<span class="sp-pc-mbf-after">' . $args['after'] . '</span> ' : '';
		// Meta Value
		$default_value = isset( $args['default'] ) ? $args['default'] : '';
		$meta          = get_post_meta( $post->ID, $args['id'], true );
		$value         = ! empty( $meta ) ? $meta : $default_value;
		if ( $value == 'zero' ) {
			$value = 0;
		}

		return array( $name, $value, $after );
	}


	private function field_before( $args ) {
		$table  = '';
		$table .= sprintf( '<div class="sp-pc-element sp-pc-input-group" id="field-%s">', $args['id'] );
		$table .= sprintf( '<div class="sp-pc-input-label">' );
		$table .= sprintf( '<label for="%1$s"><h4>%2$s</h4></label>', $args['id'], $args['name'] );
		if ( ! empty( $args['desc'] ) ) {
			$table .= sprintf( '<p class="sp-pc-input-desc">%s</p>', $args['desc'] );
		}
		$table .= '</div>';
		$table .= sprintf( '<div class="sp-pc-input-field">' );

		return $table;
	}

	private function field_after() {
		return '</div></div>';
	}

}
