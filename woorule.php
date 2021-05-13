<?php
/**
 * WooRule - RuleMailer integration for WooCommerce.
 *
 * @wordpress-plugin
 * @woocommerce-plugin
 *
 * Plugin Name:     WooRule
 * Plugin URI:      http://github.com/rulecom/woorule
 * Description:     RuleMailer integration for WooCommerce.
 * Version:         1.3
 * Author:          RuleMailer, Neevalex
 * Author URI:      http://rule.se
 * Developer:       Jonas Adolfsson, Neev Alex
 * Developer URI:   http://lurig.github.io,http://neevalex.com
 *
 * Text Domain:     woorule
 * Domain Path:     /languages
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'includes/class-wc-woorule.php');

function activate_woorule()
{
    WooRule::check_rules();
}
register_activation_hook(__FILE__, 'activate_woorule');

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
if (!get_option('woocommerce_rulemailer_settings')['woorule_api_key']) {
    add_action('admin_notices', 'woorule_admin_notice_api_error');
}

function woorule_enqueue_admin_script( $hook ) {
    wp_enqueue_script( 'woorule_js', plugin_dir_url( __FILE__ ) . 'includes/admin/assets/rule.js', array(), '1.0' );
    wp_register_style( 'woorule_css', plugin_dir_url( __FILE__ ) . 'includes/admin/assets/rule.css', false, '1.0.0' );
    wp_enqueue_style( 'woorule_css' );
}
add_action( 'admin_enqueue_scripts', 'woorule_enqueue_admin_script' );


function run_woorule()
{
    $lang_dir = basename(dirname(__FILE__ . '/languages'));
    load_plugin_textdomain('woorule', false, $lang_dir);

    $plugin = new WooRule();
    $plugin->run();
}

add_action('plugins_loaded', 'run_woorule', 0);


// Shortcode stuff
function woorule_func($atts)
{
    if (get_option('woocommerce_rulemailer_settings')['woorule_api_key']) {
        wp_enqueue_style('woorule-css', plugin_dir_url(__FILE__) . "/assets/woorule.css");
    
        if (!(empty($atts['text']))) {
            $text = $atts['text'];
        } else {
            $text = 'Your e-mail';
        }
        if (!(empty($atts['button']))) {
            $button = $atts['button'];
        } else {
            $button = 'Submit';
        }
        if (!(empty($atts['success']))) {
            $success = $atts['success'];
        } else {
            $success = 'Success!';
        }

        $output =  '<div class="woorule form newsletter subscribe">';
        $output .= '<input type="text" class="input email qty" placeholder="'.$text.'" />';
        $output .= '<button class="subscribe btn" />'.$button.'</button>';
        $output .= '<span class="success" />'.$success.'</span>';
        $output .= '<span class="error" /></span>';
        $output .= '</div>';
    } else {
        $output = __('Looks like your Rule API Key are empty. Please do not forget to add it <a href="'.get_admin_url().'admin.php?page=wc-settings&tab=integration">inside the settings</a>.', 'woorule');
    }

    return $output;
}

add_shortcode('woorule', 'woorule_func');


add_action('wp_enqueue_scripts', 'woorule_submit_scripts');
add_action('wp_ajax_ajax-wooruleSubmit', 'woorule_wooruleSubmit_func');
add_action('wp_ajax_nopriv_ajax-wooruleSubmit', 'woorule_wooruleSubmit_func');
function woorule_submit_scripts()
{
    wp_enqueue_script('woorule_submit', plugin_dir_url(__FILE__)  . '/assets/woorule.js', array( 'jquery' ));
    wp_localize_script(
        'woorule_submit',
        'WooRule_Ajax',
        array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'nextNonce' => wp_create_nonce('woorule-next-nonce')
        )
    );
}

// This will add the direct "Settings" link inside wp plugins menu.
add_filter( 'plugin_action_links_woorule/woorule.php', 'woorule_settings_link' );
function woorule_settings_link( $links ) {

	$url = esc_url( add_query_arg(
		'page','wc-settings', get_admin_url() . 'admin.php?page=wc-settings&tab=woorule_settings_tab'
	) );

	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

	array_push($links,$settings_link);
	return $links;
}



function woorule_wooruleSubmit_func()
{
    // check nonce
    $nonce = $_POST['nextNonce'];
    if (! wp_verify_nonce($nonce, 'woorule-next-nonce')) {
        die('Busted!');
    }
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        WP_RuleMailer_API::new_subscription(sanitize_email($_POST['email']));
        echo sanitize_email($_POST['email']);
    }
    exit;
}
