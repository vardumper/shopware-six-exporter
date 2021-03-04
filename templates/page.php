<?php declare(strict_types = 1);

use vardumper\Shopware_Six_Exporter\Plugin;
?>
<div class="wrap" id="sw6export">
    <?php if ( isset($_POST) && count($_POST) > 0 && !empty($_POST['action']) ) { ?>
    <div class="notice notice-success"><p>Settings saved.</p></div>
    <?php } ?>
    <h1 class="wp-heading-inline">Wordpress & WooCommerce Export for Shopware 6</h1>
    <p>This plugin helps you creates importable CSV files for the most important entities like Customers, Guest Users, Products and Order History.</p>
    <h2>How to use this plugin:</h2>
    <ol class="ol-decimal">
        <li>Download the corresponding profile for the entity you want to import. Add it to your Shopware Database via MySQL.</li>
        <li>Now add the necessary Shopware Uuid's (for country, languages, salutations, sales-channels) and save them.</li>
        <li>Click "Export" to generate your .CSV</li>
    </ol>
    <p>If you need to do more advanced country to sales channel mappings, you can do that too by using this plugins <samp>get_shopware_six_sales_channel_by_billing_country($country)</samp> filter. Read about it in the documentation under advanced mappings.</p>

    <h2 class="nav-tab-wrapper wp-clearfix">
        <a href="#settings" data-id="settings" class="nav-tab nav-tab-active">Settings</a>
        <a href="#profiles" data-id="profiles" class="nav-tab">Shopware Import/Export Profiles</a>
        <a href="#preview" data-id="preview" class="nav-tab">Preview Data</a>
        <a href="#export" data-id="export" class="nav-tab">Export</a>
    </h2>
    <script type="text/javascript">jQuery(document).ready(function(){
        jQuery('#sw6export a.nav-tab').click(function(e){
            e.preventDefault();
            jQuery(this).parent().find('a').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('.content-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
    });</script>
    <style>input[type='text'] { font:Monaco,monospace,monospaced; }</style>
    <div class="content-tab" id="settings">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <fieldset>
                <table class="form-table" role="presentation">
                    <thead>
                        <tr>
                            <th scope="col" width="25%">Setting</th>
                            <th scope="col" width="35%">Value</th>
                            <th scope="col" width="">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Default Customer Language ID</th>
                            <td>
                                <input name="customerDefaultGroupId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultGroupId']; ?>" />
                            </td>
                            <td><p class="description">Your Default Customer Group ID (32 character Uuid). Required.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Default Customer Language ID</th>
                            <td>
                                <input name="customerDefaultLanguageId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultLanguageId']; ?>" />
                            </td>
                            <td><p class="description">Your Shopware Language ID (32 character Uuid). Leave empty to have Shopware figure it out.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Default Customer Payment Method ID</th>
                            <td>
                                <input name="customerDefaultPaymentMethodId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultPaymentMethodId']; ?>" />
                            </td>
                            <td><p class="description">Your Shopware Payment Method ID (32 character Uuid). Leave empty to have Shopware use it's default.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Default Customer SalesChannel ID</th>
                            <td>
                                <input name="customerDefaultSalesChannelId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultSalesChannelId']; ?>" />
                            </td>
                            <td><p class="description">Which Shopware Sales Channel shall your customers be assigned to? Shopware Sales Channel ID (32 character Uuid). Customer will be assigned to all Sales Channel if left empty.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Customer Bound to SalesChannel ID</th>
                            <td>
                                <input name="customerBoundSalesChannelId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerBoundSalesChannelId']; ?>" />
                            </td>
                            <td><p class="description">32 digit Uuid</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Customer Default Country ID</th>
                            <td>
                                <input name="customerDefaultCountryId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultCountryId']; ?>" />
                            </td>
                            <td><p class="description">32 digit Uuid</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Default Customer Email</th>
                            <td>
                                <input name="customerDefaultEmail" placeholder="fallback@<?php echo parse_url(get_bloginfo('url'), PHP_URL_HOST); ?>" class="large-text" type="text" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultEmail']; ?>" />
                            </td>
                            <td><p class="description">optional email used as a fallback when a customer/guest doesn't have an email address (e.g. guest/telesales/backend orders)</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Use Fake Emails</th>
                            <td>
                                <input id="fakeEmails" name="fakeEmails" class="" type="checkbox" <?php if (json_decode(get_option(Plugin::SETTINGS_KEY), true)['fakeEmails'] === 'yes') { ?>checked="checked"<?php } ?> value="yes" />
                            </td>
                            <td><p class="description">If checked no real customer emails will be exported. This might help you prevent sending out campaign/transactional emails during development.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Prevent Duplicates</th>
                            <td>
                                <input id="customerPreventDups" name="customerPreventDups" class="" type="checkbox" <?php if (json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerPreventDups'] === 'yes') { ?>checked="checked"<?php } ?> value="yes" />
                            </td>
                            <td><p class="description">If checked, this plugin will generate random unique IDs, store them in a new usermeta field with key <samp>shopware_exporter_random_id</samp>. This random ID is then used in the CSV file as autoIncrement ID. This basically allows you to re-import the same file several times. Customers will then be updated instead of duplicated. When activated, this plugin will automatically generate random unique IDs for newly registered users.</p></td>
                        </tr>
                        <tr>
                            <th scope="row">Customer Salutation ID Male</th>
                            <td>
                                <input name="customerSalutationIdMale" class="large-text"  type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerSalutationIdMale']; ?>" />
                            </td>
                            <td><p class="description">32 digit Uuid</p></td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Customer Salutation ID Female</th>
                            <td>
                                <input name="customerSalutationIdFemale" class="large-text"  type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerSalutationIdFemale']; ?>" />
                            </td>
                            <td><p class="description">32 digit Uuid</p></td>
                        </tr>
                        
                        <tr>
                            <th scope="row">Customer Salutation ID Unknown</th>
                            <td>
                                <input name="customerSalutationIdUnknown" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerSalutationIdUnknown']; ?>" /> 32 digit Uuid
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"></th>
                            <td>
                                <input type="hidden" name="action" value="save" />
                                <input name="action" class="button button-secondary button-large" type="submit" value="Save Settings" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
    <div class="content-tab" id="profiles" hidden="hidden">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">Shopware Import/Export Profile for Customer Entities</th>
                    <td>
                        <a class="button button-secondary button-large" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>customer-profile.sql">Download</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Shopware Import/Export Profile for Product Entities</th>
                    <td>
                        <a class="button button-secondary button-large" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>product-profile.sql">Download</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Shopware Import/Export Profile for Order Entities</th>
                    <td>
                        <a class="button button-secondary button-large" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>order-profile.sql">Download</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="content-tab" id="export" hidden="hidden">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&download_csv" method="post">
            <fieldset>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">Product Export</th>
                            <td>
                                <input name="action" class="button button-primary" type="submit" value="Export Products" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            
            <fieldset>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">Order Export</th>
                            <td>
                                <input name="action" class="button button-primary" type="submit" value="Export Orders" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
    
            <fieldset>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">Customer Export</th>
                            <td>
                                <input name="action" class="button button-primary button-large" type="submit" value="Export Customers" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Guests" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
    
</div>