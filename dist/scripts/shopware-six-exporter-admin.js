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
        jQuery('#shopware-six-exporter ul.subsubsub a').click(function(e){
            e.preventDefault();
            jQuery('#shopware-six-exporter div.content-tab ul.subsubsub li a').removeClass('current');
            jQuery(this).addClass('current');
            jQuery('.settings-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
    });

})( jQuery );