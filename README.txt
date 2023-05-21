=== GSheet For Woo Importer ===
Contributors: mrdollar4444
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K9NTJ6E2RQN3A&source=url
Tags: woocommerce, importer, google sheet
Requires at least: 4.7
Tested up to: 6.2
Stable tag: 2.3
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Plugin that extends woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file.

== Description ==

Import woocommerce products from google sheet by using native woocommerce importer.

Standard woocommerce import, that was introduced by woocommerce team since version 3.1, became a great plugin feature that lets you not use additional plugins and extensions for product import processes. However, if it’s a pain every time when you’re loading csv import files from your local machine, then import-products-from-gsheet-for-woo-importer is a great choice that lets you not to do it anymore. Just set your google sheet name that you store on your google drive once and in the future you will only have to press the button "Import" as usual. Plugin itself will pull the new data from the specified google sheet table.

This plugin that extends standard woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file which you store on your google drive and can be edited by any member of your store team.

Full instruction about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer) or in a section with screenshots below.

== Installation ==
1. Upload the plugin folder to the ‘/wp-content/plugins/’ directory
2. Activate the Woocommerce Import Products Google Sheet plugin through the ‘Plugins’ menu in WordPress
3. Go to Woocommerce Import Products Google Sheet Settings menu to connect your Google sheet and woocommerce import.
4. Full instruction about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer)

== Frequently Asked Questions ==

= Where I can get "Google drive API client_secret json" for a plugin settings  =

Instructions about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer)
Also you can follow instructions on a screenshots above.

== Screenshots ==

1. Go to plugin setting and press "Get Code" button in "One Click Auto Connect" tab.

2. You will be redirected to google plugin application page. Please choose google account where you store your google sheet import file on google drive.

3. In the next page you need provide access "See and download all your Google Drive files." to plugin application.

4. Then you will be redirected back to the plugin settings page with your access code on it. Please press "Save Options" button.

5. If code valid you will see corresponding message and new select for google sheet title and then you must to choose google sheet title that become your import file.

6. Sheet title you can find in upper left corner of your sheet on google drive.

7. That all. If you set all settings properly you will receive success connection message with link to standard woocommerce import page where you can process import products with your google sheet file.

== Changelog ==

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
