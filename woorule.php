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
 * Version:         2.7.5
 * Author:          Rule
 * Author URI:      http://rule.se
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 6.5.1
 *
 * @package WooRule
 */

define( 'WOORULE_VERSION', '2.7.5' );
define( 'WOORULE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOORULE_URL', plugin_dir_url( __FILE__ ) );

require_once WOORULE_PATH . 'inc/class-woorule.php';

$woorule = new Woorule();
