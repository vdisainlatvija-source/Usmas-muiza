<?php
/**
 * Section: Mission
 *
 * ACF Flexible Content Layout: mission
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label            = get_sub_field( 'label' );
$text             = get_sub_field( 'text' );
$background_image = get_sub_field( 'background_image' );
$vector_overlay   = get_sub_field( 'vector_overlay' );

$out = '';

$out .= '<section class="section-mission" id="mission">';

	// Background image
	if ( $background_image && is_array( $background_image ) ) {
		$out .= '<div class="mission-bg">';
			$out .= '<img src="' . esc_url( $background_image['url'] ) . '" alt="' . esc_attr( $background_image['alt'] ) . '">';
			$out .= '<div class="mission-overlay"></div>';
		$out .= '</div>';
	}

	// Decorative vector overlay
	if ( $vector_overlay && is_array( $vector_overlay ) ) {
		$out .= '<div class="mission-vector">';
			$out .= '<img src="' . esc_url( $vector_overlay['url'] ) . '" alt="">';
		$out .= '</div>';
	}

	$out .= '<div class="container">';

		if ( $label ) {
			$out .= '<span class="mission-label" data-aos="fade-right" data-aos-duration="600">' . esc_html( $label ) . '</span>';
		}

		if ( $text ) {
			$out .= '<p data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">' . esc_html( $text ) . '</p>';
		}

	$out .= '</div>';

$out .= '</section>';

echo $out;
