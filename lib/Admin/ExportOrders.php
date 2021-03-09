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
class ExportOrders {
    
    private $csv;
    private $plugin;
    
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        
        $this->csv = Writer::createFromString();
        $this->csv->setDelimiter(';');
        
        //insert the header
        $this->csv->insertOne($this->getHeaders());
        
        $records = $this->getRecords();
        
        //insert all the records
        $this->csv->insertAll($records);
        
        return $this->csv->toString();
    }

    private static function getHeaders() : array 
    {
        return [
            'active',
            'affiliateCode',
            'autoIncrement',
            'birthday',
            'boundSalesChannel',
            'boundSalesChannelId',
            'campaignCode',
            'company',
            'createdAt',
            'customFields',
            'customerNumber',
            'defaultBillingAddress', // subset
            'defaultBillingAddress.additionalAddressLine1',
            'defaultBillingAddress.additionalAddressLine2',
            'defaultBillingAddress.city',
            'defaultBillingAddress.company',
            'defaultBillingAddress.country',
            'defaultBillingAddress.countryId',
            'defaultBillingAddress.countryState',
            'defaultBillingAddress.countryStateId',
            'defaultBillingAddress.createdAt',
            'defaultBillingAddress.customFields',
            'defaultBillingAddress.customer',
            'defaultBillingAddress.customerId',
            'defaultBillingAddress.department',
            'defaultBillingAddress.firstName',
            'defaultBillingAddress.id',
            'defaultBillingAddress.lastName',
            'defaultBillingAddress.phoneNumber',
            'defaultBillingAddress.salutation',
            'defaultBillingAddress.salutationId',
            'defaultBillingAddress.street',
            'defaultBillingAddress.title',
            'defaultBillingAddress.updatedAt',
            'defaultBillingAddress.vatId',
            'defaultBillingAddress.zipcode',
            'defaultBillingAddressId',
            'defaultPaymentMethod',
            'defaultPaymentMethodId',
            'defaultShippingAddress', // subset
            'defaultShippingAddress.additionalAddressLine1',
            'defaultShippingAddress.additionalAddressLine2',
            'defaultShippingAddress.city',
            'defaultShippingAddress.company',
            'defaultShippingAddress.country',
            'defaultShippingAddress.countryId',
            'defaultShippingAddress.countryState',
            'defaultShippingAddress.countryStateId',
            'defaultShippingAddress.createdAt',
            'defaultShippingAddress.customFields',
            'defaultShippingAddress.customer',
            'defaultShippingAddress.customerId',
            'defaultShippingAddress.department',
            'defaultShippingAddress.firstName',
            'defaultShippingAddress.id',
            'defaultShippingAddress.lastName',
            'defaultShippingAddress.phoneNumber',
            'defaultShippingAddress.salutation',
            'defaultShippingAddress.salutationId',
            'defaultShippingAddress.street',
            'defaultShippingAddress.title',
            'defaultShippingAddress.updatedAt',
            'defaultShippingAddress.vatId',
            'defaultShippingAddress.zipcode',
            'defaultShippingAddressId',
            'doubleOptInConfirmDate',
            'doubleOptInEmailSentDate',
            'doubleOptInRegistration',
            'email',
            'firstLogin',
            'firstName',
            'group',
            'groupId',
            'guest',
            'hash',
            'id',
            'language',
            'languageId',
            'lastLogin',
            'lastName',
            'lastOrderDate',
            'lastPaymentMethod',
            'lastPaymentMethodId',
            'legacyEncoder',
            'legacyPassword',
            'newsletter',
            'orderCount',
            'password',
            'promotions',
            'recoveryCustomer',
            'remoteAddress',
            'requestedGroup',
            'requestedGroupId',
            'salesChannel',
            'salesChannelId',
            'salutation',
            'salutationId',
            'tagIds',
            'tags',
            'title',
            'updatedAt',
            'vatIds',
        ];
    }
}