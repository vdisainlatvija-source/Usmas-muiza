<?php
/**
 * Component: Project Postcard XLarge
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = $args['post_id'] ?? get_the_ID();
$title     = get_the_title( $post_id );
$permalink = get_permalink( $post_id );
$excerpt   = get_the_excerpt( $post_id );
$thumbnail = get_the_post_thumbnail( $post_id, 'hero' );
$tags      = get_the_tags( $post_id );

$out = '';
$out .= '<a href="' . esc_url( $permalink ) . '" class="component-postcard_xlarge">';
	if ( $thumbnail ) {
		$out .= '<div class="postcard-image">' . $thumbnail . '</div>';
	}
	$out .= '<div class="postcard-content">';
		$out .= '<h2 class="postcard-title">' . esc_html( $title ) . '</h2>';
		if ( $excerpt ) {
			$out .= '<p class="postcard-excerpt">' . esc_html( $excerpt ) . '</p>';
		}
		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			$out .= '<div class="postcard-tags">';
			foreach ( $tags as $tag ) {
				$out .= '<span class="tag">' . esc_html( $tag->name ) . '</span>';
			}
			$out .= '</div>';
		}
	$out .= '</div>';
$out .= '</a>';

echo $out;
