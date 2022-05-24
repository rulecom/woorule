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
class WC_Rule_Logging extends WC_Unit_Test_Case {
	// @codingStandardsIgnoreEnd

	use Woorule_Logging;

	public function test_log() {
		if ( ! defined( 'WP_DEBUG' ) ) {
			define( 'WP_DEBUG', true );
		}

		$result = self::log( 'test' );
		$this->assertNull( $result );
	}

	public function test_http_api_debug() {
		$response = array(
			'body'     => '',
			'response' => array(
				'code' => 200
			)
		);

		$parsed_args = array(
			'method'  => 'GET',
			'body'    => '',
			'headers' => array()
		);

		$result = self::http_api_debug(
			$response,
			null,
			null,
			$parsed_args,
			'http://example.com'
		);

		$this->assertNull( $result );
	}
}
