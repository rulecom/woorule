<?php
/**
 * RuleMailer_API class.
 *
 * @package WooRule
 */

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r

/**
 * RuleMailer_API class.
 *
 * @package WooRule
 */
class RuleMailer_API {
	const URL = 'https://app.rule.io/api/v2/subscribers';

	/**
	 * Subscribe.
	 *
	 * @param array $body_data Body data.
	 *
	 * @return void
	 */
	public static function subscribe( $body_data ) {
		$data = array(
			'timeout'  => 45,
			'blocking' => true,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( $body_data ),
		);

		$resp = wp_remote_post( self::URL, $data );

		if ( is_wp_error( $resp ) ) {
			static::log( 'Error: ' . $resp->get_error_message() );
		} else {
			static::log( 'Subscribe Success: ' . print_r( $resp['body'], true ) );
			static::log( 'Subscribe Success: ' . print_r( $body_data, true ) );
		}
	}

	/**
	 * Log.
	 *
	 * @param mixed $msg Message.
	 *
	 * @return void
	 */
	private static function log( $msg ) {
		if ( WP_DEBUG === true ) {
			$logger = new WC_Logger();

			if ( is_array( $msg ) || is_object( $msg ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				$logger->add( 'woorule', print_r( $msg, true ) );
			} else {
				$logger->add( 'woorule', $msg );
			}
		}
	}
}
