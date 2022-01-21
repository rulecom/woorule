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
 */
class Woorule_Cart_Hooks {
	/**
	 * Current customer.
	 *
	 * @var WC_Customer
	 */
	private $current_customer;

	/**
	 * Woorule_Order_Hooks constructor.
	 */
	public function __construct() {
		add_action(
			'woocommerce_add_to_cart',
			array( $this, 'cart_updated' )
		);
		add_action(
			'woocommerce_cart_item_removed',
			array( $this, 'cart_updated' )
		);
		add_action(
			'woocommerce_applied_coupon',
			array( $this, 'cart_updated' )
		);
		add_action(
			'woocommerce_removed_coupon',
			array( $this, 'cart_updated' )
		);
		add_filter(
			'woocommerce_update_cart_action_cart_updated',
			array( $this, 'filter_cart_updated' ),
			PHP_INT_MAX
		);
	}

	/**
	 * On triggered cart updated action.
	 *
	 * @param bool $cart_updated Is cart updated?.
	 *
	 * @return bool
	 */
	public function filter_cart_updated( $cart_updated ) {
		if ( $cart_updated ) {
			$this->cart_updated();
		}

		return $cart_updated;
	}

	/**
	 * Retrieve current logged in customer.
	 *
	 * @return int
	 */
	protected function retrieve_current_customer() {
		$current_user_id = get_current_user_id();

		if ( $current_user_id ) {
			try {
				$this->current_customer = new WC_Customer( $current_user_id );
			} catch ( Exception $e ) {
				return 0;
			}
		}

		return $this->current_customer->get_id();
	}

	/**
	 * On cart updated.
	 *
	 * @return void
	 */
	public function cart_updated() {
		if ( ! $this->retrieve_current_customer() ) {
			return;
		}

		WC()->cart->calculate_shipping();
		WC()->cart->calculate_fees();
		WC()->cart->calculate_totals();

		$subscription = array(
			'apikey'              => Woorule_Options::get_api_key(),
			'update_on_duplicate' => true,
			'auto_create_tags'    => true,
			'auto_create_fields'  => true,
			'automation'          => 'reset',
			'async'               => true,
			'tags'                => $this->get_subscription_tags(),
			'subscribers'         => array(
				'email'        => $this->current_customer->get_billing_email(),
				'phone_number' => $this->current_customer->get_billing_phone(),
				'language'     => substr( get_locale(), 0, 2 ),
				'fields'       => array_merge(
					$this->get_subscriber_fields(),
					$this->get_order_fields(),
					$this->get_order_items_fields()
				),
			),
		);

		RuleMailer_API::subscribe( $subscription );
	}

	/**
	 * Get subscription tags.
	 *
	 * @return array
	 */
	protected function get_subscription_tags() {
		return array( 'CartInProgress' );
	}

	/**
	 * Get subscriber fields.
	 *
	 * @return array
	 */
	protected function get_subscriber_fields() {
		return array(
			array(
				'key'   => 'Subscriber.FirstName',
				'value' => $this->current_customer->get_billing_first_name(),
			),
			array(
				'key'   => 'Subscriber.LastName',
				'value' => $this->current_customer->get_billing_last_name(),
			),
			array(
				'key'   => 'Subscriber.Number',
				'value' => $this->current_customer->get_id(),
			),
			array(
				'key'   => 'Subscriber.Street1',
				'value' => $this->current_customer->get_billing_address_1(),
			),
			array(
				'key'   => 'Subscriber.Street2',
				'value' => $this->current_customer->get_billing_address_2(),
			),
			array(
				'key'   => 'Subscriber.City',
				'value' => $this->current_customer->get_billing_city(),
			),
			array(
				'key'   => 'Subscriber.Zipcode',
				'value' => $this->current_customer->get_billing_postcode(),
			),
			array(
				'key'   => 'Subscriber.State',
				'value' => $this->current_customer->get_billing_state(),
			),
			array(
				'key'   => 'Subscriber.Country',
				'value' => $this->current_customer->get_billing_country(),
			),
			array(
				'key'   => 'Subscriber.Company',
				'value' => $this->current_customer->get_billing_company(),
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
	 * @return array
	 */
	protected function get_order_fields() {
		return array(
			array(
				'key'   => 'Order.Number',
				'value' => null,
			),
			array(
				'key'   => 'Order.Date',
				'value' => gmdate( 'Y/m/d H:i:s' ),
				'type'  => 'datetime',
			),
			array(
				'key'   => 'Order.Subtotal',
				'value' => WC()->cart->get_subtotal(),
			),
			array(
				'key'   => 'Order.SubtotalVat',
				'value' => WC()->cart->get_subtotal() + WC()->cart->get_cart_contents_tax(),
			),
			array(
				'key'   => 'Order.Discount',
				'value' => WC()->cart->get_discount_total(),
			),
			array(
				'key'   => 'Order.Shipping',
				'value' => WC()->cart->get_shipping_total(),
			),
			array(
				'key'   => 'Order.Total',
				'value' => WC()->cart->get_total( null ),
			),
			array(
				'key'   => 'Order.Vat',
				'value' => WC()->cart->get_total_tax(),
			),
			array(
				'key'   => 'Order.Currency',
				'value' => get_woocommerce_currency(),
			),
			array(
				'key'   => 'Order.PaymentMethod',
				'value' => null,
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Order.DeliveryMethod',
				'value' => null,
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Order.BillingFirstname',
				'value' => $this->current_customer->get_billing_first_name(),
			),
			array(
				'key'   => 'Order.BillingLastname',
				'value' => $this->current_customer->get_billing_last_name(),
			),
			array(
				'key'   => 'Order.BillingStreet',
				'value' => $this->current_customer->get_billing_address_1(),
			),
			array(
				'key'   => 'Order.BillingCity',
				'value' => $this->current_customer->get_billing_city(),
			),
			array(
				'key'   => 'Order.BillingZipcode',
				'value' => $this->current_customer->get_billing_postcode(),
			),
			array(
				'key'   => 'Order.BillingState',
				'value' => $this->current_customer->get_billing_state(),
			),
			array(
				'key'   => 'Order.BillingCountry',
				'value' => $this->current_customer->get_billing_country(),
			),
			array(
				'key'   => 'Order.BillingTele',
				'value' => $this->current_customer->get_billing_phone(),
			),
			array(
				'key'   => 'Order.BillingCompany',
				'value' => $this->current_customer->get_billing_company(),
			),
		);
	}

	/**
	 * Get order items fields.
	 *
	 * @return array
	 */
	protected function get_order_items_fields() {
		$items_data = $this->get_order_items_data();

		$order_items_fields = array();

		$brands = array_filter( wp_list_pluck( $items_data['products'], 'brands' ) );
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
	 * @return array
	 */
	protected function get_order_items_data() {
		$products   = array();
		$categories = array();
		$tags       = array();

		foreach ( WC()->cart->cart_contents as $item ) {
			$p_img = wp_get_attachment_image_src( get_post_thumbnail_id( $item['data']->get_id() ), 'full' );

			$price_excluding_tax = wc_get_price_excluding_tax( $item['data'] );
			$price_including_tax = wc_get_price_including_tax( $item['data'] );

			$products[] = array(
				'brand'     => $item['data']->get_attribute( 'brand' ),
				'name'      => $item['data']->get_title(),
				'image'     => $p_img[0],
				'price'     => round( $price_excluding_tax, 2 ),
				'price_vat' => round( $price_including_tax, 2 ),
				'vat'       => round( $price_including_tax - $price_excluding_tax, 2 ),
				'qty'       => $item['quantity'],
				'subtotal'  => round( $price_excluding_tax * $item['quantity'], 2 ),
				'total'     => round( $price_including_tax * $item['quantity'], 2 ),
			);

			$categories_string = wp_strip_all_tags( wc_get_product_category_list( $item['data']->get_id() ) );
			if ( $categories_string ) {
				$categories = array_unique( array_merge( $categories, explode( ',', $categories_string ) ) );
			}

			$tags_string = wp_strip_all_tags( wc_get_product_tag_list( $item['data']->get_id() ) );
			if ( $tags_string ) {
				$tags = array_unique( array_merge( $tags, explode( ',', $tags_string ) ) );
			}
		}

		return compact( 'products', 'categories', 'tags' );
	}
}
