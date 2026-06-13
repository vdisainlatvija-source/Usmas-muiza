<?php
/**
 * Section: Special Offers (inner pages)
 *
 * Flexible content layout "offers" — a heading + grid of offer cards pulled
 * from the "offer" custom post type (newest first, limited by the count field).
 * Each offer supplies its featured image, title and ACF fields (description,
 * price, button).
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

$offers = new WP_Query(
	array(
		'post_type'      => 'offer',
		'posts_per_page' => $count > 0 ? $count : -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $offers->have_posts() ) {
	wp_reset_postdata();
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<section class="section-offers"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $heading ) : ?>
		<h2 class="offers-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="container offers-grid">
		<?php
		$i = 0;
		while ( $offers->have_posts() ) :
			$offers->the_post();
			$offer_id = get_the_ID();
			$desc     = get_field( 'description', $offer_id );
			$price    = get_field( 'price', $offer_id );
			// The card button always opens this offer's single page.
			$btn_url  = get_permalink( $offer_id );
			$btn_txt  = usmasmuiza_ui_string( 'Skatīt vairāk', 'offers_card_button' );
			?>
			<article class="offer-card" data-aos="fade-up" data-aos-delay="<?php echo (int) ( ( $i % 3 ) * 100 ); ?>">

				<?php if ( has_post_thumbnail( $offer_id ) ) : ?>
					<a class="offer-card__image" href="<?php echo esc_url( $btn_url ); ?>">
						<?php echo get_the_post_thumbnail( $offer_id, 'medium_large' ); ?>
					</a>
				<?php endif; ?>

				<div class="offer-card__body">
					<h3 class="offer-card__title"><?php the_title(); ?></h3>

					<?php if ( $desc ) : ?>
						<p class="offer-card__desc"><?php echo wp_kses_post( nl2br( $desc ) ); ?></p>
					<?php endif; ?>

					<?php if ( $price ) : ?>
						<p class="offer-card__price"><?php echo esc_html( $price ); ?></p>
					<?php endif; ?>
				</div>

				<a class="btn btn--outline-accent offer-card__btn" href="<?php echo esc_url( $btn_url ); ?>">
					<?php echo esc_html( $btn_txt ); ?>
				</a>

			</article>
			<?php
			$i++;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
