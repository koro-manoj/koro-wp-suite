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
		'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap',
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
