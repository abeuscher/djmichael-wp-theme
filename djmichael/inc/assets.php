<?php
/**
 * Front-end and editor assets.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		$ver = DJM_VERSION;
		wp_enqueue_style( 'djm-tokens', DJM_URI . '/assets/css/tokens.css', array(), $ver );
		wp_enqueue_style( 'djm-site', DJM_URI . '/assets/css/site.css', array( 'djm-tokens' ), $ver );
		wp_enqueue_script( 'djm-site', DJM_URI . '/assets/js/site.js', array(), $ver, array( 'strategy' => 'defer' ) );

		// Blocks are rendered server-side; the core block library CSS isn't used.
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

add_action(
	'wp_head',
	function () {
		// The menu toggle hides itself without JS; mark JS early to avoid a flash.
		echo "<script>document.documentElement.classList.add('js');</script>\n";
		foreach ( array( 'source-serif-4-latin.woff2', 'inter-latin.woff2' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( DJM_URI . '/assets/fonts/' . $font )
			);
		}
	},
	1
);
