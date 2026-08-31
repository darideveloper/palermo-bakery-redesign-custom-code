<?php
define( 'SNSVICKY_THEME_DIR', get_template_directory() );
define( 'SNSVICKY_THEME_URI', get_template_directory_uri() );
require_once( SNSVICKY_THEME_DIR.'/framework/init.php' );
/** 
 *   Initialize Visual Composer in the theme.
 **/
add_action( 'vc_before_init', 'snsvicky_vc_setastheme' );
function snsvicky_vc_setastheme() {
	if ( function_exists('vc_set_as_theme') ) vc_set_as_theme(true);
}
/** 
 *	Width of content, it's max width of content without sidebar.
 **/
if ( ! isset( $content_width ) ) { $content_width = 660; }

/** 
 *	Set base function for theme.
 **/
if ( ! function_exists( 'snsvicky_setup' ) ) {
    function snsvicky_setup() {
        global $snsvicky_opt;
    	// Load default theme textdomain.
        load_theme_textdomain( 'snsvicky' , SNSVICKY_THEME_DIR . '/languages' );
		// Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );
		// Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );
        // Add title-tag, it auto title of head
        add_theme_support( 'title-tag' );
        // Enable support for Post Formats.
        add_theme_support( 'post-formats',
            array(
                'video', 'quote', 'link', 'gallery'
            )
        );
        // Register images size
        add_image_size('snsvicky_blog_tiny_thumb', 70,70, true);
        add_image_size('snsvicky_blog_small_thumb', 170,130, true);
        add_image_size('snsvicky_blog_large_thumb', 370,270, true);
        if(class_exists('WooCommerce')){
            add_image_size('snsvicky_woo_7080_thumb', 70,80, true);
            add_image_size('snsvicky_woo_110127_thumb', 110,127, true);
        }
		//Setup the WordPress core custom background & custom header feature.
         $default_background = array(
            'default-color' => '#FFF',
        );
        add_theme_support( 'custom-background', $default_background );
        $default_header = array();
        add_theme_support( 'custom-header', $default_header );
        // Register navigations
	    register_nav_menus( array(
			'main_navigation' => esc_html__( 'Main navigation', 'snsvicky' ),
            'myaccount_navigation'  => esc_html__( 'My Account navigation', 'snsvicky' ),
		) );
    }
    add_action ( 'after_setup_theme', 'snsvicky_setup' );
}

/** 
    Add class for body
 **/
add_filter( 'body_class', 'snsvicky_bodyclass' );
function snsvicky_bodyclass( $classes ) {
    if ( snsvicky_themeoption('use_boxedlayout', 0) == 1) {
        $classes[] = 'boxed-layout';
    }
    $classes[] = 'layout-type-'.snsvicky_layouttype('layouttype', 'l-m');
    if( snsvicky_themeoption('advance_tooltip', 1) == 1){
        $classes[] = 'use-tooltip';
    }
    if( snsvicky_themeoption('use_stickmenu') == 1){
        $classes[] = 'use_stickmenu';
    }
    if ( snsvicky_themeoption('woo_uselazyload') == 1 ){
        $classes[] = 'use_lazyload';
    }
    if ( is_page() && snsvicky_metabox('useslideshow') == 1 && snsvicky_metabox('revolutionslider') != '' ) {
        $classes[] = 'use-slideshow';
        if ( snsvicky_metabox('page_class') ) {
            $classes[] = snsvicky_metabox('page_class');
        }
    }
    $classes[] = 'header-'.snsvicky_getoption('header_style', 'style1');
    $classes[] = 'footer-'.snsvicky_getoption('footer_layout', 'layout_1');
    if ( snsvicky_getoption('enable_search_cat') == true ) $classes[] = 'enable-search-cat';
    if(class_exists('WooCommerce')){
        global $product;
        $classes[] = 'woocommerce';
        $woo_onsale_layout = snsvicky_themeoption('woo_onsale_layout', 'carousel');
        if(is_product_category()){
            $cate = get_queried_object();
            $woo_onsale_layout = snsvicky_get_term_byid($cate->term_id, 'snsvicky_woo_onsale_layout');
        }
        if( $woo_onsale_layout != '' ){
            $classes[] = 'sns-archive-product-onsale-'.$woo_onsale_layout;
        }
        if( is_product() && $product->get_type() === 'variable' && snsvicky_themeoption('woo_designvariations', 1) == 1 && snsvicky_getoption('use_variation_thumb', 1) == 1 ){
            $classes[] = 'use-variation-thumb';
        }
        if ( is_product() ) {
            $classes[] = 'zoom-type-'.snsvicky_getoption('woo_zoomtype');
        }
    }

    return $classes;
}

function snsvicky_widgetlocations(){
    // Register widgetized locations
    if(function_exists('register_sidebar')) {
        register_sidebar(array(
           'name' => esc_html__( 'Widget Area','snsvicky' ),
           'id'   => 'widget-area',
            'description'   => esc_html__( 'These are widgets for the Widget Area.','snsvicky' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title"><span>',
            'after_title' => '</span></h3>',
        ));
        register_sidebar(array(
	        'name' => esc_html__( 'Menu Sidebar #1','snsvicky' ),
	        'id'   => 'menu_sidebar_1',
	        'description'   => esc_html__( 'These are widgets for Mega Menu Columns style. This sidebar displayed in the left of column.','snsvicky' ),
	        'before_widget' => '<div class="widget sidebar-menu-widget %2$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h4 class="hide">',
	        'after_title'   => '</h4>'
        ));
        register_sidebar(array(
	        'name' => esc_html__( 'Menu Sidebar #2','snsvicky' ),
	        'id'   => 'menu_sidebar_2',
	        'description'   => esc_html__( 'These are widgets for Mega Menu Columns style. This sidebar displayed in the right of column.','snsvicky' ),
	        'before_widget' => '<div class="widget sidebar-menu-widget %2$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h4 class="hide">',
	        'after_title'   => '</h4>'
        ));
        register_sidebar(array(
            'name' => esc_html__( 'Menu Sidebar #3','snsvicky' ),
            'id'   => 'menu_sidebar_3',
            'description'   => esc_html__( 'These are widgets for Mega Menu Columns style. This sidebar displayed in the bottom of menu.','snsvicky' ),
            'before_widget' => '<div class="widget sidebar-menu-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="hide">',
            'after_title'   => '</h4>'
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Top Header','snsvicky' ),
           'id'   => 'top-header',
            'description'   => esc_html__( 'Used for Top Header','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget top-header %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Top Header 2','snsvicky' ),
           'id'   => 'top-header-2',
            'description'   => esc_html__( 'Used for Top Header','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget top-header-2 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Main Header','snsvicky' ),
           'id'   => 'main-header',
            'description'   => esc_html__( 'Used for Main Header','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget main-header %2$s">',       
            'after_widget'  => '</div>',
        )); 
        register_sidebar(array(
           'name' => esc_html__( 'Footer Box #1','snsvicky' ),
           'id'   => 'footer-box-1',
            'description'   => esc_html__( 'These are widgets for Footer Box #1','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget footer_box_1 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Footer Box #2','snsvicky' ),
           'id'   => 'footer-box-2',
            'description'   => esc_html__( 'These are widgets for Footer Box #2','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget footer_bo_2 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Footer Box #3','snsvicky' ),
           'id'   => 'footer-box-3',
            'description'   => esc_html__( 'These are widgets for Footer Box #3','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget footer_box_3 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Footer Box #4','snsvicky' ),
           'id'   => 'footer-box-4',
            'description'   => esc_html__( 'These are widgets for Footer Box #4','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget footer_box_4 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
           'name' => esc_html__( 'Footer Box #5','snsvicky' ),
           'id'   => 'footer-box-5',
            'description'   => esc_html__( 'These are widgets for Footer Box #5','snsvicky' ),
            'before_widget' => '<div id="%1$s" class="widget footer_box_5 %2$s">',       
            'after_widget'  => '</div>',
        ));
        register_sidebar(array(
            'name' => esc_html__( 'Product Sidebar','snsvicky' ),
            'id' => 'product-sidebar',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title"><span>',
            'after_title' => '</span></h3>',
        ));
        register_sidebar(array(
            'name' => esc_html__( 'Woo Sidebar','snsvicky' ),
            'id' => 'woo-sidebar',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title"><span>',
            'after_title' => '</span></h3>',
        ));
        register_sidebar(array(
            'name' => esc_html__( 'Woo Sidebar - Horizontal','snsvicky' ),
            'id'   => 'woo-sidebar-horizontal',
            'before_widget' => '<div id="%1$s" class="widget woo-sidebar-horizontal col-md-3 %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>'
        ));
        register_sidebar(array(
            'name' => esc_html__( 'Blog Sidebar','snsvicky' ),
            'id' => 'blog-sidebar',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title"><span>',
            'after_title' => '</span></h3>',
        ));
      
    }
}
add_action( 'widgets_init', 'snsvicky_widgetlocations' );
/** 
 *	Add styles & scripts
 **/
function snsvicky_scripts() {
	global $snsvicky_opt, $wp_query;
    $optimize = '.min'; $optimize = '';
	// Enqueue style
	$css_file = snsvicky_css_file();
	wp_enqueue_style('bootstrap', SNSVICKY_THEME_URI . '/assets/css/bootstrap.min.css');
    wp_enqueue_style('owlcarousel', SNSVICKY_THEME_URI . '/assets/css/owl.carousel.min.css');
    wp_enqueue_style('slick', SNSVICKY_THEME_URI . '/assets/css/slick.min.css');
	wp_enqueue_style('font-awesome', SNSVICKY_THEME_URI . '/assets/fonts/awesome/css/font-awesome.min.css');
    wp_enqueue_style('snsvicky-ie9', SNSVICKY_THEME_URI . '/assets/css/ie9.css');
    wp_enqueue_style('select2', SNSVICKY_THEME_URI . '/assets/css/select2.min.css' );
	wp_enqueue_style('snsvicky-theme-style', SNSVICKY_THEME_URI . '/assets/css/' . $css_file);

    wp_register_script('slick', SNSVICKY_THEME_URI . '/assets/js/slick'.$optimize.'.js', array('jquery', 'jquery-ui-autocomplete'), '', true); wp_enqueue_script('slick');
	wp_register_script('owlcarousel', SNSVICKY_THEME_URI . '/assets/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('owlcarousel'); // Alway enqueue
    wp_enqueue_script('resizesensor', SNSVICKY_THEME_URI . '/assets/js/resizesensor.js', array('jquery'), '', true);
    wp_enqueue_script('sticky-sidebar', SNSVICKY_THEME_URI . '/assets/js/sticky-sidebar.js', array('jquery'), '', true);
	wp_register_script('masonry', SNSVICKY_THEME_URI . '/assets/js/masonry.pkgd.min.js', array('jquery'), '', true);
	wp_register_script('imagesloaded', SNSVICKY_THEME_URI . '/assets/js/imagesloaded.pkgd.min.js', array('jquery'), '', true);
    wp_register_script('snsvicky-blog-ajax', SNSVICKY_THEME_URI . '/assets/js/ajax.js', array('jquery'), '', true);
	wp_register_script('countdown', SNSVICKY_THEME_URI . '/assets/countdown/jquery.countdown.min.js', array('jquery'), '2.1.0', true);
    // Enqueue script
    wp_enqueue_script('bootstrap', SNSVICKY_THEME_URI . '/assets/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('bootstrap-tabdrop', SNSVICKY_THEME_URI . '/assets/js/bootstrap-tabdrop.min.js', array('jquery'), '', true);
    wp_enqueue_script('select2', SNSVICKY_THEME_URI.'/assets/js/select2.min.js', array(), '', true);
    if( snsvicky_themeoption('woo_uselazyload') == 1 ) wp_enqueue_script('lazyload', SNSVICKY_THEME_URI . '/assets/js/jquery.lazyload'.$optimize.'.js', array(), '', true);
    wp_enqueue_script('waitforimages', SNSVICKY_THEME_URI.'/assets/js/jquery.waitforimages'.$optimize.'.js', array(), '', true);
    
    if(class_exists('WooCommerce')){
        if ( $snsvicky_opt['woo_usecloudzoom'] ){
            wp_enqueue_script('jquery-elevatezoom', SNSVICKY_THEME_URI.'/assets/js/jquery.elevatezoom'.$optimize.'.js', array('jquery'), '', true);
        }
        wp_enqueue_script('snsvicky-woocommerce', SNSVICKY_THEME_URI.'/assets/js/sns-woocommerce.js', array('jquery'), '', true);
    }
    wp_enqueue_script('snsvicky-script', SNSVICKY_THEME_URI . '/assets/js/sns-script.js', array('jquery'), '', true);
    // IE
    wp_enqueue_script('html5shiv', SNSVICKY_THEME_URI . '/assets/js/html5shiv.min.js', array('jquery'), '');
    wp_script_add_data('html5shiv', 'conditional', 'lt IE 9');
    wp_enqueue_script('respond', SNSVICKY_THEME_URI . '/assets/js/respond.min.js', array('jquery'), '');
    wp_script_add_data('respond', 'conditional', 'lt IE 9');
    // Add style inline with option in admin theme option
    wp_add_inline_style('snsvicky-theme-style', snsvicky_cssinline());
    // Add inline scritp
    wp_add_inline_script('snsvicky-script', snsvicky_jsinline());
    // Code to declare the URL to the file handing the AJAX request
    $js_params = array(
    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'query_vars' => $wp_query->query_vars
    );
    wp_localize_script('ajax-request', 'sns', $js_params);
    
}
add_action( 'wp_enqueue_scripts', 'snsvicky_scripts' );

/*
 * Enqueue admin styles and scripts
 */
function snsvicky_admin_styles_scripts(){
	wp_enqueue_style('snsvicky_admin_style', SNSVICKY_THEME_URI.'/admin/assets/css/admin-style.css');
	wp_enqueue_style( 'wp-color-picker' );
	
	wp_enqueue_media();
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script('snsvicky_admin_template_js', SNSVICKY_THEME_URI.'/admin/assets/js/admin_template.js', array( 'jquery', 'wp-color-picker' ), false, true);
}
add_action('admin_enqueue_scripts', 'snsvicky_admin_styles_scripts');

// Editor style
add_editor_style('assets/css/editor-style.css');
/**
 * CSS inline
**/
function snsvicky_cssinline(){
    global $snsvicky_opt;
    $inline_css = '';
    // CSS custom properties so the custom WPBakery components (WPBakery-custom-addons
    // "wca-*") track the theme's Body/Heading font choices instead of hardcoding
    // their own fonts. The components reference these via var() with fallbacks.
    $inline_css .= ':root{'
        . '--wca-font-display:' . snsvicky_font_family_value( 'heading_font', 'Cormorant Garamond', 'Georgia, serif' ) . ';'
        . '--wca-font-body:' . snsvicky_font_family_value( 'body_font', 'Montserrat', 'Arial, sans-serif' ) . ';'
        . '}';
    // Body style
    $bodycss = '';
    if (snsvicky_themeoption('use_boxedlayout') == 1) {
        if( $snsvicky_opt['body_bg_type'] == 'img' ){
            $bodycss .= 'background-image: url('.$snsvicky_opt['body_bg_type_img']['url'].');';
        }
    }
    // Body typography (from the "Body font" theme option). Only valid CSS
    // properties are emitted, so junk keys (google, font-options, subsets,
    // font-backup) never leak into the stylesheet as invalid declarations.
    $bodycss .= snsvicky_typography_css(
        isset($snsvicky_opt['body_font']) ? $snsvicky_opt['body_font'] : array(),
        'Montserrat', 'Arial, sans-serif'
    );
    $inline_css .= 'body {'.$bodycss.'}';

    // Heading typography (from the "Heading font" theme option). Applied to
    // every heading across the theme, WPBakery and WooCommerce. font-family is
    // forced (!important) so it also beats page-builder inline styles.
    $heading_css = snsvicky_typography_css(
        isset($snsvicky_opt['heading_font']) ? $snsvicky_opt['heading_font'] : array(),
        'Cormorant Garamond', 'Georgia, serif', true
    );
    if ( $heading_css !== '' ) {
        $inline_css .= 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6,'
            . '.entry-title,.page-title,.post-title,.section-title,.block-title,.sns-heading,'
            . '.widget-title,.widget_title,.vc_custom_heading,.wpb_heading,'
            . '.product_title,.woocommerce-loop-product__title,.products .product h2,'
            . 'blockquote{' . $heading_css . '}';
    }

    // --- Overlay header on the hero slideshow (Celeste-style) --------------
    // Applied only when tpl-head-4 added the `.head-overlay` class (pages that
    // render the full-bleed slider). Lifts the whole header onto the first hero
    // image via z-index, drops the bar backgrounds/borders, and recolours the
    // socials, nav, logo and favorites/cart so they read over the image.
    // Layout (socials | logo | favorites, nav row below) is unchanged.
    //
    // Colour follows the Customizer option Header > "Header Overlay Color":
    //   light (default) = white text/logo + dark scrim  -> dark/photo heroes
    //   dark            = dark text/logo  + light scrim  -> bright/light heroes
    // BOTH palettes are emitted, each scoped to `.scheme-light` / `.scheme-dark`
    // on #sns_header. tpl-head-4 sets the active class from the saved option, and
    // the Customizer preview (assets/js/customizer-preview.js) swaps that class
    // live via postMessage — so the preview repaints without a server round trip
    // (Redux does not surface in-progress option values to the front-end render).
    $body_font_var = "var(--wca-font-body), 'Montserrat', Arial, sans-serif";
    // Scheme-independent structural rules.
    $inline_css .= ''
        // Lift header out of flow and over the slideshow.
        . '#sns_header.head-overlay{position:absolute;top:0;left:0;right:0;width:100%;z-index:50;background:transparent;}'
        // Top scrim shell (colour set per scheme below).
        . '#sns_header.head-overlay::before{content:"";position:absolute;top:0;left:0;right:0;height:260px;pointer-events:none;z-index:0;}'
        . '#sns_header.head-overlay > *{position:relative;z-index:1;}'
        // Strip the solid backgrounds / dividers from each bar.
        . '#sns_header.head-overlay .sns-promobar,'
            . '#sns_header.head-overlay .main-header,'
            . '#sns_header.head-overlay #sns_menu.menu-header,'
            . '#sns_header.head-overlay #sns_mainnav{background:transparent !important;border:0 !important;box-shadow:none !important;}'
        // Socials are WPBakery vc_icon links whose <a> carries a white circular
        // badge background; drop it so only the clean glyph shows (both schemes).
        . '#sns_header.head-overlay .left-mheader a{background:transparent !important;box-shadow:none !important;}'
        // Main navigation typography (colour is per scheme below).
        . '#sns_header.head-overlay #sns_mainmenu .nav > li > a{font-family:' . $body_font_var . ';'
            . 'text-transform:uppercase;letter-spacing:2px;font-size:11px;font-weight:500;}';

    // Per-scheme colour palettes. The .number count bubble keeps its own (dark)
    // badge styling in both schemes, so it is intentionally left untouched.
    $head_schemes = array(
        'light' => array(
            'fg'     => '#ffffff',
            'scrim'  => 'linear-gradient(to bottom,rgba(0,0,0,.5) 0%,rgba(0,0,0,.2) 45%,rgba(0,0,0,0) 100%)',
            'shadow' => '0 1px 3px rgba(0,0,0,.45)',
            'logo'   => 'brightness(0) invert(1)',
            'border' => 'rgba(255,255,255,.4)',
        ),
        'dark'  => array(
            'fg'     => '#1a1a1a',
            'scrim'  => 'linear-gradient(to bottom,rgba(255,255,255,.6) 0%,rgba(255,255,255,.25) 45%,rgba(255,255,255,0) 100%)',
            'shadow' => '0 1px 3px rgba(255,255,255,.6)',
            'logo'   => 'none',
            'border' => 'rgba(0,0,0,.2)',
        ),
    );
    foreach ( $head_schemes as $name => $p ) {
        $s = '#sns_header.head-overlay.scheme-' . $name;
        $inline_css .= ''
            . $s . '::before{background:' . $p['scrim'] . ';}'
            . $s . ' .sns-promobar,' . $s . ' .sns-promobar *{color:' . $p['fg'] . ';border-color:' . $p['border'] . ';}'
            . $s . ' .left-mheader a,' . $s . ' .left-mheader a:hover,'
                . $s . ' .left-mheader .vc_icon_element-icon,' . $s . ' .left-mheader i{color:' . $p['fg'] . ' !important;}'
            . $s . ' .cart-account a,' . $s . ' .cart-account .cart-label,'
                . $s . ' .cart-account .login-regis,' . $s . ' .cart-account .usr-welcome,'
                . $s . ' .cart-account i{color:' . $p['fg'] . ' !important;}'
            . $s . ' #sns_mainmenu .nav > li > a{color:' . $p['fg'] . ';}'
            . $s . ' #sns_mainmenu .nav > li > a:hover,'
                . $s . ' #sns_mainmenu .nav > li.current-menu-item > a,'
                . $s . ' #sns_mainmenu .nav > li.current_page_item > a{color:' . $p['fg'] . ';opacity:.7;}'
            . $s . ' #sns_menu a,' . $s . ' #sns_menu i{color:' . $p['fg'] . ';}'
            // Search toggle (FontAwesome glyph on .search-input/.sns-searchwrap
            // ::before, which carries its own dark colour) -> scheme colour.
            . $s . ' .sns-searchwrap,' . $s . ' .search-input,'
                . $s . ' .sns-searchwrap::before,' . $s . ' .sns-searchwrap *::before,'
                . $s . ' .search-input::before{color:' . $p['fg'] . ' !important;}'
            . $s . ' #logo img,' . $s . ' .sns-icon-nav .btn-navbar{filter:' . $p['logo'] . ';}'
            . $s . ' .sns-promobar,' . $s . ' .left-mheader,' . $s . ' .cart-account,'
                . $s . ' #sns_mainmenu .nav > li > a{text-shadow:' . $p['shadow'] . ';}';
    }
    // ----------------------------------------------------------------------

    // --- Scroll-into-view reveal (one-time) --------------------------------
    // Matches the demo: content fades up (translateY 30px -> 0) the first time
    // it enters the viewport, then stays. reveal.js adds `.sns-reveal` to the
    // target blocks and `.is-visible` once they scroll in; reduced-motion users
    // (and no-JS) just see the content normally.
    $reveal_ease = 'cubic-bezier(.4,0,.2,1)';
    $reveal_dur  = '1.1s'; // reveal transition duration (paired with the JS delays)
    $inline_css .= ''
        // Hidden base = fade only, applied instantly (transition:none beats the
        // theme's own row opacity transition) so tagging never causes a fade-OUT.
        . '#sns_content .sns-reveal{opacity:0;transition:none;will-change:opacity,transform;}'
        // Optional directional offset (only on elements that should also slide).
        . '#sns_content .sns-reveal.sns-reveal--up{transform:translateY(30px);}'
        . '#sns_content .sns-reveal.sns-reveal--left{transform:translateX(-40px);}'
        . '#sns_content .sns-reveal.sns-reveal--right{transform:translateX(40px);}'
        // Revealed: the transition lives here, so it only animates INTO view.
        . '#sns_content .sns-reveal.is-visible{opacity:1;transform:none;'
            . 'transition:opacity ' . $reveal_dur . ' ' . $reveal_ease . ',transform ' . $reveal_dur . ' ' . $reveal_ease . ';}'
        . '@media (prefers-reduced-motion:reduce){#sns_content .sns-reveal{opacity:1 !important;transform:none !important;transition:none !important;}}';
    // ----------------------------------------------------------------------

    // --- WPBakery image carousel (.vc_images_carousel) ---------------------
    // Match the demo "Our Collections" gallery: crisp square, borderless images.
    // The carousel images otherwise pick up a 4px corner radius from the theme.
    $inline_css .= ''
        . '.vc_images_carousel .vc_item img,.vc_images_carousel img{border-radius:0 !important;border:0 !important;}';
    // ----------------------------------------------------------------------

    // --- Mobile menu: full-width, drops down from the top ------------------
    // The theme's responsive menu (#sns_content_rsm, toggled by the hamburger)
    // is a 290px drawer that slides in from the left. Re-style it on mobile as a
    // full-screen panel that drops down from the top and covers the header, with
    // its own visible close (X). Closing is wired up in snsvicky_resmenu_close_js.
    $inline_css .= ''
        . '@media (max-width:991px){'
            // Full-screen panel, hidden above the viewport, drops down on .active.
            . '#sns_content_rsm{position:fixed;top:0;left:0;right:0;width:100%;height:100vh;height:100dvh;'
                . 'overflow-y:auto;transform:translateY(-100%);transition:transform .35s ease-out;'
                . 'background:#1a1a1a;padding:88px 24px 40px;text-align:center;z-index:99999;}'
            . '#sns_content_rsm.active{transform:translateY(0);}'
            // Visible close button (white X) in the top-right of the panel.
            . '.sns-resmenu-close{position:absolute;top:22px;right:18px;width:42px;height:42px;'
                . 'background:transparent;border:0;padding:0;cursor:pointer;z-index:2;}'
            . '.sns-resmenu-close::before,.sns-resmenu-close::after{content:"";position:absolute;'
                . 'top:50%;left:50%;width:26px;height:2px;background:#fff;}'
            . '.sns-resmenu-close::before{transform:translate(-50%,-50%) rotate(45deg);}'
            . '.sns-resmenu-close::after{transform:translate(-50%,-50%) rotate(-45deg);}'
            // Light, airy menu items on the dark panel.
            . '#sns_content_rsm #sns_resmenu{line-height:54px;}'
            . '#sns_content_rsm #sns_resmenu .resp-nav a{'
                . 'font-family:var(--wca-font-body),\'Montserrat\',Arial,sans-serif;font-weight:500;'
                . 'letter-spacing:.12em;text-transform:uppercase;font-size:14px;}'
            . '#sns_content_rsm #sns_resmenu .resp-nav .accr_header{border-bottom:1px solid rgba(255,255,255,.14);}'
            // The panel is ALWAYS dark, so keep its text/icons/search light no
            // matter the header overlay colour scheme. The panel sits inside
            // .left-mheader, so the Dark scheme\'s `.left-mheader a` / search
            // rules (which carry !important) would otherwise darken these — two
            // ids here outrank the scheme\'s single id.
            . '#sns_header #sns_content_rsm a,#sns_header #sns_content_rsm i,'
                . '#sns_header #sns_content_rsm .vc_icon_element-icon,'
                . '#sns_header #sns_content_rsm .search-input,#sns_header #sns_content_rsm .sns-searchwrap,'
                . '#sns_header #sns_content_rsm .sns-searchwrap *,'
                . '#sns_header #sns_content_rsm .sns-searchwrap::before,'
                . '#sns_header #sns_content_rsm .search-input::before{color:#fff !important;}'
            . '#sns_header #sns_content_rsm #sns_resmenu .resp-nav a:hover,'
                . '#sns_header #sns_content_rsm #sns_resmenu .resp-nav a.active{color:#c4a162 !important;}'
            // The socials render as white circular badges (.sns-info-inline.social_rounded a);
            // the white-icon rule above whitened their glyphs too -> white-on-white/invisible.
            // Keep the white badge, darken the glyph (2 classes outrank that rule) so the icons read.
            . '#sns_header #sns_content_rsm .sns-info-inline .vc_icon_element-icon,'
                . '#sns_header #sns_content_rsm .sns-info-inline a i{color:#1a1a1a !important;}'
        . '}'
        // Hide the close button outside the mobile menu context.
        . '@media (min-width:992px){.sns-resmenu-close{display:none;}}'
        // Book Appointment (1109), mobile: WPBakery's .vc_column-inner default
        // 15px side padding squeezes the already-narrow column and breaks the
        // header text; columns are stacked full-width here and .container
        // still provides the page gutter, so drop the inner padding.
        . '@media (max-width:767px){'
            . '.page-id-1109 #sns_content .vc_column-inner{padding-left:0;padding-right:0;}'
        . '}'
        // Cupcake contact popup (custom-css-js "custom-popup-form"), mobile:
        // its full-screen mode uses 40px+60px padding and a ~180px textarea,
        // so the form overflows and scrolls on phones. Compact everything so
        // the whole form fits the viewport. (Overrides the plugin entry; the
        // extra #custom-popup-wrapper ancestor outranks its single-id rules.)
        . '@media (max-width:600px){'
            . '#custom-popup-wrapper #popup-form-container{padding:14px 16px !important;}'
            . '#custom-popup-wrapper #popup-form-container .popup-content{padding-bottom:0 !important;}'
            . '#custom-popup-wrapper #popup-form-container .popup-header{margin-bottom:4px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form > p:first-of-type{font-size:13px;line-height:1.45;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form p{margin:0 0 10px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form label{font-size:13px;margin-bottom:3px;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form-control:not(.wpcf7-submit){padding:9px 12px;}'
            . '#custom-popup-wrapper #popup-form-container textarea{height:84px !important;min-height:84px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-submit{padding:12px 20px !important;}'
        . '}'
        // Desktop: the 350px panel capped at 80vh still scrolls (content ~790px
        // — overflows even tall screens, badly on 768px laptops). Widen it and
        // compact spacing moderately so the whole form fits; cap height to the
        // room above the cupcake anchor as a safety net.
        . '@media (min-width:601px){'
            . '#custom-popup-wrapper #popup-form-container{width:420px;padding:20px 24px !important;'
                . 'max-height:calc(100vh - 150px);}'
            . '#custom-popup-wrapper #popup-form-container .popup-header{margin-bottom:4px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form > p:first-of-type{font-size:13px;line-height:1.5;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form p{margin:0 0 10px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form label{margin-bottom:3px;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form-control:not(.wpcf7-submit){padding:10px 12px;}'
            . '#custom-popup-wrapper #popup-form-container textarea{height:110px !important;min-height:110px !important;}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-submit{padding:12px 20px !important;}'
        . '}'
        // Short desktop viewports (small laptops): the 90px cupcake-anchor gap
        // wastes height the form needs — let the open panel sit nearly at the
        // viewport bottom (covering the cupcake, which the panel replaces while
        // open) and shorten the message box further.
        . '@media (min-width:601px) and (max-height:820px){'
            . '#custom-popup-wrapper #popup-form-container{bottom:0;padding:14px 20px !important;'
                . 'max-height:calc(100vh - 70px);}'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form p{margin:0 0 8px !important;}'
            . '#custom-popup-wrapper #popup-form-container textarea{height:84px !important;min-height:84px !important;}'
        . '}'
        // Very short desktop screens (1366x768-class laptops): drop the intro
        // helper sentence so all actual fields + submit fit without scrolling.
        . '@media (min-width:601px) and (max-height:700px){'
            . '#custom-popup-wrapper #popup-form-container .wpcf7-form > p:first-of-type{display:none;}'
        . '}'
        // While the popup is OPEN, lift the whole wrapper above the header —
        // the site CSS puts .left-mheader (hamburger) at z-index 99999, which
        // painted over the full-screen form on mobile. Closed, the wrapper
        // stays at its own 9999 so the menu drawer still covers the cupcake.
        . '#custom-popup-wrapper:has(#popup-form-container:not(.popup-hidden)){z-index:100001;}';
    // ----------------------------------------------------------------------

    // --- Design-system utility classes ------------------------------------
    // Reusable typographic hooks matching the example design.
    //   .gold   -> the brand gold (example "primary", rgb 193,156,92)
    //   .subtle -> the small, all-caps, wide-tracked label look of
    //              "The Sweetest Moment" (Montserrat 14px / .4em / uppercase)
    $inline_css .= ''
        . '.gold{color:#c19c5c !important;}'
        . '.subtle{font-family:\'Montserrat\',Arial,sans-serif !important;'
            . 'font-size:12px !important;font-weight:400 !important;letter-spacing:.4em !important;'
            . 'text-transform:uppercase !important;line-height:1.6;}';

    // Typographic coercion: bring this page's standard content type onto one
    // consistent system (body = Montserrat 15px; headings = Cormorant on a fixed
    // size scale). Skips `.subtle` eyebrows and `wca-*` component titles, which
    // are intentional designs.
    $serif = '\'Cormorant Garamond\',Georgia,serif';
    $inline_css .= ''
        // Body copy in standard Text Blocks -> Montserrat, one size.
        . '#sns_content .wpb_text_column,#sns_content .vc_column_text,'
            . '#sns_content .wpb_text_column p,#sns_content .vc_column_text p{'
            . 'font-family:\'Montserrat\',Arial,sans-serif;font-size:15px;line-height:1.7;}'
        // Content headings -> Cormorant, consistent weight + size scale.
        . '#sns_content h1:not(.subtle):not([class*="wca-"]),'
            . '#sns_content h2:not(.subtle):not([class*="wca-"]){'
            . 'font-family:' . $serif . ' !important;font-weight:500 !important;'
            . 'font-size:38px !important;line-height:1.15 !important;}'
        . '#sns_content h3:not(.subtle):not([class*="wca-"]){'
            . 'font-family:' . $serif . ' !important;font-weight:500 !important;'
            . 'font-size:24px !important;line-height:1.3 !important;}'
        . '#sns_content h4:not(.subtle):not([class*="wca-"]){'
            . 'font-family:' . $serif . ' !important;font-weight:500 !important;'
            . 'font-size:20px !important;line-height:1.35 !important;}';

    // --- "Signature Styles / Our Collections" section -> match the example ---
    // The header row is the one immediately before the .wca-cc cards row.
    $coll_hdr = '#sns_content .vc_row.wpb_row:has(+ .vc_row.wpb_row .wca-cc)';
    $inline_css .= ''
        // Section heading: large Cormorant, dark, centered.
        . $coll_hdr . ' h2.vc_custom_heading:not(.subtle),'
            . $coll_hdr . ' h3.vc_custom_heading:not(.subtle){'
            . 'font-family:' . $serif . ' !important;font-weight:400 !important;'
            . 'font-size:48px !important;line-height:1.1 !important;color:#1a1a1a !important;}'
        // Little gold divider under the heading (96x1px, centered).
        . $coll_hdr . ' h2.vc_custom_heading:not(.subtle)::after,'
            . $coll_hdr . ' h3.vc_custom_heading:not(.subtle)::after{'
            . 'content:"";display:block;width:96px;height:1px;background:#c19c5c;margin:1.25rem auto 0;}'
        // Eyebrow: gold, 14px to match the example.
        . $coll_hdr . ' .subtle{font-size:14px !important;color:#c19c5c !important;}'
        // Cards row: cap width so the three cakes match the example (~413 wide, 3:4).
        . '#sns_content .vc_row.wpb_row:has(.wca-cc){'
            . 'max-width:1280px !important;margin-left:auto !important;margin-right:auto !important;}';

    // --- Reusable max-width utility for WPBakery rows -----------------------
    // Add one of these to a row's "Extra class name" to cap + centre its width:
    //   pcc-maxw (1280px) · pcc-maxw--narrow (768px) · pcc-maxw--wide (1440px).
    // !important also overrides a "Stretch row" setting's inline margins.
    $inline_css .= ''
        . '#sns_content .vc_row.wpb_row.pcc-maxw,'
        . '#sns_content .vc_row.wpb_row.pcc-maxw--narrow,'
        . '#sns_content .vc_row.wpb_row.pcc-maxw--wide{margin-left:auto !important;margin-right:auto !important;}'
        . '#sns_content .vc_row.wpb_row.pcc-maxw{max-width:1280px !important;}'
        . '#sns_content .vc_row.wpb_row.pcc-maxw--narrow{max-width:768px !important;}'
        . '#sns_content .vc_row.wpb_row.pcc-maxw--wide{max-width:1440px !important;}';
    // ----------------------------------------------------------------------

    // --- "The Sweetest Moment / Let's Get Married" block (.focus) -> example -
    // Narrow, centered column; bigger heading + body; example-style italic
    // serif blockquote (no border). Two classes + !important beat the coercion.
    $inline_css .= ''
        . '#sns_content_rsm .wpb_text_column{margin-bottom:0;}'
        . '#sns_content .focus.wpb_text_column{max-width:1024px;margin-left:auto;margin-right:auto;text-align:center;}'
        . '#sns_content .focus.wpb_text_column h1{font-size:60px !important;font-weight:400 !important;line-height:1.05 !important;}'
        . '#sns_content .focus.wpb_text_column p{font-size:20px !important;color:#666 !important;line-height:1.55 !important;}'
        // Blockquote -> centered italic Cormorant, no rule/border (matches example).
        . '#sns_content .focus.wpb_text_column blockquote{border:0 !important;padding:16px 0 !important;margin:.5rem auto !important;}'
        . '#sns_content .focus.wpb_text_column blockquote,'
            . '#sns_content .focus.wpb_text_column blockquote p{'
            . 'font-family:' . $serif . ' !important;font-style:italic !important;font-size:24px !important;'
            . 'font-weight:400 !important;color:rgba(26,26,26,.8) !important;}';
    // ----------------------------------------------------------------------

    // --- Image carousel (.vc_images_carousel) -> match the example gallery --
    // Taller 4:5 cards (override the carousel's short landscape height), gaps,
    // a hover dark-overlay + underlined "View Detail", and a small gold caption.
    $inline_css .= ''
        . '.vc_images_carousel .vc_carousel-inner,.vc_images_carousel .vc_carousel-slideline,'
            . '.vc_images_carousel .vc_carousel-slideline-inner,.vc_images_carousel .vc_item,'
            . '.vc_images_carousel .vc_inner{height:auto !important;}'
        // ~8px inset from the page edge (a touch under the 10px border padding).
        // (The "show 1/8 of the 4th slide" peek lives in pcc-gallery.css — it just
        // narrows the slide flex-basis; the native scroll-snap track does the rest.)
        . '.vc_images_carousel{padding:0 8px;box-sizing:border-box;}'
        . '.vc_images_carousel .vc_item{position:relative;padding:0;box-sizing:border-box;}'
        . '.vc_images_carousel .vc_carousel-indicators{display:none !important;}'
        // Image sits fixed, inset 10px on left/right/bottom (flush at top). Strip
        // VC's own .vc_inner side margin so the frame can hug the item edge.
        . '.vc_images_carousel .vc_inner{position:relative;aspect-ratio:4/5;overflow:hidden;'
            . 'box-sizing:border-box;margin:0 !important;padding:0 10px 10px;}'
        . '.vc_images_carousel .vc_inner img{width:100% !important;height:100% !important;object-fit:cover;display:block;}'
        // 1px gold frame, open at top, drawn ~10px off the image. On hover ONLY the
        // frame slides inward onto the image edges (the image itself never moves);
        // top stays pinned to the image top so the sides never poke above it.
        . '.vc_images_carousel .vc_item::after{content:"";position:absolute;z-index:3;pointer-events:none;'
            . 'top:0;left:0;right:0;bottom:0;border:0 solid rgba(193,156,92,.45);border-width:0 1px 1px;'
            . 'transition:left .7s cubic-bezier(.4,0,.2,1),right .7s cubic-bezier(.4,0,.2,1),'
            . 'bottom .7s cubic-bezier(.4,0,.2,1);}'
        . '.vc_images_carousel .vc_item:hover::after{left:10px;right:10px;bottom:10px;}'
        // Hover: dark overlay (over the image) + underlined "View Detail".
        . '.vc_images_carousel .vc_inner::before{content:"";position:absolute;top:0;left:10px;right:10px;bottom:10px;background:rgba(0,0,0,.4);'
            . 'opacity:0;transition:opacity .5s cubic-bezier(.4,0,.2,1);z-index:1;pointer-events:none;}'
        . '.vc_images_carousel .vc_inner::after{content:"View Detail";position:absolute;top:50%;left:50%;'
            . 'transform:translate(-50%,-50%);z-index:2;color:#fff;font-family:\'Montserrat\',sans-serif;'
            . 'font-size:10px;letter-spacing:.4em;text-transform:uppercase;padding-bottom:8px;'
            . 'border-bottom:1px solid rgba(255,255,255,.5);opacity:0;transition:opacity .5s ease;'
            . 'pointer-events:none;white-space:nowrap;}'
        . '.vc_images_carousel .vc_item:hover .vc_inner::before,'
            . '.vc_images_carousel .vc_item:hover .vc_inner::after{opacity:1;}'
        // "Swipe to explore our gallery" caption -> small gold label with lead line.
        . '#sns_content .wpb_text_column.prepend p{font-size:10px !important;font-weight:600 !important;'
            . 'letter-spacing:.4em !important;color:#c19c5c !important;text-transform:uppercase;margin:0;'
            . 'text-indent:100px;}'
        . '#sns_content .wpb_text_column.prepend p::before{content:"";display:inline-block;width:32px;height:1px;'
            . 'background:#c19c5c;vertical-align:middle;margin-right:16px;}';
    // ----------------------------------------------------------------------

    // --- Cake-form styling for EVERY CF7 form built on .cake-form-grid ------
    // The venue form's look lives in a Custom CSS & JS entry ("form-style",
    // post 1863) scoped to .page-id-1122, so copies of that form on other
    // pages (e.g. the contact page) render unstyled. These are the same rules
    // keyed to the form's own layout class instead of a page id; values match
    // the venue kit, so the venue page is unaffected.
    $cform = '#sns_content .wpcf7 form:has(.cake-form-grid)';
    $inline_css .= ''
        . $cform . '{background:#fff;padding:35px;border-radius:12px;'
            . 'box-shadow:0 5px 30px rgba(0,0,0,.05);max-width:900px;margin:0 auto 50px;}'
        . $cform . ' label{display:block;font-weight:600;color:#333;font-size:14px;margin-bottom:5px;}'
        . $cform . ' input[type="text"],' . $cform . ' input[type="email"],'
            . $cform . ' input[type="tel"],' . $cform . ' input[type="date"],'
            . $cform . ' select,' . $cform . ' textarea{'
            . 'width:100%;height:auto !important;min-height:48px;padding:12px 15px;'
            . 'border:1px solid #d1d1d1;border-radius:6px;background:#fafafa;font-size:14px;'
            . 'color:#444;transition:all .3s ease;box-sizing:border-box;}'
        . $cform . ' input:focus,' . $cform . ' select:focus,' . $cform . ' textarea:focus{'
            . 'outline:none;border-color:#333;background:#fff;box-shadow:0 0 0 3px rgba(51,51,51,.1);}'
        . $cform . ' .wpcf7-submit{background-color:#333 !important;color:#fff !important;'
            . 'border:none !important;padding:16px 30px !important;font-size:16px !important;'
            . 'font-weight:bold !important;border-radius:8px !important;cursor:pointer !important;'
            . 'width:100%;text-transform:uppercase;margin-top:20px;transition:background-color .3s ease !important;}'
        . $cform . ' .wpcf7-submit:hover{background-color:#555 !important;}';
    // ----------------------------------------------------------------------

    return $inline_css;
}

/**
 * Header overlay colour: Customizer live-preview wiring.
 *
 * The header_overlay_scheme field has no Redux output/compiler, and Redux does
 * not expose in-progress option values to the front-end render, so neither a
 * refresh nor Redux's preview overlay repaints the overlaid header. Instead we
 * drive it via postMessage: force the setting's transport to postMessage and
 * load a tiny preview script that swaps the .scheme-light/.scheme-dark class on
 * #sns_header (both palettes are already in the page CSS).
 */
add_action( 'customize_register', 'snsvicky_header_overlay_customize_transport', 100 );
function snsvicky_header_overlay_customize_transport( $wp_customize ) {
    $setting = $wp_customize->get_setting( 'snsvicky_themeoptions[header_overlay_scheme]' );
    if ( $setting ) {
        $setting->transport = 'postMessage';
    }
}
add_action( 'customize_preview_init', 'snsvicky_header_overlay_customize_preview_js' );
function snsvicky_header_overlay_customize_preview_js() {
    wp_enqueue_script(
        'snsvicky-customizer-preview',
        SNSVICKY_THEME_URI . '/assets/js/customizer-preview.js',
        array( 'jquery', 'customize-preview' ),
        '1.0',
        true
    );
}

/**
 * Whether the header should float (transparent overlay) over this page's hero.
 *
 * Driven by the per-page "Overlay Header on Hero" option (Page Config):
 *   on  -> always overlay (pages whose hero is built into the content, e.g. the
 *          Occasion / Wedding landing pages that have no theme slideshow)
 *   off -> never overlay
 *   ''  -> default: overlay when the page renders the theme RevSlider slideshow
 *
 * Used for both the .head-overlay header class (tpl-head-4) and the page-builder
 * reveal gating, so "overlay pages" and "reveal pages" stay in sync.
 */
function snsvicky_header_is_overlay() {
    $mode = snsvicky_metabox( 'header_overlay' );
    if ( 'on' === $mode ) {
        return true;
    }
    if ( 'off' === $mode ) {
        return false;
    }
    return ( is_page() && snsvicky_metabox( 'useslideshow' ) == 1 );
}

/**
 * Enqueue the one-time scroll-into-view reveal script.
 *
 * Loaded site-wide so the wca-ss grid directional reveal works on every page
 * that uses the component. The broader page-builder row reveal is gated to hero
 * pages (their content sits below the fold, so nothing flashes) and toggled via
 * the localized `rows` flag. reveal.js no-ops for reduced-motion / missing
 * IntersectionObserver, leaving content visible.
 */
/**
 * Wire the mobile menu's close (X) button to dismiss the panel — mirrors the
 * hamburger's own close logic (sns-script.js) so state stays in sync.
 */
add_action( 'wp_enqueue_scripts', 'snsvicky_resmenu_close_js', 25 );
function snsvicky_resmenu_close_js() {
    $js = "jQuery(function($){"
        . "$(document).on('click','.sns-resmenu-close',function(){"
            . "$('#sns_content_rsm').removeClass('active');"
            . "$('body').removeClass('show-sidebar');"
            . "$('.sns-icon-nav .btn2.offcanvas .overlay').stop(true,true).fadeOut(200);"
        . "});"
    . "});";
    wp_add_inline_script( 'snsvicky-script', $js );
}

add_action( 'wp_enqueue_scripts', 'snsvicky_enqueue_reveal', 20 );
function snsvicky_enqueue_reveal() {
    wp_enqueue_script( 'snsvicky-reveal', SNSVICKY_THEME_URI . '/assets/js/reveal.js', array(), '1.2', true );
    wp_localize_script( 'snsvicky-reveal', 'snsvickyReveal', array(
        'rows' => snsvicky_header_is_overlay() ? 1 : 0,
    ) );
}

/**
 * Build a CSS declaration string from a Redux typography option value.
 *
 * Whitelists real CSS properties (so option metadata such as `google`,
 * `font-options`, `subsets` is never emitted), quotes multi-word family names
 * and appends a sensible fallback stack. When the option has no family, the
 * supplied default family is used.
 *
 * @param mixed  $font           The typography option array (or anything).
 * @param string $default_family Family to use when the option has none.
 * @param string $fallback       Fallback stack appended after the family.
 * @param bool   $important      Add !important to font-family (for headings).
 * @return string CSS declarations, e.g. "font-family:'Cormorant Garamond',serif;font-weight:600;".
 */
function snsvicky_typography_css( $font, $default_family = '', $fallback = 'sans-serif', $important = false ) {
    $allowed = array( 'font-weight', 'font-style', 'font-size', 'line-height', 'letter-spacing', 'text-transform', 'color' );
    $font    = is_array( $font ) ? $font : array();
    $family  = isset( $font['font-family'] ) ? trim( $font['font-family'] ) : '';
    if ( $family === '' ) {
        $family = $default_family;
    }

    $css = '';
    if ( $family !== '' ) {
        $quoted = ( strpos( $family, ' ' ) !== false && strpos( $family, ',' ) === false ) ? "'" . $family . "'" : $family;
        $css   .= 'font-family:' . $quoted . ', ' . $fallback . ( $important ? ' !important' : '' ) . ';';
    }
    foreach ( $font as $prop => $value ) {
        if ( ! in_array( $prop, $allowed, true ) ) {
            continue;
        }
        if ( $value === '' || $value === 'true' || $value === 'false' ) {
            continue;
        }
        $css .= $prop . ':' . $value . ';';
    }
    return $css;
}

/**
 * Enqueue Palermo component enhancements (swipeable gallery for WPBakery
 * image carousels that fail to initialise natively).
 */
function snsvicky_palermo_components() {
    $css = SNSVICKY_THEME_DIR . '/assets/css/pcc-gallery.css';
    $js  = SNSVICKY_THEME_DIR . '/assets/js/pcc-gallery.js';
    wp_enqueue_style( 'pcc-gallery', SNSVICKY_THEME_URI . '/assets/css/pcc-gallery.css', array( 'snsvicky-theme-style' ), file_exists( $css ) ? filemtime( $css ) : null );
    wp_enqueue_script( 'pcc-gallery', SNSVICKY_THEME_URI . '/assets/js/pcc-gallery.js', array(), file_exists( $js ) ? filemtime( $js ) : null, true );

    $feature_css = SNSVICKY_THEME_DIR . '/assets/css/pcc-feature.css';
    wp_enqueue_style( 'pcc-feature', SNSVICKY_THEME_URI . '/assets/css/pcc-feature.css', array( 'snsvicky-theme-style' ), file_exists( $feature_css ) ? filemtime( $feature_css ) : null );
}
add_action( 'wp_enqueue_scripts', 'snsvicky_palermo_components', 20 );

/**
 * Cache-bust every local stylesheet/script by versioning it with the file's
 * modification time. The theme enqueues most assets with no version (WP then
 * appends ?ver=<core version>, which never changes when a file is edited), so
 * browsers and the WP Engine CDN keep serving stale CSS/JS after deploys —
 * purging WP Engine can't reach visitors' browser caches. A changed file now
 * gets a changed URL, which every cache layer treats as a brand-new asset.
 * Core assets (/wp-includes/) are untouched; anything under /wp-content/ that
 * exists on disk gets its mtime as ?ver=.
 */
function pcc_asset_cache_bust( $src ) {
    $path = wp_parse_url( $src, PHP_URL_PATH );
    if ( ! $path ) return $src;
    $pos = strpos( $path, '/wp-content/' );
    if ( false === $pos ) return $src;
    $file = WP_CONTENT_DIR . substr( $path, $pos + strlen( '/wp-content' ) );
    if ( is_file( $file ) ) {
        $src = add_query_arg( 'ver', (string) filemtime( $file ), $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'pcc_asset_cache_bust', 9999 );
add_filter( 'script_loader_src', 'pcc_asset_cache_bust', 9999 );

// Palermo custom WPBakery elements.
require_once get_template_directory() . '/pcc-feature.php';

/**
 * Build just the CSS font-family value (quoted family + fallback stack) from a
 * typography option, for use in CSS custom properties. Falls back to the given
 * default family when the option has none.
 */
function snsvicky_font_family_value( $opt_key, $default_family, $fallback ) {
    global $snsvicky_opt;
    $v      = isset( $snsvicky_opt[ $opt_key ] ) && is_array( $snsvicky_opt[ $opt_key ] ) ? $snsvicky_opt[ $opt_key ] : array();
    $family = ! empty( $v['font-family'] ) ? trim( $v['font-family'] ) : $default_family;
    $quoted = ( strpos( $family, ' ' ) !== false && strpos( $family, ',' ) === false ) ? "'" . $family . "'" : $family;
    return $quoted . ', ' . $fallback;
}

/**
 * Enqueue the Google Fonts used by the body and heading typography options.
 *
 * Reads the families chosen in the theme options (falling back to Montserrat /
 * Cormorant Garamond), skips web-safe families that don't need loading, and
 * requests a single combined css2 stylesheet. This replaces the theme's
 * unreliable per-field font loading so admin font changes actually load.
 */
function snsvicky_palermo_google_fonts() {
    global $snsvicky_opt;

    $websafe = array( 'arial', 'helvetica', 'georgia', 'times new roman', 'times', 'verdana', 'tahoma', 'courier new', 'serif', 'sans-serif', 'monospace', 'inherit' );
    $weights = ':wght@300;400;500;600;700';
    $families = array();

    foreach ( array( 'body_font' => 'Montserrat', 'heading_font' => 'Cormorant Garamond' ) as $opt => $fallback_family ) {
        $val    = isset( $snsvicky_opt[ $opt ] ) && is_array( $snsvicky_opt[ $opt ] ) ? $snsvicky_opt[ $opt ] : array();
        $family = ! empty( $val['font-family'] ) ? trim( $val['font-family'] ) : $fallback_family;
        $google = ! isset( $val['google'] ) || $val['google'] !== '0';
        if ( $family === '' || ! $google || in_array( strtolower( $family ), $websafe, true ) ) {
            continue;
        }
        $families[ $family ] = str_replace( ' ', '+', $family ) . $weights;
    }

    if ( empty( $families ) ) {
        return;
    }
    $url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', array_values( $families ) ) . '&display=swap';
    wp_enqueue_style( 'snsvicky-google-fonts', $url, array(), null );
}
add_action( 'wp_enqueue_scripts', 'snsvicky_palermo_google_fonts', 5 );

/**
 * Preconnect to Google Fonts hosts for faster webfont loading.
 */
function snsvicky_palermo_font_preconnect( $hints, $relation ) {
    if ( 'preconnect' === $relation ) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'snsvicky_palermo_font_preconnect', 10, 2 );

/* 
 * Add tpl footer
 */
function snsvicky_tplfooter() {
    $output = '';
    ob_start();
    require SNSVICKY_THEME_DIR . '/tpl-footer.php';
    $output = ob_get_clean();
    echo $output;
}
add_action('wp_footer', 'snsvicky_tplfooter');
/* 
 * Custom js inline and js in admin panel theme
 */
function snsvicky_jsinline() {
    // write out custom code
    $output = '';
    ob_start();
    ?>
    if (typeof ajaxurl == 'undefined') {
        var ajaxurl = '<?php echo esc_js( admin_url('admin-ajax.php') ); ?>';
    }
    var sns_sp_var = [];
    sns_sp_var['poup'] = '<?php echo (snsvicky_themeoption('woo_usepopupimage', 1)) ? 1 : 0 ; ?>';
    sns_sp_var['zoom'] = '<?php echo (snsvicky_themeoption('woo_usecloudzoom', 1)) ? 1 : 0 ; ?>';
    sns_sp_var['zoomtype'] = '<?php echo snsvicky_getoption('woo_zoomtype', 'lens'); ?>';
    sns_sp_var['zoommobile'] = '<?php echo (snsvicky_themeoption('woo_usezoommobile', 0)) ? 1 : 0 ; ?>';
    sns_sp_var['thumbnum'] = '<?php echo snsvicky_themeoption('woo_thumb_num', 5) ; ?>';
    sns_sp_var['lenssize'] = '<?php echo snsvicky_themeoption('woo_lenssize', 200) ; ?>';
    sns_sp_var['lensshape'] = '<?php echo snsvicky_themeoption('woo_lensshape', 'round') ; ?>';
    <?php
    if(class_exists('WooCommerce')){
        global $product;
        $theID = get_the_id();
        $product = wc_get_product( $theID );
        if( is_product() && $product->get_type() === 'variable' && snsvicky_themeoption('woo_designvariations', 1) == 1 ){
            $attributes = $product->get_attributes(); ?>
            var sns_arr_attr = {};
            <?php foreach ( $attributes as $attribute ) :
                if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
                    continue;
                } else {}
                $terms = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'all' ) );
                $type = '';
                $key_val = array();
                $i = 0;
                foreach ($terms as $term) { $i++;
                    $type = snsvicky_get_term_byid( $term->term_id, 'snsvicky_product_attribute_type' );
                    $type = ($type == '') ? 'text' : $type;
                    switch ($type) {
                        case 'color':
                            if( snsvicky_getoption('use_variation_thumb', 1) == 1){
                                $available_variations = $product->get_available_variations();
                                foreach ($available_variations as $available_variation) {
                                    if($term->slug === $available_variation['attributes']["attribute_$term->taxonomy"]){
                                        $image_src = get_post_thumbnail_id( $available_variation['variation_id'] ); 
                                        $image_src = wp_get_attachment_image_src( $image_src, 'shop_thumbnail');
                                        $image_src = isset($image_src['0']) ? $image_src['0'] : '';
                                        $key_val[$term->slug] = $image_src;
                                    }
                                }
                            }else {
                                $key_val[$term->slug] = snsvicky_get_term_byid( $term->term_id, 'snsvicky_product_attribute_color' );
                            }
                            break;
                        default: // type is text
                            $key_val[$term->slug] = $term->name;
                            break;
                    }
                }
                ?>

                var attributeName = '<?php echo esc_attr($attribute['name']) ?>';
                var data_type = '<?php echo esc_attr($type); ?>';
                var key_val = {};
                <?php foreach ($key_val as $key => $value):?>
                    key_val['<?php echo esc_attr($key) ?>'] = '<?php echo esc_attr($value) ?>';
                <?php endforeach;?>
                sns_arr_attr['attribute_' + attributeName] = {'type': data_type, key_val};
            <?php endforeach;
        }
    }
    if( snsvicky_themeoption('tag_showmore', '1') == '1' ): ?>
    jQuery(document).ready(function(){
        if(jQuery('.widget_tag_cloud').length > 0){
            var $tag_display_first  = <?php echo absint( snsvicky_themeoption('tag_display_first', 8) ) - 1?>;
            var $number_tags        = jQuery('.widget_tag_cloud .tagcloud a').length;
            var $_this              = jQuery('.widget_tag_cloud .tagcloud');
            var $view_all_tags      = "<?php echo esc_html__('View all tags', 'snsvicky');?>";
            var $hide_all_tags      = "<?php echo esc_html__('Hide all tags', 'snsvicky');?>";
            
            if( $number_tags > $tag_display_first ){
                jQuery('.widget_tag_cloud .tagcloud a:gt('+$tag_display_first+')').addClass('is_visible').hide();
                jQuery($_this).append('<div class="view-more-tag"><a href="#" title="">'+$view_all_tags+'</a></div>');

                jQuery('.widget_tag_cloud .tagcloud .view-more-tag a').click(function(){
                    if(jQuery(this).hasClass('active')){
                        if( jQuery($_this).find('a').hasClass('is_hidden') ){
                            $_this.find('.is_hidden').removeClass('is_hidden').addClass('is_visible').stop().slideUp(600);
                        }
                        jQuery(this).removeClass('active');
                        jQuery(this).html($view_all_tags);
                        
                    }else{
                        if(jQuery($_this).find('a').hasClass('is_visible')){
                            $_this.find('.is_visible').removeClass('is_visible').addClass('is_hidden').stop().slideDown(600);
                        }
                        jQuery(this).addClass('active');
                        jQuery(this).html($hide_all_tags);
                    }
                    
                    return false;
                });
            }
        }
    });
    <?php
    endif;
    $output = ob_get_clean();
    return $output;
}
/** 
 *  Quick view for product list style
 **/
function snsvicky_quickview_liststyle(){
    if ( !class_exists('YITH_WCQV_Frontend') ) return;
    global $product;
    $product_id = 0;
    // get product id
    ! $product_id && $product_id = yit_get_prop( $product, 'id', true );
    $button = '<a href="#" class="button yith-wcqv-button" data-product_id="' . $product_id . '"></a>';
    $button = apply_filters( 'yith_add_quick_view_button_html', $button, '', $product );
    echo $button;
}
/** 
 *	Tile for page, post
 **/
function snsvicky_pagetitle(){
	// Disable title in page
	if( is_page() && function_exists('rwmb_meta') && rwmb_meta('snsvicky_showtitle') == '2' ) return;
	// Show title in page, single post
	if( is_single() || is_page() || ( is_home() && get_option( 'show_on_front' ) == 'page' ) ) : ?>
		<h1 class="page-header">
            <span><?php the_title(); ?></span>
        </h1>
        
    <?php 
    // Show title for category page
    elseif ( is_category() ) : ?>
        <h1 class="page-header">
            <span><?php single_cat_title(); ?></span>
        </h1>
    <?php
    // Author
    elseif ( is_author() ) : ?>
        <h1 class="page-header">
            <span>
        <?php
            printf( esc_html__( 'All posts by: %s', 'snsvicky' ), get_the_author() );
        ?>
            </span>
        </h1>
        <?php if ( get_the_author_meta( 'description' ) ) : ?>
        <header class="archive-header">
            <div class="author-description"><p><?php the_author_meta( 'description' ); ?></p></div>
        </header>
        <?php endif; ?>
    <?php 
    // Tag
    elseif ( is_tag() ) : ?>
        <h1 class="page-header">
            <span>
            <?php printf( esc_html__( 'Tag Archives: %s', 'snsvicky' ), single_tag_title( '', false ) ); ?>
            </span>
        </h1>
        <?php
        $term_description = term_description();
        if ( ! empty( $term_description ) ) : ?>
        <header class="archive-header">
            <?php printf( '<div class="taxonomy-description">%s</div>', $term_description ); ?>
        </header>
        <?php endif; ?>
    <?php 
    // Search
    elseif ( is_search() ) : ?>
    <h1 class="page-header"><span><?php printf( esc_html__( 'Search Results for: %s', 'snsvicky' ), get_search_query() ); ?></span></h1>
    <?php
    // Archive
    elseif ( is_archive() ) : ?>
        <?php the_archive_title( '<h2 class="page-header">', '</h2>' ); ?>
        <?php
        if( get_the_archive_description() ): ?>
        <header class="archive-header">
            <?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
        </header>
        <?php    
        endif;
        ?>
    <?php
    // Default
    else : ?>
        <h1 class="page-header">
            <span><?php the_title(); ?></span>
        </h1>
    <?php
	endif;
}

// Excerpt Function
if(!function_exists('snsvicky_excerpt')){
    function snsvicky_excerpt($limit, $afterlimit='[...]') {
        $limit = ($limit) ? $limit : 55 ;
        $excerpt = get_the_excerpt();
        if( $excerpt != '' ){
           $excerpt = explode(' ', strip_tags( $excerpt ), intval($limit));
        }else{
            $excerpt = explode(' ', strip_tags(get_the_content( )), intval($limit));
        }
        if ( count($excerpt) >= $limit ) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt).' '.$afterlimit;
        } else {
            $excerpt = implode(" ",$excerpt);
        }
        $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
        return strip_shortcodes( $excerpt );
    }
}

/*
 * Ajax page navigation
 */
function snsvicky_ajax_load_next_page(){
	// Get current layout
	global $snsvicky_blog_layout;
	$snsvicky_blog_layout = isset($_POST['snsvicky_blog_layout']) ? esc_html($_POST['snsvicky_blog_layout']) : '';
	if( $snsvicky_blog_layout == '' ) $snsvicky_blog_layout = snsvicky_getoption('blog_type');
	
	// Get current page
	$page = $_POST['page'];
	
	// Number of published sticky posts
	$sticky_posts = snsvicky_get_sticky_posts_count();
	
	// Current query vars
	$vars = $_POST['vars'];
	
	// Convert string value into corresponding data types
	foreach ($vars as $key => $value){
		if( is_numeric($value) ) $vars[$key] = intval($value);
		if( $value == 'false' ) $vars[$key] = false;
		if( $value == 'true' ) $vars[$key] = true;
	}
	
	// Item template file 
	$template = $_POST['template'];
	
	// Return next page
	$page = intval($page) + 1;
	
	$posts_per_page = get_option('posts_per_page');
    if( $page == 2 && $vars['posts_per_page'] ){
        $offset = $vars['posts_per_page'];
    }else{
        $offset = $vars['posts_per_page'] + ($page - 2) * $posts_per_page;
    }
	
	// Get more posts per page than necessary to detect if there are more posts
	$args = array('post_status'=>'publish', 'posts_per_page'=>$posts_per_page + 1, 'offset'=>$offset);
	$args = array_merge($vars, $args);
	
	// Remove unnecessary variables
	unset($args['paged']);
	unset($args['p']);
	unset($args['page']);
	unset($args['pagename']); // This is necessary in case Posts Page is set to static page
	
	$query = new WP_Query($args);
	$idx = 0;
	if( $query->have_posts() ){
		while ( $query->have_posts() ){
			$query->the_post();
			$idx = $idx + 1;
			if( $idx < $posts_per_page + 1 )
				get_template_part($template, get_post_format());
		}
		if( $query->post_count <= $posts_per_page ){
			// There are no more posts
			// Print a flag to detect
			echo '<div id="sns-load-more-no-posts" class="no-posts"><!-- --></div>';
		}
	}else{
		// No posts found
	}
	/* Restore original Post Data*/
	wp_reset_postdata();
	
	die('');
}
// When the request action is "load_more", the snsvicky_ajax_load_next_page() will be called
add_action('wp_ajax_load_more', 'snsvicky_ajax_load_next_page');
add_action('wp_ajax_nopriv_load_more', 'snsvicky_ajax_load_next_page');

// Word Limiter
function snsvicky_limitwords($string, $word_limit) {
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $word_limit));
}
//
if(!function_exists('snsvicky_sharebox')){
    function snsvicky_sharebox( $layout='',$args=array() ){
        global $post;
        $default = array(
            'position' => 'top',
            'animation' => 'true'
            );
        $args = wp_parse_args( (array) $args, $default );
        ?>
    <div class="post-share-block">
        <span class="label-shareblock">
        <?php echo esc_html__('Share: ', 'snsvicky'); ?>
        </span>
        <div class="sns-share-box">
            <ul class="socials">
                <?php if(snsvicky_getoption('show_facebook_sharebox')==1): ?>
                <li class="facebook">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr( $args['position'] ); ?>" data-animation="<?php echo esc_attr($args['animation'] ); ?>"  data-original-title="Facebook" href="http://www.facebook.com/sharer.php?s=100&p&#91;url&#93;=<?php the_permalink(); ?>&p&#91;title&#93;=<?php the_title(); ?>" target="_blank">
                        <i class="fa fa-facebook"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_twitter_sharebox')==1): ?>
                <li class="twitter">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="Twitter" href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" target="_blank">
                        <i class="fa fa-twitter"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_linkedin_sharebox')==1): ?>
                <li class="linkedin">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="LinkedIn" href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" target="_blank">
                        <i class="fa fa-linkedin"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_tumblr_sharebox')==1): ?>
                <li class="tumblr">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="Tumblr" href="http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink()); ?>&amp;name=<?php echo urlencode($post->post_title); ?>&amp;description=<?php echo urlencode(get_the_excerpt()); ?>" target="_blank">
                        <i class="fa fa-tumblr"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_gplus_sharebox')==1): ?>
                <li class="google">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="Google +1" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" onclick="javascript:window.open(this.href,
            '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" target="_blank">
                        <i class="fa fa-google-plus"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_pinterest_sharebox')==1): ?>
                <li class="pinterest">
                    <?php $full_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="Pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&amp;description=<?php echo urlencode($post->post_title); ?>&amp;media=<?php echo urlencode($full_image[0]); ?>" target="_blank">
                        <i class="fa fa-pinterest"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(snsvicky_getoption('show_email_sharebox')==1): ?>
                <li class="email">
                    <a data-toggle="tooltip" data-placement="<?php echo esc_attr($args['position']); ?>" data-animation="<?php echo esc_attr($args['animation']); ?>"  data-original-title="Email" href="mailto:?subject=<?php the_title(); ?>&amp;body=<?php the_permalink(); ?>">
                        <i class="fa fa-envelope"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
        <?php
    }
}
//
if(!function_exists('snsvicky_relatedpost')){
    function snsvicky_relatedpost(){
        global $post;
        if($post){
        	$post_id = $post->ID;
        }else{
        	// Return if cannot find any post
        }
        
        $relate_count = snsvicky_themeoption('related_num');
        $get_related_post_by = snsvicky_themeoption('related_posts_by');

        $args = array(
            'post_status' => 'publish',
            'posts_per_page' => $relate_count,
            'orderby' => 'date',
            'ignore_sticky_posts' => 1,
            'post__not_in' => array ($post_id)
        );
        
        if($get_related_post_by == 'cat'){
        	$categories = wp_get_post_categories($post_id);
        	$args['category__in'] = $categories;
        }else{
        	$posttags = wp_get_post_tags($post_id);
        	
        	$array_tags = array();
        	if($posttags){
        		foreach ($posttags as $tag){
        			$tags = $tag->term_id;
        			array_push($array_tags, $tags);
        		}
        	}
        	$args['tag__in'] = $array_tags;
        }
        
        $relates = new WP_Query( $args );
        $template_name = '/framework/tpl/posts/related_post.php';
        if(is_file(SNSVICKY_THEME_DIR.$template_name)) {
            include(SNSVICKY_THEME_DIR.$template_name);
        }
        wp_reset_postdata();
    }
}

/*
 * Function to display number of posts.
 */
function snsvicky_get_post_views($post_id){
	$count_key = 'post_views_count';
	$count = get_post_meta($post_id, $count_key, true);
	if($count == ''){
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
		return esc_html__('0 view', 'snsvicky');
	}
	return $count. esc_html__(' View', 'snsvicky');
}

/*
 * Function to count views.
 */
function snsvicky_set_post_views($post_id){
	$count_key = 'post_views_count';
	$count = get_post_meta($post_id, $count_key, true);
	if($count == ''){
		$count = 0;
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
	}else{
		$count++;
		update_post_meta($post_id, $count_key, $count);
	}
}

function snsvicky_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
    <?php $add_below = ''; ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
        <div class="comment-body">
        	<div class="comment-user-meta">
        		<?php echo get_avatar($comment, 100); ?>
    			<h4 class="comment-user"><?php echo get_comment_author_link(); ?></h4>
                 <?php if ($comment->comment_type != 'pingback'): ?>
                <div class="comment-content">
                    <?php if ($comment->comment_approved == '0') : ?>
                    <p>
                        <em><?php echo esc_html__('Your comment is awaiting moderation.', 'snsvicky') ?></em><br />
                    </p>
                    <?php endif; ?>
                     <?php comment_text() ?>
                </div>
                <?php endif; ?>
                <div class="comment-meta">
                    <div class="date"><?php printf(esc_html__('%1$s at %2$s', 'snsvicky'), get_comment_date(),  get_comment_time()) ?></div>
                    <?php comment_reply_link(array_merge( $args, array('reply_text' => esc_html__('Reply', 'snsvicky'), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'])))?>
                    <?php edit_comment_link(esc_html__('Edit', 'snsvicky'),'  ','') ?>
                </div>
        	</div>
        </div>
  <?php 
}
/** 
 *	Breadcrumbs
 **/
function snsvicky_getbreadcrumbs(){
    $showbreadcrumbs = snsvicky_themeoption('showbreadcrump', 1);
    if ( get_post_type( get_the_ID() ) == 'page' ) :
        if ( is_front_page() || ( snsvicky_metabox('showbreadcrump', '2') == '2' ) ) :
            $showbreadcrumbs = 0;
        endif;
    // elseif ( get_post_type( get_the_ID() ) == 'post') :
    //         $showbreadcrumbs = 1;
    endif;
    if ( $showbreadcrumbs == 1 && !is_front_page()  && !is_404()) : ?>  
    <div id="sns_breadcrumbs" class="wrap">
        <div class="container">
            <?php 
            $template_name = '/tpl-breadcrumb.php';
            if(is_file(SNSVICKY_THEME_DIR.$template_name)) {
                include(SNSVICKY_THEME_DIR.$template_name);
            }
            ?>
        </div>
    </div>
    <?php endif;
}

/** 
 *  Search Ajax From
 **/
if( !function_exists('snsvicky_get_searchform') ){
    function snsvicky_get_searchform($search_box_type = 'def'){
        $exists_woo = (class_exists('WooCommerce'))?true:false;
        if( $exists_woo ){
            $taxonomy = 'product_cat';
            $post_type = 'product';
            $placeholder_text = esc_html__('Search for product', 'snsvicky');
        }else{
            $taxonomy = 'category';
            $post_type = 'post';
            $placeholder_text = esc_html__('Enter your keywords', 'snsvicky');
        }
        $options = '<option value="">'.esc_html__('All categories', 'snsvicky').'</option>';
        $options .= snsvicky_get_searchform_option($taxonomy, 0, 0);
        $uq = rand().time();
        $form = '<div class="sns-searchwrap" data-useajaxsearch="true" data-usecat-ajaxsearch="true">';
        $form .= '<div class="sns-ajaxsearchbox">
        <form method="get" id="search_form_' . $uq . '" action="' . esc_url( home_url( '/'  ) ) . '">';
        if( $search_box_type != 'hide_cat' ){
            $form .= '<select class="select-cat" name="cat">' . $options . '</select>';
        }
        $form .= '
        <div class="search-input">
            <input type="text" value="' . get_search_query() . '" name="s" id="s_' . $uq . '" placeholder="' . $placeholder_text . '" autocomplete="off" />
            <button type="submit">
                '. esc_html__('Search', 'snsvicky') .'
            </button>
            <input type="hidden" name="post_type" value="' . $post_type . '" />
            <input type="hidden" name="taxonomy" value="' . $taxonomy . '" />
         </div>
        </form></div></div>';
        echo $form;
    }
}

if( !function_exists('snsvicky_get_searchform_option') ){
    function snsvicky_get_searchform_option($taxonomy = 'product_cat', $parent = 0, $level = 0){
        $options = '';
        $spacing = '';
        for( $i = 0; $i < $level * 3 ; $i++ ){
            $spacing .= '&nbsp;';
        }
        $args = array(
            'number'        => '',
            'hide_empty'   => 1,
            'orderby'      =>'name',
            'order'        =>'asc',
            'parent'       => $parent
        );
        $select = '';
        $categories = get_terms($taxonomy, $args);
        if( is_search() &&  isset($_GET['cat']) && $_GET['cat'] != '' ){
            $select = $_GET['cat'];
        }
        $level++;
        $selected = '';
        if( is_array($categories) ){
            foreach( $categories as $cat ){
                if ($select == $cat->slug) $selected = ' selected';
                else  $selected = '';
                $options .= '<option value="' . $cat->slug . '"'.$selected.'>' . $spacing . $cat->name . '</option>';
                $options .= snsvicky_get_searchform_option($taxonomy, $cat->term_id, $level);
            }
        }
        return $options;
    }
}

/** 
 *  Search by Title only From
 **/
function snsvicky_search_by_title_only( $search, $wp_query )  {
    global $wpdb;  
    if ( empty( $search ) )  
        return $search; // skip processing - no search term in query  
    $q = $wp_query->query_vars;  
    $n = ! empty( $q['exact'] ) ? '' : '%';  
    $search =  '';
    $searchand = '';  
    foreach ( (array) $q['search_terms'] as $term ) {  
        $term = esc_sql( $wpdb->esc_like( $term ) );
        $like = $n . $term . $n;
        $search .= $wpdb->prepare( "{$searchand}($wpdb->posts.post_title LIKE %s)", $like );
        $searchand = ' AND ';  
    }  
    if ( ! empty( $search ) ) {  
        $search = " AND ({$search}) ";  
        if ( ! is_user_logged_in() )  
            $search .= " AND ($wpdb->posts.post_password = '') ";  
    } 
    return $search;  
}  
if ( snsvicky_themeoption('search_title_only') == true ) add_filter( 'posts_search', 'snsvicky_search_by_title_only', 10, 2 );

/**
 * Ajax search action
 **/
add_action( 'wp_ajax_snsvicky_ajax_search', 'snsvicky_ajax_search' );
add_action( 'wp_ajax_nopriv_snsvicky_ajax_search', 'snsvicky_ajax_search' );
if( !function_exists('snsvicky_ajax_search') ){
    function snsvicky_ajax_search(){
        global $post;
        $exists_woo = (class_exists('WooCommerce'))?true:false;
        if( $exists_woo ){
            $taxonomy = 'product_cat';
            $post_type = 'product';
        }else{
            $taxonomy = 'category';
            $post_type = 'post';
        }
        if ( snsvicky_getoption('enable_search_cat') == true ) $num_result = 6;
        else $num_result = 4;
        $num_result = -1;
        $keywords = $_POST['keywords'];
        $category = isset($_POST['category'])? $_POST['category']: '';
        $args = array(
            'post_type'        => $post_type,
            'post_status'      => 'publish',
            's'                => $keywords,
            'orderby'      =>'date',
            'order'        =>'desc',
            'posts_per_page'   => $num_result
        );

        if( $category != '' ){
            $args['tax_query'] = array(
                array(
                    'taxonomy'  => $taxonomy,
                    'terms'     => $category,
                    'field'     => 'slug'
                )
            );
        } 
        $results = new WP_Query($args);
        if( $results->have_posts() ){
            $extra_class = '';
            if( isset($results->post_count, $results->found_posts) && $results->found_posts > $results->post_count ){
                $extra_class = 'allcat-result';
            }
            $html = '<ul class="'.$extra_class.'">';
            while( $results->have_posts() ){
                $results->the_post();
                $link = get_permalink($post->ID);
                $image = '';
                if( $post_type == 'product' ){
                    $product = wc_get_product($post->ID);
                    $image = $product->get_image();
                }
                else if( has_post_thumbnail($post->ID) ){
                    $image = get_the_post_thumbnail($post->ID, 'thumbnail');
                }
                $html .= '<li>';
                    if( $image ){
                        $html .= '<div class="thumbnail">';
                            $html .= '<a href="'.esc_url($link).'">'. $image .'</a>';
                        $html .= '</div>';
                    }
                    $html .= '<div class="meta">';
                        $html .= '<a href="'.esc_url($link).'" class="title">'. snsvicky_ajaxsearch_highlight_key($post->post_title, $keywords) .'</a>';
                        if( $post_type == 'product' ){
                            if( $price_html = $product->get_price_html() ){
                                $html .= '<span class="price">'. $price_html .'</span>';
                            }
                        }
                    $html .= '</div>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            if( isset($results->post_count, $results->found_posts) && $results->found_posts > $results->post_count ){
                $viewall_text = sprintf( esc_html__('View all %d results', 'snsvicky'), $results->found_posts );
                $html .= '<div class="viewall-result">';
                    $html .= '<a href="#">'. $viewall_text .'</a>';
                $html .= '</div>';
            }
            wp_reset_postdata();
            
            $return = array();
            $return['html'] = $html;
            $return['keywords'] = $keywords;
            die( json_encode($return) );
        }else{
            wp_reset_postdata();
            $return = array();
            if( $exists_woo ){
                $return['html'] = esc_html__('No products were found matching your selection', 'snsvicky');
            }else{
                $return['html'] = esc_html__('No post were found matching your selection', 'snsvicky');
            }  
            $return['keywords'] = $keywords;
            die( json_encode($return) );
        }
    }
}
/**
 *  Highlight search key
 **/
if( !function_exists('snsvicky_ajaxsearch_highlight_key') ){
    function snsvicky_ajaxsearch_highlight_key($string, $keywords){
        $hl_string = '';
        $position_left = stripos($string, $keywords);
        if( $position_left !== false ){
            $position_right = $position_left + strlen($keywords);
            $hl_string_rightsection = substr($string, $position_right);
            $highlight = substr($string, $position_left, strlen($keywords));
            $hl_string_leftsection = stristr($string, $keywords, true);
            $hl_string = $hl_string_leftsection . '<span class="hightlight">' . $highlight . '</span>' . $hl_string_rightsection;
        } else{
            $hl_string = $string;
        }
        return $hl_string;
    }
}

/**
 *  Match with default search
 **/
add_filter('woocommerce_get_catalog_ordering_args', 'snsvicky_woo_get_catalog_ordering_args');
if( !function_exists('snsvicky_woo_get_catalog_ordering_args') ){
    function snsvicky_woo_get_catalog_ordering_args( $args ){
        if( class_exists('WooCommerce') && is_search() && !isset($_GET['orderby']) && get_option( 'woocommerce_default_catalog_orderby' ) == 'menu_order' 
            && 1==1 ){
            $args['orderby'] = '';
            $args['order'] = '';
        }
        return $args;
    }
}
/**
 * Main menu wrap
 **/
function snsvicky_main_menu_wrap(){
    $mainnav_class = ' col-md-12 col-xs-12';
    ?>
    <div id="sns_menu" class="menu-header">
        <div class="container">
            <div class="row">
                <div class="sns-mainnav-wrapper<?php echo esc_attr($mainnav_class);?>">
                    <div id="sns_mainnav">
                        <div id="sns_mainmenu" class="visible-lg visible-md">
                            <?php
                            if(has_nav_menu('main_navigation')):
                               wp_nav_menu( array(
                                            'theme_location' => 'main_navigation',
                                            'container' => false, 
                                            'menu_id' => 'main_navigation',
                                            'walker' => new snsvicky_Megamenu_Front,
                                            'menu_class' => 'nav navbar-nav'
                                ) ); 
                            else:
                                echo '<p class="main_navigation_alert">'.esc_html__('Please sellect menu for Main navigation', 'snsvicky').'</p>';
                            endif;
                            ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
/**
 * Promo Bar
 **/
function snsvicky_promobar($slug){
    $class = '';
    $wcode = new WP_Query(array( 'name' => $slug, 'post_type' => 'post-wcode' ));
    if ( $wcode->have_posts() && snsvicky_themeoption('promo_status', false) == true ) {
        $class = ' active';
    } ?>
    <div class="sns-promobar<?php echo $class; ?>">
        <div class="container">
            <?php
            if ( $wcode->have_posts() ) { ?>
            <div class="content"><?php echo do_shortcode('[snsvicky_postwcode name="' . $slug . '"]'); ?></div>
            <div class="btn-tongle"></div>
            <?php
            } ?>
         </div>
    </div>
    <?php
    wp_reset_postdata(); 
}
/**
 * Slideshow wrap
 **/
function snsvicky_slideshow_wrap(){
    if ( is_page() && snsvicky_metabox('useslideshow') == 1 ): ?>
    <div id="sns_slideshow" class="wrap">
        <?php echo do_shortcode('[rev_slider '.esc_attr(snsvicky_metabox('revolutionslider')).' ]'); ?>
    </div>
    <?php
    endif;
}
/** 
 * Sample data 
 **/
add_action( 'admin_enqueue_scripts', 'snsvicky_importlib' );
function snsvicky_importlib(){
    wp_enqueue_script('sampledata', SNSVICKY_THEME_URI . '/framework/sample-data/assets/script.js', array('jquery'), '', true);
    wp_enqueue_style('sampledata-css', SNSVICKY_THEME_URI . '/framework/sample-data/assets/style.css');
}
add_action( 'wp_ajax_sampledata', 'snsvicky_importsampledata' );
function snsvicky_importsampledata(){
    include_once(SNSVICKY_THEME_DIR . '/framework/sample-data/sns-importdata.php');
    snsvicky_importdata();
}
/**
 * Product content trailer.
 */
function nt_single_product_after_content() {
	echo '<div class="single-product-after-content">', do_shortcode('[snsvicky_postwcode name="single-product-after-content"]'), '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'nt_single_product_after_content', 5 );


/**
 * FIX: Disable WooCommerce Single Result Redirect
 * Ensures that searching for a single product stays on the grid/archive page.
 */
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

/**
 * OPTIONAL: Security Redirect
 * If any single product page is accessed directly, redirect to the gallery.
 */
add_action( 'template_redirect', 'dari_developer_disable_product_pages' );
function dari_developer_disable_product_pages() {
    if ( is_product() ) {
        wp_redirect( get_post_type_archive_link( 'product' ) );
        exit;
    }
}


// =============== This section is for favorite button ============


// =============== START This section is for optimize gallery images ============

add_action('template_redirect', function () {
    if (
        !is_shop() &&
        !is_product_category() &&
        !is_product_tag() &&
        !is_page('cake-gallery')
    ) {
        return;
    }

    ob_start(function ($buffer) {
        if (empty($buffer)) {
            return $buffer;
        }

        // Remove the rogue `stripSuffix` script that's inlined by the
        // "Simple Custom CSS and JS" plugin. Even with our sentinel query
        // string defeating its regex, its `img.src = img.src` reassignment
        // bypasses native loading="lazy" and forces re-fetches every 450ms.
        // It also runs a MutationObserver on the entire documentElement,
        // pegging the iOS Safari main thread. Strip it at the HTML level so
        // it never reaches the browser.
        // Match by the rogue script's actual function declaration, NOT by
        // a substring like "stripSuffix" — our own gallery script comments
        // mention that name and would otherwise be removed too.
        // The script body is matched with an unrolled loop ([^<]* chunks)
        // instead of a lazy [\s\S]*? — the lazy form counts every stepped
        // character against pcre.backtrack_limit (1M), so any inline script
        // over ~1MB (e.g. yith_wcwl_l10n on the full shop loop) makes preg
        // return null and the whole page go blank.
        $stripped = preg_replace_callback(
            '/<script\b[^>]*>[^<]*(?:<(?!\/script\b)[^<]*)*<\/script>/i',
            function ($s) {
                if (strpos($s[0], 'const stripSuffix') !== false
                    || strpos($s[0], 'const updateImagesAndHide') !== false) {
                    return '<!-- rogue stripSuffix script removed -->';
                }
                return $s[0];
            },
            $buffer
        );
        if ($stripped !== null) {
            $buffer = $stripped;
        }

        // A "Simple Custom CSS and JS" entry in WP admin runs every 450ms
        // and strips `-\d+x\d+` from any img src/data-original whose URL
        // ENDS in .jpg/.png/.webp. Appending this sentinel query string
        // breaks the regex's end-of-string anchor — the rogue script becomes
        // a no-op on our gallery images, while the image still fetches the
        // same file (WordPress ignores unknown query params).
        // Gallery images load at full resolution (no -300x300 dimension suffix)
        // to match the quality of the lightbox view.
        $thumb_sentinel = '?t=300';

        // Match <img> with "shop_catalog" in class AND data-original
        // pointing to /uploads/. Handles attributes in any order.
        $rewritten = preg_replace_callback(
            '/<img\s(?=[^>]*class="[^"]*shop_catalog[^"]*")[^>]*data-original="([^"]*?\/uploads\/[^"]*?)\.(jpg|jpeg|png|webp)(?:\?[^"]*)?"[^>]*>/i',
            function ($m) use ($thumb_sentinel) {
                $path      = $m[1];
                $ext       = $m[2];
                $full_tag  = $m[0];

                // Strip any WordPress dimension suffix (e.g. -300x300, -150x150)
                // to get the base (full-resolution) path.
                $base_path = preg_replace('/-\d+x\d+$/', '', $path);
                $full_url  = $base_path . '.' . $ext;

                // 1) data-original → full-res URL (with sentinel)
                //    data-lightbox-src → full-res URL (lightbox click target,
                //    leave untouched by rogue regex by using ?l=1).
                $full_tag = preg_replace(
                    '/\sdata-original="[^"]*"/i',
                    ' data-original="' . $full_url . $thumb_sentinel . '"',
                    $full_tag,
                    1
                );
                if (strpos($full_tag, 'data-lightbox-src=') === false) {
                    $full_tag = preg_replace(
                        '/<img\s/i',
                        '<img data-lightbox-src="' . $full_url . '?l=1" ',
                        $full_tag,
                        1
                    );
                } else {
                    $full_tag = preg_replace(
                        '/\sdata-lightbox-src="[^"]*"/i',
                        ' data-lightbox-src="' . $full_url . '?l=1"',
                        $full_tag,
                        1
                    );
                }

                // 2) Rewrite src to the full-res URL with sentinel, regardless
                //    of whether it was the spinner GIF or already an upload.
                //    Sentinel defeats the rogue stripSuffix loop.
                $full_tag = preg_replace(
                    '/\ssrc="[^"]*"/i',
                    ' src="' . $full_url . $thumb_sentinel . '"',
                    $full_tag,
                    1
                );

                // 3) Add native lazy-loading + async decode, and remove the
                //    `lazy` class so jquery.lazyload (a scroll-event lib)
                //    doesn't track these images. Together these eliminate
                //    the per-scroll iteration over hundreds of <img>s that
                //    pegged the iOS main thread.
                if (strpos($full_tag, ' loading=') === false) {
                    $full_tag = preg_replace('/<img\s/i', '<img loading="lazy" decoding="async" ', $full_tag, 1);
                }
                $full_tag = preg_replace(
                    '/class="([^"]*?)\blazy\b\s*([^"]*)"/i',
                    'class="$1$2"',
                    $full_tag
                );

                // Drop any srcset — we control the exact URL and don't want
                // the browser picking a different responsive variant.
                $full_tag = preg_replace('/\ssrcset="[^"]*"/i', '', $full_tag);

                return $full_tag;
            },
            $buffer
        );
        if ($rewritten !== null) {
            $buffer = $rewritten;
        }

        return $buffer;
    });
});


// =============== END section is for optimize gallery images ============


// =============== START: stop iOS Safari spinner / tab-kill on cake-gallery ===
// The browser's `load` event never fires because of slow third-party
// subresources (WooCommerce cart-fragments XHR, Google reCAPTCHA,
// WordPress emoji SVGs). Dequeue / strip them on shop archive pages.

// /cake-gallery/ is actually the WC shop archive, not a WP page —
// is_page() alone returns false on it. Match the same conditions the
// existing image-rewrite buffer uses.
function _palermo_is_gallery_view() {
    return is_shop() || is_product_category() || is_product_tag() || is_page('cake-gallery');
}

// Debug marker so we can verify this block actually runs.
add_action('wp_head', function () {
    if (_palermo_is_gallery_view()) {
        echo "\n<!-- ios-fix-v2 active -->\n";
    }
}, 1);

add_action('wp_enqueue_scripts', function () {
    if (!_palermo_is_gallery_view()) return;

    // WooCommerce mini-cart fragments XHR — useless on a gallery page.
    wp_dequeue_script('wc-cart-fragments');
    wp_dequeue_script('wc-add-to-cart');

    // WordPress emoji SVG preloads — stall under Cloudflare, block load.
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}, 100);

// Strip the hard-coded reCAPTCHA <script>. It's tied to a marketing form
// not shown on the gallery but still loads, blocking the load event.
add_action('template_redirect', function () {
    if (!_palermo_is_gallery_view()) return;
    ob_start(function ($buffer) {
        if (empty($buffer)) return $buffer;
        $stripped = preg_replace(
            '/<script[^>]*src="[^"]*google\.com\/recaptcha\/api\.js[^"]*"[^>]*><\/script>/i',
            '',
            $buffer
        );
        return $stripped !== null ? $stripped : $buffer;
    });
}, 1);
// =============== END: iOS Safari spinner fix ============
//
//================ Section start for favorite cake =======================
//
// Force-enqueue WooCommerce's prettyPhoto on the favorites page.
// WC only enqueues it on shop/product views; the favorites page is a
// regular WP page with a shortcode, so without this the lightbox library
// is missing and image-lightbox.js can't bind.
add_action('wp_enqueue_scripts', function () {
    if (!is_page('favorite-cakes') && !is_page(12)) return;
    $wc_url = plugins_url('', WC_PLUGIN_FILE);
    wp_enqueue_script(
        'prettyPhoto',
        $wc_url . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js',
        array('jquery'),
        '3.1.6',
        true
    );
    wp_enqueue_style(
        'woocommerce_prettyPhoto_css',
        $wc_url . '/assets/css/prettyPhoto.css',
        array(),
        '3.1.6'
    );
}, 20);

// ==========================================
// Cake Gallery Auth Buttons
// ==========================================
add_action('woocommerce_before_shop_loop', function() {
    if (!_palermo_is_gallery_view() || is_user_logged_in()) {
        return;
    }
    ?>
    <div class="gallery-auth-buttons">
        <a href="/favorite-cakes" class="gallery-auth-btn">♥ Favorite Cakes</a>
        <a href="/login" class="gallery-auth-btn">Login</a>
        <a href="/register" class="gallery-auth-btn signup">Sign Up</a>
    </div>
    <?php
});

// ==========================================
// 1. PASS SECURE DATA TO JAVASCRIPT
// ==========================================
add_action('wp_head', 'inject_cake_favs_data');
function inject_cake_favs_data() {
    ?>
    <script type="text/javascript">
        var cakeFavsData = {
            ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
            nonce: '<?php echo wp_create_nonce('cake_fav_nonce'); ?>',
            isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
            loginUrl: '<?php echo esc_url(wp_login_url(get_permalink())); ?>'
        };
    </script>
    <?php
}

// ==========================================
// 2. SAVE FAVORITES TO DATABASE (AJAX)
// ==========================================
add_action('wp_ajax_save_user_favorites', 'ajax_save_user_favorites');
function ajax_save_user_favorites() {
    check_ajax_referer('cake_fav_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('User not logged in');

    $favs = isset($_POST['favs']) ? sanitize_text_field(wp_unslash($_POST['favs'])) : '';
    $fav_ids = array_filter(array_unique(explode(',', preg_replace('/[^0-9,]/', '', $favs))));
    $clean_favs = implode(',', $fav_ids);
    
    update_user_meta(get_current_user_id(), 'my_cake_favorites', $clean_favs);
    wp_send_json_success('Saved');
}

// ==========================================
// 3. GET FAVORITES ON LOGIN (AJAX)
// ==========================================
add_action('wp_ajax_get_user_favorites', 'ajax_get_user_favorites');
function ajax_get_user_favorites() {
    check_ajax_referer('cake_fav_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('User not logged in');

    $favs_str = get_user_meta(get_current_user_id(), 'my_cake_favorites', true);
    $fav_ids = array_filter(array_unique(explode(',', preg_replace('/[^0-9,]/', '', $favs_str))));

    if (empty($fav_ids)) {
        wp_send_json_success('');
        return;
    }

    // Validate that products still exist and are published
    $valid_ids = get_posts(array(
        'post_type'   => 'product',
        'post_status' => 'publish',
        'post__in'    => $fav_ids,
        'fields'      => 'ids',
        'posts_per_page' => -1
    ));

    $clean_favs = implode(',', $valid_ids);
    wp_send_json_success($clean_favs);
}

// ==========================================
// 4. THE SHORTCODE TO DISPLAY THE PAGE
// ==========================================
add_shortcode('my_favorite_cakes', 'render_favorite_cakes_page');
function render_favorite_cakes_page() {
    ob_start(); ?>
    <div id="favorite-cakes-wrapper">
        
        <!-- NEW: SHARED SECTION (Hidden by default) -->
        <div id="shared-section" style="display: none; margin-bottom: 60px;">
            <h2 style="text-align: center; margin-bottom: 30px; font-weight: bold;">Cakes Shared With You</h2>
            <div id="shared-cakes-list" class="cake-masonry-grid"></div>
            <hr style="margin-top: 40px; border-top: 2px dashed #eaeaea;">
        </div>

        <!-- MY FAVORITES SECTION -->
        <h2 id="my-favs-title" style="text-align: center; margin-bottom: 30px; font-weight: bold;">My Favorite Cakes</h2>
        <?php if (!is_user_logged_in()): ?>
        <div class="gallery-auth-buttons">
            <a href="/login" class="gallery-auth-btn">Login</a>
            <a href="/register" class="gallery-auth-btn signup">Sign Up</a>
        </div>
        <?php endif; ?>
        <p id="fav-loading-msg" style="text-align: center;">Loading your favorite cakes...</p>
        <div id="favorite-cakes-list" class="cake-masonry-grid"></div>
        
        <div style="text-align: center; margin-top: 40px;">
            <button id="share-favs-page-btn" class="button" style="display:none;"><span class="share-btn-icon">📤</span><span class="share-btn-text">Share My Favorites</span></button>
        </div>
    </div>
    <?php return ob_get_clean();
}

// ==========================================
// 5. RENDER MASONRY PRODUCTS VIA AJAX
// ==========================================
add_action('wp_ajax_render_favorite_products', 'ajax_render_favorite_products');
add_action('wp_ajax_nopriv_render_favorite_products', 'ajax_render_favorite_products');
function ajax_render_favorite_products() {
    check_ajax_referer('cake_fav_nonce', 'nonce');

    $favs = isset($_POST['favs']) ? sanitize_text_field(wp_unslash($_POST['favs'])) : '';
    $fav_ids = array_filter(array_unique(explode(',', preg_replace('/[^0-9,]/', '', $favs))));
    
    // Check if this is the shared section or the user's own section
    $is_shared = isset($_POST['is_shared']) && $_POST['is_shared'] === 'true';

    if (empty($fav_ids)) {
        wp_send_json_success('');
    }

    $args = array(
        'post_type'      => 'product',
        'post__in'       => $fav_ids,
        'posts_per_page' => -1,%"+y
        'orderby'        => 'post__in' 
    );

    $loop = new WP_Query($args);
    ob_start();

    if ($loop->have_posts()) {
        while ($loop->have_posts()) : $loop->the_post();
            $product = wc_get_product(get_the_ID());
            $image_url = wp_get_attachment_image_url($product->get_image_id(), 'full'); 
            if (!$image_url) $image_url = wc_placeholder_img_src(); 
            ?>
            
            <div class="masonry-item" id="fav-item-<?php echo esc_attr(get_the_ID()); ?>">
                <a href="<?php echo esc_url($image_url); ?>"
                   data-rel="prettyPhoto[fav-gallery]"
                   title="<?php echo esc_attr(get_the_title()); ?>"
                   data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <div class="masonry-label"><?php echo esc_html(get_the_title()); ?></div>
                </a>

                <?php if ($is_shared): ?>
                    <!-- Button for cakes shared WITH the user -->
                    <button class="save-shared-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" aria-label="Save to my favorites"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="#999" stroke-width="2"/></svg></button>
                <?php else: ?>
                    <!-- Button for the user's OWN cakes -->
                    <button class="my-custom-fav-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" aria-label="Remove from favorites"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#d63031"/></svg></button>
                <?php endif; ?>
            </div>

            <?php
        endwhile;
    } else {
        echo '<p style="text-align:center;">No cakes found.</p>';
    }

    wp_reset_postdata();
    wp_send_json_success(ob_get_clean());
}
