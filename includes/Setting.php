<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\File;
use SEOLinkExplorer\Event;

/**
 * Class Setting
 *
 * This class handles the administration interface of the SEO Link Explorer plugin.
 * It registers the plugin's submenu, enqueues scripts, and manages AJAX requests.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class Setting {

	/**
	 * The singleton instance of the Setting class.
	 *
	 * @var Setting|null
	 */
	public static ?Setting $_instance = null;

	/**
	 * Get the singleton instance of the Setting class.
	 *
	 * @return Setting|null The Setting instance.
	 */
	public static function get_instance(): ?Setting {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Setting class.
	 *
	 * Sets up actions and hooks related to the administration interface.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_submenu' ) );
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
			<p class="description"><?php echo __( 'The crawl is done automatically once every hour. Click the button if you want to manually crawl the homepage. ', 'seo-link-explorer' ); ?></p>
			<div id="seo-link-explorer__results">
				<?php
				$linked_pages = get_option('seo-link-explorer' );
				if( ! empty( $linked_pages ) ) {
					echo Event::linked_pages_html( $linked_pages );
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

				<p><?php echo __( 'You can use this shortcode to show the sitemap URL', 'seo-link-explorer' ); ?><code>['seo_link_explorer_sitemap_link']</code></p>
			</div>
		</div>
		<?php
	}
}
