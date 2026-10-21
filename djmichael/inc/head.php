<?php
/**
 * Document head: per-page title and description, structured data, analytics.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

// Optional full title override per page ("SEO" field group).
add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( is_singular() && function_exists( 'get_field' ) ) {
			$custom = get_field( 'seo_title' );
			if ( $custom ) {
				return $custom;
			}
		}
		return $title;
	}
);

add_action(
	'wp_head',
	function () {
		if ( ! is_singular() || ! function_exists( 'get_field' ) ) {
			return;
		}
		$description = get_field( 'seo_description' );
		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( get_permalink() ) );
		echo '<meta name="theme-color" content="#141413">' . "\n";
	},
	5
);

// LocalBusiness on the front page. No aggregateRating: the page shows per-platform ratings only.
add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		$same_as = array();
		if ( djm_option( 'instagram_url' ) ) {
			$same_as[] = djm_option( 'instagram_url' );
		}
		foreach ( (array) djm_option( 'review_platforms', array() ) as $row ) {
			if ( is_array( $row ) && ! empty( $row['url'] ) ) {
				$same_as[] = $row['url'];
			}
		}
		$data = array_filter(
			array(
				'@context'      => 'https://schema.org',
				'@type'         => 'LocalBusiness',
				'@id'           => home_url( '/#business' ),
				'name'          => djm_option( 'business_name', get_bloginfo( 'name' ) ),
				'alternateName' => djm_option( 'owner_name' ),
				'url'           => home_url( '/' ),
				'email'         => djm_option( 'email' ),
				'areaServed'    => array_values( array_filter( array_map( 'trim', explode( "\n", (string) djm_option( 'areas_served' ) ) ) ) ),
				'sameAs'        => $same_as,
			)
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
);

// GA4, only when an ID is set and never on local/development installs.
add_action(
	'wp_head',
	function () {
		$id = djm_option( 'ga4_id' );
		if ( ! $id || in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
			return;
		}
		?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $id ); ?>');</script>
		<?php
	}
);
