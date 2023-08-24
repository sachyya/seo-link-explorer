<?php

namespace SEOLinkExplorer;

/**
 * Class File
 *
 * This class handles saving HTML files, such as sitemap and page content, for the SEO Link Explorer plugin.
 * It provides methods to create and manage directories, save HTML content, and retrieve URLs.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class File {

	/**
	 * The singleton instance of the File class.
	 *
	 * @var File|null
	 */
	public static ?File $_instance = null;

	/**
	 * The folder name for storing plugin-related files.
	 *
	 * @var string
	 */
	public static $folder_name = 'seo-link-explorer';

	/**
	 * Get the singleton instance of the File class.
	 *
	 * @return File|null The File instance.
	 */
	public static function get_instance(): ?File {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	public static function get_plugin_upload_dir() {
		$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] );
		return $uploads_dir . self::$folder_name;
	}

	public static function get_plugin_upload_url() {
		$uploads_dir = trailingslashit( wp_upload_dir()['baseurl'] );
		return $uploads_dir . self::$folder_name;
	}

	public static function get_plugin_upload_files() {
		$plugin_upload_dir = self::get_plugin_upload_dir();

		return scandir( $plugin_upload_dir );
	}
	/**
	 * Save HTML content to a file.
	 *
	 * @param string $filename The name of the file.
	 * @param string $content The HTML content to save.
	 */
	public static function save_file( $filename, $content ) {
		$plugin_upload_dir = self::get_plugin_upload_dir();
		wp_mkdir_p( $plugin_upload_dir );

		$file_path = trailingslashit( $plugin_upload_dir ) . $filename;
		file_put_contents( $file_path, $content );
	}

	public static function delete_files() {
		$files = self::get_plugin_upload_files();
		foreach ( $files as $filename ) {
			// Check if the filename starts with "sitemap-" or "homepage-"
			if ( strpos( $filename, 'sitemap-' ) === 0 || strpos( $filename, 'homepage-' ) === 0 ) {
				$file_path = self::get_plugin_upload_dir() . '/' . $filename;

				unlink( $file_path );
			}
		}
	}
	/**
	 * Save sitemap HTML content.
	 *
	 * @param string $content The sitemap HTML content.
	 */
	public static function save_sitemap_html( $content ){
		$filename = 'sitemap-' . date('YmdHis') .'.html';
		$sitemap_html_version = '<!DOCTYPE html>
				<html>
				<head>
					<title>' . esc_html( get_bloginfo() ) . ' - Sitemap</title>
				</head>
				<body>
				' . $content . '
				</body>
				</html>';

		self::save_file( $filename, $sitemap_html_version );

		// Return filename to be saved to option table
		return $filename;
	}

	/**
	 * Save homepage HTML content.
	 *
	 * @param string $content The homepage HTML content.
	 */
	public static function save_page_html( $content ) {
		$filename = 'homepage-' . date('YmdHis') .'.html';
		self::save_file( $filename, $content );
	}

	/**
	 * Get the URL of the sitemap.
	 *
	 * @return string The URL of the sitemap HTML file.
	 */
	public static function get_sitemap_url() {
		$uploads_url = self::get_plugin_upload_url();
		$filename = get_option( 'seo-link-explorer-sitemap-filename' );

		if( ! $filename ) {
			return false;
		}

		return $uploads_url . '/' . $filename;
	}
}
