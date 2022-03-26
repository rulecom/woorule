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
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class RuleMailer_API {
	use Woorule_Logging;

	const URL = 'https://app.rule.io/api/v2/subscribers';

	/**
	 * Subscribe.
	 *
	 * @param array $body_data Body data.
	 *
	 * @return array|WP_Error
	 * @SuppressWarnings(PHPMD.ElseExpression)
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

			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			static::log( 'Error: ' . wc_print_r( $resp, true ) );
			static::log( 'Error: ' . wc_print_r( $body_data, true ) );

			return new WP_Error( 600, $resp['error'] );
		} else {
			static::log( 'Subscribe Success: ' . wc_print_r( $resp, true ) );
			static::log( 'Subscribe Success: ' . wc_print_r( $body_data, true ) );
		}

		return $resp;
	}

	/**
	 * Delete subscriber tag.
	 *
	 * @param string $email Subscriber email.
	 * @param string $tag Tag.
	 *
	 * @return array|WP_Error
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public static function delete_subscriber_tag( $email, $tag ) {
		$data = array(
			'method'   => 'DELETE',
			'timeout'  => 45,
			'blocking' => true,
		);

		$resp = wp_remote_post( self::URL . "/{$email}/tags/{$tag}", $data );

		if ( is_wp_error( $resp ) ) {
			static::log( 'Error delete subscriber tag: ' . $resp->get_error_message() );

			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			static::log( 'Error: ' . wc_print_r( $resp, true ) );
			static::log( 'Error: ' . wc_print_r( compact( 'email', 'tag' ), true ) );

			return new WP_Error( 600, $resp['error'] );
		} else {
			static::log( 'Subscriber tag deleted successfully: ' . wc_print_r( $resp, true ) );
		}

		return $resp;
	}
}
