jQuery(document).ready(function($) {

    // Shortcode copy to clipboard
	$(document).on('click', '.shortcode_copy', function(e) {

        alert('Copy shortcode to clipboard');

		var text = "[gems_bookings]";
		navigator.clipboard.writeText(text).then(function() {
		  console.log('Async: Copying to clipboard was successful!');
		}, function(err) {
		  console.error('Async: Could not copy text: ', err);
		});


	});


});

