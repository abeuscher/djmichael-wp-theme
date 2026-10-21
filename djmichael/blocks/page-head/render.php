<?php
/**
 * Block: Page head.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title = get_field( 'title' );
$djm_title = $djm_title ? $djm_title : get_the_title();
$djm_intro = get_field( 'intro' );

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="page-head"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="page-head" aria-labelledby="page-title">
	<div class="container">
		<h1 class="page-head__title" id="page-title"><?php echo esc_html( $djm_title ); ?></h1>
		<?php if ( $djm_intro ) : ?>
			<p class="page-head__intro"><?php echo esc_html( $djm_intro ); ?></p>
		<?php endif; ?>
	</div>
</section>
