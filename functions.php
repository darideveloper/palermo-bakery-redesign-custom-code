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
    // Body style
    $bodycss = '';
    if (snsvicky_themeoption('use_boxedlayout') == 1) {
        if( $snsvicky_opt['body_bg_type'] == 'img' ){
            $bodycss .= 'background-image: url('.$snsvicky_opt['body_bg_type_img']['url'].');';
        }
    }
    if(isset($snsvicky_opt['body_font']) && is_array($snsvicky_opt['body_font'])) {
        $body_font = '';
        foreach($snsvicky_opt['body_font'] as $propety => $value)
            if($value != 'true' && $value != 'false' && $value != '' && $propety != 'subsets')
                $body_font .= $propety . ':' . $value . ';';
        
        if($body_font != '') $bodycss .= $body_font;
    }
    $inline_css .= 'body {'.$bodycss.'}';
    return $inline_css;
}

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

add_filter('woocommerce_get_image_size_shop_catalog', function ($size) {
    return array(
        'width'  => 300,
        'height' => 300,
        'crop'   => 1,
    );
});


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

        // Match <img> with "shop_catalog" in class AND data-original
        // pointing to /uploads/. Handles attributes in any order.
        $buffer = preg_replace_callback(
            '/<img\s(?=[^>]*class="[^"]*shop_catalog[^"]*")[^>]*data-original="([^"]*?\/uploads\/[^"]*?)\.(jpg|jpeg|png|webp)"[^>]*>/i',
            function ($m) {
                $path      = $m[1];
                $ext       = $m[2];
                $full_tag  = $m[0];

                if (strpos($path, '-300x300') !== false) {
                    return $full_tag;
                }

                // Rewrite data-original to the 300x300 thumbnail.
                // Preserve the original full URL as data-lightbox-src for the lightbox.
                $old_url = $path . '.' . $ext;
                $new_url = $path . '-300x300.' . $ext;
                $new_tag = str_replace(
                    'data-original="' . $old_url . '"',
                    'data-original="' . $new_url . '" data-lightbox-src="' . $old_url . '"',
                    $full_tag
                );

                // Also rewrite src if it happens to point at an upload
                // (some themes put the real URL in both).
                $new_tag = preg_replace_callback(
                    '/src="([^"]*?\/uploads\/[^"]*?)\.(jpg|jpeg|png|webp)"/i',
                    function ($s) {
                        if (strpos($s[1], '-300x300') !== false) {
                            return $s[0];
                        }
                        return 'src="' . $s[1] . '-300x300.' . $s[2] . '"';
                    },
                    $new_tag
                );

                return $new_tag;
            },
            $buffer
        );

        return $buffer;
    });
});


// =============== END section is for optimize gallery images ============
// 
//================ Section start for favorite cake =======================
// 
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
            isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>
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
    $clean_favs = preg_replace('/[^0-9,]/', '', $favs);
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

    $favs = get_user_meta(get_current_user_id(), 'my_cake_favorites', true);
    wp_send_json_success($favs);
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
        <p id="fav-loading-msg" style="text-align: center;">Loading your favorite cakes...</p>
        <div id="favorite-cakes-list" class="cake-masonry-grid"></div>
        
        <div style="text-align: center; margin-top: 40px;">
            <button id="share-favs-page-btn" class="button" style="display:none; padding: 12px 24px; font-weight: bold; background: #333; color: #fff; border: none; border-radius: 6px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Share My Favorites</button>
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
    $fav_ids = array_filter(explode(',', preg_replace('/[^0-9,]/', '', $favs)));
    
    // Check if this is the shared section or the user's own section
    $is_shared = isset($_POST['is_shared']) && $_POST['is_shared'] === 'true';

    if (empty($fav_ids)) {
        wp_send_json_success('');
    }

    $args = array(
        'post_type'      => 'product',
        'post__in'       => $fav_ids,
        'posts_per_page' => -1,
        'orderby'        => 'post__in' 
    );

    $loop = new WP_Query($args);
    ob_start();

    if ($loop->have_posts()) {
        while ($loop->have_posts()) : $loop->the_post();
            $product = wc_get_product(get_the_ID());
            $image_url = wp_get_attachment_image_url($product->get_image_id(), 'large'); 
            if (!$image_url) $image_url = wc_placeholder_img_src(); 
            ?>
            
            <div class="masonry-item" id="fav-item-<?php echo esc_attr(get_the_ID()); ?>">
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <div class="masonry-label"><?php echo esc_html(get_the_title()); ?></div>
                </a>
                
                <?php if ($is_shared): ?>
                    <!-- Button for cakes shared WITH the user -->
                    <button class="save-shared-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" aria-label="Save to my favorites">🤍</button>
                <?php else: ?>
                    <!-- Button for the user's OWN cakes -->
                    <button class="remove-fav-btn" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" aria-label="Remove from favorites">✖</button>
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