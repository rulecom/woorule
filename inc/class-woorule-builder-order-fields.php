<?php

/**
 * Class Woorule_Builder_Order_Fields
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Woorule_Builder_Order_Fields implements Woorule_WC_Fields, Woorule_Rule_Fields {
	/**
	 * Get WC fields.
	 *
	 * @return array
	 */
	public function get_wc_fields() {
		return apply_filters(
			'woorule_wc_order_fields',
			array(
				// Order fields
				self::FIELD_ORDER_NUMBER          => __( 'Order number', 'woorule' ),
				self::FIELD_ORDER_DATE            => __( 'Order date', 'woorule' ),
				self::FIELD_PAYMENT_DATE          => __( 'Payment date', 'woorule' ),
				self::FIELD_STATUS                => __( 'Status', 'woorule' ),
				self::FIELD_CURRENCY              => __( 'Currency', 'woorule' ),
				self::FIELD_TOTAL                 => __( 'Total', 'woorule' ),
				self::FIELD_SUBTOTAL              => __( 'Subtotal', 'woorule' ),
				self::FIELD_SHIPPING              => __( 'Shipping', 'woorule' ),
				self::FIELD_TAX                   => __( 'Tax', 'woorule' ),
				self::FIELD_DISCOUNT              => __( 'Discount', 'woorule' ),
				self::FIELD_PAYMENT_METHOD        => __( 'Payment method', 'woorule' ),
				self::FIELD_SHIPPING_METHOD       => __( 'Shipping method', 'woorule' ),
				self::FIELD_TRANSACTION_ID        => __( 'Transaction ID', 'woorule' ),
				self::FIELD_SUBTOTAL_TAX          => __( 'Subtotal Tax', 'woorule' ),
				self::FIELD_SHIPPING_TAX          => __( 'Shipping Tax', 'woorule' ),

				// Order note
				self::FIELD_CUSTOMER_NOTE         => __( 'Customer note', 'woorule' ),

				// Order address fields
				self::FIELD_BILLING_FIRST_NAME    => __( 'Billing first name', 'woorule' ),
				self::FIELD_BILLING_LAST_NAME     => __( 'Billing last name', 'woorule' ),
				self::FIELD_BILLING_ADDRESS1      => __( 'Billing address 1', 'woorule' ),
				self::FIELD_BILLING_ADDRESS2      => __( 'Billing address 2', 'woorule' ),
				self::FIELD_BILLING_POSTCODE      => __( 'Billing postcode', 'woorule' ),
				self::FIELD_BILLING_CITY          => __( 'Billing city', 'woorule' ),
				self::FIELD_BILLING_STATE         => __( 'Billing state', 'woorule' ),
				self::FIELD_BILLING_COUNTRY       => __( 'Billing country', 'woorule' ),
				self::FIELD_BILLING_COUNTRY_CODE  => __( 'Billing country code', 'woorule' ),
				self::FIELD_BILLING_EMAIL         => __( 'Billing e-mail', 'woorule' ),
				self::FIELD_BILLING_PHONE         => __( 'Billing phone', 'woorule' ),
				self::FIELD_BILLING_COMPANY       => __( 'Billing company', 'woorule' ),
				self::FIELD_ORDER_URL             => __( 'Order Url', 'woorule' ),
				self::FIELD_SOURCE                => __( 'Subscriber source (WooRule)', 'woorule' ),

				// Order delivery address fields
				self::FIELD_SHIPPING_FIRST_NAME   => __( 'Shipping first name', 'woorule' ),
				self::FIELD_SHIPPING_LAST_NAME    => __( 'Shipping last name', 'woorule' ),
				self::FIELD_SHIPPING_ADDRESS1     => __( 'Shipping address 1', 'woorule' ),
				self::FIELD_SHIPPING_ADDRESS2     => __( 'Shipping address 2', 'woorule' ),
				self::FIELD_SHIPPING_POSTCODE     => __( 'Shipping postcode', 'woorule' ),
				self::FIELD_SHIPPING_CITY         => __( 'Shipping city', 'woorule' ),
				self::FIELD_SHIPPING_STATE        => __( 'Shipping state', 'woorule' ),
				self::FIELD_SHIPPING_COUNTRY      => __( 'Shipping country', 'woorule' ),
				self::FIELD_SHIPPING_COUNTRY_CODE => __( 'Shipping country code', 'woorule' ),
				self::FIELD_SHIPPING_COMPANY      => __( 'Shipping company', 'woorule' ),

				// Customer
				self::FIELD_USER_ID               => __( 'Customer ID', 'woorule' ),

				// Virtual fields
				self::FIELD_ORDER_NAMES           => __( 'Order names', 'woorule' ),
				self::FIELD_ORDER_BRANDS          => __( 'Order brands', 'woorule' ),
				self::FIELD_ORDER_TAGS            => __( 'Order tags', 'woorule' ),
				self::FIELD_ORDER_PRODUCTS        => __( 'Order products', 'woorule' ),
				self::FIELD_ORDER_CATEGORIES      => __( 'Order categories', 'woorule' ),
			)
		);
	}

	/**
	 * Get Rule order fields.
	 *
	 * @return array Key => type
	 */
	public function get_rule_fields_types() {
		$fields = $this->get_rule_default_fields();

		// Get default fields and fill them as "string"
		$default = array_combine(
			array_keys( $fields ),
			array_fill( 0, count( $fields ), self::TYPE_STRING )
		);

		$types = array_merge(
			$default,
			array(
				self::RULE_ORDER_PAYMENT_METHOD  => self::TYPE_MULTIPLE,
				self::RULE_ORDER_DELIVERY_METHOD => self::TYPE_MULTIPLE,
				self::RULE_ORDER_NAMES           => self::TYPE_MULTIPLE,
				self::RULE_ORDER_BRANDS          => self::TYPE_MULTIPLE,
				self::RULE_ORDER_TAGS            => self::TYPE_JSON,
				self::RULE_ORDER_PRODUCTS        => self::TYPE_JSON,
				self::RULE_ORDER_COLLECTIONS     => self::TYPE_MULTIPLE,

			)
		);

		return apply_filters(
			'woorule_order_fields_types',
			$types
		);
	}

	/**
	 * Get default assigns for Rule order.
	 *
	 * @return array Key => type
	 */
	public function get_rule_default_fields() {
		return apply_filters(
			'woorule_order_fields_default_assigns',
			array(
				// Subscriber
				self::RULE_SUBSCRIBER_FIRST_NAME    => self::FIELD_BILLING_FIRST_NAME,
				self::RULE_SUBSCRIBER_LAST_NAME     => self::FIELD_BILLING_LAST_NAME,
				self::RULE_SUBSCRIBER_NUMBER        => self::FIELD_USER_ID,
				self::RULE_SUBSCRIBER_STREET1       => self::FIELD_BILLING_ADDRESS1,
				self::RULE_SUBSCRIBER_STREET2       => self::FIELD_BILLING_ADDRESS2,
				self::RULE_SUBSCRIBER_CITY          => self::FIELD_BILLING_CITY,
				self::RULE_SUBSCRIBER_ZIPCODE       => self::FIELD_BILLING_POSTCODE,
				self::RULE_SUBSCRIBER_STATE         => self::FIELD_BILLING_STATE,
				self::RULE_SUBSCRIBER_COUNTRY       => self::FIELD_BILLING_COUNTRY,
				self::RULE_SUBSCRIBER_COMPANY       => self::FIELD_BILLING_COMPANY,
				self::RULE_SUBSCRIBER_SOURCE        => self::FIELD_SOURCE,

				// Order
				self::RULE_ORDER_NUMBER             => self::FIELD_ORDER_NUMBER,
				self::RULE_ORDER_DATE               => self::FIELD_ORDER_DATE,
				self::RULE_ORDER_SUBTOTAL           => self::FIELD_SUBTOTAL,
				self::RULE_ORDER_DISCOUNT           => self::FIELD_DISCOUNT,
				self::RULE_ORDER_SHIPPING           => self::FIELD_SHIPPING,
				self::RULE_ORDER_TOTAL              => self::FIELD_TOTAL,
				self::RULE_ORDER_VAT                => self::FIELD_TAX,
				self::RULE_ORDER_SUBTOTAL_VAT       => self::FIELD_SUBTOTAL_TAX,
				self::RULE_ORDER_SHIPPING_VAT       => self::FIELD_SHIPPING_TAX,
				self::RULE_ORDER_CURRENCY           => self::FIELD_CURRENCY,
				self::RULE_ORDER_PAYMENT_METHOD     => self::FIELD_PAYMENT_METHOD,
				self::RULE_ORDER_DELIVERY_METHOD    => self::FIELD_SHIPPING_METHOD,

				self::RULE_ORDER_BILLING_FIRST_NAME => self::FIELD_BILLING_FIRST_NAME,
				self::RULE_ORDER_BILLING_LAST_NAME  => self::FIELD_BILLING_LAST_NAME,
				self::RULE_ORDER_BILLING_STREET     => self::FIELD_BILLING_ADDRESS1,
				self::RULE_ORDER_BILLING_CITY       => self::FIELD_BILLING_CITY,
				self::RULE_ORDER_BILLING_ZIPCODE    => self::FIELD_BILLING_POSTCODE,
				self::RULE_ORDER_BILLING_STATE      => self::FIELD_BILLING_STATE,
				self::RULE_ORDER_BILLING_COUNTRY    => self::FIELD_BILLING_COUNTRY_CODE,
				self::RULE_ORDER_BILLING_TELE       => self::FIELD_BILLING_PHONE,
				self::RULE_ORDER_BILLING_COMPANY    => self::FIELD_BILLING_COMPANY,
				self::RULE_ORDER_ORDER_URL          => self::FIELD_ORDER_URL,

				// Virtual
				self::RULE_ORDER_NAMES              => self::FIELD_ORDER_NAMES,
				self::RULE_ORDER_BRANDS             => self::FIELD_ORDER_BRANDS,
				self::RULE_ORDER_TAGS               => self::FIELD_ORDER_TAGS,
				self::RULE_ORDER_PRODUCTS           => self::FIELD_ORDER_PRODUCTS,
				self::RULE_ORDER_COLLECTIONS        => self::FIELD_ORDER_CATEGORIES,
			)
		);
	}

	/**
	 * Get Order field value.
	 *
	 * @param $wc_field
	 * @param WC_Order $order
	 *
	 * @return mixed
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function get_field_value( $wc_field, WC_Order $order ) {
		switch ( $wc_field ) {
			// Order fields
			case self::FIELD_ORDER_NUMBER:
				return $order->get_order_number();
			case self::FIELD_ORDER_DATE:
				$order_date = '';

				if ( $order->has_status( 'processing' ) ) {
					$order_date = date_format( $order->get_date_created(), 'Y-m-d H:i:s' );
				} elseif ( $order->has_status( 'completed' ) ) {
					$order_date = date_format( $order->get_date_completed(), 'Y-m-d H:i:s' );
				}

				return $order_date;
			case self::FIELD_PAYMENT_DATE:
				if ( $order->is_paid() ) {
					return $order->get_date_paid();
				}

				return '';
			case self::FIELD_TOTAL:
				return Woorule_Utils::round( $order->get_total() );
			case self::FIELD_SUBTOTAL:
				return Woorule_Utils::round( $order->get_subtotal() );
			case self::FIELD_SHIPPING:
				return Woorule_Utils::round( $order->get_shipping_total() );
			case self::FIELD_TAX:
				return Woorule_Utils::round( $order->get_total_tax() );
			case self::FIELD_DISCOUNT:
				return Woorule_Utils::round( $order->get_total_discount() );

			case self::FIELD_USER_ID:
			case self::FIELD_STATUS:
			case self::FIELD_CURRENCY:
			case self::FIELD_PAYMENT_METHOD:
			case self::FIELD_SHIPPING_METHOD:
			case self::FIELD_TRANSACTION_ID:
			case self::FIELD_CUSTOMER_NOTE:
			case self::FIELD_BILLING_FIRST_NAME:
			case self::FIELD_BILLING_LAST_NAME:
			case self::FIELD_BILLING_ADDRESS1:
			case self::FIELD_BILLING_ADDRESS2:
			case self::FIELD_BILLING_POSTCODE:
			case self::FIELD_BILLING_CITY:
			case self::FIELD_BILLING_STATE:
			case self::FIELD_BILLING_COMPANY:
			case self::FIELD_SHIPPING_FIRST_NAME:
			case self::FIELD_SHIPPING_LAST_NAME:
			case self::FIELD_SHIPPING_ADDRESS1:
			case self::FIELD_SHIPPING_ADDRESS2:
			case self::FIELD_SHIPPING_POSTCODE:
			case self::FIELD_SHIPPING_CITY:
			case self::FIELD_SHIPPING_STATE:
			case self::FIELD_SHIPPING_COMPANY:
				return call_user_func_array( array( $order, 'get_' . $wc_field ), array() );
			case self::FIELD_BILLING_COUNTRY_CODE:
				return $order->get_billing_country();
			case self::FIELD_BILLING_COUNTRY:
				$country   = $order->get_billing_country();
				$countries = WC()->countries->get_countries();

				return isset( $countries[ $country ] ) ? $countries[ $country ] : $country;
			case self::FIELD_SHIPPING_COUNTRY_CODE:
				return $order->get_shipping_country();
			case self::FIELD_SHIPPING_COUNTRY:
				$country   = $order->get_shipping_country();
				$countries = WC()->countries->get_countries();

				return isset( $countries[ $country ] ) ? $countries[ $country ] : $country;
			case self::FIELD_ORDER_NAMES:
				$names = array();
				$items = $order->get_items();
				foreach ( $items as $item ) {
					$names[] = $item->get_name();
				}

				return $names;
			case self::FIELD_BILLING_PHONE:
				return Woorule_Utils::get_order_phone_number( $order );
			case self::FIELD_SUBTOTAL_TAX:
				return Woorule_Utils::round( $order->get_subtotal() + $order->get_cart_tax() );
			case self::FIELD_SHIPPING_TAX:
				return Woorule_Utils::round( $order->get_shipping_total() + $order->get_shipping_tax() );
			case self::FIELD_SOURCE:
				return 'WooRule';
			case self::FIELD_ORDER_URL:
				return $order->get_view_order_url();
			case self::FIELD_ORDER_BRANDS:
				$brands = array();
				$items  = $order->get_items();
				foreach ( $items as $item ) {
					$brands[] = $item->get_product()->get_attribute( 'brand' );
				}

				return array_unique( $brands );
			case self::FIELD_ORDER_TAGS:
				$tags  = array();
				$items = $order->get_items();
				foreach ( $items as $item ) {
					$tags_string = wp_strip_all_tags( wc_get_product_tag_list( $item->get_product_id() ) );
					if ( $tags_string ) {
						$tags = array_unique( array_merge( $tags, explode( ',', $tags_string ) ) );
					}
				}

				return $tags;
			case self::FIELD_ORDER_CATEGORIES:
				$categories = array();
				$items      = $order->get_items();
				foreach ( $items as $item ) {
					$categories_string = wp_strip_all_tags( wc_get_product_category_list( $item->get_product_id() ) );
					if ( $categories_string ) {
						$categories = array_unique(
							array_merge( $categories, explode( ',', $categories_string ) )
						);
					}
				}

				return $categories;
			case self::FIELD_ORDER_PRODUCTS:
				$products = array();
				$items    = $order->get_items();
				foreach ( $items as $item ) {
					$product = $item->get_product();
					$image   = wp_get_attachment_image_src( $product->get_image_id(), 'full' );

					if ( $image ) {
						$image = array_shift( $image );
					} else {
						$image = wc_placeholder_img_src( 'full' );
					}

					$qty            = $item->get_quantity();
					$price_excl_tax = $order->get_line_subtotal( $item, false, false );
					$price_incl_tax = $order->get_line_subtotal( $item, true, false );

					$products[] = array(
						'brand'     => $product->get_attribute( 'brand' ),
						'name'      => $item->get_name(),
						'image'     => $image,
						'price'     => Woorule_Utils::round( $price_excl_tax / $qty ),
						'price_vat' => Woorule_Utils::round( $price_incl_tax / $qty ),
						'vat'       => Woorule_Utils::round( $price_incl_tax - $price_excl_tax ),
						'qty'       => $qty,
						'subtotal'  => Woorule_Utils::round( $item->get_total() ),
						'total'     => Woorule_Utils::round( $price_incl_tax ),
					);
				}

				return $products;
			default:
				return null;
		}
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_rule_fields_values( WC_Order $order ) {
		$fields        = $this->load_fields();
		$fields_status = $this->load_fields_status();
		$types         = $this->get_rule_fields_types();

		$result = array();
		foreach ( $fields as $rule_field => $wc_field ) {
			if ( ! $fields_status[ $rule_field ] ) {
				continue;
			}

			$type  = $types[ $rule_field ];
			$value = $this->get_field_value( $wc_field, $order );

			switch ( $type ) {
				case self::TYPE_STRING:
					$result[] = array(
						'key'   => $rule_field,
						'value' => $value,
					);

					break;
				case self::TYPE_DATETIME:
				case self::TYPE_MULTIPLE:
					$result[] = array(
						'key'   => $rule_field,
						'value' => $value,
						'type'  => $type,
					);

					break;
				case self::TYPE_JSON:
					$result[] = array(
						'key'   => $rule_field,
						'value' => wp_json_encode( $value ),
						'type'  => $type,
					);
			}
		}

		return $result;
	}

	/**
	 * Load Fields.
	 *
	 * @return array
	 */
	public function load_fields() {
		$default_fields = $this->get_rule_default_fields();
		$fields         = (array) json_decode( get_option( 'rule_order_fields', '[]' ), true );

		return array_merge( $default_fields, $fields );
	}

	/**
	 * Load Fields Status.
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.LongVariable)
	 */
	public function load_fields_status() {
		$default_fields        = $this->get_rule_default_fields();
		$default_fields_status = array_combine(
			array_keys( $default_fields ),
			array_fill( 0, count( $default_fields ), '1' )
		);
		$fields_status         = (array) json_decode( get_option( 'rule_order_fields_status', '[]' ), true );

		return array_merge( $default_fields_status, $fields_status );
	}
}
