<?php
/**
 * Block: Contact block.
 *
 * The form is a Fluent Forms form, styled to the prototype by
 * assets/css/fluent-forms.css. With a title set the block carries its own
 * heading, as on the home page; without one the page head provides the
 * heading and the block takes the --page modifier, as on the Contact page.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_title    = get_field( 'title' );
$djm_form     = (int) get_field( 'form' );
$djm_email    = djm_option( 'email' );
$djm_ig_url   = djm_option( 'instagram_url' );
$djm_ig_label = djm_option( 'instagram_handle' );
$djm_note     = djm_option( 'booking_note' );

$djm_anchor = ! empty( $block['anchor'] ) ? $block['anchor'] : 'contact';
?>
<section class="section contact-block<?php echo $djm_title ? '' : ' contact-block--page'; ?>" id="<?php echo esc_attr( $djm_anchor ); ?>" data-part="contact-block" <?php echo $djm_title ? 'aria-labelledby="contact-title"' : 'aria-label="' . esc_attr__( 'Contact form', 'djmichael' ) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>>
	<div class="contact-block__inner container">
		<div class="contact-block__main">
			<?php if ( $djm_title ) : ?>
				<h2 class="section__title" id="contact-title"><?php echo esc_html( $djm_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $djm_form && shortcode_exists( 'fluentform' ) ) : ?>
				<?php echo do_shortcode( sprintf( '[fluentform id="%d"]', $djm_form ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- shortcode output. ?>
			<?php elseif ( $is_preview ) : ?>
				<p style="padding:1rem"><?php esc_html_e( 'Choose the Fluent Forms form for this block.', 'djmichael' ); ?></p>
			<?php endif; ?>
		</div>

		<aside class="contact-block__aside" aria-label="<?php esc_attr_e( 'Other ways to reach me', 'djmichael' ); ?>">
			<?php if ( $djm_email ) : ?>
				<p class="contact-block__label"><?php esc_html_e( 'Or email me directly', 'djmichael' ); ?></p>
				<p class="contact-block__email"><a href="<?php echo esc_url( 'mailto:' . $djm_email ); ?>"><?php echo esc_html( $djm_email ); ?></a></p>
			<?php endif; ?>
			<?php if ( $djm_ig_url && $djm_ig_label ) : ?>
				<p class="contact-block__instagram"><?php esc_html_e( 'Instagram', 'djmichael' ); ?> <a href="<?php echo esc_url( $djm_ig_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $djm_ig_label ); ?><?php echo djm_new_tab_label(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></a></p>
			<?php endif; ?>
			<?php if ( $djm_note ) : ?>
				<p class="contact-block__note"><?php echo esc_html( $djm_note ); ?></p>
			<?php endif; ?>
		</aside>
	</div>
</section>
