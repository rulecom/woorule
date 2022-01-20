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
					'label'   => get_option( 'woocommerce_rulemailer_settings' )['woorule_checkout_label'],
				),
				1
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
	 */
	public function custom_checkout_field_update_order_meta( $order_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['woorule_opt_in'] ) ) {
			update_post_meta( $order_id, 'woorule_opt_in', 'true' );
		}
	}
}
