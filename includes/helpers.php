<?php
/**
* WP helpers
*
* @package headofsales
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Allow SVG support - restricted to administrators only
add_filter( 'upload_mimes', function( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
} );

/**
 * Get template part with arguments and return the output as a string.
 */
function get_component($slug, $name = null, $args = []) {
    ob_start();
    get_template_part($slug, $name, $args);
    return ob_get_clean();
}
