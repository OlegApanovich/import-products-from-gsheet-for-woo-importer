############################
# Checklist before reliaze #
############################
1. file_put_contents('/var/www/html/test.html'
2. console.log
3. debug.log.
4. консоль ошибки.
5. phpcs.
6. update languges

#######################
# Release new version #
#######################
1. Clone svn rep
svn co http://svn.wp-plugins.org/import-products-from-gsheet-for-woo-importer
2. Update trunk folder with last verstion data
3. Remove off dev repository files
r .*; r composer*; r LICENSE; r phpcs.xml; r README.md; r assets/*.png; r config;
r vendor/google/apiclient-services/src/!("Drive"|"Drive.php"|"Oauth2"|"Oauth2.php"|"Sheets"|"Sheets.php")
4. Update tag version in plugin main file.
5. Add all new files to svn
svn add --force  trunk/*
6. Create new verstion in tags
svn cp trunk tags/2.2.0
7. Push new verstion ( login: mrDollar pass: )
svn ci -m "tagging version 2.2.0"
8. Clone rep again and test new verstion if all test pass we update stable verstion tag in README.txt add new features to changelog and push again
svn ci -m "set stable tag verstion 2.2.0"
9. Create new tag in gihub repo and new realize

#################
# Documentation #
#################

// application google cloud console page 
https://console.cloud.google.com/apis/credentials/consent?project=gsheet-product-importer

// google php api client code base
https://github.com/googleapis/google-api-php-client 

// google sheet api docs
https://developers.google.com/sheets/api/reference/rest

##########
# Access #
##########
// plugin testing google account
# gmail
gsheetplugintest@gmail.com
# pass
7141141AoV


// client_secret json for manual connect test 
{
  "type": "service_account",
  "project_id": "barbar-264615",
  "private_key_id": "4e3ebc4634b5a9b4084bd68c9697961a99462029",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCdSp2Ior7fm5ps\nO/VbLEuj4RTAfM2JuftmxszyxFKpUN7p5wEzio9PWSsAfHU+zs8BEGfDm7eQSKE8\nMXV6tGQ4dZumxsJFPWTDk3Mhld075sIgZpawznqqtUot6bSP5oS55QR1NYh9yeeN\nAeKG1n67/v+wXSmWX99/eJd0qFMYThm9XGHM6xu8Ao3+BdOjjgKgY1H1/8IYTmA9\nCuRrCQnqO5ukTgqDiTqmyTsCFkVU1WGQraNQ1HvnFTq3aEZms/HCevdkcNcIOYrt\ndN06Ii7feSAJkmbU6mRS4xjtvHMzik6dARv6ikH8Axe1Vd7V7PQtl7wKch78YdmX\nHbeQr34PAgMBAAECggEACVm+M7k5D6H4dCa8V0cy1/MxgJ/quB1OTLXUyTnSkC42\nkkEhWScLjfQHEmhTsAArXizTflVrfDBVJXbrPPgsXQ7gyfFW6zbTHq1NUtnN5Rm6\nwKJYovojYLKWTlRuX3+ctFBhC3a7Pn8aPM+337wwnIx0Ns/TARWB+9n3Zv/6sJ8c\nG/IudruyhhTlAXFFLkXqs5O0RjPvEJvJ+WxAi46zlfP0HnK9v2zcE5qZjCadksem\n919ATftS5d+/UXCOtB6mUJrBiVb0asLinhNGn7Xyb2zoHqBiADnHa+IftHVdbtgj\ndLybf9k5aLH13uEf4u4KHDOk+AQ4zy25hq5SV0nxQQKBgQDMK8ifEIJzOvv5Zo5X\n7/CWu82433y8Pk0Nsv4L5NVvwN3WInGUzKtZefSKJFHrrBL4lXPaC21WLng3vS0j\nTyXJm58B9wg/MXgCLqdx/u5m1kQUQiupQoyD/eTbdrNEuCCbJejSk2KOb5T0Mk3z\nGtGCaqPW5zT+TLJ7DRyejJ0dOwKBgQDFOFN4joikLoi+OolyRQVwhpg8cfbZZaUp\nMdrHMJE6Ly3MtXOTiPZbGXfVvr3tzkOcCQKcxW1lxhtbNipOakJM/10h1wkRrlNQ\n9iLLO212uu0POfa7bdjn2E71BBt0pRmmANDHy8XYOBogTXqY70jYpSMg0rvCXk2M\ngiEYpbIlPQKBgH8tmsdyaj/K8yAUgQBH5q6OB4RsOe+sQyUQZO0Vutnk4oo7ZFLS\n9r1CmU/fdeP+iMatmb+ttIqlYZ8eyNoguCIQPQjlTw7GCsIZO5ZnvSrztu6DlVzW\npl6lrYQDOYHJzA24nIFm61JcMQW3vBR9lRnOwYXg+YKaVecOcNBWOJv3AoGARZY+\nPYdtRyD0NsrIvH0GElIrXQiJJOPshsCEhUvpsjH7YwOTKDdnVXWDBvQqZ8IjsOas\n+Uvf8c0Y0fIms0xi4HAqGEqbdJWh6Csw06zATuhdxMWa/T8hDY0RLvqoBVxL1Hrt\nL9ICmOwSq9sqqtOjTG3YGzi/7zD/A9jWfK/aT7ECgYBpFw275DBaQaJoPIf9x6CK\nZivMEVdpMEVjqs6hgW5VXRbRAdlplYp0Nr0zzxwDSvpIy4ETpYlXarnKLvVwfZD9\nX0MTu/6hWTIRMNWoo7nVdkWwAiEf0UusCTPhxlus2R/5+I6y171ZTysE1vROkQvi\ndh61XGYVkf/emDHyZqxfyQ==\n-----END PRIVATE KEY-----\n",
  "client_email": "gseetp-plugin@barbar-264615.iam.gserviceaccount.com",
  "client_id": "116546449662259462870",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/gseetp-plugin%40barbar-264615.iam.gserviceaccount.com"
}

#################
# v 2.0 Backlog #
#################
1. Сделать подсказку со скрином напротив настройки title.
2. Вынести название методов подключения в константы.
3. Сделать на последней странице после импорта просьбу про отзывы.

################
# Current Task #
################
// запрос другого рабочего плагина 

https://accounts.google.com/o/oauth2/auth/oauthchooseaccount?

access_type=offline

approval_prompt=force

client_id=1075324102277-mdac3ljkp964kie3usoc8qj28laen2tb.apps.googleusercontent.com

redirect_uri=https%3A%2F%2Foauth.gsheetconnector.com

response_type=code

scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fdrive.metadata.readonly

flowName=GeneralOAuthFlow

service=lso
o2v=1






## Description

This is a wordpress plugin that extends standard woocommerce import products functionality and lets you import, not only from a local file, but also from your google sheet file which you store on your google drive and can be edited by any member of your store team.

Standard woocommerce import, that was introduced by woocommerce team since version 3.1, became a greater plugin feature that lets you not use additional plugins and extensions for product import processes. However, if it’s a pain every time when you’re loading csv import files from your local machine, then this file is a great choice that lets you not to do it anymore. Just set your google sheet name that you store on your google drive once and in the future you will only have to press the button "Import" as usual. Plugin itself will pull the new data from the specified google sheet table.

## Installation
It's a recommended to use the [official plugin page on WordPress.org](https://wordpress.org/plugins/import-products-from-gsheet-for-woo-importer/).

Also as option you can directly install plugin from github repository.
1. Clone repository to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.


## Set plugin options

After plugin installation we need to set connection with google API. In a plugin settings you have two options to set google API connection.
1. "One Click Auto Connect" method with google auth code ( more simple and straightforward connection method ).
2. "Manual Connect" method with assertion client_secret json code.


You need to set connection once and in the feature you can process import woocommerce product with your google sheet file every time you need.

### 1 method. "One Click Auto Connect" with google auth code (!!! Please note since google change his connection api, first method  temporary not working, please use second method with "Manual Connect" !!!)

This method is recomended and default in plugin settings area. Follow the steps to set it.

1. Go to plugin setting and press "Get Code" button in "One Click Auto Connect" tab ![](assets/screenshot-12.png)

2. You will be redirected with new tab in plugin application page on a google service. Please choose google account where you store your google sheet import file on google drive ![](assets/screenshot-13.png)

3. In the next page you need provide access "See and download all your Google Drive files." with plugin application. Please check corresponding checkbox and press continue button ![](assets/screenshot-14.png)

4. In the last page you received google auth code. Please copy this code.![](assets/screenshot-15.png)

5. After you received code, please return to plugin setting page, paste it to corresponding input and press "Save Options" button.![](assets/screenshot-16.png)

6. If code valid you will see corresponding message and new select for google sheet title and then you must to choose google sheet title that become your import file ![](assets/screenshot-17.png) Sheet title you can find in upper left corner of your sheet on google drive ![](assets/screenshot-18.png) 

7. That all. If you set all settings properly you will receive success connection message with link to standard woocommerce import page where you can process import products with your google sheet file ![](assets/screenshot-19.png)


### 2 method. "Manual Connect" with assertion client_secret json code.

1. If you do not have a google API client_secret json code than go to [google cloud console page.](https://console.developers.google.com) where you can create new one (if you have than you can go to step 13)

2. You should have "Google Drive API" enabled in your 'APIs & Services' google cloud console page list for your current project there ![](assets/screenshot-1.png) with service account setup for it, If your don't then go to next step, if you already have than you can move to step 11.

5. Go to [API Library page ](https://console.cloud.google.com/apis/library) and in the search input type "Google Drive API" ![](assets/screenshot-2.png) 

6. Click on a first search result and enable "Google Drive API" ![](assets/screenshot-3.png)

7. Then you can create new credentials.  Just click button "Create Сredentials" ![](assets/screenshot-5.png)

9. In the next page, the system  will ask you to fill the form with data for your credentials, please fill it in the same way as you can see on the screenshot below ![](assets/screenshot-6.png)

10. In the next step you will be redirected to 'Create service account' page you will see the form for your it, feel free to choose any service account name in the appropriate field and select role project -> editor, than skip 'Grant users access to this service account" and press "Done" button. ![](assets/screenshot-7.png) After it you should have a newly created service account.

11. Then you can go to your [credentials page](https://console.cloud.google.com/apis/credentials) find your newly created 'Service Account' click on it ![](assets/screenshot-8.png)

12. Then find the key tab and create a new key client_secret for your service account with json format. ![](assets/screenshot-9.png)

13. Please find client_email in client_secret json and copy it to your buffer for the next step.

14. Open your google sheet import file on your google drive and share access to it with client_email that you copy to buffer in previous step ![](assets/screenshot-10.png)

12. Please copy the client_secret json key (all file content file) to appropriate input in the option plugin page and save it. ![](assets/screenshot-11.png)

15. After it, you should see select with google sheets list for your import, pick one of them and press 'Save Options'. ![](assets/screenshot-12.png)

That’s all, if you set valid data you will see a success message, and when you next time try to import woocommerce product you will see an additional button that gives you the opportunity to import product from google sheet. ![](assets/screenshot-4.png)
