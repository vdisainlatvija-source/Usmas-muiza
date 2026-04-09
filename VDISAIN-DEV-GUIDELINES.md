# **VDISAIN WORDPRESS DEVELOPMENT GUIDELINES - TAMMER THEME PATTERN**

## **FILE STRUCTURE (REQUIRED)**

```
wp-content/themes/[theme-name]/
├── functions.php                    # Main theme setup (minimal, auto-includes)
├── style.css                        # Theme header info only
├── header.php                       # HTML head wrapper
├── footer.php                       # Closing tags wrapper
├── index.php                        # Fallback template
├── home.php                         # Blog listing
├── searchform.php                   # Search form template
├── screenshot.png                   # Theme screenshot
├── editor-style.css                 # Editor styling
├── acf-json/                        # ACF field exports
├── includes/                        # All PHP functionality
│   ├── enqueue.php                  # Scripts/styles registration
│   ├── helpers.php                  # Helper functions
│   ├── hooks.php                    # WordPress hooks/filters
│   ├── ajax.php                     # AJAX handlers
│   ├── shortcodes.php               # Shortcode definitions
│   └── ui.php                       # UI components
├── template-parts/                  # Reusable PHP components
│   ├── header.php                   # Actual header markup
│   ├── footer.php                   # Actual footer markup
│   ├── 404.php                      # 404 content
│   └── components/                  # Reusable components
│       ├── hero.php
│       ├── post-card.php
│       ├── reference-card.php
│       └── [component-name].php
├── templates/                       # Page templates
│   ├── homepage.php
│   ├── contact.php
│   ├── about.php
│   └── [template-name].php
├── search/                          # Search functionality
│   ├── search-results.php
│   └── search_functions.php
├── assets/
│   ├── css/
│   │   ├── scss/                    # Source SCSS files AND compiled CSS
│   │   │   ├── theme.scss           # Main SCSS (always required)
│   │   │   ├── theme.css            # Compiled main CSS (NEW: stored in scss folder)
│   │   │   ├── _mixins.scss         # Mixins/variables (always required)
│   │   │   ├── _ui.scss             # UI components (always required)
│   │   │   ├── _fonts.scss          # Font declarations
│   │   │   ├── _header.scss         # Header styles
│   │   │   ├── _footer.scss         # Footer styles
│   │   │   ├── home.scss            # Homepage specific
│   │   │   ├── home.css             # Compiled homepage CSS (NEW: stored in scss folder)
│   │   │   └── [page-name].scss     # Page-specific styles
│   │   │   └── [page-name].css      # Compiled page CSS (NEW: stored in scss folder)
│   ├── js/
│   │   ├── theme.js                 # Main JS (always loaded)
│   │   ├── home.js                  # Homepage specific
│   │   └── [page-name].js           # Page-specific JS
│   ├── images/
│   │   └── svg/                     # SVG assets
│   ├── fonts/                       # Custom fonts
│   └── addons/                      # Third-party libraries
│       ├── slick/
│       ├── fancybox3/
│       └── aos-master/
├── archive-[post-type].php          # Custom post type archives
├── single-[post-type].php           # Custom post type singles
└── [custom-templates].php
```

---

## **PHP CODING STANDARDS**

### **1. File Headers**
```php
<?php
/**
 * [Description]
 *
 * @package [ThemeName]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
```

### **2. Functions.php Pattern**
```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'THEME_VERSION', '0.0.1' );

load_theme_textdomain( 'theme-slug', TEMPLATEPATH.'/languages' );

// Auto include PHP files from includes/
foreach (glob(TEMPLATEPATH . "/includes/*.php") as $filename){
    include $filename;
}
```

### **3. Template Structure Pattern**
```php
<?php
/*
Template name: [Template Name]
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// ACF fields
$field_1 = get_field('field_1');
$field_2 = get_field('field_2');

$out = '';

// Build HTML string
$out .= '<main id="[pageId]Main">';
    $out .= '<section class="section-[name]">';
        $out .= '<div class="container">';
            // Content here
        $out .= '</div>';
    $out .= '</section>';
$out .= '</main>';

echo $out;

get_footer();
```

### **4. Component Pattern (template-parts/components/)**
```php
<?php
/**
 * Component description
 *
 * @package ThemeName
 */

// Enqueue component CSS only when loaded
wp_enqueue_style('component-name-style', get_template_directory_uri() . '/assets/css/component-name.min.css');

// Accept arguments
$title = $args['title'] ?? get_the_title();
$desc  = $args['desc'] ?? '';
$class = $args['class'] ?? '';

$out = '';

$out .= '<div class="component-wrapper '.$class.'">';
    $out .= '<h2>'.$title.'</h2>';
    if(!empty($desc)) {
        $out .= '<p>'.$desc.'</p>';
    }
$out .= '</div>';

echo $out;
```

### **5. Helper Functions Pattern**
```php
// Use descriptive function names
function get_component($slug, $name = null, $args = []) {
    ob_start();
    get_template_part($slug, $name, $args);
    return ob_get_clean();
}

// Animation helper
function anim() {
    return 'data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-offset="0"';
}

function animLeft() {
    return 'data-aos="fade-right" data-aos-anchor-placement="top-bottom" data-aos-offset="0"';
}
```

### **6. AJAX Pattern**
```php
add_action('wp_ajax_action_name', 'action_name');
add_action('wp_ajax_nopriv_action_name', 'action_name');

function action_name() {
    $param = sanitize_text_field($_POST['param']);

    $args = [
        'post_type' => 'post',
        'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            ob_start();
            include locate_template('template-parts/components/card.php');
            echo ob_get_clean();
        endwhile;
        wp_reset_postdata();
    }

    wp_die();
}
```

### **7. Header/Footer Pattern**
- **header.php**: Only HTML structure, includes `template-parts/header.php`
- **template-parts/header.php**: Actual header markup built with `$out` variable
- Same for footer

### **8. String Building Convention**
```php
// ALWAYS build HTML using $out variable
$out = '';

$out .= '<div class="wrapper">';
    $out .= '<h2>'.esc_html($title).'</h2>';

    if(!empty($description)) {
        $out .= '<p>'.esc_html($description).'</p>';
    }

    foreach($items as $item) {
        $out .= '<div class="item">';
            $out .= '<span>'.esc_html($item['name']).'</span>';
        $out .= '</div>';
    }
$out .= '</div>';

echo $out;
```

### **9. SVG Inline Pattern**
```php
// Use file_get_contents for inline SVG
$out .= '<div class="logo">';
    $out .= file_get_contents( get_template_directory() . '/assets/images/svg/logo.svg' );
$out .= '</div>';

// With escaping for dynamic SVG
$icon = $item['icon'];
if(!empty($icon['url'])) {
    $out .= file_get_contents( esc_attr($icon['url']) );
}
```

---

## **SCSS CODING STANDARDS**

### **1. Main SCSS Structure (_mixins.scss)**
```scss
// Colors
$primary: #191928;
$purple: #B500FF;
$white: #FCFCFC;
$gray: #818190;

// Breakpoints
$tablet-min: 768px;
$tablet-max: 1149px;
$phone-max: 767px;
$pc-min: 1150px;

// Mixins
@mixin tablet {
    @media (min-width: #{$tablet-min}) and (max-width: #{$tablet-max}) {
        @content;
    }
}
@mixin phone {
    @media (max-width: #{$phone-max}) {
        @content;
    }
}
@mixin pc {
    @media (min-width: #{$pc-min}) {
        @content;
    }
}

@mixin transition {
    -webkit-transition: all 0.25s ease !important;
    transition: all 0.25s ease !important;
}

@mixin padding {
    padding-left: 30px;
    padding-right: 30px;

    @include tablet {
        padding-left: 20px;
        padding-right: 20px;
    }
    @include phone{
        padding-left: 16px;
        padding-right: 16px;
    }
}

@mixin inter {
    font-family: "Inter", sans-serif;
}
@mixin oyko {
    font-family: "Oyko", "Inter", sans-serif;
}

@mixin bodyRegular {
    font-size: 16px;
    font-weight: 400;
    line-height: 150%;
}
```

### **2. Theme SCSS Pattern**
```scss
@import 'mixins';
@import 'ui';
@import 'header';
@import 'footer';
@import 'fonts';

section {
    @include padding;
}
.container {
    max-width: 1240px !important;
    margin: 0 auto !important;
    width: 100% !important;
}

// Global styles
body {
    @include inter;
    // styles
}
```

### **3. Page SCSS Pattern**
```scss
@import 'mixins';

main#pageIdMain {
    @include tablet {
        overflow: hidden;
    }
    @include phone {
        overflow: hidden;
    }

    section {
        &.section-hero {
            // styles

            @include tablet {
                // tablet styles
            }
            @include phone {
                // phone styles
            }
        }
    }
}
```

### **4. UI Components (_ui.scss)**
```scss
@import 'mixins';

.--button {
    @include inter;
    @include transition;

    &.-full {
        color: $white;
        background-color: $purple;
        // styles
    }

    &.-icon {
        display: flex;
        align-items: center;

        &:hover {
            color: $purple;
        }
    }
}
```

### **5. Naming Conventions**
- **Sections**: `.section-[name]` (e.g., `.section-hero`, `.section-about`)
- **Modifiers**: `.-[modifier]` (e.g., `.-full`, `.-icon`, `.-purple`)
- **Utilities**: `.--[utility]` (e.g., `.--button`)
- **IDs**: `#[page]Main` for main wrapper (e.g., `#homeMain`, `#contactMain`)

---

## **JAVASCRIPT CODING STANDARDS**

### **1. Main Theme JS Pattern**
```javascript
AOS.init({
    duration: 1000,
    once: false,
    mirror: true
});

(function($){
    $(window).on("load", function () {
        AOS.init();
    });

    $(document).ready(function() {
        headerScroll();
        searchBar();

        // Functions here

        function headerScroll() {
            function scrolling() {
                var header = document.getElementById('mainHeader');
                if (window.scrollY > 0) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
            scrolling();
            window.addEventListener('scroll', function() {
                scrolling();
            });
        }
    });
}(window.jQuery));
```

### **2. Page-Specific JS Pattern**
```javascript
(function($){
    $(document).ready(function() {
        initSlider();
        initGallery();

        function initSlider() {
            $('.slider').slick({
                slidesToShow: 1,
                infinite: true,
                arrows: true,
                dots: true,
                autoplay: true,
                autoplaySpeed: 4000,
                responsive: [
                    {
                        breakpoint: 767,
                        settings: {
                            arrows: false,
                        }
                    }
                ]
            });

            AOS.refresh();
        }
    });
}(window.jQuery));
```

### **3. AJAX Pattern**
```javascript
$.ajax({
    url: theme.ajaxurl,
    type: "POST",
    data: {
        action: "ajax_action_name",
        param: value,
        nonce: theme.nonce,
    },
    success: function (res) {
        if (res.data && res.data.html) {
            $container.html(res.data.html);
        }
    },
    complete: function () {
        // cleanup
    },
});
```

### **4. JavaScript Conventions**
- Always wrap in `(function($){ }(window.jQuery));`
- Use `$(document).ready()` for DOM manipulation
- Use `$(window).on("load")` for images/heavy content
- Define functions inside ready block
- Call `AOS.refresh()` after dynamic content or sliders
- Use `var`, not `let` or `const` for compatibility

---

## **ENQUEUE PATTERN (includes/enqueue.php)**

### **1. Theme Setup**
```php
function vdisain_theme_setup() {
    add_image_size( 'hero', 1980, 700, true );

    register_nav_menus( array( 'primary-menu'   => __( 'Primary', 'theme-slug' ) ) );
    register_nav_menus( array( 'footer1-menu'   => __( 'Footer 1', 'theme-slug' ) ) );
    register_nav_menus( array( 'footer2-menu'   => __( 'Footer 2', 'theme-slug' ) ) );

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

    add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'vdisain_theme_setup' );
```

### **2. Conditional Loading**
```php
function vdisain_scripts_styles() {
    global $post;
    $template_uri = get_template_directory_uri();

    // Always loaded
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'theme-base', $template_uri . '/style.css', [], THEME_VERSION );

    // Main theme CSS with file modification time (NEW: CSS files stored in scss folder)
    $site_css_uri = '/assets/css/scss/theme.css';
    $site_css_time = filemtime(TEMPLATEPATH . $site_css_uri);
    wp_enqueue_style( 'theme-style', $template_uri . $site_css_uri, [], $site_css_time );

    // Main theme JS
    $app_js_uri = '/assets/js/theme.js';
    $app_js_time = filemtime(TEMPLATEPATH . $app_js_uri);
    wp_enqueue_script( 'theme-scripts', $template_uri . $app_js_uri, ['jquery'], $app_js_time, true );

    // Localize script
    wp_localize_script( 'theme-scripts', 'theme',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('theme_nonce'),
        )
    );

    // Page-specific (NEW: CSS files stored in scss folder)
    if( is_page_template('templates/homepage.php') ){
        $home_css_uri = '/assets/css/scss/home.css';
        $home_css_time = filemtime(TEMPLATEPATH . $home_css_uri);
        wp_enqueue_style( 'home-style', $template_uri . $home_css_uri, [], $home_css_time );

        $home_js_uri = '/assets/js/home.js';
        $home_js_time = filemtime(TEMPLATEPATH . $home_js_uri);
        wp_enqueue_script( 'home-scripts', $template_uri . $home_js_uri, ['jquery'], $home_js_time, true );
    }
}
add_action( 'wp_enqueue_scripts', 'vdisain_scripts_styles' );
```

### **3. Register Libraries (Don't Enqueue by Default)**
```php
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

// Register Fancy Box
wp_register_style(
    'theme-fancybox',
    $template_uri . '/assets/addons/fancybox3/jquery.fancybox.min.css',
    [],
    THEME_VERSION
);
wp_register_script(
    'theme-fancybox',
    $template_uri . '/assets/addons/fancybox3/jquery.fancybox.min.js',
    ['jquery'],
    THEME_VERSION,
    true
);

// Register AOS animations (Usually enqueued globally)
wp_register_style(
    'theme-aos',
    $template_uri . '/assets/addons/aos-master/dist/aos.css',
    [],
    THEME_VERSION
);
wp_register_script(
    'theme-aos',
    $template_uri . '/assets/addons/aos-master/dist/aos.js',
    ['jquery'],
    THEME_VERSION,
    true
);
wp_enqueue_style( 'theme-aos' );
wp_enqueue_script( 'theme-aos' );
```

### **4. Template Conditional Examples**
```php
// Homepage
if( is_page_template('templates/homepage.php') ){ }

// Contact
if (is_page_template('templates/contact.php')) { }

// Custom Post Type Archive
if (is_post_type_archive('references')) { }

// Custom Post Type Archive OR Taxonomy
if (is_post_type_archive('products-tammer') || is_tax('product-category-tammer')) { }

// Single Custom Post Type
if ( is_singular('references') ) { }

// Blog
if ( is_home() ) { }

// 404
if (is_404()) { }
```

---

## **WORDPRESS INTEGRATION PATTERNS**

### **1. WP_Query Loop Pattern**
```php
$query = new WP_Query([
    'post_type'      => 'references',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $title = get_the_title();
        $link  = get_permalink();
        $image = get_the_post_thumbnail(get_the_ID(), 'large');

        $out .= '<div class="post-item">';
            $out .= '<a href="'.esc_url($link).'">'.$image.'</a>';
            $out .= '<h3>'.esc_html($title).'</h3>';
        $out .= '</div>';
    }
    wp_reset_postdata();
}
```

### **2. ACF Repeater Pattern**
```php
$repeater = get_field('repeater_field');

if(!empty($repeater)) {
    foreach($repeater as $item) {
        $title = esc_html($item['title']);
        $desc  = esc_html($item['description']);
        $link  = $item['link'];

        $out .= '<div class="item">';
            $out .= '<h3>'.$title.'</h3>';
            if(!empty($desc)) {
                $out .= '<p>'.$desc.'</p>';
            }
            if(!empty($link)) {
                $out .= '<a href="'.esc_url($link['url']).'" target="'.$link['target'].'">'.esc_html($link['title']).'</a>';
            }
        $out .= '</div>';
    }
}
```

### **3. Navigation Menu Pattern**
```php
if ( has_nav_menu( 'primary-menu' ) ) :
    $out .= '<nav class="site-navigation" role="navigation">';
        $out .= wp_nav_menu( array( 'theme_location' => 'primary-menu', 'echo' => false ) );
    $out .= '</nav>';
endif;
```

### **4. Search Form Pattern**
```php
// Use get_search_form with false to return instead of echo
$out .= get_search_form( false );
```

### **5. Template Part with Arguments**
```php
// Load component
get_template_part('template-parts/components/hero', null, [
    'title' => 'Page Title',
    'desc'  => 'Page Description',
    'class' => 'custom-class',
]);

// Or return as string
$hero = get_component('template-parts/components/hero', null, [
    'title' => 'Page Title',
]);
```

---

## **KEY PRINCIPLES**

### **Must Follow**
1. ✅ **String Building**: Always use `$out` variable to build HTML strings, then echo once
2. ✅ **Security**: Always escape output (`esc_html()`, `esc_url()`, `esc_attr()`)
3. ✅ **ABSPATH Check**: Every PHP file must check `if ( ! defined( 'ABSPATH' ) ) { exit; }`
4. ✅ **File Modification Time**: Use `filemtime()` for cache busting on CSS/JS
5. ✅ **Conditional Loading**: Load page-specific CSS/JS only when needed
6. ✅ **Components**: Create reusable components in `template-parts/components/`
7. ✅ **SCSS Organization**: Separate files per page/component, import in main
8. ✅ **Responsive**: Always use mixins for breakpoints (`@include phone`, `@include tablet`)
9. ✅ **Animations**: Use AOS library with helper functions (`anim()`, `animLeft()`, `animRight()`)
10. ✅ **jQuery Wrapper**: Always wrap JS in `(function($){ }(window.jQuery));`

### **Code Quality**
- Keep functions simple and focused
- Use descriptive function/variable names
- Comment complex logic
- Follow WordPress coding standards
- Keep main theme files clean (auto-include from `/includes/`)
- No inline styles (use classes and SCSS)
- Minimize database queries (use `no_found_rows => true` when pagination not needed)

### **Performance**
- Only enqueue scripts/styles when needed
- Use `wp_register_*` for libraries, enqueue conditionally
- Compress and minify CSS/JS
- Use proper image sizes
- Cache bust with `filemtime()`

---

## **COMMON THIRD-PARTY LIBRARIES**

### **Always Included**
- **AOS** (Animate On Scroll) - Global animations
- **jQuery** - JavaScript library

### **Conditionally Loaded**
- **Slick Slider** - Carousels/sliders
- **Fancybox** - Lightboxes/galleries
- **WPML** - Multilingual support

---

## **TRANSLATION PATTERN**

```php
// Simple string
__('Text', 'theme-slug')

// Echo string
_e('Text', 'theme-slug')

// With variables (use sprintf)
sprintf(__('Welcome %s', 'theme-slug'), $name)

// In templates
$out .= '<p>'.__('Hello World', 'theme-slug').'</p>';
```

---

## **QUICK REFERENCE**

### **File Naming**
- Templates: `templates/[name].php`
- Components: `template-parts/components/[name].php`
- SCSS: `assets/css/scss/[name].scss` (partials with `_` prefix)
- JS: `assets/js/[name].js`
- Archives: `archive-[post-type].php`
- Singles: `single-[post-type].php`

### **Common Helper Functions**
```php
anim()                          // AOS fade-up
animLeft()                      // AOS fade-right
animRight()                     // AOS fade-left
get_component($slug, $name, $args) // Get template part as string
get_country_flag($country)      // Get country flag SVG
format_excerpt($text, $length)  // Format excerpt with max length
getIconHtml($value)            // Get icon HTML (legacy or dynamic)
```

### **Common SCSS Mixins**
```scss
@include phone { }              // Mobile styles
@include tablet { }             // Tablet styles
@include pc { }                 // Desktop styles
@include padding                // Responsive padding
@include transition             // CSS transition
@include inter                  // Inter font
@include oyko                   // Oyko font
@include bodyRegular            // Body text styles
```

---

**This is the vDisain standard for WordPress theme development.**

---

## **EXAMPLE: CREATING A NEW PAGE TEMPLATE**

### Step 1: Create Template File
**File**: `wp-content/themes/theme-name/templates/services.php`

```php
<?php
/*
Template name: Services
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// ACF fields
$hero_title = get_field('hero_title');
$hero_desc  = get_field('hero_description');
$services   = get_field('services_repeater');

$out = '';

// Hero Section
get_template_part('template-parts/components/hero', null, [
    'title' => $hero_title,
    'desc'  => $hero_desc,
]);

// Services Section
$out .= '<main id="servicesMain">';
    $out .= '<section class="section-services">';
        $out .= '<div class="container">';
            if(!empty($services)) {
                foreach($services as $service) {
                    $title = esc_html($service['title']);
                    $desc  = esc_html($service['description']);
                    $icon  = $service['icon'];

                    $out .= '<div class="service-item" '.anim().'>';
                        if(!empty($icon)) {
                            $out .= '<div class="icon">';
                                $out .= file_get_contents($icon['url']);
                            $out .= '</div>';
                        }
                        $out .= '<h3>'.$title.'</h3>';
                        $out .= '<p>'.$desc.'</p>';
                    $out .= '</div>';
                }
            }
        $out .= '</div>';
    $out .= '</section>';
$out .= '</main>';

echo $out;

get_footer();
```

### Step 2: Create SCSS File
**File**: `wp-content/themes/theme-name/assets/css/scss/services.scss`

```scss
@import 'mixins';

main#servicesMain {
    section {
        &.section-services {
            padding: 80px 0;

            @include phone {
                padding: 40px 0;
            }

            .service-item {
                padding: 30px;
                background: $white;
                border-radius: 8px;
                @include transition;

                &:hover {
                    @include hover;
                }

                .icon {
                    width: 50px;
                    height: 50px;
                    margin-bottom: 20px;

                    svg {
                        width: 100%;
                        height: 100%;
                    }
                }

                h3 {
                    @include oyko;
                    margin-bottom: 10px;
                }

                p {
                    @include bodyRegular;
                    color: $gray;
                }
            }
        }
    }
}
```

### Step 3: Create JS File (if needed)
**File**: `wp-content/themes/theme-name/assets/js/services.js`

```javascript
(function($){
    $(document).ready(function() {
        initServicesAnimation();

        function initServicesAnimation() {
            $('.service-item').on('mouseenter', function() {
                $(this).addClass('active');
            }).on('mouseleave', function() {
                $(this).removeClass('active');
            });
        }
    });
}(window.jQuery));
```

### Step 4: Enqueue Assets
**File**: `wp-content/themes/theme-name/includes/enqueue.php`

Add to `vdisain_scripts_styles()` function:

```php
if( is_page_template('templates/services.php') ){
    $services_css_uri = '/assets/css/services.min.css';
    $services_css_time = filemtime(TEMPLATEPATH . $services_css_uri);
    wp_enqueue_style(
        'services-style',
        $template_uri . $services_css_uri,
        [],
        $services_css_time
    );

    $services_js_uri = '/assets/js/services.js';
    $services_js_time = filemtime(TEMPLATEPATH . $services_js_uri);
    wp_enqueue_script(
        'services-scripts',
        $template_uri . $services_js_uri,
        ['jquery'],
        $services_js_time,
        true
    );
}
```

### Step 5: Compile SCSS
Compile `services.scss` to `services.min.css` using your build tool.

---

**Done! Your new page template follows the vDisain standard.**
