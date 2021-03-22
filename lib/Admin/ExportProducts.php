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

class ExportProducts {
    
    private $csv;
    
    public function __construct()
    {
        $this->csv = Writer::createFromString();
        $this->csv->setDelimiter(';');
        
        //insert the header
        $this->csv->insertOne($this->getHeaders());
        
        $records = $this->getRecords();
        
        //insert all the records
        $this->csv->insertAll($records);
        
        return $this->csv->toString();
    }

    public static function getHeaders() : array 
    {
        return [
            'active', // Angabe ob das Produkt aktiv ist	product
            'autoIncrement', //	Einmalige Dezimalzahl	product
            'available', //	Angabe ob das Produkt verfügbar ist	product
            'availableStock', //	Verfügbarer Lagerbestand	product
            'childCount', //	Anzahl der Varianten	product
            'configuratorGroupConfig', //	Eigene Sortierung der Eigenschaften	product
            'cover.versionId', //	UUID welche die Version des Vorschaubildes des Artikels angibt.	product_media
            'cover.media.Id', //	UUID des Vorschaubildes des Artikels. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden. 	media
            'cover.position', //	Position des Vorschaubildes in der Medien Übersicht des Artikels.	product_media
            'cover.customFields', //	Vorschaubild Zusatzfeld	custom_field
            'cover.createdAt', //	Vorschaubild hochgeladen	product_media
            'cover.updatedAt', //	Vorschaubild aktualisiert	product_media
            'deliveryTime.id', //	UUID der Lieferzeit	delivery_time
            'deliveryTime.name', //	Name der Lieferzeit	delivery_time_translation
            'deliveryTime.customFields', //	Lieferzeit Zusatzfelder	delivery_time_translation
            'deliveryTime.min', //	Minimale Lieferzeit	delivery_time
            'deliveryTime.max', //	Maximale Lieferzeit	delivery_time
            'deliveryTime.unit', //	Liederzeit Einheit	delivery_time
            'deliveryTime.translations', //	Übersetzungen der deliveryTime Felder. Hinter translations kann ein Punkt gesetzt und so auf weitere Felder zugegriffen werden.	delivery_time_translation
            'deliveryTime.createdAt', //	Lieferzeit erstellt	delivery_time
            'deliveryTime.updated', //	Lieferzeit aktualisiert	delivery_time
            'ean', //	EAN Nummer	product
            'height', //	Höhe des Produktes	product
            'Id', //	UUID welche vom System vergeben wird. Beim Neuanlegen von Artikeln sollte diese Spalte leer gelassen werden.
            'product', //
            'isCloseout', //	Abverkauf	product
            'length', //	Länge	product
            'listingPrices', //	Erweiterte Preise	product
            'manufacturer.id', //	UUID des Herstellers	product_manufacturer
            'manufacturer.versionId', //	UUID welche die Version des Herstellers angibt. 	product_manufacturer
            'manufacturer.link', //	Webseite des Herstellers	product_manufacturer
            'manufacturer.name', //	Name des Herstellers	product_manufacturer_translation
            'manufacturer.description', //	Beschreibung des Herstellers	product_manufacturer_translation
            'manufacturer.customFields', //	Hersteller Zusatzfelder	product_manufacturer_translation
            'manufacturer.media.Id', //	UUID des Herstellerbildes. Hinter media kann ein Punkt gesetzt und so auf weitere Felder innerhalb von media zugegriffen werden.	media
            'manufacturer.translations', //	Übersetzungen der manufacturer Felder. Hinter translations kann ein Punkt gesetzt und so auf weitere Felder zugegriffen werden.	product_manufacturer_translation
            'manufacturer.createdAt', //	Hersteller angelegt	product_manufacturer
            'manufacturer.updatedAt', //	Hersteller aktualisiert	product_manufacturer
            'manufacturerNumber', //	Produktnummer des Herstellers	product
            'markAsTopseller', //	Produkt hervorheben	product
            'maxPurchase', //	Maximal Abnahme	product
            'minPurchase', //	Minimal Abnahme	product
            'optionIds', //	Variantenoptionen	product_option
            'options', //	Varianten Optionen	property_group_option
            'parent', //	Felder des Hauptproduktes bei Variantenartikel. Hinter parent kann ein Punkt gesetzt und somit auf alle Felder zugegriffen werden, welche auch im Object Type Product zur Verfügung stehen. 	product
            'price.DEFAULT.net', //	Standard netto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.gross', //	Standard brutto Preis. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.currencyId', //	UUID der Währung. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	currency
            'price.DEFAULT.linked', //	Angabe, ob der Nett und Bruttopreis verknüpft sind. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'price.DEFAULT.listPrice', //	Erweiterte Preise. DEFAULT kann durch die jeweilige Währung ersetzt werden. Bsp. EUR	product
            'productNumber', //	Produktnummer	product
            'properties', //	UUID der Eigenschaften getrennt durch ein Pipe-Symbol (|).	property_group_option
            'purchasePrice', //	Einkaufspreis	product
            'purchaseSteps', //	Staffelung	product
            'purchaseUnit', //	Verkaufseinheit	product
            'ratingAvarage', //	Durchschnittsbewertung	product
            'referenceUnit', //	Grundeinheit	product
            'releaseDate', //	Erscheinungsdatum	product
            'restockTime', //	Wiederauffüllzeit	product
            'shippingFree', //	Versandkostenfrei	product
            'stock', //	Lagerbestand	product
            'tagIds', //	Produkt Tags	product_tag
            'tags', //	UUID der Tags, getrennt durch ein Pipe-Symbol (|)	product_tag
            'tax.Id', //	UUID des Steuersatzes	tax
            'tax.taxRate', //	Prozentsatz	tax
            'tax.name', //	Steuername	tax.translate
            'tax.customField', //	Zusatzfelder der Steuersätze	custom_field
            'tax.createdAt', //	Steuersatz erstellt	tax
            'tax.updatedAT', //	Steuersatz aktualisiert	tax
            'translations.DEFAULT', //
            'translations.de_DE.name', //
            'translations.de_DE.customFields', //
            'translations.en_GB.metaTitle', //	Alle Sprachabhängigen Produktfelder. DEFAULT kann hierbei durch die Sprache ersetzt werden und durch einen anschließenden Punkt kann auf das jeweilige Feld zugegriffen werden. Bspw. translations.en-GB.name	product_translation
            'unit.Id', //	UUID der Maßeinheiten	unit
            'unit.shortCode', //	Maßeinheit Kürzel	unit_translation
            'unit.name', //	Maßeinheit Name	unit_translation
            'unit.customFields', //	Maßeinheit Zusatzfelder	unit_translation
            'unit.translations', //	Übersetzungen der Maßeinheit Felder. Hinter translations kann ein Punkt gesetzt werden und so auf weitere Felder zugegriffen werden.	unit_translation
            'unit.createdAt', //	Maßeinheit erstellt	unit
            'unit.updatedAt', //	Maßeinheit aktualisiert	unit
            'variantRestrictions', //	Ausschlüsse von Varianten aus dem Variantengenerator	product
            'versionId', //	UUID welche die Version des Artikels angibt. 	product
            'visibilities.all', //	UUID des Verkaufskanals, in dem der Artikel komplett verfügbar ist.	product_visibility
            'visibilities.link', //	UUID des Verkaufskanals, in dem der Artikel versteckt ist und nur über den direkten Link erreichbar ist.	product_visibility
            'visibilities.search', //	UUID des Verkaufskanals, in dem der Artikel nur über die Suche erreichbar ist.	product_visibility
            'weight', //	Gewicht	product
            'width', // Breite
        ];
    }
    
    public static function getRecords($random = null) : array
    {
        return [];
    }
}