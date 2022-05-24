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
class Woorule_Options_Test extends WC_Unit_Test_Case {
	// @codingStandardsIgnoreEnd

	public function test_options() {
		$result = Woorule_Options::set_api_key( 'test' );
		$this->assertNull( $result );

		$result = Woorule_Options::get_api_key();
		$this->assertEquals( 'test', $result );

		$result = Woorule_Options::__callStatic( 'get_api_key', array() );
		$this->assertEquals( 'test', $result );

		$this->expectException( BadMethodCallException::class );
		Woorule_Options::__callStatic( 'wrong_method', array() );
	}
}
