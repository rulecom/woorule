<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WP_RuleMailer_API
 *
 * requires: Wordpress
 */
class WP_RuleMailer_API {
	private static $instance = null;

	private static $api_key;
	private static $api_url;

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public static function subscribe( $body_data ) {
		
		$url = 'https://app.rule.io/api/v2/subscribers';

		$data = array(
			'timeout' => 45,
			'blocking' => true,
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => json_encode( $body_data )
		);

		$resp = wp_remote_post( $url, $data );

		if ( is_wp_error( $resp ) ) {
			static::log( 'Error: ' . $resp->get_error_message() );

		} else {
			static::log( 'Success: ' . print_r( $resp['body'], true ) );
			static::log( 'Success: ' . print_r( $body_data, true ) );
		}
	}

	private static function log( $msg ) {
		if ( WP_DEBUG === true ) {
			$logger = new WC_Logger();

			if ( is_array( $msg ) || is_object( $msg ) ) {
				$logger->add('woorule', print_r( $msg, true ) );

			} else {
				$logger->add('woorule', $msg );	

			}
		}
	}

	// disable
	protected function __construct() {}
	protected function __wakeup() {}
	protected function __clone() {}
}

