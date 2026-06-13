<?php
/**
 * Section: Gallery (homepage)
 *
 * Flexible content layout "gallery". Shows category tabs; each tab pulls the
 * 3 newest images from the "gallery" post type in that gallery_category term
 * (images come from each gallery post's ACF "images" gallery field).
 * A "see more" button links to the full gallery page.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' ); // optional (Page – Sections only)
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$heading     = get_sub_field( 'heading' );
$cats        = get_sub_field( 'categories' ); // array of WP_Term (return_format: object)
$button      = get_sub_field( 'button' );

if ( empty( $cats ) ) {
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

// Build [ term, images[] ] for each category. Newest posts first; flatten
// their gallery-field images and keep the first 3 for the homepage preview.
$groups = array();
foreach ( $cats as $term ) {
	if ( ! $term instanceof WP_Term ) {
		continue;
	}

	// The ACF field stores the default-language (Latvian) term IDs. Map each
	// to its translation for the current language so the tab label, slug and
	// query all use the localized term (WPML). Falls back to the original.
	$localized_id = apply_filters( 'wpml_object_id', $term->term_id, 'gallery_category', true );
	if ( $localized_id && (int) $localized_id !== (int) $term->term_id ) {
		$localized = get_term( $localized_id, 'gallery_category' );
		if ( $localized instanceof WP_Term ) {
			$term = $localized;
		}
	}

	$q = new WP_Query(
		array(
			'post_type'      => 'gallery',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'gallery_category',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	$images = array();
	foreach ( $q->posts as $gallery_post ) {
		$post_images = get_field( 'images', $gallery_post->ID );
		if ( $post_images ) {
			$images = array_merge( $images, $post_images );
		}
	}
	wp_reset_postdata();

	if ( $images ) {
		$groups[] = array(
			'term'   => $term,
			'images' => array_slice( $images, 0, 3 ),
		);
	}
}

if ( empty( $groups ) ) {
	return;
}
?>
<section class="section-gallery"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="container gallery-head">
		<?php if ( $heading ) : ?>
			<h2 class="gallery-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( count( $groups ) > 1 ) : ?>
			<div class="gallery-tabs" role="tablist" data-aos="fade-up" data-aos-delay="100">
				<?php foreach ( $groups as $i => $group ) : ?>
					<button class="gallery-tab<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button" data-tab="<?php echo esc_attr( $i ); ?>" data-slug="<?php echo esc_attr( $group['term']->slug ); ?>" data-text="<?php echo esc_attr( $group['term']->name ); ?>">
						<?php echo esc_html( $group['term']->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="gallery-panels">
		<?php foreach ( $groups as $i => $group ) : ?>
			<div class="gallery-panel<?php echo 0 === $i ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr( $i ); ?>">
				<div class="gallery-track">
					<?php
					foreach ( $group['images'] as $img ) :
						$src  = ! empty( $img['sizes']['large'] ) ? $img['sizes']['large'] : $img['url'];
						$full = $img['url'];
						?>
						<figure class="gallery-item">
							<a href="<?php echo esc_url( $full ); ?>" data-lightbox="gallery-<?php echo esc_attr( $i ); ?>">
								<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy">
							</a>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php
	// Button → gallery archive, carrying the active category (?cat=<slug>).
	// JS keeps ?cat in sync as the visitor switches tabs (see theme.js).
	$archive_base = get_post_type_archive_link( 'gallery' );
	$first_slug   = $groups[0]['term']->slug;
	$btn_url      = $archive_base ? add_query_arg( 'cat', $first_slug, $archive_base ) : ( ! empty( $button['url'] ) ? $button['url'] : '' );
	$btn_label    = ! empty( $button['title'] ) ? $button['title'] : usmasmuiza_ui_string( 'Skatīt vairāk', 'gallery_button' );
	$btn_target   = ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : '';
	?>
	<?php if ( $btn_url ) : ?>
		<div class="container gallery-cta" data-aos="fade-up">
			<a class="btn btn--outline" href="<?php echo esc_url( $btn_url ); ?>" data-archive="<?php echo esc_url( (string) $archive_base ); ?>"<?php echo $btn_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>
	<?php endif; ?>

</section>
