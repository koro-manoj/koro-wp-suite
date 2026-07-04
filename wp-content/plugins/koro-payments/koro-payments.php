<?php
/**
 * Plugin Name: Koro Payments
 * Plugin URI: https://github.com/koro-wp-suite/koro-payments
 * Description: Payment gateway integration with encrypted credential storage. Supports sandbox and live modes.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Koro Suite
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: koro-payments
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KORO_PAYMENTS_VERSION', '1.0.0' );
define( 'KORO_PAYMENTS_FILE', __FILE__ );
define( 'KORO_PAYMENTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'KORO_PAYMENTS_URL', plugin_dir_url( __FILE__ ) );

require_once KORO_PAYMENTS_DIR . 'includes/class-koro-payments-crypto.php';
require_once KORO_PAYMENTS_DIR . 'includes/class-koro-payments-settings.php';
require_once KORO_PAYMENTS_DIR . 'includes/class-koro-payments-gateway.php';
require_once KORO_PAYMENTS_DIR . 'includes/class-koro-payments-transactions.php';
require_once KORO_PAYMENTS_DIR . 'includes/class-koro-payments-plugin.php';

Koro_Payments_Plugin::instance();
