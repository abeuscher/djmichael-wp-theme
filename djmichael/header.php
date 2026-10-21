<?php
/**
 * Site header. Opens <main>; footer.php closes it.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

$djm_email = djm_option( 'email' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'djmichael' ); ?></a>

	<header class="site-header">
		<div class="site-header__inner container">
			<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="wordmark__name"><?php echo esc_html( djm_option( 'business_name', get_bloginfo( 'name' ) ) ); ?></span>
				<?php if ( djm_option( 'tagline' ) ) : ?>
					<span class="wordmark__tagline"><?php echo esc_html( djm_option( 'tagline' ) ); ?></span>
				<?php endif; ?>
			</a>

			<nav class="site-nav" id="site-menu" aria-label="<?php esc_attr_e( 'Main', 'djmichael' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
				<div class="site-nav__extra">
					<a class="btn btn--primary btn--block" href="<?php echo esc_url( djm_contact_url() ); ?>"><?php echo esc_html( djm_cta_label() ); ?></a>
					<?php if ( $djm_email ) : ?>
						<a class="site-nav__email" href="mailto:<?php echo esc_attr( antispambot( $djm_email ) ); ?>"><?php echo esc_html( antispambot( $djm_email ) ); ?></a>
					<?php endif; ?>
				</div>
			</nav>

			<a class="btn btn--primary btn--header site-header__cta" href="<?php echo esc_url( djm_contact_url() ); ?>"><?php echo esc_html( djm_cta_label() ); ?></a>

			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-menu">
				<span class="visually-hidden"><?php esc_html_e( 'Menu', 'djmichael' ); ?></span>
				<svg class="menu-toggle__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path class="menu-toggle__open" d="M3 7h18M3 12h18M3 17h18" />
					<path class="menu-toggle__close" d="M5 5l14 14M19 5L5 19" />
				</svg>
			</button>
		</div>
	</header>

	<main id="main">
