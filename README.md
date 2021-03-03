# shopware-six-exporter
Wordpress Plugin intended to simplify data migration from WooCommerce to Shopware 6.x 

## Who this is for
Anyone migrating a WooCommerce online store over to Shopware Community, Professional or Enterprise Edition.

## Requirements
In order to migrate customer passwords you need to install [a Wordpress Password Encoder](https://github.com/vardumper/wordpress-password-encoder-for-shopware-six) on the Shopware side.  

## What it does
This plugin generates .csv files of your WooCommerce online stores' most important entities (such as customers, products and orders) for later import into Shopware 6. It sanitizes and maps your WooCommerce data to Shopware countries, sales channels. It comes with many hooks/filters which allows for some more advanced mappings and .
