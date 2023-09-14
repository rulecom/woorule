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
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class Woorule_Options {
	const OPTIONS_KEY = 'woocommerce_rulemailer_settings';

	/**
	 * Prevent unserializing.
	 *
	 * @return void
	 */
	public function __wakeup() {
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
	 * @return $this Instance.
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
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public static function __callStatic( $name, $arguments ) {
		$instance = self::get_instance();

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
	 */
	protected function get( $option_name ) {
		$options = self::load_options();

		if ( 'options' === $option_name ) {
			return $options;
		}

		return isset( $options[ 'woorule_' . $option_name ] ) ? $options[ 'woorule_' . $option_name ] : null;
	}

	/**
	 * Implement setter functions.
	 *
	 * @param string $option_name Option name.
	 * @param mixed $value Value.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function set( $option_name, $value ) {
		$options = self::load_options();

		if ( 'options' === $option_name ) {
			if ( is_array( $value ) ) {
				// Merge new options with existent object's options.
				$value = wp_parse_args( $value, $options );

				update_option( self::OPTIONS_KEY, $value, false );
			}
		} else {
			$options[ 'woorule_' . $option_name ] = $value;

			update_option( self::OPTIONS_KEY, $options, false );
		}
	}

	/**
	 * Get options defaults.
	 *
	 * @return array
	 */
	private static function get_options_defaults() {
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
	 * @return array
	 */
	private static function filter_options( $options ) {
		return shortcode_atts(
			self::get_options_defaults(),
			$options
		);
	}

	/**
	 * Load options.
	 *
	 * @return array
	 */
	private static function load_options() {
		return self::filter_options( get_option( self::OPTIONS_KEY, array() ) );
	}
}
