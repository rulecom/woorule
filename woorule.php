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
 * Version:         2.1
 * Author:          RuleMailer
 * Author URI:      http://rule.se
 * Developer:       Jonas Adolfsson, Neev Alex
 * Developer URI:   http://lurig.github.io,http://neevalex.com
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 */


function woorule_admin_notice_woo_error()
{
    $class = 'notice notice-error';
    $message = __('Woorule requires Woocomerce plugin to be installed and activated.', 'woorule');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

function woorule_admin_notice_api_error()
{
    $class = 'notice notice-error';
    $message = __('It looks like your Rule API Key are empty. Please do not forget to add it <a href="'.get_admin_url().'admin.php?page=wc-settings&tab=integration">inside the settings</a>.', 'woorule');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}

if (! class_exists('WooCommerce')) {
    add_action('admin_notices', 'woorule_admin_notice_woo_error');
    return;
}

if ((!get_option('woocommerce_rulemailer_settings') ) || (!get_option('woocommerce_rulemailer_settings')['woorule_api_key']) ){
    add_action('admin_notices', 'woorule_admin_notice_api_error');
}

require_once(plugin_dir_path(__FILE__) . 'inc/class-wc-rulemailer-api.php');
require_once(plugin_dir_path(__FILE__) . 'inc/class-wc-woorule.php');

$woorule = new Woorule;