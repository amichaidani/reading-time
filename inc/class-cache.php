<?php

namespace Reading_Time;

class Cache {

	const POST_META_KEY = '_reading_time';

	/**
	 * @var bool
	 */
	private $is_refreshing_cache = false;

	public function __construct() {

		// Filter initial getter value.
		add_filter( 'reading_time_in_seconds', [ $this, 'maybe_fetch_from_cache' ], 10, 2 );

		// Save cache events.
		add_action( 'wp_after_insert_post', [ $this, 'maybe_save_cache_after_post_save' ], 20, 1 );
		add_action( 'reading_time_calculated_seconds', [ $this, 'save_cache_after_calculate' ], 10, 2 );
		add_action( 'reading_time_settings_updated', [ $this, 'conditionally_update_cache_after_settings_update' ], 10, 2 );
	}

	/**
	 * Maybe fetch cached reading time seconds value for post.
	 *
	 * @param mixed $seconds seconds. Defaults as false.
	 * @param int   $post_id the post id.
	 *
	 * @return int|mixed
	 */
	public function maybe_fetch_from_cache( $seconds, int $post_id ) {

		if ( $post_id && ! $this->is_refreshing_cache ) {
			$meta_value = get_post_meta( $post_id, self::POST_META_KEY, true );

			if ( is_int( $meta_value ) ) {
				$seconds = $meta_value;
			}
		}

		return $seconds;
	}

	/**
	 * After calculated seconds for post - cache it.
	 *
	 * @param int $seconds of reading time.
	 * @param int $post_id the post id.
	 */
	public function save_cache_after_calculate( int $seconds, int $post_id ) {
		update_post_meta( $post_id, self::POST_META_KEY, $seconds );
	}

	/**
	 * Refresh and save cache for post.
	 *
	 * @param int $post_id the post id.
	 */
	public function maybe_save_cache_after_post_save( int $post_id ) {
		$this->is_refreshing_cache = true;
		get_reading_time( $post_id );
	}

	public function conditionally_update_cache_after_settings_update( $old_vals, $new_vals ) {

		$clear_all = false;

		// On words per min change.
		$setting_words_per_min = RT()->settings::SETTING__WORDS_PER_MIN;

		if ( (int) $old_vals[ $setting_words_per_min ] !== (int) $new_vals[ $setting_words_per_min ] ) {
			$clear_all = true;
		}

		// On rounding change.
		$setting_rounding = RT()->settings::SETTING__ROUNDING;

		if ( ! $clear_all && $old_vals[ $setting_rounding ] !== $new_vals[ $setting_rounding ] ) {
			$clear_all = true;
		}

		if ( $clear_all ) {
			$this::clear_posts_cache();

		} else {
			// On post types change.
			$setting_types = RT()->settings::SETTING__POST_TYPES;

			if ( $old_vals[ $setting_types ] !== $new_vals[ $setting_types ] ) {
				$dropped_post_types = array_diff( $old_vals[ $setting_types ], $new_vals[ $setting_types ] );

				if ( ! empty( $dropped_post_types ) ) {
					self::clear_posts_cache( $dropped_post_types );
				}
			}
		}
	}

	/**
	 * Delete cache for all posts.
	 *
	 * @var array $post_types selected posts types, optionally.
	 */
	private static function clear_posts_cache( array $post_types = [] ) {
		$args = [
			'fields'         => 'ids',
			'posts_per_page' => - 1,
			'post_type'      => empty( $post_types ) ? 'any' : $post_types,
			'meta_query'     => [
				[
					'key'     => self::POST_META_KEY,
					'compare' => 'EXISTS',
				],
			],
		];

		$posts_ids = get_posts( $args );

		foreach ( $posts_ids as $post_id ) {
			delete_post_meta( $post_id, self::POST_META_KEY );
		}
	}
}
