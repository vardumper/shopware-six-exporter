INSERT INTO `import_export_profile` (
    `id`, 
    `name`, 
    `system_default`, 
    `source_entity`, 
    `file_type`, 
    `delimiter`, 
    `enclosure`, 
    `mapping`, 
    `created_at`, 
    `updated_at`, 
    `config`
) VALUES (UNHEX('d6b4525f4cda4e80be757396c9202515'), 
    'Kunden', 
    0, 
    'customer', 
    'text/csv', 
    ';', 
    '"', 
    '[{"key":"active","mappedKey":"active"},{"key":"affiliateCode","mappedKey":"affiliateCode"},{"key":"autoIncrement","mappedKey":"autoIncrement"},{"key":"birthday","mappedKey":"birthday"},{"key":"boundSalesChannelId","mappedKey":"boundSalesChannelId"},{"key":"campaignCode","mappedKey":"campaignCode"},{"key":"company","mappedKey":"company"},{"key":"createdAt","mappedKey":"createdAt"},{"key":"customFields","mappedKey":"customFields"},{"key":"customerNumber","mappedKey":"customerNumber"},{"key":"defaultBillingAddress.additionalAddressLine1","mappedKey":"defaultBillingAddress.additionalAddressLine1"},{"key":"defaultBillingAddress.additionalAddressLine2","mappedKey":"defaultBillingAddress.additionalAddressLine2"},{"key":"defaultBillingAddress.city","mappedKey":"defaultBillingAddress.city"},{"key":"defaultBillingAddress.company","mappedKey":"defaultBillingAddress.company"},{"key":"defaultBillingAddress.countryId","mappedKey":"defaultBillingAddress.countryId"},{"key":"defaultBillingAddress.countryStateId","mappedKey":"defaultBillingAddress.countryStateId"},{"key":"defaultBillingAddress.createdAt","mappedKey":"defaultBillingAddress.createdAt"},{"key":"defaultBillingAddress.customFields","mappedKey":"defaultBillingAddress.customFields"},{"key":"defaultBillingAddress.customerId","mappedKey":"defaultBillingAddress.customerId"},{"key":"defaultBillingAddress.department","mappedKey":"defaultBillingAddress.department"},{"key":"defaultBillingAddress.firstName","mappedKey":"defaultBillingAddress.firstName"},{"key":"defaultBillingAddress.id","mappedKey":"defaultBillingAddress.id"},{"key":"defaultBillingAddress.lastName","mappedKey":"defaultBillingAddress.lastName"},{"key":"defaultBillingAddress.phoneNumber","mappedKey":"defaultBillingAddress.phoneNumber"},{"key":"defaultBillingAddress.salutationId","mappedKey":"defaultBillingAddress.salutationId"},{"key":"defaultBillingAddress.street","mappedKey":"defaultBillingAddress.street"},{"key":"defaultBillingAddress.title","mappedKey":"defaultBillingAddress.title"},{"key":"defaultBillingAddress.updatedAt","mappedKey":"defaultBillingAddress.updatedAt"},{"key":"defaultBillingAddress.vatId","mappedKey":"defaultBillingAddress.vatId"},{"key":"defaultBillingAddress.zipcode","mappedKey":"defaultBillingAddress.zipcode"},{"key":"defaultBillingAddressId","mappedKey":"defaultBillingAddressId"},{"key":"defaultPaymentMethodId","mappedKey":"defaultPaymentMethodId"},{"key":"defaultShippingAddress.additionalAddressLine1","mappedKey":"defaultShippingAddress.additionalAddressLine1"},{"key":"defaultShippingAddress.additionalAddressLine2","mappedKey":"defaultShippingAddress.additionalAddressLine2"},{"key":"defaultShippingAddress.city","mappedKey":"defaultShippingAddress.city"},{"key":"defaultShippingAddress.company","mappedKey":"defaultShippingAddress.company"},{"key":"defaultShippingAddress.countryId","mappedKey":"defaultShippingAddress.countryId"},{"key":"defaultShippingAddress.countryStateId","mappedKey":"defaultShippingAddress.countryStateId"},{"key":"defaultShippingAddress.createdAt","mappedKey":"defaultShippingAddress.createdAt"},{"key":"defaultShippingAddress.customFields","mappedKey":"defaultShippingAddress.customFields"},{"key":"defaultShippingAddress.customerId","mappedKey":"defaultShippingAddress.customerId"},{"key":"defaultShippingAddress.department","mappedKey":"defaultShippingAddress.department"},{"key":"defaultShippingAddress.firstName","mappedKey":"defaultShippingAddress.firstName"},{"key":"defaultShippingAddress.id","mappedKey":"defaultShippingAddress.id"},{"key":"defaultShippingAddress.lastName","mappedKey":"defaultShippingAddress.lastName"},{"key":"defaultShippingAddress.phoneNumber","mappedKey":"defaultShippingAddress.phoneNumber"},{"key":"defaultShippingAddress.salutationId","mappedKey":"defaultShippingAddress.salutationId"},{"key":"defaultShippingAddress.street","mappedKey":"defaultShippingAddress.street"},{"key":"defaultShippingAddress.title","mappedKey":"defaultShippingAddress.title"},{"key":"defaultShippingAddress.updatedAt","mappedKey":"defaultShippingAddress.updatedAt"},{"key":"defaultShippingAddress.vatId","mappedKey":"defaultShippingAddress.vatId"},{"key":"defaultShippingAddress.zipcode","mappedKey":"defaultShippingAddress.zipcode"},{"key":"defaultShippingAddressId","mappedKey":"defaultShippingAddressId"},{"key":"doubleOptInConfirmDate","mappedKey":"doubleOptInConfirmDate"},{"key":"doubleOptInEmailSentDate","mappedKey":"doubleOptInEmailSentDate"},{"key":"doubleOptInRegistration","mappedKey":"doubleOptInRegistration"},{"key":"email","mappedKey":"email"},{"key":"firstLogin","mappedKey":"firstLogin"},{"key":"firstName","mappedKey":"firstName"},{"key":"group","mappedKey":"group"},{"key":"groupId","mappedKey":"groupId"},{"key":"guest","mappedKey":"guest"},{"key":"hash","mappedKey":"hash"},{"key":"id","mappedKey":"id"},{"key":"languageId","mappedKey":"languageId"},{"key":"lastLogin","mappedKey":"lastLogin"},{"key":"lastName","mappedKey":"lastName"},{"key":"lastOrderDate","mappedKey":"lastOrderDate"},{"key":"lastPaymentMethodId","mappedKey":"lastPaymentMethodId"},{"key":"legacyEncoder","mappedKey":"legacyEncoder"},{"key":"legacyPassword","mappedKey":"legacyPassword"},{"key":"newsletter","mappedKey":"newsletter"},{"key":"orderCount","mappedKey":"orderCount"},{"key":"password","mappedKey":"password"},{"key":"promotions","mappedKey":"promotions"},{"key":"remoteAddress","mappedKey":"remoteAddress"},{"key":"requestedGroupId","mappedKey":"requestedGroupId"},{"key":"salesChannelId","mappedKey":"salesChannelId"},{"key":"salutationId","mappedKey":"salutationId"},{"key":"tagIds","mappedKey":"tagIds"},{"key":"title","mappedKey":"title"},{"key":"updatedAt","mappedKey":"updatedAt"},{"key":"vatIds","mappedKey":"vatIds"}]', 
    NOW(), 
    NOW(), 
    NULL
); 