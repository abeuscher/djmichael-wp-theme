<?php
/**
 * Block: Gallery and slideshow.
 *
 * One list feeds both the grid and the slideshow. The first few items show as
 * tiles; the rest only appear in the slideshow. Each tile links to its file, so
 * the gallery still works with JavaScript off.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title   = get_field( 'title' );
$djm_title   = $djm_title ? $djm_title : __( 'Pictures and video', 'djmichael' );
$djm_visible = (int) get_field( 'visible' );
$djm_visible = $djm_visible > 0 ? $djm_visible : 8;

// Keep only rows that have a tile image; the image doubles as a video's poster.
$djm_items = array_values(
	array_filter(
		(array) get_field( 'items' ),
		function ( $row ) {
			return is_array( $row ) && ! empty( $row['image'] );
		}
	)
);

if ( ! $djm_items ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add photos and videos to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section section--rule gallery"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="gallery" aria-labelledby="gallery-title">
	<div class="container">
		<h2 class="section__title" id="gallery-title"><?php echo esc_html( $djm_title ); ?></h2>
		<ul class="gallery__grid">
			<?php
			foreach ( $djm_items as $djm_i => $djm_item ) :
				$djm_img      = (int) $djm_item['image'];
				$djm_video    = ! empty( $djm_item['video'] ) ? (int) $djm_item['video'] : 0;
				$djm_meta     = wp_get_attachment_metadata( $djm_img );
				$djm_w        = ! empty( $djm_meta['width'] ) ? (int) $djm_meta['width'] : 0;
				$djm_h        = ! empty( $djm_meta['height'] ) ? (int) $djm_meta['height'] : 0;
				$djm_full     = wp_get_attachment_image_url( $djm_img, 'full' );
				$djm_href     = $djm_video ? wp_get_attachment_url( $djm_video ) : $djm_full;
				$djm_more     = $djm_i >= $djm_visible ? ' gallery__item--more' : '';
				$djm_vid_cls  = $djm_video ? ' gallery__link--video' : '';
				?>
				<li class="gallery__item<?php echo esc_attr( $djm_more ); ?>">
					<a class="gallery__link<?php echo esc_attr( $djm_vid_cls ); ?>" href="<?php echo esc_url( $djm_href ); ?>" data-lightbox="<?php echo $djm_video ? 'video' : 'image'; ?>"<?php echo $djm_video ? ' data-poster="' . esc_url( $djm_full ) . '"' : ''; ?> data-width="<?php echo esc_attr( $djm_w ); ?>" data-height="<?php echo esc_attr( $djm_h ); ?>">
						<?php
						echo wp_get_attachment_image(
							$djm_img,
							'large',
							false,
							array(
								'class'   => 'gallery__img',
								'loading' => 'lazy',
							)
						);
						?>
						<?php if ( $djm_video ) : ?>
							<span class="gallery__play" aria-hidden="true"><?php echo djm_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( count( $djm_items ) > $djm_visible ) : ?>
			<p class="gallery__all" hidden data-gallery-all><button class="link-arrow gallery__all-btn" type="button">
				<?php
				/* translators: %s: total number of photos and videos */
				printf( esc_html__( 'View all %s photos and videos', 'djmichael' ), esc_html( number_format_i18n( count( $djm_items ) ) ) );
				?>
			</button></p>
		<?php endif; ?>
		<?php
		$djm_ig_url    = djm_option( 'instagram_url' );
		$djm_ig_handle = djm_option( 'instagram_handle' );
		?>
		<?php if ( $djm_ig_url && $djm_ig_handle ) : ?>
			<p class="gallery__caption"><?php esc_html_e( 'More on Instagram', 'djmichael' ); ?> <a class="link-arrow" href="<?php echo esc_url( $djm_ig_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $djm_ig_handle ); ?><?php echo djm_new_tab_label(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></a></p>
		<?php endif; ?>
	</div>
</section>
<?php if ( ! $is_preview ) : ?>
	<dialog class="slideshow" aria-label="<?php echo esc_attr( $djm_title ); ?>" aria-roledescription="carousel" data-slideshow>
		<ul class="slideshow__track" tabindex="0" aria-label="<?php esc_attr_e( 'Slides', 'djmichael' ); ?>" data-slideshow-track></ul>
		<p class="slideshow__count" aria-live="polite" data-slideshow-count></p>
		<button class="slideshow__btn slideshow__btn--prev" type="button" data-slideshow-prev><span class="visually-hidden"><?php esc_html_e( 'Previous', 'djmichael' ); ?></span><svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7"/></svg></button>
		<button class="slideshow__btn slideshow__btn--next" type="button" data-slideshow-next><span class="visually-hidden"><?php esc_html_e( 'Next', 'djmichael' ); ?></span><svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7"/></svg></button>
		<button class="slideshow__btn slideshow__btn--close" type="button" data-slideshow-close><span class="visually-hidden"><?php esc_html_e( 'Close', 'djmichael' ); ?></span><svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
	</dialog>
<?php endif; ?>
