<?php
/**
 * Class Woorule_Options
 *
 * @package Woorule
 */

// phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType

/**
 * Class Woorule_Options
 *
 * @method static get_api_key()
 * @method static get_checkout_tags()
 * @method static get_checkout_label()
 * @method static get_checkout_show()
 * @method static get_options()
 * @method static set_api_key( string $value )
 * @method static set_checkout_tags( string $value )
 * @method static set_checkout_label( string $value )
 * @method static set_checkout_show( string $value )
 * @method static set_options( array $options )
 *
 * @package Woorule
 */
class Woorule_Options {
	const OPTIONS_KEY = 'woocommerce_rulemailer_settings';

	/**
	 * Plugin options.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Woorule_Options constructor.
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->options = get_option( self::OPTIONS_KEY, array() );
	}

	/**
	 * Prevent unserializing.
	 *
	 * @return void
	 */
	protected function __wakeup() {
	}

	/**
	 * Prevent cloning.
	 *
	 * @return void
	 */
	protected function __clone() {
	}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	protected static function get_instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Get option value magic method.
	 *
	 * @param string $name Getter functions name.
	 * @param array $arguments Functions arguments.
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException Exception if not a getter function.
	 */
	public static function __callStatic( $name, $arguments ) {
		$instance = self::get_instance();

		$instance->filter_options();

		if ( 0 === strpos( $name, 'get_' ) ) {
			return $instance->get( substr( $name, 4 ) );
		} elseif ( 0 === strpos( $name, 'set_' ) ) {
			$instance->set( substr( $name, 4 ), $arguments[0] );
		} else {
			throw new BadMethodCallException( $name . ' is not defined in ' . __CLASS__ );
		}

		return null;
	}

	/**
	 * Implement getter functions.
	 *
	 * @param string $option_name Option name.
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException Exception.
	 */
	protected function get( $option_name ) {
		$this->options = get_option( self::OPTIONS_KEY, array() );

		if ( 'options' === $option_name ) {
			return $this->options;
		} elseif ( isset( $this->options[ 'woorule_' . $option_name ] ) ) {
			return $this->options[ 'woorule_' . $option_name ];
		} else {
			throw new BadMethodCallException( $option_name . ' is not defined in ' . __CLASS__ );
		}
	}

	/**
	 * Implement setter functions.
	 *
	 * @param string $option_name Option name.
	 * @param mixed $value Value.
	 *
	 * @return void
	 *
	 * @throws BadMethodCallException Exception.
	 */
	protected function set( $option_name, $value ) {
		static $updated = null;

		if ( 'options' === $option_name && is_array( $value ) ) {
			// Merge new options with existent object's options.
			$value = wp_parse_args( $value, $this->options );
			$this->filter_options( $value );
		} elseif ( isset( $this->options[ 'woorule_' . $option_name ] ) ) {
			$this->options[ 'woorule_' . $option_name ] = $value;
		} else {
			throw new BadMethodCallException( $option_name . ' is not defined in ' . __CLASS__ );
		}

		if ( is_null( $updated ) ) {
			$updated = true;

			// Save options to DB on shutdown.
			add_action(
				'shutdown',
				// Anonymous function is used because we do want to have public DB update function.
				function () {
					$this->filter_options();
					update_option( self::OPTIONS_KEY, $this->options, false );
				}
			);
		}
	}

	/**
	 * Get options defaults.
	 *
	 * @return array
	 */
	protected function get_options_defaults() {
		return (array) apply_filters(
			'woorule_options_defaults',
			array(
				'woorule_api_key'        => '',
				'woorule_checkout_tags'  => '',
				'woorule_checkout_label' => '',
				'woorule_checkout_show'  => '',
			)
		);
	}

	/**
	 * Filter options.
	 *
	 * @param array $options Options.
	 *
	 * @return void
	 */
	protected function filter_options( $options = null ) {
		$this->options = shortcode_atts(
			self::get_options_defaults(),
			is_null( $options ) ? $this->options : $options
		);
	}
}
