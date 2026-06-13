<?php
/**
 * Section: Reservation (inner pages)
 *
 * Flexible content layout "reservation" — a heading + a shortcode (e.g. a
 * booking-engine widget or form). The shortcode is run through do_shortcode().
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$heading     = get_sub_field( 'heading' );
$image       = get_sub_field( 'image' );
$shortcode   = get_sub_field( 'shortcode' );

if ( ! $heading && ! $shortcode && empty( $image ) ) {
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<section class="section-reservation"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $heading ) : ?>
		<h2 class="reservation-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<?php if ( $shortcode ) : ?>
		<div class="container reservation-embed" data-aos="fade-up" data-aos-delay="100">
			<?php echo usmasmuiza_render_allowed_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- only allow-listed shortcodes are rendered ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $image['url'] ) ) : ?>
		<figure class="reservation-image" data-aos="fade-up" data-aos-delay="50">
			<img src="<?php echo esc_url( ! empty( $image['sizes']['large'] ) ? $image['sizes']['large'] : $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy">
		</figure>
	<?php endif; ?>

</section>
