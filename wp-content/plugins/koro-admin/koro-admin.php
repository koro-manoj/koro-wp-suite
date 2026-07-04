<?php
/**
 * Plugin Name: Koro Admin
 * Plugin URI: https://github.com/koro-wp-suite/koro-admin
 * Description: Custom admin dashboard for bookings, payments, and suite overview.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Koro Suite
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: koro-admin
 *
 * @package Koro_Admin
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KORO_ADMIN_VERSION', '1.0.0' );
define( 'KORO_ADMIN_FILE', __FILE__ );
define( 'KORO_ADMIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KORO_ADMIN_URL', plugin_dir_url( __FILE__ ) );

require_once KORO_ADMIN_DIR . 'includes/class-koro-admin-dashboard.php';
require_once KORO_ADMIN_DIR . 'includes/class-koro-admin-plugin.php';

Koro_Admin_Plugin::instance();
