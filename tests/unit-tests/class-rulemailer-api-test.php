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
class RuleMailer_API_Test extends WP_UnitTestCase {
	// @codingStandardsIgnoreEnd

	/**
	 * @var string
	 */
	private $api_key;

	/**
	 * @var bool
	 */
	private $is_configured = false;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->api_key = getenv( 'RULE_API_KEY' );

		if ( ! empty( $this->api_key ) ) {
			$this->is_configured = true;
		}
	}

	public function test_subscribe() {
		$subscription = array(
			'apikey'              => $this->api_key,
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'async'               => true,
			'tags'                => array(
				'test',
			),
			'subscribers'         => array(
				'email' => 'nobody@example.com',
			),
			'require_opt_in'      => true,
		);

		$result = RuleMailer_API::subscribe( $subscription );

		if ( $this->is_configured ) {
			$this->assertIsArray( $result );
		} else {
			$this->assertInstanceOf( WP_Error::class, $result );
		}
	}

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function test_delete_subscriber_tag() {
		$this->markTestSkipped('TODO: Use mocks');
		$result = RuleMailer_API::delete_subscriber_tag( 'nobody@example.com', 'test' );
		$this->assertIsArray( $result );
	}
}
