<?php

namespace SEOLinkExplorer;
class Admin {

	public static ?Admin $_instance = null;

	/**
	 * @return Admin|null
	 */
	public static function get_instance(): ?Admin {
		if ( is_null( self ::$_instance ) ) {
			self ::$_instance = new self();
		}

		return self:: $_instance;
	}

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_submenu' ) );
		add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts') );

		// Add AJAX action
		add_action('wp_ajax_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
		add_action('wp_ajax_nopriv_crawl_homepage', array( $this, 'ajax_crawl_homepage' ) );
	}

	public function register_submenu() {
		add_submenu_page(
			'options-general.php', __( 'SEO Link Explorer', 'seo-link-explorer' ), __( 'SEO Link Explorer', 'seo-link-explorer' ), 'manage_options', 'seo-link-explorer', array( $this, 'output_page_content' )
		);
	}

	public  function output_page_content() { ?>
		<div class="wrap seo-link-explorer">
			<h1><?php echo __( 'SEO Link Explorer', 'seo-link-explorer' ); ?></h1>
			<p class="submit"><input type="submit" name="submit" id="seo-link-explorer__button" class="button button-primary" value="<?php echo __( 'Crawl Homepage', 'seo-link-explorer' ); ?>"></p>

			<div id="seo-link-explorer__results">
				<?php
				$linked_pages = get_option('seo-link-explorer' );
				if ( ! empty( $linked_pages ) ) {
					echo '<h2>Linked Pages:</h2>';
					echo '<ul>';
						foreach ( $linked_pages as $link ) {
						echo '<li><a href="' . esc_url( $link ) . '">' . esc_html( $link ) . '</a></li>';
						}
						echo '</ul>';
					}
				?>
			</div>
			<div>Your sitemap is available <a href="" target="_blank">here</a></div>
		</div>
		<?php
	}

	public function enqueue_scripts( $hook ) {
		// Enqueue JS only on this page. If not, bail out.
		if( 'settings_page_seo-link-explorer' != $hook ) {
			return;
		}

		wp_enqueue_script('seo-link-explorer', SEO_LINK_EXPLORER_ROOT_URI_PATH . 'assets/explorer.js', array('jquery'), SEO_LINK_EXPLORER_VERSION, true );

		// Localize data for JavaScript
		wp_localize_script('seo-link-explorer', 'seo_link_explorer_params', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('seo-link-explorer-nonce')
		));
	}

	public function ajax_crawl_homepage() {
		check_ajax_referer('seo-link-explorer-nonce', 'security');

		ob_start(); // Start output buffering
		$this->display_linked_pages();
		$response = ob_get_clean(); // Get and clean the output buffer

		echo $response;
		wp_die();
	}
	public function display_linked_pages() {
		$homepage_url     = home_url();
		$homepage_content = wp_remote_get( $homepage_url )[ 'body' ];

		if ( ! empty( $homepage_content ) ) {
			$dom = new \DOMDocument();
			@$dom -> loadHTML( $homepage_content ); // Suppress errors caused by invalid HTML
			$links        = $dom -> getElementsByTagName( 'a' );
			$linked_pages = array ();

			foreach ( $links as $link ) {
				$linked_pages[] = $link -> getAttribute( 'href' );
			}

			if ( ! empty( $linked_pages ) ) {

				$links_content = '<h2>Linked Pages:</h2>';
				$links_content .= '<ul>';
				foreach ( $linked_pages as $link ) {
					$links_content .= '<li><a href="' . esc_url( $link ) . '">' . esc_html( $link ) . '</a></li>';
				}
				$links_content .= '</ul>';

				echo $links_content;

				delete_option( 'seo-link-explorer' );
				update_option( 'seo-link-explorer', $linked_pages );

				//Save to a file
				$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'seo-link-explorer';
				wp_mkdir_p( $uploads_dir );

				$html_content = '<!DOCTYPE html>
				<html>
				<head>
					<title>' . get_bloginfo() . ' - Sitemap</title>
				</head>
				<body>
				' . $links_content . '
				</body>
				</html>';

				$file_path = $uploads_dir . '/sitemap.html';
				file_put_contents( $file_path, $html_content );

			} else {
				echo 'No linked pages found on the homepage.';
			}
		} else {
			echo 'Error fetching homepage content.';
		}
	}
}
