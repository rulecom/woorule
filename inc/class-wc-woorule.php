<?php
class Woorule
{

    const DELIMITER = ',';

    public static function init()
    {
        add_action('admin_menu', __CLASS__ . '::settings_page_init' );
        add_action('admin_head', __CLASS__ . '::admin_css');
        // This will add the direct "Settings" link inside wp plugins menu.
        add_filter('plugin_action_links_woorule/woorule.php', __CLASS__ . '::settings_link' );
        // Orders hook
        add_action('woocommerce_order_status_changed', __CLASS__ . '::order_status_changed', 10, 4);
        // newsletter subscribe button on checkout
        add_action('woocommerce_review_order_before_submit', __CLASS__ . '::custom_checkout_field');
        add_action('woocommerce_checkout_update_order_meta', __CLASS__ . '::custom_checkout_field_update_order_meta');
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
            plugins_url( 'woorule/assets/fav.png' ),
            6
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
                'label'     => get_option('woocommerce_rulemailer_settings_checkout_label')
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

    public static function order_status_changed($id, $status = '', $new_status = '')
    {

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
                'price' => $p->get_price_excluding_tax(),
                'vat' => ($p->get_price_including_tax() - $p->get_price_excluding_tax()),
                'qty' => $item->get_quantity(),
                'subtotal' => $item->get_total()
            );

            $categoriesString = strip_tags(wc_get_product_category_list(
                $item['product_id'],
                self::DELIMITER
            ));
            $tagsString = strip_tags(wc_get_product_tag_list(
                $item['product_id'],
                self::DELIMITER
            ));

            if (!empty($categoriesString)) {
                $itemCategories = explode(self::DELIMITER, $categoriesString);
                $categories = array_unique(array_merge($categories, $itemCategories));
            }

            if (!empty($tagsString)) {
                $itemTags = explode(self::DELIMITER, $tagsString);
                $tags = array_unique(array_merge($tags, $itemTags));
            }
        }

        $order_data = $order->get_data();
        array_push($tags, $new_status ? $new_status : 'new'); // $new_status is empty on new orders.
        if(get_post_meta($id, 'woorule_opt_in', true) == 'true')  array_push($tags, 'newsletter');

        if(!empty(get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags'])) {
            foreach(explode(",", get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags']) as $t) {
                array_push($tags, $t);
            }
        }

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

        if (!empty($tags)) {
            $subscription['subscribers']['fields'][] = array(
                'key'            => 'Order.Tags',
                'value'        => $tags,
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
        <img width="123" height="32" src="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/logo.png" alt="" class="lazyloaded" data-ll-status="loaded">
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
                    <th><label for="woorule_checkout_show">Show checkout checkbox</label></th>
                    <td>
                        <input type="checkbox" name="woorule_checkout_show" id="woorule_checkout_show" <?php echo (get_option('woocommerce_rulemailer_settings')['woorule_checkout_show'] == 'on') ? 'checked' : ''; ?> />
                        <span class="description"><?php _e('Display a signup form on the checkout page', 'woorule'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="category_base">Checkout Label</label></th>
                    <td>
                        <input name="woorule_checkout_label" id="woorule_checkout_label" type="text" value="<?php echo get_option('woocommerce_rulemailer_settings')['woorule_checkout_label'] ? get_option('woocommerce_rulemailer_settings')['woorule_checkout_label'] : 'Sign up to the newsletter'; ?>" class="regular-text code">
                        <span class="description"><?php _e('Text to display next to the signup form', 'woorule'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="woorule_checkout_tags">Tags</label></th>
                    <td>
                        <input name="woorule_checkout_tags" id="woorule_checkout_tags" type="text" value="<?php echo get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags'] ? get_option('woocommerce_rulemailer_settings')['woorule_checkout_tags'] : 'Newsletter'; ?>" class="regular-text code">
                        <span class="description"><?php _e('Default tags (Comma separated)', 'woorule'); ?></span>
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
                        <span class="description"><?php _e('You can find your RULE API key inside <a href="http://app.rule.io/#/settings/developer">developer tab on user account settings</a>.', 'woorule'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
    <?php



    }
}