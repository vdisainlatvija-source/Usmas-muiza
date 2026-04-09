<?php
/*
Template name: About
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

echo '<main id="aboutMain">';

// ACF Flexible Content Sections
// Uses helper to fall back to original language post in WPML advanced mode
$sections_post_id = headofsales_get_acf_post_id( 'sections' );

if ( have_rows( 'sections', $sections_post_id ) ) {
	while ( have_rows( 'sections', $sections_post_id ) ) {
		the_row();
		$layout = get_row_layout();

		// Featured image section - inline since it's simple and unique to this page
		if ( $layout === 'featured_image' ) {
			$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
			if ( $featured_image ) {
				echo '<section class="section-featured_image" style="background-image: url(' . esc_url( $featured_image ) . ');"></section>';
			}
		} else {
			$template_path = 'template-parts/sections/section-' . $layout;
			get_template_part( $template_path );
		}
	}
}

echo '</main>';

get_footer();
