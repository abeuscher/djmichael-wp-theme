<?php
/**
 * DJ Michael theme bootstrap.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

define( 'DJM_VERSION', '0.1.0' );
define( 'DJM_DIR', get_template_directory() );
define( 'DJM_URI', get_template_directory_uri() );

require DJM_DIR . '/inc/setup.php';
require DJM_DIR . '/inc/assets.php';
require DJM_DIR . '/inc/acf.php';
require DJM_DIR . '/inc/template-tags.php';
require DJM_DIR . '/inc/head.php';
require DJM_DIR . '/inc/fluent-forms.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require DJM_DIR . '/inc/class-djm-seed-forms.php';
	require DJM_DIR . '/inc/class-djm-seed-command.php';
}
