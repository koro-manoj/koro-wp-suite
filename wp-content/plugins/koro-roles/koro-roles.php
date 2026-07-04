<?php
/**
 * Plugin Name: Koro Roles
 * Plugin URI: https://github.com/koro-wp-suite/koro-roles
 * Description: Custom roles and editorial workflows for the Koro booking suite.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Koro Suite
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: koro-roles
 *
 * @package Koro_Roles
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KORO_ROLES_VERSION', '1.0.0' );
define( 'KORO_ROLES_FILE', __FILE__ );
define( 'KORO_ROLES_DIR', plugin_dir_path( __FILE__ ) );

require_once KORO_ROLES_DIR . 'includes/class-koro-roles-registry.php';
require_once KORO_ROLES_DIR . 'includes/class-koro-roles-workflows.php';
require_once KORO_ROLES_DIR . 'includes/class-koro-roles-plugin.php';

Koro_Roles_Plugin::instance();
