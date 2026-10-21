<?php
/**
 * Block: Hero.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_image    = (int) get_field( 'image' );
$djm_mobile   = (int) get_field( 'mobile_image' );
$djm_video    = (int) get_field( 'video' );
$djm_eyebrow  = get_field( 'eyebrow' );
$djm_title    = get_field( 'title' );
$djm_subhead  = get_field( 'subhead' );
$djm_button   = get_field( 'button' );
$djm_btn_url  = ! empty( $djm_button['url'] ) ? $djm_button['url'] : djm_contact_url();
$djm_btn_text = ! empty( $djm_button['title'] ) ? $djm_button['title'] : djm_cta_label();
$djm_btn_tab  = ! empty( $djm_button['target'] ) ? $djm_button['target'] : '';

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="hero"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="hero" aria-labelledby="hero-title">
	<div class="hero__media">
		<?php if ( $djm_image ) : ?>
			<picture>
				<?php if ( $djm_mobile ) : ?>
					<source media="(max-width: 47.99em)" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $djm_mobile, 'full' ) ); ?>" sizes="100vw">
				<?php endif; ?>
				<?php
				echo wp_get_attachment_image(
					$djm_image,
					'full',
					false,
					array(
						'class'         => 'hero__img',
						'sizes'         => '100vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</picture>
		<?php endif; ?>
		<?php if ( $djm_video && ! $is_preview ) : ?>
			<video class="hero__video" muted loop playsinline preload="none" poster="<?php echo esc_url( wp_get_attachment_image_url( $djm_image, 'full' ) ); ?>" aria-hidden="true" data-hero-video>
				<source data-src="<?php echo esc_url( wp_get_attachment_url( $djm_video ) ); ?>" type="video/mp4">
			</video>
		<?php endif; ?>
	</div>
	<div class="hero__content container">
		<div class="hero__text">
			<?php if ( $djm_eyebrow ) : ?>
				<p class="hero__eyebrow eyebrow"><?php echo esc_html( $djm_eyebrow ); ?></p>
			<?php endif; ?>
			<h1 class="hero__title" id="hero-title"><?php echo esc_html( $djm_title ? $djm_title : get_the_title() ); ?></h1>
			<?php if ( $djm_subhead ) : ?>
				<p class="hero__subhead"><?php echo esc_html( $djm_subhead ); ?></p>
			<?php endif; ?>
			<a class="btn btn--primary btn--large hero__cta" href="<?php echo esc_url( $djm_btn_url ); ?>"<?php echo $djm_btn_tab ? ' target="' . esc_attr( $djm_btn_tab ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $djm_btn_text ); ?></a>
		</div>
	</div>
</section>
