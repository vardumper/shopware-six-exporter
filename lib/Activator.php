<?php declare(strict_types = 1);

/**
 * Fired during plugin activation
 *
 * @link       https://erikpoehler.com/shopware-six-exporter/
 * @since      1.0.0
 *
 * @package    Shopware_Six_Exporter
* @subpackage Shopware_Six_Exporter/includes
 */

namespace vardumper\Shopware_Six_Exporter;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Shopware_Six_Exporter
* @subpackage Shopware_Six_Exporter/includes
 * @author     Erik Pöhler <info@erikpoehler.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    // add empty option
        add_option(Plugin::SETTINGS_KEY, json_encode([], JSON_PRETTY_PRINT));
	}
}
