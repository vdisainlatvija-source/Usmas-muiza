<?php
/**
 * Section: Testimonials (homepage)
 *
 * Flexible content layout "testimonials". A heading + a horizontally
 * scrollable row of testimonial cards (quote mark + text + author).
 * Rendered inside the have_rows( 'sections' ) loop in templates/homepage.php.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$heading = get_sub_field( 'heading' );

if ( ! have_rows( 'items' ) ) {
	return;
}
?>
<section class="section-testimonials">

	<?php if ( $heading ) : ?>
		<div class="container">
			<h2 class="testimonials-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
		</div>
	<?php endif; ?>

	<div class="testimonials-track">
		<?php
		while ( have_rows( 'items' ) ) :
			the_row();
			$text   = get_sub_field( 'text' );
			$author = get_sub_field( 'author' );
			if ( ! $text ) {
				continue;
			}
			?>
			<figure class="testimonial">
				<span class="testimonial__quote" aria-hidden="true">&rdquo;</span>
				<blockquote class="testimonial__text"><?php echo wp_kses_post( nl2br( $text ) ); ?></blockquote>
				<?php if ( $author ) : ?>
					<figcaption class="testimonial__author"><?php echo esc_html( $author ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endwhile; ?>
	</div>

</section>
