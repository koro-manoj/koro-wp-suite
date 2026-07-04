<?php
/**
 * Roles plugin bootstrap.
 *
 * @package Koro_Roles
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main roles plugin.
 */
final class Koro_Roles_Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( KORO_ROLES_FILE, array( Koro_Roles_Registry::class, 'install_roles' ) );
		register_deactivation_hook( KORO_ROLES_FILE, array( Koro_Roles_Registry::class, 'remove_roles' ) );

		add_action( 'init', array( Koro_Roles_Registry::class, 'grant_admin_caps' ) );
		Koro_Roles_Workflows::init();
	}
}
