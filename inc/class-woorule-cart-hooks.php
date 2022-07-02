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
class Woorule_Cart_Hooks {
	/**
	 * Current customer.
	 *
	 * @var WC_Customer
	 */
	private $current_customer;

	/**
	 * Woorule_Order_Hooks constructor.
	 */
	public function __construct() {
		add_action(
			'woocommerce_add_to_cart',
			array( $this, 'cart_updated' )
		);
		add_action(
			'woocommerce_cart_item_removed',
			array( $this, 'cart_updated' )
		);
		add_filter(
			'woocommerce_update_cart_action_cart_updated',
			array( $this, 'filter_cart_updated' ),
			PHP_INT_MAX
		);
	}

	/**
	 * On triggered cart updated action.
	 *
	 * @param bool $cart_updated Is cart updated?.
	 *
	 * @return bool
	 */
	public function filter_cart_updated( $cart_updated ) {
		if ( $cart_updated ) {
			$this->cart_updated();
		}

		return $cart_updated;
	}

	/**
	 * Retrieve current logged in customer.
	 *
	 * @return int
	 */
	protected function retrieve_current_customer() {
		$current_user_id = get_current_user_id();

		if ( $current_user_id ) {
			try {
				$this->current_customer = new WC_Customer( $current_user_id );
			} catch ( Exception $e ) {
				$current_user_id = 0;
			}
		}

		return $current_user_id;
	}

	/**
	 * On cart updated.
	 *
	 * @return void
	 */
	public function cart_updated() {
		if ( ! $this->retrieve_current_customer() ) {
			return;
		}

		$email = $this->current_customer->get_billing_email();
		if ( empty( $email ) ) {
			$email = $this->current_customer->get_email();
		}

		WC()->cart->calculate_shipping();
		WC()->cart->calculate_fees();
		WC()->cart->calculate_totals();

		$builder = new Woorule_Builder_Cart_Fields();

		$subscription = array(
			'apikey'              => Woorule_Options::get_api_key(),
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'automation'          => 'reset',
			'async'               => true,
			'tags'                => $this->get_subscription_tags(),
			'subscribers'         => array(
				'email'        => $email,
				'phone_number' => Woorule_Utils::get_customer_phone_number( $this->current_customer ),
				'language'     => substr( get_locale(), 0, 2 ),
				'fields'       => $builder->get_rule_fields_values(),
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
	}

	/**
	 * Get subscription tags.
	 *
	 * @return array
	 */
	protected function get_subscription_tags() {
		return array( 'CartInProgress' );
	}
}
