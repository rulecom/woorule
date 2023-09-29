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
class WC_Rule_Cart_Hooks extends WP_UnitTestCase {
	// @codingStandardsIgnoreEnd

	/**
	 * @var Woorule_Cart_Hooks
	 */
	private $object;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->object = new Woorule_Cart_Hooks();
	}

	public function test_filter_cart_updated() {
		$result = $this->object->filter_cart_updated( true );
		$this->assertIsBool( $result );
	}

	public function test_cart_updated() {
		$result = $this->object->cart_updated();
		$this->assertNull( $result );
	}
}
