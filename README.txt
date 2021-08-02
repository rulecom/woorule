=== WooRule ===
Contributors: lurig, neevalex
Tags: rulemailer, woocommerce, newsletter
Requires at least: 5.0.0
Tested up to: 5.8.0
License: MIT
License URI: http://opensource.org/licenses/MIT

A [RuleMailer](https://www.rule.se/) integration with WooCommerce.

== Description ==

Subscribe your customers on various order events.
Supports multiple and different events.

== Installation ==

1. Either download the latest release and upload it to your `/wp-content/plugins/` directory or grab it from [Wordpress Plugin Directory](http://wordpress.org/plugins/woorule/).

2. Activate the plugin under the _Plugins_  from Wordpress Admin panel.

3. Go to _Woocommerce_ -> _Settings_ -> _Integration_ -> _RuleMailer_, fill in your `API Key`, and click the 'save' button. You can find your RULE API key on the developer tab in your [Rule account settings](http://app.rule.io/#/settings/developer).

= Usage =
NOTE: By default WooRule will create the "Default Rule" that will trigger the data sync for all new orders, tagged as "New Order"

1. Go to the _RuleMailer tab_ and click _Add new_ and a new Rule appears in the list.

2. Press _Edit_ on the new Rule.

3. Give it a good name and fill in the rest according to your _RuleMailer_ setup.

== Shortcode ==

You can embed a Newsletter sign-up form in your posts, or on any page with a simple shortcode `[woorule]`

You can also customize the subscribe form with the shortcode options:
`[woorule text="This is a new title text" button="New submit button text!" success="New Success message"]`

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

= 1.2 =
Bugfixes

= 1.3 =
Bugfixes

= 1.4 =
Bugfixes

= 1.5 =
UI improvements.