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
    protected $version = '0.0.5';

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
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_city', $plugin_admin, 'filter_customer_defaultBillingAddress_city', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_company', $plugin_admin, 'filter_customer_defaultBillingAddress_company', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_countryId', $plugin_admin, 'filter_customer_defaultBillingAddress_countryId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_countryStateId', $plugin_admin, 'filter_customer_defaultBillingAddress_countryStateId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_createdAt', $plugin_admin, 'filter_customer_defaultBillingAddress_createdAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_customFields', $plugin_admin, 'filter_customer_defaultBillingAddress_customFields', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_customerId', $plugin_admin, 'filter_customer_defaultBillingAddress_customerId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_department', $plugin_admin, 'filter_customer_defaultBillingAddress_department', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_firstName', $plugin_admin, 'filter_customer_defaultBillingAddress_firstName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_id', $plugin_admin, 'filter_customer_defaultBillingAddress_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_lastName', $plugin_admin, 'filter_customer_defaultBillingAddress_lastName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_phoneNumber', $plugin_admin, 'filter_customer_defaultBillingAddress_phoneNumber', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_salutationId', $plugin_admin, 'filter_customer_defaultBillingAddress_salutationId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_street', $plugin_admin, 'filter_customer_defaultBillingAddress_title', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_title', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_updatedAt', $plugin_admin, 'filter_customer_defaultBillingAddress_updatedAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_vatId', $plugin_admin, 'filter_customer_defaultBillingAddress_vatId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddress_zipcode', $plugin_admin, 'filter_customer_defaultBillingAddress_zipcode', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultBillingAddressId', $plugin_admin, 'filter_customer_defaultBillingAddressId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultPaymentMethodId', $plugin_admin, 'filter_customer_defaultPaymentMethodId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_additionalAddressLine1', $plugin_admin, 'filter_customer_defaultShippingAddress_additionalAddressLine1', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_additionalAddressLine2', $plugin_admin, 'filter_customer_defaultShippingAddress_additionalAddressLine2', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_city', $plugin_admin, 'filter_customer_defaultShippingAddress_city', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_company', $plugin_admin, 'filter_customer_defaultShippingAddress_company', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_countryId', $plugin_admin, 'filter_customer_defaultShippingAddress_countryId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_countryStateId', $plugin_admin, 'filter_customer_defaultShippingAddress_countryStateId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_createdAt', $plugin_admin, 'filter_customer_defaultShippingAddress_createdAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_customFields', $plugin_admin, 'filter_customer_defaultShippingAddress_customFields', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_customerId', $plugin_admin, 'filter_customer_defaultShippingAddress_customerId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_department', $plugin_admin, 'filter_customer_defaultShippingAddress_department', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_firstName', $plugin_admin, 'filter_customer_defaultShippingAddress_firstName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_id', $plugin_admin, 'filter_customer_defaultShippingAddress_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_lastName', $plugin_admin, 'filter_customer_defaultShippingAddress_lastName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_phoneNumber', $plugin_admin, 'filter_customer_defaultShippingAddress_phoneNumber', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_salutationId', $plugin_admin, 'filter_customer_defaultShippingAddress_salutationId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_street', $plugin_admin, 'filter_customer_defaultShippingAddress_street', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_title', $plugin_admin, 'filter_customer_defaultShippingAddress_title', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_updatedAt', $plugin_admin, 'filter_customer_defaultShippingAddress_updatedAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_vatId', $plugin_admin, 'filter_customer_defaultShippingAddress_vatId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddress_zipcode', $plugin_admin, 'filter_customer_defaultShippingAddress_zipcode', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_defaultShippingAddressId', $plugin_admin, 'filter_customer_defaultShippingAddressId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInConfirmDate', $plugin_admin, 'filter_customer_doubleOptInConfirmDate', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInEmailSentDate', $plugin_admin, 'filter_customer_doubleOptInEmailSentDate', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_doubleOptInRegistration', $plugin_admin, 'filter_customer_doubleOptInRegistration', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_email', $plugin_admin, 'filter_customer_email', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_firstLogin', $plugin_admin, 'filter_customer_firstLogin', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_firstName', $plugin_admin, 'filter_customer_firstName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_groupId', $plugin_admin, 'filter_customer_groupId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_guest', $plugin_admin, 'filter_customer_guest', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_hash', $plugin_admin, 'filter_customer_hash', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_id', $plugin_admin, 'filter_customer_id', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_languageId', $plugin_admin, 'filter_customer_languageId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastLogin', $plugin_admin, 'filter_customer_lastLogin', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastName', $plugin_admin, 'filter_customer_lastName', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_lastPaymentMethodId', $plugin_admin, 'filter_customer_lastPaymentMethodId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_legacyEncoder', $plugin_admin, 'filter_customer_legacyEncoder', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_legacyPassword', $plugin_admin, 'filter_customer_legacyPassword', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_newsletter', $plugin_admin, 'filter_customer_newsletter', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_password', $plugin_admin, 'filter_customer_password', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_promotions', $plugin_admin, 'filter_customer_promotions', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_recoveryCustomer', $plugin_admin, 'filter_customer_recoveryCustomer', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_remoteAddress', $plugin_admin, 'filter_customer_remoteAddress', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_requestedGroupId', $plugin_admin, 'filter_customer_requestedGroupId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_salesChannelId', $plugin_admin, 'filter_customer_salesChannelId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_salutationId', $plugin_admin, 'filter_customer_salutationId', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_tagIds', $plugin_admin, 'filter_customer_tagIds', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_title', $plugin_admin, 'filter_customer_title', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_updatedAt', $plugin_admin, 'filter_customer_updatedAt', 10, 4);
        $this->loader->add_filter('shopware_six_exporter_filter_customer_vatIds', $plugin_admin, 'filter_customer_vatIds', 10, 4);
        
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
