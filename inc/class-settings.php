<?php

namespace Reading_Time;

/**
 * Plugin settings.
 */
class Settings {

	/**
	 * Options name.
	 */
	const OPTIONS_NAME = 'reading_time_options';

	/**
	 * Individual options.
	 */
	const SETTING__WORDS_PER_MIN = 'words_per_min';
	const SETTING__POST_TYPES    = 'post_types';
	const SETTING__ROUNDING      = 'rounding';

	/**
	 * Rounding values.
	 */
	const ROUNDING_UP            = 'round_up';
	const ROUNDING_DOWN          = 'round_down';
	const ROUNDING_HALF_MIN_UP   = 'half_min_up';
	const ROUNDING_HALF_MIN_DOWN = 'half_min_down';

	/**
	 * Settings fetched.
	 *
	 * @var array
	 */
	private $settings;

	public function __construct() {
		add_action( 'update_option', [ $this, 'maybe_trigger_setting_update' ], 10, 3 );
	}

	/**
	 * Maybe trigger custom action on settings option update.
	 *
	 * @param string $option
	 * @param mixed  $old_value
	 * @param mixed  $value
	 */
	public function maybe_trigger_setting_update( string $option, $old_value, $value ) {

		if ( self::OPTIONS_NAME === $option & $old_value !== $value ) {
			do_action( 'reading_time_settings_updated', $old_value, $value );
		}
	}

	/**
	 * Get plugin settings.
	 *
	 * @return array
	 */
	public function get_settings(): array {

		if ( ! isset( $this->settings ) ) {
			$this->settings = get_option( self::OPTIONS_NAME ) ?? [];
		}

		if ( empty( $this->settings[ self::SETTING__WORDS_PER_MIN ] ) ) {
			$this->settings[ self::SETTING__WORDS_PER_MIN ] = 200;
		}

		if ( empty( $this->settings[ self::SETTING__POST_TYPES ] ) ) {
			$this->settings[ self::SETTING__POST_TYPES ] = [ 'post' ];
		}

		if ( empty( $this->settings[ self::SETTING__ROUNDING ] ) ) {
			$this->settings[ self::SETTING__ROUNDING ] = self::ROUNDING_UP;
		}

		return $this->settings;
	}

	/**
	 * Get the option name as stored in WP options table.
	 *
	 * @return string
	 */
	public static function get_option_name(): string {
		return self::OPTIONS_NAME;
	}

	/**
	 * Get available rounding values.
	 *
	 * @return string[]
	 */
	public static function get_rounding_values(): array {
		return [
			self::ROUNDING_UP,
			self::ROUNDING_DOWN,
			self::ROUNDING_HALF_MIN_UP,
			self::ROUNDING_HALF_MIN_DOWN,
		];
	}
}