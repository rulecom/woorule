<?php
/**
 * requires: Wordpress
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

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

	public static function subscribe( $api_url, $body_data ) {
		$url = $api_url . 'subscribers';

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
			echo 'Error: ' . $resp->get_error_message();

		} else {
			echo '<pre>';
			echo 'Success: ' . $resp;
			echo print_r( $resp );
			echo '</pre>';
		}
	}

	// disable
	protected function __construct() {}
	protected function __wakeup() {}
	protected function __clone() {}
}

