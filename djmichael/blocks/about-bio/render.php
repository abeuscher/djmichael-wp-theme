<?php
/**
 * Block: About (biography).
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_photo   = (int) get_field( 'photo' );
$djm_bio     = get_field( 'bio' );
$djm_stats   = get_field( 'stats' );
$djm_support = (int) get_field( 'support_photo' );

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section about-page"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="about-bio" aria-label="<?php esc_attr_e( 'Biography', 'djmichael' ); ?>">
	<div class="container">
		<?php if ( $djm_photo ) : ?>
			<figure class="about-page__lead">
				<?php
				// Above the fold, so it isn't lazy-loaded.
				echo wp_get_attachment_image(
					$djm_photo,
					'large',
					false,
					array( 'class' => 'about-page__img' )
				);
				?>
			</figure>
		<?php endif; ?>
		<?php if ( $djm_bio ) : ?>
			<div class="prose about-page__bio"><?php echo wp_kses_post( $djm_bio ); ?></div>
		<?php endif; ?>
		<?php echo djm_stats( $djm_stats, 'about-page__stats' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
		<?php if ( $djm_support ) : ?>
			<figure class="about-page__support">
				<?php
				echo wp_get_attachment_image(
					$djm_support,
					'large',
					false,
					array(
						'class'   => 'about-page__img about-page__img--wide',
						'loading' => 'lazy',
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>
