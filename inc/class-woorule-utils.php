<?php
/**
 * Utils class.
 *
 * @package WooRule
 */

/**
 * Utils class.
 *
 * @package WooRule
 */
class Woorule_Utils {
	/**
	 * Round price values to store's currency decimal setting.
	 *
	 * @param float|int|string $value Value.
	 *
	 * @return string
	 */
	public static function round( $value ) {
		return number_format( (float) $value, wc_get_price_decimals(), '.', '' );
	}

    /**
     * Get and format phone number.
     *
     * @param WC_Order $order
     * @return string
     */
    public static function get_order_phone_number( WC_Order $order ) {
        $phone = preg_replace( '/[^0-9\+]/', '', $order->get_billing_phone() );

        if ( '+' !== substr( $phone, 0, 1 ) ) {
            $code = WC()->countries->get_country_calling_code( $order->get_billing_country() );

            return '+' . $code . $phone;
        }

        return $phone;
    }

    /**
     * Get and format customer's phone number.
     *
     * @param WC_Customer $customer
     * @return string
     */
    public static function get_customer_phone_number( WC_Customer $customer ) {
        $phone = preg_replace( '/[^0-9\+]/', '', $customer->get_billing_phone() );

        if ( '+' !== substr( $phone, 0, 1 ) ) {
            $code = WC()->countries->get_country_calling_code( $customer->get_billing_country() );

            return '+' . $code . $phone;
        }

        return $phone;
    }
}
