<?php
/**
 * Plugin bootstrap and admin UI.
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
final class Koro_Payments_Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( KORO_PAYMENTS_FILE, array( Koro_Payments_Transactions::class, 'install' ) );

		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function register_menu(): void {
		add_submenu_page(
			'koro-dashboard',
			__( 'Payments', 'koro-payments' ),
			__( 'Payments', 'koro-payments' ),
			'manage_koro_payments',
			'koro-payments',
			array( $this, 'render_settings_page' )
		);
	}

	public function enqueue_admin_assets( string $hook ): void {
		if ( 'koro-dashboard_page_koro-payments' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'koro-payments-admin',
			KORO_PAYMENTS_URL . 'assets/css/admin.css',
			array(),
			KORO_PAYMENTS_VERSION
		);
	}

	public function handle_save(): void {
		if ( ! isset( $_POST['koro_payments_nonce'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_koro_payments' ) ) {
			return;
		}

		check_admin_referer( 'koro_payments_save', 'koro_payments_nonce' );

		$input = array(
			'mode'           => sanitize_key( wp_unslash( $_POST['mode'] ?? 'sandbox' ) ),
			'gateway'        => sanitize_key( wp_unslash( $_POST['gateway'] ?? 'koro_stripe' ) ),
			'currency'       => sanitize_text_field( wp_unslash( $_POST['currency'] ?? 'USD' ) ),
			'enabled'        => isset( $_POST['enabled'] ) ? 1 : 0,
			'public_key'     => sanitize_text_field( wp_unslash( $_POST['public_key'] ?? '' ) ),
			'secret_key'     => sanitize_text_field( wp_unslash( $_POST['secret_key'] ?? '' ) ),
			'webhook_secret' => sanitize_text_field( wp_unslash( $_POST['webhook_secret'] ?? '' ) ),
		);

		Koro_Payments_Settings::save( $input );

		add_settings_error( 'koro_payments', 'saved', __( 'Payment settings saved.', 'koro-payments' ), 'success' );
	}

	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_koro_payments' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'koro-payments' ) );
		}

		$settings     = Koro_Payments_Settings::get_for_admin();
		$transactions = Koro_Payments_Transactions::recent( 8 );
		?>
		<div class="wrap koro-admin-wrap">
			<h1><?php esc_html_e( 'Payment Gateway', 'koro-payments' ); ?></h1>
			<?php settings_errors( 'koro_payments' ); ?>

			<form method="post" action="">
				<?php wp_nonce_field( 'koro_payments_save', 'koro_payments_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enabled', 'koro-payments' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="enabled" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?>>
								<?php esc_html_e( 'Accept payments', 'koro-payments' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="koro_mode"><?php esc_html_e( 'Mode', 'koro-payments' ); ?></label></th>
						<td>
							<select name="mode" id="koro_mode">
								<option value="sandbox" <?php selected( $settings['mode'], 'sandbox' ); ?>><?php esc_html_e( 'Sandbox', 'koro-payments' ); ?></option>
								<option value="live" <?php selected( $settings['mode'], 'live' ); ?>><?php esc_html_e( 'Live', 'koro-payments' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="koro_currency"><?php esc_html_e( 'Currency', 'koro-payments' ); ?></label></th>
						<td><input type="text" class="regular-text" name="currency" id="koro_currency" value="<?php echo esc_attr( $settings['currency'] ); ?>" maxlength="3"></td>
					</tr>
					<tr>
						<th scope="row"><label for="koro_public_key"><?php esc_html_e( 'Public Key', 'koro-payments' ); ?></label></th>
						<td><input type="text" class="large-text" name="public_key" id="koro_public_key" value="<?php echo esc_attr( $settings['public_key'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="koro_secret_key"><?php esc_html_e( 'Secret Key', 'koro-payments' ); ?></label></th>
						<td>
							<input type="password" class="large-text" name="secret_key" id="koro_secret_key" value="<?php echo esc_attr( $settings['secret_key'] ); ?>" autocomplete="new-password">
							<p class="description"><?php esc_html_e( 'Stored encrypted in the database. Leave masked value unchanged to keep existing key.', 'koro-payments' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="koro_webhook_secret"><?php esc_html_e( 'Webhook Secret', 'koro-payments' ); ?></label></th>
						<td><input type="password" class="large-text" name="webhook_secret" id="koro_webhook_secret" value="<?php echo esc_attr( $settings['webhook_secret'] ); ?>" autocomplete="new-password"></td>
					</tr>
				</table>

				<?php submit_button( __( 'Save Settings', 'koro-payments' ) ); ?>
			</form>

			<h2><?php esc_html_e( 'Recent Transactions', 'koro-payments' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Transaction', 'koro-payments' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'koro-payments' ); ?></th>
						<th><?php esc_html_e( 'Status', 'koro-payments' ); ?></th>
						<th><?php esc_html_e( 'Reference', 'koro-payments' ); ?></th>
						<th><?php esc_html_e( 'Date', 'koro-payments' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $transactions ) ) : ?>
						<tr><td colspan="5"><?php esc_html_e( 'No transactions yet.', 'koro-payments' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $transactions as $row ) : ?>
							<tr>
								<td><code><?php echo esc_html( $row->transaction_id ); ?></code></td>
								<td><?php echo esc_html( $row->currency . ' ' . number_format( (float) $row->amount, 2 ) ); ?></td>
								<td><?php echo esc_html( $row->status ); ?></td>
								<td><?php echo esc_html( $row->reference ); ?></td>
								<td><?php echo esc_html( $row->created_at ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function register_rest_routes(): void {
		register_rest_route(
			'koro/v1',
			'/payments/status',
			array(
				'methods'             => 'GET',
				'callback'            => function () {
					return rest_ensure_response(
						array(
							'ready'  => Koro_Payments_Settings::is_ready(),
							'mode'   => Koro_Payments_Settings::get()['mode'],
						)
					);
				},
				'permission_callback' => '__return_true',
			)
		);
	}
}

/**
 * Process payment for other plugins.
 *
 * @param float  $amount    Amount in major units.
 * @param string $reference Order reference.
 * @param array<string, mixed> $meta Metadata.
 * @return array{success:bool,transaction_id?:string,message?:string}
 */
function koro_payments_charge( float $amount, string $reference, array $meta = array() ): array {
	$gateway = new Koro_Payments_Gateway();
	return $gateway->charge( $amount, $reference, $meta );
}
