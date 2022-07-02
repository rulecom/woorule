<?php
/**
 * Class Woorule_Order_Hooks
 *
 * @package Woorule
 */

// phpcs:disable Generic.Formatting.MultipleStatementAlignment.NotSameWarning
// phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType

/**
 * Class Woorule_Order_Hooks
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Order_Hooks {
	const ALLOWED_STATUSES = array( 'processing', 'completed', 'shipped' );
	// This array lists all the event triggers that will trigger data transfer to Rule
	// The following array is a list of all the possible default order event triggers:
	// [ 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' ]
	// Note that all active event triggers must have an associated tag name defined in the $custom_tags array.

	/**
	 * Woorule_Order_Hooks constructor.
	 */
	public function __construct() {
		add_action(
			'woocommerce_order_status_changed',
			array( $this, 'order_status_changed' ),
			10,
			3
		);
	}

	/**
	 * On order status changed.
	 *
	 * @param int $order_id Order ID.
	 * @param string $status_from Status "from".
	 * @param string $status_to Status "to".
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function order_status_changed( $order_id, $status_from, $status_to ) {
		if ( ! in_array( $status_to, self::ALLOWED_STATUSES, true ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		$builder = new Woorule_Builder_Order_Fields();

		$subscription = array(
			'apikey'              => Woorule_Options::get_api_key(),
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'automation'          => 'reset',
			'async'               => true,
			'tags'                => $this->get_subscription_tags( $order, $status_to ),
			'subscribers'         => array(
				'email'        => $order->get_billing_email(),
				'phone_number' => Woorule_Utils::get_order_phone_number( $order ),
				'language'     => substr( get_locale(), 0, 2 ),
				'fields'       => $builder->get_rule_fields_values( $order ),
			),
		);

		$result = RuleMailer_API::subscribe( $subscription );
		if ( is_wp_error( $result ) && 400 === $result->get_error_code() ) {
			// New attempt without phone number
			unset( $subscription['subscribers']['phone_number'] );

			// Remove phone number from fields
			if ( isset( $subscription['subscribers'] ) && isset( $subscription['subscribers']['fields'] ) ) {
				$fields = $subscription['subscribers']['fields'];

				foreach ( $fields as $key => $field ) {
					if ( 'Order.BillingTele' === $field['key'] ) {
						unset( $fields[ $key ] );
					}
				}

				$subscription['subscribers']['fields'] = $fields;
			}

			RuleMailer_API::subscribe( $subscription );
		}

		if ( ! $order->meta_exists( '_cart_in_progress_deleted' ) ) {
			RuleMailer_API::delete_subscriber_tag( $order->get_billing_email(), 'CartInProgress' );
			$order->add_meta_data( '_cart_in_progress_deleted', true, true );
			$order->save();
		}
	}

	/**
	 * Get subscription tags.
	 *
	 * @param WC_Order $order Order.
	 * @param string $status_to Status "to".
	 *
	 * @return array
	 */
	protected function get_subscription_tags( $order, $status_to ) {
		// Here you can define the tag names that are applied to a subscriber upon an event trigger.
		// The format is "eventName" => "tagName".
		// Note that all active event triggers MUST have a tag name associated with it.
		$custom_tags = array(
			'processing' => 'OrderProcessing',
			'completed'  => 'OrderCompleted',
			'shipped'    => 'OrderShipped',
		);

		$tags = array();

		if ( isset( $custom_tags[ $status_to ] ) ) {
			$tags[] = $custom_tags[ $status_to ];
		}

		if ( 'true' === $order->get_meta( 'woorule_opt_in' ) ) {
			$tags[] = 'Newsletter'; // Check for a newsletter (checkout) chekbox.
		}

		$tags = array_unique( $tags ); // API will give an error on duplicate tags. Making sure there won't be any.

		if ( empty( $tags ) ) {
			$tags[] = 'WooRule';
		} // Making sure the tags array will never be empty as the API will not like this.

		return $tags;
	}
}
