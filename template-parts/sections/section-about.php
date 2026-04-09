<?php
/**
 * Section: About
 *
 * ACF Flexible Content Layout: about
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label         = get_sub_field( 'label' );
$title         = get_sub_field( 'title' );
$vision_label  = get_sub_field( 'vision_label' );
$vision_text   = get_sub_field( 'vision_text' );
$button        = get_sub_field( 'button' );

$out = '';

$out .= '<section class="section-about" id="about">';
	$out .= '<div class="container">';

		// Header — label left, title right
		$out .= '<div class="about-header">';

			if ( $label ) {
				$out .= '<span class="about-label" data-aos="fade-left" data-aos-duration="600">' . esc_html( $label ) . '</span>';
			}

			if ( $title ) {
				$out .= '<h2 data-aos="fade-left" data-aos-duration="1000" data-aos-delay="100">' . esc_html( $title ) . '</h2>';
			}

		$out .= '</div>';

		// Bottom — vision label left, vision text + button right
		$out .= '<div class="about-bottom">';

			if ( $vision_label ) {
				$out .= '<span class="about-vision-label" data-aos="fade-right" data-aos-duration="600" data-aos-delay="200">' . esc_html( $vision_label ) . '</span>';
			}

			$out .= '<div class="about-vision-content">';

				if ( $vision_text ) {
					$out .= '<p data-aos="fade-right" data-aos-duration="800" data-aos-delay="300">' . esc_html( $vision_text ) . '</p>';
				}

			$out .= '</div>';

			if ( $button ) {
				$btn_url   = is_array( $button ) ? $button['url'] : '#';
				$btn_title = is_array( $button ) ? $button['title'] : $button;
				$btn_target = ( is_array( $button ) && ! empty( $button['target'] ) ) ? ' target="' . esc_attr( $button['target'] ) . '"' : '';
				$out .= '<div class="button-entrance-box" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="400">';
					$out .= '<a href="' . esc_url( $btn_url ) . '" class="btn-round" data-popup="contact"' . $btn_target . '>' . esc_html( $btn_title ) . '</a>';
				$out .= '</div>';
			}

		$out .= '</div>';

	$out .= '</div>';
$out .= '</section>';

echo $out;
