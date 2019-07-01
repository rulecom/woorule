<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * WC_Integration_RuleMailer
 */
class WC_Integration_RuleMailer extends WC_Integration
{
    public function __construct()
    {
        $this->id = 'rulemailer';
        $this->method_title = 'RuleMailer';
        $this->method_description = 'RuleMailer integration for WooCommerce';

        $this->init_settings();
        $this->api_key = get_option('woocommerce_rulemailer_settings')['woorule_api_key'];
        $this->init_form_fields();

        // actions
        add_action('woocommerce_update_options_integration', array( &$this, 'process_admin_options'));

        // triggers
        if ($this->api_key) {
            do_action('woorule_api_loaded', $this->api_key);
        }
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'woorule_api_key' => array(
                'title' 				=> __('API Key', 'woorule_api_key'),
                'type' 					=> 'text',
                'default' 			=> ''
            )
        );
    }

    public function admin_options()
    {
        ?>
			<img style="margin-top:15px;" src="<?php echo plugin_dir_url(__DIR__); ?>../../assets/woorule.svg" />
			<p><?php _e('You can find your RULE API key inside <a href="http://app.rule.io/#/settings/developer">developer tab on user account settings</a>.', 'woorule'); ?></p>

			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>

		<?php
    }
}
