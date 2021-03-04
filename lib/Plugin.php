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
