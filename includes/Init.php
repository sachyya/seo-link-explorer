<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\Admin;

final class Init {

	const MINIMUM_PHP_VERSION = '7.2';

	public static ?Init $_instance = null;

	/**
	 * @return Init|null
	 */
	public static function get_instance(): ?Init {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * pluginName constructor.
	 */
	public function __construct() {

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );

			return;
		}

		$this->autoload();

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		register_activation_hook( SEO_LINK_EXPLORER_FILE_PATH, [ $this, 'plugin_activated' ] );
		register_deactivation_hook( SEO_LINK_EXPLORER_FILE_PATH, [ $this, 'plugin_deactivated' ] );
	}

	public function plugin_activated() {
		//other plugins can get this option and check if plugin is activated
		update_option( 'seo_link_explorer_plugin_activate', 'activated' );
	}

	public function plugin_deactivated() {
		delete_option( 'seo_link_explorer_plugin_activate' );
	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'seo-link-explorer' ),
			'<strong>' . esc_html__( 'SEO Link Exporer', 'seo-link-explorer' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'seo-link-explorer' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Autoload classes
	 */
	public function autoload() {
		require_once SEO_LINK_EXPLORER_ROOT_DIR_PATH . '/vendor/autoload.php';
	}

	public function init_plugin() {
		add_action( 'init', [ $this, 'load_textdomain' ] );

		Admin::get_instance();
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'seo-link-explorer', false, dirname( plugin_basename( SEO_LINK_EXPLORER_FILE_PATH ) ) . '/languages' );
	}

	/**
	 * @param $plugin
	 *
	 * @return bool
	 */
	public function is_plugin_active( $plugin ): bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}
}

Init::get_instance();
