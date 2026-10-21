<?php
/**
 * Theme supports, menus and editor restrictions.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( array( 'assets/css/tokens.css', 'assets/css/site.css', 'assets/css/editor.css' ) );

		// Core patterns and the pattern directory don't fit this design.
		remove_theme_support( 'core-block-patterns' );

		register_nav_menus(
			array(
				'primary' => __( 'Header', 'djmichael' ),
				'footer'  => __( 'Footer', 'djmichael' ),
			)
		);
	}
);

add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Pages may only use this theme's blocks.
 */
add_filter(
	'allowed_block_types_all',
	function ( $allowed, $context ) {
		if ( empty( $context->post ) || 'page' !== $context->post->post_type ) {
			return $allowed;
		}
		$names = array();
		foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
			if ( str_starts_with( $name, 'acf/' ) ) {
				$names[] = $name;
			}
		}
		return $names;
	},
	10,
	2
);

/**
 * Header menu: give links the theme's classes. The "Home" item is mobile-only;
 * mark it with the CSS class "site-nav__item--mobile" in Appearance → Menus.
 */
add_filter(
	'nav_menu_link_attributes',
	function ( $atts, $item, $args ) {
		if ( 'primary' === $args->theme_location ) {
			$atts['class'] = 'site-nav__link';
		}
		return $atts;
	},
	10,
	3
);

add_filter(
	'nav_menu_css_class',
	function ( $classes, $item, $args ) {
		if ( 'primary' !== $args->theme_location ) {
			return $classes;
		}
		// Keep only author-set classes plus our item class; WP's defaults add noise.
		$custom = array_filter( (array) get_post_meta( $item->ID, '_menu_item_classes', true ) );
		return array_merge( array( 'site-nav__item' ), $custom );
	},
	10,
	3
);

// Trim default head output this site doesn't use.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
