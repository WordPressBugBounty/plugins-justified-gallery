<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DGWT_JG_FeedbackNotice {

	const ACTIVATION_DATE_OPT = 'dgwt_jg_activation_date';

	const HIDE_NOTICE_OPT = 'dgwt_jg_dismiss_review_notice';

	const DISMISS_AJAX_ACTION = 'dgwt_jg_dismiss_notice';

	const REVIEW_URL = 'https://wordpress.org/support/plugin/justified-gallery/reviews/?filter=5';

	/**
	 * Admin notice offset
	 *
	 * @var int timestamp
	 */
	private $offset;

	public function __construct() {
		$this->offset = strtotime( '-7 days' );
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'wp_ajax_' . self::DISMISS_AJAX_ACTION, array( $this, 'dismiss_notice' ) );
		add_action( 'admin_footer', array( $this, 'print_dismiss_js' ) );
	}

	/**
	 * Check if is possible to display admin notice on the current screen
	 *
	 * @return bool
	 */
	private function allow_display() {
		if (
			in_array( get_current_screen()->base, array( 'dashboard', 'post', 'edit' ), true )
			|| strpos( get_current_screen()->base, DGWT_JG_SETTINGS_KEY ) !== false
		) {
			return true;
		}

		return false;
	}

	/**
	 * Display feedback notice
	 */
	public function display_notice() {
		global $current_user;

		if ( $this->allow_display() ) {
			?>
			<div class="dgwt-jg-review-notice">
				<div class="dgwt-jg-review-notice-logo"></div>
					<?php
					echo wp_kses(
						sprintf(
							__( "Hey %1\$s, it's Mateusz Czardybon from %2\$s. You have used this free plugin for some time now, and I hope you like it!", 'justified-gallery' ),
							'<strong>' . $current_user->display_name . '</strong>',
							'<strong>' . DGWT_JG_NAME . '</strong>'
						),
						array( 'strong' => array() )
					);
					?>
				<br />
					<?php
					echo wp_kses(
						sprintf(
							__( 'I have spent countless hours developing it, and it would mean a lot to me if you %1$ssupport it with a quick review on WordPress.org.%2$s', 'justified-gallery' ),
							'<strong><a target="_blank" href="' . self::REVIEW_URL . '">',
							'</a></strong>'
						),
						array(
							'strong' => array(),
							'a'      => array(
								'target' => array(),
								'href'   => array(),
							),
						)
					);
					?>
				<div class="button-container">
					<a href="<?php echo esc_attr( self::REVIEW_URL ); ?>" target="_blank" data-link="follow" class="button-secondary dgwt-review-notice-dismiss">
						<span class="dashicons dashicons-star-filled"></span>
						<?php esc_html_e( 'Review Justified Gallery', 'justified-gallery' ); ?>
					</a>
					<a href="#" class="button-secondary dgwt-review-notice-dismiss">
						<span class="dashicons dashicons-no-alt"></span>
						<?php esc_html_e( 'No thanks', 'justified-gallery' ); ?>
					</a>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Check installation date
	 */
	public function check_installation_date() {
		$date = get_option( self::ACTIVATION_DATE_OPT );
		if ( empty( $date ) ) {
			add_option( self::ACTIVATION_DATE_OPT, time() );
		}

		$notice_closed = get_option( self::HIDE_NOTICE_OPT );

		if ( empty( $notice_closed ) ) {
			$install_date = get_option( self::ACTIVATION_DATE_OPT );

			if ( $this->offset >= $install_date && current_user_can( 'install_plugins' ) ) {
				add_action( 'admin_notices', array( $this, 'display_notice' ) );
			}
		}
	}


	/**
	 * Hide admin notice
	 */
	public function dismiss_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1, 403 );
		}
		check_ajax_referer( self::DISMISS_AJAX_ACTION );
		update_option( self::HIDE_NOTICE_OPT, true );
		wp_send_json_success();
	}

	/**
	 * Print JS for close admin notice
	 */
	public function print_dismiss_js() {
		if ( ! $this->allow_display() ) {
			return false;
		}
		?>
		<script>
			(function ($) {
				$( document ).on( 'click', '.dgwt-review-notice-dismiss', function () {
					var $box = $( this ).closest( '.dgwt-jg-review-notice' ),
						isLink = $( this ).attr('data-link') === 'follow' ? true : false;

					$box.fadeOut( 700 );

					$.ajax( {
						url: ajaxurl,
						data: {
							action: '<?php echo esc_js( self::DISMISS_AJAX_ACTION ); ?>',
							_wpnonce: '<?php echo wp_create_nonce( self::DISMISS_AJAX_ACTION ); ?>',
						}
					} ).done( function ( data ) {
						setTimeout(function(){
							$box.remove();
						}, 700);
					} );

					if(!isLink) {
						return false;
					}
				} );
			}(jQuery));
		</script>
		<?php
	}
}

new DGWT_JG_FeedbackNotice();
