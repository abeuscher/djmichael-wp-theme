<?php
/**
 * Small helpers shared by templates and blocks.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

/**
 * A value from Site settings.
 *
 * @param string $name    Field name.
 * @param mixed  $default Fallback when empty or ACF is missing.
 */
function djm_option( $name, $default = '' ) {
	$value = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;
	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * URL of the Contact page (every "Contact me" button points here).
 */
function djm_contact_url() {
	$page = djm_option( 'contact_page' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return home_url( '/contact/' );
}

/**
 * Primary button label.
 */
function djm_cta_label() {
	return djm_option( 'cta_label', __( 'Contact me', 'djmichael' ) );
}

/**
 * Inline SVG icons used across templates. Output is static markup.
 *
 * @param string $name Icon name.
 */
function djm_icon( $name ) {
	$icons = array(
		'external' => '<svg class="icon icon--external" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M4.5 2.5h5v5M9.5 2.5l-7 7"/></svg>',
		'play'     => '<svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M6 3.5v13l11-6.5z"/></svg>',
	);
	return $icons[ $name ] ?? '';
}

/**
 * "(opens in a new tab)" for screen readers.
 */
function djm_new_tab_label() {
	return '<span class="visually-hidden"> ' . esc_html__( '(opens in a new tab)', 'djmichael' ) . '</span>';
}

/**
 * The <dl class="stats"> list shared by the About blocks.
 *
 * @param array  $rows  Repeater rows with "label" and "value".
 * @param string $class Extra class for the list.
 */
function djm_stats( $rows, $class = '' ) {
	$rows = array_filter( (array) $rows, 'is_array' );
	if ( ! $rows ) {
		return '';
	}
	$out = '<dl class="stats' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '">';
	foreach ( $rows as $row ) {
		if ( empty( $row['label'] ) && empty( $row['value'] ) ) {
			continue;
		}
		$out .= '<div class="stats__item">';
		$out .= '<dt class="stats__label">' . esc_html( $row['label'] ?? '' ) . '</dt>';
		$out .= '<dd class="stats__value">' . esc_html( $row['value'] ?? '' ) . '</dd>';
		$out .= '</div>';
	}
	return $out . '</dl>';
}

/**
 * A URL as link text, the way the prototype shows it: no scheme, no
 * trailing slash. "https://example.com/path/" becomes "example.com/path".
 *
 * @param string $url Full URL.
 */
function djm_link_label( $url ) {
	return rtrim( preg_replace( '#^https?://#i', '', (string) $url ), '/' );
}
