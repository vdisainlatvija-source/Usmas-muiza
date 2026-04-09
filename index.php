<?php
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_404() ) {
	add_filter('body_class', function ($classes) {
        $classes[] = 'dark-theme';
        return $classes;
    });
}
get_header();

if ( is_404() ) {
	get_template_part( 'template-parts/404' );
} elseif ( have_posts() ) {
	echo '<main>';
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_type() );
	}
	echo '</main>';
} else {
	get_template_part( 'template-parts/content', 'none' );
}

get_footer();