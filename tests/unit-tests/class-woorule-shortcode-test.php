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
class Woorule_Shortcode_Test extends WC_Unit_Test_Case {
    // @codingStandardsIgnoreEnd

	public function test_output() {
		$object = new Woorule_Shortcode();

		$result = $object->output(
			array(
				'title' => 'Newsletter test',
			)
		);
		$this->assertIsString( $result );
		$this->assertStringContainsString( 'Newsletter test', $result );
	}
}
