<?php
/**
 * Template helper functions.
 *
 * @package Koro_Base
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display site branding (logo or title).
 */
function koro_base_site_branding(): void {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php bloginfo( 'name' ); ?>
	</a>
	<?php
}

/**
 * Render primary navigation.
 */
function koro_base_primary_nav(): void {
	if ( ! has_nav_menu( 'primary' ) ) {
		return;
	}

	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'menu_class'     => 'primary-menu',
			'container'      => false,
			'fallback_cb'    => false,
		)
	);
}

/**
 * Format price for display.
 *
 * @param float $amount Amount in major currency units.
 */
function koro_base_format_price( float $amount ): string {
	return '$' . number_format( $amount, 2 );
}

/**
 * Get excerpt with fallback.
 *
 * @param int $length Word count.
 */
function koro_base_excerpt( int $length = 24 ): string {
	$text = get_the_excerpt();
	if ( empty( $text ) ) {
		$text = wp_strip_all_tags( get_the_content() );
	}

	return wp_trim_words( $text, $length, '&hellip;' );
}
