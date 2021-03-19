(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){
        // nav tabs onclick
        jQuery('#shopware-six-exporter a.nav-tab').click(function(e){
            e.preventDefault();
            jQuery(this).parent().find('a').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('.content-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
        
        // settings submenu onclick
        jQuery('#shopware-six-exporter #settings ul.subsubsub a').click(function(e){
            e.preventDefault();
            jQuery('#shopware-six-exporter #settings div.content-tab ul.subsubsub li a').removeClass('current');
            jQuery(this).addClass('current');
            jQuery('#shopware-six-exporter #settings .settings-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
        
        // previews submenu onclick
        jQuery('#shopware-six-exporter #preview ul.subsubsub a').click(function(e){
            e.preventDefault();
            jQuery('#shopware-six-exporter #preview ul.subsubsub li a').removeClass('current');
            jQuery(this).addClass('current');
            jQuery('#shopware-six-exporter #preview .preview-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
    });

})( jQuery );