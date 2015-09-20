<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WC_Admin_Settings_RuleMailer
 */
class WC_Admin_Settings_Rulemailer {
	static $ACTION;
	static $RULE_ID;

	public static function init() {
		// filters
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__.'::add_setting_tab', 100 );

		// actions
		add_action( 'woocommerce_settings_tabs_woorule_settings_tab',  __CLASS__.'::route' );
		add_action( 'woocommerce_update_options_woorule_settings_tab', __CLASS__.'::update_options' );

		add_action( 'woocommerce_checkout_update_order_meta',  __CLASS__.'::order_status_changed', 1000, 1 );
		add_action( 'woocommerce_order_status_changed',        __CLASS__.'::order_status_changed', 10, 3 );

		add_action( 'woocommerce_checkout_fields',            __CLASS__.'::checkout_fields' );
		add_action( 'woocommerce_checkout_update_order_meta', __CLASS__.'::save_checkout_fields' );

		// params
		self::$ACTION  = empty( $_GET['woo-rule-action'] ) ? '' : sanitize_title( $_GET['woo-rule-action'] );
		self::$RULE_ID = empty( $_GET['rule-id'] )         ? '' : sanitize_title( $_GET['rule-id'] );
	}

	public static function add_setting_tab( $tabs ) {
		$tabs['woorule_settings_tab'] = __( 'RuleMailer', 'woorule' );
		return $tabs;
	}

	public static function save_checkout_fields( $order_id ) {
		// @TODO: fix, this is st0pid
		foreach ( $_POST as $k => $v ) {
			if ( substr( $k, 0, 15) === 'woorule_opt_in_' ) {
				$rule_id = substr( $k, 15, strlen( $k ) );
				update_post_meta( $order_id, 'woorule_opt_in_'.$rule_id, 'yes');
			}
		}
	}

	public static function checkout_fields( $fields ) {
		$rules = get_option( 'woorule_rules', array() );

		foreach ( $rules as $rule_id => $rule ) {
			$enabled = get_option( $rule['enabled']['id'] ) === 'yes' ? true : false;

			if ( $enabled && isset($rule['show_opt_in']) ) {
				$show_opt_in = get_option( $rule['show_opt_in']['id'] ) === 'yes' ? true : false;

				if ( $show_opt_in ) {
					$opt_in_label = get_option( $rule['opt_in_label']['id'] );

					$fields['order']['woorule_opt_in_'.$rule_id] = array(
						'type' => 'checkbox',
						'label' => $opt_in_label
					);
				}
			}
		}

		return $fields;
	}

	public static function order_status_changed( $id, $status = '', $new_status = '' ) {
		$rules  = get_option('woorule_rules', array());

		foreach ( $rules as $k => $rule ) {
			$enabled = get_option( $rule['enabled']['id'] ) === 'yes' ? true : false;

			if ( $enabled ) {
				$which_event    = get_option( $rule['occurs']['id'] );
				$is_opt_in_rule = false;
				$want_in        = true;

				if ( isset( $rule['show_opt_in'] ) ) {
					$is_opt_in_rule = get_option( $rule['show_opt_in']['id'] ) === 'yes' ? true : false;
				}

				if ( $is_opt_in_rule ) {
					$want_in = get_post_meta( $id, 'woorule_opt_in_'.$k, false );

					if ( ! empty( $want_in ) ) {
						$want_in = $want_in[0] === 'yes' ? true : false;
					}
				}

				if ( $want_in && $new_status === $which_event ) {
					$integration    = new WC_Integration_RuleMailer();
					$order          = new WC_Order( $id );
					$user           = $order->get_user();
					$currency       = $order->get_order_currency();
					$order_subtotal = $order->order_total - ($order->order_shipping_tax + $order->order_shipping) - $order->cart_discount;
					$items          = $order->get_items();
					$brands         = array();
					$categories     = array();
					$tags           = array();

					foreach ( $items as $item ) {
						$p = new WC_Product_Simple( $item['product_id'] );
						$brands[] = $p->get_attribute( 'brand' );

						// this is bullshit
						$cat = strip_tags( $p->get_categories( '' ));
						$tag = strip_tags( $p->get_tags( '' ));

						if ( ! empty( $cat ) ) {
							$categories[] = $cat;
						}

						if ( ! empty( $tag ) ) {
							$tags[] = $tag;
						}
					}

					$subscription = array(
						'apikey'              => $integration->api_key,
						'update_on_duplicate'	=> get_option( $rule['update_on_duplicate']['id'] )	=== 'yes' ? true : false,
						'auto_create_tags'		=> get_option( $rule['auto_create_tags']['id'] )		=== 'yes' ? true : false,
						'auto_create_fields'	=> get_option( $rule['auto_create_fields']['id'] )	=== 'yes' ? true : false,
						'tags'								=> explode(',', get_option( $rule['tags']['id'] )),

						'subscribers' => array(
							'email'					=> $order->billing_email,
							'phone_number'	=> $order->billing_phone,

							'fields' => array(
								array(
									'key'			=> 'Order.Number',
									'value'		=> $order->get_order_number()
								),
								array(
									'key'			=> 'Order.Date',
									'value'		=> $order->order_date
								),
								array(
									'key'			=> 'Order.FirstName',
									'value'		=> $order->billing_first_name
								),
								array(
									'key'			=> 'Order.LastName',
									'value'		=> $order->billing_last_name
								),
								array(
									'key'			=> 'Order.Street1',
									'value'		=> $order->billing_address_1
								),
								array(
									'key'			=> 'Order.Street2',
									'value'		=> $order->billing_address_2
								),
								array(
									'key'			=> 'Order.City',
									'value'		=> $order->billing_city
								),
								array(
									'key'			=> 'Order.Country',
									'value'		=> $order->billing_country
								),
								array(
									'key'			=> 'Order.State',
									'value'		=> $order->billing_state
								),

								array(
									'key'			=> 'Order.Subtotal',
									'value'		=> $order_subtotal
								),
								array(
									'key'			=> 'Order.Discount',
									'value'		=> $order->cart_discount
								),
								array(
									'key'			=> 'Order.Shipping',
									'value'		=> $order->order_shipping + $order->order_shipping_tax
								),
								array(
									'key'			=> 'Order.Total',
									'value'		=> $order->order_total
								),
								array(
									'key'			=> 'Order.Vat',
									'value'		=> $order->order_tax
								)
							)
						)
					);

					if ( ! empty( $categories ) ) {
						$subscription['subscribers']['fields'][] = array(
							'key'			=> 'Order.Categories',
							'value'		=> $categories,
							'type'		=> 'multiple'
						);
					}

					if ( ! empty( $tags ) ) {
						$subscription['subscribers']['fields'][] = array(
							'key'			=> 'Order.Tags',
							'value'		=> array( $tags ),
							'type'		=> 'multiple'
						);
					}

					if ( ! empty( $brands ) ) {
						$subscription['subscribers']['fields'][] = array(
							'key'			=> 'Order.Brands',
							'value'		=> $brands ,
							'type'		=> 'multiple'
						);
					}

					$api = WP_RuleMailer_API::get_instance();
					$api::subscribe( $integration->api_url, $subscription );
				}
			}
		}
	}

	public static function route() {
		switch ( self::$ACTION ) {
			case 'create':
				self::create_new();
				return self::show_list();
				break;

			case 'edit':
				$GLOBALS['hide_save_button'] = true;
				$delete_url = admin_url( 'admin.php?page=wc-settings&tab=woorule_settings_tab&woo-rule-action=delete&rule-id=' ) . self::$RULE_ID;

				woocommerce_admin_fields( self::edit_rule() );
				include_once( (new WooRule())->get_path() . '/includes/admin/views/html-admin-buttons.php' );

				break;

			case 'delete':
				self::delete_rule();
				self::show_list();
				break;

			default:
				return self::show_list();
				break;
		}
	}

	public static function edit_rule() {
		$rules = get_option( 'woorule_rules', array() );
		$settings = self::get_rule_settings_by_id( self::$RULE_ID );

		return apply_filters( 'wc_settings_rulemailer', $settings );
	}

	public static function delete_rule() {
		$to_delete = self::get_rule_settings_by_id( self::$RULE_ID );

		if ( ! empty( $to_delete ) ) {
			foreach ( $to_delete as $k => $v ) {
				delete_option( $to_delete[$k]['id'] );
			}

			$rules = get_option( 'woorule_rules', array() );
			unset( $rules[self::$RULE_ID] );
			update_option( 'woorule_rules', $rules );
		}
	}

	public static function create_new() {
		$rules = get_option( 'woorule_rules', array() );

		if ( empty( $rules ) ) {
			$new_id = 1;

		} else {
			$keys = array_keys( $rules );
			$new_id = end( $keys ) + 1;
		}

		$settings = self::get_default_settings( $new_id );
		$rules[ $new_id ] = $settings;

		update_option( 'woorule_rules', $rules );

		return apply_filters( 'wc_settings_rulemailer', $settings );
	}

	public static function update_options( $options ) {
		$rule_id = self::$RULE_ID;

		if ( !empty( $rule_id ) ) {
			$settings = self::get_rule_settings_by_id( $rule_id );
			woocommerce_update_options( $settings );
		}
	}

	private static function get_rule_settings_by_id( $rule_id ) {
		if ( empty( $rule_id ) ) {
			return;
		}

		$rules = get_option( 'woorule_rules', array() );
		$current_rule = array();

		if ( ! empty( $rules ) ) {
			$current_rule = $rules[ $rule_id ];
		}

		if ( empty( $current_rule ) ) {
			return;
		}

		return $current_rule;
	}

	private static function get_default_settings( $id ) {
		$settings = array(
			'section_title' => array(
				'title'		=> __( 'Edit', 'woorule' ),
				'type'		=> 'title',
				'id'			=> 'woorule_title_'.$id
			),

			'name' => array(
				'title'		=> __( 'Name', 'woorule' ),
				'type'		=> 'text',
				'id' 			=> 'woorule_name_'.$id,
				'desc' 		=> 'Give your rule a name',
				'default' => 'Unnamed #'.$id
			),

			'enabled' => array(
				'title'			=> __( 'Enabled', 'woorule' ),
				'type'			=> 'checkbox',
				'id'				=> 'woorule_enabled_'.$id,
				'default'		=> 'no'
			),

			'show_opt_in' => array(
				'title'			=> __( 'Show in Checkout', 'woorule' ),
				'type'			=> 'checkbox',
				'id'				=> 'woorule_show_opt_in_'.$id,
				'default'		=> 'no'
			),

			'opt_in_label' => array(
				'title'			=> __( 'Checkout label', 'woorule' ),
				'type'			=> 'text',
				'id'				=> 'woorule_opt_in_label_'.$id
			),

			'update_on_duplicate' => array(
				'title' 		=> __( 'Update on duplicate', 'woorule' ),
				'type' 			=> 'checkbox',
				'id' 				=> 'woorule_update_on_duplicate_'.$id,
				'default'		=> 'no'
			),

			'auto_create_tags' => array(
				'title' 		=> __( 'Auto create tags', 'woorule' ),
				'type' 			=> 'checkbox',
				'id' 				=> 'woorule_auto_create_tags_'.$id,
				'default'		=> 'no'
			),

			'auto_create_fields' => array(
				'title' 		=> __( 'Auto create fields', 'woorule' ),
				'type' 			=> 'checkbox',
				'id' 				=> 'woorule_auto_create_fields_'.$id,
				'default'		=> 'no'
			),

			'occurs' => array(
				'title' 		=> __( 'Which Event', 'woorule' ),
				'type' 			=> 'select',
				'desc' 			=> __( 'On which order event should this rule fire?', 'woorule' ),
				'default'		=> 'pending',
				'id' 				=> 'woorule_event_'.$id,
				'options' 	=> array(
					'pending'    	=> __( 'Pending Payment', 'woorule' ),
					'processing'	=> __( 'Processing', 'woorule' ),
					'on-hold'     => __( 'On Hold', 'woorule' ),
					'completed'  	=> __( 'Completed', 'woorule' ),
					'cancelled'  	=> __( 'Cancelled', 'woorule' ),
					'refunded'  	=> __( 'Refunded', 'woorule' ),
					'failed'      => __( 'Failed', 'woorule' ),
				),
			),

			'tags' => array(
				'title'		=> __( 'Tags', 'woorule' ),
				'type'		=> 'text',
				'id' 			=> 'woorule_tags_'.$id,
				'desc' 		=> 'Comma separated list of tags'
			),

			'section_end' => array(
				'type'		=> 'sectionend',
				'id'			=> 'wc_settings_rulemailer_section_end'
			)
		);

		return $settings;
	}

	private static function show_list() {
		$GLOBALS['hide_save_button'] = true;

		$create_url		= admin_url('admin.php?page=wc-settings&tab=woorule_settings_tab&woo-rule-action=create');
		$edit_url 		= admin_url('admin.php?page=wc-settings&tab=woorule_settings_tab&woo-rule-action=edit&rule-id=');
		$rules 				= get_option('woorule_rules', array());

		include_once( (new WooRule())->get_path() . '/includes/admin/views/html-admin-rule-list.php' );
	}
}

