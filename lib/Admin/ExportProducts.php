<?php declare(strict_types = 1);

/**
 * Product Export
 *
 * @link       https://erikpoehler.com/shopware-six-exporter/
 *
 * @package    Shopware_Six_Exporter
 * @subpackage Shopware_Six_Exporter/Admin
 */

namespace vardumper\Shopware_Six_Exporter\Admin;

use League\Csv\Writer;
use vardumper\Shopware_Six_Exporter\Plugin;

class ExportProducts {
    /** @var Writer $csv */
    private $csv;
    
    /** @var array $settings */
    private $settings;
    
    public function __construct()
    {
        $this->settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        $this->csv = Writer::createFromString();
        $this->csv->setDelimiter(';');
    }
    
    public function export() {
        //insert the header
        $this->csv->insertOne($this->getHeaders());
        
        $records = $this->getRecords();
        
        //insert all the records
        $this->csv->insertAll($records);
        
        return $this;
    }

    public function getCsv() : string
    {
        return $this->csv->getContent();
    }
    
    public static function getHeaders() : array
    {
        return array_keys(self::getDefaults());
    }
    
    public static function getRecords($random = null) : array
    {
        global $wpdb;
        
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $defaults = self::getDefaults();
        $query = sprintf("SELECT MAX( CASE WHEN pm.meta_key = '_visibility' AND p.ID = pm.post_id AND pm.meta_value = 'visible' THEN 1 ELSE 0 END ) AS `active`,
            MAX( CASE WHEN pm.meta_key = 'shopware_exporter_random_id' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `autoIncrement`,
            MAX( CASE WHEN pm.meta_key = '_visibility' AND p.ID = pm.post_id AND pm.meta_value = 'visible' THEN 1 ELSE 0 END ) AS `available`,
            MAX( CASE WHEN pm.meta_key = '_stock' AND p.ID = pm.post_id THEN pm.meta_value - IFNULL((SELECT SUM( order_item_meta.meta_value )
            			FROM wp_posts AS posts
            			LEFT JOIN wp_woocommerce_order_items as order_items ON posts.ID = order_items.order_id
            			LEFT JOIN wp_woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            			LEFT JOIN wp_woocommerce_order_itemmeta as order_item_meta2 ON order_items.order_item_id = order_item_meta2.order_item_id
            			WHERE 	order_item_meta.meta_key    = '_qty'
            			AND 	order_item_meta2.meta_key   = '_product_id'
            			AND 	order_item_meta2.meta_value = p.ID
            			AND 	posts.post_type             IN ( 'shop_order' )
            			AND 	posts.post_status           = 'wc-pending'),0) END ) AS `availableStock`,
            (SELECT COUNT(ID) FROM wp_posts WHERE post_type = 'product_variation' AND post_status IN ('publish','draft') AND post_parent = p.ID) AS `childCount`,
            '' AS `configuratorGroupConfig`,
            '' AS `cover.versionId`,
            '' AS `cover.media.id`,
            MAX( CASE WHEN pm.meta_key = '_thumbnail_id' AND p.ID = pm.post_id THEN (SELECT guid FROM wp_posts WHERE ID = pm.meta_value) END ) AS `cover.media.url`,
            '' AS `cover.position`,
            '' AS `cover.customFields`,
            MAX( CASE WHEN pm.meta_key = '_thumbnail_id' AND p.ID = pm.post_id THEN (SELECT post_date FROM wp_posts WHERE ID = pm.meta_value) END ) AS `cover.createdAt`,
            MAX( CASE WHEN pm.meta_key = '_thumbnail_id' AND p.ID = pm.post_id THEN (SELECT post_modified FROM wp_posts WHERE ID = pm.meta_value) END ) AS `cover.updatedAt`,
            '' AS `deliveryTime.id`,
            '' AS `deliveryTime.name`,
            '' AS `deliveryTime.customFields`,
            '' AS `deliveryTime.min`,
            '' AS `deliveryTime.max`,
            '' AS `deliveryTime.unit`,
            '' AS `deliveryTime.createdAt`,
            '' AS `deliveryTime.updated`,
            MAX( CASE WHEN pm.meta_key = '_gp_ean' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `ean`,
            MAX( CASE WHEN pm.meta_key = '_height' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `height`,
            p.ID AS `id`,
            '' AS `product`,
            MAX( CASE WHEN pm.meta_key = '_ausverkauf' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `isCloseout`,
            MAX( CASE WHEN pm.meta_key = '_length' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `length`,
            '' AS `listingPrices`,
            '' AS `manufacturer.id`,
            '' AS `manufacturer.versionId`,
            '' AS `manufacturer.link`,
            '' AS `manufacturer.name`,
            '' AS `manufacturer.description`,
            '' AS `manufacturer.customFields`,
            '' AS `manufacturer.media.id`,
            '' AS `manufacturer.media.url`,
            '' AS `manufacturer.createdAt`,
            '' AS `manufacturer.updatedAt`,
            MAX( CASE WHEN pm.meta_key = '_gp_mpn' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `manufacturerNumber`,
            0 AS `markAsTopseller`,
            MAX( CASE WHEN pm.meta_key = '_max_cart_quantity' AND p.ID = pm.post_id THEN IF(pm.meta_value IS NULL OR pm.meta_value = '', NULL, pm.meta_value) END ) AS `maxPurchase`,
            IFNULL(MAX( CASE WHEN pm.meta_key = '_min_cart_quantity' AND p.ID = pm.post_id THEN IF(pm.meta_value IS NULL OR pm.meta_value = '', 1, pm.meta_value) END ),1) AS `minPurchase`,
            '' AS `optionIds`,
            '' AS `options`,
            '' AS `parent`,
            '' AS `price.DEFAULT.net`,
            '' AS `price.DEFAULT.gross`,
            '' AS `price.DEFAULT.currencyId`,
            '' AS `price.DEFAULT.linked`,
            '' AS `price.DEFAULT.listPrice`,
            MAX( CASE WHEN pm.meta_key = '_sku' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `productNumber`,
            '' AS `properties`,
            '' AS `purchasePrice`,
            '' AS `purchaseSteps`,
            '' AS `purchaseUnit`,
            '' AS `ratingAvarage`,
            '' AS `referenceUnit`,
            p.post_date AS `releaseDate`,
            '' AS `restockTime`,
            '' AS `shippingFree`,
            MAX( CASE WHEN pm.meta_key = '_stock' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `stock`,
            '' AS `tagIds`,
            '' AS `tags`,
            '' AS `tax.id`,
            '' AS `tax.taxRate`,
            '' AS `tax.name`,
            '' AS `tax.customField`,
            '' AS `tax.createdAt`,
            '' AS `tax.updatedAT`,
            '' AS `translations.DEFAULT`,
            p.post_title AS `translations.DEFAULT.name`,
            p.post_content AS `translations.DEFAULT.description`
            '' AS `translations.en_GB.metaTitle`,
            '' AS `unit.id`,
            '' AS `unit.shortCode`,
            IFNULL(MAX( CASE WHEN pm.meta_key = '_unit' AND p.ID = pm.post_id THEN IF(pm.meta_value IS NULL OR pm.meta_value = '0', NULL, pm.meta_value) END ),NULL) AS `unit.name`,
            '' AS `unit.customFields`,
            '' AS `unit.createdAt`,
            '' AS `unit.updatedAt`,
            '' AS `variantRestrictions`,
            '' AS `versionId`,
            '' AS `visibilities.all`,
            '' AS `visibilities.link`,
            '' AS `visibilities.search`,
            MAX( CASE WHEN pm.meta_key = '_weight' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `weight`,
            MAX( CASE WHEN pm.meta_key = '_width' AND p.ID = pm.post_id THEN pm.meta_value END ) AS `width` 
            FROM wp_posts AS p 
            JOIN wp_postmeta pm ON p.ID = pm.post_id 
            WHERE p.post_type = 'product'
            AND p.post_status IN ('publish','draft')
            GROUP BY p.ID
            ORDER BY p.ID ASC
            %s;",
            $random ? " LIMIT 1 " : " "
        );

        return $wpdb->get_results($query, ARRAY_A);
//         return [];
    }
    
    public static function getDefaults() : array
    {
        $options = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        $host = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        $defaultCurrencyId          = !empty($options['productDefaultCurrencyId']) ? $options['productDefaultCurrencyId'] : null;
        
        return [
            'active'                            => 1,                   // Angabe ob das Produkt aktiv ist	product
            'autoIncrement'                     => null,                //	Einmalige Dezimalzahl	product
            'available'                         => 1,                //	Angabe ob das Produkt verfügbar ist	product
            'availableStock'                    => null,                //	Verfügbarer Lagerbestand	product
            'childCount'                        => null,                //	Anzahl der Varianten	product
            'configuratorGroupConfig'           => null,                //	Eigene Sortierung der Eigenschaften	product
            'cover.versionId'                   => null,                //	UUID welche die Version des Vorschaubildes des Artikels angibt.	product_media
            'cover.media.id'                    => null,                //	UUID des Vorschaubildes des Artikels. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden. 	media
            'cover.media.url'                   => null,                //	Image URL
            'cover.media.translations.de-DE.alt' => null,               //  Image Alt
            'cover.media.translations.de-DE.title' => null,             //  Image Title
            'cover.position'                    => null,                //	Position des Vorschaubildes in der Medien Übersicht des Artikels.	product_media
            'cover.customFields'                => null,                //	Vorschaubild Zusatzfeld	custom_field
            'cover.createdAt'                   => null,                //	Vorschaubild hochgeladen	product_media
            'cover.updatedAt'                   => null,                //	Vorschaubild aktualisiert	product_media
            'deliveryTime.id'                   => null,                //	UUID der Lieferzeit	delivery_time
            'deliveryTime.name'                 => null,                //	Name der Lieferzeit	delivery_time_translation
            'deliveryTime.customFields'         => null,                //	Lieferzeit Zusatzfelder	delivery_time_translation
            'deliveryTime.min'                  => null,                //	Minimale Lieferzeit	delivery_time
            'deliveryTime.max'                  => null,                //	Maximale Lieferzeit	delivery_time
            'deliveryTime.unit'                 => null,                //	Liederzeit Einheit	delivery_time
            'deliveryTime.translations'         => null,                //	Übersetzungen der deliveryTime Felder. Hinter translations kann ein Punkt gesetzt und so auf weitere Felder zugegriffen werden.	delivery_time_translation
            'deliveryTime.createdAt'            => null,                //	Lieferzeit erstellt	delivery_time
            'deliveryTime.updated'              => null,                //	Lieferzeit aktualisiert	delivery_time
            'ean'                               => null,                //	EAN Nummer	product
            'height'                            => null,                //	Höhe des Produktes	product
            'id'                                => null,                //	UUID welche vom System vergeben wird. Beim Neuanlegen von Artikeln sollte diese Spalte leer gelassen werden.
            'product'                           => null,
            'isCloseout'                        => 0,                   //	Abverkauf	product
            'length'                            => null,                //	Länge	product
            'listingPrices'                     => null,                //	Erweiterte Preise	product
            'manufacturer.id'                   => null,                //	UUID des Herstellers	product_manufacturer
            'manufacturer.versionId'            => null,                //	UUID welche die Version des Herstellers angibt. 	product_manufacturer
            'manufacturer.link'                 => null,                //	Webseite des Herstellers	product_manufacturer
            'manufacturer.name'                 => null,                //	Name des Herstellers	product_manufacturer_translation
            'manufacturer.description'          => null,                //	Beschreibung des Herstellers	product_manufacturer_translation
            'manufacturer.customFields'         => null,                //	Hersteller Zusatzfelder	product_manufacturer_translation
            'manufacturer.media.id'             => null,                //	UUID des Herstellerbildes. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden.	media
            'manufacturer.media.url'            => null,                //	Image URL
            'manufacturer.translations'         => null,                //	Übersetzungen der manufacturer Felder. Hinter translations kann ein Punkt gesetzt und so auf weitere Felder zugegriffen werden.	product_manufacturer_translation
            'manufacturer.createdAt'            => null,                //	Hersteller angelegt	product_manufacturer
            'manufacturer.updatedAt'            => null,                //	Hersteller aktualisiert	product_manufacturer
            'manufacturerNumber'                => null,                //	Produktnummer des Herstellers	product
            'markAsTopseller'                   => 0,                   //	Produkt hervorheben	product
            'maxPurchase'                       => null,                //	Maximal Abnahme	product
            'minPurchase'                       => null,                //	Minimal Abnahme	product
            'optionIds'                         => null,                //	Variantenoptionen	product_option
            'options'                           => null,                //	Varianten Optionen	property_group_option
            'parent'                            => null,                //	Felder des Hauptproduktes bei Variantenartikel. Hinter parent kann ein Punkt gesetzt und somit auf alle Felder zugegriffen werden, welche auch im Object Type Product zur Verfügung stehen. 	product
            'price.DEFAULT.net'                 => null,                //	Standard netto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.gross'               => null,                //	Standard brutto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.currencyId'          => $defaultCurrencyId,  //	UUID der Währung. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	currency
            'price.DEFAULT.linked'              => null,                //	Angabe, ob der Nett und Bruttopreis verknüpft sind. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.listPrice'           => null,                //	Erweiterte Preise. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'productNumber'                     => null,                //	Produktnummer	product
            'properties'                        => null,                //	UUID der Eigenschaften getrennt durch ein Pipe-Symbol (|).	property_group_option
            'purchasePrice'                     => null,                //	Einkaufspreis	product
            'purchaseSteps'                     => null,                //	Staffelung	product
            'purchaseUnit'                      => null,                //	Verkaufseinheit	product
            'ratingAvarage'                     => null,                //	Durchschnittsbewertung	product
            'referenceUnit'                     => null,                //	Grundeinheit	product
            'releaseDate'                       => null,                //	Erscheinungsdatum	product
            'restockTime'                       => null,                //	Wiederauffüllzeit	product
            'shippingFree'                      => null,                //	Versandkostenfrei	product
            'stock'                             => null,                //	Lagerbestand	product
            'tagIds'                            => null,                //	Produkt Tags	product_tag
            'tags'                              => null,                //	UUID der Tags, getrennt durch ein Pipe-Symbol (|)	product_tag
            'tax.id'                            => null,                //	UUID des Steuersatzes	tax
            'tax.taxRate'                       => null,                //	Prozentsatz	tax
            'tax.name'                          => null,                //	Steuername	tax.translate
            'tax.customField'                   => null,                //	Zusatzfelder der Steuersätze	custom_field
            'tax.createdAt'                     => null,                //	Steuersatz erstellt	tax
            'tax.updatedAT'                     => null,                //	Steuersatz aktualisiert	tax
            'translations.DEFAULT'              => null,
            'translations.de_DE.name'           => null,
            'translations.de_DE.customFields'   => null,
            'translations.en_GB.metaTitle'      => null,                //	Alle Sprachabhängigen Produktfelder. DEFAULT kann hierbei durch die Sprache ersetzt werden und durch einen anschließenden Punkt kann auf das jeweilige Feld zugegriffen werden. Bspw. translations.en-GB.name	product_translation
            'unit.id'                           => null,                //	UUID der Maßeinheiten	unit
            'unit.shortCode'                    => null,                //	Maßeinheit Kürzel	unit_translation
            'unit.name'                         => null,                //	Maßeinheit Name	unit_translation
            'unit.customFields'                 => null,                //	Maßeinheit Zusatzfelder	unit_translation
            'unit.translations'                 => null,                //	Übersetzungen der Maßeinheit Felder. Hinter translations kann ein Punkt gesetzt werden und so auf weitere Felder zugegriffen werden.	unit_translation
            'unit.createdAt'                    => null,                //	Maßeinheit erstellt	unit
            'unit.updatedAt'                    => null,                //	Maßeinheit aktualisiert	unit
            'variantRestrictions'               => null,                //	Ausschlüsse von Varianten aus dem Variantengenerator	product
            'versionId'                         => null,                //	UUID welche die Version des Artikels angibt. 	product
            'visibilities.all'                  => null,                //	UUID des Verkaufskanals, in dem der Artikel komplett verfügbar ist.	product_visibility
            'visibilities.link'                 => null,                //	UUID des Verkaufskanals, in dem der Artikel versteckt ist und nur über den direkten Link erreichbar ist.	product_visibility
            'visibilities.search'               => null,                //	UUID des Verkaufskanals, in dem der Artikel nur über die Suche erreichbar ist.	product_visibility
            'weight'                            => null,                //	Gewicht	product
            'width'                             => null,                //  Breite
        ];
    }
}