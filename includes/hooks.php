<?php
/**
* WP hooks
*
* @package headofsales
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define('ACF_INCLUDE_LEGACY_ICON_CHOICES', true);

// Enable excerpts for pages
add_post_type_support( 'page', 'excerpt' );

/**
 * Remove default Posts post type from admin
 */
// Remove "Posts" from admin menu
function headofsales_remove_posts_menu() {
    remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'headofsales_remove_posts_menu' );

// Remove "Posts" from admin bar "+ New" dropdown
function headofsales_remove_posts_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node( 'new-post' );
}
add_action( 'wp_before_admin_bar_render', 'headofsales_remove_posts_admin_bar' );

// Remove Posts-related dashboard widgets
function headofsales_remove_posts_dashboard_widgets() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'headofsales_remove_posts_dashboard_widgets' );

// Add text to login
function add_h1_to_login_form() {
    echo '<h1>'.__('Logi sisse', 'headofsales').':</h1>';
}
add_action('woocommerce_login_form_start', 'add_h1_to_login_form');

// Login screen logo
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/svg/logo-black.svg?v=<?php echo filemtime( get_stylesheet_directory() . '/assets/images/svg/logo-black.svg' ); ?>);
            height:65px;
            width:320px;
            background-size: 320px 65px;
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
    return 'Palus';
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );


/**
 * ACF JSON Sync
 * Bidirectional sync between ACF dashboard and JSON files
 */

// Save ACF JSON files to theme folder when saving in dashboard
function headofsales_acf_json_save_point( $path ) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'headofsales_acf_json_save_point' );

// Load ACF JSON files from theme folder
function headofsales_acf_json_load_point( $paths ) {
    // Remove the original path
    unset( $paths[0] );

    // Add our custom path
    $paths[] = get_stylesheet_directory() . '/acf-json';

    return $paths;
}
add_filter( 'acf/settings/load_json', 'headofsales_acf_json_load_point' );

/**
 * Auto-import JSON field groups into database
 * This makes them editable in ACF dashboard
 */
function headofsales_acf_auto_import_json() {
    // Only run in admin
    if ( ! is_admin() ) {
        return;
    }

    // Check if ACF Pro functions exist
    if ( ! function_exists( 'acf_get_field_group' ) || ! function_exists( 'acf_import_field_group' ) ) {
        return;
    }

    $json_dir = get_stylesheet_directory() . '/acf-json';

    // Check if directory exists
    if ( ! is_dir( $json_dir ) ) {
        return;
    }

    // Get all JSON files
    $json_files = glob( $json_dir . '/*.json' );

    if ( empty( $json_files ) ) {
        return;
    }

    foreach ( $json_files as $json_file ) {
        $json_data = file_get_contents( $json_file );
        $field_group = json_decode( $json_data, true );

        if ( ! $field_group || ! isset( $field_group['key'] ) ) {
            continue;
        }

        // Check if this field group exists in database
        $existing = acf_get_field_group( $field_group['key'] );

        $json_modified = isset( $field_group['modified'] ) ? $field_group['modified'] : 0;
        $db_modified = isset( $existing['modified'] ) ? $existing['modified'] : 0;

        // Count fields in JSON vs database
        $json_field_count = isset( $field_group['fields'] ) ? count( $field_group['fields'] ) : 0;
        $db_field_count = 0;

        if ( $existing && isset( $existing['ID'] ) ) {
            $db_fields = acf_get_fields( $existing['ID'] );
            $db_field_count = is_array( $db_fields ) ? count( $db_fields ) : 0;
        }

        // Import if: doesn't exist, JSON is newer, or field count is different
        if ( ! $existing || $json_modified > $db_modified || $json_field_count !== $db_field_count ) {

            // Delete existing field group and its fields first
            if ( $existing && isset( $existing['ID'] ) ) {
                // Delete all fields
                $fields = acf_get_fields( $existing['ID'] );
                if ( $fields ) {
                    foreach ( $fields as $field ) {
                        wp_delete_post( $field['ID'], true );
                    }
                }
                // Delete the field group
                wp_delete_post( $existing['ID'], true );
            }

            // Import the field group from JSON
            acf_import_field_group( $field_group );
        }
    }
}

/**
 * Disable Gutenberg editor globally for all post types
 */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter( 'use_block_editor_for_post_type', '__return_false' );

/**
 * Disable revisions for all post types
 */
function headofsales_disable_revisions() {
    foreach ( get_post_types( array(), 'names' ) as $post_type ) {
        remove_post_type_support( $post_type, 'revisions' );
    }
}
add_action( 'init', 'headofsales_disable_revisions' );

/**
 * Disable comments and discussions globally
 */
// Disable comments support for all post types
function headofsales_disable_comments_support() {
    foreach ( get_post_types( array(), 'names' ) as $post_type ) {
        remove_post_type_support( $post_type, 'comments' );
        remove_post_type_support( $post_type, 'trackbacks' );
    }
}
add_action( 'init', 'headofsales_disable_comments_support' );

// Close comments on frontend
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments from admin menu
function headofsales_remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'headofsales_remove_comments_admin_menu' );

// Remove comments from admin bar
function headofsales_remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'headofsales_remove_comments_admin_bar' );

// Redirect comments page to dashboard
function headofsales_redirect_comments_page() {
    global $pagenow;
    if ( $pagenow === 'edit-comments.php' ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}
add_action( 'admin_init', 'headofsales_redirect_comments_page' );

// Remove comments metabox from dashboard
function headofsales_remove_dashboard_comments_metabox() {
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'headofsales_remove_dashboard_comments_metabox' );

/**
 * ACF Field for Category - Hide from archive filter
 */
function headofsales_acf_category_fields() {
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
add_action( 'acf/init', 'headofsales_acf_category_fields' );

/**
 * Gravity Forms - Replace submit input with button element and add info text
 */
function headofsales_gform_submit_button( $button, $form ) {
	// Get the submit button text from the form settings or use default
	$button_text = ! empty( $form['button']['text'] ) ? $form['button']['text'] : __( 'Sūtīt', 'headofsales' );

	// Create custom button with arrow
	$custom_button = '<button type="submit" class="gform_button button" id="gform_submit_button_' . $form['id'] . '">';
		$custom_button .= '<span>' . esc_html( $button_text ) . '</span>';
		$custom_button .= '<i aria-hidden="true"></i>';
	$custom_button .= '</button>';

	// Add info text after the button
	$info = '<div class="gform-info">';
		$info .= '<i></i>';
		$info .= '<p>' . __( 'This helps us prepare for the conversation.', 'headofsales' ) . '</p>';
	$info .= '</div>';

	return $custom_button . $info;
}
add_filter( 'gform_submit_button', 'headofsales_gform_submit_button', 10, 2 );

/**
 * Menu Item Hover SVG Elements
 * Adds decorative SVG hover effects under primary navigation links.
 * Configurable per menu item in Appearance > Menus.
 */
function headofsales_get_hover_svgs() {
	return array(
		''                  => __( 'None', 'headofsales' ),
		'darbi-hover'       => 'Darbi',
		'blogs-hover'       => 'Blogs',
		'pakalpojumi-hover' => 'Pakalpojumi',
		'sakt-sarunu-hover' => 'Sākt sarunu',
		'studija-hover'     => 'Studija',
	);
}

// Add hover SVG fields to each nav menu item (Appearance > Menus)
function headofsales_menu_item_hover_svg_field( $item_id, $item, $depth, $args ) {
	$hover_svg  = get_post_meta( $item_id, '_menu_item_hover_svg', true );
	$hover_bottom = get_post_meta( $item_id, '_menu_item_hover_bottom', true );
	$hover_x    = get_post_meta( $item_id, '_menu_item_hover_x', true );
	$hover_scale = get_post_meta( $item_id, '_menu_item_hover_scale', true );
	$svgs = headofsales_get_hover_svgs();
	?>
	<p class="field-hover-svg description description-wide">
		<label for="edit-menu-item-hover-svg-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Hover SVG Element', 'headofsales' ); ?>
			<select id="edit-menu-item-hover-svg-<?php echo esc_attr( $item_id ); ?>" name="menu-item-hover-svg[<?php echo esc_attr( $item_id ); ?>]" class="widefat">
				<?php foreach ( $svgs as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $hover_svg, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<p class="field-hover-svg-position description description-wide">
		<label><?php esc_html_e( 'Hover SVG Position', 'headofsales' ); ?></label>
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
add_action( 'wp_nav_menu_item_custom_fields', 'headofsales_menu_item_hover_svg_field', 10, 4 );

// Save hover SVG fields
function headofsales_save_menu_item_hover_svg( $menu_id, $menu_item_db_id, $args ) {
	// Save SVG selection
	if ( isset( $_POST['menu-item-hover-svg'][ $menu_item_db_id ] ) ) {
		$value = sanitize_text_field( $_POST['menu-item-hover-svg'][ $menu_item_db_id ] );
		$valid_svgs = array_keys( headofsales_get_hover_svgs() );
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
add_action( 'wp_update_nav_menu_item', 'headofsales_save_menu_item_hover_svg', 10, 3 );

/**
 * Get the menu item ID that holds hover SVG settings.
 * Falls back to the default language menu item if current one has no settings.
 */
function headofsales_get_hover_svg_source_id( $item_id ) {
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
function headofsales_inject_hover_svg( $item_output, $item, $depth, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'primary-menu' || $depth !== 0 ) {
		return $item_output;
	}

	$source_id = headofsales_get_hover_svg_source_id( $item->ID );
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
add_filter( 'walker_nav_menu_start_el', 'headofsales_inject_hover_svg', 10, 4 );

