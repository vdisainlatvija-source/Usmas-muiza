<?php
/**
 * Custom post types & taxonomies.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gallery — each post is one gallery image (its featured image), filed under
 * one or more "Gallery Category" terms. Used by the homepage gallery section
 * and (later) the full gallery archive page.
 */
function usmasmuiza_register_gallery() {

	register_post_type(
		'gallery',
		array(
			'labels'       => array(
				'name'          => __( 'Gallery', 'usmasmuiza' ),
				'singular_name' => __( 'Gallery Image', 'usmasmuiza' ),
				'add_new_item'  => __( 'Add Gallery Image', 'usmasmuiza' ),
				'edit_item'     => __( 'Edit Gallery Image', 'usmasmuiza' ),
				'all_items'     => __( 'All Images', 'usmasmuiza' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-format-gallery',
			'menu_position' => 21,
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'galerija' ),
			'show_in_rest' => false,
		)
	);

	register_post_type(
		'room',
		array(
			'labels'        => array(
				'name'          => __( 'Rooms', 'usmasmuiza' ),
				'singular_name' => __( 'Room', 'usmasmuiza' ),
				'add_new_item'  => __( 'Add Room', 'usmasmuiza' ),
				'edit_item'     => __( 'Edit Room', 'usmasmuiza' ),
				'all_items'     => __( 'All Rooms', 'usmasmuiza' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'menu_icon'     => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="#a7aaad" d="M2 5H1v10h2v-2h14v2h2V9a3 3 0 0 0-3-3H8v3H3V5H2zm3.5 1.5A1.75 1.75 0 1 1 5.5 10 1.75 1.75 0 0 1 5.5 6.5z"/></svg>' ),
			'menu_position' => 22,
			'supports'      => array( 'title', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'numurs' ),
			'show_in_rest'  => false,
		)
	);

	register_post_type(
		'offer',
		array(
			'labels'        => array(
				'name'          => __( 'Special Offers', 'usmasmuiza' ),
				'singular_name' => __( 'Offer', 'usmasmuiza' ),
				'add_new_item'  => __( 'Add Offer', 'usmasmuiza' ),
				'edit_item'     => __( 'Edit Offer', 'usmasmuiza' ),
				'all_items'     => __( 'All Offers', 'usmasmuiza' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'menu_icon'     => 'dashicons-tag',
			'menu_position' => 23,
			'supports'      => array( 'title', 'thumbnail' ),
			'rewrite'       => array( 'slug' => 'piedavajums' ),
			'show_in_rest'  => false,
		)
	);

	register_post_type(
		'jaunums',
		array(
			'labels'        => array(
				'name'          => __( 'News', 'usmasmuiza' ),
				'singular_name' => __( 'Jaunums', 'usmasmuiza' ),
				'add_new_item'  => __( 'Add News Item', 'usmasmuiza' ),
				'edit_item'     => __( 'Edit News Item', 'usmasmuiza' ),
				'all_items'     => __( 'All News', 'usmasmuiza' ),
			),
			'public'        => true,
			'has_archive'   => false, // /jaunumi/ is a Page; avoid the archive slug clash
			'menu_icon'     => 'dashicons-megaphone',
			'menu_position' => 24,
			'supports'      => array( 'title', 'thumbnail', 'editor' ),
			'rewrite'       => array( 'slug' => 'jaunums' ),
			'show_in_rest'  => false,
		)
	);

	register_taxonomy(
		'gallery_category',
		'gallery',
		array(
			'labels'            => array(
				'name'          => __( 'Gallery Categories', 'usmasmuiza' ),
				'singular_name' => __( 'Gallery Category', 'usmasmuiza' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'galerija-kategorija' ),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'init', 'usmasmuiza_register_gallery' );
