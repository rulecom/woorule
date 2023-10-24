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
class Woorule_Order_Hooks {
	const ALLOWED_STATUSES = array( 'processing', 'completed', 'shipped' );
	// This array lists all the event triggers that will trigger data transfer to Rule
	// The following array is a list of all the possible default order event triggers:
	// [ 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' ]
	// Note that all active event triggers must have an associated tag name defined in the $custom_tags array.

	/**
	 * Woorule_Order_Hooks constructor.
	 */
	public function __construct() {
		add_action(
			'woocommerce_order_status_changed',
			array( $this, 'order_status_changed' ),
			10,
			3
		);
	}

	/**
	 * On order status changed.
	 *
	 * @param int $order_id Order ID.
	 * @param string $status_from Status "from".
	 * @param string $status_to Status "to".
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function order_status_changed( $order_id, $status_from, $status_to ) {
		if ( ! in_array( $status_to, self::ALLOWED_STATUSES, true ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		$subscription = array(
			'apikey'              => Woorule_Options::get_api_key(),
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'automation'          => 'reset',
			'async'               => true,
			'tags'                => $this->get_subscription_tags( $order, $status_to ),
			'subscribers'         => array(
				'email'        => $order->get_billing_email(),
				'phone_number' => Woorule_Utils::get_order_phone_number( $order ),
				'language'     => substr( get_locale(), 0, 2 ),
				'fields'       => array_merge(
					$this->get_subscriber_fields( $order ),
					$this->get_order_fields( $order, $status_to ),
					$this->get_order_items_fields( $order )
				),
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

		if ( ! $order->meta_exists( '_cart_in_progress_deleted' ) ) {
			RuleMailer_API::delete_subscriber_tag( $order->get_billing_email(), 'CartInProgress' );
			$order->add_meta_data( '_cart_in_progress_deleted', true, true );
			$order->save();
		}
	}

	/**
	 * Get subscription tags.
	 *
	 * @param WC_Order $order Order.
	 * @param string $status_to Status "to".
	 *
	 * @return array
	 */
	protected function get_subscription_tags( $order, $status_to ) {
		// Here you can define the tag names that are applied to a subscriber upon an event trigger.
		// The format is "eventName" => "tagName".
		// Note that all active event triggers MUST have a tag name associated with it.
		$custom_tags = array(
			'processing' => 'OrderProcessing',
			'completed'  => 'OrderCompleted',
			'shipped'    => 'OrderShipped',
		);

		$tags = array();

		if ( isset( $custom_tags[ $status_to ] ) ) {
			$tags[] = $custom_tags[ $status_to ];
		}

		if ( 'true' === $order->get_meta( 'woorule_opt_in' ) ) {
			$tags[] = 'Newsletter'; // Check for a newsletter (checkout) chekbox.
		}

		$tags = array_unique( $tags ); // API will give an error on duplicate tags. Making sure there won't be any.

		if ( empty( $tags ) ) {
			$tags[] = 'WooRule';
		} // Making sure the tags array will never be empty as the API will not like this.

		return $tags;
	}

	/**
	 * Get subscriber fields.
	 *
	 * @param WC_Order $order Order.
	 *
	 * @return array
	 */
	protected function get_subscriber_fields( $order ) {
		return array(
			array(
				'key'   => 'Subscriber.FirstName',
				'value' => $order->get_billing_first_name(),
			),
			array(
				'key'   => 'Subscriber.LastName',
				'value' => $order->get_billing_last_name(),
			),
			array(
				'key'   => 'Subscriber.Number',
				'value' => $order->get_user_id(),
			),
			array(
				'key'   => 'Subscriber.Street1',
				'value' => $order->get_billing_address_1(),
			),
			array(
				'key'   => 'Subscriber.Street2',
				'value' => $order->get_billing_address_2(),
			),
			array(
				'key'   => 'Subscriber.City',
				'value' => $order->get_billing_city(),
			),
			array(
				'key'   => 'Subscriber.Zipcode',
				'value' => $order->get_billing_postcode(),
			),
			array(
				'key'   => 'Subscriber.State',
				'value' => $order->get_billing_state(),
			),
			array(
				'key'   => 'Subscriber.Country',
				'value' => $order->get_billing_country(),
			),
			array(
				'key'   => 'Subscriber.Company',
				'value' => $order->get_billing_company(),
			),
			array(
				'key'   => 'Subscriber.Source',
				'value' => 'WooRule',
			),
		);
	}

	/**
	 * Get order fields.
	 *
	 * @param WC_Order $order Order.
	 * @param string $status_to Status "to".
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function get_order_fields( $order, $status_to ) {
		switch ( $status_to ) {
			case 'processing':
				$date       = $order->get_date_created();
				$order_date = date_format( $date ? $date : new \DateTime(), 'Y-m-d H:i:s' );
				break;
			case 'completed':
				$date       = $order->get_date_completed();
				$order_date = date_format( $date ? $date : new \DateTime(), 'Y-m-d H:i:s' );
				break;
			default:
				$order_date = '';
		}

		return array(
			array(
				'key'   => 'Order.Number',
				'value' => $order->get_order_number(),
			),
			array(
				'key'   => 'Order.Date',
				'value' => $order_date,
				'type'  => 'datetime',
			),
			array(
				'key'   => 'Order.Subtotal',
				'value' => Woorule_Utils::round( $order->get_subtotal() ),
			),
			array(
				'key'   => 'Order.SubtotalVat',
				'value' => Woorule_Utils::round( $order->get_subtotal() + $order->get_cart_tax() ),
			),
			array(
				'key'   => 'Order.Discount',
				'value' => Woorule_Utils::round( $order->get_total_discount() ),
			),
			array(
				'key'   => 'Order.Shipping',
				'value' => Woorule_Utils::round( $order->get_shipping_total() ),
			),
			array(
				'key'   => 'Order.ShippingVat',
				'value' => Woorule_Utils::round( $order->get_shipping_total() + $order->get_shipping_tax() ),
			),
			array(
				'key'   => 'Order.Total',
				'value' => Woorule_Utils::round( $order->get_total() ),
			),
			array(
				'key'   => 'Order.Vat',
				'value' => Woorule_Utils::round( $order->get_total_tax() ),
			),
			array(
				'key'   => 'Order.Currency',
				'value' => $order->get_currency(),
			),
			array(
				'key'   => 'Order.PaymentMethod',
				'value' => $order->get_payment_method(),
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Order.DeliveryMethod',
				'value' => $order->get_shipping_method(), // @todo: test!!!! was delivery method
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Order.BillingFirstname',
				'value' => $order->get_billing_first_name(),
			),
			array(
				'key'   => 'Order.BillingLastname',
				'value' => $order->get_billing_last_name(),
			),
			array(
				'key'   => 'Order.BillingStreet',
				'value' => $order->get_billing_address_1(),
			),
			array(
				'key'   => 'Order.BillingCity',
				'value' => $order->get_billing_city(),
			),
			array(
				'key'   => 'Order.BillingZipcode',
				'value' => $order->get_billing_postcode(),
			),
			array(
				'key'   => 'Order.BillingState',
				'value' => $order->get_billing_state(),
			),
			array(
				'key'   => 'Order.BillingCountry',
				'value' => $order->get_billing_country(),
			),
			array(
				'key'   => 'Order.BillingTele',
				'value' => Woorule_Utils::get_order_phone_number( $order ),
			),
			array(
				'key'   => 'Order.BillingCompany',
				'value' => $order->get_billing_company(),
			),
			array(
				'key'   => 'Order.OrderUrl',
				'value' => $order->get_view_order_url(),
			),
		);
	}

	/**
	 * Get order items fields.
	 *
	 * @param WC_Order $order Order.
	 *
	 * @return array
	 */
	protected function get_order_items_fields( $order ) {
		$items_data = $this->get_order_items_data( $order->get_items() );

		$order_items_fields = array();

		$brands = array_filter( wp_list_pluck( $items_data['products'], 'brand' ) );
		if ( ! empty( $brands ) ) {
			$order_items_fields[] = array(
				'key'   => 'Order.Brands',
				'value' => $brands,
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['categories'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Order.Collections',
				'value' => $items_data['categories'],
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['tags'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Order.Tags',
				'value' => $items_data['tags'],
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['products'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Order.Products',
				'value' => wp_json_encode( $items_data['products'] ),
				'type'  => 'json',
			);

			$products_names = wp_list_pluck( $items_data['products'], 'name' );
			if ( ! empty( $products_names ) ) {
				$order_items_fields[] = array(
					'key'   => 'Order.Names',
					'value' => $products_names,
					'type'  => 'multiple',
				);
			}
		}

		return $order_items_fields;
	}

	/**
	 * Get order items data.
	 *
	 * @param WC_Order_Item_Product[] $items Order items.
	 *
	 * @return array
	 */
	protected function get_order_items_data( $items ) {
		$products   = array();
		$categories = array();
		$tags       = array();

		foreach ( $items as $item ) {
			$product = new WC_Product_Simple( $item->get_product_id() );
			$p_img   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'full' );

			$price_excluding_tax = wc_get_price_excluding_tax( $product );
			$price_including_tax = wc_get_price_including_tax( $product );

			$products[] = array(
				'brand'     => $product->get_attribute( 'brand' ),
				'name'      => $product->get_title(),
				'image'     => isset( $p_img[0] ) ? $p_img[0] : '',
				'price'     => Woorule_Utils::round( $price_excluding_tax ),
				'price_vat' => Woorule_Utils::round( $price_including_tax ),
				'vat'       => Woorule_Utils::round( $price_including_tax - $price_excluding_tax ),
				'qty'       => $item->get_quantity(),
				'subtotal'  => Woorule_Utils::round( $item->get_total() ),
				'total'     => Woorule_Utils::round( $price_including_tax * $item->get_quantity() ),
				'slug'      => $product->get_slug(),
			);

			$categories_string = wp_strip_all_tags( wc_get_product_category_list( $item->get_product_id() ) );
			if ( $categories_string ) {
				$categories = array_unique( array_merge( $categories, explode( ',', $categories_string ) ) );
			}

			$tags_string = wp_strip_all_tags( wc_get_product_tag_list( $item->get_product_id() ) );
			if ( $tags_string ) {
				$tags = array_unique( array_merge( $tags, explode( ',', $tags_string ) ) );
			}
		}

		return compact( 'products', 'categories', 'tags' );
	}
}
