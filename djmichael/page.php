<?php
/**
 * Pages: content is a stack of the theme's ACF blocks.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
