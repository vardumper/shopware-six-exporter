(function( $ ) {
	'use strict';

	$(document).ready(function(){
        $('#shopware-six-exporter a.nav-tab').click(function(e){
            e.preventDefault();
            $(this).parent().find('a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.content-tab').removeAttr('hidden').hide();
            $(document.getElementById($(this).attr('data-id'))).show();
        });
    });

})( jQuery );