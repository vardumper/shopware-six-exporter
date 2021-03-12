<?php declare(strict_types = 1);

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://erikpoehler.com/shopware-six-exporter/
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
        $query = sprintf("SELECT 
                   u.ID     AS `ID`,
                   1 AS active,
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
                   MAX( CASE WHEN um.meta_key = 'billing_state' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.countryStateId`,
                   MAX( CASE WHEN um.meta_key = 'billing_first_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.firstName`,
                   MAX( CASE WHEN um.meta_key = 'billing_last_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.lastName`,
                   MAX( CASE WHEN um.meta_key = 'billing_phone' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.phoneNumber`,
                   MAX( CASE WHEN um.meta_key = 'billing_title' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.salutationId`,
                   MAX( CASE WHEN um.meta_key = 'billing_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.street`,
                   MAX( CASE WHEN um.meta_key = 'billing_postcode' and u.ID = um.user_id THEN um.meta_value END ) as `defaultBillingAddress.zipcode`,
                   MAX( CASE WHEN um.meta_key = 'shipping_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.additionalAddressLine1`,
                   MAX( CASE WHEN um.meta_key = 'shipping_address_2' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.additionalAddressLine2`,
                   MAX( CASE WHEN um.meta_key = 'shipping_city' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.city`,
                   MAX( CASE WHEN um.meta_key = 'shipping_company' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.company`,
                   MAX( CASE WHEN um.meta_key = 'shipping_country' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.country`,
                   MAX( CASE WHEN um.meta_key = 'shipping_state' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.countryStateId`,
                   MAX( CASE WHEN um.meta_key = 'shipping_first_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.firstName`,
                   MAX( CASE WHEN um.meta_key = 'shipping_last_name' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.lastName`,
                   MAX( CASE WHEN um.meta_key = 'shipping_phone' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.phoneNumber`,
                   MAX( CASE WHEN um.meta_key = 'shipping_title' and u.ID = um.user_id THEN um.meta_value END ) AS `defaultShippingAddress.salutationId`,
                   MAX( CASE WHEN um.meta_key = 'shipping_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.street`,
                   MAX( CASE WHEN um.meta_key = 'shipping_postcode' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.zipcode`,
                   1 AS `doubleOptInRegistration`,
                   0 AS `guest`,
                   u.user_registered AS `firstLogin`,
                   u.user_registered AS `createdAt`,
                   u.user_email AS `email`,
                   u.user_pass AS `legacyPassword`,
                   u.user_login AS `userLogin`
            FROM   wp_users u 
                   JOIN wp_usermeta um 
                     ON u.ID = um.user_id 
                   %s
            GROUP BY u.ID 
            ORDER BY u.ID ASC
            %s;",
            $random ? " JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM wp_users)) AS id) AS u2 WHERE u.ID >= u2.ID " : "",
            $random ? " LIMIT 1 " : ""
        );
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        /**
         * Loop over results once and add additional info, do basic sanitization, etc
         */
        $i = 0;
        foreach($results as $result) {

            // edge case: missing or incomplete shipping address -> copy over billing address
            if ( empty($result['defaultShippingAddress.city']) || empty($result['defaultShippingAddress.street']) ) {
                $result['defaultShippingAddress.firstName']                = $result['defaultBillingAddress.firstName'];
                $result['defaultShippingAddress.lastName']                 = $result['defaultBillingAddress.lastName'];
                $result['defaultShippingAddress.street']                   = $result['defaultBillingAddress.street'];
                $result['defaultShippingAddress.zipcode']                  = $result['defaultBillingAddress.zipcode'];
                $result['defaultShippingAddress.phoneNumber']              = $result['defaultBillingAddress.phoneNumber'];
                $result['defaultShippingAddress.country']                  = $result['defaultBillingAddress.country'];
                $result['defaultShippingAddress.countryStateId']           = $result['defaultBillingAddress.countryStateId'];
                $result['defaultShippingAddress.company']                  = $result['defaultBillingAddress.company'];
                $result['defaultShippingAddress.city']                     = $result['defaultBillingAddress.city'];
                $result['defaultShippingAddress.additionalAddressLine1']   = $result['defaultBillingAddress.additionalAddressLine1'];
                $result['defaultShippingAddress.additionalAddressLine2']   = $result['defaultBillingAddress.additionalAddressLine2'];
            }

            $result['defaultShippingAddress.firstName']    = implode('-', array_map('ucfirst', explode('-', strtolower((string) $result['defaultShippingAddress.firstName']))));
            $result['defaultShippingAddress.lastName']     = implode('-', array_map('ucfirst', explode('-', strtolower((string) $result['defaultShippingAddress.lastName']))));
            $result['defaultShippingAddress.city']         = trim(ucwords(strtolower((string)$result['defaultShippingAddress.city'])));
            $result['defaultShippingAddress.street']       = trim(ucwords(strtolower((string)$result['defaultShippingAddress.street'])));
            $result['defaultShippingAddress.additionalAddressLine1'] = ucwords(strtolower((string) $result['defaultShippingAddress.additionalAddressLine1']));
            $result['defaultShippingAddress.additionalAddressLine2'] = ucwords(strtolower((string) $result['defaultShippingAddress.additionalAddressLine2']));
            
            // edge case: remove serialized stuff
            $result['defaultBillingAddress.company']       = (self::isSerialized($result['defaultBillingAddress.company'])) ? '' : $result['defaultBillingAddress.company'];
            
            $result['defaultBillingAddress.firstName']     = implode('-', array_map('ucfirst', explode('-', strtolower((string) $result['defaultBillingAddress.firstName']))));
            $result['defaultBillingAddress.lastName']      = implode('-', array_map('ucfirst', explode('-', strtolower((string) $result['defaultBillingAddress.lastName']))));
            $result['defaultBillingAddress.city']          = trim(ucwords(strtolower((string) $result['defaultBillingAddress.city'])));
            $result['defaultBillingAddress.street']        = trim(ucwords(strtolower((string) $result['defaultBillingAddress.street'])));
            $result['defaultBillingAddress.additionalAddressLine1'] = ucwords(strtolower((string) $result['defaultBillingAddress.additionalAddressLine1']));
            $result['defaultBillingAddress.additionalAddressLine2'] = ucwords(strtolower((string) $result['defaultBillingAddress.additionalAddressLine2']));
            $result['salutationId']                        = $result['defaultBillingAddress.salutationId'];
            
            $results[$i] = $result;
            $i++;
        }

        /**
         * Loop over results again and set defautl values where no data given
         */
        $i = 0;
        foreach ($results as $customer) {
            $user_id = (int) $customer['ID'];
            
            $c = [];
            foreach(self::getHeaders() as $key) {
                // we want to apply filters on both: results from db & those not included in query, so we loop over all headers
                if (isset($customer[$key]) && !empty($customer[$key])) {
                    $c[$key] = apply_filters('shopware_six_exporter_filter_customer_'. str_replace('.','_',$key), $customer[$key], $user_id, $customer, $defaults[$key]);
                } else {
                    $c[$key] = apply_filters('shopware_six_exporter_filter_customer_'. str_replace('.','_',$key), null, $user_id, $customer, $defaults[$key]); 
                }
            }
            $results[$i] = $c;
            $i++;
        }
        return $results;
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
    
    public static function getDefaults() : array
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