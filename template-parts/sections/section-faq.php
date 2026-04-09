<?php
/**
 * Section: FAQ
 *
 * ACF Flexible Content Layout: faq
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label     = get_sub_field( 'label' );
$title     = get_sub_field( 'title' );
$questions = get_sub_field( 'questions' );

$out = '';

$out .= '<section class="section-faq" id="faq">';
	$out .= '<div class="container">';

		// Header — label left, title right
		$out .= '<div class="faq-header">';

			if ( $label ) {
				$out .= '<span class="faq-label" data-aos="fade-up" data-aos-duration="600">' . esc_html( $label ) . '</span>';
			}

			if ( $title ) {
				$out .= '<h2 data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">' . esc_html( $title ) . '</h2>';
			}

		$out .= '</div>';

		// Accordion
		if ( $questions ) {
			$out .= '<div class="faq-list">';
				$delay = 0;
				foreach ( $questions as $q ) {
					if ( ! empty( $q['question'] ) ) {
						$out .= '<div class="faq-item" data-aos="fade-up" data-aos-duration="600" data-aos-delay="' . $delay . '">';

							$out .= '<button class="faq-toggle" type="button">';
								$out .= '<h3>' . esc_html( $q['question'] ) . '</h3>';
								$out .= '<div class="faq-icon"><span></span><span></span></div>';
							$out .= '</button>';

							if ( ! empty( $q['answer'] ) ) {
								$out .= '<div class="faq-answer">';
									$out .= '<div class="faq-answer-inner">';
										$out .= wp_kses_post( $q['answer'] );
									$out .= '</div>';
								$out .= '</div>';
							}

						$out .= '</div>';
						$delay += 100;
					}
				}
			$out .= '</div>';
		}

	$out .= '</div>';
$out .= '</section>';

echo $out;
