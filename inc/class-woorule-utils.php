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
}
