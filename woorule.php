<?php
/**
 * WooRule - RuleMailer integration for WooCommerce.
 *
 * @wordpress-plugin
 * @woocommerce-plugin
 *
 * Plugin Name:     WooRule - RuleMailer Integration for WooCommerce
 * Plugin URI:      http://github.com/rulecom/woorule
 * Description:     RuleMailer integration for WooCommerce.
 * Version:         0.0.1
 * Author:          RuleMailer
 * Author URI:      http://rule.se
 * Developer:       Jonas Adolfsson
 * Developer URI:   http://lurig.github.io
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function activate_woorule() {
}

function deactivate_woorule() {
}

register_activation_hook( __FILE__, 'activate_woorule' );
register_deactivation_hook( __FILE__, 'deactivate_woorule' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wc-woorule.php' );

function run_woorule() {
	$lang_dir = basename( dirname( __FILE__ . '/languages' ) );
	load_plugin_textdomain( 'woorule', false, $lang_dir );

	$plugin = new WooRule();
	$plugin->run();
}

add_action( 'plugins_loaded', 'run_woorule', 0 );

