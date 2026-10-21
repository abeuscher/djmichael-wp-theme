<?php
/**
 * ACF Pro: local JSON, the Site settings options page and block registration.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

// Field groups are version-controlled in acf-json/.
add_filter( 'acf/settings/save_json', fn() => DJM_DIR . '/acf-json' );
add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		return array( DJM_DIR . '/acf-json' );
	}
);

add_action(
	'acf/init',
	function () {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => __( 'Site settings', 'djmichael' ),
					'menu_title' => __( 'Site settings', 'djmichael' ),
					'menu_slug'  => 'djm-site-settings',
					'capability' => 'edit_pages',
					'icon_url'   => 'dashicons-admin-settings',
					'position'   => 59,
					'redirect'   => false,
				)
			);
		}
	}
);

// One block per page section, each in blocks/<name>/ with block.json and render.php.
add_action(
	'init',
	function () {
		foreach ( glob( DJM_DIR . '/blocks/*/block.json' ) as $file ) {
			register_block_type( dirname( $file ) );
		}
	}
);

add_filter(
	'block_categories_all',
	function ( $categories ) {
		array_unshift(
			$categories,
			array(
				'slug'  => 'djmichael',
				'title' => __( 'DJ Michael', 'djmichael' ),
			)
		);
		return $categories;
	}
);

add_action(
	'admin_notices',
	function () {
		if ( ! function_exists( 'acf_register_block_type' ) && current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'The DJ Michael theme needs ACF Pro. Page blocks and Site settings are unavailable until it is active.', 'djmichael' ) . '</p></div>';
		}
	}
);
