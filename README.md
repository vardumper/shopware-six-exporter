# Shopware 6 Exporter for WooCommerce
Wordpress Plugin intended to simplify data migration from WooCommerce to Shopware 6.x 

## Installation
Download the [zip](https://github.com/vardumper/shopware-six-exporter/archive/main.zip) upload it to your plugins folder and activate the plugin.
If your Wordpress & WooCommerce site is powered by Composer you can install the plugin with

```
composer require vardumper/shopware-six-exporter
```
or
```
composer require wpackagist-plugin/shopware-six-exporter
```

## Requirements
* In order to being able export .csv files which have correct mappings (countries, languages, currencies, payment methods, sales channels, etc.) you need to tell the plugin their Uuids so obviously you need to configure Shopware beforehand.   
* In order to keep and migrate your customers login credentials as well, you need to install my [Wordpress Password Encoder](https://github.com/vardumper/wordpress-password-encoder-for-shopware-six) on the Shopware side, as well. Otherwise, your customers will have to reset their passwords, which might work for you as well. 

## Plugin Features
* This plugin generates importable .csv files of your WooCommerce online stores' most important entities (such as customers, products and orders) for later import into Shopware 6. 
* Fake customer emails to prevent accidential
* Prevents auto increment ID collisions when importing from multiple WooCommerce stores. 
* It allows you to map countries, sales channels, payment methods to the corresponding Shopware Uuids.
* Filters allow you to modify each csv column to your needs. You can and should use these to extend mappings, or retrieve more entity data. 
* Complete Shopware Import/Export Profiles are included.

## Why not use the Shopware Migration Wizard?
The Shopware Migration Assistant or Wizard requires a Wordpress XML export – which is simply impossible to do on any Multi-Gigabyte database.
If you have tried the migration wizard, and it worked for you, well then congrats. Lucky you! But I guess you wouldn't be here, if that was the case.

## So who is this for?
Anyone migrating a WooCommerce online store over to Shopware 6 in Community, Professional or Enterprise flavour. 
If you import multiple WooCommerce installations into a single Shopware installation it also makes sense to import matching .csv files with identical format and compatible with the same Shopware ImportExport profile.
