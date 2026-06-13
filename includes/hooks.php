<?php
/**
* WP hooks
*
* @package usmasmuiza
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define('ACF_INCLUDE_LEGACY_ICON_CHOICES', true);

// Enable excerpts for pages
add_post_type_support( 'page', 'excerpt' );

/**
 * Keep wp-login.php / wp-admin free of WPML's language prefix.
 *
 * When browsing the English side, WPML prepends the language code to the login
 * URL, producing /en/wp-login.php — which does not exist and 404s. This strips
 * the 2-letter language segment so the login/admin URL stays at the site root.
 * Query args (redirect_to, action, _wpnonce) are preserved.
 */
function usmasmuiza_strip_lang_from_login_url( $url ) {
	if ( ! is_string( $url ) ) {
		return $url;
	}
	return preg_replace( '#/[a-z]{2}(/wp-(?:login\.php|admin))#', '$1', $url );
}
add_filter( 'login_url', 'usmasmuiza_strip_lang_from_login_url', 999 );
add_filter( 'logout_url', 'usmasmuiza_strip_lang_from_login_url', 999 );

/**
 * Remove default Posts post type from admin
 */
// Remove "Posts" from admin menu
function usmasmuiza_remove_posts_menu() {
    remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'usmasmuiza_remove_posts_menu' );

// Remove "Posts" from admin bar "+ New" dropdown
function usmasmuiza_remove_posts_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node( 'new-post' );
}
add_action( 'wp_before_admin_bar_render', 'usmasmuiza_remove_posts_admin_bar' );

// Remove Posts-related dashboard widgets
function usmasmuiza_remove_posts_dashboard_widgets() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'usmasmuiza_remove_posts_dashboard_widgets' );

// Add text to login
function add_h1_to_login_form() {
    echo '<h1>'.__('Login', 'usmasmuiza').':</h1>';
}
add_action('woocommerce_login_form_start', 'add_h1_to_login_form');

// Login screen logo
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/screenshot.png?v=<?php echo filemtime( get_stylesheet_directory() . '/screenshot.png' ); ?>);
            height:240px;
            width:320px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

// Login screen link
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );
function my_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );


/**
 * ACF JSON Sync
 * Bidirectional sync between ACF dashboard and JSON files
 */

// Save ACF JSON files to theme folder when saving in dashboard
function usmasmuiza_acf_json_save_point( $path ) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'usmasmuiza_acf_json_save_point' );

// Load ACF JSON files from theme folder
function usmasmuiza_acf_json_load_point( $paths ) {
    // Remove the original path
    unset( $paths[0] );

    // Add our custom path
    $paths[] = get_stylesheet_directory() . '/acf-json';

    return $paths;
}
add_filter( 'acf/settings/load_json', 'usmasmuiza_acf_json_load_point' );

/**
 * Disable Gutenberg editor globally for all post types
 */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter( 'use_block_editor_for_post_type', '__return_false' );

/**
 * Disable revisions for all post types
 */
function usmasmuiza_disable_revisions() {
    foreach ( get_post_types( array(), 'names' ) as $post_type ) {
        remove_post_type_support( $post_type, 'revisions' );
    }
}
add_action( 'init', 'usmasmuiza_disable_revisions' );

/**
 * Disable comments and discussions globally
 */
// Disable comments support for all post types
function usmasmuiza_disable_comments_support() {
    foreach ( get_post_types( array(), 'names' ) as $post_type ) {
        remove_post_type_support( $post_type, 'comments' );
        remove_post_type_support( $post_type, 'trackbacks' );
    }
}
add_action( 'init', 'usmasmuiza_disable_comments_support' );

// Close comments on frontend
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments from admin menu
function usmasmuiza_remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'usmasmuiza_remove_comments_admin_menu' );

// Remove comments from admin bar
function usmasmuiza_remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'usmasmuiza_remove_comments_admin_bar' );

// Redirect comments page to dashboard
function usmasmuiza_redirect_comments_page() {
    global $pagenow;
    if ( $pagenow === 'edit-comments.php' ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}
add_action( 'admin_init', 'usmasmuiza_redirect_comments_page' );

// Remove comments metabox from dashboard
function usmasmuiza_remove_dashboard_comments_metabox() {
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'usmasmuiza_remove_dashboard_comments_metabox' );

/**
 * ACF Field for Category - Hide from archive filter
 */
function usmasmuiza_acf_category_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_category_settings',
        'title' => 'Category Settings',
        'fields' => array(
            array(
                'key' => 'field_hide_from_archive_filter',
                'label' => 'Hide from archive filter',
                'name' => 'hide_from_archive_filter',
                'type' => 'true_false',
                'instructions' => 'Check this to hide this category from the projects archive filter dropdown.',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'category',
                ),
            ),
        ),
    ) );
}
add_action( 'acf/init', 'usmasmuiza_acf_category_fields' );

/**
 * Gravity Forms - Replace submit input with button element and add info text
 */
function usmasmuiza_gform_submit_button( $button, $form ) {
	// Get the submit button text from the form settings or use default
	$button_text = ! empty( $form['button']['text'] ) ? $form['button']['text'] : __( 'Send', 'usmasmuiza' );

	// Create custom button with arrow
	$custom_button = '<button type="submit" class="gform_button button" id="gform_submit_button_' . $form['id'] . '">';
		$custom_button .= '<span>' . esc_html( $button_text ) . '</span>';
		$custom_button .= '<i aria-hidden="true"></i>';
	$custom_button .= '</button>';

	// Add info text after the button
	$info = '<div class="gform-info">';
		$info .= '<i></i>';
		$info .= '<p>' . __( 'This helps us prepare for the conversation.', 'usmasmuiza' ) . '</p>';
	$info .= '</div>';

	return $custom_button . $info;
}
add_filter( 'gform_submit_button', 'usmasmuiza_gform_submit_button', 10, 2 );

/**
 * Expose each nav link's text as a data-text attribute, so CSS can reserve
 * the bold-weight width (a hidden bold "ghost") and avoid layout shift when
 * the link goes bold on hover.
 */
function usmasmuiza_nav_link_data_text( $atts, $item, $args, $depth ) {
	if ( isset( $args->theme_location ) && 'primary-menu' === $args->theme_location ) {
		$atts['data-text'] = wp_strip_all_tags( $item->title );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'usmasmuiza_nav_link_data_text', 10, 4 );

/**
 * Compact admin styling for the optional "Anchor" repeater (wrapper class
 * .acf-anchor-compact). Collapses it to a small "+ Anchor ID" button tucked
 * into the top-right corner so it doesn't take a full field block.
 */
function usmasmuiza_acf_anchor_admin_css() {
	?>
	<style>
		.acf-field.acf-anchor-compact { position: relative; padding: 8px 12px; }
		.acf-field.acf-anchor-compact > .acf-label { display: inline-block; margin: 0; }
		.acf-field.acf-anchor-compact > .acf-label label { font-size: 12px; font-weight: 500; color: #787c82; }
		/* Hide the empty repeater table; show the add button as a small corner pill. */
		.acf-field.acf-anchor-compact .acf-repeater.-empty > .acf-table { display: none; }
		.acf-field.acf-anchor-compact .acf-actions { position: absolute; top: 6px; right: 12px; margin: 0; padding: 0; text-align: right; }
		.acf-field.acf-anchor-compact .acf-actions .acf-button { min-height: 0; height: auto; padding: 2px 10px; font-size: 12px; line-height: 1.7; }
		/* When a row is added, keep it tidy (no drag handle / order column). */
		.acf-field.acf-anchor-compact .acf-row-handle.order { display: none; }
	</style>
	<?php
}
add_action( 'acf/input/admin_head', 'usmasmuiza_acf_anchor_admin_css' );

/**
 * Menu Item Hover SVG Elements
 * Adds decorative SVG hover effects under primary navigation links.
 * Configurable per menu item in Appearance > Menus.
 *
 * Register each SVG file (placed in /assets/images/svg/{slug}.svg)
 * in the array below to expose it as a hover option.
 */
function usmasmuiza_get_hover_svgs() {
	return array(
		'' => __( 'None', 'usmasmuiza' ),
	);
}

// Add hover SVG fields to each nav menu item (Appearance > Menus)
function usmasmuiza_menu_item_hover_svg_field( $item_id, $item, $depth, $args ) {
	$hover_svg  = get_post_meta( $item_id, '_menu_item_hover_svg', true );
	$hover_bottom = get_post_meta( $item_id, '_menu_item_hover_bottom', true );
	$hover_x    = get_post_meta( $item_id, '_menu_item_hover_x', true );
	$hover_scale = get_post_meta( $item_id, '_menu_item_hover_scale', true );
	$svgs = usmasmuiza_get_hover_svgs();
	?>
	<p class="field-hover-svg description description-wide">
		<label for="edit-menu-item-hover-svg-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Hover SVG Element', 'usmasmuiza' ); ?>
			<select id="edit-menu-item-hover-svg-<?php echo esc_attr( $item_id ); ?>" name="menu-item-hover-svg[<?php echo esc_attr( $item_id ); ?>]" class="widefat">
				<?php foreach ( $svgs as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $hover_svg, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<p class="field-hover-svg-position description description-wide">
		<label><?php esc_html_e( 'Hover SVG Position', 'usmasmuiza' ); ?></label>
		<span style="display:flex;gap:8px;margin-top:4px;">
			<label style="flex:1;">
				<input type="number" name="menu-item-hover-bottom[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $hover_bottom ); ?>" placeholder="-10" style="width:100%;">
				<small>Bottom (px)</small>
			</label>
			<label style="flex:1;">
				<input type="number" name="menu-item-hover-x[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $hover_x ); ?>" placeholder="0" style="width:100%;">
				<small>X offset (px)</small>
			</label>
			<label style="flex:1;">
				<input type="number" step="0.1" name="menu-item-hover-scale[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $hover_scale ); ?>" placeholder="1" style="width:100%;">
				<small>Scale</small>
			</label>
		</span>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'usmasmuiza_menu_item_hover_svg_field', 10, 4 );

// Save hover SVG fields
function usmasmuiza_save_menu_item_hover_svg( $menu_id, $menu_item_db_id, $args ) {
	// Save SVG selection
	if ( isset( $_POST['menu-item-hover-svg'][ $menu_item_db_id ] ) ) {
		$value = sanitize_text_field( $_POST['menu-item-hover-svg'][ $menu_item_db_id ] );
		$valid_svgs = array_keys( usmasmuiza_get_hover_svgs() );
		if ( in_array( $value, $valid_svgs, true ) ) {
			update_post_meta( $menu_item_db_id, '_menu_item_hover_svg', $value );
		}
	}

	// Save position fields
	$position_fields = array( '_menu_item_hover_bottom', '_menu_item_hover_x', '_menu_item_hover_scale' );
	$post_keys = array( 'menu-item-hover-bottom', 'menu-item-hover-x', 'menu-item-hover-scale' );

	foreach ( $position_fields as $i => $meta_key ) {
		$post_key = $post_keys[ $i ];
		if ( isset( $_POST[ $post_key ][ $menu_item_db_id ] ) ) {
			$val = sanitize_text_field( $_POST[ $post_key ][ $menu_item_db_id ] );
			if ( $val !== '' ) {
				update_post_meta( $menu_item_db_id, $meta_key, $val );
			} else {
				delete_post_meta( $menu_item_db_id, $meta_key );
			}
		}
	}
}
add_action( 'wp_update_nav_menu_item', 'usmasmuiza_save_menu_item_hover_svg', 10, 3 );

/**
 * Get the menu item ID that holds hover SVG settings.
 * Falls back to the default language menu item if current one has no settings.
 */
function usmasmuiza_get_hover_svg_source_id( $item_id ) {
	$hover_svg = get_post_meta( $item_id, '_menu_item_hover_svg', true );

	if ( ! empty( $hover_svg ) ) {
		return $item_id;
	}

	// Fallback: check the default language menu item
	$default_lang = apply_filters( 'wpml_default_language', null );
	$current_lang = apply_filters( 'wpml_current_language', null );

	if ( ! $default_lang || ! $current_lang || $current_lang === $default_lang ) {
		return $item_id;
	}

	$original_id = apply_filters( 'wpml_object_id', $item_id, 'nav_menu_item', true, $default_lang );

	if ( $original_id && $original_id !== $item_id ) {
		$original_svg = get_post_meta( $original_id, '_menu_item_hover_svg', true );
		if ( ! empty( $original_svg ) ) {
			return $original_id;
		}
	}

	return $item_id;
}

// Inject hover SVG into menu item output on the frontend
function usmasmuiza_inject_hover_svg( $item_output, $item, $depth, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'primary-menu' || $depth !== 0 ) {
		return $item_output;
	}

	$source_id = usmasmuiza_get_hover_svg_source_id( $item->ID );
	$hover_svg = get_post_meta( $source_id, '_menu_item_hover_svg', true );

	if ( empty( $hover_svg ) ) {
		return $item_output;
	}

	$svg_path = get_stylesheet_directory() . '/assets/images/svg/' . $hover_svg . '.svg';

	if ( ! file_exists( $svg_path ) ) {
		return $item_output;
	}

	// Build inline style from position settings
	$bottom = get_post_meta( $source_id, '_menu_item_hover_bottom', true );
	$x      = get_post_meta( $source_id, '_menu_item_hover_x', true );
	$scale  = get_post_meta( $source_id, '_menu_item_hover_scale', true );

	$style_parts = array();
	if ( $bottom !== '' && $bottom !== false ) {
		$style_parts[] = '--hover-bottom:' . intval( $bottom ) . 'px';
	}
	if ( $x !== '' && $x !== false ) {
		$style_parts[] = '--hover-x:' . intval( $x ) . 'px';
	}
	if ( $scale !== '' && $scale !== false ) {
		$style_parts[] = '--hover-scale:' . floatval( $scale );
	}

	$style_attr = ! empty( $style_parts ) ? ' style="' . esc_attr( implode( ';', $style_parts ) ) . '"' : '';

	$svg_content = file_get_contents( $svg_path );
	$hover_element = '<span class="menu-hover-svg menu-hover-svg--' . esc_attr( $hover_svg ) . '"' . $style_attr . '>' . $svg_content . '</span>';

	$item_output .= $hover_element;

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'usmasmuiza_inject_hover_svg', 10, 4 );
