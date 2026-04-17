## Description

This is a wordpress plugin that extends standard woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file which you store on your google drive and can be edited by any member of your store team.

Standard woocommerce import, that was introduced by woocommerce team since version 3.1, became a greater plugin feature that lets you not use additional plugins and extensions for product import processes. However, if it’s a pain every time when you’re loading csv import files from your local machine, then this file is a great choice that lets you not to do it anymore. Just set your google sheet name that you store on your google drive once and in the future you will only have to press the button "Import" as usual. Plugin itself will pull the new data from the specified google sheet table.

## Installation
It's a recommended to use the [official plugin page on WordPress.org](https://wordpress.org/plugins/import-products-from-gsheet-for-woo-importer/).

Also as option you can directly install plugin from github repository.
1. Clone repository to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.


## Set plugin options

After plugin installation, we need to set a connection with the Google API. In the plugin settings, you have to set the Google Drive API client_secret json.

You need to set connection once and in the feature you can process import woocommerce product with your google sheet file every time you need.


### Set connection wtih client_secret json code.

1. If you do not have a google API client_secret json code than go to [google cloud console page.](https://console.developers.google.com) where you can create new one (if you have than you can go to step 11)


2. Go to [API Library page ](https://console.cloud.google.com/apis/library) and in the search input type "Google Drive API" ![](assets/images/screenshot-2.png) 


3. Click on a first search result and enable "Google Drive API" ![](assets/images/screenshot-3.png)


4. Then you can create new credentials.  Just click button "Create Сredentials" ![](assets/images/screenshot-5.png)


5. In the next page, the system  will ask you to fill the form with data for your credentials, please fill it in the same way as you can see on the screenshot below ![](assets/images/screenshot-6.png)


6. In the next step you will be redirected to 'Create service account' page and you will see the form for it, feel free to choose any service account name in the appropriate field and select role project -> editor, than skip "Grant users access to this service account" and press "Done" button. ![](assets/images/screenshot-7.png) After it you should have a newly created service account.


7. Then you can go to your [credentials page](https://console.cloud.google.com/apis/credentials) and find your newly created 'Service Account' click on it ![](assets/images/screenshot-8.png)


8. Then find the key tab and create a new key client_secret for your service account with json format. ![](assets/images/screenshot-9.png)


9. Please find client_email in client_secret json and copy it to your buffer for the next step.


10. Open your google sheet import file on your google drive and share access to it with client_email that you copy to buffer in previous step ![](assets/images/screenshot-10.png)


11. Please copy the client_secret json key (all file content file) to appropriate input in the option plugin page and save it. ![](assets/images/screenshot-11.png)


12. After it, you should see select with google sheets list for your import, pick one of them and press 'Save Options'. ![](assets/images/screenshot-12.png)


That’s all, if you set valid data you will see a success message, and when you next time try to import woocommerce product you will see an additional button that gives you the opportunity to import product from google sheet. ![](assets/images/screenshot-4.png)
