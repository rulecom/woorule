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
class WC_Rule_Utils_Test extends WC_Unit_Test_Case {
    // @codingStandardsIgnoreEnd

	public function test_round() {
		$object = new Woorule_Utils();
		$this->assertInstanceOf( Woorule_Utils::class, new $object );

		$result = Woorule_Utils::round( 125 );
		$this->assertEquals( '125.00', $result );
		$this->assertIsString( $result );
	}

	public function test_order_phone_number() {
		$object = new Woorule_Utils();
		$this->assertInstanceOf( Woorule_Utils::class, new $object );

		/** @var WC_Order $order */
		$order = WC_Helper_Order::create_order();
		$order->set_billing_country( 'SE' );
		$order->set_billing_phone( '732528523' );
		$order->save();

		$result = Woorule_Utils::get_order_phone_number( $order );
		$this->assertEquals( '+46732528523', $result );

		$order->set_billing_country( 'SE' );
		$order->set_billing_phone( '+46732528523' );
		$order->save();

		$result = Woorule_Utils::get_order_phone_number( $order );
		$this->assertEquals( '+46732528523', $result );

		$order->set_billing_country( 'SE' );
		$order->set_billing_phone( '46732528523' );
		$order->save();

		$result = Woorule_Utils::get_order_phone_number( $order );
		$this->assertEquals( '+46732528523', $result );
	}

	public function test_customer_phone_number() {
		/** @var WC_Customer $customer */
		$customer = WC_Helper_Customer::create_mock_customer();
		$customer->set_billing_country( 'SE' );
		$customer->set_billing_phone( '732528523' );

		$result = Woorule_Utils::get_customer_phone_number( $customer );
		$this->assertEquals( '+46732528523', $result );
	}
}
