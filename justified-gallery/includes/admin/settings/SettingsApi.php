<?php
/**
 * Adapted from https://github.com/tareq1988/wordpress-settings-api-class
 */

/**
 * weDevs Settings API wrapper class
 *
 * @version 1.1
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @link http://tareq.weDevs.com Tareq's Planet
 * @example src/settings-api.php How to use the class
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DGWT_JG_Settings_API {
	/**
	 * Settings sections array
	 *
	 * @var array
	 */
	private $settings_sections = array();

	/**
	 * Settings fields array
	 *
	 * @var array
	 */
	private $settings_fields = array();

	/**
	 * Name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructor
	 *
	 * @param string $name - unique prefix for CSS classes and other names
	 */
	public function __construct( $name = '' ) {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		$this->name   = sanitize_title( $name );
		$this->prefix = sanitize_title( $name ) . '-';
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Set settings sections
	 *
	 * @param array $sections setting sections array
	 */
	public function set_sections( $sections ) {
		$this->settings_sections = $sections;

		return $this;
	}

	/**
	 * Add a single section
	 *
	 * @param array $section
	 */
	public function add_section( $section ) {
		$this->settings_sections[] = $section;

		return $this;
	}

	/**
	 * Set settings fields
	 *
	 * @param array $fields settings fields array
	 */
	public function set_fields( $fields ) {
		$this->settings_fields = $fields;

		return $this;
	}

	public function add_field( $section, $field ) {
		$defaults = array(
			'name'  => '',
			'label' => '',
			'desc'  => '',
			'type'  => 'text',
		);

		$arg                                 = wp_parse_args( $field, $defaults );
		$this->settings_fields[ $section ][] = $arg;

		return $this;
	}

	/**
	 * Initialize and registers the settings sections and fileds to WordPress
	 *
	 * Usually this should be called at `admin_init` hook.
	 *
	 * This function gets the initiated settings sections and fields. Then
	 * registers them to WordPress and ready for use.
	 */
	public function settings_init() {
		if ( get_option( $this->name ) === false ) {
			add_option( $this->name );
		}

		// Register settings sections
		foreach ( $this->settings_sections as $section ) {
			if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
				$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
				if ( PHP_VERSION_ID < 70200 ) {
					// phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions.create_functionDeprecated, Generic.PHP.DeprecatedFunctions.Deprecated, WordPress.PHP.RestrictedPHPFunctions.create_function_create_function
					$callback = create_function( '', 'echo "' . str_replace( '"', '\"', $section['desc'] ) . '";' );
				} else {
					$callback = function () use ( $section ) {
						echo esc_html( $section['desc'] );
					};
				}
			} elseif ( isset( $section['callback'] ) ) {
				$callback = $section['callback'];
			} else {
				$callback = null;
			}
			add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
		}

		// Register settings fields
		foreach ( $this->settings_fields as $section => $field ) {
			foreach ( $field as $option ) {
				$type = isset( $option['type'] ) ? $option['type'] : 'text';

				$args = array(
					'id'                => $option['name'],
					'label_for'         => $args['label_for'] = "$this->name[{$option[ 'name' ]}]",
					'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
					'name'              => $option['label'],
					'size'              => isset( $option['size'] ) ? $option['size'] : null,
					'options'           => isset( $option['options'] ) ? $option['options'] : '',
					'std'               => isset( $option['default'] ) ? $option['default'] : '',
					'class'             => isset( $option['class'] ) ? $option['class'] : '',
					'disabled'          => isset( $option['disabled'] ) ? $option['disabled'] : '',
					'pro'               => isset( $option['pro'] ) ? $option['pro'] : '',
					'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
					'type'              => $type,
				);

				add_settings_field("$this->name[" . $option['name'] . ']', '<span>' . $option['label'] . '</span>', array($this, 'callback_' . $type), $section, $section, $args);
			}
		}

		// Creates our settings in the options table
		foreach ( $this->settings_sections as $section ) {
			register_setting( $section['id'], $this->name, array( $this, 'sanitize_options' ) );
		}
	}

	/**
	 * Get field description for display
	 *
	 * @param array $args settings field args
	 */
	public function get_field_description( $args ) {
		if ( ! empty( $args['desc'] ) ) {
			$css_class = $this->prefix . 'description-field';

			$desc = sprintf( '<p class="%s">%s</p>', $css_class, $args['desc'] );
		} else {
			$desc = '';
		}

		return $desc;
	}

	/**
	 * Head
	 */
	public function callback_head( $args ) {
		echo '<span class="dgwt-jg-settings-hr"></span>';
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_text( $args ) {
		$value = apply_filters( 'dgwt/jg/settings/option_value', esc_attr( $this->get_option( $args['id'], $args['std'] ) ), $args['std'], $args );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type  = isset( $args['type'] ) ? $args['type'] : 'text';
		$class = isset( $args['class'] ) ? ' ' . $args['class'] : '';

		$html  = sprintf( '<input type="%1$s" class="%2$s-text%6$s" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s" />', $type, $size, $this->name, $args['id'], esc_attr( $value ), $class );
		$html .= $this->get_field_description( $args );

		$this->print_field( $html );
	}

	/**
	 * Displays a url field for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_url( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Displays a number field for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_number( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_checkbox( $args ) {
		$value = apply_filters( 'dgwt/jg/settings/option_value', esc_attr( $this->get_option( $args['id'], $args['std'] ) ), $args['std'], $args );
		$html  = '<fieldset>';
		$html .= sprintf( '<label for="%1$s[%2$s]">', $this->name, $args['id'] );
		$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $this->name, $args['id'] );
		$html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $this->name, $args['id'], checked( $value, 'on', false ) );
		$html .= sprintf( '<p class="%1$s-description-field">%2$s</p></label>', $this->name, $args['desc'] );
		$html .= '</fieldset>';

		$this->print_field( $html );
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_multicheck( $args ) {
		$value = $this->get_option( $args['id'], $args['std'] );

		$html = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
			$html   .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $this->name, $args['id'], $key );
			$html   .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $this->name, $args['id'], $key, checked( $checked, $key, false ) );
			$html   .= sprintf( '%1$s</label><br>', $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';

		$this->print_field( $html );
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_radio( $args ) {
		$value = apply_filters( 'dgwt/jg/settings/option_value', $this->get_option( $args['id'], $args['std'] ), $args['std'], $args );
		$html  = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$dis = ! empty( $args['disabled'] ) || $args['pro'] === true ? ' disabled="disabled" ' : '';

			$html .= sprintf( '<label class="dgwt_jg_radio--label" for="%1$s%2$s[%3$s][%4$s]">', $this->prefix, $this->name, $args['id'], $key );
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s%2$s[%3$s][%4$s]" name="%2$s[%3$s]" value="%4$s" %5$s %6$s />', $this->prefix, $this->name, $args['id'], $key, checked( $value, $key, false ), $dis );
			$html .= sprintf( '%1$s</label>', $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';

		$this->print_field( $html );
	}

	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_select( $args ) {
		$value = apply_filters( 'dgwt/jg/settings/option_value', esc_html( $this->get_option( $args['id'], $args['std'] ) ), $args['std'], $args );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]" data-default="%4$s">', $size, $this->name, $args['id'], esc_html( $args['std'] ) );
		foreach ( $args['options'] as $key => $label ) {
			$dis   = ! empty( $args['disabled'] ) ? ' disabled="disabled" ' : '';
			$html .= sprintf( '<option value="%s" %s %s>%s</option>', $key, selected( $value, $key, false ), $dis, $label );
		}
		$html .= sprintf( '</select>' );
		$html .= $this->get_field_description( $args );

		$this->print_field( $html );
	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_textarea( $args ) {
		$value = esc_textarea( $this->get_option( $args['id'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$dis   = ! empty( $args['disabled'] ) ? ' disabled="disabled" ' : '';

		$html  = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" %4$a>%5$s</textarea>', $size, $this->name, $args['id'], $dis, $value );
		$html .= $this->get_field_description( $args );

		$this->print_field( $html );
	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_html( $args ) {
		$this->print_field( $this->get_field_description( $args ) );
	}

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_color( $args ) {
		$value = apply_filters( 'dgwt/jg/settings/option_value', esc_attr( $this->get_option( $args['id'], $args['std'] ) ), $args['std'], $args );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $this->name, $args['id'], $value, $args['std'] );

		if ( DGWT_JG()->settings->is_option_premium( $args ) && ! dgwt_freemius()->is_premium() ) {
			$html = sprintf( '<div class="wp-picker-container"><button type="button" class="button wp-color-result" style="background-color: %1$s"><span class="wp-color-result-text">%2$s</span></button></div>', $value, __( 'Select Color' ) );
		}

		$html .= $this->get_field_description( $args );

		$this->print_field( $html );
	}

	/**
	 * Displays a description only
	 *
	 * @param array $args settings field args
	 */
	public function callback_desc( $args ) {
		$html = '';
		if ( isset( $args['desc'] ) && ! empty( $args['desc'] ) ) {
			$html .= '<div class="dgwt-jg-settings-info">';
			$html .= $args['desc'];
			$html .= '</div>';
		}

		$this->print_field( $html );
	}

	/**
	 * Displays a promobox section
	 *
	 * @param array $args settings field args
	 */
	public function callback_promobox( $args ) {
		$class = isset( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '';

		$html  = '<div class="dgwt_jg_promobox">';
		$html .= apply_filters( 'dgwt/jg/settings/promobox/id=' . esc_attr( $args['id'] ), '' );
		$html .= '</div>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	}

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @param array $args settings field args
	 */
	public function callback_datepicker( $args ) {
		$value = esc_attr( $this->get_option( $args['id'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html  = sprintf( '<input type="text" class="%1$s-text dgwt-datepicker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" />', $size, $this->name, $args['id'], $value );
		$html .= $this->get_field_description( $args );

		$this->print_field( $html );
	}

	/**
	 * Sanitize callback for Settings API
	 */
	public function sanitize_options( $options ) {
		if ( empty( $options ) || ! is_array( $options ) ) {
			return $options;
		}

		foreach ( $options as $option_slug => $option_value ) {
			$sanitize_callback = $this->get_sanitize_callback( $option_slug );

			// If callback is set, call it
			if ( $sanitize_callback ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}
		}

		return $options;
	}

	/**
	 * Get sanitization callback for given option slug
	 *
	 * @param string $slug option slug
	 *
	 * @return mixed string or bool false
	 */
	public function get_sanitize_callback( $slug = '' ) {
		if ( empty( $slug ) ) {
			return false;
		}

		// Iterate over registered fields and see if we can find proper callback
		foreach ( $this->settings_fields as $section => $options ) {
			foreach ( $options as $option ) {
				if ( $option['name'] !== $slug ) {
					continue;
				}

				// Return the callback name
				return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
			}
		}

		return false;
	}

	/**
	 * Get the value of a settings field
	 *
	 * @param string $option  settings field name
	 * @param string $default default text if it's not found
	 * @return string
	 */
	public function get_option( $option, $default = '' ) {
		$options = get_option( $this->name );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

	/**
	 * Show navigations as tab
	 *
	 * Shows all the settings section labels as tab
	 */
	public function show_navigation() {
		$html = '<h2 class="nav-tab-wrapper ' . $this->prefix . 'nav-tab-wrapper">';

		foreach ( $this->settings_sections as $tab ) {
			$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', esc_attr( $tab['id'] ), esc_html( $tab['title'] ) );
		}

		$html .= '</h2>';

		$this->print_field( $html );
	}

	/**
	 * Show the section settings forms
	 *
	 * This function displays every sections in a different form
	 */
	public function show_forms() {
		?>
		<div class="metabox-holder">
			<form class="dgwt-eq-settings-form" method="post" action="options.php">
				<?php foreach ( $this->settings_sections as $form ) { ?>
					<div id="<?php echo esc_attr( $form['id'] ); ?>" class="<?php echo esc_attr( $this->prefix ); ?>group" style="display: none;">
						<?php
						do_action( $this->prefix . 'form_top_' . $form['id'], $form );
						settings_fields( $form['id'] );
						do_settings_sections( $form['id'] );
						do_action( $this->prefix . 'form_bottom_' . $form['id'], $form );
						?>
						<div style="padding-left: 10px">
							<?php submit_button(); ?>
						</div>
					</div>
				<?php } ?>
			</form>
		</div>
		<?php
		$this->script();
	}

	/**
	 * Tabbable JavaScript codes & Initiate Color Picker
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	public function script() {
		?>
		<script>
			jQuery( document ).ready( function ( $ ) {
				//Initiate Color Picker
				if ( $( '.wp-color-picker-field' ).length > 0 ) {
					$( '.wp-color-picker-field' ).wpColorPicker();
				}

				// Switches option sections
				$( '.<?php echo esc_js( $this->prefix ); ?>group' ).hide();
				var activetab = '';
				var maybe_active = '';

				if ( typeof ( localStorage ) != 'undefined' ) {
					maybe_active = localStorage.getItem( '<?php echo esc_js( $this->prefix ); ?>settings-active-tab' );

					if ( maybe_active ) {
						// Check if tabs exists
						$( '.<?php echo esc_js( $this->prefix ); ?>nav-tab-wrapper a' ).each( function () {
							if ( $( this ).attr( 'href' ) === maybe_active ) {
								activetab = maybe_active;
							}
						} );
					}
				}

				if ( activetab !== '' && $( activetab ).length ) {
					$( activetab ).fadeIn();
				} else {
					$( '.<?php echo esc_js( $this->prefix ); ?>group:first' ).fadeIn();
				}
				$( '.<?php echo esc_js( $this->prefix ); ?>group .collapsed' ).each( function () {
					$( this ).find( 'input:checked' ).parent().parent().parent().nextAll().each(
						function () {
							if ( $( this ).hasClass( 'last' ) ) {
								$( this ).removeClass( 'hidden' );
								return false;
							}
							$( this ).filter( '.hidden' ).removeClass( 'hidden' );
						} );
				} );

				if ( activetab !== '' && $( activetab + '-tab' ).length ) {
					$( activetab + '-tab' ).addClass( 'nav-tab-active' );
				} else {
					$( '.<?php echo esc_js( $this->prefix ); ?>nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
				}
				$( '.<?php echo esc_js( $this->prefix ); ?>nav-tab-wrapper a' ).click( function ( evt ) {
					if ( typeof ( localStorage ) != 'undefined' ) {
						localStorage.setItem( '<?php echo esc_js( $this->prefix ); ?>settings-active-tab', $( this ).attr( 'href' ) );
					}

					$( '.<?php echo esc_js( $this->prefix ); ?>nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );

					$( this ).addClass( 'nav-tab-active' ).blur();
					var clicked_group = $( this ).attr( 'href' );

					$( '.<?php echo esc_js( $this->prefix ); ?>group' ).hide();
					$( clicked_group ).fadeIn();
					evt.preventDefault();
				} );

				$( '.<?php echo esc_js( $this->prefix ); ?>browse' ).on( 'click', function ( event ) {
					event.preventDefault();

					var self = $( this );

					// Create the media frame.
					var file_frame = wp.media.frames.file_frame = wp.media( {
						title: self.data( 'uploader_title' ),
						button: {
							text: self.data( 'uploader_button_text' ),
						},
						multiple: false
					} );

					file_frame.on( 'select', function () {
						var attachment = file_frame.state().get( 'selection' ).first().toJSON();

						self.prev( '.<?php echo esc_js( $this->prefix ); ?>url' ).val( attachment.url );
					} );

					// Finally, open the modal
					file_frame.open();
				} );
			} );
		</script>

		<style type="text/css">
			/** WordPress 3.8 Fix **/
			.form-table th { padding: 20px 10px; }
			#wpbody-content .metabox-holder { padding-top: 5px; }
		</style>
		<?php
	}

	private function print_field( $html ) {
		echo wp_kses(
			$html,
			array(
				'input'      => array(
					'type'               => array(),
					'class'              => array(),
					'id'                 => array(),
					'name'               => array(),
					'value'              => array(),
					'checked'            => array(),
					'disabled'           => array(),
					'data-default-color' => array(),
				),
				'code'       => array(),
				'fieldset'   => array(),
				'label'      => array(
					'class' => array(),
					'for'   => array(),
				),
				'b'          => array(),
				'a'          => array(
					'href'  => array(),
					'rel'   => array(),
					'class' => array(),
					'id'    => array(),
				),
				'p'          => array(
					'class' => array(),
				),
				'select'     => array(
					'class'        => array(),
					'name'         => array(),
					'id'           => array(),
					'data-default' => array(),
				),
				'option'     => array(
					'class'    => array(),
					'selected' => array(),
					'value'    => array(),
				),
				'textarea'   => array(
					'rows' => array(),
					'cols' => array(),
					'name' => array(),
					'id'   => array(),
				),
				'div'        => array(
					'class'               => array(),
					'id'                  => array(),
					'data-last_row'       => array(),
					'data-margin'         => array(),
					'data-border'         => array(),
					'data-row_height'     => array(),
					'data-max_row_height' => array(),
				),
				'span'       => array(
					'class' => array(),
				),
				'button'     => array(
					'class' => array(),
					'type'  => array(),
					'style' => array(),
				),
				'h2'         => array(
					'class' => array(),
				),
				'h4'         => array(
					'class' => array(),
				),
				'figure'     => array(
					'class'     => array(),
					'data-size' => array(),
				),
				'img'        => array(
					'src' => array(),
				),
				'figcaption' => array(
					'class' => array(),
				),
			)
		);
	}
}
