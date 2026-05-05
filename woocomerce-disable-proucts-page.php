<?php

// Script located at the bottom of ccdev2026/wp-content/themes/snsvicky/functions.php
// this disbale each single woocomerce page form search bar or direct access

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