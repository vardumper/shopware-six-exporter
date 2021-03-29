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

class ExportMedia {
    /** @var Writer $csv */
    private $csv;
    
    /** @var array $settings */
    private $settings;
    
    public function __construct() {
        $this->csv = Writer::createFromString();
        $this->csv->setDelimiter(';');
        $this->settings = json_decode(get_option(Plugin::SETTINGS_KEY), true);
    }
    
    public function export() {
        $headers = $this->getHeaders();
        $this->csv->insertOne($headers);
        
        $records = $this->getRecords();
        $this->csv->insertAll($records);
        
        return $this;
    }
    
    public function getHeaders() : array
    {
        return array_keys($this->getDefaults());
    }
    
    public function getDefaults() : array
    {
        $mediaFolderParentId = (isset($this->settings['mediaFolderParentId']) && !empty($this->settings['mediaFolderParentId'])) ? $this->settings['mediaFolderParentId'] : null;
        
        return [
            'id' => null,
            'mediaFolder.id' => null,
            'mediaFolder.name' => null,
            'mediaFolder.parent.id' => $mediaFolderParentId, // something like the product images folder
            'url' => '',
            'private' => 0,
            'type' => null,
            'title' => '',
            'alt' => '',
        ];
    }
    
    public function getCsv() : string
    {
        return $this->csv->__toString();
    }
    
    public function filenameToTitle(?string $title) : ?string
    {
        if (is_null($title)) {
            return null;
        }
        
        $title = str_replace(['-','_','–'], ' ', $title);
        return $title;
    }
    
    /**
     * Images are usually stored in:
     * attachments (main and product images)
     * content
     * content shortcodes
     * excerpt
     * aditional plugins
     */
    public function getRecords(bool $random = false) : array 
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        
        global $wpdb;
        
        $tmp = [];
        /**
         * step 1 get all posts/pages/products and their image attachments (leave out zips/pdfs for now)
         * @var array $images
         */
        $attachments = $wpdb->get_results("SELECT p.ID AS post_id,
	   p.post_title,
	   p.post_name,
       p.post_content, 
       p.post_excerpt,
       p.post_type,
       p.post_parent,
       attachment.ID AS image_id, 
       attachment.guid AS url,
       attachment.post_name AS image_name,
       attachment.post_content AS image_description,
       attachment.post_excerpt AS image_caption,
       attachment.post_content AS image_description,
       attachment.post_mime_type AS image_type,
       alt.meta_value AS image_alt
FROM   wp_posts AS p
JOIN wp_posts AS attachment ON (p.ID = attachment.post_parent AND attachment.post_type = 'attachment' AND attachment.post_mime_type LIKE 'image/%')
LEFT JOIN wp_postmeta AS alt ON (alt.post_id = attachment.ID AND alt.meta_key = '_wp_attachment_image_alt')
WHERE  p.post_type IN ( 'product', 'product_variation','post','page' );", ARRAY_A);
        
        $mediaFolderParentId = (isset($this->settings['mediaFolderParentId']) && !empty($this->settings['mediaFolderParentId'])) ? $this->settings['mediaFolderParentId'] : null;
        
        foreach($attachments as $post) {
            $folder_name = $post['post_name'];
            if (!empty($post['post_parent'])) {
                $parent = get_post((int) $post['post_parent']);
                $folder_name = $parent->post_name;
            }
            $tmp[$post['url']] = [
                'id' => get_post_meta($post['image_id'], 'shopware_six_exporter_uuid', true),
                'mediaFolder.id' => null,
                'mediaFolder.name' => sanitize_file_name(strtolower($folder_name)), // one folder for each post/page/product
                'mediaFolder.parent.id' => $mediaFolderParentId, // something like the product images folder
                'url' => $post['url'],
                'private' => 0,
                'type' => $post['image_type'],
                'title' => $this->filenameToTitle($post['image_name']),
                    'alt' => $this->filenameToTitle($post['image_alt']),
            ];
            
            /**
             * step 2 add gallery images
             */
            if (in_array($post['post_type'], ['product', 'product_variation'])) {
                $product_image_gallery = get_post_meta($post['post_id'], '_product_image_gallery', true);
                if (!empty($product_image_gallery)) {
                    $product_image_gallery = explode(',', $product_image_gallery);
                    
                    foreach($product_image_gallery as $product_image_id) {
                        $p = get_post((int) $product_image_id);
                        $tmp[$p->guid] = [
                            'id' => get_post_meta($post['image_id'], 'shopware_six_exporter_uuid', true),
                            'mediaFolder.id' => null,
                            'mediaFolder.name' => sanitize_file_name(strtolower($folder_name)), // one folder for each post/page/product
                            'mediaFolder.parent.id' => $mediaFolderParentId, // something like the product images folder
                            'url' => $p->guid,
                            'private' => 0,
                            'type' => $p->post_mime_type,
                            'title' => $this->filenameToTitle($p->post_title),
                            'alt' => $this->filenameToTitle(get_post_meta($p->ID, '_wp_attachment_image_alt', true)),
                        ];
                    }
                }
            }
            
            /**
             * step 3 extract and add images from content & excerpt & shortcodes
             */
            if ( !empty($post['post_content']) && class_exists('DOMDocument') ) {
                $the_content = new \DOMDocument();
                libxml_use_internal_errors(true);
                $the_content->loadHTML('<div>'.do_shortcode($post['post_content']).'</div>');
                libxml_clear_errors();
                libxml_use_internal_errors(false);
                
                $xpath = new \DOMXPath($the_content);
                // <img />
                $img = $xpath->query('//img');
                if ($img->length > 0) {
                    foreach($img as $image) {
                        /** @var \DOMNode $image */
                        $path_parts = pathinfo($image->getAttribute('src'));
                        
                        /** @todo check for and handle srcset as well **/
                        $tmp[$image->getAttribute('src')] = [
                            'id' => null,
                            'mediaFolder.id' => null,
                            'mediaFolder.name' => sanitize_file_name(strtolower($folder_name)), // one folder for each post/page/product
                            'mediaFolder.parent.id' => $mediaFolderParentId, // something like the product images folder
                            'url' => $image->getAttribute('src'),
                            'private' => 0,
                            'type' => null,
                            'title' => !empty($image->getAttribute('alt')) ? $this->filenameToTitle($image->getAttribute('alt')) : $this->filenameToTitle($path_parts['filename']),
                            'alt' => !empty($image->getAttribute('alt')) ? $this->filenameToTitle($image->getAttribute('alt')) : $this->filenameToTitle($path_parts['filename']),
                        ];
                    }
                }
                
                // <source />
                /** @todo */
            }
            
            if ( !empty($post['post_excerpt']) && class_exists('DOMDocument') ) {
                $the_excerpt = new \DOMDocument();
                libxml_use_internal_errors(true);
                $the_excerpt->loadHTML('<div>'.$post['post_excerpt'].'</div>');
                libxml_clear_errors();
                libxml_use_internal_errors(false);
                
                $xpath = new \DOMXPath($the_excerpt);
                $img = $xpath->query('//img');
                if ($img->length > 0) {
                    foreach($img as $image) {
                        /** @var \DOMNode $image */
                        $path_parts = pathinfo($image->getAttribute('src'));
                        
                        /** @todo check for and handle srcset as well **/
                        $tmp[$image->getAttribute('src')] = [
                            'id' => null,
                            'mediaFolder.id' => null,
                            'mediaFolder.name' => sanitize_file_name(strtolower($folder_name)), // one folder for each post/page/product
                            'mediaFolder.parent.id' => $mediaFolderParentId, // something like the product images folder
                            'url' => $image->getAttribute('src'),
                            'private' => 0,
                            'type' => null,
                            'title' => !empty($image->getAttribute('alt')) ? $this->filenameToTitle($image->getAttribute('alt')) : $this->filenameToTitle($path_parts['filename']),
                                'alt' => !empty($image->getAttribute('alt')) ? $this->filenameToTitle($image->getAttribute('alt')) : $this->filenameToTitle($path_parts['filename']),
                        ];
                    }
                }
            }
        }

        /**
         * step 4 filter out small images / missing files
         */

        /**
         * step 5 convert to webp and add additional rows (so we import originals & webp version)
         */
        
        
        return array_values($tmp);
    }
}