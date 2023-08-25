// Wait for the document to be ready before executing jQuery code
jQuery(document).ready(function($) {

	// When the button with the ID 'seo-link-explorer__button' is clicked
	$('#seo-link-explorer__button').click(function() {

		// Create a spinner element to show while the request is being made
		let spinner = $('.spinner');
		spinner.css('visibility', 'visible');
		spinner.css('float', 'left');
		spinner.css('width', '100%');

		// Replace the content of the 'seo-link-explorer__results' element with the spinner
		// This shows the spinner while the AJAX request is being made
		$('#seo-link-explorer__results').html(spinner);

		// Data to be sent with the AJAX request
		let data = {
			action: 'crawl_homepage', // The WordPress AJAX action
			security: seo_link_explorer_params.nonce // Nonce for security
		};

		// Make a POST request to the WordPress AJAX endpoint
		$.post(seo_link_explorer_params.ajax_url, data, function(response) {

			// Replace the content of the 'seo-link-explorer__results' element with the AJAX response
			$('#seo-link-explorer__results').html(response.linked_pages_html);

			// Update the href attribute of the <a> element in sitemap URL with the response value
			$('#seo-link-explorer__sitemap_url a').attr('href', response.sitemap_url);
		});
	});
});
