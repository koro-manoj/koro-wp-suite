<?php
/**
 * Admin plugin bootstrap.
 *
 * @package Koro_Admin
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main admin plugin.
 */
final class Koro_Admin_Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( Koro_Admin_Dashboard::class, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( Koro_Admin_Dashboard::class, 'enqueue_assets' ) );
	}
}
