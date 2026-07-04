<?php
/**
 * Service card partial.
 *
 * @package Koro_Base
 */

$price    = (float) get_post_meta( get_the_ID(), '_koro_price', true );
$duration = (int) get_post_meta( get_the_ID(), '_koro_duration', true );
?>

<article <?php post_class( 'service-card' ); ?>>
	<a class="service-card__link" href="<?php the_permalink(); ?>">
		<div class="service-card__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'koro-card' );
			} else {
				echo '<div class="service-card__placeholder"></div>';
			}
			?>
		</div>
		<div class="service-card__body">
			<h3 class="service-card__title"><?php the_title(); ?></h3>
			<p class="service-card__excerpt"><?php echo esc_html( koro_base_excerpt( 16 ) ); ?></p>
			<div class="service-card__meta">
				<?php if ( $price > 0 ) : ?>
					<span><?php echo esc_html( koro_base_format_price( $price ) ); ?></span>
				<?php endif; ?>
				<?php if ( $duration > 0 ) : ?>
					<span><?php echo esc_html( sprintf( _n( '%d min', '%d mins', $duration, 'koro-base' ), $duration ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</a>
</article>
