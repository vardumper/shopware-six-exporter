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

    /** @var array $settings */
    private $settings;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * 
     * @param Plugin $plugin This plugin's instance.
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
        $this->settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        
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

    public function filter_customer_active(?int $value, int $user_id, array $row, $default = null) : ?int 
    {
        if (is_null($value)) {
            return $default;
        }
        return (int) $value;
    }
    
    public function filter_customer_id($value, int $user_id, array $row, $default = null) : ?int
    {
        if (!is_null($value)) {
            return (int) $value;
        }
        return null;
    }
    
    public function filter_customer_affiliateCode($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_autoIncrement($value, int $user_id, array $row, $default = null) : ?string
    {
        $customerPreventDups = isset($this->settings['customerPreventDups']) && $this->settings['customerPreventDups'] === 'yes';
        if (!$customerPreventDups) {
            return null;
        }
        // this should never happen, but hey: better safe than sorry
        if (false === get_user_meta($user_id, 'shopware_exporter_random_id', true)) {
            add_user_meta($user_id, 'shopware_exporter_random_id', self::getRandomId());
        }
        return get_user_meta($user_id, 'shopware_exporter_random_id', true);
    }
    
    public function filter_customer_birthday($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_boundSalesChannelId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $this->settings['customerDefaultSalesChannelId'];
    }
    
    public function filter_customer_campaignCode($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_company($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value) && !empty($row['defaultBillingAddress.company'])) {
            return $row['defaultBillingAddress.company'];
        }
        return $value;
    }
    
    public function filter_customer_createdAt($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_customFields($value, int $user_id, array $row, $default = null) : ?string
    {
        $meta_data['description'] = get_user_meta($user_id,'description', true);
        return json_encode($meta_data);
    }
    
    public function filter_customer_customerNumber($value, int $user_id, array $row, $default = null) : ?string
    {
        // edge case: customer nr is empty
        if (empty($value)) {
            // on some sites we set the user login to be the customer nr (with one leading letter, so if the rest is a digit use this one)
            return (string) $user_id;
        }
        return $value;
    }
    public function filter_customer_defaultBillingAddress_city($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_company($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_countryId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_countryStateId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_customFields($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_customerId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_department($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_firstName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value)) {
            $value = $row['firstName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    public function filter_customer_defaultBillingAddress_additionalAddressLine1($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultBillingAddress_additionalAddressLine2($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_id($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_phoneNumber($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultBillingAddress_lastName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value)) {
            $value = $row['lastName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    
    public function filter_customer_defaultBillingAddress_salutationId($value, int $user_id, array $row, $default = null) : ?string
    {
        if ( empty($value) && (!empty($row['salutationId']) || !empty($row['defaultShippingAddress.salutationId'])) ) {
            $value = $row['salutationId'] ?? $row['defaultShippingAddress.salutationId'];
        }
        $salutations = [
            '1' => $this->settings['customerSalutationIdMale'], // male
            '2' => $this->settings['customerSalutationIdFemale'], // female
        ];
        if (array_key_exists((string) $value, $salutations)) {
            return $salutations[(string) $value];
        }
        return $this->settings['customerSalutationIdUnknown']; // unknown
    }
    
    public function filter_customer_defaultBillingAddress_title($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    public function filter_customer_defaultBillingAddress_createdAt($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_defaultBillingAddress_updatedAt($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_defaultBillingAddress_vatId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultBillingAddress_zipcode($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultBillingAddressId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultPaymentMethodId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_additionalAddressLine1($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_additionalAddressLine2($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_city($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_company($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_countryId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_countryStateId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_createdAt($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_defaultShippingAddress_customFields($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_customerId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_department($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_firstName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value)) {
            $value = $row['firstName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    
    public function filter_customer_defaultShippingAddress_id($value, int $user_id, array $row, $default = null) : ?int
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_lastName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value)) {
            $value = $row['lastName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    
    public function filter_customer_defaultShippingAddress_phoneNumber($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_salutationId($value, int $user_id, array $row, $default = null) : ?string
    {
        if ( empty($value) && (!empty($row['salutationId']) || !empty($row['defaultBillingAddress.salutationId'])) ) {
            $value = $row['salutationId'] ?? $row['defaultBillingAddress.salutationId'];
        }
        $salutations = [
            '1' => $this->settings['customerSalutationIdMale'], // male
            '2' => $this->settings['customerSalutationIdFemale'], // female
        ];
        if (array_key_exists((string) $value, $salutations)) {
            return $salutations[(string) $value];
        }
        return $this->settings['customerSalutationIdUnknown']; // unknown
    }
    
    public function filter_customer_defaultShippingAddress_street($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_title($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_updatedAt($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_vatId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddress_zipcode($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_defaultShippingAddressId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_doubleOptInConfirmDate($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_doubleOptInEmailSentDate($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_doubleOptInRegistration($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_email($value, int $user_id, array $row, $default = null) : ?string
    {
        $fakeEmails = isset($this->settings['fakeEmails']) && $this->settings['fakeEmails'] === 'yes';
        if ($fakeEmails) {
            $value = md5($value) . '@' . parse_url(get_bloginfo('url'), PHP_URL_HOST);
        }
        
        return $value;
    }
    
    public function filter_customer_firstLogin($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_firstName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value) && !empty($row['defaultBillingAddress.firstName'])) {
            $value = $row['defaultBillingAddress.firstName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    
    public function filter_customer_groupId($value, int $user_id, array $row, $default = null) : ?string
    {
        if (is_null($value)) {
            return $default;
        }
        return $value;
    }
    
    public function filter_customer_guest($value, int $user_id, array $row, $default = null) : ?int
    {
        if (is_null($value)) {
            return $default;
        }
        return $value;
    }
    
    public function filter_customer_hash($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_languageId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_lastLogin($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_lastName($value, int $user_id, array $row, $default = null) : ?string
    {
        if (empty($value) && !empty($row['defaultBillingAddress.lastName'])) {
            $value = $row['defaultBillingAddress.lastName'];
        }
        return implode('-', array_map('ucfirst', explode('-', strtolower((string) $value))));
    }
    
    public function filter_customer_lastPaymentMethodId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_legacyEncoder($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_legacyPassword($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_newsletter($value, int $user_id, array $row, $default = null) : ?int
    {
        return $value;
    }
    
    public function filter_customer_password($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_promotions($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_recoveryCustomer($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_remoteAddress($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_requestedGroupId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_salesChannelId($value, int $user_id, array $row, $default = null) : ?string
    {
        return $this->settings['customerDefaultSalesChannelId'];
    }
    
    public function filter_customer_salutationId($value, int $user_id, array $row, $default = null) : string
    {   
        if ( empty($value) && (!empty($row['defaultBillingAddress.salutationId']) || !empty($row['defaultShippingAddress.salutationId'])) ) {
            $value = $row['defaultBillingAddress.salutationId'] ?? $row['defaultShippingAddress.salutationId'];
        }
        $salutations = [
            '1' => $this->settings['customerSalutationIdMale'], // male
            '2' => $this->settings['customerSalutationIdFemale'], // female
        ];
        if (array_key_exists((string) $value, $salutations)) {
            return $salutations[(string) $value];
        }
        return $this->settings['customerSalutationIdUnknown']; // unknown
    }
    
    public function filter_customer_tagIds($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_title($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
    }
    
    public function filter_customer_updatedAt($value, int $user_id, array $row, $default = null) : ?string
    {
        if (!is_null($value)) {
            $tz = (!empty(get_option('timezone_string'))) ? new \DateTimeZone(get_option('timezone_string')) : null;
            $dt = new \DateTimeImmutable($value, $tz);
            return $dt->format('c');
        }
        return null;
    }
    
    public function filter_customer_vatIds($value, int $user_id, array $row, $default = null) : ?string
    {
        return $value;
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
