=== Shopware 6 Exporter for WooCommerce ===
Contributors: lilmofo
Tags: comments, spam
Requires at least: 5.6.2
Tested up to: 5.6.2
Stable tag: 0.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress Plugin intended to simplify data migration from WooCommerce to Shopware 6.x

== Installation ==
If your Wordpress & WooCommerce site is Composer driven you can simply run

composer require vardumper/shopware-six-exporter 
or
composer require wpackagist-plugin/shopware-six-exporter

Then activate the plugin. Alternatively, download the zip, upload and extract it to your `wp-content/plugins` folder and activate the plugin.

== Description ==

Requirements:
* In order to being able export .csv files which have correct mappings (countries, languages, currencies, payment methods, sales channels, etc.) you need to tell the plugin their Uuids so obviously you need to configure Shopware beforehand.   
* In order to keep and migrate your customers login credentials as well, you need to install my [Wordpress Password Encoder](https://github.com/vardumper/wordpress-password-encoder-for-shopware-six) on the Shopware side, as well. Otherwise, your customers will have to reset their passwords, which might work for you as well. 

Plugin Features:
* This plugin generates importable .csv files of your WooCommerce online stores' most important entities (such as customers, products and orders) for later import into Shopware 6. 
* Fake customer emails to prevent accidential
* Prevents auto increment ID collisions when importing from multiple WooCommerce stores. 
* It allows you to map countries, sales channels, payment methods to the corresponding Shopware Uuids.
* Filters allow you to modify each csv column to your needs. You can and should use these to extend mappings, or retrieve more entity data. 
* Complete Shopware Import/Export Profiles are included.

Why not use the Shopware Migration Wizard?
The Shopware Migration Assistant or Wizard requires a Wordpress XML export – which is simply impossible to do on any Multi-Gigabyte database.
If you have tried the migration wizard, and it worked for you, well then congrats. Lucky you! But I guess you wouldn't be here, if that was the case.

So who is this for?
Anyone migrating a WooCommerce online store over to Shopware 6 in Community, Professional or Enterprise flavour. 
If you import multiple WooCommerce installations into a single Shopware installation it also makes sense to import matching .csv files with identical format and compatible with the same Shopware ImportExport profile.