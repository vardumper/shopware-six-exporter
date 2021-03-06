<?php declare(strict_types = 1);

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://erikpoehler.com/
 * @since      1.0.0
 *
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/includes
 */

namespace vardumper\Shopware_Six_Exporter;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/includes
 * @author     Erik Pöhler <info@erikpoehler.com>
 */
class Plugin {

    public const SETTINGS_KEY = 'shopware_six_exporter';
    
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $pluginname    The string used to uniquely identify this plugin.
     */
    protected $pluginname = 'shopware-six-exporter';

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version = '1.0.0';

    /**
     * Define the core functionality of the plugin.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new I18n();
        $plugin_i18n->set_domain( $this->get_plugin_name() );
        $plugin_i18n->load_plugin_textdomain();

    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Admin( $this );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu'); // menu
        
        // prevent duplicates
        $this->loader->add_action('user_register', $plugin_admin, 'shopware_exporter_add_customer_random_id', 99, 1 ); // on all inserts (products, action-schedules, posts, etc.)
        $this->loader->add_action('woocommerce_created_customer', $plugin_admin, 'shopware_exporter_add_customer_random_id', 99, 1); // upon customer creation (register form)
        
        // add endpoints – in order to make csv download work
        $this->loader->add_filter('query_vars', $plugin_admin, 'query_vars', 10, 1);
        $this->loader->add_action('parse_request', $plugin_admin, 'parse_request');
        
        // customer filters
        $this->loader->add_filter('shopware_six_exporter_filter_customer_id', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_active', $plugin_admin, 'filter_customer_active', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_affiliateCode', $plugin_admin, 'filter_customer_affiliateCode', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_autoIncrement', $plugin_admin, 'filter_customer_autoIncrement', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_birthday', $plugin_admin, 'filter_customer_birthday', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_boundSalesChannelId', $plugin_admin, 'filter_customer_boundSalesChannelId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_campaignCode', $plugin_admin, 'filter_customer_campaignCode', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_company', $plugin_admin, 'filter_customer_company', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_createdAt', $plugin_admin, 'filter_customer_createdAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_customFields', $plugin_admin, 'filter_customer_customFields', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_customerNumber', $plugin_admin, 'filter_customer_customerNumber', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_additionalAddressLine1', $plugin_admin, 'filter_customer_defaultBillingAddress_additionalAddressLine1', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_additionalAddressLine2', $plugin_admin, 'filter_customer_defaultBillingAddress_additionalAddressLine2', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.city', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.company', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.countryId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.countryStateId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.createdAt', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.customFields', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.customerId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.department', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.firstName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.id', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.lastName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.phoneNumber', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.salutationId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.street', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.title', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.updatedAt', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.vatId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress.zipcode', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddressId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultPaymentMethodId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.additionalAddressLine1', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.additionalAddressLine2', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.city', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.company', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.countryId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.countryStateId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.createdAt', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.customFields', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.customerId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.department', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.firstName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.id', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.lastName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.phoneNumber', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.salutationId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.street', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.title', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.updatedAt', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.vatId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress.zipcode', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddressId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInConfirmDate', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInEmailSentDate', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInRegistration', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_email', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_firstLogin', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_firstName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_groupId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_guest', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_hash', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_id', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_languageId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastLogin', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastName', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastPaymentMethodId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_legacyEncoder', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_legacyPassword', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_newsletter', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_password', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_promotions', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_recoveryCustomer', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_remoteAddress', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_requestedGroupId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_salesChannelId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_salutationId', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_tagIds', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_title', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_updatedAt', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_vatIds', $plugin_admin, 'filter_customer_id', 10, 4);
        
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_frontend_hooks() {

        $plugin_frontend = new Frontend( $this );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_frontend, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_frontend, 'enqueue_scripts' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->set_locale();
        $this->define_admin_hooks();
//         $this->define_frontend_hooks();
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->pluginname;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
