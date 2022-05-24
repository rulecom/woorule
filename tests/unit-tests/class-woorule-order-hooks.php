<?php
// @codingStandardsIgnoreStart

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class WC_Rule_Order_Hooks extends WC_Unit_Test_Case {
	// @codingStandardsIgnoreEnd

	/**
	 * @var Woorule_Order_Hooks
	 */
	private $object;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->object = new Woorule_Order_Hooks();
	}

	public function test_order_status_changed() {
		/** @var WC_Order $order */
		$order = WC_Helper_Order::create_order();

		$result = $this->object->order_status_changed(
			$order->get_id(),
			'processing',
			'completed'
		);
		$this->assertNull( $result );
	}
}
