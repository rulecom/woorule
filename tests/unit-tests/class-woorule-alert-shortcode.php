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
class WC_Rule_Alert_Shortcode extends WC_Unit_Test_Case {
	// @codingStandardsIgnoreEnd

	public function test_register_assets() {
		$object = new Woorule_Alert_Shortcode();
		$this->assertInstanceOf( Woorule_Alert_Shortcode::class, new $object );

		$result = $object->register_assets();
		$this->assertNull( $result );
	}

	public function test_output() {
		$object = new Woorule_Alert_Shortcode();
		$result = $object->output( array() );
		$this->assertIsString( $result );
	}

	public function test_subscribe_alert() {
		$product = WC_Helper_Product::create_simple_product();
		$object  = new Woorule_Alert_Shortcode();

		$_POST['nonce']      = wp_create_nonce( 'woorule' );
		$_POST['email']      = 'test@example.com';
		$_POST['product_id'] = $product->get_id();
		$result              = $object->subscribe_alert();

		$this->assertNull( $result );
	}
}