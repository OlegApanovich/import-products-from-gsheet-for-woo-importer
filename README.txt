=== GSheet For Woo Importer ===
Contributors: mrdollar4444
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K9NTJ6E2RQN3A&source=url
Tags: woocommerce, importer, google sheet
Requires at least: 4.7
Tested up to: 5.6
Stable tag: 1.1.5
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Plugin that extends woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file.

== Description ==

This is a wordpress plugin that extends standard woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file which you store on your google drive and can be edited by any member of your store team.

Standard woocommerce import, that was introduced by woocommerce team since version 3.1, became a great plugin feature that lets you not use additional plugins and extensions for product import processes. However, if it’s a pain every time when you’re loading csv import files from your local machine, then import-products-from-gsheet-for-woo-importer is a great choice that lets you not to do it anymore. Just set your google sheet name that you store on your google drive once and in the future you will only have to press the button "Import" as usual. Plugin itself will pull the new data from the specified google sheet table.

Full instruction about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer) or in a section with screenshots below.

== Installation ==

1. Upload the plugin folder to the ‘/wp-content/plugins/’ directory
2. Activate the Woocommerce Import Products Google Sheet plugin through the ‘Plugins’ menu in WordPress
3. Go to Woocommerce Import Products Google Sheet Settings menu to make main settings.
4. Full instruction about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer)

== Frequently Asked Questions ==

= Where I can get "Google drive API client_secret json" for a plugin settings  =

Instructions about plugin options you can find on [our github plugin page](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer)
Also you can follow instructions on a screenshots below.

== Screenshots ==

1. Set your standard woocommerce import file to google drive. Click [link](https://console.developers.google.com) where you can find google console developer. In the search input type "google drive" and click on the first tip "Google Drive API".

2. If it is your first project in google developer console and you have never created a project before, then the system will ask you to create a new one, click button "Create".

3. Then you will be redirected to the project settings page, for our needs, there will be enough standard project options and you can just click button "Create" on this page.

4. In the next page, the system will ask you to enable google drive api for your new project, then just press button "Enable".

5. Then you will be redirected to the main google drive api info page where you can create new credentials if you have not created them before. Just click button "Create Сredentials".

6. In the next page, the system  will ask you to fill the form out with data for your credentials, please fill it in the same way as you can see on the screenshot below and press the button "What credentials do I need".

7. In the next page you will see the next form for your credentials, feel free to choose any service account name in the appropriate field and select role project -> editor, key button type leave default json and press button "Continue".

8. After all, the system will offer you to download the api key json file. Save it on your local machine and then you can close google developer console browser tab. Please copy the client_secret json key (all file content) that you have received in the previous step to appropriate input in option plugin page. Please find client_email in client_secret json file and copy it to your buffer for the next step.

9. Open your google sheet file that you set to your google drive in step 1 and share access to it with client_email email that you copy to buffer in previous step.

10. Then copy the name of the google sheet file to appropriate plugin setting field "Google sheet title" and press the submit button.

11. If you properly set your setting then after all you can find additional import button near standard import woocommerce button on a product page.

== Changelog ==

= 1.1.5 =
* Fix - Fix PHP fatal error on a log activity #9
* Fix - Fix PHP warnings on setting plugin page.
* Fix - Fix link to setting page.
* Dev - Add WP v-5.6 and PHP v-8 compatibility.

= 1.0 =
* Initial release.

== Upgrade Notice ==

[See the release history.](https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer/releases)
