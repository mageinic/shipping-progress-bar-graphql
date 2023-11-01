# Shipping Progress Bar GraphQl

**Shipping Progress Bar GraphQl is a part of MageINIC Shipping Progress Bar extension that adds GraphQL features.** This extension extends Shipping Progress Bar definitions.

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageinic/shipping-progress-bar-graphql

php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento maintenance:disable
php bin/magento cache:flush
```

**Note:**
Magento 2 Shipping Progress Bar GraphQL requires installing [MageINIC Shipping Progress Bar](https://github.com/mageinic/Shipping-Progress-Bar) in your Magento installation.

**Or Install via composer [Recommend]**
```
composer require mageinic/shippingprogressbar
```

## 2. How to use

- To view the queries that the **MageINIC Shipping Progress Bar GraphQL** extension supports, you can check `Shipping Progress Bar GraphQl User Guide.pdf` Or run `Shipping Progress Bar Graphql.postman_collection.json` in Postman.

## 3. Get Support

- Feel free to [contact us](https://www.mageinic.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star**
