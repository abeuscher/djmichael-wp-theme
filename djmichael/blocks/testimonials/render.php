<?php
/**
 * Block: Testimonials.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title  = get_field( 'title' );
$djm_quotes = array_filter( (array) get_field( 'quotes' ), 'is_array' );

if ( ! $djm_quotes ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add a quote to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section testimonials"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="testimonials" aria-labelledby="testimonials-title">
	<div class="container">
		<h2 class="section__title" id="testimonials-title"><?php echo esc_html( $djm_title ? $djm_title : __( 'What couples say', 'djmichael' ) ); ?></h2>
		<ul class="testimonials__list">
			<?php
			foreach ( $djm_quotes as $djm_quote ) :
				if ( empty( $djm_quote['text'] ) ) {
					continue;
				}
				?>
				<li class="testimonials__item">
					<figure class="quote">
						<blockquote class="quote__text"><?php echo wpautop( esc_html( $djm_quote['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped before wpautop. ?></blockquote>
						<?php if ( ! empty( $djm_quote['name'] ) || ! empty( $djm_quote['date'] ) || ! empty( $djm_quote['source'] ) ) : ?>
							<figcaption class="quote__cite">
								<?php
								$djm_cite = array();
								if ( ! empty( $djm_quote['name'] ) ) {
									$djm_cite[] = '<span class="quote__name">' . esc_html( $djm_quote['name'] ) . '</span>';
								}
								if ( ! empty( $djm_quote['date'] ) ) {
									$djm_cite[] = '<span class="quote__date">' . esc_html( $djm_quote['date'] ) . '</span>';
								}
								if ( ! empty( $djm_quote['source'] ) ) {
									$djm_cite[] = '<span class="quote__source">' . esc_html( $djm_quote['source'] ) . '</span>';
								}
								echo implode( ' · ', $djm_cite ); // phpcs:ignore WordPress.Security.EscapeOutput -- each part escaped above.
								?>
							</figcaption>
						<?php endif; ?>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
