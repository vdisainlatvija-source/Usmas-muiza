<?php
/**
 * Archive template for Projekts post type
 *
 * @package headofsales
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$out = '';

// Get current filter values from URL
$current_tag = isset( $_GET['tag'] ) ? sanitize_text_field( $_GET['tag'] ) : '';
$current_category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';

$out .= '<main id="projektsArchive">';

	// Hero Section with Filters
	$out .= '<section class="section-projekts-hero">';
		$out .= '<div class="container">';
			$out .= '<div class="hero-content">';
				$out .= '<h1>' . esc_html__( 'projekti', 'headofsales' ) . '</h1>';

				$out .= '<aside class="hero-filters">';

					// Pakalpojumi dropdown (Tags)
					$tags = get_terms( array(
						'taxonomy'   => 'post_tag',
						'hide_empty' => true,
						'object_ids' => get_posts( array(
							'post_type'      => 'project',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						) ),
					) );

					$out .= '<nav class="filter-dropdown" data-filter="tag">';
						$tag_term = $current_tag ? get_term_by( 'slug', $current_tag, 'post_tag' ) : false;
						$tag_label = $tag_term ? $tag_term->name : __( 'pakalpojumi', 'headofsales' );
						$out .= '<button class="filter-toggle' . ( $current_tag ? ' has-selection' : '' ) . '">';
							$out .= '<span>' . esc_html( $tag_label ) . '</span>';
						$out .= '</button>';
						$out .= '<ul class="filter-options">';
							$out .= '<li><a href="' . esc_url( remove_query_arg( 'tag' ) ) . '"' . ( ! $current_tag ? ' class="active"' : '' ) . '>' . esc_html__( 'visi', 'headofsales' ) . '</a></li>';
							if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
								foreach ( $tags as $tag ) {
									$tag_url = add_query_arg( 'tag', $tag->slug, remove_query_arg( 'paged' ) );
									$out .= '<li><a href="' . esc_url( $tag_url ) . '"' . ( $current_tag === $tag->slug ? ' class="active"' : '' ) . '>' . esc_html( $tag->name ) . '</a></li>';
								}
							}
						$out .= '</ul>';
					$out .= '</nav>';

					// Nozares dropdown (Categories)
					$categories = get_terms( array(
						'taxonomy'   => 'category',
						'hide_empty' => true,
						'object_ids' => get_posts( array(
							'post_type'      => 'project',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						) ),
					) );

					$out .= '<nav class="filter-dropdown" data-filter="category">';
						$cat_term = $current_category ? get_term_by( 'slug', $current_category, 'category' ) : false;
						$cat_label = $cat_term ? $cat_term->name : __( 'nozares', 'headofsales' );
						$out .= '<button class="filter-toggle' . ( $current_category ? ' has-selection' : '' ) . '">';
							$out .= '<span>' . esc_html( $cat_label ) . '</span>';
						$out .= '</button>';
						$out .= '<ul class="filter-options">';
							$out .= '<li><a href="' . esc_url( remove_query_arg( 'category' ) ) . '"' . ( ! $current_category ? ' class="active"' : '' ) . '>' . esc_html__( 'visi', 'headofsales' ) . '</a></li>';
							if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
								foreach ( $categories as $category ) {
									// Skip categories marked as hidden from archive filter
									$hide_from_filter = get_field( 'hide_from_archive_filter', 'category_' . $category->term_id );
									if ( $hide_from_filter ) {
										continue;
									}
									$cat_url = add_query_arg( 'category', $category->slug, remove_query_arg( 'paged' ) );
									$out .= '<li><a href="' . esc_url( $cat_url ) . '"' . ( $current_category === $category->slug ? ' class="active"' : '' ) . '>' . esc_html( $category->name ) . '</a></li>';
								}
							}
						$out .= '</ul>';
					$out .= '</nav>';

				$out .= '</aside>';
			$out .= '</div>';
		$out .= '</div>';
	$out .= '</section>';

	$out .= '<section class="section-projekts-archive">';
		$out .= '<div class="container">';

			// Get all projekts
			$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			$args = array(
				'post_type'      => 'project',
				'posts_per_page' => 12,
				'paged'          => $paged,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			// Add tag filter
			if ( $current_tag ) {
				$args['tag'] = $current_tag;
			}

			// Add category filter
			if ( $current_category ) {
				$args['category_name'] = $current_category;
			}

			$projekts_query = new WP_Query( $args );

			if ( $projekts_query->have_posts() ) {
				$counter = 0;
				$out .= '<div class="projekts-grid">';

				while ( $projekts_query->have_posts() ) {
					$projekts_query->the_post();
					$post_id = get_the_ID();

					// Pattern: 1 xlarge, 2 default, repeat
					// Position in pattern (0, 1, 2, 0, 1, 2, ...)
					$position_in_pattern = $counter % 3;

					if ( $position_in_pattern === 0 ) {
						// XLarge - full width
						$out .= '<article class="--xlarge">';
							ob_start();
							get_template_part( 'template-parts/components/component-postcard_xlarge', null, array( 'post_id' => $post_id ) );
							$out .= ob_get_clean();
						$out .= '</article>';
					} else {
						// Default - half width
						$out .= '<article class="--default">';
							ob_start();
							get_template_part( 'template-parts/components/component-postcard_default', null, array( 'post_id' => $post_id ) );
							$out .= ob_get_clean();
						$out .= '</article>';
					}

					$counter++;
				}

				$out .= '</div>';

				// Pagination
				$total_pages = $projekts_query->max_num_pages;
				if ( $total_pages > 1 ) {
					$out .= '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'headofsales' ) . '">';
						$out .= paginate_links( array(
							'total'     => $total_pages,
							'current'   => $paged,
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
						) );
					$out .= '</nav>';
				}

				wp_reset_postdata();

			} else {
				$out .= '<p>' . esc_html__( 'Nav atrasti projekti.', 'headofsales' ) . '</p>';
			}

		$out .= '</div>'; // .container
	$out .= '</section>';
$out .= '</main>';

echo $out;

get_footer();
