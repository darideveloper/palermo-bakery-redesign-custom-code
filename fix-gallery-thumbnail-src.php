<?php

// Place this code in your theme's functions.php or use a "Code Snippets" plugin.

/**
 * FIX: Serve 300x300 thumbnails in the gallery grid instead of full-resolution images.
 *
 * The browser starts loading images from <img src="..."> during HTML parsing,
 * BEFORE any JavaScript runs. If the src is the full-resolution image, 300+
 * simultaneous requests saturate the connection pool, causing iOS Safari to
 * crash or time out.
 *
 * This fix ensures the server sends thumbnail URLs in <img src="..."> so the
 * browser only loads small thumbnails initially. The lightbox still receives
 * the full-resolution image via the anchor's href.
 */

// ---------------------------------------------------------------------------
// 1. Ensure the WooCommerce catalog thumbnail size is 300x300.
// ---------------------------------------------------------------------------
add_filter('woocommerce_get_image_size_shop_catalog', function ($size) {
    return array(
        'width'  => 300,
        'height' => 300,
        'crop'   => 1,
    );
});

// ---------------------------------------------------------------------------
// 2. Override the src attribute on product loop images to use 300x300 thumbnails.
// ---------------------------------------------------------------------------
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    // Only run on the front end
    if (is_admin()) {
        return $attr;
    }

    // Only modify images that belong to the product loop (shop_catalog size)
    $class = isset($attr['class']) ? $attr['class'] : '';
    if (strpos($class, 'shop_catalog') === false && strpos($class, 'product-image') === false) {
        return $attr;
    }

    // Skip if already a thumbnail
    if (!empty($attr['src']) && strpos($attr['src'], '-300x300') !== false) {
        return $attr;
    }

    // Strategy A: Use WordPress Attachment API to get the correct thumbnail URL.
    if (!empty($attachment->ID)) {
        $thumbnail = wp_get_attachment_image_src($attachment->ID, 'shop_catalog');
        if ($thumbnail && !empty($thumbnail[0])) {
            $thumbnail_url = $thumbnail[0];
            // Make sure we actually got the 300x300 version, not the full-res.
            if (strpos($thumbnail_url, '-300x300') !== false) {
                $attr['src'] = $thumbnail_url;
                return $attr;
            }
        }
    }

    // Strategy B: Fallback – manually rewrite the URL to add -300x300.
    if (!empty($attr['src'])) {
        $new_src = preg_replace(
            '/\.(jpg|jpeg|png|webp)$/i',
            '-300x300$0',
            $attr['src']
        );
        // Only apply if the URL changed (avoid infinite loops).
        if ($new_src !== $attr['src']) {
            $attr['src'] = $new_src;
        }
    }

    return $attr;
}, 10, 3);
