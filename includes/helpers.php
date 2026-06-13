<?php
/**
* WP helpers
*
* @package usmasmuiza
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Allow SVG support - restricted to administrators only
add_filter( 'upload_mimes', function( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
} );

/**
 * Translate a theme UI string (e.g. button labels) per language.
 *
 * The theme ships no .mo files and the WPML String Translation route proved
 * fragile (the source language was recorded from whichever page first rendered
 * the string), so translations live here in code instead — deterministic and
 * deployed with the theme. The Latvian source is returned for the default
 * language; non-default languages use the map below when an entry exists.
 *
 * To add a translation: add the Latvian source as the key under the language.
 *
 * @param string $string Latvian source text.
 * @param string $name   Unused; kept for call-site compatibility.
 * @return string
 */
function usmasmuiza_ui_string( $string, $name = '' ) {
	$current = apply_filters( 'wpml_current_language', null );
	$default = apply_filters( 'wpml_default_language', null );

	// Default language (or WPML inactive) → return the Latvian source as-is.
	if ( ! $current || $current === $default ) {
		return $string;
	}

	$translations = array(
		'en' => array(
			'Skatīt vairāk'       => 'See more',
			'Vairāk informācijas' => 'More information',
			'Rezervēt'            => 'Book now',
			'Uzzināt vairāk'      => 'Learn more',
			'Lasīt vairāk'        => 'Read more',
		),
	);

	return isset( $translations[ $current ][ $string ] ) ? $translations[ $current ][ $string ] : $string;
}

/**
 * Point an internal link at its current-language equivalent (WPML).
 *
 * ACF link fields store the Latvian page URL (e.g. /kontakti/?temats=x#anchor),
 * so on the English side a CTA still lands on the Latvian contacts page. This
 * resolves the URL to its post, maps it to the current language's translation,
 * and rebuilds the permalink — keeping any ?query and #fragment. Anchors,
 * mailto:/tel:, external links and non-page URLs are returned untouched, and
 * it always returns a valid URL (falls back to the original).
 *
 * @param string $url
 * @return string
 */
function usmasmuiza_localize_url( $url ) {
	if ( ! is_string( $url ) || '' === $url ) {
		return $url;
	}
	if ( preg_match( '#^(\#|mailto:|tel:|javascript:)#i', $url ) ) {
		return $url;
	}
	if ( ! has_filter( 'wpml_object_id' ) ) {
		return $url;
	}

	// Set the query string and fragment aside; resolve only the bare path.
	$frag = '';
	if ( false !== ( $h = strpos( $url, '#' ) ) ) {
		$frag = substr( $url, $h );
		$url  = substr( $url, 0, $h );
	}
	$query = '';
	if ( false !== ( $q = strpos( $url, '?' ) ) ) {
		$query = substr( $url, $q );
		$url   = substr( $url, 0, $q );
	}

	// Not internal content (external, archive, home) → leave as typed.
	$post_id = url_to_postid( $url );
	if ( ! $post_id ) {
		return $url . $query . $frag;
	}

	$type = get_post_type( $post_id ) ?: 'page';
	$tid  = apply_filters( 'wpml_object_id', $post_id, $type, true );
	$new  = get_permalink( $tid ? $tid : $post_id );
	if ( ! is_string( $new ) || '' === $new ) {
		$new = $url;
	}

	if ( '' !== $query ) {
		$new .= ( false === strpos( $new, '?' ) ? '?' : '&' ) . ltrim( $query, '?' );
	}
	return $new . $frag;
}

/**
 * Get template part with arguments and return the output as a string.
 */
function get_component($slug, $name = null, $args = []) {
    ob_start();
    get_template_part($slug, $name, $args);
    return ob_get_clean();
}

/**
 * Social icon links (from the header_* Site Settings fields).
 * Returns the <a> markup for each social with a URL set, or '' if none.
 */
function usmasmuiza_social_icons() {
	$socials = array(
		'facebook'  => get_field( 'header_facebook', 'option' ),
		'instagram' => get_field( 'header_instagram', 'option' ),
		'tiktok'    => get_field( 'header_tiktok', 'option' ),
	);
	$icons = array(
		'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H17V3.6c-.3-.04-1.3-.1-2.45-.1-2.4 0-4.05 1.47-4.05 4.17v2.23H7.7V13h2.8v8h3z"/></svg>',
		'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r="1.1" fill="currentColor" stroke="none"/></svg>',
		'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.33 2.04 1.6 3.5 3.5 3.78v2.4c-1.2 0-2.36-.38-3.4-1.02v6.34c0 3-2.2 5.5-5.2 5.5s-5.2-2.5-5.2-5.5 2.2-5.5 5.2-5.5c.3 0 .6.02.9.07v2.55a2.65 2.65 0 1 0 1.8 2.5V3h2.4z"/></svg>',
	);

	$out = '';
	foreach ( $socials as $key => $url ) {
		if ( empty( $url ) || empty( $icons[ $key ] ) ) {
			continue;
		}
		$out .= '<a class="social-icon social-icon--' . esc_attr( $key ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( ucfirst( $key ) ) . '">' . $icons[ $key ] . '</a>';
	}
	return $out;
}
