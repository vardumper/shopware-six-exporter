<?php declare(strict_types = 1);

use vardumper\Shopware_Six_Exporter\Plugin;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Shopware_Six_Exporter
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce & Wordpress Data Export for Shopware 6
 * Plugin URI:        http://erikpoehler.com/wordpress-woocommerce-data-export-fro-shopware-six
 * Description:       This plugin helps export data from Wordpress and WooCommerce into an importable CSV format for Shopware 6
 * Version:           1.0.0
 * Author:            Erik Pöhler
 * Author URI:        http://erikpoehler.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shopware-six-exporter
 * Domain Path:       /languages
 */

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in lib/Activator.php
 */
\register_activation_hook( __FILE__, '\vardumper\Shopware_Six_Exporter\Activator::activate' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in lib/Deactivator.php
 */
\register_deactivation_hook( __FILE__, '\vardumper\Shopware_Six_Exporter\Deactivator::deactivate' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
\add_action( 'plugins_loaded', function () {
    $plugin = new Plugin();
    $plugin->run();
} );
