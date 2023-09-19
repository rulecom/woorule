<?php

/**
 * Class Woorule_Alert
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
class Woorule_Alert {
	/**
	 * @var Woorule_Background_Alert_Queue
	 */
	public static $background_process;

	/**
	 * Woorule_Alert constructor.
	 */
	public function __construct() {
		add_filter(
			'woocommerce_get_stock_html',
			array( $this, 'add_stock_html' ),
			10,
			2
		);

		add_filter(
			'woorule_options_defaults',
			array( $this, 'add_options_defaults' )
		);

		add_action(
			'woorule_admin_settings_after_checkout',
			array( $this, 'admin_settings' ),
			20
		);

		add_filter(
			'woorule_update_options',
			array( $this, 'update_options' )
		);

		add_action(
			'woocommerce_after_product_object_save',
			array( $this, 'product_object_save' ),
			20,
			2
		);

		add_action(
			'woocommerce_save_product_variation',
			array( $this, 'save_product_variation' ),
			20,
			2
		);

		add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );

		if ( ! is_multisite() ) {
			add_action( 'customize_save_after', array( $this, 'maybe_process_queue' ) );
			add_action( 'after_switch_theme', array( $this, 'maybe_process_queue' ) );
		}
	}

	/**
	 * Process product variation.
	 *
	 * @param int $variation_id
	 * @param int $i
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 */
	public function save_product_variation( $variation_id, $i ) {
		$variation = new WC_Product_Variation( $variation_id );
		if ( $variation->get_id() ) {
			// Create Background Process Task
			$background_process = new Woorule_Background_Alert_Queue();
			$background_process->push_to_queue(
				array(
					'product_id' => $variation->get_id(),
					'stock'      => $variation->get_stock_quantity(),
				)
			);

			$background_process->save();
		}
	}

	/**
	 * Process product.
	 *
	 * @param WC_Product $product
	 * @param object $data_store
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function product_object_save( $product, $data_store ) {
		if ( 'variable' === $product->get_type() ) {
			/** @var WC_Product_Variable $product */

			// Variable products must be processed by `woocommerce_save_product_variation` hook

			return;
		}

		// Create Background Process Task
		$background_process = new Woorule_Background_Alert_Queue();
		$background_process->push_to_queue(
			array(
				'product_id' => $product->get_id(),
				'stock'      => $product->get_stock_quantity(),
			)
		);
		$background_process->save();
	}

	/**
	 * Add stock html.
	 *
	 * @param string $html
	 * @param WC_Product $product
	 * @return string
	 */
	public function add_stock_html( $html, $product ) {
		if ( 'on' === Woorule_Options::get_alert_product_show() &&
			$product->managing_stock() &&
			( ! $product->is_in_stock() || $product->get_stock_quantity() <= 0 )
		) {
			$html .= do_shortcode( '[woorule_alert product_id="' . $product->get_id() . '"]' );
		}

		return $html;
	}

	/**
	 * Add options defaults.
	 *
	 * @param array $options_defaults Options defaults.
	 *
	 * @return array
	 */
	public function add_options_defaults( $options_defaults ) {
		$options_defaults['woorule_alert_product_show'] = '';
		$options_defaults['woorule_alert_label']        = __( 'Alert subscription', 'woorule' );
		$options_defaults['woorule_alert_success']      = __( 'Thank you!', 'woorule' );
		$options_defaults['woorule_alert_error']        = __( 'Oops, something is wrong..', 'woorule' );
		$options_defaults['woorule_alert_placeholder']  = __( 'Your e-mail', 'woorule' );
		$options_defaults['woorule_alert_button']       = __( 'Submit', 'woorule' );
		$options_defaults['woorule_alert_tags']         = 'Rule - Waiting For Product Alert';
		$options_defaults['woorule_alert_product_tags'] = '';
		$options_defaults['woorule_alert_min_stock']    = '10';
		$options_defaults['woorule_alerts_per_stock']   = '20';

		return $options_defaults;
	}

	/**
	 * Render admin settings.
	 *
	 * @return void
	 */
	public function admin_settings() {
		wc_get_template(
			'woorule/admin-alert.php',
			array(
				'args' => array(
					'show'         => Woorule_Options::get_alert_product_show(),
					'label'        => Woorule_Options::get_alert_label(),
					'success'      => Woorule_Options::get_alert_success(),
					'error'        => Woorule_Options::get_alert_error(),
					'placeholder'  => Woorule_Options::get_alert_placeholder(),
					'button'       => Woorule_Options::get_alert_button(),
					'tags'         => Woorule_Options::get_alert_tags(),
					'product_tags' => Woorule_Options::get_alert_product_tags(),
					'min_stock'    => Woorule_Options::get_alert_min_stock(),
					'per_stock'    => Woorule_Options::get_alerts_per_stock(),
				),
			),
			'',
			__DIR__ . '/../templates/'
		);
	}

	/**
	 * Update options.
	 *
	 * @param array $options Options.
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function update_options( $options ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$options['woorule_alert_product_show'] = isset( $_POST['woorule_alert_product_show'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_product_show'] ) )
			: '';

		$options['woorule_alert_label'] = isset( $_POST['woorule_alert_label'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_label'] ) )
			: '';

		$options['woorule_alert_placeholder'] = isset( $_POST['woorule_alert_placeholder'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_placeholder'] ) )
			: '';

		$options['woorule_alert_button'] = isset( $_POST['woorule_alert_button'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_button'] ) )
			: '';

		$options['woorule_alert_tags'] = isset( $_POST['woorule_alert_tags'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_tags'] ) )
			: '';

		$options['woorule_alert_product_tags'] = isset( $_POST['woorule_alert_product_tags'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_product_tags'] ) )
			: '';

		$options['woorule_alert_min_stock'] = isset( $_POST['woorule_alert_min_stock'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alert_min_stock'] ) )
			: '';

		$options['woorule_alerts_per_stock'] = isset( $_POST['woorule_alerts_per_stock'] )
			// phpcs:ignore WordPress.Security
			? sanitize_text_field( wc_clean( $_POST['woorule_alerts_per_stock'] ) )
			: '';

		// Save ProductAlert settings
		// phpcs:disable
		ProductAlert_API::put_settings( array(
			'apikey'           => Woorule_Options::get_api_key(),
			'alert_min_stock'  => $options['woorule_alert_min_stock'],
			'alerts_per_stock' => $options['woorule_alerts_per_stock'],
		) );
		// phpcs:enable

		return $options;
	}

	/**
	 * WooCommerce Init
	 */
	public function woocommerce_init() {
		include_once __DIR__ . '/class-woorule-background-alert-queue.php';

		self::$background_process = new Woorule_Background_Alert_Queue();
	}

	/**
	 * Dispatch Background Process
	 */
	public function maybe_process_queue() {
		self::$background_process->dispatch();
	}
}

new Woorule_Alert();
