<?php

class DGWT_JG_Conflicts {

	public function __construct() {
		add_action( 'init', array( $this, 'fix_lazy_loading' ) );
	}

	public function fix_lazy_loading() {

		// Jetpack Lazy loading
		if (
			class_exists( 'Jetpack' )
			&& method_exists( 'Jetpack', 'is_module_active' )
			&& Jetpack::is_module_active( 'lazy-images' )
		) {
			add_filter( 'dgwt/jg/gallery/img/atts', array( $this, 'add_jetpack_lazy_loading_fix_attr' ) );
		}

		// WP Rocket Lazy loading
		if (
			function_exists( 'rocket_lazyload_get_option' )
			&& rocket_lazyload_get_option( 'images' )
		) {
			add_filter( 'dgwt/jg/gallery/img/atts', array( $this, 'add_wprocket_lazy_loading_fix_attr' ) );
		}

		// @ TODO a3 Lazy Load (class "a3-notlazy")
	}

	/**
	 * Add html class to img to exlude images from lazy loading served by Jetpack
	 *
	 * @return string
	 */
	public function add_jetpack_lazy_loading_fix_attr( $atts ) {
		$atts['class'] = empty( $atts['class'] ) ? 'skip-lazy' : ' skip-lazy';

		return $atts;
	}

	/**
	 * Add specific html attribute to the img to exlude images from lazy loading served by WP Rocket
	 *
	 * @return string
	 */
	public function add_wprocket_lazy_loading_fix_attr( $atts ) {
		$atts['data-no-lazy'] = '1';

		return $atts;
	}
}
