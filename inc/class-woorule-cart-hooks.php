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
				$current_user_id = 0;
			}
		}

		return $current_user_id;
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

		$email = $this->current_customer->get_billing_email();
		if ( empty( $email ) ) {
			$email = $this->current_customer->get_email();
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
				'email'        => $email,
				'phone_number' => Woorule_Utils::get_customer_phone_number( $this->current_customer ),
				'language'     => substr( get_locale(), 0, 2 ),
				'fields'       => array_merge(
					$this->get_subscriber_fields(),
					$this->get_order_fields(),
					$this->get_order_items_fields()
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
					if ( 'Cart.BillingTele' === $field['key'] ) {
						unset( $fields[ $key ] );
					}
				}

				$subscription['subscribers']['fields'] = $fields;
			}

			RuleMailer_API::subscribe( $subscription );
		}
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
				'key'   => 'Cart.Date',
				'value' => gmdate( 'Y-m-d H:i:s' ),
				'type'  => 'datetime',
			),
			array(
				'key'   => 'Cart.Subtotal',
				'value' => Woorule_Utils::round( WC()->cart->get_subtotal() ),
			),
			array(
				'key'   => 'Cart.SubtotalVat',
				'value' => Woorule_Utils::round( WC()->cart->get_subtotal() + WC()->cart->get_cart_contents_tax() ),
			),
			array(
				'key'   => 'Cart.Discount',
				'value' => Woorule_Utils::round( WC()->cart->get_discount_total() ),
			),
			array(
				'key'   => 'Cart.Shipping',
				'value' => Woorule_Utils::round( WC()->cart->get_shipping_total() ),
			),
			array(
				'key'   => 'Cart.ShippingVat',
				'value' => Woorule_Utils::round( WC()->cart->get_shipping_total() + WC()->cart->get_shipping_tax() ),
			),
			array(
				'key'   => 'Cart.Total',
				'value' => Woorule_Utils::round( WC()->cart->get_total( null ) ),
			),
			array(
				'key'   => 'Cart.Vat',
				'value' => Woorule_Utils::round( WC()->cart->get_total_tax() ),
			),
			array(
				'key'   => 'Cart.Currency',
				'value' => get_woocommerce_currency(),
			),
			array(
				'key'   => 'Cart.PaymentMethod',
				'value' => null,
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Cart.DeliveryMethod',
				'value' => null,
				'type'  => 'multiple',
			),
			array(
				'key'   => 'Cart.BillingFirstname',
				'value' => $this->current_customer->get_billing_first_name(),
			),
			array(
				'key'   => 'Cart.BillingLastname',
				'value' => $this->current_customer->get_billing_last_name(),
			),
			array(
				'key'   => 'Cart.BillingStreet',
				'value' => $this->current_customer->get_billing_address_1(),
			),
			array(
				'key'   => 'Cart.BillingCity',
				'value' => $this->current_customer->get_billing_city(),
			),
			array(
				'key'   => 'Cart.BillingZipcode',
				'value' => $this->current_customer->get_billing_postcode(),
			),
			array(
				'key'   => 'Cart.BillingState',
				'value' => $this->current_customer->get_billing_state(),
			),
			array(
				'key'   => 'Cart.BillingCountry',
				'value' => $this->current_customer->get_billing_country(),
			),
			array(
				'key'   => 'Cart.BillingTele',
				'value' => Woorule_Utils::get_customer_phone_number( $this->current_customer ),
			),
			array(
				'key'   => 'Cart.BillingCompany',
				'value' => $this->current_customer->get_billing_company(),
			),
			array(
				'key'   => 'Cart.CartUrl',
				'value' => wc_get_cart_url(),
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
				'key'   => 'Cart.Brands',
				'value' => $brands,
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['categories'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Cart.Collections',
				'value' => $items_data['categories'],
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['tags'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Cart.Tags',
				'value' => $items_data['tags'],
				'type'  => 'multiple',
			);
		}

		if ( ! empty( $items_data['products'] ) ) {
			$order_items_fields[] = array(
				'key'   => 'Cart.Products',
				'value' => wp_json_encode( $items_data['products'] ),
				'type'  => 'json',
			);

			$products_names = wp_list_pluck( $items_data['products'], 'name' );
			if ( ! empty( $products_names ) ) {
				$order_items_fields[] = array(
					'key'   => 'Cart.Names',
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
				'price'     => Woorule_Utils::round( $price_excluding_tax ),
				'price_vat' => Woorule_Utils::round( $price_including_tax ),
				'vat'       => Woorule_Utils::round( $price_including_tax - $price_excluding_tax ),
				'qty'       => $item['quantity'],
				'subtotal'  => Woorule_Utils::round( $price_excluding_tax * $item['quantity'] ),
				'total'     => Woorule_Utils::round( $price_including_tax * $item['quantity'] ),
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
