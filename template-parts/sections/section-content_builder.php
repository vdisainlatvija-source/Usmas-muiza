<?php
/**
 * Section: Content builder (inner pages)
 *
 * Flexible content layout "content_builder" — a configurable section built from
 * a "blocks" repeater (Title / Text / Gallery / Table / Button) on a chosen
 * background colour. Table rows can be expandable (a "+" reveals extra info).
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$bg          = get_sub_field( 'background' ) ?: 'white';

if ( ! have_rows( 'blocks' ) ) {
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>
<section class="section-cb section-cb--<?php echo esc_attr( $bg ); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container cb-inner">
		<?php
		$i = 0;
		while ( have_rows( 'blocks' ) ) :
			the_row();
			$type = get_sub_field( 'type' );
			$aos  = ' data-aos="fade-up" data-aos-delay="' . (int) ( $i * 100 ) . '"';

			if ( 'title' === $type ) :
				$title = get_sub_field( 'title' );
				if ( $title ) :
					?>
					<h2<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $title ); ?></h2>
					<?php
					$i++;
				endif;

			elseif ( 'text' === $type ) :
				$text = get_sub_field( 'text' );
				if ( $text ) :
					?>
					<div class="cb-text"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_post( $text ); ?></div>
					<?php
					$i++;
				endif;

			elseif ( 'gallery' === $type ) :
				$images = get_sub_field( 'gallery' );
				if ( $images ) :
					?>
					<div class="cb-gallery"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php foreach ( $images as $img ) : ?>
							<figure class="cb-gallery__item">
								<a href="<?php echo esc_url( $img['url'] ); ?>" data-lightbox="cb-gallery-<?php echo (int) $i; ?>">
									<img src="<?php echo esc_url( ! empty( $img['sizes']['large'] ) ? $img['sizes']['large'] : $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy">
								</a>
							</figure>
						<?php endforeach; ?>
					</div>
					<?php
					$i++;
				endif;

			elseif ( 'table' === $type ) :
				if ( have_rows( 'rows' ) ) :
					?>
					<div class="cb-table"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						while ( have_rows( 'rows' ) ) :
							the_row();
							$c1     = get_sub_field( 'col1' );
							$c2     = get_sub_field( 'col2' );
							$c3     = get_sub_field( 'col3' );
							$exp_type  = get_sub_field( 'expand_type' ) ?: 'none';
							$expand    = get_sub_field( 'expand' );
							$exp_items = ( 'items' === $exp_type ) ? (array) get_sub_field( 'expand_items' ) : array();
							$has_text  = ( 'text' === $exp_type ) && '' !== trim( wp_strip_all_tags( (string) $expand ) );
							$has_items = ( 'items' === $exp_type ) && ! empty( $exp_items );
							$is_exp    = $has_text || $has_items;
							$tag       = $is_exp ? 'button' : 'div';
							?>
							<div class="cb-table__row<?php echo $is_exp ? ' is-expandable' : ''; ?>">
								<<?php echo $tag; ?> class="cb-table__head"<?php echo $is_exp ? ' type="button" aria-expanded="false"' : ''; ?>>
									<span class="cb-table__c1"><span class="cb-table__label"><?php echo esc_html( $c1 ); ?></span></span>
									<?php if ( '' !== (string) $c2 ) : ?><span class="cb-table__c2"><?php echo esc_html( $c2 ); ?></span><?php endif; ?>
									<?php if ( '' !== (string) $c3 ) : ?><span class="cb-table__c3"><?php echo esc_html( $c3 ); ?></span><?php endif; ?>
									<?php if ( $is_exp ) : ?><span class="cb-table__toggle" aria-hidden="true">+</span><?php endif; ?>
								</<?php echo $tag; ?>>
								<?php if ( $is_exp ) : ?>
									<div class="cb-table__panel"><div class="cb-table__panel-inner">
										<?php if ( $has_items ) : ?>
											<ul class="cb-menu">
												<?php foreach ( $exp_items as $item ) : ?>
													<li class="cb-menu__item">
														<span class="cb-menu__label"><?php echo esc_html( $item['label'] ?? '' ); ?></span>
														<?php if ( ! empty( $item['price'] ) ) : ?><span class="cb-menu__price"><?php echo esc_html( $item['price'] ); ?></span><?php endif; ?>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php else : ?>
											<?php echo wp_kses_post( $expand ); ?>
										<?php endif; ?>
									</div></div>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
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
					<div class="cb-btn"<?php echo $aos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<a class="<?php echo esc_attr( $btn_class ); ?>" href="<?php echo esc_url( usmasmuiza_localize_url( $button['url'] ) ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
							<?php echo esc_html( ! empty( $button['title'] ) ? $button['title'] : usmasmuiza_ui_string( 'Uzzināt vairāk', 'content_builder_button' ) ); ?>
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
