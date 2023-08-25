<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\File;

/**
 * Class Shortcode
 *
 * This class handles the administration interface of the SEO Link Explorer plugin.
 * It registers the plugin's submenu, enqueues scripts, and manages AJAX requests.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class Shortcode {

	/**
	 * The singleton instance of the Shortcode class.
	 *
	 * @var Shortcode|null
	 */
	public static $_instance = null;

	/**
	 * Get the singleton instance of the Shortcode class.
	 *
	 * @return Shortcode|null The Shortcode instance.
	 */
	public static function get_instance(): ?Shortcode {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Shortcode class.
	 *
	 * Sets up actions and hooks related to the administration interface.
	 */
	public function __construct() {
		add_shortcode( 'seo_link_explorer_sitemap_link', array( $this, 'sitemap_shortcode' ) );
	}

	public function sitemap_shortcode() {
		$sitemap_url = File::get_sitemap_url();

		return '<a href="' . esc_url( $sitemap_url ) . '">' . __ ('View Sitemap', 'seo-link-explorer' ) . '</a>';
	}
}
