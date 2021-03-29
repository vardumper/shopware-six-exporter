<?php declare(strict_types = 1);

use vardumper\Shopware_Six_Exporter\Plugin;
use vardumper\Shopware_Six_Exporter\Admin\ExportCustomers;
use vardumper\Shopware_Six_Exporter\Admin\ExportGuests;
use vardumper\Shopware_Six_Exporter\Admin\ExportProducts;
?>
<div class="wrap" id="shopware-six-exporter">
    <?php if ( isset($_POST) && count($_POST) > 0 && !empty($_POST['action']) ) { ?>
    <div class="notice notice-success"><p>Settings saved.</p></div>
    <?php } ?>
    <h1 class="wp-heading">Shopware 6 Exporter<img style="float:right;" src="<?php echo plugin_dir_url(__DIR__) ?>assets/shopware-logo.svg" width="150" />
        <div style="display:inline-block;float:right;height:50px;width:165px;background:url(<?php echo plugin_dir_url(__DIR__) ?>assets/woocommerce-logo.svg);background-position: center;background-repeat: no-repeat;background-size: cover;"></div>
    </h1>
    <p>
        This plugin helps you export your customer, product and order data in a ready-to-import format supported by Shopware.
    </p>
    <span style="clear:both;"></span>
    <h2 class="nav-tab-wrapper wp-clearfix">
        <a href="#settings" data-id="settings" class="nav-tab nav-tab-active">Settings</a>
        <a href="#profiles" data-id="profiles" class="nav-tab">Shopware Import/Export Profiles</a>
        <a href="#preview" data-id="preview" class="nav-tab">Preview Data</a>
        <a href="#export" data-id="export" class="nav-tab">Export</a>
    </h2>
    
    <div class="content-tab" id="settings">
        <ul class="subsubsub">
            <li><a href="#customers" data-id="settings-customers" class="current">Customers & Guests</a> |</li>
            <li><a href="#products" data-id="settings-products" class="disabled">Products</a> |</li>
            <li><a href="#orders" data-id="settings-orders" class="disabled" style="pointer-events: none;">Orders</a></li>
        </ul>
        <br class="clear">
        
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div class="settings-tab" id="settings-customers">
                <h2>Customer & Guest Settings</h2>
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
                                <th scope="row">Default Customer Group ID</th>
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
                                <td><p class="description">If you do not bound the customer to a specific sales channel he will be able to log into all. This basically locks the customer into a specific sales channel. Sales Channel ID (32 character Uuid)</p></td>
                            </tr>
                            <tr>
                                <th scope="row">Default Customer Country ID</th>
                                <td>
                                    <input name="customerDefaultCountryId" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultCountryId']; ?>" />
                                </td>
                                <td><p class="description">In case the country is missing in addresses, this default Country ID will be assigned. Country ID (32 character Uuid)</p></td>
                            </tr>
                            <tr>
                                <th scope="row">Default Customer Email</th>
                                <td>
                                    <input name="customerDefaultEmail" placeholder="fallback@<?php echo parse_url(get_bloginfo('url'), PHP_URL_HOST); ?>" class="large-text" type="text" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerDefaultEmail']; ?>" />
                                </td>
                                <td><p class="description">Optional. Email address used as a fallback when a customer/guest doesn't have an email address (e.g. guest/telesales/backend orders)</p></td>
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
                                <td><p class="description">Salutation ID (32 character Uuid)</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Customer Salutation ID Female</th>
                                <td>
                                    <input name="customerSalutationIdFemale" class="large-text"  type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerSalutationIdFemale']; ?>" />
                                </td>
                                <td><p class="description">Salutation ID (32 character Uuid)</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Customer Salutation ID Unknown</th>
                                <td>
                                    <input name="customerSalutationIdUnknown" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['customerSalutationIdUnknown']; ?>" />
                                </td>
                                <td>Salutation ID (32 character Uuid)</td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Guest Export Chunk Size (optional)</th>
                                <td>
                                    <input name="guestChunkSize" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['guestChunkSize']; ?>" />
                                </td>
                                <td>Breaks down the SQL Query into smaller chunks. If empty chunks of 5000 guest orders will be processed sequentially in order to reduce DB resources used.</td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Guest Export Limit (optional)</th>
                                <td>
                                    <input name="guestLimit" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['guestLimit']; ?>" />
                                </td>
                                <td>This allows you to define how many guests you want to export at once. If left empty, all guests will be exported. This might be required if you have many hundreds of thousands of orders.</td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Guest Export Offest (optional)</th>
                                <td>
                                    <input name="guestOffset" class="large-text" type="text" length="32" maxlength="32" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['guestOffset']; ?>" />
                                </td>
                                <td>If left empty, exporting guests will start with the first guest order found. This is supposed to be used together with the 'Guest Export Limit'.</td>
                            </tr>
                            
                            <tr>
                                <th scope="row"></th>
                                <td>
                                    <input type="hidden" name="action" value="save" />
                                    <input name="action" class="button button-secondary button-large" type="submit" value="Save Settings" />
                                </td>
                                <td>When "Prevent Duplicates" is activated, a unique ID will be attached as meta value to all of your orders and users. So don't be surprised if saving the settings can take many minutes to finish.
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            
            <div class="settings-tab" id="settings-products" hidden="hidden">
                <h2>Product Settings</h2>
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
                                <th scope="row">Post Media Parent Folder ID</th>
                                <td>
                                    <input id="postMediaFolderId" name="postMediaFolderId" class="large-text" length="32" maxlength="32" type="text" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['postMediaFolderId'] ?? ''; ?>"/>
                                </td>
                                <td><p class="description">Folder ID (32 character Uuid). Default folder for post and page images. (probably your CMS Media folder ID)</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Product Media Parent Folder ID</th>
                                <td>
                                    <input id="productMediaFolderId" name="productMediaFolderId" class="large-text" length="32" maxlength="32" type="text" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['productMediaFolderId'] ?? ''; ?>"/>
                                </td>
                                <td><p class="description">Folder ID (32 character Uuid). Default folder for product images. (probably your CMS Media folder ID)</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Include Product Drafts</th>
                                <td>
                                    <input id="productIncludeDrafts" name="productIncludeDrafts" class="" type="checkbox" <?php if (json_decode(get_option(Plugin::SETTINGS_KEY), true)['productIncludeDrafts'] === 'yes') { ?>checked="checked"<?php } ?> value="yes" />
                                </td>
                                <td><p class="description">If activated, product drafts will be exported as well. Otherwise only products with status publish will be included.</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Prevent Duplicates</th>
                                <td>
                                    <input id="productPreventDups" name="productPreventDups" class="" type="checkbox" <?php if (json_decode(get_option(Plugin::SETTINGS_KEY), true)['productPreventDups'] === 'yes') { ?>checked="checked"<?php } ?> value="yes" />
                                </td>
                                <td><p class="description">If checked, this plugin will generate random unique IDs, store them in a new usermeta field with key <samp>shopware_exporter_random_id</samp>. This random ID is then used in the CSV file as autoIncrement ID. This allows you to re-import the same CSV file several times. Shopware will then update products instead of create duplicates. When activated, this plugin will automatically generate random unique IDs for newly created products.</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row">Product Default Currency ID</th>
                                <td>
                                    <input id="productDefaultCurrencyId" name="productDefaultCurrencyId" class="large-text" length="32" maxlength="32" type="text" value="<?php echo json_decode(get_option(Plugin::SETTINGS_KEY), true)['productDefaultCurrencyId'] ?? ''; ?>" />
                                </td>
                                <td><p class="description">Currency ID (32 character Uuid) of the WooCommerce currency you define your prices in.</p></td>
                            </tr>
                            
                            <tr>
                                <th scope="row"></th>
                                <td>
                                    <input type="hidden" name="action" value="save" />
                                    <input name="action" class="button button-secondary button-large" type="submit" value="Save Settings" />
                                </td>
                                <td>When "Prevent Duplicates" is activated, a unique ID will be attached as meta value to all of your products. So don't be surprised if saving the settings can take many minutes to finish.
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>

            <div class="settings-tab" id="settings-orders" hidden="hidden">
                <h2>Order Settings</h2>
            </div>
        </form>
    </div>
    
    <div class="content-tab" id="profiles" hidden="hidden">
        <h2>Shopware Import/Export Prfoiles</h2>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">Customer & Guest Profile</th>
                    <td>
                        <a class="button button-secondary button-large" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>customer-profile.sql">Download</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Product Profile</th>
                    <td>
                        <a class="button button-secondary button-large" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>product-profile.sql">Download</a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Order Profile</th>
                    <td>
                        <a class="button button-secondary button-large disabled" title="MySQL query to add the full customer import/export profile" href="<?php echo plugin_dir_url(__FILE__) ?>order-profile.sql">Download</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="content-tab" id="preview" hidden="hidden">
        
        <h2>Preview Data</h2>
        <p>This pages shows you how your final CSV would look like if you export your data with your currently saved settings. It simply picks some random customers/products/orders. This is supposed to help you validate the output before starting a lengthy export. Reload this page to see a differet set of customer/product/order.</p>
        
        <ul class="subsubsub">
            <li><a href="#customers" data-id="preview-customers" class="current">Customers</a> |</li>
            <li><a href="#guests" data-id="preview-guests" class="">Guests</a> |</li>
            <li><a href="#products" data-id="preview-products" class="">Products</a> |</li>
            <li><a href="#orders" data-id="preview-orders" class="disabled" style="pointer-events: none;">Orders</a></li>
        </ul>
        <br class="clear" />
        
        <div class="preview-tab" id="preview-customers">
            <h3>Customer</h3>
            <table class="form-table preview wp-list-table widefat fixed striped table-view-list posts" role="presentation">
                <thead>
                    <tr>
                        <th width="25%" style="width:25%;text-align:right;">Column</th>
                        <th width="75%" style="text-align:center;">Customer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $customers = ExportCustomers::getRecords(true);
                    if (!empty($customers)) {
                        foreach(ExportCustomers::getHeaders() as $name) { ?>
                            <tr>
                                <th style="text-align:right;"><?php echo $name; ?></th>
                                <td><?php echo $customers[0][$name]; ?></td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="2">No customers found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="preview-tab" id="preview-guests" hidden="hidden">
            <h3>Guest</h3>
            <table class="form-table preview wp-list-table widefat fixed striped table-view-list posts" role="presentation">
                <thead>
                    <tr>
                        <th width="25%" style="width:25%;text-align:right;">Column</th>
                        <th width="75%" style="text-align:center;">Customer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $guests = ExportGuests::getRecords(true);
                    if (!empty($guests)) {
                        foreach(ExportGuests::getHeaders() as $name) { ?>
                        <tr>
                            <th style="text-align:right;"><?php echo $name; ?></th>
                            <td><?php echo $guests[0][$name]; ?></td>
                        </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="2">No guests found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="preview-tab" id="preview-products" hidden="hidden">
            <h3>Product</h3>
            <table class="form-table preview wp-list-table widefat fixed striped table-view-list posts" role="presentation">
                <thead>
                    <tr>
                        <th width="25%" style="width:25%;text-align:right;">Column</th>
                        <th width="75%" style="text-align:center;">Product</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $products = ExportProducts::getRecords(true);
                    if (!empty($products)) {
                        foreach(ExportProducts::getHeaders() as $name) { ?>
                            <tr>
                                <th style="text-align:right;"><?php echo $name; ?></th>
                                <td><?php echo $products[0][$name]; ?></td>
                            </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="2">No products found</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="content-tab" id="export" hidden="hidden">
        <h2>Export Data</h2>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&download_csv" method="post">
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
            
            <fieldset>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">Product Export</th>
                            <td>
                                <input name="action" class="button button-primary button-large" type="submit" value="Export Product Media" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Product Categories" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Product Attributes" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Simple Products" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Variable Products" />&nbsp;<input name="action" class="button button-primary button-large" type="submit" value="Export Product Variations" />
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
                                <input name="action" class="button button-primary button-large disabled" type="submit" title="not implemented yet" value="Export Orders" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
    
    <hr />
    
    <div id="poststuff">
        <span style="clear:both; display:block;height:40px; width:100%;"></span>
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2><span>How to use this plugin?</span></h2>
                        <div class="inside">
                            <ol class="ol-decimal">
                                <li>If you don't want to loose your customers login credential during migration to Shopware, please install <a href="https://github.com/vardumper/wordpress-password-encoder-for-shopware-six">Wordpress Legacy Password Encoder for Shopware 6</a> in the Shopware installation you're importing into.</li>
                                <li>Download the Import/Export profiles and add them to your Shopware MySQL database. It's simpler and faster but you could also create these profiles in the Shopware Backend manually. Thes profiles provided here are based on the Shopware documentation and include all available columns.</li>
                                <li>Configure this plugin by opening the settings tab and entering the desired Shopware Uuid's (for countries, sales channels, salutations, languages and so on...) and save them.</li>
                                <li>Preview the resulting data and start exporting.</li>
                                <li>Finally you can start importing in Shopware</li> 
                            </ol>
                            
                            <p><strong>Advanced mappings</strong><br />
                            This plugin provides a lot of filters which allow you to manipulate each and every table cells' content before the final CSV is generated. One example: if you want to assign customers with different billing countries to individual sales channels, you can do that with filters. Browse some advanced sanitization and mapping examples in the <a href="">plugin documentation</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2><span>About</span></h2>
                        <div class="inside">
                            <p>
                                Plugin Author: <a href="https://github.com/vardumper">github.com/vardumper</a><br />
                                Current Version: <?php 
                                $plugin = new Plugin();
                                echo $plugin->get_version(); ?><br />
                                <?php if ($plugin->update_check()) { ?>
                                <strong><span style="color:#DC3232;">There is an update available</span></strong><br />
                                <?php } else { ?>
                                (You are using the latest version)<br />
                                <?php } ?>
                                Plugin Homepage: <a href="https://erikpoehler.com/shopware-six-exporter">Shopware 6 Exporter</a><br />
                                Plugin Support: <a href="https://github.com/vardumper/shopware-six-exporter/issues">Issue Tracker</a>
                            </p>
                            <br class="clear" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear" />
    </div>
</div>