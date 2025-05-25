(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	var woow_init = function(){
		$('a#order_on_whatsapp_cart').click(function(){
			$('.loader').show();
			$.ajax({
				url: ajax_object.ajaxurl,
				type: 'post',
				data:{action: 'get_cart_contents'},
				success: function(data){
					// alert(data);
					window.location.replace(data);
				}
			});
		});
	};

	$(document).ready(function(){
		// console.log('On Load!');
		woow_init();
	});

	$(document).on('updated_cart_totals', function(){
		// console.log('On Cart Update!');
		woow_init();
	});

})( jQuery );
