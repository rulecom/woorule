<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WC_Integration_RuleMailer extends WC_Integration {

	public function __construct() {
		$this->id 									= 'rulemailer';
		$this->method_title 				= 'RuleMailer';
		$this->method_description 	= 'RuleMailer integration for WooCommerce';

		$this->init_settings();
		$this->api_key = $this->get_option( 'woorule_api_key' );
		$this->api_url = $this->get_option( 'woorule_api_url' );
		$this->init_form_fields();

		// actions
		add_action( 'woocommerce_update_options_integration', array( &$this, 'process_admin_options') );
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
				'description'		=> __( 'Go to RuleMailers settings here to generetate one.', 'woorule' ),
				'default' 			=> '' 
			),

			'woorule_api_url' => array(
				'title' 				=> __( 'API URL', 'woorule' ),
				'type' 					=> 'text',
				'description'		=> __( 'URL to the API.', 'woorule' ),
				'default' 			=> '' 
			)
		);
	}

	public function admin_options() {
		?>
			<h2>RuleMailer</h2>
			<p>Add your API key and a new tab will show up at the top of this page.</p>
			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
		<?php
	}
}

