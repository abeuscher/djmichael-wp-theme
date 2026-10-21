<?php
/**
 * Block: Vendors.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_items = array_filter(
	(array) get_field( 'items' ),
	function ( $row ) {
		return is_array( $row ) && ! empty( $row['name'] );
	}
);

if ( ! $djm_items ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add vendors to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section vendors"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="vendors" aria-label="<?php esc_attr_e( 'Vendors', 'djmichael' ); ?>">
	<div class="container">
		<ul class="vendors__list">
			<?php foreach ( $djm_items as $djm_item ) : ?>
				<li class="vendor">
					<?php if ( ! empty( $djm_item['category'] ) ) : ?>
						<p class="vendor__category"><?php echo esc_html( $djm_item['category'] ); ?></p>
					<?php endif; ?>
					<h2 class="vendor__name"><?php echo esc_html( $djm_item['name'] ); ?></h2>
					<?php if ( ! empty( $djm_item['place'] ) ) : ?>
						<p class="vendor__place"><?php echo esc_html( $djm_item['place'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $djm_item['url'] ) ) : ?>
						<a class="vendor__link link-arrow" href="<?php echo esc_url( $djm_item['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( djm_link_label( $djm_item['url'] ) ); ?><?php echo djm_new_tab_label(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
