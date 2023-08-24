<?php
namespace SEOLinkExplorer;

use SEOLinkExplorer\Setting;

/**
 * Class Cron
 *
 * This class handles scheduling and executing the crawling process on a predefined time interval.
 *
 * @package SEOLinkExplorer
 * @since 1.0.0
 */
class Cron {

	/**
	 * The singleton instance of the Cron class.
	 *
	 * @var Cron|null
	 */
	public static ?Cron $_instance = null;

	/**
	 * Get the singleton instance of the Cron class.
	 *
	 * @return Cron|null The Cron instance.
	 */
	public static function get_instance(): ?Cron {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * Constructor for the Cron class.
	 *
	 * Sets up actions and hooks for scheduling and executing the crawling process.
	 */
	public function __construct() {
		add_filter('cron_schedules', array( $this, 'add_custom_cron_schedule' ) );
		add_action('wp', array( $this, 'schedule_crawler_execution') );
		add_action('seo_link_explorer_event', array( $this, 'run_crawler_on_schedule' ) );
	}

	/**
	 * Add a custom cron schedule for running the crawler.
	 *
	 * @param array $schedules Existing cron schedules.
	 * @return array Modified cron schedules.
	 */
	public function add_custom_cron_schedule( $schedules ) {
		$schedules['seo_link_explorer_every_hour'] = array(
			'interval' => HOUR_IN_SECONDS,
			'display' => __('SEO Link Explorer Every Hour', 'seo-link-explorer' )
		);
		return $schedules;
	}

	/**
	 * Schedule the crawler execution at the defined interval.
	 */
	public function schedule_crawler_execution() {
		if ( ! wp_next_scheduled('seo_link_explorer_event' ) ) {
			wp_schedule_event( time(), 'seo_link_explorer_every_hour', 'seo_link_explorer_event' );
		}
	}

	/**
	 * Execute the crawler on the defined schedule.
	 */
	public function run_crawler_on_schedule() {
		$admin_instance = Setting ::get_instance();
		$admin_instance->display_linked_pages();
	}
}
