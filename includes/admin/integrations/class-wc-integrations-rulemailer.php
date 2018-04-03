<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WC_Integration_RuleMailer
 */
class WC_Integration_RuleMailer extends WC_Integration {

	public function __construct() {
		$this->id 									= 'rulemailer';
		$this->method_title 				= 'RuleMailer';
		$this->method_description 	= 'RuleMailer integration for WooCommerce';

		$this->init_settings();
		$this->api_key = $this->get_option( 'woorule_api_key' );
		$this->init_form_fields();

		// action
		add_action( 'woocommerce_update_options_integration_woorule', array( &$this, 'process_admin_options') );

		// filters
		add_filter( 'woocommerce_get_sections_integration', array( &$this, 'rule_add_section' ) );

		// triggers
		if ( $this->api_key ) {
			do_action( 'woorule_api_loaded', $this->api_key );
		}
	}

	function rule_add_section( $sections ) {
		$sections['woorule'] = 'RuleMailer';
		return $sections;
	}

	function init_form_fields() {
		$this->form_fields = array(
			'woorule_api_key' => array(
				'title' 				=> __( 'API Key', 'woorule' ),
				'type' 					=> 'text',
				'default' 			=> '' 
			)
		);
	}

	public function admin_options() {
		?>
			<h2><?php _e( 'RuleMailer', 'woorule' ); ?></h2>
			<p><?php _e( 'You may find your RULE API key inside <a href="http://app.rule.io/#/settings/developer">developer tab on user account settings</a>.', 'woorule' ); ?></p>

			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>





		<?php
	}
}

