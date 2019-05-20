# WooRule

A [Rule Communication](https://www.rule.se/) integration with WooCommerce.

## Requirements

- Wordpress >= 5.1
- WooCommerce >= 3.5

## Installation

1. Either download the latest release and upload it to your `/wp-content/plugins/`
directory or grab it from [Wordpress Plugin
Directory](http://wordpress.org/plugins/woorule/).

2. Activate the plugin under the _Plugins_  from Wordpress Admin panel.

3. Go to _Woocommerce_ -> _Settings_ -> _Integration_ -> _RuleMailer_, fill in your `API Key`, and click the 'save' button. You can find your RULE API key on the developer tab in your [Rule account settings](http://app.rule.io/#/settings/developer).

## Usage

_NOTE: By default WooRule will create the "Defaul Rule" that will trigger the data sync for all new orders, tagged as "New Order"_

1. Go to the _RuleMailer tab_ and click _Add new_ and a new Rule appears in the
   list.

2. Press _Edit_ on the new Rule.

3. Give it a good name and fill in the rest according to your _RuleMailer_ setup.

### Shortcode

You can embed a Newsletter sign-up form in your posts, or on any page with a simple shortcode `[woorule]`
 
You can also customise the subscribe form with the shortcode options:
`[woorule text="This is a new title text" button="New submit button text!" success="New Success message"]`



### Resources

- [Rule documentation](https://docs.rule.se/).
- [WooCommerce](http://docs.woothemes.com/documentation/plugins/woocommerce/).

## License

Please see [LICENSE.txt](/LICENSE.txt).

## Changelog

### 0.0.1
- Not yet released

### 0.2
New Version public release. (New API, Shortcode, Settings)

### 0.3
Bugfixes

### 0.4 
Missing Assets

### 0.5 
New features. Bugfixes. Newest WP support