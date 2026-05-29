<?php
/**
* WP Enqueue
*
* @package usmasmuiza
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Set up theme support.
 *
 * @return void
 */
function vdisain_theme_setup() {
	add_image_size( 'hero', 1980, 700, true );

	register_nav_menus( array( 'primary-menu'   => __( 'Primary', 'usmasmuiza' ) ) );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 350,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}
add_action( 'after_setup_theme', 'vdisain_theme_setup' );

/**
 * Theme Scripts & Styles.
 *
 * @return void
 */
function vdisain_scripts_styles() {
	$template_uri = get_template_directory_uri();

	wp_enqueue_script( 'jquery' );

	// Base style.css
	wp_enqueue_style(
		'theme-base',
		$template_uri . '/style.css',
		[],
		THEME_VERSION
	);

	// Register Slick Slider
	wp_register_style(
		'theme-slick-slider',
		$template_uri . '/assets/addons/slick/slick.css',
		[],
		THEME_VERSION
	);
	wp_register_script(
		'theme-slick-slider',
		$template_uri . '/assets/addons/slick/slick.min.js',
		['jquery'],
		THEME_VERSION,
		true
	);

	// AOS - Animate On Scroll (vendored locally)
	wp_enqueue_style(
		'aos-css',
		$template_uri . '/assets/addons/aos/aos.css',
		[],
		'2.3.1'
	);
	wp_enqueue_script(
		'aos-js',
		$template_uri . '/assets/addons/aos/aos.js',
		[],
		'2.3.1',
		true
	);

	// Main CSS theme file
	$site_css_uri = '/assets/css/theme.min.css';
	$site_css_time = filemtime(TEMPLATEPATH . $site_css_uri);
	wp_enqueue_style(
		'theme-style',
		$template_uri . $site_css_uri,
		[],
		$site_css_time
	);

	// Main JS theme file
	$app_js_uri = '/assets/js/theme.js';
	$app_js_time = filemtime(TEMPLATEPATH . $app_js_uri);
	wp_enqueue_script(
		'theme-scripts',
		$template_uri . $app_js_uri,
		['jquery'],
		$app_js_time,
		true
	);

	// Header JS file
	$header_js_uri = '/assets/js/header.js';
	$header_js_time = filemtime(TEMPLATEPATH . $header_js_uri);
	wp_enqueue_script(
		'header-scripts',
		$template_uri . $header_js_uri,
		[],
		$header_js_time,
		true
	);



	// Homepage
	if( is_page_template('templates/homepage.php') ){
		$home_css_uri = '/assets/css/home.min.css';
		$home_css_time = filemtime(TEMPLATEPATH . $home_css_uri);
		wp_enqueue_style(
			'home-style',
			$template_uri . $home_css_uri,
			[],
			$home_css_time
		);
	}
}
add_action( 'wp_enqueue_scripts', 'vdisain_scripts_styles' );
