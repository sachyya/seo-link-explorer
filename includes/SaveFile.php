<?php

namespace SEOLinkExplorer;
class SaveFile {

	public static ?SaveFile $_instance = null;

	public $folder_name = 'seo-link-explorer';
	/**
	 * @return SaveFile|null
	 */
	public static function get_instance(): ?SaveFile {
		if ( is_null( self ::$_instance ) ) {
			self ::$_instance = new self();
		}

		return self:: $_instance;
	}

	public function save_file( $filename, $content ) {
		$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] );
		$plugin_upload_dir = $uploads_dir . $this->folder_name;
		wp_mkdir_p( $uploads_dir );

		$file_path = trailingslashit( $plugin_upload_dir ) . $filename;
		file_put_contents( $file_path, $content );
	}

	public function save_page_html() {

	}

	public function save_sitemap() {

	}
}
