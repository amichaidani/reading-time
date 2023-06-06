<?php

namespace Reading_Time;

use Exception;
use WP_Post;

/**
 * Class reading time.
 */
class Reading_Time {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * The post we're checking.
	 *
	 * @var array|WP_Post|null
	 */
	private $post;

	/**
	 * This post HTML.
	 *
	 * @var string
	 */
	private $post_html;

	/**
	 *
	 * @var int
	 */
	private $reading_time_secs;

	/**
	 * Provide the constructor with post id.
	 * If no post id is present - the global post will be used, if present.
	 *
	 * @throws Exception
	 */
	public function __construct( int $post_id = 0 ) {

		// Maybe fetch global post id, if none provided.
		if ( ! $post_id ) {
			global $post;

			if ( ! is_a( $post, 'WP_Post' ) ) {
				throw new Exception ( 'Not a valid post.' );
			}

			$post_id    = $post->ID;
			$this->post = $post;
		}

		// 3rd party can provide cached data.
		$reading_time_secs = apply_filters( 'reading_time_in_seconds', false, $post_id );

		// If we got prefetched value, then set it, else - set up the vars needed for calculation process.
		if ( false !== $reading_time_secs && is_int( $reading_time_secs ) ) {

			$this->reading_time_secs = $reading_time_secs;

		} else {

			$post = get_post( $post_id );

			if ( ! is_a( $post, 'WP_Post' ) ) {
				throw new Exception ( 'Not a valid post.' );
			}

			$this->settings = RT()->settings->get_settings();

			if ( ! in_array( $post->post_type, $this->settings[ Settings::SETTING__POST_TYPES ], true ) ) {
				throw new Exception ( 'Not a supported post type.' );
			}

			$this->post = $post;
		}
	}

	/**
	 * Get the reading time duration string.
	 *
	 * @return string
	 */
	public function get(): string {
		$reading_time_string = '';
		$seconds             = 0;

		// If we got a prefetched value, use that.
		if ( isset( $this->reading_time_secs ) && is_int( $this->reading_time_secs ) ) {

			$seconds = $this->reading_time_secs;

		} else {

			if ( $this->post ) {
				$this->post_html = get_the_content( null, false, $this->post );

				if ( is_string( $this->post_html ) && ! empty( $this->post_html ) ) {
					$seconds = $this->calculate_seconds();
					do_action( 'reading_time_calculated_seconds', $seconds, $this->post->ID );
				}
			}
		}

		if ( $seconds ) {
			$reading_time_string = self::format_duration_string( $seconds );
		}

		return $reading_time_string;
	}

	/**
	 * Calculate seconds for current post set in this object, based on plugin settings.
	 *
	 * @return int
	 */
	private function calculate_seconds(): int {

		$words_per_min  = $this->settings[ Settings::SETTING__WORDS_PER_MIN ];
		$round_behavior = $this->settings[ Settings::SETTING__ROUNDING ];
		$total_seconds  = 60 * ( $this->word_count() / $words_per_min );

		//
		switch ( $round_behavior ) {
			case 'round_up':
				$total_seconds = ceil( $total_seconds );
				break;
			case 'round_down':
				$total_seconds = floor( $total_seconds );
				break;
			case 'half_min_up':
				$total_seconds = ceil( $total_seconds / 30 ) * 30;
				break;
			case 'half_min_down':
				// TODO: What about 0 returned?
				$total_seconds = floor( $total_seconds / 30 ) * 30;
				break;
		}

		return $total_seconds;
	}

	/**
	 * Return a word count for this object's post content.
	 *
	 * @return int
	 */
	private function word_count(): int {
		$content_no_tags = wp_strip_all_tags( $this->post_html );

		return count( preg_split( '/\s+/', $content_no_tags ) );
	}

	/**
	 * Format seconds into hh:mm:ss string.
	 *
	 * @param int $seconds
	 *
	 * @return string
	 */
	private static function format_duration_string( int $seconds ): string {
		$hours   = floor( $seconds / 3600 );
		$minutes = floor( ( $seconds % 3600 ) / 60 );
		$seconds = $seconds % 60;

		$dur_string = '';

		if ( $hours > 0 ) {
			$dur_string .= str_pad( $hours, 2, '0', STR_PAD_LEFT ) . ':';
		}

		if ( $hours > 0 || $minutes > 0 ) {
			$dur_string .= str_pad( $minutes, 2, '0', STR_PAD_LEFT ) . ':';
		}

		$dur_string .= str_pad( $seconds, 2, '0', STR_PAD_LEFT );

		return human_readable_duration( ( $dur_string ) );
	}
}
