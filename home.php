<?php
/**
 * The template for displaying the blog posts page.
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

echo '<main id="blogMain">';

// Hero Section - pull content from Site Settings
$blog_hero = get_field( 'blog_hero', 'option' );

if ( ! empty( $blog_hero ) ) {
	$out = '';
	$out .= '<section class="section-hero">';
		$out .= '<div class="container">';
			$out .= $blog_hero;
		$out .= '</div>';
	$out .= '</section>';
	echo $out;
}

// Blog Posts
if ( have_posts() ) {
	echo '<section class="section-blog">';
		echo '<div class="container">';
			echo '<div class="blog-grid">';

			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/components/component', 'blogcard' );
			}

			echo '</div>';

			// Pagination
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			) );

		echo '</div>';
	echo '</section>';
} else {
	get_template_part( 'template-parts/content', 'none' );
}

echo '</main>';

get_footer();
