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

if ( ! defined( 'SEO_LINK_EXPLORER_VERSION' ) ) {
	define( 'SEO_LINK_EXPLORER_VERSION', '1.0.0' );
}

if ( ! defined( 'SEO_LINK_EXPLORER_FILE_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_FILE_PATH', __FILE__ );
}

if ( ! defined( 'SEO_LINK_EXPLORER_ROOT_DIR_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_ROOT_DIR_PATH', DIRNAME( __FILE__ ) );
}

if ( ! defined( 'SEO_LINK_EXPLORER_ROOT_URI_PATH' ) ) {
	define( 'SEO_LINK_EXPLORER_ROOT_URI_PATH', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SEO_LINK_EXPLORER_BASE_FILE' ) ) {
	define( 'SEO_LINK_EXPLORER_BASE_FILE', plugin_basename( __FILE__ ) );
}

function pre( $a ) {
	echo '<pre>';
	print_r( $a );
	echo '</pre>';
}

require_once SEO_LINK_EXPLORER_ROOT_DIR_PATH . '/includes/Init.php';