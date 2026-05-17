<?php
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
        <p id="fav-loading-msg" style="text-align: center;">Loading your favorite cakes...</p>
        <div id="favorite-cakes-list" class="cake-masonry-grid"></div>
        <div style="text-align: center; margin-top: 30px;">
            <button id="share-favs-page-btn" class="button" style="display:none;">Share My Favorites</button>
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

    if (empty($fav_ids)) {
        wp_send_json_success('<p style="text-align:center;">Your favorites list is empty.</p>');
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
            $image_url = wp_get_attachment_image_url($product->get_image_id(), 'full');
            if (!$image_url) $image_url = wc_placeholder_img_src();
            ?>

            <div class="masonry-item">
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <div class="masonry-label"><?php echo esc_html(get_the_title()); ?></div>
                </a>
            </div>

            <?php
        endwhile;
    } else {
        echo '<p style="text-align:center;">No cakes found.</p>';
    }

    wp_reset_postdata();
    wp_send_json_success(ob_get_clean());
}
