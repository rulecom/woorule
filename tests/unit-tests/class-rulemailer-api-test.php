<?php

class RuleMailer_API_Test extends WC_Unit_Test_Case {
	public function test_subscribe() {
		$api_key = getenv( 'RULE_API_KEY' );
		if ( empty( $api_key ) ) {
			$this->markTestSkipped('RULE_API_KEY is not defined.');
		}

		$subscription = array(
			'apikey'              => $api_key,
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'async'               => true,
			'tags'                => array(
				'test'
			),
			'subscribers'         => array(
				'email' => 'nobody@example.com',
			),
			'require_opt_in'      => true,
		);

		$result = RuleMailer_API::subscribe( $subscription );
	}

	public function test_delete_subscriber_tag() {
		$result = RuleMailer_API::delete_subscriber_tag( 'nobody@example.com', 'test' );
	}
}
