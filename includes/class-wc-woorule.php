<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * WooRule
 */
class WooRule
{
    public $plugin_path = null;

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->load_dependencies();
        // $this->check_rules();
        // WooRule::check_rules();
    }

    private function load_dependencies()
    {
        require_once($this->plugin_path . 'includes/api/class-wc-rulemailer-api.php');
        require_once($this->plugin_path . 'includes/admin/integrations/class-wc-integrations-rulemailer.php');
        require_once($this->plugin_path . 'includes/admin/settings/class-wc-admin-settings-woorule.php');
    }


    private static function get_default_field_settings($id)
    {
        $settings = array(
            'section_title' => array(
                'title'		=> __('Edit', 'woorule'),
                'type'		=> 'title',
                'id'			=> 'woorule_title_'.$id
            ),

            'name' => array(
                'title'		=> __('Name', 'woorule'),
                'type'		=> 'text',
                'id' 			=> 'woorule_name_'.$id,
                'desc' 		=> 'Give your rule a name',
                'default' => 'Default Rule'
            ),

            'enabled' => array(
                'title'			=> __('Enabled', 'woorule'),
                'type'			=> 'checkbox',
                'id'				=> 'woorule_enabled_'.$id,
                'default'		=> 'yes'
            ),

            'show_opt_in' => array(
                'title'			=> __('Show in Checkout', 'woorule'),
                'type'			=> 'checkbox',
                'id'				=> 'woorule_show_opt_in_'.$id,
                'default'		=> 'no'
            ),

            'opt_in_label' => array(
                'title'			=> __('Checkout label', 'woorule'),
                'type'			=> 'text',
                'id'				=> 'woorule_opt_in_label_'.$id
            ),

            'update_on_duplicate' => array(
                'title' 		=> __('Update on duplicate', 'woorule'),
                'type' 			=> 'checkbox',
                'id' 				=> 'woorule_update_on_duplicate_'.$id,
                'default'		=> 'yes'
            ),

            'automation' => array(
                'title' 		=> __(' ', 'woorule'),
                'type' 			=> 'select',
                'id' 				=> 'woorule_automation_'.$id,
                'default'		=> 'true',
                'options' 	=> array(
                    'none'    	=> __('None', 'woorule'),
                    'force'    	=> __('Force', 'woorule'),
                    'reset'	=> __('Reset', 'woorule'),
                    'true'    	=> __('True', 'woorule')
                ),
            ),

            'auto_create_tags' => array(
                'title' 		=> __('Auto create tags', 'woorule'),
                'type' 			=> 'checkbox',
                'id' 				=> 'woorule_auto_create_tags_'.$id,
                'default'		=> 'yes'
            ),

            'auto_create_fields' => array(
                'title' 		=> __('Auto create fields', 'woorule'),
                'type' 			=> 'checkbox',
                'id' 				=> 'woorule_auto_create_fields_'.$id,
                'default'		=> 'yes'
            ),

            'occurs' => array(
                'title' 		=> __('Which Event', 'woorule'),
                'type' 			=> 'select',
                'desc' 			=> __('On which order event should this rule fire?', 'woorule'),
                'default'		=> 'processing',
                'id' 				=> 'woorule_event_'.$id,
                'options' 	=> array(
                    'pending'    	=> __('Pending Payment', 'woorule'),
                    'processing'	=> __('Processing', 'woorule'),
                    'on-hold'     => __('On Hold', 'woorule'),
                    'completed'  	=> __('Completed', 'woorule'),
                    'cancelled'  	=> __('Cancelled', 'woorule'),
                    'refunded'  	=> __('Refunded', 'woorule'),
                    'failed'      => __('Failed', 'woorule'),
                ),
            ),

            'tags' => array(
                'title'		=> __('Tags', 'woorule'),
                'type'		=> 'text',
                'id' 			=> 'woorule_tags_'.$id,
                'desc' 		=> 'Comma separated list of tags',
                'default'		=> 'New order'
            ),

            'custom_fields' => array(
                'title'			=> __('Custom Fields', 'woorule'),
                'type'			=> 'text',
                'id'				=> 'woorule_custom_fields_'.$id
            ),

            'section_end' => array(
                'type'		=> 'sectionend',
                'id'			=> 'wc_settings_rulemailer_section_end'
            )
          
        );

        return $settings;
    }


    public static function check_rules()
    {
        // Let's make sure that there always will be a "Default" rule.
        $rules = get_option('woorule_rules', array());
        if (empty($rules)) {
            $settings = self::get_default_field_settings(1);
            $rules[ 1 ] = $settings;
            update_option('woorule_rules', $rules);

            update_option('woorule_name_1', 'Default Rule');
            update_option('woorule_enabled_1', 'yes');
            update_option('woorule_update_on_duplicate_1', 'yes');
            update_option('woorule_auto_create_tags_1', 'yes');
            update_option('woorule_event_1', 'processing');
            update_option('woorule_tags_1', 'New order');
            update_option('woorule_automation_1', 'reset');
            update_option('woorule_custom_fields_1', '[{"attribute":"_payment_method","source":"order"},{"attribute":"_payment_method_title","source":"order"},{"attribute":"_customer_ip_address","source":"order"},{"attribute":"nickname","source":"user"},{"attribute":"first_name","source":"user"},{"attribute":"last_name","source":"user"}]');
            

            apply_filters('wc_settings_rulemailer', $settings);
        }
    }

    public function run()
    {
        if (! class_exists('WooCommerce')) {
            return;
        }

        //$plugin_path = plugin_dir_path( dirname( __FILE__ ) );
        require_once($this->plugin_path . 'includes/admin/integrations/class-wc-integrations-rulemailer.php');

        global $woocommerce;

        $settings_url = admin_url('admin.php?page=wc-settings&tab=integration&section=rulemailer');

        if (! defined('WOOCOMMERCE_RULEMAILER_SETTINGS_URL')) {
            define('WOOCOMMERCE_RULEMAILER_SETTINGS_URL', $settings_url);
        }

        function add_rulemailer_integration($methods)
        {
            $methods[] = 'WC_Integration_RuleMailer';
            return $methods;
        }

        function action_links($links)
        {
            $plugin_links = array(
                '<a href="' . WOOCOMMERCE_RULEMAILER_SETTINGS_URL . '">' . __('Settings', 'woorule') . '</a>'
            );

            return array_merge($plugin_links, $links);
        }

        function api_loaded($key)
        {
        }

        WC_Admin_Settings_Rulemailer::init();

        add_filter('woocommerce_integrations', 'add_rulemailer_integration');
        add_filter('plugin_action_links_woorule', 'action_links');

        add_action('woorule_api_loaded', 'api_loaded');
    }

    public function get_path()
    {
        return $this->plugin_path;
    }
}
