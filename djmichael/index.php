<?php
/**
 * Fallback template. The site is pages only; anything else gets a simple listing.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="page-head">
	<div class="container">
		<h1 class="page-head__title"><?php echo esc_html( is_404() ? __( 'Page not found', 'djmichael' ) : wp_get_document_title() ); ?></h1>
	</div>
</section>
<section class="section">
	<div class="container prose">
		<?php if ( is_404() || ! have_posts() ) : ?>
			<p><?php esc_html_e( "That page isn't here.", 'djmichael' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'djmichael' ); ?></a>.</p>
		<?php else : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
