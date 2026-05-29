<?php
/**
 * The template for displaying footer.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ACF Options Fields
$cta_label       = get_field( 'footer_cta_label', 'option' );
$cta_title       = get_field( 'footer_cta_title', 'option' );
$cta_text        = get_field( 'footer_cta_text', 'option' );
$cta_button      = get_field( 'footer_cta_button', 'option' );
$email_text      = get_field( 'footer_email_text', 'option' );
$contacts        = get_field( 'footer_contacts', 'option' );
$copyright       = get_field( 'footer_copyright', 'option' );
$vector_overlay  = get_field( 'footer_vector', 'option' );
$privacy_link    = get_field( 'footer_privacy_link', 'option' );

$out = '';

$out .= '<div id="contact-us"></div>';
$out .= '<footer id="siteFooter">';

	// Vector overlay
	if ( $vector_overlay && is_array( $vector_overlay ) ) {
		$out .= '<div class="footer-vector">';
			$out .= '<img src="' . esc_url( $vector_overlay['url'] ) . '" alt="">';
		$out .= '</div>';
	}

	$out .= '<div class="container">';

		// CTA Section
		$out .= '<div class="footer-cta">';

			$out .= '<div class="footer-cta-left" data-aos="fade-up" data-aos-duration="800">';
				if ( $cta_label ) {
					$out .= '<span class="footer-label">' . esc_html( $cta_label ) . '</span>';
				}
				if ( $cta_title ) {
					$out .= '<h2>' . nl2br( esc_html( $cta_title ) ) . '</h2>';
				}
			$out .= '</div>';

			$out .= '<div class="footer-cta-right" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">';
				if ( $cta_text ) {
					$out .= '<p>' . esc_html( $cta_text ) . '</p>';
				}
				if ( $cta_button ) {
					$btn_url   = is_array( $cta_button ) ? $cta_button['url'] : '#';
					$btn_title = is_array( $cta_button ) ? $cta_button['title'] : $cta_button;
					$out .= '<div class="button-entrance-box" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="400">';
						$out .= '<a href="' . esc_url( $btn_url ) . '" class="btn-round" data-popup="contact">' . esc_html( $btn_title ) . '</a>';
					$out .= '</div>';
				}
			$out .= '</div>';

		$out .= '</div>';

		// Contact info
		$out .= '<div class="footer-contacts">';

			$out .= '<div class="footer-contacts-inner">';

				if ( $email_text ) {
					$out .= '<div class="footer-email" data-aos="fade-up" data-aos-duration="800">';
						$out .= wp_kses_post( $email_text );
					$out .= '</div>';
				}

				if ( $contacts ) {
					$out .= '<div class="footer-people">';
						$person_delay = 100;
						foreach ( $contacts as $contact ) {
							$out .= '<div class="footer-person" data-aos="fade-up" data-aos-duration="800" data-aos-delay="' . $person_delay . '">';

								if ( ! empty( $contact['photo'] ) && is_array( $contact['photo'] ) ) {
									$out .= '<div class="person-photo">';
										$out .= '<img src="' . esc_url( $contact['photo']['url'] ) . '" alt="' . esc_attr( $contact['photo']['alt'] ) . '">';
									$out .= '</div>';
								}

								$out .= '<div class="person-info">';
									if ( ! empty( $contact['name'] ) ) {
										$out .= '<strong>' . esc_html( $contact['name'] ) . '</strong>';
									}
									if ( ! empty( $contact['company'] ) ) {
										$out .= '<span class="person-company">' . esc_html( $contact['company'] ) . '</span>';
									}
									if ( ! empty( $contact['phone'] ) ) {
										$out .= '<a href="tel:' . esc_attr( preg_replace( '/\s+/', '', $contact['phone'] ) ) . '">' . esc_html( $contact['phone'] ) . '</a>';
									}
									if ( ! empty( $contact['email'] ) ) {
										$out .= '<a href="mailto:' . esc_attr( $contact['email'] ) . '">' . esc_html( $contact['email'] ) . '</a>';
									}
								$out .= '</div>';

							$out .= '</div>';
							$person_delay += 200;
						}
					$out .= '</div>';
				}

			$out .= '</div>';

			// Copyright
			if ( $copyright ) {
				$out .= '<div class="footer-copyright">';
				if ( $copyright ) {
					$out .= '<p>' . esc_html( $copyright ) . '</p>';
				}
				if ( $privacy_link && is_array( $privacy_link ) ) {
					$out .= '<a href="' . esc_url( $privacy_link['url'] ) . '"' . ( ! empty( $privacy_link['target'] ) ? ' target="' . esc_attr( $privacy_link['target'] ) . '"' : '' ) . '>' . esc_html( $privacy_link['title'] ) . '</a>';
				}
			$out .= '</div>';
			}

		$out .= '</div>';

	$out .= '</div>';

$out .= '</footer>';

echo $out;

// Contact Popup
get_template_part( 'template-parts/popup-contact' );
