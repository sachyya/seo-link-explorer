<?php

namespace SEOLinkExplorer;

use SEOLinkExplorer\Setting;
use SEOLinkExplorer\Cron;
use SEOLinkExplorer\Event;
use SEOLinkExplorer\Shortcode;

/**
 * Class Init
 *
 * This class initializes the SEO Link Explorer plugin.
 * It sets up necessary hooks, actions, and methods for the plugin's functionality.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
final class Init {

	/**
	 * Minimum required PHP version for the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.2';

	/**
	 * The singleton instance of the Init class.
	 *
	 * @var Init|null
	 */
	public static ?Init $_instance = null;

	/**
	 * Get the singleton instance of the Init class.
	 *
	 * @return Init|null The Init instance.
	 */
	public static function get_instance(): ?Init {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Init class.
	 *
	 * Initializes the plugin by setting up actions, hooks, and loading required components.
	 */
	public function __construct() {

		// Check for minimum PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Autoload classes
		$this->autoload();

		// Initialize the plugin after WordPress has loaded
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

		// Register activation and deactivation hooks
		register_activation_hook( SEO_LINK_EXPLORER_FILE_PATH, [ $this, 'plugin_activated' ] );
		register_deactivation_hook( SEO_LINK_EXPLORER_FILE_PATH, [ $this, 'plugin_deactivated' ] );
	}

	/**
	 * Perform actions when the plugin is activated.
	 */
	public function plugin_activated() {
		// Set a plugin activation flag in options
		update_option( 'seo_link_explorer_plugin_activate', 'activated' );
	}

	/**
	 * Perform actions when the plugin is deactivated.
	 */
	public function plugin_deactivated() {
		// Remove the plugin activation flag from options
		delete_option( 'seo_link_explorer_plugin_activate' );
	}

	/**
	 * Display an admin notice for the minimum PHP version requirement.
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'seo-link-explorer' ),
			'<strong>' . esc_html__( 'SEO Link Explorer', 'seo-link-explorer' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'seo-link-explorer' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Autoload classes using Composer's autoloader.
	 */
	public function autoload() {
		require_once SEO_LINK_EXPLORER_ROOT_DIR_PATH . '/vendor/autoload.php';
	}

	/**
	 * Initialize the plugin by setting up actions, hooks, and loading class components.
	 */
	public function init_plugin() {
		// Load the plugin's text domain for localization
		add_action( 'init', [ $this, 'load_textdomain' ] );

		// Initialize the Setting class
		Setting ::get_instance();

		// Initialize the Setting class
		Event ::get_instance();

		// Initialize the Cron class
		Cron::get_instance();

		// Initialize the Shortcode class
		Shortcode::get_instance();
	}

	/**
	 * Load the plugin's text domain for localization.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'seo-link-explorer', false, dirname( plugin_basename( SEO_LINK_EXPLORER_FILE_PATH ) ) . '/languages' );
	}

	/**
	 * Check if a specific plugin is active.
	 *
	 * @param string $plugin Plugin basename.
	 * @return bool Whether the plugin is active.
	 */
	public function is_plugin_active( $plugin ): bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}
}

// Initialize the Init class
Init::get_instance();
