<?php
/**
 * Section: Hero (inner pages)
 *
 * Flexible content layout "hero" — a full-width background image only
 * (no overlay, no text). Used on inner content pages.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$image = get_sub_field( 'image' );

if ( empty( $image['url'] ) ) {
	return;
}

$src         = ! empty( $image['sizes']['hero'] ) ? $image['sizes']['hero'] : $image['url'];
$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$id_attr     = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<section class="section-hero"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="background-image:url('<?php echo esc_url( $src ); ?>');" role="img" aria-label="<?php echo esc_attr( $image['alt'] ); ?>"></section>
