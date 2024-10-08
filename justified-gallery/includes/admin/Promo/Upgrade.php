<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DGWT_JG_Upgrade {

	private $can_display = false;

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'current_screen', array( $this, 'print_scripts' ) );
	}

	public function print_scripts() {
		if ( dgwt_freemius()->is_premium() ) {
			return;
		}

		$cs = get_current_screen();

		if ( ! empty( $cs->base ) && strpos( $cs->base, 'dgwt_jg' ) !== false ) {
			add_thickbox();
			add_action( 'admin_footer', array( $this, 'render_upgrade_modal' ), 10 );
			add_action( 'admin_footer', array( $this, 'upgrade_modal_handler' ), 20 );
		}
	}

	/**
	 * Render plugin upgrade modal
	 *
	 * @return void
	 */
	public function render_upgrade_modal() {
		$features = array(
			__( 'Speed up galleries load time (even 20x faster!)', 'justified-gallery' ),
			__( 'Tiles style settings ', 'justified-gallery' ),
			__( 'Photoswipe settings', 'justified-gallery' ),
			__( 'Swipebox settings', 'justified-gallery' ),
			__( 'All future Pro features at current price', 'justified-gallery' ),
		);
		?>
		<a style="display:none;" class="thickbox js-dgwt-jg-modal-pro-handler" href="#TB_inline?width=600&height=320&inlineId=dgwt-jg-modal" title="<?php echo esc_attr__( 'Justified Gallery Pro - Upgrade Now', 'justified-gallery' ); ?>"></a>
		<div id="dgwt-jg-modal" class="dgwt-jg-modal-upgrade" style="display:none;">
		<img class="dgwt-jg-modal-logo" src="<?php echo esc_attr( DGWT_JG_URL . 'assets/img/jg-logo-128.png' ); ?>" width="128" height="128" />
		<h2 class="dgwt-jg-modal-title">
		<?php
		echo wp_kses(
			__( 'Changing this option is only possible in Justified Gallery <b>Pro</b>. <br /> Upgrade and get all features:', 'justified-gallery' ),
			array(
				'b'  => array(),
				'br' => array(),
			)
		);
		?>
		</h2>
		<ul>
			<?php
			foreach ( $features as $feature ) {
				echo '<li><strong>+ ' . esc_html( $feature ) . '</strong></li>';
			}
			?>
		</ul>
		<p>
			<?php esc_html_e( 'You can upgrade without leaving the admin panel by clicking below.', 'justified-gallery' ); ?>
			<br />
			<?php esc_html_e( 'Free updates and email support included.', 'justified-gallery' ); ?>
		</p>
		<p>
			<a class="button-primary" target="_blank" href="<?php echo esc_attr( self::get_upgrade_url() ); ?>"><?php esc_html_e( 'Upgrade Now', 'justified-gallery' ); ?></a>
		</p>
		</div>
		<?php
	}

	public function upgrade_modal_handler() {
		?>
		<script>
			(function ($) {
				var $handler = $('.dgwt-jg-premium-only label, .dgwt-jg-premium-only input, .dgwt-jg-premium-only button');

					$handler.on('click', function (e) {
						triggerModal(e);
					});

					$('.dgwt-jg-premium-only select').on('change', function(e){
						$(this).val($(this).attr('data-default'));
						triggerModal(e);
					});

				function triggerModal(e){
					e.preventDefault();
					$('.js-dgwt-jg-modal-pro-handler').trigger('click');
				}
			})(jQuery);
		</script>
		<?php
	}

	public static function get_upgrade_url() {
		return esc_url( dgwt_freemius()->get_upgrade_url() );
	}
}

new DGWT_JG_Upgrade();
