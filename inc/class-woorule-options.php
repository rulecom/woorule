<?php
/**
 * Class Woorule_Options
 *
 * @package Woorule
 */

/**
 * Class Woorule_Options
 *
 * @package Woorule
 */
class Woorule_Options {
	/**
	 * Plugin options.
	 *
	 * @var array
	 */
	protected static $options = array();

	/**
	 * Get API key.
	 *
	 * @return string
	 */
	public static function get_api_key() {
		return self::get_option( 'woorule_api_key' );
	}

	/**
	 * Get checkout tags.
	 *
	 * @return string
	 */
	public static function get_checkout_tags() {
		return self::get_option( 'woorule_checkout_tags' );
	}

	/**
	 * Get checkout label.
	 *
	 * @return string
	 */
	public static function get_checkout_label() {
		return self::get_option( 'woorule_checkout_label' );
	}

	/**
	 * Get checkout show.
	 *
	 * @return string
	 */
	public static function get_checkout_show() {
		return self::get_option( 'woorule_checkout_show' );
	}

	/**
	 * Get option.
	 *
	 * @param string $option_name Option name.
	 *
	 * @return string
	 */
	protected static function get_option( $option_name ) {
		if ( empty( self::$options ) ) {
			self::$options = get_option( 'woocommerce_rulemailer_settings', array() );
		}

		return isset( self::$options[ $option_name ] ) ? self::$options[ $option_name ] : '';
	}

	/**
	 * Set plugin options.
	 *
	 * @param array $options Options.
	 *
	 * @return void
	 */
	public static function set_options( $options ) {
		self::$options = wp_parse_args(
			$options,
			array(
				'woorule_api_key'        => '',
				'woorule_checkout_tags'  => '',
				'woorule_checkout_label' => '',
				'woorule_checkout_show'  => '',
			)
		);

		update_option( 'woocommerce_rulemailer_settings', self::$options, false );
	}
}
