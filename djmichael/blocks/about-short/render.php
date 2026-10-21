<?php
/**
 * Block: About (short).
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_image = (int) get_field( 'image' );
$djm_title = get_field( 'title' );
$djm_text  = get_field( 'text' );
$djm_stats = get_field( 'stats' );
$djm_link  = get_field( 'link' );

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section section--rule about-short"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="about-short" aria-labelledby="about-title">
	<div class="container">
		<div class="about-short__inner">
			<?php if ( $djm_image ) : ?>
				<div class="about-short__media">
					<?php
					echo wp_get_attachment_image(
						$djm_image,
						'large',
						false,
						array(
							'class'   => 'about-short__img',
							'loading' => 'lazy',
						)
					);
					?>
				</div>
			<?php endif; ?>
			<div class="about-short__body">
				<h2 class="section__title" id="about-title"><?php echo esc_html( $djm_title ? $djm_title : __( 'About Michael', 'djmichael' ) ); ?></h2>
				<?php if ( $djm_text ) : ?>
					<p class="about-short__text"><?php echo esc_html( $djm_text ); ?></p>
				<?php endif; ?>
				<?php echo djm_stats( $djm_stats ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
				<?php if ( ! empty( $djm_link['url'] ) ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $djm_link['url'] ); ?>"<?php echo ! empty( $djm_link['target'] ) ? ' target="' . esc_attr( $djm_link['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( ! empty( $djm_link['title'] ) ? $djm_link['title'] : __( 'More about me', 'djmichael' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
