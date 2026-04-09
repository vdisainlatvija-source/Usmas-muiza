<?php
/**
* WP Enqueue
*
* @package headofsales
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

	register_nav_menus( array( 'primary-menu'   => __( 'Primary', 'headofsales' ) ) );

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

	// AOS - Animate On Scroll
	wp_enqueue_style(
		'aos-css',
		'https://unpkg.com/aos@2.3.1/dist/aos.css',
		[],
		'2.3.1'
	);
	wp_enqueue_script(
		'aos-js',
		'https://unpkg.com/aos@2.3.1/dist/aos.js',
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

    wp_localize_script( 'theme-scripts', 'theme',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('theme_nonce'),
        )
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

	// Services page
	if (is_page_template('templates/services.php')) {
		$services_css_uri = '/assets/css/services.min.css';
		$services_css_time = filemtime(TEMPLATEPATH . $services_css_uri);
		wp_enqueue_style('services-style', $template_uri . $services_css_uri, [], $services_css_time);
	}

	// About page
	if (is_page_template('templates/about.php')) {
		$about_css_uri = '/assets/css/about.min.css';
		$about_css_time = filemtime(TEMPLATEPATH . $about_css_uri);
		wp_enqueue_style('about-style', $template_uri . $about_css_uri, [], $about_css_time);
	}

	// Blog page
	if (is_home()) {
		$blog_css_uri = '/assets/css/blog.min.css';
		$blog_css_time = filemtime(TEMPLATEPATH . $blog_css_uri);
		wp_enqueue_style('blog-style', $template_uri . $blog_css_uri, [], $blog_css_time);
	}

	// Single post
	if (is_singular('post')) {
		$single_css_uri = '/assets/css/single.min.css';
		$single_css_time = filemtime(TEMPLATEPATH . $single_css_uri);
		wp_enqueue_style('single-style', $template_uri . $single_css_uri, [], $single_css_time);
	}

	// 404 page
	if (is_404()) {
		$error_css_uri = '/assets/css/error.min.css';
		$error_css_time = filemtime(TEMPLATEPATH . $error_css_uri);
		wp_enqueue_style(
			'error-style',
			$template_uri . $error_css_uri,
			[],
			$error_css_time
		);
	}

	// Default page (page.php)
	if (is_page() && !is_page_template()) {
		$page_css_uri = '/assets/css/page.min.css';
		$page_css_time = filemtime(TEMPLATEPATH . $page_css_uri);
		wp_enqueue_style('page-style', $template_uri . $page_css_uri, [], $page_css_time);
	}

	// Projekts archive
	if (is_post_type_archive('project')) {
		$archive_css_uri = '/assets/css/archive-projekts.min.css';
		$archive_css_time = filemtime(TEMPLATEPATH . $archive_css_uri);
		wp_enqueue_style('archive-projekts-style', $template_uri . $archive_css_uri, [], $archive_css_time);

		$archive_js_uri = '/assets/js/archive-projekts.js';
		$archive_js_time = filemtime(TEMPLATEPATH . $archive_js_uri);
		wp_enqueue_script('archive-projekts-scripts', $template_uri . $archive_js_uri, ['jquery'], $archive_js_time, true);
	}

	// Single project
	if (is_singular('project')) {
		$single_css_uri = '/assets/css/single-projekts.min.css';
		$single_css_time = filemtime(TEMPLATEPATH . $single_css_uri);
		wp_enqueue_style('single-projekts-style', $template_uri . $single_css_uri, [], $single_css_time);
	}
}
add_action( 'wp_enqueue_scripts', 'vdisain_scripts_styles' );
