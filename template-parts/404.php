<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$out = '';

$out .= '<main id="errorMain">';
    $out .= '<section class="section-error">';
        $out .= '<div class="container">';

            // 404 Heading
            $out .= '<h1 class="error-code">404</h1>';

            // Content
            $out .= '<header>';
                $out .= '<h2>' . esc_html__('Page was not found', 'usmasmuiza') . '</h2>';
                $out .= '<p>' . esc_html__('We could not have found the page you were looking for', 'usmasmuiza') . '</p>';
            $out .= '</header>';

            // Button
            $out .= '<a href="' . esc_url(home_url('/')) . '" class="btn">';
                $out .= '<span>' . esc_html__('Back to Homepage', 'usmasmuiza') . '</span>';
            $out .= '</a>';

        $out .= '</div>';
    $out .= '</section>';
$out .= '</main>';

echo $out;
