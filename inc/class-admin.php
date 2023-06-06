<?php

namespace Reading_Time;

/**
 * Admin hooks and settings page.
 */
class Admin {

	/**
	 * Holds the values to be used in the fields callbacks.
	 */
	private $settings;

	/**
	 * Settings page name.
	 */
	const PAGE_SLUG = 'reading_time_settings_page';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
	}

	/**
	 * Add custom settings page.
	 */
	public function add_options_page() {
		add_options_page(
			__( 'Reading Time', 'reading-time' ),
			__( 'Reading Time', 'reading-time' ),
			'manage_options',
			self::PAGE_SLUG,
			[
				$this,
				'settings_page',
			]
		);
	}

	/**
	 * Options page callback
	 */
	public function settings_page() {
		// Set class property
		$this->settings = RT()->settings->get_settings();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Reading Time Settings', 'reading-time' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'reading_time_options_group' );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings fields.
	 */
	public function register_settings() {
		register_setting(
			'reading_time_options_group',
			RT()->settings::OPTIONS_NAME,
			[ $this, 'sanitize' ]
		);

		add_settings_section(
			'reading_time_settings_general',
			__( 'General Settings', 'reading-time' ),
			'',
			self::PAGE_SLUG
		);

		add_settings_field(
			RT()->settings::SETTING__WORDS_PER_MIN,
			__( 'Words per minute', 'reading-time' ),
			[ $this, 'words_per_min_callback' ],
			self::PAGE_SLUG,
			'reading_time_settings_general'
		);

		add_settings_field(
			RT()->settings::SETTING__POST_TYPES,
			__( 'Supported post types', 'reading-time' ),
			[ $this, 'post_types_callback' ],
			self::PAGE_SLUG,
			'reading_time_settings_general'
		);

		add_settings_field(
			RT()->settings::SETTING__ROUNDING,
			__( 'Rounding behaviour', 'reading-time' ),
			[ $this, 'rounding_callback' ],
			self::PAGE_SLUG,
			'reading_time_settings_general'
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function words_per_min_callback() {
		$setting_name = RT()->settings::SETTING__WORDS_PER_MIN;
		$option_val   = $this->settings[ $setting_name ] ?? 200;

		printf(
			'<input type="text" id="%s" name="%s" value="%s" />',
			$setting_name,
			self::get_input_name( $setting_name ),
			esc_attr( $option_val )
		);
	}

	/**
	 * Supported post types setting field markup.
	 */
	public function post_types_callback() {

		$setting_name = RT()->settings::SETTING__POST_TYPES;

		$post_types          = get_post_types( [], 'objects' );
		$selected_post_types = $this->settings[ $setting_name ] ?? [ 'post' ];

		printf(
			'<select multiple id="%s" name="%s[]" />',
			$setting_name,
			self::get_input_name( $setting_name )
		);

		foreach ( $post_types as $post_type ) {

			if ( ! $post_type->public ) {
				continue;
			}

			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $post_type->name ),
				in_array( $post_type->name, $selected_post_types ) ? 'selected' : '',
				esc_html( $post_type->label ),
			);
		}

		echo '</select>';
	}

	/**
	 * Rounding setting field markup.
	 */
	public function rounding_callback() {

		$settings_class = RT()->settings;
		$setting_name   = $settings_class::SETTING__ROUNDING;

		$option_values = [
			$settings_class::ROUNDING_UP            => __( 'Round Up', 'reading-time' ),
			$settings_class::ROUNDING_DOWN          => __( 'Round Down', 'reading-time' ),
			$settings_class::ROUNDING_HALF_MIN_UP   => __( 'Round up in 1⁄2 minute steps', 'reading-time' ),
			$settings_class::ROUNDING_HALF_MIN_DOWN => __( 'Round down in 1⁄2 minute steps', 'reading-time' ),
		];

		$selected_value = $this->settings[ $setting_name ] ?? $option_values[0];

		printf(
			'<select id="%s" name="%s">',
			esc_attr( $setting_name ),
			self::get_input_name( $setting_name )
		);

		foreach ( $option_values as $_option_val => $_option_label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $_option_val ),
				$selected_value === $_option_val ? 'selected' : '',
				esc_html( $_option_label )
			);
		}
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( array $input ): array {

		$settings_class = RT()->settings;

		foreach ( $input as $key => $val ) {

			switch ( $key ) {

				case $settings_class::SETTING__WORDS_PER_MIN:
					$input[ $key ] = absint( $val );
					break;

				case $settings_class::SETTING__POST_TYPES:
					if ( ! is_array( $val ) ) {
						$input[ $key ] = [ 'post' ];
					}

					foreach ( $val as $idx => $post_type_submitted ) {

						$post_types_registered = get_post_types();

						if ( ! in_array( $post_type_submitted, $post_types_registered, true ) ) {
							unset( $input[ $key ][ $idx ] );
						}
					}
					break;

				case $settings_class::SETTING__ROUNDING:
					$rounding_available_vals = $settings_class::get_rounding_values();

					if ( ! in_array( $val, $rounding_available_vals, true ) ) {
						$input[ $key ] = $rounding_available_vals[0];
					}
					break;

			}
		}

		return $input;
	}

	/**
	 * Get name attribute for input tags, based on selected setting.
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	private static function get_input_name( string $setting ): string {
		return RT()->settings::get_option_name() . '[' . $setting . ']';
	}

}

