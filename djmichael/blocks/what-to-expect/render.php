<?php
/**
 * Block: What to expect.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title = get_field( 'title' );
$djm_steps = array_values( array_filter( (array) get_field( 'steps' ), 'is_array' ) );

if ( ! $djm_steps ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add the steps to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section section--rule steps"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="what-to-expect" aria-labelledby="steps-title">
	<div class="container">
		<h2 class="section__title" id="steps-title"><?php echo esc_html( $djm_title ? $djm_title : __( 'What to expect', 'djmichael' ) ); ?></h2>
		<ol class="steps__list">
			<?php foreach ( $djm_steps as $djm_i => $djm_step ) : ?>
				<li class="steps__item">
					<span class="steps__number" aria-hidden="true"><?php echo esc_html( number_format_i18n( $djm_i + 1 ) ); ?></span>
					<?php if ( ! empty( $djm_step['title'] ) ) : ?>
						<h3 class="steps__title"><?php echo esc_html( $djm_step['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $djm_step['text'] ) ) : ?>
						<p class="steps__text"><?php echo esc_html( $djm_step['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
