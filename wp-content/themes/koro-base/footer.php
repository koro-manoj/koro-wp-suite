</main>

<footer class="site-footer" role="contentinfo">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<?php koro_base_site_branding(); ?>
			<p class="site-footer__tagline"><?php bloginfo( 'description' ); ?></p>
		</div>

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="site-footer__widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'koro-base' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'menu_class'     => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>

	<div class="site-footer__bottom container">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'koro-base' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
