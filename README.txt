=== GSheet For Woo Importer ===
Contributors: mrdollar4444
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K9NTJ6E2RQN3A&source=url
Tags: woocommerce, importer, google sheet
Requires at least: 5.9
Tested up to: 6.9
Stable tag: 2.4
Requires PHP: 8.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Enhances WooCommerce import by allowing product imports directly from Google Sheets stored on Google Drive.

== Description ==

This is a wordpress plugin that extends standard woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file, which you store on your google drive and can be edited by any member of your store team.

Standard woocommerce import, which was introduced by woocommerce team since version 3.1, became a greater plugin feature that lets you not use additional plugins and extensions for product import processes. However, if it’s a pain every time when you’re loading csv import files from your local machine, then this plugin is a great choice that lets you not do it anymore. Just set it once, and in the future you will only have to press the button "Import" as usual. The plugin itself will pull the new data from the specified google sheet table.

Full instruction about plugin setup options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer).

== Installation ==
1. Upload the plugin folder to the ‘/wp-content/plugins/’ directory
2. Activate the Woocommerce Import Products Google Sheet plugin through the ‘Plugins’ menu in WordPress
3. Go to Woocommerce Import Products Google Sheet Settings menu to connect your Google sheet and woocommerce import.
4. Full instruction about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer)

== Frequently Asked Questions ==

= Where I can get "Google drive API client_secret json" for a plugin settings  =

Instructions about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer).

== Screenshots ==

== Changelog ==

= 2.4.0 =
* Dev - Add support for Microsoft Excel native formats.
* Dev - Add redirect to plugin settings after plugin activation.
* Dev - Bump min PHP version to 8.1 and min WordPress version to 5.9.
* Fix - Remove 'One Click Auto Connect' due to Google API new payment requirements that do not correspond to nonprofit open source projects.
* Fix - PHP fatal error when WooCommerce is not active.

= 2.3.1 =
* Fix - Php warnings related to translation.
* Fix - Php fatal error with deleted woocommerce.

= 2.3 =
* Fix - 'One Click Auto Connect' Google API connect method and remove deprecated oob link functionality.

= 2.2.0 =
* Fix - fix error with csv get functinality.

= 2.1.1 =
* Fix - move plugin functionality to new Google v4 API

= 2.0.3 =
* Fix - Php warnings with not stable google API library.

= 2.0.2 =
* Dev - Add new google API connection "Auth Code" method.
* Dev - Add notice on product page if plugin setting do not set completely.
* Dev - Add new system error notification.

= 1.1.5 =
* Fix - Fix PHP fatal error on a log activity #9
* Fix - Fix PHP warnings on setting plugin page.
* Fix - Fix link to setting page.
* Dev - Add WP v-5.6 and PHP v-8 compatibility.

= 1.0 =
* Initial release.

== Upgrade Notice ==

[See the release history.](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer/releases)
