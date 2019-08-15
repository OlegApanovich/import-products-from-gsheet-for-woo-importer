## Description

This is wordpress plugin, that extend standard woocommerce product import and let you import not only from local file but also from your google sheet file which you store on your google drive and can be edited by any member of your store team.

Standard woocommerce import that was introduced by woocommerce team since 3.1 version became a greater plugin feature, that let you not use additional plugins and extensions for product import process. But if you feel a pain every time when load csv import file from your local machine, then woocommerce-import-products-google-sheet is a great choice, that let you do not do it anymore. Just set once google sheet name that you store on your google drive and in the future you only press the button "Import" as usual. Plugin itself will pull the new data from the specified google sheet table.

## Installation
1. Upload the plugin to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Set plugin option

After plugin installation we need to get client_secret json of google drive api.

Below the instraction how we can recieve client_secret json.

1. Set your standart woocmmerce import file to google drive.

2. Click link https://console.developers.google.com where you can find google console developer.

3. In the seach input type "google drive" and click on the first tip "Google Drive API" (assets/images/readme_1.png)

4. If it is your first project in google developer console and you never create project before, then system ask you create new one, click button "Create" (./assets/images/readme_2.png)

5. Then you will be redirect to project settings page, for us it will be enough standard project options and you can just click button "Create" on this page (./assets/images/readme_3.png)

6. In the next page system ask you enable google drive api for your new project, let's do it, just press button "Enable" (./assets/images/readme_4.png)

7. Then you will be redirect to main google drive api page info where you can create new credetians if you do not create it before, just click button "Create Credetials" (./assets/images/readme_5.png)

8. In the next page system ask you fill the form with data for your credetians, plese fill it the same as you can see on the screenshot below and press button "What credetians do I need" (./assets/images/readme_6.png)

9. In the next page you will see the next form for your credetians, fill free choose any service account name in the appropriate field and select role project -> editor (./assets/images/readme_7.png), key button type leave default json and press button "Continue" (./assets/images/readme_7.png)

10. After it system will offer you download api key json file, save it on your local machine and after it you can close google developer console browser tab.

11. Plese copy client_secret json key that we recieved in previos step to apropriate input in option plugin page (./assets/images/readme_8.png)

12. Plese find client_email in client_secret json and copy it to your buffer for a next step.

13. Open your google sheet file that you set to your google drive in step 1 and share access to it with client_email email that you copy to buffer in previous step (./assets/images/readme_9.png)

14. Then copy google sheet file to apropriate plugin setting field "Google sheet title" and press submit button. [(./assets/images/readme_0.png)

Thats all, if you set valid data you will see success message, and when you next time try to import woocommerce product you will see additional button that give you oportunity import product from google sheet. 11.png
