<?php
/*
 * Plugin Name:       SEO Link Exporer
 * Plugin URI:        #
 * Description:       Plugin to see how the site's web pages are linked to home page so that you can manually search for ways to improve my SEO rankings.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            sachyya-sachet
 * Author URI:        https://sachyya.github.io/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        #
 * Text Domain:       seo-link-explorer
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( 'Script Kiddies Go Away' );

/**
 * Define constants for SEO Link Explorer plugin.
 *
 * These constants define essential values used throughout the plugin.
 * They provide version information, file paths, and URLs for easy reference.
 *
 * @since 1.0.0
 */

// Plugin version
if ( ! defined( 'SEO_LINK_EXPLORER_VERSION' ) ) {
	define( 'SEO_LINK_EXPLORER_VERSION', '1.0.0' );
}

// Plugin main file path
if ( ! defined( 'SEO_LINK_EXPLORER_FILE_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_FILE_PATH', __FILE__ );
}

// Plugin root directory path
if ( ! defined( 'SEO_LINK_EXPLORER_ROOT_DIR_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_ROOT_DIR_PATH', DIRNAME( __FILE__ ) );
}

// Plugin root URI path
if ( ! defined( 'SEO_LINK_EXPLORER_ROOT_URI_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_ROOT_URI_PATH', plugin_dir_url( __FILE__ ) );
}

// Plugin base file
if ( ! defined( 'SEO_LINK_EXPLORER_BASE_FILE' ) ) {
	define( 'SEO_LINK_EXPLORER_BASE_FILE', plugin_basename( __FILE__ ) );
}

// Debugger function
function pre( $a ) {
	echo '<pre>';
	print_r( $a );
	echo '</pre>';
}

/**
 * Require the main initialization file of the SEO Link Explorer plugin.
 *
 * This line includes the 'Init.php' file located in the plugin's 'includes' directory.
 * The 'Init.php' file is responsible for setting up and initializing various components of the plugin.
 *
 * @since 1.0.0
 */
require_once SEO_LINK_EXPLORER_ROOT_DIR_PATH . '/includes/Init.php';
