<?php
/**
 * Template name: Page – Sections
 *
 * Flexible-content page for inner content pages (Viesnīca, Restorāns, SPA...).
 * Builds the page from the "sections" flexible content field, loading
 * template-parts/sections/section-{layout}.php for each row.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

echo '<main id="pageMain">';

$acf_flexible_source = usmasmuiza_get_acf_post_id( 'sections' );

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
