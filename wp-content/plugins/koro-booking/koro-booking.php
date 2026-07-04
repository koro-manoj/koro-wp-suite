<?php
/**
 * Plugin Name: Koro Booking
 * Plugin URI: https://github.com/koro-wp-suite/koro-booking
 * Description: Service catalog, session cart, and checkout flow integrated with Koro Payments.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Koro Suite
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: koro-booking
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KORO_BOOKING_VERSION', '1.0.0' );
define( 'KORO_BOOKING_FILE', __FILE__ );
define( 'KORO_BOOKING_DIR', plugin_dir_path( __FILE__ ) );
define( 'KORO_BOOKING_URL', plugin_dir_url( __FILE__ ) );

require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-post-types.php';
require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-cart.php';
require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-checkout.php';
require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-orders.php';
require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-shortcodes.php';
require_once KORO_BOOKING_DIR . 'includes/class-koro-booking-plugin.php';

Koro_Booking_Plugin::instance();
