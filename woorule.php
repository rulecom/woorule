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
 * Version:         0.2
 * Author:          RuleMailer, Neevalex
 * Author URI:      http://rule.se
 * Developer:       Jonas Adolfsson, Neev Alex
 * Developer URI:   http://lurig.github.io,http://neevalex.com
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


function woorule_admin_notice__error() {
	$class = 'notice notice-error';
	$message = __( 'Woorule requires Woocomerce plugin to be installed and activated.', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}



	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woorule_admin_notice__error' );
			return;
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





// Shortcode stuff
function woorule_func( $atts ) {


	wp_enqueue_style( 'woorule-css',  plugin_dir_url( __FILE__ ) . "/assets/woorule.css");
	
    if (!(empty($atts['text']))) {$text = $atts['text'];} else { $text = 'Your e-mail';}
    if (!(empty($atts['button']))) {$button = $atts['button'];} else { $button = 'Submit';}
    if (!(empty($atts['success']))) {$success = $atts['success'];} else { $success = 'Success!';}

   

   $output =  '<div class="woorule form newsletter subscribe">';
   $output .= '<input type="text" class="input email qty" placeholder="'.$text.'" />';
   $output .= '<button class="subscribe btn" />'.$button.'</button>';
   $output .= '<span class="success" />'.$success.'</span>';
   $output .= '<span class="error" /> Please enter valid email! </span>';
   $output .= '</div>';



	return $output;
}

add_shortcode( 'woorule', 'woorule_func' );


add_action( 'wp_enqueue_scripts', 'woorule_submit_scripts' );
add_action( 'wp_ajax_ajax-wooruleSubmit', 'woorule_wooruleSubmit_func' );
add_action( 'wp_ajax_nopriv_ajax-wooruleSubmit', 'woorule_wooruleSubmit_func' );
function woorule_submit_scripts() {
	wp_enqueue_script( 'woorule_submit', plugin_dir_url( __FILE__ )  . '/assets/woorule.js', array( 'jquery' ) );
	wp_localize_script( 'woorule_submit', 'WooRule_Ajax', array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'nextNonce' => wp_create_nonce( 'woorule-next-nonce' )
		)
	);
}
function woorule_wooruleSubmit_func() {
	// check nonce
	$nonce = $_POST['nextNonce'];
	if ( ! wp_verify_nonce( $nonce, 'woorule-next-nonce' ) ) {
		die ( 'Busted!' );
	}
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	WC_Admin_Settings_Rulemailer::new_subscription( sanitize_email(  $_POST['email'] ) );
	echo sanitize_email($_POST['email']);
    }

	exit;
}

