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
class WC_Rule_Background_Alert_Queue extends WP_UnitTestCase {
	// @codingStandardsIgnoreEnd

	public function test_dispatch_queue() {
		$object = new Woorule_Background_Alert_Queue();
		$this->assertInstanceOf( Woorule_Background_Alert_Queue::class, new $object );

		$result = $object->dispatch_queue();
		$this->assertNull( $result );
	}
}
