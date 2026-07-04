<?php
/**
 * Single service template.
 *
 * @package Koro_Base
 */

get_header();

while ( have_posts() ) :
	the_post();

	$price    = (float) get_post_meta( get_the_ID(), '_koro_price', true );
	$duration = (int) get_post_meta( get_the_ID(), '_koro_duration', true );
	?>

	<article <?php post_class( 'service-single container' ); ?>>
		<div class="service-single__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'koro-hero' );
			}
			?>
		</div>

		<div class="service-single__content">
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="service-meta">
					<?php if ( $price > 0 ) : ?>
						<span class="service-meta__price"><?php echo esc_html( koro_base_format_price( $price ) ); ?></span>
					<?php endif; ?>
					<?php if ( $duration > 0 ) : ?>
						<span class="service-meta__duration"><?php echo esc_html( sprintf( _n( '%d minute', '%d minutes', $duration, 'koro-base' ), $duration ) ); ?></span>
					<?php endif; ?>
				</div>
			</header>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php if ( function_exists( 'koro_booking_render_add_to_cart' ) ) : ?>
				<div class="service-single__actions">
					<?php koro_booking_render_add_to_cart( get_the_ID() ); ?>
				</div>
			<?php endif; ?>
		</div>
	</article>

	<?php
endwhile;

get_footer();
