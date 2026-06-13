<?php
/**
 * Section: Map (inner pages)
 *
 * Flexible content layout "map" — a heading + an embedded Google Map (iframe).
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$heading     = get_sub_field( 'heading' );
$embed       = get_sub_field( 'embed' );

if ( ! $heading && ! $embed ) {
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

// Allow just the <iframe> from the pasted Google Maps embed.
$allowed_iframe = array(
	'iframe' => array(
		'src'             => true,
		'width'           => true,
		'height'          => true,
		'style'           => true,
		'frameborder'     => true,
		'allowfullscreen' => true,
		'loading'         => true,
		'referrerpolicy'  => true,
		'title'           => true,
	),
);
?>
<section class="section-map"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $heading ) : ?>
		<h2 class="map-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<?php if ( $embed ) : ?>
		<div class="container map-embed" data-aos="fade-up" data-aos-delay="100">
			<?php echo wp_kses( $embed, $allowed_iframe ); ?>
		</div>
	<?php endif; ?>

</section>
