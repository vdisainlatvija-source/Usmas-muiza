<?php
/**
 * Section: Main Hero
 *
 * ACF Flexible Content Layout: main_hero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label           = get_sub_field( 'label' );
$title           = get_sub_field( 'title' );
$description     = get_sub_field( 'description' );
$button          = get_sub_field( 'button' );
$background_image = get_sub_field( 'background_image' );
$vector_overlay  = get_sub_field( 'vector_overlay' );

$out = '';

$out .= '<section class="section-main_hero">';

	// Background image
	if ( $background_image && is_array( $background_image ) ) {
		$out .= '<div class="hero-bg">';
			$out .= '<img src="' . esc_url( $background_image['url'] ) . '" alt="' . esc_attr( $background_image['alt'] ) . '">';
			$out .= '<div class="hero-overlay"></div>';
		$out .= '</div>';
	}

	// Decorative vector overlay
	if ( $vector_overlay && is_array( $vector_overlay ) ) {
		$out .= '<div class="hero-vector">';
			$out .= '<img src="' . esc_url( $vector_overlay['url'] ) . '" alt="">';
		$out .= '</div>';
	}

	$out .= '<div class="container">';

		// Left column
		$out .= '<div class="hero-left">';

			if ( $label ) {
				$out .= '<span class="hero-label" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">' . esc_html( $label ) . '</span>';
			}

			if ( $title ) {
				$out .= '<h1 data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">' . nl2br( esc_html( $title ) ) . '</h1>';
			}

		$out .= '</div>';

		// Right column
		$out .= '<div class="hero-right">';

			if ( $description ) {
				$out .= '<p data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">' . esc_html( $description ) . '</p>';
			}

			if ( $button ) {
				$btn_url   = is_array( $button ) ? $button['url'] : '#';
				$btn_title = is_array( $button ) ? $button['title'] : $button;
				$btn_target = ( is_array( $button ) && ! empty( $button['target'] ) ) ? ' target="' . esc_attr( $button['target'] ) . '"' : '';
				$out .= '<div class="button-entrance-box" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="800">';
				$out .= '<a href="' . esc_url( $btn_url ) . '" class="btn-round" data-popup="contact"' . $btn_target . '>' . esc_html( $btn_title ) . '</a>';
				$out .= '</div>';
			}

		$out .= '</div>';

	$out .= '</div>';

$out .= '</section>';

echo $out;
