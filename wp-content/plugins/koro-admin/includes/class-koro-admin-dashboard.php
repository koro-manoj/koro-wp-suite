<?php
/**
 * Custom admin dashboard pages.
 *
 * @package Koro_Admin
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Koro dashboard and widgets.
 */
final class Koro_Admin_Dashboard {

	public static function register_menu(): void {
		add_menu_page(
			__( 'Koro Suite', 'koro-admin' ),
			__( 'Koro Suite', 'koro-admin' ),
			'manage_koro_dashboard',
			'koro-dashboard',
			array( self::class, 'render_dashboard' ),
			'dashicons-chart-area',
			3
		);

		add_submenu_page(
			'koro-dashboard',
			__( 'Overview', 'koro-admin' ),
			__( 'Overview', 'koro-admin' ),
			'manage_koro_dashboard',
			'koro-dashboard',
			array( self::class, 'render_dashboard' )
		);

		add_submenu_page(
			'koro-dashboard',
			__( 'Settings', 'koro-admin' ),
			__( 'Settings', 'koro-admin' ),
			'manage_koro_settings',
			'koro-settings',
			array( self::class, 'render_settings' )
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( ! str_contains( $hook, 'koro-' ) ) {
			return;
		}

		wp_enqueue_style(
			'koro-admin',
			KORO_ADMIN_URL . 'assets/css/admin.css',
			array(),
			KORO_ADMIN_VERSION
		);

		wp_enqueue_script(
			'koro-admin',
			KORO_ADMIN_URL . 'assets/js/admin.js',
			array(),
			KORO_ADMIN_VERSION,
			true
		);
	}

	public static function render_dashboard(): void {
		if ( ! current_user_can( 'manage_koro_dashboard' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'koro-admin' ) );
		}

		$stats = self::collect_stats();
		$orders = class_exists( 'Koro_Booking_Orders' ) ? Koro_Booking_Orders::recent( 6 ) : array();
		$payments_ready = class_exists( 'Koro_Payments_Settings' ) && Koro_Payments_Settings::is_ready();
		?>
		<div class="wrap koro-admin">
			<h1><?php esc_html_e( 'Koro Suite Dashboard', 'koro-admin' ); ?></h1>

			<div class="koro-stats-grid">
				<div class="koro-stat-card">
					<span class="koro-stat-card__label"><?php esc_html_e( 'Published Services', 'koro-admin' ); ?></span>
					<strong class="koro-stat-card__value"><?php echo esc_html( (string) $stats['services'] ); ?></strong>
				</div>
				<div class="koro-stat-card">
					<span class="koro-stat-card__label"><?php esc_html_e( 'Completed Bookings', 'koro-admin' ); ?></span>
					<strong class="koro-stat-card__value"><?php echo esc_html( (string) $stats['completed_orders'] ); ?></strong>
				</div>
				<div class="koro-stat-card">
					<span class="koro-stat-card__label"><?php esc_html_e( 'Pending Bookings', 'koro-admin' ); ?></span>
					<strong class="koro-stat-card__value"><?php echo esc_html( (string) $stats['pending_orders'] ); ?></strong>
				</div>
				<div class="koro-stat-card koro-stat-card--<?php echo $payments_ready ? 'ok' : 'warn'; ?>">
					<span class="koro-stat-card__label"><?php esc_html_e( 'Payments', 'koro-admin' ); ?></span>
					<strong class="koro-stat-card__value"><?php echo $payments_ready ? esc_html__( 'Ready', 'koro-admin' ) : esc_html__( 'Setup Required', 'koro-admin' ); ?></strong>
				</div>
			</div>

			<div class="koro-panels">
				<section class="koro-panel">
					<h2><?php esc_html_e( 'Recent Bookings', 'koro-admin' ); ?></h2>
					<?php if ( empty( $orders ) ) : ?>
						<p><?php esc_html_e( 'No bookings yet.', 'koro-admin' ); ?></p>
					<?php else : ?>
						<table class="widefat striped">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Booking', 'koro-admin' ); ?></th>
									<th><?php esc_html_e( 'Customer', 'koro-admin' ); ?></th>
									<th><?php esc_html_e( 'Total', 'koro-admin' ); ?></th>
									<th><?php esc_html_e( 'Status', 'koro-admin' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $orders as $order ) : ?>
									<tr>
										<td><a href="<?php echo esc_url( get_edit_post_link( $order->ID ) ); ?>"><?php echo esc_html( get_the_title( $order ) ); ?></a></td>
										<td><?php echo esc_html( (string) get_post_meta( $order->ID, '_koro_customer_email', true ) ); ?></td>
										<td>$<?php echo esc_html( number_format( (float) get_post_meta( $order->ID, '_koro_subtotal', true ), 2 ) ); ?></td>
										<td><?php echo esc_html( (string) get_post_meta( $order->ID, '_koro_status', true ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</section>

				<section class="koro-panel">
					<h2><?php esc_html_e( 'Quick Links', 'koro-admin' ); ?></h2>
					<ul class="koro-quick-links">
						<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=koro_service' ) ); ?>"><?php esc_html_e( 'Manage Services', 'koro-admin' ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=koro_order' ) ); ?>"><?php esc_html_e( 'View Bookings', 'koro-admin' ); ?></a></li>
						<?php if ( current_user_can( 'manage_koro_payments' ) ) : ?>
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=koro-payments' ) ); ?>"><?php esc_html_e( 'Payment Settings', 'koro-admin' ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Site', 'koro-admin' ); ?></a></li>
					</ul>
				</section>
			</div>
		</div>
		<?php
	}

	public static function render_settings(): void {
		if ( ! current_user_can( 'manage_koro_settings' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'koro-admin' ) );
		}

		if ( isset( $_POST['koro_admin_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koro_admin_settings_nonce'] ) ), 'koro_admin_settings' ) ) {
			update_option( 'koro_admin_support_email', sanitize_email( wp_unslash( $_POST['support_email'] ?? '' ) ) );
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'koro-admin' ) . '</p></div>';
		}

		$support_email = get_option( 'koro_admin_support_email', get_option( 'admin_email' ) );
		?>
		<div class="wrap koro-admin">
			<h1><?php esc_html_e( 'Koro Settings', 'koro-admin' ); ?></h1>
			<form method="post">
				<?php wp_nonce_field( 'koro_admin_settings', 'koro_admin_settings_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="support_email"><?php esc_html_e( 'Support Email', 'koro-admin' ); ?></label></th>
						<td><input type="email" class="regular-text" name="support_email" id="support_email" value="<?php echo esc_attr( (string) $support_email ); ?>"></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Collect dashboard statistics.
	 *
	 * @return array{services:int,completed_orders:int,pending_orders:int}
	 */
	private static function collect_stats(): array {
		$services_query = new WP_Query(
			array(
				'post_type'      => 'koro_service',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		$completed = class_exists( 'Koro_Booking_Orders' ) ? Koro_Booking_Orders::count_by_status( 'completed' ) : 0;
		$pending   = class_exists( 'Koro_Booking_Orders' ) ? Koro_Booking_Orders::count_by_status( 'pending' ) : 0;

		return array(
			'services'         => (int) $services_query->found_posts,
			'completed_orders' => $completed,
			'pending_orders'   => $pending,
		);
	}
}
