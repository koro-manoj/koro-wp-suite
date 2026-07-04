<?php
/**
 * Service custom post type and meta.
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register bookable service post type.
 */
final class Koro_Booking_Post_Types {

	public static function register(): void {
		register_post_type(
			'koro_service',
			array(
				'labels'              => array(
					'name'          => __( 'Services', 'koro-booking' ),
					'singular_name' => __( 'Service', 'koro-booking' ),
					'add_new_item'  => __( 'Add Service', 'koro-booking' ),
					'edit_item'     => __( 'Edit Service', 'koro-booking' ),
				),
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => array( 'slug' => 'services' ),
				'menu_icon'           => 'dashicons-calendar-alt',
				'show_in_rest'        => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'capability_type'     => 'koro_service',
				'map_meta_cap'        => true,
			)
		);
	}

	public static function register_meta(): void {
		register_post_meta(
			'koro_service',
			'_koro_price',
			array(
				'type'              => 'number',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => function ( $value ) {
					return max( 0, (float) $value );
				},
				'auth_callback'     => function () {
					return current_user_can( 'edit_koro_services' );
				},
			)
		);

		register_post_meta(
			'koro_service',
			'_koro_duration',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => function ( $value ) {
					return max( 0, (int) $value );
				},
				'auth_callback'     => function () {
					return current_user_can( 'edit_koro_services' );
				},
			)
		);
	}

	public static function add_meta_boxes(): void {
		add_meta_box(
			'koro_service_details',
			__( 'Service Details', 'koro-booking' ),
			array( self::class, 'render_meta_box' ),
			'koro_service',
			'side',
			'high'
		);
	}

	public static function render_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'koro_service_meta', 'koro_service_meta_nonce' );

		$price    = get_post_meta( $post->ID, '_koro_price', true );
		$duration = get_post_meta( $post->ID, '_koro_duration', true );
		?>
		<p>
			<label for="koro_price"><strong><?php esc_html_e( 'Price (USD)', 'koro-booking' ); ?></strong></label>
			<input type="number" step="0.01" min="0" class="widefat" name="koro_price" id="koro_price" value="<?php echo esc_attr( (string) $price ); ?>">
		</p>
		<p>
			<label for="koro_duration"><strong><?php esc_html_e( 'Duration (minutes)', 'koro-booking' ); ?></strong></label>
			<input type="number" step="1" min="0" class="widefat" name="koro_duration" id="koro_duration" value="<?php echo esc_attr( (string) $duration ); ?>">
		</p>
		<?php
	}

	public static function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['koro_service_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koro_service_meta_nonce'] ) ), 'koro_service_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['koro_price'] ) ) {
			update_post_meta( $post_id, '_koro_price', max( 0, (float) wp_unslash( $_POST['koro_price'] ) ) );
		}

		if ( isset( $_POST['koro_duration'] ) ) {
			update_post_meta( $post_id, '_koro_duration', max( 0, (int) wp_unslash( $_POST['koro_duration'] ) ) );
		}
	}
}
