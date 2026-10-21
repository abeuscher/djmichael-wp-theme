<?php
/**
 * WP-CLI: `wp djm seed` builds the site's content from seed/content.php.
 *
 * Imports media from a folder, writes Site settings, creates or updates pages
 * (as ACF block markup with the right attachment IDs), menus and the front page.
 * Safe to rerun: media is matched by file name, pages by slug.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

class DJM_Seed_Command {

	/** @var array<string,int> Seed media key => attachment ID. */
	private $media = array();

	/** @var array<string,array> Field group fields by location value (block name or "options"). */
	private $fields = array();

	/** @var array<string,int> Page slug => page ID. */
	private $pages = array();

	/**
	 * Build or refresh the site's content.
	 *
	 * ## OPTIONS
	 *
	 * [--media=<dir>]
	 * : Folder of media files. Default: wp-content/djm-seed/media, or $DJM_SEED_MEDIA.
	 *
	 * [--skip-media]
	 * : Reuse media already in the library; don't import.
	 *
	 * ## EXAMPLES
	 *
	 *     wp djm seed
	 *     wp djm seed --media=/seed/media
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Flags.
	 */
	public function seed( $args, $assoc_args ) {
		$content = require DJM_DIR . '/seed/content.php';
		$dir     = $assoc_args['media'] ?? ( getenv( 'DJM_SEED_MEDIA' ) ? getenv( 'DJM_SEED_MEDIA' ) : WP_CONTENT_DIR . '/djm-seed/media' );

		$this->load_fields();
		$this->import_media( $content['media'], rtrim( $dir, '/' ), ! empty( $assoc_args['skip-media'] ) );

		// Permalinks first: "@page:<slug>" values are resolved with get_permalink()
		// while pages are written, so the structure has to be in place by then.
		update_option( 'permalink_structure', '/%postname%/' );

		// Every page exists before any content is written, so a block on one page
		// can link to another page that comes later in the list.
		foreach ( $content['pages'] as $slug => $page ) {
			$this->pages[ $slug ] = $this->reserve_page( $slug, $page['title'] );
		}
		foreach ( $content['pages'] as $slug => $page ) {
			$this->upsert_page( $slug, $page );
		}
		$page_ids = $this->pages;

		$options = $content['options'];
		if ( isset( $page_ids['contact'] ) ) {
			$options['contact_page'] = $page_ids['contact'];
		}
		$this->write_fields( 'options', $options, 'options' );
		WP_CLI::log( 'Site settings written.' );

		$this->build_menus( $content['menus'], $page_ids );

		if ( isset( $page_ids['home'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page_ids['home'] );
		}
		flush_rewrite_rules( false );

		WP_CLI::success( sprintf( 'Seeded %d pages and %d media items.', count( $page_ids ), count( $this->media ) ) );
	}

	/**
	 * Index every local-JSON field group by what it's attached to.
	 */
	private function load_fields() {
		foreach ( glob( DJM_DIR . '/acf-json/*.json' ) as $file ) {
			$group = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			foreach ( $group['location'] as $rules ) {
				foreach ( $rules as $rule ) {
					$target = 'options_page' === $rule['param'] ? 'options' : ( 'block' === $rule['param'] ? $rule['value'] : $rule['param'] . ':' . $rule['value'] );
					$this->fields[ $target ] = array_merge( $this->fields[ $target ] ?? array(), $group['fields'] );
				}
			}
		}
	}

	/**
	 * Import media files, or find them if already imported.
	 *
	 * @param array  $items Key => [file, alt].
	 * @param string $dir   Source folder.
	 * @param bool   $skip  Don't import missing files.
	 */
	private function import_media( $items, $dir, $skip ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		foreach ( $items as $key => $item ) {
			$existing = get_posts(
				array(
					'post_type'   => 'attachment',
					'post_status' => 'inherit',
					'numberposts' => 1,
					'fields'      => 'ids',
					'meta_key'    => '_djm_seed_file', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'  => $item['file'], // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			);
			if ( $existing ) {
				$this->media[ $key ] = (int) $existing[0];
				update_post_meta( $existing[0], '_wp_attachment_image_alt', $item['alt'] ?? '' );
				continue;
			}
			if ( $skip ) {
				WP_CLI::warning( "Not in library, skipped: {$item['file']}" );
				continue;
			}
			$path = $dir . '/' . $item['file'];
			if ( ! file_exists( $path ) ) {
				WP_CLI::warning( "Missing file: $path" );
				continue;
			}
			$tmp = wp_tempnam( $item['file'] );
			copy( $path, $tmp );
			$id = media_handle_sideload(
				array(
					'name'     => basename( $item['file'] ),
					'tmp_name' => $tmp,
				),
				0,
				$item['title'] ?? null
			);
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( "{$item['file']}: " . $id->get_error_message() );
				continue;
			}
			update_post_meta( $id, '_djm_seed_file', $item['file'] );
			update_post_meta( $id, '_wp_attachment_image_alt', $item['alt'] ?? '' );
			$this->media[ $key ] = (int) $id;
			WP_CLI::log( "Imported {$item['file']} (#$id)" );
		}
	}

	/**
	 * Find a page by slug, creating an empty one if it doesn't exist yet.
	 *
	 * @param string $slug  Page slug.
	 * @param string $title Page title.
	 * @return int Page ID.
	 */
	private function reserve_page( $slug, $title ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			return (int) $existing->ID;
		}
		return (int) wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_name'   => $slug,
			)
		);
	}

	/**
	 * Create or update a page by slug.
	 *
	 * @param string $slug Page slug.
	 * @param array  $page Title, blocks, seo.
	 * @return int Page ID.
	 */
	private function upsert_page( $slug, $page ) {
		$markup = '';
		foreach ( $page['blocks'] as $block ) {
			$markup .= $this->block_markup( $block[0], $block[1] ?? array() ) . "\n\n";
		}

		$postarr = array(
			'ID'           => $this->pages[ $slug ],
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $page['title'],
			'post_name'    => $slug,
			'post_content' => trim( $markup ),
		);
		// wp_insert_post unslashes; block JSON contains backslashes.
		$id = wp_insert_post( wp_slash( $postarr ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( "$slug: " . $id->get_error_message() );
		}

		$this->write_fields( 'post_type:page', $page['seo'] ?? array(), $id );
		WP_CLI::log( "Wrote page /$slug/ (#$id)" );
		return (int) $id;
	}

	/**
	 * Serialize one ACF block with its field data in ACF's storage format.
	 *
	 * @param string $name   Block name, e.g. acf/hero.
	 * @param array  $values Field name => value.
	 */
	private function block_markup( $name, $values ) {
		$data = $this->flatten( $this->fields[ $name ] ?? array(), $values, '' );
		return serialize_block(
			array(
				'blockName'    => $name,
				'attrs'        => array(
					'name' => $name,
					'data' => (object) $data,
					'mode' => 'preview',
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
	}

	/**
	 * Convert nested values to ACF's flat meta layout: name, _name => field key,
	 * repeater rows as name_0_sub. Media placeholders ("@media:key") become IDs.
	 *
	 * @param array  $fields Field definitions.
	 * @param array  $values Values by field name.
	 * @param string $prefix Meta name prefix for repeater rows.
	 */
	private function flatten( $fields, $values, $prefix ) {
		$out = array();
		foreach ( $fields as $field ) {
			if ( '' === $field['name'] || ! array_key_exists( $field['name'], $values ) ) {
				continue;
			}
			$meta  = $prefix . $field['name'];
			$value = $values[ $field['name'] ];
			if ( 'repeater' === $field['type'] ) {
				$out[ $meta ] = count( $value );
				foreach ( array_values( $value ) as $i => $row ) {
					$out += $this->flatten( $field['sub_fields'], $row, "{$meta}_{$i}_" );
				}
			} else {
				$out[ $meta ] = $this->resolve( $value );
			}
			$out[ '_' . $meta ] = $field['key'];
		}
		return $out;
	}

	/**
	 * Replace media placeholders with attachment IDs.
	 *
	 * @param mixed $value Raw value.
	 */
	private function resolve( $value ) {
		if ( is_string( $value ) && str_starts_with( $value, '@media:' ) ) {
			return (string) ( $this->media[ substr( $value, 7 ) ] ?? '' );
		}
		if ( is_string( $value ) && str_starts_with( $value, '@page:' ) ) {
			$id = $this->pages[ substr( $value, 6 ) ] ?? 0;
			return $id ? get_permalink( $id ) : '';
		}
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'resolve' ), $value );
		}
		return $value;
	}

	/**
	 * Write field values to options or post meta, in ACF's storage format.
	 *
	 * @param string     $target Fields index key.
	 * @param array      $values Values by field name.
	 * @param int|string $where  Post ID or "options".
	 */
	private function write_fields( $target, $values, $where ) {
		foreach ( $this->flatten( $this->fields[ $target ] ?? array(), $values, '' ) as $key => $value ) {
			if ( 'options' === $where ) {
				// ACF keeps options as options_{name} and _options_{name} => field key.
				$option = str_starts_with( $key, '_' ) ? '_options_' . substr( $key, 1 ) : 'options_' . $key;
				update_option( $option, $value, false );
			} else {
				update_post_meta( $where, $key, $value );
			}
		}
	}

	/**
	 * Create or replace the header and footer menus.
	 *
	 * @param array $menus    Location => items.
	 * @param array $page_ids Slug => page ID.
	 */
	private function build_menus( $menus, $page_ids ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $menus as $location => $menu ) {
			$existing = wp_get_nav_menu_object( $menu['name'] );
			if ( $existing ) {
				wp_delete_nav_menu( $existing->term_id );
			}
			$menu_id = wp_create_nav_menu( $menu['name'] );
			foreach ( $menu['items'] as $position => $item ) {
				$args = array(
					'menu-item-title'    => $item['title'],
					'menu-item-status'   => 'publish',
					'menu-item-position' => $position + 1,
					'menu-item-classes'  => $item['class'] ?? '',
					'menu-item-target'   => $item['target'] ?? '',
				);
				if ( isset( $item['page'] ) ) {
					if ( empty( $page_ids[ $item['page'] ] ) ) {
						continue;
					}
					$args['menu-item-type']      = 'post_type';
					$args['menu-item-object']    = 'page';
					$args['menu-item-object-id'] = $page_ids[ $item['page'] ];
				} else {
					$args['menu-item-type'] = 'custom';
					$args['menu-item-url']  = $item['url'];
				}
				wp_update_nav_menu_item( $menu_id, 0, $args );
			}
			$locations[ $location ] = $menu_id;
		}
		set_theme_mod( 'nav_menu_locations', $locations );
		WP_CLI::log( 'Menus built.' );
	}
}

WP_CLI::add_command( 'djm', 'DJM_Seed_Command' );
