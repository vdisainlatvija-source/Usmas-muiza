<?php
/**
 * Template name: Homepage
 *
 * @package usmasmuiza
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
	$acf_flexible_source = usmasmuiza_get_acf_post_id( 'sections' );
}

if ( $acf_flexible_source && have_rows( 'sections', $acf_flexible_source ) ) {
	while ( have_rows( 'sections', $acf_flexible_source ) ) {
		the_row();
		$layout = get_row_layout();

		// Loads template-parts/sections/section-{$layout}.php when it exists.
		get_template_part( 'template-parts/sections/section', $layout );
	}
}

echo '</main>';

get_footer();
