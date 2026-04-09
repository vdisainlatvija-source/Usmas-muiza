# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Head of Sales** - A custom WordPress theme following the vDisain "Tammer Theme Pattern". Built for WordPress 6.5.2+ with PHP 8.0+.

## Architecture

### File Structure
- `functions.php` - Minimal, auto-includes all PHP from `/includes/`
- `includes/` - All PHP functionality (enqueue.php, hooks.php, helpers.php, ajax.php, shortcodes.php, ui.php)
- `templates/` - Page templates (homepage.php, contact.php, services.php)
- `template-parts/` - Reusable components (header.php, footer.php, components/)
- `assets/css/scss/` - **Both SCSS source and compiled .min.css files in same folder**
- `assets/js/` - JavaScript files (theme.js always loaded, page-specific conditionally)
- `acf-json/` - ACF field group exports (bi-directional sync)

### Key Patterns

**PHP String Building** - Always use `$out` variable:
```php
$out = '';
$out .= '<div class="section">';
$out .= '</div>';
echo $out;
```

**Security** - Every PHP file must have:
```php
if ( ! defined( 'ABSPATH' ) ) { exit; }
```

**Output Escaping** - Always escape: `esc_html()`, `esc_url()`, `esc_attr()`

**Conditional Asset Loading** - Page-specific CSS/JS loaded only when needed via `is_page_template()` checks in `includes/enqueue.php`

### SCSS Architecture

## SCSS
- When writing scss avoid fixed units. Only units which should have is like for paddings and marking everying else shuold be with %. To make everyting responsive.
- Page scss is only to support page layout. For font sizes, colors etc, always use fallbacks from theme.scss, ui.scss, mixins.scss files. 
- Always check _mixins.scss, _ui.scss, theme.scss before writing new styles
- Don't duplicate styles that already exist

**Color Variables** (`_mixins.scss`):
```scss
$bg: #C8C5B5;           // Main background green
$dark-green: #1A2D19;   // Dark accent
$accent: #C4F870;       // Light green accent
```

**Breakpoints**:
```scss
$phone-max: 767px;      // @include phone { }
$tablet-min: 768px;     // @include tablet { }
$tablet-max: 1149px;
$pc-min: 1150px;        // @include pc { }
```

**Typography Mixins**: `@include h1`, `@include h2`, `@include h3`, `@include h4`, `@include bodyRegular`, `@include label`

**Utility Mixins**: `@include padding`, `@include transition`, `@include hover`, `@include fixed-size($size)`

**Naming Conventions**:
- Sections: `.section-[name]`
- Main wrapper IDs: `#[page]Main` (e.g., `#homeMain`)
- Modifiers: `.-[modifier]`

### JavaScript

Always wrap in jQuery IIFE:
```javascript
(function($){
    $(document).ready(function() {
        // code here
    });
}(window.jQuery));
```

AJAX available via `theme.ajaxurl` and `theme.nonce` (localized in enqueue.php).

### Integrated Libraries
- **AOS** - Scroll animations (always loaded). Use `data-aos="fade-up"` attributes
- **Slick/Fancybox/GSAP** - Registered, enqueue per-page when needed

## Development Guidelines

### SCSS Rules
- **Avoid fixed units** - Use percentages for layout responsiveness (padding/margin exceptions allowed)
- **Page SCSS is for layout only** - Use `_mixins.scss`, `_ui.scss`, `theme.scss` for fonts, colors, shared styles
- **Check existing files first** - Don't duplicate styles from `_mixins.scss`, `_ui.scss`, `theme.scss`
- Files compile to `.min.css` in same `/assets/css/scss/` folder
- **Section/Container padding pattern** - Vertical padding goes on `.container`, NOT on the section. Section only has `position: relative; overflow: hidden;`. Example:
```scss
.section-name {
    position: relative;
    overflow: hidden;

    .container {
        padding-top: 140px;
        padding-bottom: 140px;

        @include tablet {
            padding-top: 60px;
            padding-bottom: 60px;
        }

        @include phone {
            padding-top: 40px;
            padding-bottom: 40px;
        }
    }
}
```

### Code Style
- Tabs for PHP indentation, spaces for SCSS
- Follow existing naming conventions
- Keep functions/components small and focused
- Don't create new files unless necessary - prefer editing existing

### Adding a New Page Template

1. Create `templates/[name].php` with template header and ABSPATH check
2. Create `assets/css/scss/[name].scss` importing `@use 'mixins'`
3. Compile to `assets/css/scss/[name].min.css`
4. Add conditional enqueue in `includes/enqueue.php`:
```php
if( is_page_template('templates/[name].php') ){
    $css_time = filemtime(TEMPLATEPATH . '/assets/css/scss/[name].min.css');
    wp_enqueue_style('[name]-style', $template_uri . '/assets/css/scss/[name].min.css', [], $css_time);
}
```

## No Build System

SCSS files must be compiled manually (no webpack/gulp configured). Compiled CSS uses `filemtime()` for cache busting.
