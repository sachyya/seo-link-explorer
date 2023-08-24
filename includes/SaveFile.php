<?php

namespace SEOLinkExplorer;

/**
 * Class SaveFile
 *
 * This class handles saving HTML files, such as sitemap and page content, for the SEO Link Explorer plugin.
 * It provides methods to create and manage directories, save HTML content, and retrieve URLs.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class SaveFile {

	/**
	 * The singleton instance of the SaveFile class.
	 *
	 * @var SaveFile|null
	 */
	public static ?SaveFile $_instance = null;

	/**
	 * The folder name for storing plugin-related files.
	 *
	 * @var string
	 */
	public static $folder_name = 'seo-link-explorer';

	/**
	 * Get the singleton instance of the SaveFile class.
	 *
	 * @return SaveFile|null The SaveFile instance.
	 */
	public static function get_instance(): ?SaveFile {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Save HTML content to a file.
	 *
	 * @param string $filename The name of the file.
	 * @param string $content The HTML content to save.
	 */
	public static function save_file( $filename, $content ) {
		$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] );
		$plugin_upload_dir = $uploads_dir . self::$folder_name;
		wp_mkdir_p( $uploads_dir );

		$file_path = trailingslashit( $plugin_upload_dir ) . $filename;
		file_put_contents( $file_path, $content );
	}

	/**
	 * Save sitemap HTML content.
	 *
	 * @param string $content The sitemap HTML content.
	 */
	public static function save_sitemap_html( $content ) {
		$sitemap_html_version = '<!DOCTYPE html>
				<html>
				<head>
					<title>' . esc_html( get_bloginfo() ) . ' - Sitemap</title>
				</head>
				<body>
				' . $content . '
				</body>
				</html>';

		self::save_file( 'sitemap.html', $sitemap_html_version );
	}

	/**
	 * Save homepage HTML content.
	 *
	 * @param string $content The homepage HTML content.
	 */
	public static function save_page_html( $content ) {
		self::save_file( 'homepage.html', $content );
	}

	/**
	 * Get the URL of the sitemap.
	 *
	 * @return string The URL of the sitemap HTML file.
	 */
	public static function get_sitemap_url() {
		$uploads_url = trailingslashit( wp_upload_dir()['baseurl'] );
		return $uploads_url . self::$folder_name . '/sitemap.html';
	}
}
