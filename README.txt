=== WooRule ===
Contributors: lurig, neevalex
Tags: rulemailer, woocommerce, newsletter
Requires at least: 5.0.0
Tested up to: 5.2.0
License: MIT
License URI: http://opensource.org/licenses/MIT

A [RuleMailer](https://www.rule.se/) integration with WooCommerce.

== Description ==

Subscribe your customers on various order events.
Supports multiple and different events.

== Installation ==

1. Upload and extract to your `/wp-content/plugins/` directory and activate it from Wordpress Admin panel.
Or upload and activate it from wordpress catalog.

2. Go to Woocommerce -> Settings -> Integration -> RuleMailer and fill in an `API Key`, and click 'save' button. You can find your RULE API key inside developer tab in [user account settings](http://app.rule.io/#/settings/developer)

== Shortcode ==

You can embed a Newsletter sign-up form in your posts, or on any page with a simple shortcode [woorule]
 
You can also customise the subscribe form with the shorcode options:
[woorule text="This is a new title text" button="New submit button text!" success="New Success message"]


= Usage =
NOTE: By default WooRule will create the "Defaul Rule" that will trigger the data sync for all new orders, tagged as "New Order"
1. Enable your plugin
2. Go to WooCommerce -> Settings -> Integrations -> RuleMailer and submit an API key and URL.
3. A new tab should appear in the WooCommerce Settings called WooRule (if not, try refresh the page).

== Changelog ==

= 0.0.1 =
* Non-public release.

= 0.2 =
* New Version public release

= 0.3 =
* Bugfixes

= 0.4 =
* Bugfix. Missing assets.

= 0.5 =
* New features. Bugfixes. Newest WP support

= 0.6 =
* New features. Bugfixes.

= 1.1 =
User\order meta fields.
Default data improvements
Bugfixes
Visual Adjustments