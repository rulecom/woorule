<?php
class Woorule
{

    const DELIMITER = ',';
    const ALLOWED_STATUSES = ['processing', 'completed', 'shipped'];
    // This array lists all the event triggers that will trigger data transfer to Rule
    // The following array is a list of all the possible default order event triggers:
    // [ 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' ]
    // Note that all active event triggers must have an associated tag name defined in the $custom_tags array

    public function __construct()
    {
        add_action('admin_menu', array($this, 'settings_page_init' ));
        add_action('admin_head', array($this, 'admin_css'));
        // This will add the direct "Settings" link inside wp plugins menu.
        add_filter('plugin_action_links_woorule/woorule.php', array($this, 'settings_link' ));
        // Orders hook
        add_action('woocommerce_order_status_changed', array($this, 'order_status_changed'), 10, 3);
        // newsletter subscribe button on checkout
        add_action('woocommerce_review_order_before_submit', array($this, 'custom_checkout_field'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'custom_checkout_field_update_order_meta'));

        // Shortcode
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_shortcode('woorule',  array($this, 'woorule_func'));
        add_action('wp_ajax_woorule_subscribe_user', array($this, 'subscribe_user')); // Admins only
        add_action('wp_ajax_nopriv_woorule_subscribe_user', array($this, 'subscribe_user')); // Users only
    }

    // Plugin Stylesheet
    public function register_assets()
    {
        wp_enqueue_style('woorule', plugin_dir_url(__FILE__) . '../assets/woorule.css', 10, '1.0');
        wp_register_script('woorule', plugin_dir_url(__FILE__) . '../assets/woorule.js');
        wp_enqueue_script('woorule', plugin_dir_url(__FILE__) . '../assets/woorule.js', array('woorule'));

        wp_localize_script('woorule', 'ajax_var', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woorule'),
        ));
    }

    public function woorule_func($atts)
    {
        //print_r($atts);
        $title = (isset($atts['title'])) ? $atts['title'] : __('Newsletter subscribtion', 'woorule');
        $submit = (isset($atts['button'])) ? $atts['button'] : __('Submit', 'woorule');
        $success = (isset($atts['success'])) ? $atts['success'] : __('Thank you!', 'woorule');
        $placeholder = (isset($atts['placeholder'])) ? $atts['placeholder'] :  __('Your e-mail', 'woorule');
        $error = __('Oops, something is wrong..', 'woorule');

        $return = '<div class="woorule-subscribe">';
        $return .= '<form>';
            $return .= '<label for="semail" class="form_elem">' . $title . '</label>';
            $return .= '<input type="text" id="semail" name="email" class="form_elem" placeholder="' . $placeholder . '">';
            $return .= '<input type="submit" value="' . $submit . '" class="form_elem">';

            if(isset($atts['tag'])) $return .= '<input value="' . $atts['tag'] . '" name="tags" class="tag hidden form_elem">';

            $return .= '<p class="hidden success">' . $success . '</p>';
            $return .= '<p class="hidden error">' . $error . '</p>';
        $return .= '</form>';
        $return .= '</div>';

        return $return;
    }

    public function subscribe_user()
    {
        // Check for nonce security
        if ((!wp_verify_nonce($_POST['nonce'], 'woorule')) || (!isset($_POST['email']))) {
            die('err');
        }

        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die('err');
        }

        // Default tag should exist. Otherwise there will be an error from RULE API.
        $tags = [];
        // Add custom tags if set
        if (isset($_POST['tags'])) foreach(explode(',', $_POST['tags']) as $tag ) array_push($tags, $tag);

        $subscription = array(
            'apikey'              => get_option('woocommerce_rulemailer_settings')['woorule_api_key'],
            'update_on_duplicate' => true,
            'auto_create_tags'    => true,
            'auto_create_fields'  => true,
            'async'               => true,
            'tags'                => $tags,
            'subscribers'         => array(
                'email'           => $email
            )
        );

        $api = WP_RuleMailer_API::get_instance();
        $api::subscribe($subscription);
        die('ok');
    }

    public function settings_link( $links ) {

        $url = esc_url( add_query_arg(
            'page','woorule-settings', get_admin_url() . 'options-general.php'
        ) );

        $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

        array_push($links,$settings_link);
        return $links;
    }

    public static function settings_page_init() {
        add_menu_page(
            __( 'Woorule', 'woorule' ),
            'WooRule',
            'manage_options',
            'woorule-settings',
            __CLASS__ . '::settings_page',
            plugins_url( 'woorule/assets/fav.svg' ),
            100
        );
        return;
    }

    public function custom_checkout_field()
    {
        if (get_option('woocommerce_rulemailer_settings')['woorule_checkout_show'] == 'on') {
            echo '<div id="my_custom_checkout_field">';

            woocommerce_form_field('woorule_opt_in', array(
                'type'      => 'checkbox',
                'default'           => 'checked',
                'class'     => array('input-checkbox'),
                'label'     => get_option('woocommerce_rulemailer_settings')['woorule_checkout_label']
            ), 1);
            echo '</div>';
        }
    }

    public function custom_checkout_field_update_order_meta($order_id)
    {
        if (!empty($_POST['woorule_opt_in'])) {
            update_post_meta($order_id,'woorule_opt_in', 'true');
        }
        return;
    }

    public function order_status_changed($id, $status = '', $new_status = '')
    {
        $custom_tags = [ // Here you can define the tag names that are applied to a subscriber upon an event trigger. The format is "eventName" => "tagName". Note that all active event triggers MUST have a tag name associated with it.
            "processing" => "OrderProcessing",
            "completed"  => "OrderCompleted",
            "shipped"    => "OrderShipped"
        ];

        if(!in_array($new_status, self::ALLOWED_STATUSES)) return;

        $order          = new WC_Order($id);
        $order_subtotal = $order->get_total() - ($order->get_total_shipping()) - $order->get_total_discount();
        $items          = $order->get_items();
        $brands         = array();

        $products        = array();
        $categories     = array();
        $tags           = array();

        foreach ($items as $item) {
            $p = new WC_Product_Simple($item['product_id']);
            $brands[] = $p->get_attribute('brand');
            $p_img = wp_get_attachment_image_src(get_post_thumbnail_id($p->get_id()), 'full');
            $products[] = array(
                'brand' => $p->get_attribute('brand'),
                'name' => $p->get_title(),
                'image' => $p_img[0],
                'price' => round($p->get_price_excluding_tax(), 2),
                'vat' => round(($p->get_price_including_tax() - $p->get_price_excluding_tax()),2),
                'qty' => $item->get_quantity(),
                'subtotal' => $item->get_total()
            );

            $categoriesString = strip_tags(wc_get_product_category_list(
                $item['product_id'],
                self::DELIMITER
            ));

            $tagsStringOrder = strip_tags(wc_get_product_tag_list(
                $item['product_id'],
                self::DELIMITER
            ));

            if (!empty($categoriesString)) {
                $itemCategories = explode(self::DELIMITER, $categoriesString);
                $categories = array_unique(array_merge($categories, $itemCategories));
            }

        }

        if(isset($custom_tags[$new_status])) {
            array_push( $tags, $custom_tags[$new_status] );
        }

        $order_data = $order->get_data();

        if ( get_post_meta($id,'woorule_opt_in') ) {
            array_push( $tags, 'Newsletter'); // Check for a newsletter (checkout) chekbox
        }

        $tags = array_unique($tags); // API will give an error on duplicate tags. Making sure there wont be any.

        if(empty($tags)) array_push( $tags, 'WooRule'); // Making sure the tags array will never be empty as the API will not like this.

        $language = substr(get_locale(), 0, 2);

        $subscription = array(
            'apikey'              => get_option('woocommerce_rulemailer_settings')['woorule_api_key'],
            'update_on_duplicate'    => true,
            'auto_create_tags'        => true,
            'auto_create_fields'    => true,
            'automation'    => get_option($rule['automation']['id']),

            'async'  => true,
            'tags'    => $tags,
            'subscribers' => array(

                'email'                    => $order->get_billing_email(),
                'phone_number'        => $order_data['billing']['phone'] ?? '',
                'language' => $language,

                'fields' => array(
                    array(
                        'key'            => 'Order.Number',
                        'value'        => $order->get_order_number()
                    ),
                    array(
                        'key'            => 'Subscriber.FirstName',
                        'value'        => $order->get_billing_first_name()
                    ),
                    array(
                        'key'            => 'Subscriber.LastName',
                        'value'        => $order->get_billing_last_name()
                    ),
                    array(
                        'key'            => 'Subscriber.Number',
                        'value'        => $order->get_user_id()
                    ),
                    array(
                        'key'            => 'Subscriber.Street1',
                        'value'        => $order->get_billing_address_1()
                    ),
                    array(
                        'key'            => 'Subscriber.Street2',
                        'value'        => $order->get_billing_address_2()
                    ),
                    array(
                        'key'            => 'Subscriber.City',
                        'value'        => $order->get_billing_city()
                    ),
                    array(
                        'key'            => 'Subscriber.Zipcode',
                        'value'        => $order->get_billing_postcode()
                    ),
                    array(
                        'key'            => 'Subscriber.State',
                        'value'        => $order->get_billing_state()
                    ),
                    array(
                        'key'            => 'Subscriber.Country',
                        'value'        => $order->get_billing_country()
                    ),
                    array(
                        'key'            => 'Subscriber.Company',
                        'value'        => $order->get_billing_company()
                    ),
                    array(
                        'key'            => 'Subscriber.Source',
                        'value'        => 'WooRule'
                    ),
                    array(
                        'key'            => 'Order.Date',
                        'value'        => $order->get_date_completed()
                            ? date_format($order->get_date_completed(), "Y/m/d H:i:s") : '',
                        'type' => 'datetime'
                    ),
                    array(
                        'key'            => 'Order.Subtotal',
                        'value'        => $order_subtotal
                    ),
                    array(
                        'key'            => 'Order.Discount',
                        'value'        => $order->get_total_discount()
                    ),
                    array(
                        'key'            => 'Order.Shipping',
                        'value'        => $order->get_total_shipping()
                    ),
                    array(
                        'key'            => 'Order.Total',
                        'value'        => $order->get_total()
                    ),
                    array(
                        'key'            => 'Order.Vat',
                        'value'        => $order->get_total_tax()
                    ),
                    array(
                        'key'            => 'Order.Currency',
                        'value'        => $order_data['currency'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.PaymentMethod',
                        'value'        => $order_data['payment_method'] ?? '',
                        'type' => 'multiple'
                    ),
                    array(
                        'key'            => 'Order.DeliveryMethod',
                        'value'        => $order_data['delivery_method'] ?? '',
                        'type' => 'multiple'
                    ),
                    array(
                        'key'            => 'Order.BillingFirstname',
                        'value'        => $order_data['billing']['first_name'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingLastname',
                        'value'        => $order_data['billing']['last_name'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingStreet',
                        'value'        => $order_data['billing']['address_1'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingCity',
                        'value'        => $order_data['billing']['city'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingZipcode',
                        'value'        => $order_data['billing']['postcode'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingState',
                        'value'        => $order_data['billing']['state'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingCountry',
                        'value'        => $order_data['billing']['country'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingTele',
                        'value'        => $order_data['billing']['phone'] ?? ''
                    ),
                    array(
                        'key'            => 'Order.BillingCompany',
                        'value'        => $order_data['billing']['company'] ?? ''
                    )
                )
            )
        );

        if (!empty($brands)) {
            $subscription['subscribers']['fields'][] = array(
                'key'            => 'Order.Brands',
                'value'        => $brands,
                'type'        => 'multiple'
            );
        }


        if (!empty($categories)) {
            $subscription['subscribers']['fields'][] = array(
                'key'            => 'Order.Collections',
                'value'        => $categories,
                'type'        => 'multiple'
            );
        }

        if (!empty($tagsStringOrder)) {
            $subscription['subscribers']['fields'][] = array(
                'key'            => 'Order.Tags',
                'value'        => $tagsStringOrder,
                'type'        => 'multiple'
            );
        }


        if (!empty($products)) {
            $subscription['subscribers']['fields'][] = array(
                'key'            => 'Order.Products',
                'value'        =>  json_encode($products),
                'type'        => 'json'
            );
        }

        if (get_option($rule['custom_fields']['id'])) {
            $cf = json_decode(get_option($rule['custom_fields']['id']));

            foreach ($cf as $field) {

                if ($field->attribute) {

                    if ($field->source == 'user') {
                        $v = get_user_meta($order->get_id(), $field->attribute, true);
                        $k = 'Subscriber.' . $field->attribute;
                    } else {
                        $v = get_post_meta($order->get_id(), $field->attribute, true);
                        $k = 'Order.' . $field->attribute;
                    }

                    if ($v) {
                        array_push($subscription['subscribers']['fields'], array('key' => $k, 'value' => $v));
                    }
                }
            }
        }

        $api = WP_RuleMailer_API::get_instance();
        $api::subscribe($subscription);
    }

    public static function checkInput() {

        $woorule_api = [];
        $woorule_api['woorule_api_key'] = isset($_GET['woorule_api']) ? $_GET['woorule_api'] : '';
        $woorule_api['woorule_checkout_tags'] = isset($_GET['woorule_checkout_tags']) ? $_GET['woorule_checkout_tags'] : '';
        $woorule_api['woorule_checkout_label'] = isset($_GET['woorule_checkout_label']) ? $_GET['woorule_checkout_label'] : '';
        $woorule_api['woorule_checkout_show'] = isset($_GET['woorule_checkout_show']) ? $_GET['woorule_checkout_show'] : '';

        if(( (isset($_GET['save'])) && ($_GET['save'] == 'woorule') )) update_option('woocommerce_rulemailer_settings', $woorule_api);
    }

    public static function admin_css() {
        echo
        '<style>
        form.woorule {
            margin-top: 20px;
        }
        .woorule .description {
            display: inline-block;
            width: 100%; margin-top: 5px;
        }
        .woorule tr.line {
            border-bottom: 1px solid #ddd;
        }
        .woorule h2 {
            margin: 0;
        }
        </style>';
    }

    public static function settings_page()
    {
        self::checkInput();
        //print_r(get_option('woocommerce_rulemailer_settings'));

    ?>
    <form method="get" class="woorule" action="/wp-admin/options-general.php">
        <a href="https://app.rule.io" target="_blank">
            <img width="128" src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/logo.png" alt="" class="lazyloaded" data-ll-status="loaded">
        </a>
        <input type="hidden" name="page" value="woorule-settings" />
        <input type="hidden" name="save" value="woorule" />


        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th>
                        <h2><?php echo _e('Checkout form', 'woorule'); ?></h2>
                    </th>
                </tr>

                <tr>
                    <th><label for="woorule_checkout_show">Show signup form on checkout</label></th>
                    <td>
                        <input type="checkbox" name="woorule_checkout_show" id="woorule_checkout_show" <?php echo (get_option('woocommerce_rulemailer_settings')['woorule_checkout_show'] == 'on') ? 'checked' : ''; ?> />
                        <span class="description"><?php _e('Display a signup form on the checkout page', 'woorule'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="category_base">Signup form label</label></th>
                    <td>
                        <input name="woorule_checkout_label" id="woorule_checkout_label" type="text" value="<?php echo get_option('woocommerce_rulemailer_settings')['woorule_checkout_label'] ? get_option('woocommerce_rulemailer_settings')['woorule_checkout_label'] : 'Please sign me up to the newsletter!'; ?>" class="regular-text code">
                        <span class="description"><?php _e('Text to display next to the signup form', 'woorule'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="woorule_checkout_tags">Tags</label></th>
                    <td>
                        <input name="woorule_checkout_tags" id="woorule_checkout_tags" type="text" value="<?php echo get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags'] ? get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags'] : 'Newsletter'; ?>" class="regular-text code">
                        <span class="description"><?php _e('Signup form tags (Comma separated)', 'woorule'); ?></span>
                    </td>
                </tr>

                <tr class="line"><th></th><td></td></tr>

                <tr>
                    <th>
                        <h2><?php echo _e('Configuration', 'woorule'); ?></h2>
                    </th>
                </tr>

                <tr>
                    <th><label for="woorule_api">Rule API Key</label></th>
                    <td>
                        <input name="woorule_api" id="woorule_api" type="text" class="regular-text code" value="<?php echo get_option('woocommerce_rulemailer_settings')['woorule_api_key']; ?>">
                        <span class="description"><?php _e('You can find your Rule API key in the <a href="http://app.rule.io/#/settings/developer">developer tab in your Rule account</a>.', 'woorule'); ?></span>
                    </td>
                </tr>

                <!-- <tr>
                    <th>
                        Plugin Documentation: <a href="https://wordpress.org/plugins/woorule/">https://wordpress.org/plugins/woorule/</a>
                    </th>
                </tr> -->

            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
    <?php



    }
}