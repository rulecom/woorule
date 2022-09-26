<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once WC_ABSPATH . '/includes/abstracts/class-wc-background-process.php';
}

/**
 * WC_Background_Woorule_Alert_Queue class.
 *
 * @package WooRule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Background_Alert_Queue extends WC_Background_Process {
	/**
	 * @var WC_Logger
	 */
	private $logger;

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		$this->logger = wc_get_logger();

		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_woorule_alert_queue';

		// Dispatch queue after shutdown.
		add_action( 'shutdown', array( $this, 'dispatch_queue' ), 100 );

		parent::__construct();
	}

	/**
	 * Schedule fallback event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event(
				time() + MINUTE_IN_SECONDS,
				$this->cron_interval_identifier,
				$this->cron_hook_identifier
			);
		}
	}

	/**
	 * Log message.
	 *
	 * @param $message
	 */
	private function log( $message ) {
		$this->logger->info( $message, array( 'source' => $this->action ) );
	}

	/**
	 * Code to execute for each item in the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		$this->log( sprintf( 'Start task: %s', var_export( $item, true ) ) );

		$product = wc_get_product( $item['product_id'] );
		if ( ! $product ) {
			// Remove from queue
			return false;
		}

		// Check for pending alerts
		$products = ProductAlert_API::get_products();
		if ( ! is_wp_error( $products ) ) {
			$pending_products = array_column( $products['products'], 'product_id' );
			if ( ! in_array( $item['product_id'], $pending_products ) ) { //phpcs:ignore
				$this->log( sprintf( 'Product alert is not pending: %s', $item['product_id'] ) );

				// Remove from queue
				return false;
			}
		}

		$params = array(
			'apikey'     => Woorule_Options::get_api_key(),
			'product_id' => $item['product_id'],
			'stock'      => $item['stock'],
			'fields'     => $this->get_product_fields( $product ),
		);

		$tags = Woorule_Options::get_alert_product_tags();
		if ( ! empty( $tags ) ) {
			$params['alert_tags'] = explode( ',', $tags );
		}

		$result = ProductAlert_API::put_product( $params );

		if ( is_wp_error( $result ) ) {
			/** @var WP_Error $result */
			$this->log( sprintf( '[ERROR]: %s', $result->get_error_message() ) );

			// Remove from queue
			return false;
		}

		$this->log( sprintf( 'End task: %s', var_export( $result, true ) ) );

		// Remove from queue
		return false;
	}

	/**
	 * This runs once the job has completed all items on the queue.
	 *
	 * @return void
	 */
	protected function complete() {
		parent::complete();

		$this->log( 'Completed ' . $this->action . ' queue job.' );
	}

	/**
	 * Save and run queue.
	 */
	public function dispatch_queue() {
		if ( ! empty( $this->data ) ) {
			$this->save()->dispatch();
		}
	}

	/**
	 * Get Product Fields.
	 *
	 * @param WC_Product $product
	 * @return array[]
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	private function get_product_fields( WC_Product $product ) {
		$data                      = array();
		$data['sku']               = $product->get_sku();
		$data['name']              = $product->get_name();
		$data['description']       = $product->get_description();
		$data['short_description'] = $product->get_short_description();
		$data['url']               = $product->get_permalink();
		$data['price']             = $product->get_price();
		$data['regular_price']     = $product->get_regular_price();
		$data['sale_price']        = $product->get_sale_price();
		$data['tax_status']        = $product->get_tax_status();
		$data['tax_class']         = $product->get_tax_class();

		$image = wp_get_attachment_image_src( $product->get_image_id(), 'full' );
		if ( $image ) {
			$data['image'] = array_shift( $image );
		} else {
			$data['image'] = wc_placeholder_img_src( 'full' );
		}

		$result = array();
		foreach ( $data as $key => $value ) {
			$result[] = array(
				'key'   => $key,
				'value' => $value,
			);
		}

		// Add attributes
		if ( 'variation' === $product->get_type() ) {
			/** @var WC_Product_Variation $product */

			$attributes = wc_get_product_variation_attributes( $product->get_id() );
			foreach ( $attributes as $key => $value ) {
				$attribute = str_replace( 'attribute_', '', $key );

				$result[] = array(
					'key'   => wc_attribute_label( $attribute ),
					'value' => $product->get_attribute( $attribute ),
				);
			}
		}

		return $result;
	}
}
