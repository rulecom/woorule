<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooRule {
	public $plugin_path = null;

	public function __construct() {
		$this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once( $this->plugin_path . 'includes/api/class-wc-rulemailer-api.php' );
		require_once( $this->plugin_path . 'includes/admin/integrations/class-wc-integrations-rulemailer.php' );
		require_once( $this->plugin_path . 'includes/admin/settings/class-wc-admin-settings-woorule.php' );
	}

	public function run() {
		if ( ! class_exists( 'WC_Integrations' ) ) {
			return;
		}

		//$plugin_path = plugin_dir_path( dirname( __FILE__ ) );
		require_once( $this->plugin_path . 'includes/admin/integrations/class-wc-integrations-rulemailer.php' );

		global $woocommerce;

		$settings_url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=rulemailer' );

		if ( ! defined( 'WOOCOMMERCE_RULEMAILER_SETTINGS_URL' ) ) {
			define( 'WOOCOMMERCE_RULEMAILER_SETTINGS_URL', $settings_url );
		}

		function add_rulemailer_integration( $methods ) {
			$methods[] = 'WC_Integration_RuleMailer';
			return $methods;
		}

		function action_links( $links ) {
			$plugin_links = array(
				'<a href="' . WOOCOMMERCE_RULEMAILER_SETTINGS_URL . '">' . __( 'Settings', 'woorule' ) . '</a>'
			);

			return array_merge( $plugin_links, $links );
		}

		function api_loaded( $key ) {
		}

		WC_Admin_Settings_Rulemailer::init();

		add_filter( 'woocommerce_integrations', 'add_rulemailer_integration' );
		add_filter( 'plugin_action_links_woorule', 'action_links' );

		add_action( 'woorule_api_loaded', 'api_loaded' );
	}

	public function get_path() {
		return $this->plugin_path;
	}
}

