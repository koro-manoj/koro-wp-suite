<?php
/**
 * Asset enqueue.
 *
 * @package Koro_Base
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end styles and scripts.
 */
function koro_base_enqueue_assets(): void {
	wp_enqueue_style(
		'koro-base-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'koro-base-main',
		KORO_BASE_URI . '/assets/css/main.css',
		array( 'koro-base-fonts' ),
		KORO_BASE_VERSION
	);

	wp_enqueue_script(
		'koro-base-main',
		KORO_BASE_URI . '/assets/js/main.js',
		array(),
		KORO_BASE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'koro_base_enqueue_assets' );
