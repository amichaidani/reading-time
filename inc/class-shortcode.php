<?php

namespace Reading_Time;

/**
 * Shortcode functions.
 */
class Shortcode {

	public function __construct() {
		add_shortcode( 'reading_time', [ $this, 'shortcode_callback' ] );
	}

	/**
	 * Print the reading time with label, wrapped with HTML markup.
	 */
	public function shortcode_callback() {

		$rt_duration = get_reading_time();

		if ( empty( $rt_duration ) ) {
			return;
		}

		$container_classes = apply_filters( 'reading_time_container_classes', 'post-reading-time ' );
		$label_classes     = apply_filters( 'reading_time_label_classes', 'post-reading-time--label ' );
		$duration_classes  = apply_filters( 'reading_time_duration_classes', 'post-reading-time--duration ' );

		?>
		<span class="<?php echo esc_html( trim( $container_classes ) ); ?>">
			<span class="<?php echo esc_html( trim( $label_classes ) ); ?>">
				<?php printf( '%s: ', esc_html__( 'Reading time', 'reading-time' ) ); ?>
			</span>
			<span class="<?php echo esc_html( trim( $duration_classes ) ); ?>">
				<?php echo esc_html( $rt_duration ); ?>
			</span>
		</span>
		<?php
	}
}
