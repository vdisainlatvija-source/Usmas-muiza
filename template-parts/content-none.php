<?php
/**
 * Template part for displaying a message when no posts are found
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$out = '';
$out .= '<section class="no-results">';
	$out .= '<div class="container">';
		$out .= '<h1>' . esc_html__( 'Nothing found', 'usmasmuiza' ) . '</h1>';
		$out .= '<p>' . esc_html__( 'Sorry, nothing matched your search criteria.', 'usmasmuiza' ) . '</p>';
	$out .= '</div>';
$out .= '</section>';

echo $out;
