<?php

use Reading_Time\Reading_Time;

/**
 * Get reading time duration string.
 *
 * @param int $post_id the post id. If no post ID is provided - then current global post object will be used, if present.
 *
 * @return string
 */
if ( ! function_exists( 'get_reading_time' ) ) {
	function get_reading_time( int $post_id = 0 ): string {
		$rt_duration_string = '';

		try {
			$rt                 = new Reading_Time( $post_id );
			$rt_duration_string = $rt->get();
		} catch ( Exception $e ) {
			// Todo: Print exception if there's debug mode? :)
		}

		return esc_html( $rt_duration_string );
	}
}

/**
 * Print a post reading time duration string.
 *
 * @param int $post_id the post id. If no post ID is provided - then current global post object will be used, if present.
 */
if ( ! function_exists( 'the_reading_time' ) ) {
	function the_reading_time( int $post_id = 0 ) {
		echo esc_html( get_reading_time( $post_id ) );
	}
}
