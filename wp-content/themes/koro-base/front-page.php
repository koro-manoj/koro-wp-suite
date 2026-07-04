<?php
/**
 * Front page template.
 *
 * @package Koro_Base
 */

get_header();

$hero_title    = get_theme_mod( 'koro_hero_title', __( 'Book experiences that matter', 'koro-base' ) );
$hero_subtitle = get_theme_mod( 'koro_hero_subtitle', __( 'Curated services, flexible scheduling, and secure checkout — all in one place.', 'koro-base' ) );
$hero_cta      = get_theme_mod( 'koro_hero_cta_label', __( 'Browse Services', 'koro-base' ) );
$services_url  = get_post_type_archive_link( 'koro_service' ) ?: home_url( '/services/' );
?>

<section class="hero">
	<div class="container hero__inner">
		<div class="hero__content">
			<p class="hero__eyebrow"><?php esc_html_e( 'Premium Booking', 'koro-base' ); ?></p>
			<h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--primary" href="<?php echo esc_url( $services_url ); ?>"><?php echo esc_html( $hero_cta ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'How It Works', 'koro-base' ); ?></a>
			</div>
			<ul class="hero__trust">
				<li><?php esc_html_e( 'Secure payments', 'koro-base' ); ?></li>
				<li><?php esc_html_e( 'Flexible scheduling', 'koro-base' ); ?></li>
				<li><?php esc_html_e( 'Instant confirmation', 'koro-base' ); ?></li>
			</ul>
		</div>
		<div class="hero__visual" aria-hidden="true">
			<div class="hero__card hero__card--one"></div>
			<div class="hero__card hero__card--two"></div>
			<div class="hero__card hero__card--three"></div>
		</div>
	</div>
</section>

<section class="section section--alt section--features">
	<div class="container">
		<header class="section__header">
			<h2><?php esc_html_e( 'Why book with us', 'koro-base' ); ?></h2>
			<p><?php esc_html_e( 'A streamlined experience from discovery to confirmation.', 'koro-base' ); ?></p>
		</header>
		<div class="feature-grid">
			<article class="feature-card">
				<div class="feature-card__icon" aria-hidden="true">01</div>
				<h3><?php esc_html_e( 'Curated Services', 'koro-base' ); ?></h3>
				<p><?php esc_html_e( 'Browse a hand-picked catalog with clear pricing, duration, and availability.', 'koro-base' ); ?></p>
			</article>
			<article class="feature-card">
				<div class="feature-card__icon" aria-hidden="true">02</div>
				<h3><?php esc_html_e( 'Simple Checkout', 'koro-base' ); ?></h3>
				<p><?php esc_html_e( 'Add to cart, pick your date, and complete payment in a guided flow.', 'koro-base' ); ?></p>
			</article>
			<article class="feature-card">
				<div class="feature-card__icon" aria-hidden="true">03</div>
				<h3><?php esc_html_e( 'Protected Payments', 'koro-base' ); ?></h3>
				<p><?php esc_html_e( 'Gateway credentials are encrypted at rest — your data stays secure.', 'koro-base' ); ?></p>
			</article>
		</div>
	</div>
</section>

<?php if ( post_type_exists( 'koro_service' ) ) : ?>
<section class="section section--services">
	<div class="container">
		<header class="section__header section__header--row">
			<div>
				<h2><?php esc_html_e( 'Featured Services', 'koro-base' ); ?></h2>
				<p><?php esc_html_e( 'Popular picks from our catalog.', 'koro-base' ); ?></p>
			</div>
			<a class="btn btn--ghost" href="<?php echo esc_url( $services_url ); ?>"><?php esc_html_e( 'View All', 'koro-base' ); ?></a>
		</header>

		<div class="service-grid">
			<?php
			$services = new WP_Query(
				array(
					'post_type'      => 'koro_service',
					'posts_per_page' => 3,
					'post_status'    => 'publish',
				)
			);

			if ( $services->have_posts() ) :
				while ( $services->have_posts() ) :
					$services->the_post();
					get_template_part( 'template-parts/content', 'service-card' );
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p class="empty-state"><?php esc_html_e( 'Add services in the admin to populate this section.', 'koro-base' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="cta-band">
	<div class="container cta-band__inner">
		<div class="cta-band__content">
			<h2><?php esc_html_e( 'Ready to get started?', 'koro-base' ); ?></h2>
			<p><?php esc_html_e( 'Explore our services and book your next appointment in minutes.', 'koro-base' ); ?></p>
		</div>
		<a class="btn btn--warm" href="<?php echo esc_url( $services_url ); ?>"><?php esc_html_e( 'View Services', 'koro-base' ); ?></a>
	</div>
</section>

<?php
get_footer();
