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

		$result = ProductAlert_API::put_product(
			array(
				'apikey'     => Woorule_Options::get_api_key(),
				'product_id' => $item['product_id'],
				'stock'      => $item['stock'],
			)
		);

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
}
