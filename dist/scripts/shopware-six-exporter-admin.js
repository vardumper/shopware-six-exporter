(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){
        jQuery('#sw6export a.nav-tab').click(function(e){
            e.preventDefault();
            jQuery(this).parent().find('a').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('.content-tab').removeAttr('hidden').hide();
            jQuery(document.getElementById(jQuery(this).attr('data-id'))).show();
        });
    });

})( jQuery );
