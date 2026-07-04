<?php
/**
 * Front-end shortcodes.
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register cart and checkout shortcodes.
 */
final class Koro_Booking_Shortcodes {

	public static function register(): void {
		add_shortcode( 'koro_cart', array( self::class, 'render_cart' ) );
		add_shortcode( 'koro_checkout', array( self::class, 'render_checkout' ) );
	}

	public static function render_cart(): string {
		self::handle_cart_actions();

		$items = Koro_Booking_Cart::get_items();
		ob_start();
		include KORO_BOOKING_DIR . 'templates/cart.php';
		return (string) ob_get_clean();
	}

	public static function render_checkout(): string {
		$result = null;

		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			$result = Koro_Booking_Checkout::process();
		}

		$items = Koro_Booking_Cart::get_items();
		ob_start();
		include KORO_BOOKING_DIR . 'templates/checkout.php';
		return (string) ob_get_clean();
	}

	/**
	 * Handle cart update/remove actions.
	 */
	private static function handle_cart_actions(): void {
		if ( ! isset( $_POST['koro_cart_action'] ) ) {
			return;
		}

		if ( ! isset( $_POST['koro_cart_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koro_cart_nonce'] ) ), 'koro_cart' ) ) {
			return;
		}

		$action     = sanitize_key( wp_unslash( $_POST['koro_cart_action'] ) );
		$service_id = (int) ( $_POST['service_id'] ?? 0 );

		if ( 'update' === $action ) {
			$qty = (int) ( $_POST['quantity'] ?? 1 );
			Koro_Booking_Cart::update( $service_id, $qty );
		}

		if ( 'remove' === $action ) {
			Koro_Booking_Cart::remove( $service_id );
		}
	}
}

/**
 * Render add-to-cart form on single service pages.
 */
function koro_booking_render_add_to_cart( int $service_id ): void {
	$price = (float) get_post_meta( $service_id, '_koro_price', true );
	?>
	<form class="koro-booking-form" method="post" action="<?php echo esc_url( koro_booking_cart_url() ); ?>">
		<?php wp_nonce_field( 'koro_add_to_cart', 'koro_add_to_cart_nonce' ); ?>
		<input type="hidden" name="koro_add_to_cart" value="1">
		<input type="hidden" name="service_id" value="<?php echo esc_attr( (string) $service_id ); ?>">

		<div>
			<label for="koro_qty"><?php esc_html_e( 'Quantity', 'koro-booking' ); ?></label>
			<input type="number" name="quantity" id="koro_qty" value="1" min="1" max="10">
		</div>

		<button type="submit" class="btn btn--primary">
			<?php
			if ( $price > 0 ) {
				printf(
					/* translators: %s: formatted price */
					esc_html__( 'Add to Cart — $%s', 'koro-booking' ),
					esc_html( number_format( $price, 2 ) )
				);
			} else {
				esc_html_e( 'Add to Cart', 'koro-booking' );
			}
			?>
		</button>
	</form>
	<?php
}
