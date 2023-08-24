jQuery(document).ready(function($) {
	$('#seo-link-explorer__button').click(function() {
		var data = {
			action: 'crawl_homepage',
			security: seo_link_explorer_params.nonce
		};

		$.post(seo_link_explorer_params.ajax_url, data, function(response) {
			$('#seo-link-explorer__results').html(response.linked_pages_html);

			// Update the href attribute of the <a> element in sitemap URL
			$('#seo-link-explorer__sitemap_url a').attr('href', response.sitemap_url);
		});
	});
});
