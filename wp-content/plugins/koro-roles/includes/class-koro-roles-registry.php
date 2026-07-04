<?php
/**
 * Custom roles and capabilities.
 *
 * @package Koro_Roles
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Koro-specific roles and caps.
 */
final class Koro_Roles_Registry {

	/**
	 * Capability groups mapped to roles.
	 *
	 * @return array<string, array<string, bool>>
	 */
	public static function capability_map(): array {
		return array(
			'read'                   => true,
			'upload_files'           => true,
			'manage_koro_dashboard'  => true,
			'edit_koro_services'     => true,
			'edit_koro_service'      => true,
			'edit_published_koro_services' => true,
			'publish_koro_services'  => true,
			'delete_koro_services'   => true,
			'delete_published_koro_services' => true,
			'read_koro_order'        => true,
			'edit_koro_orders'       => true,
			'edit_koro_order'        => true,
			'edit_published_koro_orders' => true,
		);
	}

	public static function install_roles(): void {
		add_role(
			'koro_booking_manager',
			__( 'Booking Manager', 'koro-roles' ),
			array_merge(
				array( 'read' => true ),
				self::capability_map()
			)
		);

		add_role(
			'koro_content_editor',
			__( 'Content Editor', 'koro-roles' ),
			array(
				'read'                      => true,
				'upload_files'              => true,
				'edit_posts'                => true,
				'edit_published_posts'      => true,
				'publish_posts'             => true,
				'delete_posts'              => true,
				'edit_pages'                => true,
				'edit_published_pages'      => true,
				'edit_koro_services'        => true,
				'edit_koro_service'         => true,
				'edit_published_koro_services' => true,
				'publish_koro_services'     => true,
			)
		);

		self::grant_admin_caps();
	}

	/**
	 * Ensure administrators retain full Koro capabilities.
	 */
	public static function grant_admin_caps(): void {
		$admin = get_role( 'administrator' );
		if ( ! $admin ) {
			return;
		}

		$all_caps = array_merge(
			self::capability_map(),
			array(
				'manage_koro_dashboard' => true,
				'manage_koro_payments'  => true,
				'manage_koro_settings'  => true,
			)
		);

		foreach ( $all_caps as $cap => $grant ) {
			if ( $grant ) {
				$admin->add_cap( $cap );
			}
		}
	}

	public static function remove_roles(): void {
		remove_role( 'koro_booking_manager' );
		remove_role( 'koro_content_editor' );
	}
}
