<?php

class WC_Rule_Test extends WC_Unit_Test_Case {
	public function test_methods() {
		$object = new Woorule();

		ob_start();
		$object->notice_woo_error();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertStringContainsString( 'notice notice-error', $result );

		ob_start();
		$object->notice_api_error();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertStringContainsString( 'Rule API', $result );

		$result = $object->settings_link( array() );
		$this->assertIsArray( $result );

		Woorule_Options::set_checkout_label( 'Rule API' );

		ob_start();
		$object->settings_page();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertStringContainsString( 'Rule API', $result );
	}

	public function test_update_options() {
		$object = new Woorule();

		$_REQUEST[ '_wpnonce' ] = wp_create_nonce( 'woorule-settings' );
		$_POST[ 'save' ] = 'woorule';
		$_POST['woorule_api'] = 'test';
		$_POST['woorule_checkout_tags'] = 'test';
		$_POST['woorule_checkout_label'] = 'test';
		$_POST['woorule_checkout_show'] = 'test';
		$object->update_options();

		$result = Woorule_Options::get_api_key();
		$this->assertEquals( 'test', $result );
	}
}
