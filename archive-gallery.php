<?php
/**
 * The full gallery archive — /galerija/
 *
 * Shows every gallery_category term that has images as a tab, and all of that
 * category's images in a grid. Reuses the homepage gallery section's tab
 * switching, entrance animation and lightbox (see assets/js/theme.js).
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

// Category order is editor-controlled via Site settings → Galerijas arhīvs.
// If set, honour that order; otherwise fall back to every non-empty category.
$ordered_rows = get_field( 'gallery_archive_categories', 'option' );
$terms        = array();
if ( is_array( $ordered_rows ) ) {
	foreach ( $ordered_rows as $row ) {
		if ( ! empty( $row['category'] ) && $row['category'] instanceof WP_Term ) {
			$terms[] = $row['category'];
		}
	}
}
if ( empty( $terms ) ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'gallery_category',
			'hide_empty' => true,
		)
	);
}

// Build [ term, images[] ] per category — flatten every post's "images" field.
$groups = array();
if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
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
				'images' => $images,
			);
		}
	}
}

// Pre-select the category passed via ?cat=<term-slug> (from the homepage
// "Skatīt vairāk" button). Falls back to the first group.
$active_slug  = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$active_index = 0;
if ( $active_slug ) {
	foreach ( $groups as $i => $group ) {
		if ( $group['term']->slug === $active_slug ) {
			$active_index = $i;
			break;
		}
	}
}
// Optional full-width hero image (Site settings → Galerijas arhīvs).
$hero     = get_field( 'gallery_archive_hero', 'option' );
$hero_src = '';
if ( ! empty( $hero['url'] ) ) {
	$hero_src = ! empty( $hero['sizes']['hero'] ) ? $hero['sizes']['hero'] : $hero['url'];
}
?>

<main>

	<?php if ( $hero_src ) : ?>
		<section class="section-hero" style="background-image:url('<?php echo esc_url( $hero_src ); ?>');" role="img" aria-label="<?php echo esc_attr( $hero['alt'] ); ?>"></section>
	<?php endif; ?>

	<section class="section-gallery gallery-archive">

		<div class="container gallery-head">
			<h1 class="gallery-title" data-aos="fade-up"><?php echo esc_html__( 'Galerija', 'usmasmuiza' ); ?></h1>

			<?php if ( count( $groups ) > 1 ) : ?>
				<div class="gallery-tabs" role="tablist" data-aos="fade-up" data-aos-delay="100">
					<?php foreach ( $groups as $i => $group ) : ?>
						<button class="gallery-tab<?php echo $active_index === $i ? ' is-active' : ''; ?>" type="button" data-tab="<?php echo esc_attr( $i ); ?>" data-text="<?php echo esc_attr( $group['term']->name ); ?>">
							<?php echo esc_html( $group['term']->name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( empty( $groups ) ) : ?>
			<p class="container"><?php echo esc_html__( 'Galerijā vēl nav bilžu.', 'usmasmuiza' ); ?></p>
		<?php else : ?>
			<div class="container gallery-archive__panels">
				<?php foreach ( $groups as $i => $group ) : ?>
					<div class="gallery-panel<?php echo $active_index === $i ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr( $i ); ?>">
						<div class="gallery-grid">
							<?php
							foreach ( $group['images'] as $img ) :
								$src  = ! empty( $img['sizes']['large'] ) ? $img['sizes']['large'] : $img['url'];
								$full = $img['url'];
								?>
								<figure class="gallery-item">
									<a href="<?php echo esc_url( $full ); ?>" data-lightbox="gallery-archive-<?php echo esc_attr( $i ); ?>">
										<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy">
									</a>
								</figure>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</section>
</main>

<?php
get_footer();
