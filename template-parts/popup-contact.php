<?php
/**
 * Contact Popup
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$popup_label   = get_field( 'popup_label', 'option' );
$popup_title   = get_field( 'popup_title', 'option' );
$popup_text    = get_field( 'popup_text', 'option' );
$popup_form_id = get_field( 'popup_form_id', 'option' );

$out = '';

$out .= '<div id="contactPopup" class="popup-contact">';
	$out .= '<div class="popup-contact-overlay"></div>';
	$out .= '<div class="popup-contact-inner">';

		$out .= '<button class="popup-contact-close" aria-label="Close">';
			$out .= '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
				$out .= '<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
			$out .= '</svg>';
		$out .= '</button>';

		$out .= '<div class="popup-contact-header">';
			if ( $popup_label ) {
				$out .= '<span class="popup-contact-label">' . esc_html( $popup_label ) . '</span>';
			}
			if ( $popup_title ) {
				$out .= '<h2>' . esc_html( $popup_title ) . '</h2>';
			}
			if ( $popup_text ) {
				$out .= '<p>' . esc_html( $popup_text ) . '</p>';
			}
		$out .= '</div>';

		if ( $popup_form_id && function_exists( 'gravity_form' ) ) {
			$out .= '<div class="popup-contact-form">';
				ob_start();
				gravity_form( $popup_form_id, false, false, false, null, true );
				$out .= ob_get_clean();
			$out .= '</div>';
		}

	$out .= '</div>';
$out .= '</div>';

echo $out;
