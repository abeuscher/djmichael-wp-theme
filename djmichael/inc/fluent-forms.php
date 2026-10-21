<?php
/**
 * Fluent Forms integration. The contact form itself is built by the seed;
 * assets/css/fluent-forms.css restyles it to the prototype.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

// The notification email goes to Michael, so it shouldn't advertise the plugin.
add_filter( 'fluentform/email_footer', fn() => '' );
