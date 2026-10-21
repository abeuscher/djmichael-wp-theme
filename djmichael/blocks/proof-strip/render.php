<?php
/**
 * Block: Review counts. Content comes from Site settings → Reviews.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_platforms = array_filter( (array) djm_option( 'review_platforms', array() ), 'is_array' );
$djm_award     = djm_option( 'award_value' );

if ( ! $djm_platforms && ! $djm_award ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add review platforms in Site settings → Reviews.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="proof"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="proof-strip" aria-label="<?php esc_attr_e( 'Reviews and awards', 'djmichael' ); ?>">
	<ul class="proof__list container">
		<?php foreach ( $djm_platforms as $djm_row ) : ?>
			<li class="proof__item">
				<?php if ( ! empty( $djm_row['url'] ) ) : ?>
					<a class="proof__link" href="<?php echo esc_url( $djm_row['url'] ); ?>" target="_blank" rel="noopener">
				<?php else : ?>
					<div class="proof__link">
				<?php endif; ?>
					<span class="proof__rating"><?php echo esc_html( $djm_row['rating'] ); ?></span>
					<span class="proof__count">
						<?php
						/* translators: %s: number of reviews */
						echo esc_html( sprintf( _n( '%s review', '%s reviews', (int) $djm_row['count'], 'djmichael' ), number_format_i18n( (int) $djm_row['count'] ) ) );
						?>
					</span>
					<span class="proof__platform"><?php echo esc_html( $djm_row['platform'] ); ?> <?php echo ! empty( $djm_row['url'] ) ? djm_icon( 'external' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></span>
				<?php if ( ! empty( $djm_row['url'] ) ) : ?>
					<?php echo djm_new_tab_label(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
					</a>
				<?php else : ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		<?php if ( $djm_award ) : ?>
			<li class="proof__item proof__item--award">
				<div class="proof__link">
					<span class="proof__rating"><?php echo esc_html( $djm_award ); ?></span>
					<span class="proof__count"><?php echo esc_html( djm_option( 'award_label' ) ); ?></span>
					<span class="proof__platform"><?php echo esc_html( djm_option( 'award_source' ) ); ?></span>
				</div>
			</li>
		<?php endif; ?>
	</ul>
</section>
