<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\File;

/**
 * Class Shortcode
 *
 * This class handles the shortcode functionality for generating a sitemap link.
 *
 * @package SEOLinkExplorer
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

		// If the instance doesn't exist, create and return it
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
		// Register the shortcode named 'seo_link_explorer_sitemap_link'
		// and associate it with the 'sitemap_shortcode' method of this class
		add_shortcode( 'seo_link_explorer_sitemap_link', array( $this, 'sitemap_shortcode' ) );
	}

	/**
	 * Callback function for the 'seo_link_explorer_sitemap_link' shortcode.
	 * Outputs a link to the sitemap.
	 *
	 * @return string HTML link to the sitemap.
	 */
	public function sitemap_shortcode() {
		// Get the sitemap URL using the File class from the SEOLinkExplorer namespace
		$sitemap_url = File::get_sitemap_url();

		// Return the formatted HTML link to the sitemap
		return '<a href="' . esc_url( $sitemap_url ) . '">' . __ ('View Sitemap', 'seo-link-explorer' ) . '</a>';
	}
}
