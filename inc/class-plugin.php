<?php

namespace Reading_Time;

/**
 * General singleton class of this plugin.
 */
class Plugin {

	/**
	 * @var null|Plugin
	 */
	private static $instance = null;

	/**
	 * @var Admin
	 */
	public $admin;

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Cache
	 */
	public $cache;

	/**
	 * @var Shortcode
	 */
	private $shortcode;

	private function __construct() {
		$this->include_files();
	}

	public static function get_instance(): Plugin {
		if ( self::$instance === null ) {
			self::$instance = new Plugin();
		}

		return self::$instance;
	}

	/**
	 * Include files and set class vars.
	 */
	private function include_files() {

		require_once READING_TIME_PATH . 'inc/class-settings.php';
		$this->settings = new Settings();

		require_once READING_TIME_PATH . 'inc/class-cache.php';
		$this->cache = new Cache();

		require_once READING_TIME_PATH . 'inc/class-shortcode.php';
		$this->shortcode = new Shortcode();

		if ( is_admin() && ! wp_doing_ajax() ) {
			require_once READING_TIME_PATH . 'inc/class-admin.php';
			$this->admin = new Admin();
		}

		require_once READING_TIME_PATH . 'inc/class-reading-time.php';

		require_once READING_TIME_PATH . 'inc/reading-time-functions.php';
	}
}

