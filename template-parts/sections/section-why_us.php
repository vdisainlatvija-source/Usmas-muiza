<?php
/**
 * Section: Why Us
 *
 * ACF Flexible Content Layout: why_us
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label       = get_sub_field( 'label' );
$title       = get_sub_field( 'title' );
$description = get_sub_field( 'description' );
$points      = get_sub_field( 'points' );
$stat_number = get_sub_field( 'stat_number' );
$stat_text   = get_sub_field( 'stat_text' );

$out = '';

$out .= '<section class="section-why_us" id="why-us">';
	$out .= '<div class="container">';

		// Header — label left, title + desc right
		$out .= '<div class="diff-header">';

			if ( $label ) {
				$out .= '<span class="diff-label" data-aos="fade-up" data-aos-duration="600">' . esc_html( $label ) . '</span>';
			}

			$out .= '<div class="diff-intro">';
				if ( $title ) {
					$out .= '<h2 data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">' . esc_html( $title ) . '</h2>';
				}
				if ( $description ) {
					$out .= '<p data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">' . esc_html( $description ) . '</p>';
				}
			$out .= '</div>';

		$out .= '</div>';

		// Content area — checkmarks left, stat right
		$out .= '<div class="diff-content">';

			// Checkmark list
			if ( $points ) {
				$out .= '<ul class="diff-points">';
					$delay = 0;
					foreach ( $points as $point ) {
						if ( ! empty( $point['text'] ) ) {
							$out .= '<li data-aos="fade-up" data-aos-duration="600" data-aos-delay="' . $delay . '">';
								$out .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#EF9B1F"/><path d="M7.5 12.5L10.5 15.5L16.5 9.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
								$out .= '<span>' . esc_html( $point['text'] ) . '</span>';
							$out .= '</li>';
							$delay += 100;
						}
					}
				$out .= '</ul>';
			}

			// Stat number
			if ( $stat_number ) {
				// Extract number and suffix (e.g. "10+" => 10, "+")
				preg_match( '/(\d+)(.*)/', $stat_number, $matches );
				$num = isset( $matches[1] ) ? intval( $matches[1] ) : 0;
				$suffix = isset( $matches[2] ) ? $matches[2] : '';

				$out .= '<div class="diff-stat" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">';
					$out .= '<span class="stat-number" data-count="' . esc_attr( $num ) . '" data-suffix="' . esc_attr( $suffix ) . '">0' . esc_html( $suffix ) . '</span>';
					if ( $stat_text ) {
						$out .= '<span class="stat-text">' . esc_html( $stat_text ) . '</span>';
					}
				$out .= '</div>';
			}

		$out .= '</div>';

	$out .= '</div>';
$out .= '</section>';

echo $out;
