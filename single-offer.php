<?php
/**
 * Single Special Offer (CPT: offer).
 *
 * Title + price, then image + full content + CTA button.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

echo '<main id="offerMain">';

while ( have_posts() ) :
	the_post();
	$offer_id = get_the_ID();
	$price    = get_field( 'price', $offer_id );
	$content  = get_field( 'content', $offer_id );
	$cta      = get_field( 'cta', $offer_id );
	?>
	<section class="single-offer">

		<div class="container offer-head" data-aos="fade-up">
			<h1 class="offer-title"><?php the_title(); ?></h1>
			<?php if ( $price ) : ?>
				<p class="offer-price"><?php echo esc_html( $price ); ?></p>
			<?php endif; ?>
		</div>

		<div class="container offer-body">
			<?php if ( has_post_thumbnail( $offer_id ) ) : ?>
				<div class="offer-media" data-aos="fade-up">
					<?php echo get_the_post_thumbnail( $offer_id, 'large' ); ?>
				</div>
			<?php endif; ?>

			<div class="offer-detail" data-aos="fade-up" data-aos-delay="100">
				<?php if ( $content ) : ?>
					<div class="offer-text"><?php echo wp_kses_post( $content ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $cta['url'] ) ) : ?>
					<a class="btn btn--green offer-cta" href="<?php echo esc_url( $cta['url'] ); ?>"<?php echo ! empty( $cta['target'] ) ? ' target="' . esc_attr( $cta['target'] ) . '"' : ''; ?>>
						<?php echo esc_html( ! empty( $cta['title'] ) ? $cta['title'] : __( 'Pieteikt piedāvājumu', 'usmasmuiza' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

	</section>
	<?php
endwhile;

echo '</main>';

get_footer();
