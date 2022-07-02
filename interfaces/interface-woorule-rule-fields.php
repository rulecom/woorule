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
interface Woorule_Rule_Fields {
	// Subscriber
	const RULE_SUBSCRIBER_FIRST_NAME = 'Subscriber.FirstName';
	const RULE_SUBSCRIBER_LAST_NAME  = 'Subscriber.LastName';
	const RULE_SUBSCRIBER_NUMBER     = 'Subscriber.Number';
	const RULE_SUBSCRIBER_STREET1    = 'Subscriber.Street1';
	const RULE_SUBSCRIBER_STREET2    = 'Subscriber.Street2';
	const RULE_SUBSCRIBER_CITY       = 'Subscriber.City';
	const RULE_SUBSCRIBER_ZIPCODE    = 'Subscriber.Zipcode';
	const RULE_SUBSCRIBER_STATE      = 'Subscriber.State';
	const RULE_SUBSCRIBER_COUNTRY    = 'Subscriber.Country';
	const RULE_SUBSCRIBER_COMPANY    = 'Subscriber.Company';
	const RULE_SUBSCRIBER_SOURCE     = 'Subscriber.Source';

	// Order
	const RULE_ORDER_NUMBER          = 'Order.Number';
	const RULE_ORDER_DATE            = 'Order.Date';
	const RULE_ORDER_SUBTOTAL        = 'Order.Subtotal';
	const RULE_ORDER_DISCOUNT        = 'Order.Discount';
	const RULE_ORDER_SHIPPING        = 'Order.Shipping';
	const RULE_ORDER_TOTAL           = 'Order.Total';
	const RULE_ORDER_VAT             = 'Order.Vat';
	const RULE_ORDER_SUBTOTAL_VAT    = 'Order.SubtotalVat';
	const RULE_ORDER_SHIPPING_VAT    = 'Order.ShippingVat';
	const RULE_ORDER_CURRENCY        = 'Order.Currency';
	const RULE_ORDER_PAYMENT_METHOD  = 'Order.PaymentMethod';
	const RULE_ORDER_DELIVERY_METHOD = 'Order.DeliveryMethod';

	// Virtual fields
	const RULE_ORDER_NAMES       = 'Order.Names';
	const RULE_ORDER_BRANDS      = 'Order.Brands';
	const RULE_ORDER_COLLECTIONS = 'Order.Collections';
	const RULE_ORDER_TAGS        = 'Order.Tags';
	const RULE_ORDER_PRODUCTS    = 'Order.Products';

	// Order address fields
	const RULE_ORDER_BILLING_FIRST_NAME = 'Order.BillingFirstname';
	const RULE_ORDER_BILLING_LAST_NAME  = 'Order.BillingLastname';
	const RULE_ORDER_BILLING_STREET     = 'Order.BillingStreet';
	const RULE_ORDER_BILLING_CITY       = 'Order.BillingCity';
	const RULE_ORDER_BILLING_ZIPCODE    = 'Order.BillingZipcode';
	const RULE_ORDER_BILLING_STATE      = 'Order.BillingState';
	const RULE_ORDER_BILLING_COUNTRY    = 'Order.BillingCountry';
	const RULE_ORDER_BILLING_TELE       = 'Order.BillingTele';
	const RULE_ORDER_BILLING_COMPANY    = 'Order.BillingCompany';
	const RULE_ORDER_ORDER_URL          = 'Order.OrderUrl';

	// Cart
	const RULE_ORDER_CART_URL = 'Order.CartUrl';

	const TYPE_STRING   = 'string';
	const TYPE_DATETIME = 'datetime';
	const TYPE_JSON     = 'json';
	const TYPE_MULTIPLE = 'multiple';

	/**
	 * Get WC Fields.
	 *
	 * @return array
	 */
	public function get_wc_fields();

	/**
	 * Get Rule fields.
	 *
	 * @return array Key => type
	 */
	public function get_rule_fields_types();

	/**
	 * Get default assigns for Rule fields.
	 *
	 * @return array Key => type
	 */
	public function get_rule_default_fields();

	/**
	 * Load Fields.
	 *
	 * @return array
	 */
	public function load_fields();

	/**
	 * Load Fields Status.
	 *
	 * @return array
	 */
	public function load_fields_status();
}
