<?php
/**
 * WooRule - Rule integration for WooCommerce.
 *
 * @wordpress-plugin
 * @woocommerce-plugin
 *
 * Plugin Name:     WooRule
 * Plugin URI:      http://github.com/rulecom/woorule
 * Description:     Rule integration for WooCommerce
 * Version:         3.0.4
 * Author:          Rule
 * Author URI:      http://rule.se
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 8.2.1
 *
 * @package WooRule
 */

define( 'WOORULE_VERSION', '3.0.4' );
define( 'WOORULE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOORULE_URL', plugin_dir_url( __FILE__ ) );

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

require_once WOORULE_PATH . 'inc/class-woorule.php';

$woorule = new Woorule();
