<?php
/**
 * Default Page Template
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$out = '';

$out .= '<main id="pageMain">';

	// Hero Section
	$out .= '<section class="section-hero">';
		$out .= '<div class="container">';
			$out .= '<h1>' . esc_html( get_the_title() ) . '</h1>';
		$out .= '</div>';
	$out .= '</section>';

	// Content Section
	$out .= '<section class="section-content">';
		$out .= '<div class="container">';
			$out .= '<article class="content">';
				$out .= apply_filters( 'the_content', get_the_content() );
			$out .= '</article>';
		$out .= '</div>';
	$out .= '</section>';

$out .= '</main>';

echo $out;

get_footer();
