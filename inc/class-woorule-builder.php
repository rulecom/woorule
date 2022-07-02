<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class Woorule_Builder
 *
 * @package Woorule
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class Woorule_Builder {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );

		// Add scripts and styles for admin
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'wp_ajax_woorule_builder_field_enabled', array( $this, 'builder_field_enabled' ) );
	}

	/**
	 * Add menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page(
			'woorule-settings',
			__( 'Flexible Fields Settings', 'woorule' ),
			__( 'Flexible Fields Settings', 'woorule' ),
			'manage_options',
			'woorule_builder',
			array(
				$this,
				'settings_page',
			)
		);
	}

	/**
	 * Add admin scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'async',
			WOORULE_URL . 'assets/async' . $suffix . '.js',
			array(),
			'3.2.0',
			true
		);

		wp_enqueue_script(
			'woorule-admin-builder',
			WOORULE_URL . 'assets/woorule-admin-builder' . $suffix . '.js',
			array( 'jquery', 'async' ),
			WOORULE_VERSION,
			true
		);

		wp_localize_script(
			'woorule-admin-builder',
			'Woorule_Admin_Builder',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'woorule' ),
			)
		);
	}

	/**
	 * Allows to add WC Admin scripts.
	 * See `woocommerce_screen_ids` hook.
	 *
	 * @param $screen_ids
	 *
	 * @return array
	 */
	public function add_screen_id( $screen_ids ) {
		if ( ! is_array( $screen_ids ) ) {
			$screen_ids = array();
		}

		$screen_ids[] = 'woorule_page_woorule_builder';

		return $screen_ids;
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		// Order
		register_setting(
			'Woorule_Builder_WC_Fields',
			'rule_order_fields'
		);
		register_setting(
			'Woorule_Builder_WC_Fields',
			'rule_order_fields_status'
		);

		// Cart
		register_setting(
			'woorule_builder_cart_fields',
			'rule_cart_fields'
		);
		register_setting(
			'woorule_builder_cart_fields',
			'rule_cart_fields_status'
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Flexible Fields Settings', 'woorule' ); ?></h1>
			<a href="https://app.rule.io" target="_blank">
				<img width="128"
					src="<?php echo esc_url( WOORULE_URL . 'assets/logo.png' ); ?>"
					alt=""
					class="lazyloaded"
					data-ll-status="loaded"/>
			</a>
			<br style="clear: both"/>
			<?php
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$current_section = isset( $_GET['section'] ) ? wc_clean( $_GET['section'] ) : 'orders';

			// Add sections
			wc_get_template(
				'woorule/admin/builder/tabs.php',
				array(
					'current_section' => $current_section,
				),
				'',
				dirname( __FILE__ ) . '/../templates/'
			);

			settings_errors();

			switch ( $current_section ) {
				case 'orders':
					$builder = new Woorule_Builder_Order_Fields();

					$title        = __( 'Order Fields Settings', 'woorule' );
					$option_group = 'Woorule_Builder_WC_Fields';
					$settings     = array(
						'rule_order_fields'        => $builder->load_fields(),
						'rule_order_fields_status' => $builder->load_fields_status(),
					);

					break;
				case 'cart':
					$builder = new Woorule_Builder_Cart_Fields();

					$title        = __( 'Cart Fields Settings', 'woorule' );
					$option_group = 'woorule_builder_cart_fields';
					$settings     = array(
						'rule_cart_fields'        => $builder->load_fields(),
						'rule_cart_fields_status' => $builder->load_fields_status(),
					);

					break;
				default:
					return;
			}

			wc_get_template(
				'woorule/admin/builder/settings.php',
				array(
					'section'            => $current_section,
					'title'              => $title,
					'option_group'       => $option_group,
					'settings'           => $settings,
					'wc_fields'          => $builder->get_wc_fields(),
					'rule_fields'        => $builder->load_fields(),
					'rule_fields_status' => $builder->load_fields_status(),
				),
				'',
				dirname( __FILE__ ) . '/../templates/'
			);

			?>
		</div>
		<?php
	}

	/**
	 * Ajax: Set field status.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function builder_field_enabled() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'woorule' ) ) {
			wp_send_json_error( 'No naughty business' );

			return;
		}

		// phpcs:ignore WordPress.Security
		$section = wc_clean( $_REQUEST['section'] );

		// phpcs:ignore WordPress.Security
		$field = wc_clean( $_REQUEST['field'] );

		// phpcs:ignore WordPress.Security
		$status = wc_clean( $_REQUEST['status'] );

		switch ( $section ) {
			case 'orders':
				$builder          = new Woorule_Builder_Order_Fields();
				$fields           = $builder->load_fields_status();
				$fields[ $field ] = $status;

				update_option( 'rule_order_fields_status', json_encode( $fields ) );

				break;
			case 'cart':
				$builder          = new Woorule_Builder_Cart_Fields();
				$fields           = $builder->load_fields_status();
				$fields[ $field ] = $status;

				update_option( 'rule_cart_fields_status', json_encode( $fields ) );

				break;
		}

		wp_send_json_success( (bool) $status );
	}
}

new Woorule_Builder();
