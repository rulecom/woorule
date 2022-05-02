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
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.MissingImport)
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
		return self::add_phone_calling_code(
			$order->get_billing_phone(),
			$order->get_billing_country()
		);
	}

	/**
	 * Get and format customer's phone number.
	 *
	 * @param WC_Customer $customer
	 * @return string
	 */
	public static function get_customer_phone_number( WC_Customer $customer ) {
		return self::add_phone_calling_code(
			$customer->get_billing_phone(),
			$customer->get_billing_country()
		);
	}

	/**
	 * Format phone number.
	 *
	 * @param string $phone
	 * @param string $country
	 * @return string
	 */
	private static function add_phone_calling_code( $phone, $country ) {
		$phone = preg_replace( '/[^0-9\+]/', '', $phone );

		if ( '+' !== substr( $phone, 0, 1 ) ) {
			// Check for a calling code
			$code = ltrim( WC()->countries->get_country_calling_code( $country ), '+' );

			if ( substr( $phone, 0, strlen( $code ) ) !== $code ) {
				$phone = $code . $phone;
			}

			return '+' . $phone;
		}

		return $phone;
	}
}
