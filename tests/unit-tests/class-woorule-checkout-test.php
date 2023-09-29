<?php // @codingStandardsIgnoreStart

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Checkout_Test extends WP_UnitTestCase {
	// @codingStandardsIgnoreEnd

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

	/**
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function test_custom_checkout_field_update_order_meta() {
		$order = WC_Helper_Order::create_order();

		$object = new Woorule_Checkout();

		$_POST['woorule_opt_in'] = 1;
		$object->custom_checkout_field_update_order_meta( $order->get_id() );

		$result = get_post_meta( $order->get_id(), 'woorule_opt_in', true );
		$this->assertEquals( 'true', $result );
	}
}
