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
        $query = sprintf("SELECT p.ID FROM wp_posts AS p 

        WHERE post_type IN ('product','product_variation') 
            GROUP BY p.ID
            ORDER BY p.ID ASC
            %s;",
            $random ? " JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM wp_users)) AS id) AS u2 WHERE u.ID >= u2.ID " : "",
            $random ? " LIMIT 1 " : " "
        );

        return $wpdb->get_results($query, ARRAY_A);
//         return [];
    }
    
    public static function getDefaults() : array
    {
        $options = json_decode(get_option(Plugin::SETTINGS_KEY), true);
        $host = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        $productDefaultSomethingId          = !empty($options['productDefaultSomethingId']) ? $options['productDefaultSomethingId'] : null;
        
        return [
            'active'                            => 1,                   // Angabe ob das Produkt aktiv ist	product
            'autoIncrement'                     => null,                //	Einmalige Dezimalzahl	product
            'available'                         => null,                //	Angabe ob das Produkt verfügbar ist	product
            'availableStock'                    => null,                //	Verfügbarer Lagerbestand	product
            'childCount'                        => null,                //	Anzahl der Varianten	product
            'configuratorGroupConfig'           => null,                //	Eigene Sortierung der Eigenschaften	product
            'cover.versionId'                   => null,                //	UUID welche die Version des Vorschaubildes des Artikels angibt.	product_media
            'cover.media.id'                    => null,                //	UUID des Vorschaubildes des Artikels. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden. 	media
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
            'isCloseout'                        => null,                //	Abverkauf	product
            'length'                            => null,                //	Länge	product
            'listingPrices'                     => null,                //	Erweiterte Preise	product
            'manufacturer.id'                   => null,                //	UUID des Herstellers	product_manufacturer
            'manufacturer.versionId'            => null,                //	UUID welche die Version des Herstellers angibt. 	product_manufacturer
            'manufacturer.link'                 => null,                //	Webseite des Herstellers	product_manufacturer
            'manufacturer.name'                 => null,                //	Name des Herstellers	product_manufacturer_translation
            'manufacturer.description'          => null,                //	Beschreibung des Herstellers	product_manufacturer_translation
            'manufacturer.customFields'         => null,                //	Hersteller Zusatzfelder	product_manufacturer_translation
            'manufacturer.media.id'             => null,                //	UUID des Herstellerbildes. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden.	media
            'manufacturer.translations'         => null,                //	Übersetzungen der manufacturer Felder. Hinter translations kann ein Punkt gesetzt und so auf weitere Felder zugegriffen werden.	product_manufacturer_translation
            'manufacturer.createdAt'            => null,                //	Hersteller angelegt	product_manufacturer
            'manufacturer.updatedAt'            => null,                //	Hersteller aktualisiert	product_manufacturer
            'manufacturerNumber'                => null,                //	Produktnummer des Herstellers	product
            'markAsTopseller'                   => null,                //	Produkt hervorheben	product
            'maxPurchase'                       => null,                //	Maximal Abnahme	product
            'minPurchase'                       => null,                //	Minimal Abnahme	product
            'optionIds'                         => null,                //	Variantenoptionen	product_option
            'options'                           => null,                //	Varianten Optionen	property_group_option
            'parent'                            => null,                //	Felder des Hauptproduktes bei Variantenartikel. Hinter parent kann ein Punkt gesetzt und somit auf alle Felder zugegriffen werden, welche auch im Object Type Product zur Verfügung stehen. 	product
            'price.DEFAULT.net'                 => null,                //	Standard netto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.gross'               => null,                //	Standard brutto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.currencyId'          => null,                //	UUID der Währung. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	currency
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