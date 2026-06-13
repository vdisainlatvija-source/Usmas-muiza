<?php
/**
 * Section: Media + Text (inner pages)
 *
 * Flexible content layout "media_text" — two columns: a text block (heading,
 * body, price + button) and an image. The image side (left/right) flips per
 * instance so consecutive sections can alternate.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$bg          = get_sub_field( 'background' ) ?: 'white';
$side        = get_sub_field( 'media_side' ) ?: 'right';
$images      = (array) get_sub_field( 'images' );
$heading     = get_sub_field( 'heading' );
$text        = get_sub_field( 'text' );
$price       = get_sub_field( 'price' );
$note        = get_sub_field( 'note' );
$button      = get_sub_field( 'button' );
$color       = get_sub_field( 'button_color' ) ?: 'olive';

if ( ! $heading && ! $text && empty( $images ) ) {
	return;
}

$id_attr   = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
$btn_class = 'btn' . ( 'olive' === $color ? ' btn--green' : '' );
?>
<section class="section-media-text section-media-text--<?php echo esc_attr( $bg ); ?> media--<?php echo esc_attr( $side ); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container mt-inner">

		<div class="mt-text" data-aos="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="mt-heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<div class="mt-body"><?php echo wp_kses_post( $text ); ?></div>
			<?php endif; ?>

			<?php if ( $price || ! empty( $button['url'] ) ) : ?>
				<div class="mt-foot">
					<?php if ( $price ) : ?>
						<p class="mt-price"><?php echo esc_html( $price ); ?>
							<?php if ( $note ) : ?><span class="mt-note"><?php echo esc_html( $note ); ?></span><?php endif; ?>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $button['url'] ) ) : ?>
						<a class="<?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( usmasmuiza_localize_url( $button['url'] ) ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
							<?php echo esc_html( ! empty( $button['title'] ) ? $button['title'] : usmasmuiza_ui_string( 'Rezervēt', 'media_text_button' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $images ) ) : ?>
			<div class="mt-media" data-aos="fade-up" data-aos-delay="100">
				<div class="mt-slider">
					<div class="mt-slider__track">
						<?php foreach ( $images as $img ) : ?>
							<figure class="mt-slider__slide">
								<img src="<?php echo esc_url( ! empty( $img['sizes']['large'] ) ? $img['sizes']['large'] : $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy">
							</figure>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $images ) > 1 ) : ?>
						<div class="mt-slider__dots">
							<?php foreach ( $images as $idx => $img ) : ?>
								<button class="mt-slider__dot<?php echo 0 === $idx ? ' is-active' : ''; ?>" type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Go to image %d', 'usmasmuiza' ), $idx + 1 ) ); ?>"></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>
