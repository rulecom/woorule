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
class ProductAlert_API_Test extends WC_Unit_Test_Case {
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

	public function test_create_alert() {
		$result = ProductAlert_API::create_alert(
			array(
				'apikey'     => $this->api_key,
				'product_id' => 1,
				'email'      => 'test@example.com',
				'tags'       => '',
			)
		);

		if ( $this->is_configured ) {
			$this->assertIsArray( $result );
		} else {
			$this->assertInstanceOf( WP_Error::class, $result );
		}
	}

	public function test_get_products() {
		$result = ProductAlert_API::get_products();
		if ( $this->is_configured ) {
			$this->assertIsArray( $result );
		} else {
			$this->assertInstanceOf( WP_Error::class, $result );
		}
	}

	public function test_put_product() {
		$result = ProductAlert_API::put_product(
			array(
				'apikey'     => $this->api_key,
				'product_id' => 1,
				'stock'      => 99,
			)
		);
		if ( $this->is_configured ) {
			$this->assertIsArray( $result );
		} else {
			$this->assertInstanceOf( WP_Error::class, $result );
		}
	}

	public function test_delete_product() {
		$result = ProductAlert_API::delete_product(
			array(
				'apikey'     => $this->api_key,
				'product_id' => 1,
			)
		);

		if ( $this->is_configured ) {
			$this->assertIsArray( $result );
		} else {
			$this->assertInstanceOf( WP_Error::class, $result );
		}
	}

	public function test_put_settings() {
		$result = ProductAlert_API::put_settings(
			array(
				'apikey' => 'wrong-key',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
	}

}
