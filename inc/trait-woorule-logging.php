<?php

/**
 * Woorule_Logging trait.
 *
 * @package WooRule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
trait Woorule_Logging {

	/**
	 * Log.
	 *
	 * @param mixed $msg Message.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.MissingImport)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	private static function log( $msg ) {
		if ( WP_DEBUG === true ) {
			$logger = new WC_Logger();

			if ( is_array( $msg ) || is_object( $msg ) ) {
				$logger->add( 'woorule', wc_print_r( $msg, true ) );
			} else {
				$logger->add( 'woorule', $msg );
			}
		}
	}

	/**
	 * Activate API logging.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	private static function activate_api_logging() {
		add_action( 'http_api_debug', __CLASS__ . '::http_api_debug', 20, 5 );
	}

	/**
	 * Deactivate API logging.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	private static function deactivate_api_logging() {
		remove_action( 'http_api_debug', __CLASS__ . '::http_api_debug', 20 );
	}

	/**
	 * Debug HTTP api request.
	 *
	 * @param array|WP_Error $response
	 * @param string $arg2
	 * @param string $arg3
	 * @param array $parsed_args
	 * @param string $url
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @codeCoverageIgnore
	 */
	public static function http_api_debug( $response, $arg2, $arg3, $parsed_args, $url ) {
		$method          = $parsed_args['method'];
		$request_body    = isset( $parsed_args['body'] ) ? $parsed_args['body'] : '';
		$request_headers = isset( $parsed_args['headers'] ) ?
			implode( "\n", $parsed_args['headers'] ) : array();

		if ( is_wp_error( $response ) ) {
			/** @var WP_Error $response */

			$debug = "\n>>>>>>>> BEGIN CLIENT REQUEST DEBUG INFO >>>>>>>>\n\n" .
				"Request Method: $method\n" .
				"Request URL: $url\n" .
				"Request Headers: $request_headers\n" .
				"Request Body:\n$request_body\n\n" .
				"Error: {$response->get_error_message()}\n" .
				"<<<<<<<< END CLIENT REQUEST DEBUG INFO <<<<<<<<\n\n";

			self::log( $debug );

			return;
		}

		$body = wp_remote_retrieve_body( $response );
		$code = wp_remote_retrieve_response_code( $response );

		$debug = "\n>>>>>>>> BEGIN CLIENT REQUEST DEBUG INFO >>>>>>>>\n\n" .
			"Request Method: $method\n" .
			"Request URL: $url\n" .
			"Request Headers: $request_headers\n" .
			"Request Body:\n$request_body\n\n" .
			"Response Code: $code\n" .
			"Response Body:\n$body\n\n" .
			"<<<<<<<< END CLIENT REQUEST DEBUG INFO <<<<<<<<\n\n";

		self::log( $debug );
	}
}
