<?php
/**
 * Block: Contact band. Content comes from Site settings.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_label = djm_cta_label();
$djm_note  = djm_option( 'booking_note' );
$djm_email = djm_option( 'email' );

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section contact-cta"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="contact-cta" aria-labelledby="contact-cta-title">
	<div class="contact-cta__inner container">
		<div>
			<h2 class="section__title contact-cta__title" id="contact-cta-title"><?php echo esc_html( $djm_label ); ?></h2>
			<?php if ( $djm_note ) : ?>
				<p class="contact-cta__note"><?php echo esc_html( $djm_note ); ?></p>
			<?php endif; ?>
		</div>
		<div class="contact-cta__actions">
			<a class="btn btn--primary btn--large" href="<?php echo esc_url( djm_contact_url() ); ?>"><?php echo esc_html( $djm_label ); ?></a>
			<?php if ( $djm_email ) : ?>
				<p class="contact-cta__email"><?php esc_html_e( 'Or email me directly:', 'djmichael' ); ?> <a href="<?php echo esc_url( 'mailto:' . $djm_email ); ?>"><?php echo esc_html( $djm_email ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>
</section>
