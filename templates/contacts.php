<?php
/*
Template name: Contacts
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

echo '<main id="contactsMain">';

// ACF Flexible Content Sections
$sections_post_id = headofsales_get_acf_post_id( 'sections' );

if ( have_rows( 'sections', $sections_post_id ) ) {
	while ( have_rows( 'sections', $sections_post_id ) ) {
		the_row();
		$layout = get_row_layout();
		$template_path = 'template-parts/sections/section-' . str_replace( '_', '_', $layout );
		get_template_part( $template_path );
	}
}

// Contact Form Section
$form_id = get_field( 'popup_form_id', 'option' );

if ( $form_id && function_exists( 'gravity_form' ) ) {
	$out = '';
	$out .= '<section class="section-form">';
		$out .= '<div class="container">';
			ob_start();
			gravity_form( $form_id, false, false, false, null, true );
			$out .= ob_get_clean();
		$out .= '</div>';
	$out .= '</section>';
	echo $out;
}

echo '</main>';

get_footer();
