<?php
/**
 * Section: Anchor Nav (inner pages)
 *
 * Flexible content layout "anchor_nav" — a row of links that smooth-scroll to
 * other sections on the same page (each link targets a section's id).
 *
 * Smooth scrolling is handled globally in assets/js/theme.js (a[href^="#"]).
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! have_rows( 'links' ) ) {
	return;
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$id_attr     = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<nav class="section-anchor-nav"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container anchor-nav__inner">
		<?php
		while ( have_rows( 'links' ) ) :
			the_row();
			$label  = get_sub_field( 'label' );
			$anchor = ltrim( (string) get_sub_field( 'anchor' ), '#' );
			if ( ! $label || ! $anchor ) {
				continue;
			}
			?>
			<a class="anchor-nav__link" href="#<?php echo esc_attr( sanitize_title( $anchor ) ); ?>" data-text="<?php echo esc_attr( $label ); ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endwhile; ?>
	</div>
</nav>
