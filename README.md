# shopware-six-exporter
Wordpress Plugin intended to simplify data migration from WooCommerce to Shopware 6.x 

## Installation
If your Wordpress & WooCommerce site is Composer driven you can simply run `composer require vardumper/shopware-six-exporter` or `wpackagist-plugin/shopware-six-exporter` and then activate the plugin.
Alternatively, download the zip, upload and extract it to your `wp-content/plugins` folder and activate the plugin.

## Requirements
* In order to being able export .csv files which have correct mappings (countries, languages, currencies, payment methods, sales channels, etc.) you need to tell the plugin their Uuids so obviously you need to configure Shopware to your needs first.   
* In order to keep and migrate your customers login credentials as well, you need to install my [Wordpress Password Encoder](https://github.com/vardumper/wordpress-password-encoder-for-shopware-six) on the Shopware side, as well. Otherwise, your customers will have to reset their passwords, which might work for you as well. 

## Why not use the Shopware Migration Wizard?
When your WooCommerce online store is above a certain size, the Wordpress WPML export will simply not work due to the amount of data an online store generates. If you have tried the migration wizard, and it also worked for you, well then lucky you! 

## Who is this for?
Anyone migrating a WooCommerce online store over to Shopware 6 in any (Community, Professional or Enterprise) flavour. If you have multiple WooCommerce installations with separate databases and want to import all of them into your new Shopware website

## What the plugin does
This plugin generates importable .csv files of your WooCommerce online stores' most important entities (such as customers, products and orders) for later import into Shopware 6. It allows you to sanitize and map countries, map billing or shipping countries to sales channels, map Woocommerce payment methods to Shopware Payment Methods and so on. I added as many hooks/filters as possible which allows you to customize the resulting csv even further. You can and should extend mappings, or even  Complete Shopware Import/Export Profiles are included.
