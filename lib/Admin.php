<?php declare(strict_types = 1);

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://erikpoehler.com/shopware-six-exporter/
 * @since      1.0.0
 *
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/
 */

namespace vardumper\Shopware_Six_Exporter;

use vardumper\Shopware_Six_Exporter\Admin\ExportCustomers;
use vardumper\Shopware_Six_Exporter\Admin\ExportGuests;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/
 * @author     Erik Pöhler <info@erikpoehler.com>
 */
class Admin {

    /**
     * The plugin's instance.
     *
     * @since  1.0.0
     * @access private
     * @var    Plugin $plugin This plugin's instance.
     */
    private $plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * 
     * @param Plugin $plugin This plugin's instance.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
        
        if (isset($_GET['download_csv']))
        {
            switch ($_POST['action']) {
                case 'Export Customers':
                    $exporter = new ExportCustomers();
                    break;
                case 'Export Guests':
                    $exporter = new ExportGuests();
                    break;
                case 'Export Products':
                    $exporter = new ExportProducts();
                    break;
                case 'Export Orders':
                    $exporter = new ExportOrders();
                    break;
                default:
                    break;
            }
            
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"shopware-six-". str_replace(' ', '-', strtolower($_POST['action'])) ."-".time().".csv\";" );
            header("Content-Transfer-Encoding: binary");
            
            echo $exporter->export()->getCsv();
            exit;
        }
    }

    public function filter_customer_active($value, $user_id, $row, $default = 1) : int 
    {
        die('xxx');
        return 0;
    }
    
    public function filter_customer_id($value, $user_id, $row, $default = 1) : int
    {
        die('xxx');
        return 0;
    }
    
    public function admin_menu()
    {
        /**
        add_menu_page( 
            'Custom Menu Page Title', 
            'Custom Menu Page', 
            'manage_options', 
            'custom.php', 
            '', 
            
            90
        );
        **/
        add_menu_page(
            __('Shopware Export', 'shopware-six-exporter'),
            __('Shopware Export', 'shopware-six-exporter'),
            'manage_options',
            'shopware-six-exporter',
            [ $this, 'showPage' ],
            plugin_dir_url(__DIR__) . 'assets/shopware-icon.svg', 
            90
            );
        
    }
    
    public function showPage() : void
    {
        // save settings
        if ( isset($_POST) && count($_POST) > 0 && !empty($_POST['action']) ) {
            $data = array_map('strtolower', $_POST);
            // checkboxes
            if (!isset($data['fakeEmails'])) {
                $data['fakeEmails'] = 'no';
            }
            if (!isset($data['customerPreventDups'])) {
                $data['customerPreventDups'] = 'no';
            }
            // each time settings are saved, lets add random ids (if chosen and if necessary)
            if (isset($data['customerPreventDups']) && $data['customerPreventDups'] === 'yes') {
                $this->shopware_exporter_add_customer_random_id_batch();
            }
            unset($data['action']); // remove junk
            update_option(Plugin::SETTINGS_KEY, json_encode($data, JSON_PRETTY_PRINT  )); // save
        }
        include plugin_dir_path(__FILE__) . '/../templates/page.php';
    }
    
    public function shopware_exporter_add_customer_random_id_batch() : void
    {
        global $wpdb;

        $r = $wpdb->get_row("SELECT COUNT(ID) AS count FROM wp_users u LEFT JOIN wp_usermeta um ON (um.user_id = u.ID AND um.meta_key = 'shopware_exporter_random_id') WHERE um.meta_value IS NULL;", ARRAY_A);
        $count = (int) $r['count'];
        // only if any random ids are missing
        if ($count > 0) {
            $missing = $wpdb->get_results("SELECT ID AS user_id FROM wp_users u LEFT JOIN wp_usermeta um ON (um.user_id = u.ID AND um.meta_key = 'shopware_exporter_random_id') WHERE um.meta_value IS NULL;", ARRAY_A);
            foreach($missing as $user_id) {
                $user_id  = $user_id['user_id'];
                add_user_meta($user_id, 'shopware_exporter_random_id', self::getRandomId());
            }
        }
    }
    
    /**
     * prevent duplicates by adding a random unique id
     */
    public function shopware_exporter_add_customer_random_id($user_id) {
        $meta = get_metadata( 'user', $user_id, 'shopware_exporter_random_id' );
        $random_id = self::getRandomId();
        if (is_array($meta)) {
            // there are too many – this should never happen
            delete_user_meta($user_id, 'shopware_exporter_random_id');
            update_user_meta($user_id, 'shopware_exporter_random_id', $random_id);
        }
        if (false === $meta) {
            // only update/add if not existent
            update_user_meta($user_id, 'shopware_exporter_random_id', $random_id);
        }
        // otherwise the user already has a random id – do nothing
    }
    
    public static function getNumbers($min=1,$max=10,$count=1,$margin=0) 
    {
        $range = range(0,$max-$min);
        $return = [];
        for( $i=0; $i<$count; $i++) {
            if( !$range) {
                trigger_error("Not enough numbers to pick from!", E_USER_WARNING);
                return $return;
            }
            $next = rand(0,count($range)-1);
            $return[] = $range[$next]+$min;
            array_splice($range,max(0,$next-$margin),$margin*2+1);
        }
        return $return;
    }
    
    public static function getRandomId() : int
    {
        $number = '1' . substr(implode('',self::getNumbers(1000,9999,10,2)),rand(0,10), 11);
        return intval($number);
    }
    
    public function settings_saved() : string
    {
        echo '<div class="notice notice-success"><p>This is an awesome way to add notices!</p></div>';
    }
    
    /**
     * Allow for custom query variables
     */
    public function query_vars($query_vars)
    {
        $query_vars[] = 'download_csv';
        return $query_vars;
    }
    
    /**
     * Parse the request
     */
    public function parse_request(&$wp)
    {
        if(array_key_exists('download_csv', $wp->query_vars))
        {
            $this->download_report();
            exit;
        }
    }
    
    /**
     * Register the stylesheets for the Dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in PluginName_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The PluginName_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        \wp_enqueue_style(
            $this->plugin->get_plugin_name(),
            \plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/shopware-six-exporter-admin.css',
            array(),
            $this->plugin->get_version(),
            'all' );

    }

    /**
     * Register the JavaScript for the dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in PluginName_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The PluginName_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        \wp_enqueue_script(
            $this->plugin->get_plugin_name(),
            \plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/shopware-six-exporter-admin.js',
            array( 'jquery' ),
            $this->plugin->get_version(),
            false );

    }

}
