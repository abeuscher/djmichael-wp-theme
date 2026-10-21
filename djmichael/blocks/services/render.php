<?php
/**
 * Block: Services.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title = get_field( 'title' );
$djm_link  = get_field( 'link' );
$djm_items = array_filter( (array) get_field( 'items' ), 'is_array' );

if ( ! $djm_items ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add services to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section section--rule services"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="services" aria-labelledby="services-title">
	<div class="container">
		<h2 class="section__title" id="services-title"><?php echo esc_html( $djm_title ? $djm_title : __( 'Services', 'djmichael' ) ); ?></h2>
		<ul class="services__list">
			<?php foreach ( $djm_items as $djm_item ) : ?>
				<li class="service">
					<?php if ( ! empty( $djm_item['image'] ) ) : ?>
						<?php
						// Decorative: the service title carries the meaning.
						echo wp_get_attachment_image(
							(int) $djm_item['image'],
							'large',
							false,
							array(
								'class'   => 'service__img',
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
					<?php endif; ?>
					<?php if ( ! empty( $djm_item['title'] ) ) : ?>
						<h3 class="service__title"><?php echo esc_html( $djm_item['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $djm_item['text'] ) ) : ?>
						<p class="service__text"><?php echo esc_html( $djm_item['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( ! empty( $djm_link['url'] ) && ! empty( $djm_link['title'] ) ) : ?>
			<p class="services__more"><a class="link-arrow" href="<?php echo esc_url( $djm_link['url'] ); ?>"<?php echo ! empty( $djm_link['target'] ) ? ' target="' . esc_attr( $djm_link['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $djm_link['title'] ); ?></a></p>
		<?php endif; ?>
	</div>
</section>
