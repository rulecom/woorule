<?php
/**
 * Class Woorule
 *
 * @package Woorule
 */

require_once WOORULE_PATH . 'inc/trait-woorule-logging.php';
require_once WOORULE_PATH . 'inc/class-woorule-utils.php';
require_once WOORULE_PATH . 'inc/class-woorule-checkout.php';
require_once WOORULE_PATH . 'inc/class-woorule-order-hooks.php';
require_once WOORULE_PATH . 'inc/class-woorule-cart-hooks.php';
require_once WOORULE_PATH . 'inc/class-woorule-shortcode.php';
require_once WOORULE_PATH . 'inc/class-woorule-options.php';
require_once WOORULE_PATH . 'inc/class-rulemailer-api.php';
require_once WOORULE_PATH . 'inc/class-productalert-api.php';
require_once WOORULE_PATH . 'inc/class-woorule-alert.php';
require_once WOORULE_PATH . 'inc/class-woorule-alert-shortcode.php';

/**
 * Class Woorule
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
class Woorule {
	/**
	 * Woorule constructor.
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'update_options' ) );

		if ( $this->is_woocommerce_activated() ) {
			$this->load_integrations();

			new Woorule_Checkout();
			new Woorule_Order_Hooks();
			new Woorule_Cart_Hooks();
			new Woorule_Shortcode();
		} else {
			add_action( 'admin_notices', array( $this, 'notice_woo_error' ) );
		}

		if ( ! $this->is_api_key_set() ) {
			add_action( 'admin_notices', array( $this, 'notice_api_error' ) );
		}

		add_action( 'admin_menu', array( $this, 'settings_page_init' ) );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );

		// This will add the direct "Settings" link inside wp plugins menu.
		add_filter( 'plugin_action_links_woorule/woorule.php', array( $this, 'settings_link' ) );
	}

	/**
	 * Check if WooCommerce is activated.
	 *
	 * @return bool
	 */
	protected function is_woocommerce_activated() {
		return class_exists( 'WooCommerce', false ) || defined( 'WC_ABSPATH' );
	}

	/**
	 * Check if API key is set.
	 *
	 * @return bool
	 */
	protected function is_api_key_set() {
		return ! empty( Woorule_Options::get_api_key() );
	}

	/**
	 * Print WooCommerce missing notice.
	 *
	 * @return void
	 */
	public function notice_woo_error() {
		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			'notice notice-error',
			esc_html__( 'Woorule requires WooCommerce plugin to be installed and activated.', 'woorule' )
		);
	}

	/**
	 * Print API key missing notice.
	 *
	 * @return void
	 */
	public function notice_api_error() {
		$class   = 'notice notice-error';
		$message = sprintf(
		// translators: %s: setting page URL.
			__(
				'It looks like your Rule API Key are empty. Please do not forget to add it <a href="%s">inside the settings</a>.',
				'woorule'
			),
			esc_url( $this->get_settings_page_url() )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	/**
	 * Add link to settings page.
	 *
	 * @param array $links Links.
	 *
	 * @return array
	 */
	public function settings_link( $links ) {
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			$this->get_settings_page_url(),
			__( 'Settings', 'woorule' )
		);

		return $links;
	}

	/**
	 * Get settings page URL.
	 *
	 * @return string
	 */
	protected function get_settings_page_url() {
		return add_query_arg( 'page', 'woorule-settings', admin_url( 'options-general.php' ) );
	}

	/**
	 * Initialise settings page.
	 *
	 * @return void
	 */
	public function settings_page_init() {
		add_menu_page(
			__( 'Woorule', 'woorule' ),
			__( 'Woorule', 'woorule' ),
			'manage_options',
			'woorule-settings',
			array( $this, 'settings_page' ),
			plugins_url( 'woorule/assets/fav.svg' ),
			100
		);
	}

	/**
	 * Enqueue Scripts in admin
	 *
	 * @param $hook
	 *
	 * @return void
	 */
	public static function admin_enqueue_scripts( $hook ) {
		if ( 'toplevel_page_woorule-settings' === $hook ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style(
				'woorule-css',
				WOORULE_URL . 'assets/admin' . $suffix . '.css',
				array(),
				WOORULE_VERSION,
				'all'
			);
		}
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		load_template(
			WOORULE_PATH . 'inc/partials/admin-settings.php',
			true,
			array(
				'logo_url' => WOORULE_URL . 'assets/logo.png',
				'label'    => Woorule_Options::get_checkout_label()
					? Woorule_Options::get_checkout_label() :
					__( 'Please sign me up to the newsletter!', 'woorule' ),
				'tags'     => Woorule_Options::get_checkout_tags() ? Woorule_Options::get_checkout_tags() : 'Newsletter',
				'show'     => Woorule_Options::get_checkout_show() ? Woorule_Options::get_checkout_show() : '',
				'api_key'  => Woorule_Options::get_api_key() ? Woorule_Options::get_api_key() : '',
			)
		);
	}

	/**
	 * Update plugin options.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function update_options() {
		if ( isset( $_POST['save'] ) && 'woorule' === wc_clean( $_POST['save'] ) ) {
			check_admin_referer( 'woorule-settings' );

			Woorule_Options::set_options(
				apply_filters(
					'woorule_update_options',
					array_map(
						'sanitize_text_field',
						array(
							// phpcs:disable WordPress.Security.ValidatedSanitizedInput
							'woorule_api_key'        => isset( $_POST['woorule_api'] ) ? wc_clean( $_POST['woorule_api'] ) : '',
							'woorule_checkout_tags'  => isset( $_POST['woorule_checkout_tags'] ) ? wc_clean( $_POST['woorule_checkout_tags'] ) : '',
							'woorule_checkout_label' => isset( $_POST['woorule_checkout_label'] ) ? wc_clean( $_POST['woorule_checkout_label'] ) : '',
							'woorule_checkout_show'  => isset( $_POST['woorule_checkout_show'] ) ? wc_clean( $_POST['woorule_checkout_show'] ) : '',
							// phpcs:enable WordPress.Security.ValidatedSanitizedInput
						)
					)
				)
			);
		}
	}

	/**
	 * 3rd party plugins integrations loader.
	 *
	 * @return void
	 */
	protected function load_integrations() {
		/**
		 * Each integration must be in a separate subdirectory under the "integrations" directory.
		 * Integration directory name will be the name of a bootstrap file and integration class name.
		 * For example: WooCommerce integration
		 * |-integrations
		 * |    |-woocommerce
		 * |    |   |-class-woorule-woocommerce.php Class Woorule_Woocommerce
		 */
		$dir_list = glob( WOORULE_PATH . 'inc/integrations/*', GLOB_ONLYDIR );
		foreach ( $dir_list as $dir ) {
			$dir_name   = basename( $dir );
			$class_name = 'woorule_' . str_replace( '-', '_', $dir_name );
			$file_name  = 'class-woorule-' . $dir_name . '.php';
			$file_path  = $dir . '/' . $file_name;
			if ( is_readable( $file_path ) ) {
				require_once $file_path;
				new $class_name();
			}
		}
	}
}
