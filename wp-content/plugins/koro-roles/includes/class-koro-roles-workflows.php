<?php
/**
 * Editorial workflow restrictions.
 *
 * @package Koro_Roles
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Limit admin UI and publishing flows by role.
 */
final class Koro_Roles_Workflows {

	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'restrict_menus' ), 999 );
		add_filter( 'map_meta_cap', array( self::class, 'map_service_caps' ), 10, 4 );
		add_action( 'admin_notices', array( self::class, 'workflow_notice' ) );
		add_filter( 'post_row_actions', array( self::class, 'filter_row_actions' ), 10, 2 );
	}

	/**
	 * Hide unrelated menus for booking managers.
	 */
	public static function restrict_menus(): void {
		$user = wp_get_current_user();
		if ( ! in_array( 'koro_booking_manager', (array) $user->roles, true ) ) {
			return;
		}

		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );
		remove_submenu_page( 'koro-dashboard', 'koro-payments' );
	}

	/**
	 * Content editors can edit services but not delete orders.
	 *
	 * @param array<int, string> $caps    Required caps.
	 * @param string             $cap     Capability being checked.
	 * @param int                $user_id User ID.
	 * @param array<int, mixed>  $args    Extra args.
	 * @return array<int, string>
	 */
	public static function map_service_caps( array $caps, string $cap, int $user_id, array $args ): array {
		if ( ! in_array( $cap, array( 'delete_koro_order', 'delete_koro_orders' ), true ) ) {
			return $caps;
		}

		$user = get_userdata( $user_id );
		if ( $user && in_array( 'koro_content_editor', (array) $user->roles, true ) ) {
			return array( 'do_not_allow' );
		}

		return $caps;
	}

	/**
	 * Show workflow guidance in admin.
	 */
	public static function workflow_notice(): void {
		$screen = get_current_screen();
		if ( ! $screen || 'koro_service' !== $screen->post_type ) {
			return;
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'koro_content_editor', (array) $user->roles, true ) ) {
			return;
		}

		echo '<div class="notice notice-info"><p>' . esc_html__( 'Content Editor workflow: draft services for review, then publish when pricing and duration are confirmed.', 'koro-roles' ) . '</p></div>';
	}

	/**
	 * Remove quick trash for content editors on orders.
	 *
	 * @param array<string, string> $actions Row actions.
	 */
	public static function filter_row_actions( array $actions, WP_Post $post ): array {
		if ( 'koro_order' !== $post->post_type ) {
			return $actions;
		}

		$user = wp_get_current_user();
		if ( in_array( 'koro_content_editor', (array) $user->roles, true ) ) {
			unset( $actions['trash'], $actions['delete'] );
		}

		return $actions;
	}
}
