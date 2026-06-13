<?php
/**
 * The template for displaying the site header.
 *
 * Two tiers:
 *   - .header-top    : contacts (phone / email) + language switcher
 *   - .header-bottom : logo + primary menu + CTA button + mobile burger
 *
 * Content comes from Site Settings (ACF options): site_logo,
 * header_facebook, header_instagram, header_tiktok, header_promo.
 * Menu = "primary-menu" location.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$logo  = get_field( 'site_logo', 'option' );
$promo = get_field( 'header_promo', 'option' );

$socials = array(
	'facebook'  => get_field( 'header_facebook', 'option' ),
	'instagram' => get_field( 'header_instagram', 'option' ),
	'tiktok'    => get_field( 'header_tiktok', 'option' ),
);

// Inline SVGs for the top-bar social icons.
$social_icons = array(
	'facebook'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H17V3.6c-.3-.04-1.3-.1-2.45-.1-2.4 0-4.05 1.47-4.05 4.17v2.23H7.7V13h2.8v8h3z"/></svg>',
	'instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r="1.1" fill="currentColor" stroke="none"/></svg>',
	'tiktok'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3c.33 2.04 1.6 3.5 3.5 3.78v2.4c-1.2 0-2.36-.38-3.4-1.02v6.34c0 3-2.2 5.5-5.2 5.5s-5.2-2.5-5.2-5.5 2.2-5.5 5.2-5.5c.3 0 .6.02.9.07v2.55a2.65 2.65 0 1 0 1.8 2.5V3h2.4z"/></svg>',
);

/**
 * Language switcher — WPML.
 *
 * Renders the active languages as plain text codes (no flags), e.g. "LV / EN".
 * The current language gets the `.active` class; the others link to their
 * translated URL so clicking switches the language. Falls back to a static
 * placeholder when WPML is not active (e.g. local dev without the plugin).
 */
$render_langs = static function () {
	$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );

	// WPML not active / no languages — keep a harmless placeholder.
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		$out  = '<a href="#" class="active">LV</a>';
		$out .= '<span class="sep">/</span>';
		$out .= '<a href="#">EN</a>';
		return $out;
	}

	$items = array();
	foreach ( $languages as $lang ) {
		$code   = strtoupper( $lang['language_code'] );
		$active = ! empty( $lang['active'] ) ? ' active' : '';
		$items[] = '<a href="' . esc_url( $lang['url'] ) . '" class="lang-link' . $active . '" hreflang="' . esc_attr( $lang['language_code'] ) . '" lang="' . esc_attr( $lang['language_code'] ) . '">' . esc_html( $code ) . '</a>';
	}

	return implode( '<span class="sep">/</span>', $items );
};

/**
 * Render the logo.
 */
$render_logo = static function () use ( $logo ) {
	$out = '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-logo">';
	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		$out .= '<img src="' . esc_url( $logo['url'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	}
	$out .= '</a>';
	return $out;
};

/**
 * Render the social icon links (only the ones that have a URL).
 */
$render_socials = static function () use ( $socials, $social_icons ) {
	$out = '';
	foreach ( $socials as $key => $url ) {
		if ( empty( $url ) || empty( $social_icons[ $key ] ) ) {
			continue;
		}
		$out .= '<a class="header-social header-social--' . esc_attr( $key ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( ucfirst( $key ) ) . '">' . $social_icons[ $key ] . '</a>';
	}
	return $out;
};

$menu_args = array(
	'theme_location' => 'primary-menu',
	'container'      => false,
	'items_wrap'     => '<ul>%3$s</ul>',
	'echo'           => false,
	'fallback_cb'    => false,
);

$out = '';

$out .= '<header id="mainHeader">';

	// ---- Top header ----
	$out .= '<div class="header-top">';
		$out .= '<div class="container header-top__inner">';
			// Left: social icons
			$out .= '<div class="header-socials">' . $render_socials() . '</div>';
			// Centre: promo / announcement text
			$out .= '<div class="header-promo">' . ( $promo ? wp_kses_post( $promo ) : '' ) . '</div>';
			// Right: language switcher (placeholder)
			$out .= '<div class="lang-switcher">' . $render_langs() . '</div>';
		$out .= '</div>';
	$out .= '</div>';

	// ---- Bottom header ----
	$out .= '<div class="header-bottom">';
		$out .= '<div class="container header-bottom__inner">';

			$out .= $render_logo();

			$out .= '<nav class="desktop-nav">' . wp_nav_menu( $menu_args ) . '</nav>';

			$out .= '<div class="header-actions">';
				$out .= '<button class="burger-btn" aria-label="' . esc_attr__( 'Menu', 'usmasmuiza' ) . '"><span></span><span></span><span></span></button>';
			$out .= '</div>';

		$out .= '</div>';
	$out .= '</div>';

	// ---- Mobile sidebar ----
	$out .= '<aside class="mobile-sidebar">';

		$out .= '<button class="close-btn" aria-label="' . esc_attr__( 'Close', 'usmasmuiza' ) . '">';
			$out .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		$out .= '</button>';

		$out .= '<nav class="mobile-nav">' . wp_nav_menu( $menu_args ) . '</nav>';

		$socials_html = $render_socials();
		if ( $socials_html ) {
			$out .= '<div class="header-socials mobile-socials">' . $socials_html . '</div>';
		}

		$langs = $render_langs();
		if ( $langs ) {
			$out .= '<div class="mobile-lang-switcher">' . $langs . '</div>';
		}

	$out .= '</aside>';

	// Backdrop behind the open mobile sidebar.
	$out .= '<div class="mobile-overlay"></div>';

$out .= '</header>';

echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
