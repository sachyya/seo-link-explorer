<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\File;

/**
 * Class Admin
 *
 * This class handles the administration interface of the SEO Link Explorer plugin.
 * It registers the plugin's submenu, enqueues scripts, and manages AJAX requests.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class Admin {

	/**
	 * The singleton instance of the Admin class.
	 *
	 * @var Admin|null
	 */
	public static ?Admin $_instance = null;

	/**
	 * Get the singleton instance of the Admin class.
	 *
	 * @return Admin|null The Admin instance.
	 */
	public static function get_instance(): ?Admin {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Admin class.
	 *
	 * Sets up actions and hooks related to the administration interface.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_submenu' ) );
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts') );

		// Add AJAX actions for crawling the homepage
		add_action('wp_ajax_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
		add_action('wp_ajax_nopriv_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
	}

	/**
	 * Register the plugin's submenu page under the Settings menu.
	 */
	public function register_submenu() {
		add_submenu_page(
			'options-general.php',
			__( 'SEO Link Explorer', 'seo-link-explorer' ),
			__( 'SEO Link Explorer', 'seo-link-explorer' ),
			'manage_options',
			'seo-link-explorer',
			array( $this, 'output_page_content' )
		);
	}

	/**
	 * Output the content of the plugin's administration page.
	 */
	public  function output_page_content() { ?>
		<div class="wrap seo-link-explorer">
			<h1><?php echo __( 'SEO Link Explorer', 'seo-link-explorer' ); ?></h1>
			<p class="submit">
				<input type="submit" name="submit" id="seo-link-explorer__button" class="button button-primary"
					   value="<?php echo __( 'Crawl Homepage', 'seo-link-explorer' ); ?>">
			</p>
			<div id="seo-link-explorer__results">
				<?php
				$linked_pages = get_option('seo-link-explorer' );
				if( ! empty( $linked_pages ) ) {
					echo $this->linked_pages_html( $linked_pages );
				}
				?>
			</div>
			<div id="seo-link-explorer__sitemap_url">
				<?php
				$sitemap_url = File ::get_sitemap_url();
				if( $sitemap_url ) {
					echo sprintf(
						__( 'Your sitemap is available %1$shere%2$s' , 'seo-link-explorer' ) ,
						'<a target="_blank" href="' . esc_url( $sitemap_url ) . '">' ,
						'</a>'
					);
				} else {
					// Add a placeholder text and html to show the link
					echo sprintf(
						__( 'Your sitemap will be available %1$shere%2$s once you crawl it.' , 'seo-link-explorer' ) ,
						'<a target="_blank" href="#">' ,
						'</a>'
					);
				}
				?>
			</div>
		</div>
		<?php
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
	public function linked_pages_html( $linked_pages ) {
		$links_content = '<h2>' . __( 'Linked Pages:', 'seo-link-explorer' ) . '</h2>';
		$links_content .= '<ul>';
		foreach ( $linked_pages as $link ) {
			$links_content .= '<li><a href="' . esc_url( $link ) . '">' . esc_html( $link ) . '</a></li>';
		}
		$links_content .= '</ul>';

		return $links_content;
	}
}
