=== WooRule ===
Contributors: rulecom
Tags: rule, woocommerce, newsletter, marketing
Requires at least: 5.0.0
Tested up to: 6.3.2
Requires PHP: 5.6+
Stable tag: 3.0.4
License: MIT
License URI: http://opensource.org/licenses/MIT

A [Rule](https://www.rule.io/) integration with WooCommerce.

== Description ==

The WooRule plugin is developed by Rule as an integration between WooCommerce and the Rule marketing automation platform.

The Rule platform is an intuitive and user-friendly digital communication service that streamlines the external communication for companies and organization of any size. Rule enables you to send hyper-personalized and automated digital communications via email and SMS to your customers.

After installing this integration, your WooCommerce data will start sending to Rule. With this data you can enable many e-commerce communication flows such as: newsletters, abandoned cart, order followup, shipping followup, customer retention, customer winback, welcome communications, and much more!

= Usage =

WooRule has automatic event triggers that send data to Rule when a customer places an order. Tags will be applied to the customer within the Rule platform, which can then be used to send automated communications to subscribers (your customers). Data is saved as custom fields, and include information about the subscriber (e.g. name, address, email, phone number), and their order (products ordered, order date, price, discounts, collection, and much more!).

The following events are used to apply the following tags in the customer order flow:

`
#  Event Trigger   Tag Name          Event Description
1  cart updated    CartInProgress    Cart contents are updated
2  processing      OrderProcessing   Order is paid and awaiting fulfillment
3  completed       OrderCompleted    Order fulfilled and complete
4  shipped         OrderShipped      Order was shipped*
`
*This is a custom event trigger that will not trigger unless added by the merchant.
More information regarding order events in WooCommerce can be read [here](https://docs.woocommerce.com/document/managing-orders/).

WooRule also includes an optional checkout signup form where customers can easily opt-in to automated marketing communications, such as newsletters.

= Shortcode =

You can embed a Newsletter sign-up form in your posts, or on any page with a simple shortcode `[woorule]`

You can also customize the sign-up form with any of the following shortcode attributes:

`
[woorule title="Custom Title" placeholder="Custom Placeholder Text" button="Custom Button Text" checkbox="Custom Text Next To Checkbox" success="Custom Success Message" tag="Custom Tag" require_opt_in=false]
`

The `checkbox` attribute will add a checkbox below the signup form. If this attribute is present then checking the checkbox is required before the form can be submitted. This is useful for ensuring the subscriber agrees to your terms before being added to a mailing list.

The `tag` attribute will be applied to the subscriber when the form is submitted. If no tag field is used a `Newsletter` tag will be applied by default.

The `require_opt_in` attribute, if set to `true`, will require the subscriber to accept an opt-in email before further marketing emails can be sent. The opt-in flow requires you to have a [subscriber form](https://app.rule.io/#/subscriber/subscriber-form) setup in your Rule account in order for the opt-in email to be sent to the subscriber. Note that the `tag` attribute should be the same as the form tag.


== Frequently Asked Questions ==

= Does it cost anything to use this plugin? =

All of Rule’s integrations are offered for free!

= Do I need an account in Rule? =

In order to take advantage of this integration you will need an account in Rule. You can [sign up for a free account](https://www.rule.io/sign-up-free/) and start using Rule in just a few minutes!

= Is it difficult to integrate? =

No, just follow the simple setup steps and you’re up and running in minutes! We have [extensive documentation](https://en.docs.rule.se/) and support staff to help get you started with automating your digital marketing and communications.

= How do I migrate from version 1.x? =

In versions 2.0+, all manual mailing rules are removed and are replaced with automatic event triggers. The same data is sent to Rule, however the event tag names may differ from the tags you originally set up. Refer to the [Usage](https://wordpress.org/plugins/woorule/#details) section for more information on the exact events and tags that are used.

To make sure your automations in Rule are not interrupted with the updated tag names, make sure to either update the tags associated with your automation, or [create a tag filter](https://en.docs.rule.se/article/172-filter-sa-fungerar-det) to trigger an automation based on both the old tag names and the new tag names.

Do not hesitate to [contact support](https://www.rule.io/contact/) if you have questions or require assistance with upgrading.

== Installation ==

= Before You Start =

* This plugin requires an API key from an active Rule account to correctly function. If you don't have an account with Rule, you can [sign up free](https://www.rule.io/sign-up-free/) in just a few clicks.
* This plugin requires you to have [WooCommerce](https://woocommerce.com/) installed and activated on your WordPress site.

= Installing The Plugin =

Search for "WooRule" under "Plugins" → "Add New" in your WordPress dashboard to install the plugin.

Or follow these steps to install the plugin manually:

1. Download the [plugin zip file](https://downloads.wordpress.org/plugin/woorule.zip)
2. In your WordPress dashboard, navigate to "Plugins" → "Add New" → "Upload Plugin"
3. Select the downloaded .zip file and click on "Install Plugin"
4. Activate the plugin

= Getting Started =

To connect WooCommerce to your Rule account go to the WooRule plugin and paste in your API key and click "Save Changes". You can find your Rule API key on the developer tab in your [Rule account settings](http://app.rule.io/#/settings/developer).

If you are just getting started with Rule, you can visit Rule's [Documentation Page](https://en.docs.rule.se/) which guides you through creating automated communications for your subscribers using the data sent from WooRule.

== Screenshots ==

1. Main WooRule plugin settings page
2. Example shortcode signup form

== Changelog ==

For more information, check out our [releases](https://github.com/rulecom/woorule/releases).

= 3.0.4 =
* Products in orders now include slug so it can be used in links
* Email marketing checkbox in Klarna is now off by default to comply with standards

= 3.0.3 =
* Added method for wakeup calls required by some plugins

= 3.0.2 =
* Additional testing for Wordpress 6.3 and WooCommerce 8.0.3

= 3.0.1 =
* Added flag for High-Performance Order Storage (HPOS) support

= 3.0.0 =
* Updated flow for CartInProgress tags to follow our best practices. Read below what you need to adjust if you use this function.
* Changed CartInProgress so it will save to custom group Cart instead of Order. Prepare your automations in Rule that triggers on CartInProgress so they will use data from Cart group.
* When making above change take into consideration your interval on the automations. Triggers created before the upgrade will still have their data in Order group.
* These changes will create a more accurate history of your orders in Rule while separating unfinished carts into their own data set.

= 2.8.0 =
* Added product alert functionality

= 2.7.6 =
* Improved WooCommerce plugin detection, which in some rare cases would cause WooRule to not load

= 2.7.5 =
* Fix for incorrectly formatted date fields

= 2.7.4 =
* Added automatic phone number country codes (if not provided)
* Improved invalid phone number handling

= 2.7.3 =
* Fix for bug affecting Klarna Checkout form

= 2.7.2 =
* Temporary rollback for bug affecting Klarna Checkout

= 2.7.1 =
* Fixed checkout form translation bug

= 2.7.0 =
* Added an optional `checkbox` attribute to the woorule shortcode, which must be checked before the form can be submitted
* Added an optional `require_opt_in` attribute to the woorule shortcode, which sends the subscriber an opt-in email before they can received additional marketing emails
* Fixed Cart In Progress bug
* Improved logging

= 2.6.0 =
* Add fields: `Order.CartUrl` (for Cart in Progress) and `Order.OrderUrl` (for orders)
* Added localization support to plugin

= 2.5.2 =
* Fix for Cart In Progress

= 2.5.1 =
* Cart In Progress rollback

= 2.5.0 =
* Added new event trigger: Cart In Progress
* Added Klarna Checkout integration
* Added field: `Order.ShippingVat` (shipping incl. tax)
* Prices sent to Rule will now match the store's currency decimal setting

= 2.4.0 =
* Refactored plugin to adhere to best practices and improve plugin stability
* Fixed `Order.Subtotal` calculation (now excludes order tax)
* Added field: `Order.SubtotalVat` (subtotal incl. cart tax)

= 2.3 =
* Added Order.Date for event `processing`
* Added fields to products line items: `Products.price_vat` (price incl. vat) and `Products.total` (line item total incl. vat)

= 2.2 =
* Bugfix affecting Newsletter tags on checkout form
* Added field: `Order.Names`
* Added "tag" field to WooRule shortcode

= 2.1 =
* Bugfix affecting PHP 8.0

= 2.0 =
This is a major release with a focus on streamlining the user experience and setup process. If you are upgrading from an earlier version, refer to the [FAQ](https://wordpress.org/plugins/woorule/#faq) to avoid any disruptions in service.

* Moved WooRule settings page to the menu bar
* Moved API key entry to main plugin page
* Removed configurable mailing events. Events will now automatically trigger without any setup. Refer to the plugin documentation for more details.
* Moved checkout signup form for newsletters to the main plugin page
* Overhauled UI and added field descriptors
* Per-product VAT amount is now included in the order data
* Other small bug fixes

= 1.4 =
* Bugfixes

= 1.3 =
* Bugfixes

= 1.2 =
* Bugfixes

= 1.1 =
* User\order meta fields
* Default data improvements
* Bugfixes
* Visual Adjustments

= 0.6 =
* New features
* Bugfixes

= 0.5 =
* New features
* Bugfixes

= 0.4 =
* Fixed missing assets

= 0.3 =
* Bugfixes

= 0.2 =
* New Version public release

= 0.0.1 =
* Non-public release
