<?php
/**
 * Section: Services
 *
 * ACF Flexible Content Layout: services
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label       = get_sub_field( 'label' );
$title       = get_sub_field( 'title' );
$description = get_sub_field( 'description' );
$services    = get_sub_field( 'services' );

$out = '';

$out .= '<section class="section-services" id="services">';
	$out .= '<div class="container">';

		// Header row — label left, title + description right
		$out .= '<div class="services-header">';

			if ( $label ) {
				$out .= '<span class="services-label" data-aos="fade-up" data-aos-duration="600">' . esc_html( $label ) . '</span>';
			}

			$out .= '<div class="services-intro">';
				if ( $title ) {
					$out .= '<h2 data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">' . esc_html( $title ) . '</h2>';
				}
				if ( $description ) {
					$out .= '<p data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">' . esc_html( $description ) . '</p>';
				}
			$out .= '</div>';

		$out .= '</div>';

		// Service cards grid
		if ( $services ) {
			$out .= '<div class="services-grid">';
				$delay = 0;
				foreach ( $services as $service ) {
					$out .= '<div class="service-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="' . $delay . '">';

						if ( ! empty( $service['icon'] ) && is_array( $service['icon'] ) ) {
							$out .= '<div class="service-icon">';
								$out .= '<img src="' . esc_url( $service['icon']['url'] ) . '" alt="' . esc_attr( $service['icon']['alt'] ) . '">';
							$out .= '</div>';
						}

						if ( ! empty( $service['title'] ) ) {
							$out .= '<h4>' . esc_html( $service['title'] ) . '</h4>';
						}

						if ( ! empty( $service['description'] ) ) {
							$out .= '<p>' . esc_html( $service['description'] ) . '</p>';
						}

					$out .= '</div>';
					$delay += 100;
				}
			$out .= '</div>';
		}

	$out .= '</div>';
$out .= '</section>';

echo $out;
