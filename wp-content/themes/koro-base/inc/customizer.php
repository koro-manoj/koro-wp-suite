<?php
/**
 * Theme customizer settings.
 *
 * @package Koro_Base
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register customizer options.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function koro_base_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'koro_base_hero',
		array(
			'title'    => __( 'Hero Section', 'koro-base' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'koro_hero_title',
		array(
			'default'           => __( 'Book experiences that matter', 'koro-base' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'koro_hero_title',
		array(
			'label'   => __( 'Hero Title', 'koro-base' ),
			'section' => 'koro_base_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'koro_hero_subtitle',
		array(
			'default'           => __( 'Curated services, seamless checkout, and secure payments.', 'koro-base' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'koro_hero_subtitle',
		array(
			'label'   => __( 'Hero Subtitle', 'koro-base' ),
			'section' => 'koro_base_hero',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'koro_hero_cta_label',
		array(
			'default'           => __( 'Browse Services', 'koro-base' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'koro_hero_cta_label',
		array(
			'label'   => __( 'Hero CTA Label', 'koro-base' ),
			'section' => 'koro_base_hero',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'koro_base_customize_register' );
