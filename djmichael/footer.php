<?php
/**
 * Site footer. Closes the <main> opened in header.php.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

$djm_email = djm_option( 'email' );
?>
	</main>

	<footer class="site-footer">
		<div class="site-footer__inner container">
			<div class="site-footer__brand">
				<p class="site-footer__name"><?php echo esc_html( djm_option( 'business_name', get_bloginfo( 'name' ) ) ); ?></p>
				<?php if ( djm_option( 'location' ) ) : ?>
					<p class="site-footer__meta"><?php echo esc_html( djm_option( 'location' ) ); ?></p>
				<?php endif; ?>
				<?php if ( $djm_email ) : ?>
					<p class="site-footer__meta"><a href="mailto:<?php echo esc_attr( antispambot( $djm_email ) ); ?>"><?php echo esc_html( antispambot( $djm_email ) ); ?></a></p>
				<?php endif; ?>
			</div>
			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'djmichael' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'site-footer__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<p class="site-footer__copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( djm_option( 'business_name', get_bloginfo( 'name' ) ) ); ?></p>
		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
