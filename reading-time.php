<?php
/*
 * Plugin Name:       Reading Time (Amichai)
 * Description:       Calculate and show reading time estimated for a post.
 * Version:           1.0
 * Requires PHP:      7.3
 * Author:            Amichai Dani
 * Author URI:        https://github.com/amichaidani
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reading-time
 * Domain Path:       /languages
 */

// Block direct access to the file via url.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Reading_Time\Plugin;

// Define plugins constants.
const READING_TIME_VER = '1.0';

define( 'READING_TIME_PATH', plugin_dir_path( __FILE__ ) );
define( 'READING_TIME_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin i18n and l10n capabilities.
 */
function reading_time_load_text_domain() {
//	TODO: Create .po .mo files!
	load_plugin_textdomain( 'reading-time', false, READING_TIME_PATH . '/languages' );
}

add_action( 'plugins_loaded', 'reading_time_load_text_domain' );

// Check PHP version.
if ( ! version_compare( PHP_VERSION, '7.3', '>=' ) ) {

// TODO: Add admin notice.

} else {

	if ( ! function_exists( 'RT' ) ) {

		include_once 'inc/class-plugin.php';

		function RT(): Plugin {
			return Plugin::get_instance();
		}

		RT();

	} else {
		// TODO: Whoops. That should trigger an admin notice.
	}
}
