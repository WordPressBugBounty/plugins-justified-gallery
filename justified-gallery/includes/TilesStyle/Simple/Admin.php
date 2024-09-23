<?php

class DGWT_JG_Simple_Admin {

	/**
	 * @var string
	 */
	protected $assets_url = '';

	public function __construct( $assets_url ) {
		$this->assets_url = $assets_url;
		$this->init();
	}

	public function init() {
		$this->join_settings();
		$this->prepare_preview();
	}

	/**
	 * Method to register settings
	 */
	public function join_settings() {
		add_filter( 'dgwt/jg/settings/tiles_style', array( $this, 'register_settings' ) );
		add_filter( 'dgwt/jg/settings/promobox/id=promobox_ts_simple', array( $this, 'add_promobox' ) );
	}

	/**
	 * Add settings for this style
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function register_settings( $settings ) {
		$settings[] = array(
			'name'  => 'ts_simple_preview_head',
			'label' => __( 'Preview', 'justified-gallery' ),
			'type'  => 'head',
			'class' => 'opt-simple dgwt-jg-sgs-header',
		);

		$settings[] = array(
			'name'  => 'promobox_ts_simple',
			'label' => '',
			'type'  => 'promobox',
			'class' => 'opt-simple',
		);

		$settings[] = array(
			'name'  => 'simple_customize_head',
			'label' => __( 'Simple Settings', 'justified-gallery' ),
			'type'  => 'head',
			'class' => 'opt-simple dgwt-jg-sgs-header',
		);

		if ( ! dgwt_freemius()->is_premium() ) {
			$desc = sprintf( __( 'These are the default settings for the <b>%1$s</b> style. To change the following options you must <a href="%2$s">upgrade your plan</a>.', 'justified-gallery' ), __( 'Simple', 'justified-gallery' ), DGWT_JG_Upgrade::get_upgrade_url() );

			$settings[] = array(
				'name'  => 'ts_simple_free_desc',
				'label' => '',
				'type'  => 'desc',
				'desc'  => $desc,
				'class' => 'opt-simple dgwt-jg-option-as-desc',
			);
		}

		$settings[] = array(
			'name'    => 'ts_simple_description',
			'label'   => __( 'Caption', 'justified-gallery' ),
			'type'    => 'radio',
			'class'   => 'opt-simple',
			'options' => array(
				'show' => __( 'Show', 'justified-gallery' ),
				'hide' => __( 'Hide', 'justified-gallery' ),
			),
			'default' => 'show',
		);

		$settings[] = array(
			'name'    => 'ts_simple_description_position',
			'label'   => __( 'Caption position', 'justified-gallery' ),
			'type'    => 'radio',
			'class'   => 'opt-simple js-ts-simple-caption-position',
			'options' => array(
				'over'   => __( 'Over image', 'justified-gallery' ),
				'bottom' => __( 'Bottom', 'justified-gallery' ),
			),
			'default' => 'over',
		);

		$settings[] = array(
			'name'    => 'ts_simple_description_display_mode',
			'label'   => __( 'Caption display mode', 'justified-gallery' ),
			'type'    => 'radio',
			'class'   => 'opt-simple js-ts-simple-caption-display-mode',
			'options' => array(
				'on_hover' => __( 'On Hover', 'justified-gallery' ),
				'fixed'    => __( 'Fixed', 'justified-gallery' ),
			),
			'default' => 'fixed',
		);

		$settings[] = array(
			'name'    => 'ts_simple_font-size',
			'label'   => __( 'Caption size', 'justified-gallery' ),
			'type'    => 'select',
			'class'   => 'opt-simple dgwt-jg-premium-only',
			'options' => array(
				'8'  => __( '8pt', 'justified-gallery' ),
				'9'  => __( '9pt', 'justified-gallery' ),
				'10' => __( '10pt', 'justified-gallery' ),
				'12' => __( '12pt', 'justified-gallery' ),
				'14' => __( '14pt', 'justified-gallery' ),
				'18' => __( '18pt', 'justified-gallery' ),
				'24' => __( '24pt', 'justified-gallery' ),
			),
			'default' => '12',
		);

		$settings[] = array(
			'name'    => 'ts_simple_bg_color',
			'label'   => __( 'Background color', 'justified-gallery' ),
			'type'    => 'color',
			'class'   => 'opt-simple dgwt-jg-premium-only',
			'default' => '#000000',
		);

		$settings[] = array(
			'name'    => 'ts_simple_bg_opacity',
			'label'   => __( 'Background opacity', 'justified-gallery' ),
			'type'    => 'number',
			'size'    => 'small',
			'desc'    => '%',
			'class'   => 'opt-simple dgwt-jg-premium-only',
			'default' => '45',
		);

		$settings[] = array(
			'name'    => 'ts_simple_content_color',
			'label'   => __( 'Content color', 'justified-gallery' ),
			'type'    => 'color',
			'class'   => 'opt-simple dgwt-jg-premium-only',
			'default' => '#FFFFFF',
		);

		return $settings;
	}

	/**
	 * Return HTML of the tile hover style preview on the settings screen
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	public static function add_promobox( $html ) {
		ob_start();
		include dirname( __FILE__ ) . '/promobox.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * The special demo gallery instance for the settings page
	 */
	public function prepare_preview() {
		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Image URL
		 *
		 * @param string $html_img
		 * @param object $attachment
		 * @param array $atts
		 * @param int $image_counter
		 *
		 * @return string (HTML)
		 */
		add_filter(
			'dgwt/jg/gallery/html_img/hover=simple',
			function ( $html_img, $attachment, $atts, $imstance, $image_counter ) {
				if ( ! empty( $atts['demo'] ) ) {
					$url = '';
					switch ( $image_counter ) {
						case 1:
							$url = DGWT_JG_URL . 'assets/img/hovers-demo1.jpg';
							break;
						case 2:
							$url = DGWT_JG_URL . 'assets/img/hovers-demo2.jpg';
							break;
						case 3:
							$url = DGWT_JG_URL . 'assets/img/hovers-demo3.jpg';
							break;
					}
					$html_img = '<img src="' . $url . '" />';
				}

				return $html_img;
			},
			10,
			5
		);
	}
}
