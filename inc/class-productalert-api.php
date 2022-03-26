<?php
/**
 * ProductAlert_API class.
 *
 * @package WooRule
 */

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r

/**
 * ProductAlert_API class.
 *
 * @package WooRule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class ProductAlert_API {

	use Woorule_Logging;

	/**
	 * @see https://integrationdocs.rule.io/productalert/
	 */
	const URL = 'https://ix.rule.io/productalert';

	/**
	 * Create Alert.
	 *
	 * @param array{product_id: string, email: string, phone_number: string, tags: string, language: string} $body_data
	 *
	 * @return array|WP_Error
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public static function create_alert( $body_data ) {
		$data = array(
			'timeout'  => 45,
			'blocking' => true,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( $body_data ),
		);

		$resp = wp_remote_post( self::URL . '/alerts', $data );
		if ( is_wp_error( $resp ) ) {
			static::log( 'Error: ' . $resp->get_error_message() );

			return $resp;
		}

		if ( 201 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			static::log( 'Error: ' . wc_print_r( $resp, true ) );
			static::log( 'Error: ' . wc_print_r( $body_data, true ) );

			return new WP_Error( 600, $resp['error'] );
		} else {
			static::log( 'Success: ' . wc_print_r( $resp, true ) );
			static::log( 'Success: ' . wc_print_r( $body_data, true ) );
		}

		return $resp;
	}

	/**
	 * Create Alert.
	 *
	 * @param array{product_id: string, fields: array, alert_tags: string, stock: int} $body_data
	 *
	 * @return array|WP_Error
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public static function put_product( $body_data ) {
		$data = array(
			'method'   => 'PUT',
			'timeout'  => 45,
			'blocking' => true,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( $body_data ),
		);

		$resp = wp_remote_request( self::URL . '/products', $data );
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
			static::log( 'Success: ' . wc_print_r( $resp, true ) );
			static::log( 'Success: ' . wc_print_r( $body_data, true ) );
		}

		return $resp;
	}

	/**
	 * Create Alert.
	 *
	 * @param array{alert_min_stock: int, alerts_per_stock: int} $body_data
	 *
	 * @return array|WP_Error
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public static function put_settings( $body_data ) {
		$data = array(
			'method'   => 'PUT',
			'timeout'  => 45,
			'blocking' => true,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( $body_data ),
		);

		$resp = wp_remote_request( self::URL . '/settings', $data );
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
			static::log( 'Success: ' . wc_print_r( $resp, true ) );
			static::log( 'Success: ' . wc_print_r( $body_data, true ) );
		}

		return $resp;
	}


}
