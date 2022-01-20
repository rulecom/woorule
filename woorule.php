<?php
/**
 * WooRule - Rule integration for WooCommerce.
 *
 * @wordpress-plugin
 * @woocommerce-plugin
 *
 * Plugin Name:     WooRule
 * Plugin URI:      http://github.com/rulecom/woorule
 * Description:     RuleMailer integration for WooCommerce
 * Version:         2.3.1
 * Author:          RuleMailer
 * Author URI:      http://rule.se
 * Developer:       Jonas Adolfsson, Neev Alex
 * Developer URI:   http://lurig.github.io,http://neevalex.com
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 *
 * @package WooRule
 */

define( 'WOORULE_VERSION', '2.3.1' );
define( 'WOORULE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOORULE_URL', plugin_dir_url( __FILE__ ) );

require_once WOORULE_PATH . 'inc/class-woorule.php';

$woorule = new Woorule();
