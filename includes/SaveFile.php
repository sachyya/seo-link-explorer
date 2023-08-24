<?php

namespace SEOLinkExplorer;
class SaveFile {

	public static ?SaveFile $_instance = null;

	public static $folder_name = 'seo-link-explorer';

	public static function get_instance(): ?SaveFile {
		if ( is_null( self ::$_instance ) ) {
			self ::$_instance = new self();
		}

		return self:: $_instance;
	}

	public static function save_file( $filename, $content ) {
		$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] );
		$plugin_upload_dir = $uploads_dir . self::$folder_name;
		wp_mkdir_p( $uploads_dir );

		$file_path = trailingslashit( $plugin_upload_dir ) . $filename;
		file_put_contents( $file_path, $content );
	}

	public static function save_page_html( $content ) {
		$homepage_html_version = '<!DOCTYPE html>
				<html>
				<head>
					<title>' . get_bloginfo() . ' - Sitemap</title>
				</head>
				<body>
				' . $content . '
				</body>
				</html>';

		self::save_file( 'homepage.html', $homepage_html_version );
	}

	public static function save_sitemap_html( $content ) {
		self::save_file( 'sitemap.html', $content );
	}

	public static function get_sitemap_url() {
		$uploads_url = trailingslashit( wp_upload_dir()['baseurl'] );
		return $uploads_url . self::$folder_name . '/sitemap.html';
	}
}
