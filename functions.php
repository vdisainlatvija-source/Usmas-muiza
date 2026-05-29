<?php
/**
 * Theme functions and definitions
 *
 * @package usmasmuiza
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'THEME_VERSION', '0.0.1' ); // Current theme files version

load_theme_textdomain( 'usmasmuiza', TEMPLATEPATH.'/languages' ); // Default textdomain for translations

// Auto include PHP files
foreach (glob(TEMPLATEPATH . "/includes/*.php") as $filename){
    include $filename;
}
