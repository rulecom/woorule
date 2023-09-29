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
 * @SuppressWarnings(PHPMD.StaticAccess)
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

		self::activate_api_logging();
		$resp = wp_remote_post( self::URL . '/alerts', $data );
		self::deactivate_api_logging();

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}

		if ( 201 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			return new WP_Error( 600, $resp['error'] );
		}

		return $resp;
	}

	/**
	 * Retrieve a list of products with pending alerts.
	 *
	 * @return array|WP_Error
	 */
	public static function get_products() {
		self::activate_api_logging();
		$resp = wp_remote_get(
			self::URL . '/products',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . self::get_api_key(),
				),
			)
		);
		self::deactivate_api_logging();

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			return new WP_Error( 600, $resp['error'] );
		}

		return $resp;
	}

	/**
	 * Create Alert.
	 *
	 * @param array{product_id: string, fields: array, alert_tags: string, stock: int} $body_data
	 *
	 * @return array|WP_Error
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

		self::activate_api_logging();
		$resp = wp_remote_request( self::URL . '/products', $data );
		self::deactivate_api_logging();

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			return new WP_Error( 600, $resp['error'] );
		}

		return $resp;
	}

	/**
	 * Deletes a product entry and all pending alerts for that product.
	 *
	 * @param array{product_id: string} $body_data
	 *
	 * @return array|WP_Error
	 */
	public static function delete_product( $body_data ) {
		$data = array(
			'method'   => 'DELETE',
			'timeout'  => 45,
			'blocking' => true,
			'headers'  => array(
				'Content-Type' => 'application/json',
			),
			'body'     => wp_json_encode( $body_data ),
		);

		self::activate_api_logging();
		$resp = wp_remote_request( self::URL . '/products', $data );
		self::deactivate_api_logging();

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			return new WP_Error( 600, $resp['error'] );
		}

		return $resp;
	}

	/**
	 * Create Alert.
	 *
	 * @param array{alert_min_stock: int, alerts_per_stock: int} $body_data
	 *
	 * @return array|WP_Error
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

		self::activate_api_logging();
		$resp = wp_remote_request( self::URL . '/settings', $data );
		self::deactivate_api_logging();

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}

		if ( 200 !== $resp['response']['code'] ) {
			return new WP_Error( $resp['response']['code'], $resp['response']['message'] );
		}

		$resp = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( isset( $resp['error'] ) ) {
			return new WP_Error( 600, $resp['error'] );
		}

		return $resp;
	}

	/**
	 * Get API Key.
	 *
	 * @return string
	 */
	private static function get_api_key() {
		$api_key = getenv( 'RULE_API_KEY' );
		if ( ! $api_key ) {
			$api_key = Woorule_Options::get_api_key();
		}

		return $api_key;
	}
}
