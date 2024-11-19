<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Tiles style: Simple
 */
class DGWT_JG_TilesStyle_Simple extends DGWT_JG_TilesStyle {
    public $slug = 'Simple';

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    private function init() {
        add_filter(
            'dgwt/jg/gallery/tile_caption/hover=simple',
            array($this, 'get_caption_html'),
            10,
            2
        );
        if ( !dgwt_freemius()->is_premium() ) {
            add_action( 'wp_footer', array($this, 'add_css_style'), 95 );
            add_action( 'admin_footer', array($this, 'add_css_style'), 95 );
        }
        if ( is_admin() ) {
            new DGWT_JG_Simple_Admin($this->assets_url);
        }
    }

    /**
     * Prepare caption html
     *
     * @param string $caption
     * @param object $attachment
     *
     * @return string
     */
    public function get_caption_html( $caption, $attachment ) {
        $label = ( !empty( $attachment->post_excerpt ) ? wp_strip_all_tags( wptexturize( $attachment->post_excerpt ) ) : '' );
        if ( empty( $label ) || DGWT_JG()->settings->get_opt( 'ts_simple_description' ) !== 'show' ) {
            return '';
        }
        $caption_position = wp_strip_all_tags( DGWT_JG()->settings->get_opt( 'ts_simple_description_position', 'over' ) );
        $font_suffix = '14';
        if ( $caption_position === 'bottom' ) {
            $font_suffix = '9';
        }
        $caption_class = 'dgwt-jg-caption__font--' . $font_suffix;
        $caption_position = wp_strip_all_tags( DGWT_JG()->settings->get_opt( 'ts_simple_description_position', 'over' ) );
        $caption = '<figcaption class="dgwt-jg-caption dgwt-jg-caption__position--' . $caption_position . '">';
        $caption .= '<span class="' . $caption_class . '">' . $label . '</span>';
        $caption .= '</figcaption>';
        return $caption;
    }

    public function add_css_style() {
        if ( !$this->can_load() ) {
            return;
        }
        $caption_position = wp_strip_all_tags( DGWT_JG()->settings->get_opt( 'ts_simple_description_position', 'over' ) );
        $caption_display_mode = wp_strip_all_tags( DGWT_JG()->settings->get_opt( 'ts_simple_description_display_mode', 'fixed' ) );
        ob_start();
        ?>
		<style>
			.dgwt-jg-gallery.dgwt-jg-effect-simple .jg-entry-visible.dgwt-jg-item {
				background-color: #000000;
			}

			<?php 
        if ( $caption_position === 'over' ) {
            ?>
			.dgwt-jg-effect-simple .dgwt-jg-item:hover img {
				opacity: 0.55;
			}

			<?php 
        }
        ?>

			.dgwt-jg-effect-simple .dgwt-jg-item .dgwt-jg-caption.dgwt-jg-caption__position--bottom {
				background-color: rgba(0, 0, 0, 0.6);
			}

			.dgwt-jg-effect-simple .dgwt-jg-item .dgwt-jg-caption.dgwt-jg-caption__position--bottom > span {
				color: #ffffff;
			}

			<?php 
        if ( $caption_position === 'bottom' && $caption_display_mode === 'fixed' ) {
            ?>
			.dgwt-jg-effect-simple .dgwt-jg-item .dgwt-jg-caption.dgwt-jg-caption__position--bottom,
			.dgwt-jg-effect-simple .dgwt-jg-item .dgwt-jg-caption.dgwt-jg-caption__position--bottom > span {
				opacity: 1;
			}

			<?php 
        }
        ?>
		</style>
		<?php 
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo DGWT_JG_Helpers::minify_css( ob_get_clean() );
    }

    /**
     * @inheritdoc
     */
    public function css_style() {
        if ( !$this->can_load() ) {
            return;
        }
        wp_enqueue_style(
            'dgwt-tiles-simple',
            $this->assets_url . '/style.css',
            array(),
            DGWT_JG_VERSION
        );
    }

}
