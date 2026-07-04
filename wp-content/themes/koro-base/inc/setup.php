<?php
/**
 * Theme setup.
 *
 * @package Koro_Base
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and menus.
 */
function koro_base_setup(): void {
	load_theme_textdomain( 'koro-base', KORO_BASE_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'koro-base' ),
			'footer'  => __( 'Footer Menu', 'koro-base' ),
		)
	);

	add_image_size( 'koro-card', 640, 400, true );
	add_image_size( 'koro-hero', 1440, 720, true );
}
add_action( 'after_setup_theme', 'koro_base_setup' );

/**
 * Register widget areas.
 */
function koro_base_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Footer Column', 'koro-base' ),
			'id'            => 'footer-1',
			'description'   => __( 'Widgets in the footer area.', 'koro-base' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'koro_base_widgets_init' );

/**
 * Content width for embedded media.
 */
function koro_base_content_width(): void {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'koro_base_content_width', 0 );
