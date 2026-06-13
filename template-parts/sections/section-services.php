<?php
/**
 * Section: Services (homepage)
 *
 * Flexible content layout "services" (field group: Page - Homepage).
 * A grid of service cards (image + title + text). Each card is a single
 * clickable link to its inner page. Rendered inside the have_rows( 'sections' )
 * loop in templates/homepage.php.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! have_rows( 'cards' ) ) {
	return;
}
?>
<section class="section-services">
	<div class="container services-grid">

		<?php
		$i = 0;
		while ( have_rows( 'cards' ) ) :
			the_row();

			$image  = get_sub_field( 'image' );
			$title  = get_sub_field( 'title' );
			$text   = get_sub_field( 'text' );
			$link   = get_sub_field( 'link' );
			$url    = ( is_array( $link ) && ! empty( $link['url'] ) ) ? $link['url'] : '';
			$target = ( is_array( $link ) && ! empty( $link['target'] ) ) ? $link['target'] : '';
			$tag    = $url ? 'a' : 'div';
			?>

			<div class="service-card-wrap" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 100 ); ?>">
				<<?php echo $tag; ?> class="service-card"<?php echo $url ? ' href="' . esc_url( $url ) . '"' . ( $target ? ' target="' . esc_attr( $target ) . '"' : '' ) : ''; ?>>

					<?php if ( is_array( $image ) && ! empty( $image['url'] ) ) : ?>
						<div class="service-card__image">
							<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( ! empty( $image['alt'] ) ? $image['alt'] : (string) $title ); ?>" loading="lazy">
						</div>
					<?php endif; ?>

					<div class="service-card__body">
						<?php if ( $title ) : ?>
							<h3 class="service-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $text ) : ?>
							<p class="service-card__text"><?php echo wp_kses_post( nl2br( $text ) ); ?></p>
						<?php endif; ?>
					</div>

				</<?php echo $tag; ?>>
			</div>

		<?php
			$i++;
		endwhile;
		?>

	</div>
</section>
