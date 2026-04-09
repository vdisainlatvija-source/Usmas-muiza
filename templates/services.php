<?php
/*
Template name: Services
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

echo '<main id="servicesMain">';

// ACF Flexible Content Sections
// Uses helper to fall back to original language post in WPML advanced mode
$sections_post_id = headofsales_get_acf_post_id( 'sections' );

if ( have_rows( 'sections', $sections_post_id ) ) {
	while ( have_rows( 'sections', $sections_post_id ) ) {
		the_row();
		$layout = get_row_layout();
		$template_path = 'template-parts/sections/section-' . str_replace( '_', '_', $layout );
		get_template_part( $template_path );
	}
}

echo '</main>';

get_footer();
