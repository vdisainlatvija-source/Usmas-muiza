<?php
/**
 * Section: News / Jaunumi (inner pages)
 *
 * Flexible content layout "news" — a heading + grid of news items pulled from
 * the "jaunums" custom post type (newest first, limited by the count field).
 * Each card is a clickable image + title.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$heading     = get_sub_field( 'heading' );
$count       = (int) get_sub_field( 'count' );

$news = new WP_Query(
	array(
		'post_type'      => 'jaunums',
		'posts_per_page' => $count > 0 ? $count : -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);

if ( ! $news->have_posts() ) {
	wp_reset_postdata();
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<section class="section-news"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $heading ) : ?>
		<h2 class="news-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="container news-grid">
		<?php
		$i = 0;
		while ( $news->have_posts() ) :
			$news->the_post();
			$news_id = get_the_ID();
			?>
			<?php
			$full_url = get_the_post_thumbnail_url( $news_id, 'full' );
			?>
			<article class="news-card" data-aos="fade-up" data-aos-delay="<?php echo (int) ( ( $i % 3 ) * 100 ); ?>">
				<?php if ( has_post_thumbnail( $news_id ) ) : ?>
					<a class="news-card__image" href="<?php echo esc_url( $full_url ); ?>" data-lightbox="news">
						<?php echo get_the_post_thumbnail( $news_id, 'large' ); ?>
					</a>
				<?php endif; ?>
				<h3 class="news-card__title"><?php the_title(); ?></h3>
			</article>
			<?php
			$i++;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
