<?php
/**
 * Class Woorule_Klarna_Checkout_For_Woocommerce
 *
 * @package Woorule
 */

// phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType

/**
 * Class Woorule_Klarna_Checkout_For_Woocommerce
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
class Woorule_Klarna_Checkout_For_Woocommerce {
	/**
	 * Woorule_Klarna_Checkout_For_Woocommerce constructor.
	 */
	public function __construct() {
		if ( ! $this->is_klarna_checkout_activated() ) {
			return;
		}

		add_filter(
			'woorule_options_defaults',
			array( $this, 'add_options_defaults' )
		);

		add_action(
			'woorule_admin_settings_after_checkout',
			array( $this, 'admin_settings' )
		);

		add_filter(
			'woorule_update_options',
			array( $this, 'update_options' )
		);

		// Important!!! Should be after 'woorule_options_defaults' filter
		// because otherwise 'klarna_checkout_show' option will not be defined.
		if ( Woorule_Options::get_klarna_checkout_show() ) {
			add_filter(
				'kco_wc_api_request_args',
				array( $this, 'custom_checkout_field' )
			);

			add_action(
				'kco_wc_process_payment',
				array( $this, 'custom_checkout_field_update_order_meta' ),
				10,
				2
			);
		}
	}

	/**
	 * Check if Klarna Checkout for WooCommerce is activated.
	 *
	 * @return bool
	 */
	protected function is_klarna_checkout_activated() {
		return in_array(
			'klarna-checkout-for-woocommerce/klarna-checkout-for-woocommerce.php',
			(array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);
	}

	/**
	 * Add options defaults.
	 *
	 * @param array $options_defaults Options defaults.
	 *
	 * @return array
	 */
	public function add_options_defaults( $options_defaults ) {
		$options_defaults['woorule_klarna_checkout_show'] = '';

		return $options_defaults;
	}

	/**
	 * Render admin settings.
	 *
	 * @return void
	 */
	public function admin_settings() {
		load_template(
			WOORULE_PATH . 'inc/integrations/klarna-checkout-for-woocommerce/partials/admin-settings.php',
			true,
			array(
				'show' => Woorule_Options::get_klarna_checkout_show() ? Woorule_Options::get_klarna_checkout_show() : '',
			)
		);
	}

	/**
	 * Update options.
	 *
	 * @param array $options Options.
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function update_options( $options ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$options['woorule_klarna_checkout_show'] = isset( $_POST['woorule_klarna_checkout_show'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_klarna_checkout_show'] ) )
			: '';

		return $options;
	}

	/**
	 * Custom checkout field.
	 *
	 * @param array $request_body Request body.
	 *
	 * @return array
	 */
	public function custom_checkout_field( $request_body ) {
		$request_body['options']['additional_checkboxes'][] = array(
			'text'     => Woorule_Options::get_checkout_label(),
			'checked'  => false,
			'required' => false,
			'id'       => 'woorule_klarna_opt_in',
		);

		return $request_body;
	}

	/**
	 * Update order meta.
	 *
	 * @param int $order_id Order ID.
	 * @param array $klarna_order Klarna order data.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public function custom_checkout_field_update_order_meta( $order_id, $klarna_order ) {
		if (
			isset( $klarna_order['merchant_requested']['additional_checkboxes'] )
			&&
			is_array( $klarna_order['merchant_requested']['additional_checkboxes'] )
			&&
			! empty( $klarna_order['merchant_requested']['additional_checkboxes'] )
		) {
			$checkboxes = wp_list_pluck(
				$klarna_order['merchant_requested']['additional_checkboxes'],
				'checked',
				'id'
			);

			if (
				isset( $checkboxes['woorule_klarna_opt_in'] )
				&&
				$checkboxes['woorule_klarna_opt_in']
			) {
				update_post_meta( $order_id, 'woorule_opt_in', 'true' );
			} else {
				update_post_meta( $order_id, 'woorule_opt_in', '' );
			}
		}
	}
}
