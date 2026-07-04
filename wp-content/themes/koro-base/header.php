<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'koro-base' ); ?></a>

<header class="site-header" role="banner">
	<div class="container site-header__inner">
		<div class="site-branding">
			<?php koro_base_site_branding(); ?>
		</div>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<span class="nav-toggle__label"><?php esc_html_e( 'Menu', 'koro-base' ); ?></span>
		</button>

		<nav id="primary-navigation" class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'koro-base' ); ?>">
			<?php koro_base_primary_nav(); ?>
			<?php if ( function_exists( 'koro_booking_cart_url' ) ) : ?>
				<a class="cart-link" href="<?php echo esc_url( koro_booking_cart_url() ); ?>">
					<?php esc_html_e( 'Cart', 'koro-base' ); ?>
					<span class="cart-count" data-koro-cart-count><?php echo esc_html( (string) koro_booking_cart_count() ); ?></span>
				</a>
			<?php endif; ?>
		</nav>
	</div>
</header>

<main id="main-content" class="site-main">
