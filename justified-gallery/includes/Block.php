<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Silence is golden.' );
}

class DGWT_JG_Block {
	const PREVIEW_KEY  = 'dgwt-jg-gutenberg-preview';
	const NONCE_NAME   = 'dgwt-jg-wpnonce';
	const NONCE_ACTION = 'dgwt-jg-see-gut-preview';

	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

		$this->gallery_preview();
	}

	/**
	 * Render gallery preview
	 */
	private function gallery_preview() {
		if ( ! empty( $_GET[ self::PREVIEW_KEY ] ) && $_GET[ self::PREVIEW_KEY ] === 'active' ) {
			$this->disableQueryMonitor();

			if ( ! current_user_can( 'edit_posts' ) ) {
				exit( 'This block could not be generated: permission denied' );
			}

			if ( ! check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME ) ) {
				exit( 'This block could not be generated: invalid nonces' );
			}

			define( 'DGWT_JG_GUTENBERG_PREVIEW', true );

			add_filter( 'show_admin_bar', '__return_false' );

			add_action(
				'template_redirect',
				function () {
					if ( ! check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME ) ) {
						exit( 'This block could not be generated: invalid nonces' );
					}

					if ( empty( $_GET['id'] ) || ! is_array( $_GET['id'] ) ) {
						exit( 'This block could not be generated: no images!' );
					}

					$ids = array();
					foreach ( array_map( 'absint', $_GET['id'] ) as $id ) {
						if ( $id > 0 ) {
							$ids[] = $id;
						}
					}

					if ( empty( $ids ) ) {
						exit( 'This block could not be generated: no images!' );
					}

					$normalizedAttributes        = array();
					$normalizedAttributes['ids'] = implode( ',', $ids );

					if ( isset( $_GET['hover'] ) && in_array(
						$_GET['hover'],
						array(
							'none',
							'simple',
							'jg_standard',
							'layla',
						),
						true
					) ) {
						$normalizedAttributes['hover'] = sanitize_key( $_GET['hover'] );
					}

					if ( isset( $_GET['lastrow'] ) && in_array(
						$_GET['lastrow'],
						array(
							'nojustify',
							'center',
							'right',
							'justify',
							'hide',
						),
						true
					) ) {
						$normalizedAttributes['lastrow'] = sanitize_key( $_GET['lastrow'] );
					}

					if ( isset( $_GET['margin'] ) && intval( $_GET['margin'] ) > 0 ) {
						$normalizedAttributes['margin'] = intval( $_GET['margin'] );
					}

					if ( isset( $_GET['rowheight'] ) && intval( $_GET['rowheight'] ) > 0 ) {
						$normalizedAttributes['rowheight'] = intval( $_GET['rowheight'] );
					}

					if ( isset( $_GET['maxrowheight'] ) && intval( $_GET['maxrowheight'] ) > 0 ) {
						$normalizedAttributes['maxrowheight'] = intval( $_GET['maxrowheight'] );
					}
					?>
				<!DOCTYPE html>
				<html lang="en">
				<head>
					<?php wp_head(); ?>
				</head>
				<body>
					<?php
					echo do_shortcode( '[gallery lightbox="none" link="none" ' . $this->getAttributesString( $normalizedAttributes ) . ']' );
					wp_footer()
					?>
				</body>
				</html>
					<?php
					exit();
				}
			);
		}
	}

	/**
	 * Disable query monitor by caps
	 */
	private function disableQueryMonitor() {
		add_filter(
			'user_has_cap',
			function ( $user_caps ) {

				if ( isset( $user_caps['view_query_monitor'] ) ) {
					$user_caps['view_query_monitor'] = false;
				}

				return $user_caps;
			},
			10,
			1
		);
	}

	public function editor_assets() {
		$script_dependencies = array(
			'dependencies' => null,
			'version'      => DGWT_JG_VERSION,
		);

		if ( file_exists( DGWT_JG_DIR . 'build/block.asset.php' ) ) {
			$script_dependencies = require DGWT_JG_DIR . 'build/block.asset.php';
		}

		// Styles.
		wp_enqueue_style(
			'dgwt-jg-gallery-block-editor-css',
			DGWT_JG_URL . 'build/block.css',
			array( 'wp-edit-blocks' ),
			DGWT_JG_VERSION
		);

		// Scripts.
		wp_register_script(
			'dgwt-jg-gallery-block-editor-js',
			DGWT_JG_URL . 'build/block.js',
			$script_dependencies['dependencies'],
			$script_dependencies['version'],
			false
		);

		$block_data = array(
			'previewURL' => add_query_arg( array( self::PREVIEW_KEY => 'active' ), home_url() ),
		);

		$block_data['previewURL'] = wp_nonce_url(
			$block_data['previewURL'],
			self::NONCE_ACTION,
			self::NONCE_NAME
		);

		wp_localize_script( 'dgwt-jg-gallery-block-editor-js', 'dgwtJgGutenBlock', $block_data );

		wp_enqueue_script( 'dgwt-jg-gallery-block-editor-js' );
	}

	private function getAttributesString( $attributes ) {
		$attributesStringArr = array_map(
			function ( $key, $value ) {
				return $key . '="' . $value . '"';
			},
			array_keys( $attributes ),
			$attributes
		);

		return implode( ' ', $attributesStringArr );
	}
}
