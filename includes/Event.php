<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\File;

/**
 * Class Event
 *
 * This class handles the administration interface of the SEO Link Explorer plugin.
 * It registers the plugin's submenu, enqueues scripts, and manages AJAX requests.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class Event {

	/**
	 * The singleton instance of the Event class.
	 *
	 * @var Event|null
	 */
	public static ?Event $_instance = null;

	/**
	 * Get the singleton instance of the Event class.
	 *
	 * @return Event|null The Event instance.
	 */
	public static function get_instance(): ?Event {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Event class.
	 *
	 * Sets up actions and hooks related to the administration interface.
	 */
	public function __construct() {
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts') );

		// Add AJAX actions for crawling the homepage
		add_action('wp_ajax_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
		add_action('wp_ajax_nopriv_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
	}

	/**
	 * Enqueue necessary scripts and styles for the administration page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		// Enqueue JS only on the plugin's administration submenu page. If not, bail out.
		if( 'settings_page_seo-link-explorer' != $hook ) {
			return;
		}

		wp_enqueue_script('seo-link-explorer', SEO_LINK_EXPLORER_ROOT_URI_PATH . 'assets/explorer.js', array('jquery'), SEO_LINK_EXPLORER_VERSION, true );

		// Localize data for JavaScript
		wp_localize_script('seo-link-explorer', 'seo_link_explorer_params', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('seo-link-explorer-nonce'),
			'sitemap_filename' => File ::get_sitemap_url()
		));
	}

	/**
	 * Handle AJAX request for crawling the homepage.
	 */
	public function ajax_crawl_homepage() {
		check_ajax_referer('seo-link-explorer-nonce', 'security');

		ob_start(); // Start output buffering
		$this->display_linked_pages();
		$linked_pages_html = ob_get_clean(); // Get and clean the output buffer
		$data = array(
			'linked_pages_html' => $linked_pages_html,
			'sitemap_url' => File ::get_sitemap_url() // Send sitemap_url too to update on ajax call
		);
		wp_send_json($data);
		wp_die();
	}

	/**
	 * Display linked pages and perform crawling.
	 */
	public function display_linked_pages() {
		$homepage_url     = home_url();
		$homepage_content = wp_remote_get( $homepage_url )[ 'body' ];

		if ( ! empty( $homepage_content ) ) {
			$dom = new \DOMDocument();
			$dom -> loadHTML( $homepage_content ); // Suppress errors caused by invalid HTML

			$homepage_html_version = $dom->saveHTML();

			$links        = $dom -> getElementsByTagName( 'a' );
			$linked_pages = array ();

			foreach ( $links as $link ) {
				$linked_pages[] = $link -> getAttribute( 'href' );
			}

			if ( ! empty( $linked_pages ) ) {

				$links_content = $this->linked_pages_html( $linked_pages );
				echo $links_content;

				delete_option( 'seo-link-explorer' );
				update_option( 'seo-link-explorer', $linked_pages );

				// Delete files before saving
				File ::delete_files();
				File ::save_page_html( $homepage_html_version );
				$sitemap_filename = File ::save_sitemap_html( $links_content );

				// Save the generate sitemap filename to be used in ajax call
				update_option( 'seo-link-explorer-sitemap-filename', $sitemap_filename );
			} else {
				echo __( 'No linked pages found on the homepage.', 'seo-link-explorer' );
			}
		} else {
			echo __( 'Error fetching homepage content.', 'seo-link-explorer' );
		}
	}

	/**
	 * Generate HTML for linked pages.
	 *
	 * @param array $linked_pages Array of linked pages.
	 * @return string Generated HTML.
	 */
	public static function linked_pages_html( $linked_pages ) {
		$links_content = '<h2>' . __( 'Linked Pages:', 'seo-link-explorer' ) . '</h2>';
		$links_content .= '<ul>';
		foreach ( $linked_pages as $link ) {
			$links_content .= '<li><a href="' . esc_url( $link ) . '">' . esc_html( $link ) . '</a></li>';
		}
		$links_content .= '</ul>';

		return $links_content;
	}
}
