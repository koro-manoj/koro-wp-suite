<?php
/**
 * Service archive template.
 *
 * @package Koro_Base
 */

get_header();
?>

<div class="container content-area">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Services', 'koro-base' ); ?></h1>
		<p><?php esc_html_e( 'Choose a service and add it to your cart.', 'koro-base' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="service-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'service-card' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="empty-state"><?php esc_html_e( 'No services published yet.', 'koro-base' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
