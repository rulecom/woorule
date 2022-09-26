<?php
/**
 * Class Woorule_Order_Hooks
 *
 * @package Woorule
 */

/**
 * Class Woorule_Order_Hooks
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Woorule_Checkout {
	/**
	 * Woorule_Checkout constructor.
	 */
	public function __construct() {
		// newsletter subscribe button on checkout.
		add_action(
			'woocommerce_review_order_before_submit',
			array( $this, 'custom_checkout_field' )
		);
		add_action(
			'woocommerce_checkout_update_order_meta',
			array( $this, 'custom_checkout_field_update_order_meta' )
		);
	}

	/**
	 * Custom checkout field.
	 *
	 * @return void
	 */
	public function custom_checkout_field() {
		if ( Woorule_Options::get_checkout_show() === 'on' ) {
			echo '<div id="my_custom_checkout_field">';

			woocommerce_form_field(
				'woorule_opt_in',
				array(
					'type'    => 'checkbox',
					'default' => 'checked',
					'class'   => array( 'input-checkbox' ),
					'label'   => Woorule_Options::get_checkout_label(),
				),
				12
			);

			echo '</div>';
		}
	}

	/**
	 * Update order meta.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function custom_checkout_field_update_order_meta( $order_id ) {
		update_post_meta(
			$order_id,
			'woorule_opt_in',
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			empty( wc_clean( $_POST['woorule_opt_in'] ) ) ? '' : 'true'
		);
	}
}
