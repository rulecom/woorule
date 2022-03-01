<?php

class Woorule_Checkout_Test extends WC_Unit_Test_Case {
	public function test_checkout_field() {
		$object = new Woorule_Checkout();

		Woorule_Options::set_checkout_show( 'on' );
		Woorule_Options::set_checkout_label( 'Test Checkout' );

		ob_start();
		$object->custom_checkout_field();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertStringContainsString( 'Test Checkout', $result );

		Woorule_Options::set_checkout_show( 'off' );
		ob_start();
		$object->custom_checkout_field();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( '', $result );
	}

	public function test_custom_checkout_field_update_order_meta() {
		$order = WC_Helper_Order::create_order();

		$object = new Woorule_Checkout();

		$_POST['woorule_opt_in'] = 1;
		$object->custom_checkout_field_update_order_meta( $order->get_id() );

		$result = get_post_meta( $order->get_id(), 'woorule_opt_in', true );
		$this->assertEquals( 'true', $result );
	}
}
