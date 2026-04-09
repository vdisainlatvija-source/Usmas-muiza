<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package headofsales
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
                $out .= '<h2>' . esc_html__('Page was not found', 'headofsales') . '</h2>';
                $out .= '<p>' . esc_html__('We could not have found the page you were looking for', 'headofsales') . '</p>';
            $out .= '</header>';

            // Button
            $out .= '<a href="' . esc_url(home_url('/')) . '" class="btn">';
                $out .= '<span>' . esc_html__('Back to Homepage', 'headofsales') . '</span>';
            $out .= '</a>';

        $out .= '</div>';
    $out .= '</section>';
$out .= '</main>';

echo $out;
