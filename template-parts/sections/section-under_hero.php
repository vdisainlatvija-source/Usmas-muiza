<?php
/**
 * Section: Under Hero (inner pages)
 *
 * Flexible content layout "under_hero" — a pampas content section built from a
 * "blocks" repeater. Each block is a Heading, Text or Button, added / removed /
 * reordered freely in the editor. Section-level alignment (center/left); blocks
 * can be Full or Narrow width.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Read parent-level fields BEFORE have_rows('blocks'), which opens the blocks
// sub-loop context and would make get_sub_field() look inside it.
$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$align       = get_sub_field( 'align' ) ?: 'center';
$id_attr     = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

if ( ! have_rows( 'blocks' ) ) {
	return;
}
?>
<section class="section-under-hero under-hero--<?php echo esc_attr( $align ); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container under-hero__inner">
		<?php
		$i = 0;
		while ( have_rows( 'blocks' ) ) :
			the_row();
			$type = get_sub_field( 'type' );
			$aos  = ' data-aos="fade-up" data-aos-delay="' . (int) ( $i * 100 ) . '"';

			if ( 'heading' === $type ) :
				$heading = get_sub_field( 'heading' );
				$width   = get_sub_field( 'width' ) ?: 'full';
				if ( $heading ) :
					?>
					<h2 class="under-hero__heading under-hero__item--<?php echo esc_attr( $width ); ?>"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $heading ); ?></h2>
					<?php
					$i++;
				endif;

			elseif ( 'text' === $type ) :
				$text  = get_sub_field( 'text' );
				$width = get_sub_field( 'width' ) ?: 'full';
				if ( $text ) :
					?>
					<div class="under-hero__text under-hero__item--<?php echo esc_attr( $width ); ?>"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_post( $text ); ?></div>
					<?php
					$i++;
				endif;

			elseif ( 'schedule' === $type ) :
				$width = get_sub_field( 'width' ) ?: 'full';
				if ( have_rows( 'hours' ) ) :
					?>
					<div class="under-hero__schedule under-hero__item--<?php echo esc_attr( $width ); ?>"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						while ( have_rows( 'hours' ) ) :
							the_row();
							$sub  = get_sub_field( 'subheading' );
							$days = get_sub_field( 'days' );
							$time = get_sub_field( 'time' );
							$note = get_sub_field( 'note' );
							$line = trim( $days . ' ' . $time . ( $note ? ' ' . $note : '' ) );

							if ( $sub ) :
								?>
								<p class="schedule__subheading"><?php echo esc_html( $sub ); ?></p>
								<?php
							endif;
							if ( '' !== $line ) :
								?>
								<p class="schedule__row"><?php echo esc_html( $line ); ?></p>
								<?php
							endif;
						endwhile;
						?>
					</div>
					<?php
					$i++;
				endif;

			elseif ( 'button' === $type ) :
				$button = get_sub_field( 'button' );
				$color  = get_sub_field( 'button_color' ) ?: 'rust';
				if ( ! empty( $button['url'] ) ) :
					$btn_class = 'btn' . ( 'olive' === $color ? ' btn--green' : '' );
					?>
					<div class="under-hero__btn"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<a class="<?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( usmasmuiza_localize_url( $button['url'] ) ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
							<?php echo esc_html( ! empty( $button['title'] ) ? $button['title'] : usmasmuiza_ui_string( 'Uzzināt vairāk', 'under_hero_button' ) ); ?>
						</a>
					</div>
					<?php
					$i++;
				endif;
			endif;

		endwhile;
		?>
	</div>
</section>
