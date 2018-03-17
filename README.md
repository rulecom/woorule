# WooRule

A [RuleMailer](https://www.rule.se/) integration for WooCommerce.


## Requirements

- Wordpress >= 4.2
- WooCommerce >= 3.0

## Installation

1. Either download the latest release and upload it to your `/wp-content/plugins/`
directory or grab it from [Wordpress Plugin Directory](http://wordpress.org/plugins/woorule/).

2. Activate the plugin under the _Plugins_ page in Wordpress Admin panel.

3. Go to _Woocommerce_ -> _Settings_ -> _Integration_ -> _RuleMailer_ and fill in an `API
   Key`, and click 'save' button. You can find your RULE API key inside developer tab on [user account settings](http://app.rule.io/#/settings/developer)

## Usage

1. Go to the _RuleMailer tab_ and click _Add new_ and a new Rule apears in the
   list.

2. Press _Edit_ on the new Rule.

3. Give it a good name and fill in the rest according to your _RuleMailer_ setup.

## Shortcode
You can embed a Newsletter sign-up form in your posts, or on any page with a simple shortcode `[woorule]`

You can also customise the subscribe form with the shorcode options:
`[woorule text="Do you want to become a part of something big?" button="Do it!" success="Thank you!"]`


### Resources

- [RuleMailer](https://www.rule.se/support/manual/).
- [WooCommerce](http://docs.woothemes.com/documentation/plugins/woocommerce/).

## License

Please see [LICENSE.txt](/LICENSE.txt).

## Changelog

### 0.0.1
- Not yet released

### 0.2
- New version. (WC 3.0+ Support, Shortcode, Updated API, New Settings. )