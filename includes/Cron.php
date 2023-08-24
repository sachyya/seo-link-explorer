<?php

namespace SEOLinkExplorer;

/**
 * Class Cron
 *
 * This class handles the administration interface of the SEO Link Explorer plugin.
 * It registers the plugin's submenu, enqueues scripts, and manages AJAX requests.
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
	 * Sets up actions and hooks related to the administration interface.
	 */
	public function __construct() {
	}
}
