<?php
/**
 * 404 template.
 *
 * @package Koro_Base
 */

get_header();
?>

<div class="container content-area">
	<article class="entry">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'koro-base' ); ?></h1>
		</header>
		<div class="entry-content">
			<p><?php esc_html_e( 'The page you requested could not be found.', 'koro-base' ); ?></p>
			<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go Home', 'koro-base' ); ?></a></p>
		</div>
	</article>
</div>

<?php
get_footer();
