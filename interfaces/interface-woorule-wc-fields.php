<?php

defined( 'ABSPATH' ) || exit;

/**
 * Interface Woorule_Rule_Fields
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
interface Woorule_WC_Fields {
	// Order fields
	const FIELD_ORDER_NUMBER    = 'order_number';
	const FIELD_ORDER_DATE      = 'order_date';
	const FIELD_PAYMENT_DATE    = 'payment_date';
	const FIELD_STATUS          = 'status';
	const FIELD_CURRENCY        = 'currency';
	const FIELD_TOTAL           = 'total';
	const FIELD_SUBTOTAL        = 'subtotal';
	const FIELD_SHIPPING        = 'shipping';
	const FIELD_TAX             = 'tax';
	const FIELD_DISCOUNT        = 'discount';
	const FIELD_PAYMENT_METHOD  = 'payment_method';
	const FIELD_SHIPPING_METHOD = 'shipping_method';
	const FIELD_TRANSACTION_ID  = 'transaction_id';
	const FIELD_SUBTOTAL_TAX    = 'subtotal_tax';
	const FIELD_SHIPPING_TAX    = 'shipping_tax';

	// Order note
	const FIELD_CUSTOMER_NOTE = 'customer_note';

	// Order address fields
	const FIELD_BILLING_FIRST_NAME   = 'billing_first_name';
	const FIELD_BILLING_LAST_NAME    = 'billing_last_name';
	const FIELD_BILLING_ADDRESS1     = 'billing_address_1';
	const FIELD_BILLING_ADDRESS2     = 'billing_address_2';
	const FIELD_BILLING_POSTCODE     = 'billing_postcode';
	const FIELD_BILLING_CITY         = 'billing_city';
	const FIELD_BILLING_STATE        = 'billing_state';
	const FIELD_BILLING_COUNTRY      = 'billing_country';
	const FIELD_BILLING_COUNTRY_CODE = 'billing_country_code';
	const FIELD_BILLING_EMAIL        = 'billing_email';
	const FIELD_BILLING_PHONE        = 'billing_phone';
	const FIELD_BILLING_COMPANY      = 'billing_company';
	const FIELD_ORDER_URL            = 'order_url';
	const FIELD_SOURCE               = 'source';

	// Order delivery address fields
	const FIELD_SHIPPING_FIRST_NAME   = 'shipping_first_name';
	const FIELD_SHIPPING_LAST_NAME    = 'shipping_last_name';
	const FIELD_SHIPPING_ADDRESS1     = 'shipping_address_1';
	const FIELD_SHIPPING_ADDRESS2     = 'shipping_address_2';
	const FIELD_SHIPPING_POSTCODE     = 'shipping_postcode';
	const FIELD_SHIPPING_CITY         = 'shipping_city';
	const FIELD_SHIPPING_STATE        = 'shipping_state';
	const FIELD_SHIPPING_COUNTRY      = 'shipping_country';
	const FIELD_SHIPPING_COUNTRY_CODE = 'shipping_country_code';
	const FIELD_SHIPPING_COMPANY      = 'shipping_company';

	// Customer
	const FIELD_USER_ID = 'user_id';

	// Virtual fields
	const FIELD_ORDER_NAMES      = 'order_names';
	const FIELD_ORDER_BRANDS     = 'order_brands';
	const FIELD_ORDER_TAGS       = 'order_tags';
	const FIELD_ORDER_PRODUCTS   = 'order_products';
	const FIELD_ORDER_CATEGORIES = 'order_categories';

	// Cart
	const FIELD_CART_URL = 'cart_url';
}
