<?php
/**
 * The template for displaying single blog posts.
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

echo '<main id="singleMain">';

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$title     = get_the_title();
		$excerpt   = get_the_excerpt();
		$thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'full' );

		// Hero Section
		$out = '';
		$out .= '<section class="section-hero">';
			$out .= '<div class="container">';
				$out .= '<h1>' . esc_html( $title ) . '</h1>';
				if ( $excerpt ) {
					$out .= '<p>' . esc_html( $excerpt ) . '</p>';
				}
			$out .= '</div>';
		$out .= '</section>';
		echo $out;

		// Featured Image Section
		if ( $thumbnail ) {
			$out = '';
			$out .= '<section class="section-image">';
				$out .= '<div class="container">';
					$out .= '<figure>';
						$out .= '<img src="' . esc_url( $thumbnail ) . '" alt="' . esc_attr( $title ) . '">';
					$out .= '</figure>';
				$out .= '</div>';
			$out .= '</section>';
			echo $out;
		}

		// Content Section
		$content = get_the_content();
		if ( $content ) {
			$out = '';
			$out .= '<section class="section-content">';
				$out .= '<div class="container">';
					$out .= '<article class="content">';
						$out .= apply_filters( 'the_content', $content );
					$out .= '</article>';
				$out .= '</div>';
			$out .= '</section>';
			echo $out;
		}
	}
}

echo '</main>';

get_footer();
