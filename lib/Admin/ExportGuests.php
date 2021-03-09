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
    
    private function getRecords() : array 
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
        global $wpdb;
        
        $settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        
        $defaults = $this->getDefaults();
        $query = sprintf("SELECT u.ID     AS `ID`,
	   MAX( CASE WHEN um.meta_key = 'customer_nr' and u.ID = um.user_id THEN um.meta_value END ) as `customerNumber`,
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
	   CASE WHEN um.meta_key = 'billing_title' and u.ID = um.user_id AND um.meta_value = 2 THEN '%s'
	   		WHEN um.meta_key = 'billing_title' and u.ID = um.user_id and um.meta_value = 1 THEN '%s'
	   	 	ELSE '%s'
	   END AS `defaultBillingAddress.salutationId`,
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
	   CASE WHEN um.meta_key = 'shipping_title' and u.ID = um.user_id AND um.meta_value = 2 THEN '%s'
	   		WHEN um.meta_key = 'shipping_title' and u.ID = um.user_id and um.meta_value = 1 THEN '%s'
	   	 	ELSE '%s'
	   END AS `defaultShippingAddress.salutationId`,
	   MAX( CASE WHEN um.meta_key = 'shipping_address_1' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.street`,
	   MAX( CASE WHEN um.meta_key = 'shipping_postcode' and u.ID = um.user_id THEN um.meta_value END ) as `defaultShippingAddress.zipcode`,
	   1 AS `doubleOptInRegistration`,
       0 AS `guest`,
       u.user_registered AS `firstLogin`,
       u.user_registered AS `createdAt`,
       u.user_email AS `email`,
       u.user_pass AS `egacyPassword`
FROM   wp_users u 
       JOIN wp_usermeta um 
         ON u.ID = um.user_id 
GROUP  BY u.ID 
ORDER  BY u.ID DESC;", $settings['customerSalutationIdFemale'], $settings['customerSalutationIdMale'], $settings['customerSalutationIdUnknown'], $settings['customerSalutationIdFemale'], $settings['customerSalutationIdMale'], $settings['customerSalutationIdUnknown']);
//         var_dump($query);die;
        $results = $wpdb->get_results($query, ARRAY_A);
        /**
         * Loop over results once and add additional info
         */
        $r = [];
        foreach($results as $result) {
            $user_id = (int) $result['ID'];
            
            $fakeEmails = isset(json_decode(get_option(Plugin::SETTINGS_KEY), true)['fakeEmails']) && json_decode(get_option(Plugin::SETTINGS_KEY), true)['fakeEmails'] === 'yes';
            if ($fakeEmails) {
                $result['email'] = md5($result['email']) . '@' . parse_url(get_bloginfo('url'), PHP_URL_HOST);
            }
            
            $tmp = $result;
            $tmp['orderCount']          = $this->getOrderCountByUserId($user_id);
            $tmp['lastOrderDate']       = $this->getLastOrderDateByUserId($user_id);
            $tmp['boundSalesChannelId'] = $this->getSalesChannelIdByCountry((string) $tmp['defaultBillingAddress.country']); /* make dynamic, not hardcoded – or at least add a filter */
            $tmp['salesChannelId']      = $this->getSalesChannelIdByCountry((string) $tmp['defaultBillingAddress.country']); /* make dynamic, not hardcoded – or at least add a filter */
            $r[] = $tmp;
        }
        $results = $r;
        
        /**
         * Loop over results again and set defautl values where no data given
         */
        $r = [];
        foreach ($results as $customer) {
            $c = [];
            foreach($this->getHeaders() as $key) {
                $c[$key] = $defaults[$key];
                if (isset($customer[$key])) {
                    $c[$key] = $customer[$key];
                }
            }
            $r[] = $c;
        }
        
        return  $r;
    }
    
    private function getSalesChannelIdByCountry(string $country) : string
    {
        switch ($country) {
            case 'AT':
                return strtolower('00C8E87F5C0A4772A323FCADD70CAC86');
                break;
            case 'CH':
                return strtolower('C038A00668F94D9F85D3C3D93DCBE7C1');
                break;
            default:
                return strtolower('5D0D9D14AFA44D8AA9E86D82E97404F3');
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
    
    private function getDefaults() : array
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
            'defaultBillingAddress.additionalAddressLine1' => null,
            'defaultBillingAddress.additionalAddressLine2' => null,
            'defaultBillingAddress.city' => null,
            'defaultBillingAddress.company' => null,
            'defaultBillingAddress.countryId' => $customerDefaultCountryId,
            'defaultBillingAddress.countryStateId' => null,
            'defaultBillingAddress.createdAt' => null,
            'defaultBillingAddress.customFields' => null,
            'defaultBillingAddress.customerId' => null,
            'defaultBillingAddress.department' => null,
            'defaultBillingAddress.firstName' => null,
            'defaultBillingAddress.id' => null,
            'defaultBillingAddress.lastName' => null,
            'defaultBillingAddress.phoneNumber' => null,
            'defaultBillingAddress.salutationId' => $customerSalutationIdUnknown,
            'defaultBillingAddress.street' => null,
            'defaultBillingAddress.title' => null,
            'defaultBillingAddress.updatedAt' => null,
            'defaultBillingAddress.vatId' => null,
            'defaultBillingAddress.zipcode' => null,
            'defaultBillingAddressId' => null,
            'defaultPaymentMethodId' => $customerDefaultPaymentMethodId,
            'defaultShippingAddress.additionalAddressLine1' => null,
            'defaultShippingAddress.additionalAddressLine2' => null,
            'defaultShippingAddress.city' => null,
            'defaultShippingAddress.company' => null,
            'defaultShippingAddress.countryId' => $customerDefaultCountryId,
            'defaultShippingAddress.countryStateId' => null,
            'defaultShippingAddress.createdAt' => null,
            'defaultShippingAddress.customFields' => null,
            'defaultShippingAddress.customerId' => null,
            'defaultShippingAddress.department' => null,
            'defaultShippingAddress.firstName' => null,
            'defaultShippingAddress.id' => null,
            'defaultShippingAddress.lastName' => null,
            'defaultShippingAddress.phoneNumber' => null,
            'defaultShippingAddress.salutationId' => $customerSalutationIdUnknown,
            'defaultShippingAddress.street' => null,
            'defaultShippingAddress.title' => null,
            'defaultShippingAddress.updatedAt' => null,
            'defaultShippingAddress.vatId' => null,
            'defaultShippingAddress.zipcode' => null,
            'defaultShippingAddressId' => null,
            'doubleOptInConfirmDate' => date('c'),
            'doubleOptInEmailSentDate' => date('c'),
            'doubleOptInRegistration' => 0,
            'email' => $customerDefaultEmail,
            'firstLogin' => date('c'),
            'firstName' => null,
            'groupId' => null,
            'guest' => 0,
            'hash' => null,
            'id' => null,
            'languageId' => $customerDefaultLanguageId,
            'lastLogin' => null,
            'lastName' => null,
            'lastOrderDate' => null,
            'lastPaymentMethodId' => $customerDefaultPaymentMethodId,
            'legacyEncoder' => 'wordpress',
            'legacyPassword' => null,
            'newsletter' => 0,
            'orderCount' => null,
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
    
    private function getHeaders() : array 
    {
        return array_keys($this->getDefaults());
    }
}