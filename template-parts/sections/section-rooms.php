<?php
/**
 * Section: Rooms (inner pages)
 *
 * Flexible content layout "rooms" — a heading + grid of room cards pulled from
 * the "room" custom post type. Each room supplies its featured image, title and
 * ACF fields (feature icons + badge, included icons + text, button).
 *
 * The section's "rooms" relationship field picks/orders which rooms to show;
 * if empty, all published rooms are shown.
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$anchor_rows = get_sub_field( 'anchor' );
$anchor      = ( is_array( $anchor_rows ) && ! empty( $anchor_rows[0]['id'] ) ) ? sanitize_title( $anchor_rows[0]['id'] ) : '';
$heading     = get_sub_field( 'heading' );
$picked      = get_sub_field( 'rooms' ); // array of IDs (return_format: id) or empty

$query_args = array(
	'post_type'      => 'room',
	'posts_per_page' => -1,
	'no_found_rows'  => true,
);
if ( ! empty( $picked ) ) {
	$query_args['post__in'] = array_map( 'intval', (array) $picked );
	$query_args['orderby']  = 'post__in';
} else {
	$query_args['orderby'] = 'menu_order date';
	$query_args['order']   = 'ASC';
}

$rooms = new WP_Query( $query_args );

if ( ! $rooms->have_posts() ) {
	wp_reset_postdata();
	return;
}

$id_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

/**
 * Read an ACF field from a room, falling back to its default-language
 * counterpart when the current (e.g. English) translation has no value.
 * WPML "Copy" only fills translations created after it was enabled, so older
 * translations can be missing images — this keeps them showing the original.
 */
$room_field = static function ( $field, $room_id ) {
	$value = get_field( $field, $room_id );
	if ( ! empty( $value ) ) {
		return $value;
	}
	$default_lang = apply_filters( 'wpml_default_language', null );
	if ( $default_lang ) {
		$orig_id = apply_filters( 'wpml_object_id', $room_id, 'room', true, $default_lang );
		if ( $orig_id && (int) $orig_id !== (int) $room_id ) {
			return get_field( $field, $orig_id );
		}
	}
	return $value;
};

/** Render an icon <img> from an ACF image array. */
$render_icon = static function ( $icon, $class = 'room-icon' ) {
	if ( ! is_array( $icon ) || empty( $icon['url'] ) ) {
		return '';
	}
	return '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $icon['url'] ) . '" alt="' . esc_attr( $icon['alt'] ) . '" loading="lazy">';
};
?>
<section class="section-rooms"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php if ( $heading ) : ?>
		<h2 class="rooms-title" data-aos="fade-up"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="container rooms-grid">
		<?php
		$i = 0;
		while ( $rooms->have_posts() ) :
			$rooms->the_post();
			$room_id = get_the_ID();
			$image   = $room_field( 'image', $room_id );
			$hover   = $room_field( 'image_hover', $room_id );
			$inc     = get_field( 'included_text', $room_id );
			$button  = get_field( 'button', $room_id );
			$btn_url = ! empty( $button['url'] ) ? usmasmuiza_localize_url( $button['url'] ) : get_permalink( $room_id );
			$btn_txt = ! empty( $button['title'] ) ? $button['title'] : usmasmuiza_ui_string( 'Vairāk informācijas', 'rooms_card_button' );

			// Default image: ACF "image", or the featured image as a fallback.
			$img_url = ( is_array( $image ) && ! empty( $image['url'] ) ) ? $image['url'] : get_the_post_thumbnail_url( $room_id, 'large' );
			$img_alt = ( is_array( $image ) && ! empty( $image['alt'] ) ) ? $image['alt'] : get_the_title( $room_id );
			$has_hover = is_array( $hover ) && ! empty( $hover['url'] );
			?>
			<article class="room-card<?php echo $has_hover ? ' has-hover' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo (int) ( ( $i % 3 ) * 100 ); ?>">

				<?php if ( $img_url ) : ?>
					<div class="room-card__image">
						<img class="room-card__img" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy">
						<?php if ( $has_hover ) : ?>
							<img class="room-card__img room-card__img--hover" src="<?php echo esc_url( $hover['url'] ); ?>" alt="" aria-hidden="true" loading="lazy">
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<h3 class="room-card__title"><?php the_title(); ?></h3>

				<?php if ( have_rows( 'features', $room_id ) ) : ?>
					<div class="room-card__features">
						<?php
						while ( have_rows( 'features', $room_id ) ) :
							the_row();
							$icon  = get_sub_field( 'icon' );
							$badge = get_sub_field( 'badge' );
							if ( ! is_array( $icon ) || empty( $icon['url'] ) ) {
								continue;
							}
							?>
							<span class="room-feature">
								<?php echo $render_icon( $icon, 'room-feature__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php if ( '' !== (string) $badge ) : ?>
									<span class="room-feature__badge"><?php echo esc_html( $badge ); ?></span>
								<?php endif; ?>
							</span>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>

				<?php if ( have_rows( 'included_icons', $room_id ) || $inc ) : ?>
					<div class="room-card__included">
						<?php if ( have_rows( 'included_icons', $room_id ) ) : ?>
							<span class="room-card__included-icons">
								<?php
								while ( have_rows( 'included_icons', $room_id ) ) :
									the_row();
									echo $render_icon( get_sub_field( 'icon' ), 'room-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								endwhile;
								?>
							</span>
						<?php endif; ?>
						<?php if ( $inc ) : ?>
							<span class="room-card__included-text"><?php echo wp_kses_post( nl2br( $inc ) ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<a class="btn btn--green room-card__btn" href="<?php echo esc_url( $btn_url ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
					<?php echo esc_html( $btn_txt ); ?>
				</a>

			</article>
			<?php
			$i++;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
