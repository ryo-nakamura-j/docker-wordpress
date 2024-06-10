<?php

add_shortcode( 'wpp-elementor', 'wpp_elementor_shortcode' );

function wpp_elementor_shortcode( $attributes = array() ) {

	if ( empty( $attributes['id'] ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return '';
	}

	$include_css = false;

	if ( isset( $attributes['css'] ) && 'false' !== $attributes['css'] ) {
		$include_css = (bool) $attributes['css'];
	}

	return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $attributes['id'], $include_css );
}
