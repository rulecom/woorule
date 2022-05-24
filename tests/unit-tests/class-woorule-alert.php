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
class WC_Rule_Alert extends WC_Unit_Test_Case {
	// @codingStandardsIgnoreEnd

	public function test_save_product_variation() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$variable = WC_Helper_Product::create_variation_product();

		$result = $object->save_product_variation( $variable->get_id(), 0 );
		$this->assertNull( $result );
	}

	public function test_product_object_save() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$product = WC_Helper_Product::create_simple_product();

		$result = $object->product_object_save( $product, array() );
		$this->assertNull( $result );
	}

	public function test_add_stock_html() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$product = WC_Helper_Product::create_simple_product();

		$result = $object->add_stock_html( '', $product );
		$this->assertIsString( $result );
	}

	public function test_add_options_defaults() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$result = $object->add_options_defaults( array() );
		$this->assertIsArray( $result );
	}

	public function test_admin_settings() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		ob_start();
		$result   = $object->add_options_defaults( array() );
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertNull( $result );
		$this->assertIsString( $contents );
	}

	public function test_update_options() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$result = $object->update_options( array() );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'woorule_alert_product_show', $result );
		$this->assertArrayHasKey( 'woorule_alert_label', $result );
		$this->assertArrayHasKey( 'woorule_alert_placeholder', $result );
		$this->assertArrayHasKey( 'woorule_alert_button', $result );
		$this->assertArrayHasKey( 'woorule_alert_tags', $result );
		$this->assertArrayHasKey( 'woorule_alert_product_tags', $result );
		$this->assertArrayHasKey( 'woorule_alert_min_stock', $result );
		$this->assertArrayHasKey( 'woorule_alerts_per_stock', $result );
	}

	public function test_woocommerce_init() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$result = $object->woocommerce_init();
		$this->assertNull( $result );
	}

	public function test_maybe_process_queue() {
		$object = new Woorule_Alert();
		$this->assertInstanceOf( Woorule_Alert::class, new $object );

		$result = $object->maybe_process_queue();
		$this->assertNull( $result );
	}
}
