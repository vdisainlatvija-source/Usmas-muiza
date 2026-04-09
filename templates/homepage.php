<?php
/**
 * Template name: Homepage
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

echo '<main id="homeMain">';

$acf_flexible_source = null;

if ( is_tax() || is_category() || is_tag() ) {
	$term = get_queried_object();
	if ( $term instanceof WP_Term ) {
		$acf_flexible_source = $term;
	}
} else {
	$acf_flexible_source = headofsales_get_acf_post_id( 'sections' );
}

if ( $acf_flexible_source && have_rows( 'sections', $acf_flexible_source ) ) {
	while ( have_rows( 'sections', $acf_flexible_source ) ) {
		the_row();
		$layout = get_row_layout();

		switch ( $layout ) {
			case 'main_hero':
				get_template_part( 'template-parts/sections/section-main_hero' );
				break;
			case 'services':
				get_template_part( 'template-parts/sections/section-services' );
				break;
			case 'why_us':
				get_template_part( 'template-parts/sections/section-why_us' );
				break;
			case 'about':
				get_template_part( 'template-parts/sections/section-about' );
				break;
			case 'mission':
				get_template_part( 'template-parts/sections/section-mission' );
				break;
			case 'faq':
				get_template_part( 'template-parts/sections/section-faq' );
				break;
		}
	}
}

echo '</main>';

get_footer();
