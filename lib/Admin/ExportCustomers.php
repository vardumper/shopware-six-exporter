<?php declare(strict_types = 1);

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://erikpoehler.com/shopware-six-exporter/
 * @since      1.0.0
 *
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/Admin
 */

namespace vardumper\Shopware_Six_Exporter\Admin;

use League\Csv\Writer;
use vardumper\Shopware_Six_Exporter\Plugin;
use Ramsey\Uuid\Uuid;

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
class ExportCustomers {
    
    private $csv;
    
    public function __construct()
    {
        $this->csv = Writer::createFromString();
        $this->csv->setDelimiter(';');
    }
    
    public function export() {
        //insert the header
        $headers = $this->getHeaders();
        $this->csv->insertOne($headers);
        
        $records = self::getRecords();
        
        $this->csv->insertAll($records);
        
        return $this;
    }

    private function init() {
        $loader = $this->plugin->get_loader();
        foreach(self::getHeaders() as $key)
        {
            if (method_exists(ExportCustomers::class, 'filter_' . str_replace('.', '_', $key))) {
                $loader->add_filter("customer_$key", $this, 'filter_' . str_replace('.', '_', $key), 10, 4); // always 4 arguments: $value, $user_id, full db $row, $default value
            }
        }
    }
    
    public function getCsv() : string
    {
        return $this->csv->getContent();
    }
    
    public static function getRecords(bool $random = false) : array 
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
        global $wpdb;
        
        $settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        
        $defaults = self::getDefaults();
        $query = sprintf("SELECT u.ID     AS `ID`,
	   MAX( CASE WHEN um.meta_key = 'customer_nr' and u.ID = um.user_id THEN um.meta_value END ) as `customerNumber`,
       MAX( CASE WHEN um.meta_key = 'shopware_exporter_random_id' and u.ID = um.user_id THEN um.meta_value END ) as `autoIncrement`,
       MAX( CASE WHEN um.meta_key = 'first_name' and u.ID = um.user_id THEN um.meta_value END ) as `firstName`,
	   MAX( CASE WHEN um.meta_key = 'last_name' and u.ID = um.user_id THEN um.meta_value END ) as `lastName`,
	   MAX( CASE WHEN um.meta_key = 'last_update' and u.ID = um.user_id THEN FROM_UNIXTIME(um.meta_value) END ) as`updatedAt`,
	   MAX( CASE WHEN um.meta_key = 'wc_last_active' and u.ID = um.user_id THEN FROM_UNIXTIME(um.meta_value) END ) as `lastLogin`,
	   MAX( CASE WHEN um.meta_key = 'billing_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.additionalAddressLine1`,
	   MAX( CASE WHEN um.meta_key = 'billing_address_2' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.additionalAddressLine2`,
	   MAX( CASE WHEN um.meta_key = 'billing_city' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.city`,
	   MAX( CASE WHEN um.meta_key = 'billing_company' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.company`,
	   MAX( CASE WHEN um.meta_key = 'billing_country' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.country`,
	   MAX( CASE WHEN um.meta_key = 'billing_state' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.countryState`,
	   MAX( CASE WHEN um.meta_key = 'billing_first_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.firstName`,
	   MAX( CASE WHEN um.meta_key = 'billing_last_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.lastName`,
	   MAX( CASE WHEN um.meta_key = 'billing_phone' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.phoneNumber`,
	   MAX(CASE 
         WHEN bill_title.meta_value = '2' THEN '%s' 
         WHEN bill_title.meta_value = '1' THEN '%s' 
         ELSE '%s' 
       END) AS `defaultBillingAddress.salutationId`,
	   MAX( CASE WHEN um.meta_key = 'billing_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.street`,
	   MAX( CASE WHEN um.meta_key = 'billing_postcode' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.zipcode`,
       MAX( CASE WHEN um.meta_key = 'shipping_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.additionalAddressLine1`,
	   MAX( CASE WHEN um.meta_key = 'shipping_address_2' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.additionalAddressLine2`,
	   MAX( CASE WHEN um.meta_key = 'shipping_city' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.city`,
	   MAX( CASE WHEN um.meta_key = 'shipping_company' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.company`,
	   MAX( CASE WHEN um.meta_key = 'shipping_country' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.country`,
	   MAX( CASE WHEN um.meta_key = 'shipping_state' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.countryState`,
	   MAX( CASE WHEN um.meta_key = 'shipping_first_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.firstName`,
	   MAX( CASE WHEN um.meta_key = 'shipping_last_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.lastName`,
	   MAX( CASE WHEN um.meta_key = 'shipping_phone' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.phoneNumber`,
	   MAX(CASE 
         WHEN ship_title.meta_value = '2' THEN '%s' 
         WHEN ship_title.meta_value = '1' THEN '%s' 
         ELSE '%s' 
	   END) AS `defaultShippingAddress.salutationId`,
	   MAX( CASE WHEN um.meta_key = 'shipping_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.street`,
	   MAX( CASE WHEN um.meta_key = 'shipping_postcode' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.zipcode`,
	   1 AS `doubleOptInRegistration`,
       0 AS `guest`,
       u.user_registered AS `firstLogin`,
       u.user_registered AS `createdAt`,
       LOWER(u.user_email) AS `email`,
       u.user_pass AS `legacyPassword`,
       u.user_login AS `userLogin`
FROM   wp_users u 
       JOIN wp_usermeta um 
         ON u.ID = um.user_id 
       LEFT JOIN wp_usermeta as bill_title 
        ON (u.ID = bill_title.user_id AND bill_title.meta_key = 'billing_title')
       LEFT JOIN wp_usermeta as ship_title 
        ON (u.ID = ship_title.user_id AND ship_title.meta_key = 'shipping_title')
        %s
GROUP BY u.ID 
ORDER BY u.ID ASC
%s;", 
            $settings['customerSalutationIdFemale'], 
            $settings['customerSalutationIdMale'], 
            $settings['customerSalutationIdUnknown'], 
            $settings['customerSalutationIdFemale'], 
            $settings['customerSalutationIdMale'], 
            $settings['customerSalutationIdUnknown'],
            $random ? " JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM wp_users)) AS id) AS u2 WHERE u.ID >= u2.ID " : "",
            $random ? " LIMIT 3 " : ""
        );
        
        /**
         * SELECT user_email
  FROM wp_users AS r1 JOIN
       (SELECT CEIL(RAND() *
                     (SELECT MAX(id)
                        FROM wp_users)) AS id)
        AS r2
 WHERE r1.id >= r2.id
 ORDER BY r1.id ASC
 LIMIT 1
         * @var Ambiguous $results
         */
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        /**
         * Loop over results once and add additional info, do basic sanitization, etc
         */
        $r = [];
        foreach($results as $result) {
            $user_id = (int) $result['ID'];
            
            $fakeEmails = isset($settings['fakeEmails']) && $settings['fakeEmails'] === 'yes';
            if ($fakeEmails) {
                $result['email'] = md5($result['email']) . '@' . parse_url(get_bloginfo('url'), PHP_URL_HOST);
            }
            
            // if prevent dups setting is off, do not populate autoIncrement field
            $customerPreventDups = isset($settings['customerPreventDups']) && $settings['customerPreventDups'] === 'no';
            if (!$customerPreventDups) {
                unset($result['autoIncrement']); // overwrite the random id
            }

            $tmp = $result;
            // sanitize firstname/lastname
            $tmp['firstName']   = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['firstName']))));
            $tmp['lastName']    = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['lastName']))));
            // if billing address doesn't have a first/last name, copy it over from customer
            if (empty($tmp['defaultBillingAddress.firstName'])) {
                $tmp['defaultBillingAddress.firstName'] = $tmp['firstName'];
            }
            if (empty($tmp['defaultBillingAddress.lastName'])) {
                $tmp['defaultBillingAddress.lastName'] = $tmp['lastName'];
            }
            // if shipping address doesn't have a first/last name, copy it over from customer
            if (empty($tmp['defaultShippingAddress.firstName'])) {
                $tmp['defaultShippingAddress.firstName'] = $tmp['firstName'];
            }
            if (empty($tmp['defaultShippingAddress.lastName'])) {
                $tmp['defaultShippingAddress.lastName'] = $tmp['lastName'];
            }
            
            // edge case: billing name set, but missing in customer
            if (empty($tmp['firstName']) && !empty($tmp['defaultBillingAddress.firstName'])) {
                $tmp['firstName'] = $tmp['defaultBillingAddress.firstName'];
            }
            if (empty($tmp['lastName']) && !empty($tmp['defaultBillingAddress.lastName'])) {
                $tmp['lastName'] = $tmp['defaultBillingAddress.lastName'];
            }
            // edge case: customer nr is empty
            if (empty($tmp['customerNumber'])) {
                // on some sites we set the user login to be the customer nr (with one leading letter, so if the rest is a digit use this one)
                if (ctype_digit(substr($tmp['userLogin'],1))) {
                    $tmp['customerNumber'] = $tmp['userLogin'];
                } else {
                    // fallback, re-generate a customer nr
                    if (function_exists('create_customer_nr_by_id')) {
                        $tmp['customerNumber'] = \create_customer_nr_by_id($tmp['ID']);
                    }
                }
            }
            
            // edge case: missing or incomplete shipping address/copy over billing address
            if ( empty($tmp['defaultShippingAddress.city']) || empty($tmp['defaultShippingAddress.street']) ) {
                $tmp['defaultShippingAddress.firstName']                = $tmp['defaultBillingAddress.firstName'];
                $tmp['defaultShippingAddress.lastName']                 = $tmp['defaultBillingAddress.lastName'];
                $tmp['defaultShippingAddress.street']                   = $tmp['defaultBillingAddress.street'];
                $tmp['defaultShippingAddress.zipcode']                  = $tmp['defaultBillingAddress.zipcode'];
                $tmp['defaultShippingAddress.phoneNumber']              = $tmp['defaultBillingAddress.phoneNumber'];
                $tmp['defaultShippingAddress.country']                  = $tmp['defaultBillingAddress.country'];
                $tmp['defaultShippingAddress.company']                  = $tmp['defaultBillingAddress.company'];
                $tmp['defaultShippingAddress.city']                     = $tmp['defaultBillingAddress.city'];
                $tmp['defaultShippingAddress.additionalAddressLine1']   = $tmp['defaultBillingAddress.additionalAddressLine1'];
                $tmp['defaultShippingAddress.additionalAddressLine2']   = $tmp['defaultBillingAddress.additionalAddressLine2'];
            }
            
            $tmp['defaultShippingAddress.firstName']    = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['defaultShippingAddress.firstName']))));
            $tmp['defaultShippingAddress.lastName']     = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['defaultShippingAddress.lastName']))));
            $tmp['defaultShippingAddress.city']         = trim(ucwords(strtolower((string)$tmp['defaultShippingAddress.city'])));
            $tmp['defaultShippingAddress.street']       = trim(ucwords(strtolower((string)$tmp['defaultShippingAddress.street'])));
            $tmp['defaultShippingAddress.additionalAddressLine1'] = ucwords(strtolower((string) $tmp['defaultShippingAddress.additionalAddressLine1']));
            $tmp['defaultShippingAddress.additionalAddressLine2'] = ucwords(strtolower((string) $tmp['defaultShippingAddress.additionalAddressLine2']));
            $tmp['defaultShippingAddress.countryId']    = !empty($tmp['defaultShippingAddress.country']) ? self::getCountryIdByIsoCode($tmp['defaultShippingAddress.country']) : $settings['customerDefaultCountryId'];
            
            // edge case: remove serialized stuff
            $tmp['defaultBillingAddress.company']       = (self::isSerialized($tmp['defaultBillingAddress.company'])) ? '' : ucwords(strtolower((string) $tmp['defaultBillingAddress.company']));
            
            $tmp['defaultBillingAddress.firstName']     = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['defaultBillingAddress.firstName']))));
            $tmp['defaultBillingAddress.lastName']      = implode('-', array_map('ucfirst', explode('-', strtolower((string) $tmp['defaultBillingAddress.lastName']))));
            $tmp['defaultBillingAddress.city']          = trim(ucwords(strtolower((string) $tmp['defaultBillingAddress.city'])));
            $tmp['defaultBillingAddress.street']        = trim(ucwords(strtolower((string) $tmp['defaultBillingAddress.street'])));
            $tmp['defaultBillingAddress.additionalAddressLine1'] = ucwords(strtolower((string) $tmp['defaultBillingAddress.additionalAddressLine1']));
            $tmp['defaultBillingAddress.additionalAddressLine2'] = ucwords(strtolower((string) $tmp['defaultBillingAddress.additionalAddressLine2']));
            $tmp['defaultBillingAddress.countryId']     = !empty($tmp['defaultBillingAddress.country']) ? self::getCountryIdByIsoCode($tmp['defaultBillingAddress.country']) : $settings['customerDefaultCountryId'];
            
            $tmp['boundSalesChannelId']                 = apply_filters('customer_bound_sales_channel_id', (string) $tmp['defaultBillingAddress.country'], (int) $user_id, (string) $settings['customerDefaultSalesChannelId']);
            $tmp['salesChannelId']                      = apply_filters('customer_sales_channel_id', (string) $tmp['defaultBillingAddress.country'], (int) $user_id, (string) $settings['customerDefaultSalesChannelId']);
            $tmp['salutationId']                        = $tmp['defaultBillingAddress.salutationId'];
            
            // edge case: missing salutation on adresses
            if (empty($tmp['defaultShippingAddress.salutationId'])) {
                $tmp['defaultShippingAddress.salutationId'] = $tmp['salutationId'];
            }
            if (empty($tmp['defaultBillingAddress.salutationId'])) {
                $tmp['defaultBillingAddress.salutationId'] = $tmp['salutationId'];
            }
            
            // finally apply all individual field filters
            foreach($tmp as $key => $value) {
//                 echo $key;
//                 echo $value;
//                 die;
                $tmp[$key] = apply_filters('shopware_six_exporter_filter_customer_'. $key, $value, $user_id, $result);
            }
            $r[] = $tmp;
        }
        $results = $r;
        
        /**
         * Loop over results again and set defautl values where no data given
         */
        $r = [];
        foreach ($results as $customer) {
            $c = [];
            foreach(self::getHeaders() as $key) {
                $c[$key] = $defaults[$key];
                if (isset($customer[$key]) && !empty($customer[$key])) {
                    $c[$key] = $customer[$key];
                }
            }
            $r[] = $c;
        }
        
        return  $r;
    }
    
    public function filter_active($value, $user_id, $row, $default) {
        return 0;
    }
    
    public static function isSerialized(?string $string) : bool 
    {
        if (is_null($string)) {
            return false;
        }
        return ($string == 'b:0;' || @unserialize($string) !== false);
    }
    
    public static function isJson(?string $string) : bool
    {
        if (is_null($string)) {
            return false;
        }
        if (empty($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function getCountryIdByIsoCode(string $iso_code) : string
    {
        switch ($iso_code) {
            case 'CH':
                return '3667a6331f7141fdacf9aa2dbd2c61de';
                break;
            case 'AT':
                return '7deabf41ba494c8ba376c72cb708f342';
                break;
            case 'LI':
                return 'bde89b35a2574aa788d5a67dcff855d4';
                break;
            case 'FR':
                return 'c69089f50ddd49e0946bb2079dd699be';
                break;
            case 'LU':
                return '028c96fdb4284377a404f04b4259cfa4';
                break;
            case 'NL':
                return '6abc35e041424a79b6dbce60e5eda6a5';
                break;
            case 'BE':
                return 'c5b9ce79c81846889a23bc65765d51e6';
                break;
            case 'ES':
                return '06755a1863834c7786001c51c6770ce5';
                break;
            case 'IT':
                return 'cd2bb4721f20471d8f2b22ec8a028b97';
                break;
            case 'GB':
                return '156103f32ca142c68c1d4301f6a7f279';
                break;
            case 'IE':
                return 'e89cfb36edab4539883d84339d8f5041';
                break;
            case 'DE':
                return 'a0ac9c9b88024f4ea4230697158b9c01';
                break;
            default:
                return json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultCountryId'];
                break;
        }
    }
    
    public static function getSalesChannelIdByCountry(string $iso_code) : string
    {
        switch ($iso_code) {
            case 'BE':
                return strtolower('40366E32E048476C82F2FF73518C7232');
                break;
            default:
                return json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultSalesChannelId'];
                break;
        }
    }
    
    /**
     * @param array $records
     * @return array
     */
    private function getOrderCountByUserId(int $user_id) : int
    {
        global $wpdb;

        $count = $wpdb->get_row(sprintf("SELECT COUNT(p.ID) AS count FROM wp_postmeta pm
        LEFT JOIN wp_posts p ON (pm.post_id = p.ID)
        WHERE meta_key = '_customer_user' AND meta_value = '%s' AND p.post_type = 'shop_order';", $user_id), ARRAY_A);
        return (int) $count['count'];
    }
    
    private function getLastOrderDateByUserId(int $user_id) : ?string
    {
        global $wpdb;
        
        $res = $wpdb->get_row(sprintf("SELECT p.post_date_gmt AS orderDate FROM wp_postmeta pm
        LEFT JOIN wp_posts p ON (pm.post_id = p.ID)
        WHERE meta_key = '_customer_user' AND meta_value = '%s' AND p.post_type = 'shop_order' ORDER BY ID DESC LIMIT 1;", $user_id), ARRAY_A);
        if ($res) {
            return $res['orderDate'];
        }
        return null;
    }
    
    private static function getDefaults() : array
    {
        $options = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        $host = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        $customerDefaultPaymentMethodId     = !empty($options['customerDefaultPaymentMethodId']) ? $options['customerDefaultPaymentMethodId'] : null;
        $customerDefaultLanguageId          = !empty($options['customerDefaultLanguageId']) ? $options['customerDefaultLanguageId'] : null;
        $customerDefaultEmail               = !empty($options['customerDefaultEmail']) ? $options['customerDefaultEmail'] : sprintf('kunden+%s@%s', Uuid::uuid4()->toString(), $host); // if not configured differently import with fake, local emails
        $customerDefaultSalesChannelId      = !empty($options['customerDefaultSalesChannelId']) ? $options['customerDefaultSalesChannelId'] : null; // if not configured differently import with fake, local emails
        $customerBoundSalesChannelId        = !empty($options['customerBoundSalesChannelId']) ? $options['customerBoundSalesChannelId'] : null;
        $customerDefaultCountryId           = !empty($options['customerDefaultCountryId']) ? $options['customerDefaultCountryId'] : null;
        $customerSalutationIdUnknown        = !empty($options['customerSalutationIdUnknown']) ? $options['customerSalutationIdUnknown'] : null;
        $customerDefaultGroupId             = !empty($options['customerDefaultGroupId']) ? $options['customerDefaultGroupId'] : null;

        return [
            'active' => 1,
            'affiliateCode' => null,
            'autoIncrement' => null,
            'birthday' => null,
            'boundSalesChannelId' => $customerBoundSalesChannelId,
            'campaignCode' => null,
            'company' => null,
            'createdAt' => date('c'),
            'customFields' => null,
            'customerNumber' => null,
            'defaultBillingAddress.additionalAddressLine1' => 'N/A',
            'defaultBillingAddress.additionalAddressLine2' => 'N/A',
            'defaultBillingAddress.city' => 'N/A',
            'defaultBillingAddress.company' => ' ',
            'defaultBillingAddress.countryId' => $customerDefaultCountryId,
            'defaultBillingAddress.countryStateId' => null,
            'defaultBillingAddress.createdAt' => null,
            'defaultBillingAddress.customFields' => null,
            'defaultBillingAddress.customerId' => null,
            'defaultBillingAddress.department' => ' ',
            'defaultBillingAddress.firstName' => 'N/A',
            'defaultBillingAddress.id' => null,
            'defaultBillingAddress.lastName' => 'N/A',
            'defaultBillingAddress.phoneNumber' => 'N/A',
            'defaultBillingAddress.salutationId' => $customerSalutationIdUnknown,
            'defaultBillingAddress.street' => 'N/A',
            'defaultBillingAddress.title' => ' ',
            'defaultBillingAddress.updatedAt' => null,
            'defaultBillingAddress.vatId' => null,
            'defaultBillingAddress.zipcode' => 'N/A',
            'defaultBillingAddressId' => null,
            'defaultPaymentMethodId' => $customerDefaultPaymentMethodId,
            'defaultShippingAddress.additionalAddressLine1' => 'N/A',
            'defaultShippingAddress.additionalAddressLine2' => 'N/A',
            'defaultShippingAddress.city' => 'N/A',
            'defaultShippingAddress.company' => ' ',
            'defaultShippingAddress.countryId' => $customerDefaultCountryId,
            'defaultShippingAddress.countryStateId' => null,
            'defaultShippingAddress.createdAt' => null,
            'defaultShippingAddress.customFields' => null,
            'defaultShippingAddress.customerId' => null,
            'defaultShippingAddress.department' => ' ',
            'defaultShippingAddress.firstName' => 'N/A',
            'defaultShippingAddress.id' => null,
            'defaultShippingAddress.lastName' => 'N/A',
            'defaultShippingAddress.phoneNumber' => 'N/A',
            'defaultShippingAddress.salutationId' => $customerSalutationIdUnknown,
            'defaultShippingAddress.street' => 'N/A',
            'defaultShippingAddress.title' => ' ',
            'defaultShippingAddress.updatedAt' => null,
            'defaultShippingAddress.vatId' => null,
            'defaultShippingAddress.zipcode' => 'N/A',
            'defaultShippingAddressId' => null,
            'doubleOptInConfirmDate' => date('c'),
            'doubleOptInEmailSentDate' => date('c'),
            'doubleOptInRegistration' => 0,
            'email' => $customerDefaultEmail,
            'firstLogin' => date('c'),
            'firstName' => 'N/A',
            'groupId' => $customerDefaultGroupId,
            'guest' => 0,
            'hash' => null,
            'id' => null,
            'languageId' => $customerDefaultLanguageId,
            'lastLogin' => null,
            'lastName' => 'N/A',
//             'lastOrderDate' => null, 
            'lastPaymentMethodId' => $customerDefaultPaymentMethodId,
            'legacyEncoder' => 'wordpress',
            'legacyPassword' => '',
            'newsletter' => 0,
//             'orderCount' => null,
            'password' => null,
            'promotions' => null,
            'recoveryCustomer' => null,
            'remoteAddress' => null,
            'requestedGroupId' => null,
            'salesChannelId' => $customerDefaultSalesChannelId,
            'salutationId' => $customerSalutationIdUnknown,
            'tagIds' => null,
            'title' => null,
            'updatedAt' => null,
            'vatIds' => null,
        ];
    }
    
    public static function getHeaders() : array 
    {
        return array_keys(self::getDefaults());
    }
}