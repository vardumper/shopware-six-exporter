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
class ExportGuests extends ExportCustomers {
    
    private const CHUNK_SIZE = 5000;
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
        
        $limit = $this->getLimit();
        $chunksize = apply_filters('shopware_six_exporter_filter_guest_chunksize', self::CHUNK_SIZE);
        $offset = apply_filters('shopware_six_exporter_filter_guest_offset', 0); // by default start with offset 0, otherwise you need to explicitly set it.
        
        $total_queries = ceil($limit / $chunksize);
        
        $i = 0;
        while ($i <= $total_queries) {
            $records = self::getRecords(false, $chunksize, $offset);
            $this->csv->insertAll($records);
            $offset = $offset + $chunksize;
            $i++;
        }
        return $this;
    }
    
    public function getLimit() : int {
        global $wpdb;
        
        $r = $wpdb->get_row("SELECT
        COUNT(p.ID) AS `count`
        FROM wp_posts AS p
        LEFT JOIN wp_postmeta cu ON ( p.ID = cu.post_id  AND cu.meta_key = '_customer_user' )
        WHERE p.post_type = 'shop_order'
        AND
        (
            cu.meta_id IS NULL
            OR
            cu.meta_value = '0'
            )
            ORDER  BY p.ID ASC;", ARRAY_A);
        return apply_filters('shopware_six_exporter_filter_guest_limit', (int) $r['count']);
    }
    
    public function getCsv() : string
    {
        return $this->csv->__toString();
    }
    
    public static function getRecords(bool $random = false, int $limit = null, int $offset = null) : array
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
        global $wpdb;
        
        $settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        
        $defaults = self::getDefaults();
        $query = sprintf("SELECT 
	p.ID AS `ID`,
	p.ID AS `customerNumber`,
    MAX( CASE WHEN pm.meta_key = 'shopware_exporter_random_id' and p.ID = pm.post_id THEN pm.meta_value END ) as `autoIncrement`,
	MAX( CASE WHEN pm.meta_key = '_billing_first_name' and pm.post_id = p.ID THEN pm.meta_value END ) as `firstName`,
	MAX( CASE WHEN pm.meta_key = '_billing_last_name' and pm.post_id = p.ID THEN pm.meta_value END ) as `lastName`,
    p.post_date as`updatedAt`,
    p.post_date as `lastLogin`,
	MAX( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.additionalAddressLine1`,
	   MAX( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.additionalAddressLine2`,
	   MAX( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.city`,
	   MAX( CASE WHEN pm.meta_key = '_billing_company' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.company`,
	   MAX( CASE WHEN pm.meta_key = '_billing_country' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.countryId`,
	   MAX( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.countryStateId`,
	   MAX( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.firstName`,
	   MAX( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.lastName`,
	   MAX( CASE WHEN pm.meta_key = '_billing_phone' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.phoneNumber`,
	   MAX( CASE WHEN pm.meta_key = '_billing_gender' and p.ID = pm.post_id AND pm.meta_value = 'Frau' THEN 'f'
	   		WHEN pm.meta_key = '_billing_gender' and p.ID = pm.post_id and pm.meta_value = 'Herr' THEN 'm'
	   	 	ELSE 'n/a'
	   END ) AS `defaultBillingAddress.salutationId`,
	   MAX( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.street`,
	   MAX( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultBillingAddress.zipcode`,
       MAX( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.additionalAddressLine1`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.additionalAddressLine2`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.city`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_company' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.company`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_country' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.countryId`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.countryStateId`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.firstName`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.lastName`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_phone' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.phoneNumber`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_title' and p.ID = pm.post_id AND pm.meta_value = 'Frau' THEN 'f'
	   		WHEN pm.meta_key = '_shipping_title' and p.ID = pm.post_id and pm.meta_value = 'Herr' THEN 'm'
	   	 	ELSE 'n/a'
	   END ) AS `defaultShippingAddress.salutationId`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.street`,
	   MAX( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as `defaultShippingAddress.zipcode`,
	   1 AS `doubleOptInRegistration`,
       1 AS `guest`,
       p.post_date AS `firstLogin`,
       p.post_date AS `createdAt`,
       MAX( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) AS `email`
FROM wp_posts AS p 
JOIN wp_postmeta pm ON p.ID = pm.post_id 
LEFT JOIN wp_postmeta cu ON ( p.ID = cu.post_id  AND cu.meta_key = '_customer_user' )
WHERE p.post_type = 'shop_order'
AND 
(
    cu.meta_id IS NULL
    OR
    cu.meta_value = '0'
)
GROUP  BY p.ID 
ORDER  BY p.ID ASC
%s;",
            $random ? " LIMIT 1 " : " LIMIT $offset, $limit "
        );
//         var_dump($query);die;
        $results = $wpdb->get_results($query, ARRAY_A);
        
        /**
         * Get rid of returning customers, we import the data used when the email last appeared
         */
        $tmp = [];
        foreach ($results as $result) {
            $tmp[$result['email']] = $result;
        }
        $results = array_values($tmp); // reindex array
        
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
                $result['defaultShippingAddress.countryId']                  = $result['defaultBillingAddress.countryId'];
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
        foreach ($results as $guest) {
            $order_id = (int) $guest['ID'];
            
            $c = [];
            foreach(self::getHeaders() as $key) {
                // we want to apply filters on both: results from db & those not included in query, so we loop over all headers
                if (isset($guest[$key]) && !empty($guest[$key])) {
                    $c[$key] = apply_filters('shopware_six_exporter_filter_customer_'. str_replace('.','_',$key), $guest[$key], $order_id, $guest, $defaults[$key]);
                } else {
                    $c[$key] = apply_filters('shopware_six_exporter_filter_customer_'. str_replace('.','_',$key), null, $order_id, $guest, $defaults[$key]);
                }
            }
            $results[$i] = $c;
            $i++;
        }
        return $results;
    }
}