<?php
/**
 * The template for displaying header.
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_logo = get_field('site_logo_white', 'option');

$out = '';

$out .= '<header id="mainHeader">';
    $out .= '<div class="container">';

        // Logo
        $out .= '<a href="' . esc_url(home_url('/')) . '" class="site-logo">';
            if (!empty($site_logo) && is_array($site_logo) && isset($site_logo['url'])) {
                $out .= '<img src="' . esc_url($site_logo['url']) . '" alt="' . esc_attr(!empty($site_logo['alt']) ? $site_logo['alt'] : get_bloginfo('name')) . '">';
            }
        $out .= '</a>';

        // Desktop Navigation
        $out .= '<nav class="desktop-nav">';
            $out .= wp_nav_menu(array(
                'theme_location' => 'primary-menu',
                'container'      => false,
                'items_wrap'     => '<ul>%3$s</ul>',
                'echo'           => false,
                'fallback_cb'    => false,
            ));
        $out .= '</nav>';

        // Language Switcher (desktop)
        $out .= '<div class="lang-switcher">';
            $languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
            if ( $languages ) {
                foreach ( $languages as $lang ) {
                    $active_class = $lang['active'] ? ' class="active"' : '';
                    $out .= '<a href="' . esc_url( $lang['url'] ) . '"' . $active_class . '>' . esc_html( strtoupper( $lang['code'] ) ) . '</a>';
                }
            }
        $out .= '</div>';

        // Mobile burger button
        $out .= '<button class="burger-btn" aria-label="' . esc_attr__('Menu', 'headofsales') . '">';
            $out .= '<span></span>';
            $out .= '<span></span>';
            $out .= '<span></span>';
        $out .= '</button>';

    $out .= '</div>';

    // Mobile Sidebar
    $out .= '<aside class="mobile-sidebar">';

        $out .= '<button class="close-btn" aria-label="' . esc_attr__('Close', 'headofsales') . '">';
            $out .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $out .= '</button>';

        $out .= '<nav class="mobile-nav">';
            $out .= wp_nav_menu(array(
                'theme_location' => 'primary-menu',
                'container'      => false,
                'items_wrap'     => '<ul>%3$s</ul>',
                'echo'           => false,
                'fallback_cb'    => false,
            ));
        $out .= '</nav>';

        // Language Switcher (mobile)
        $out .= '<div class="mobile-lang-switcher">';
            $languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
            if ( $languages ) {
                foreach ( $languages as $lang ) {
                    $active_class = $lang['active'] ? ' class="active"' : '';
                    $out .= '<a href="' . esc_url( $lang['url'] ) . '"' . $active_class . '>' . esc_html( strtoupper( $lang['code'] ) ) . '</a>';
                }
            }
        $out .= '</div>';

    $out .= '</aside>';

$out .= '</header>';

echo $out;
