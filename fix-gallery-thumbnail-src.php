<?php

// Place this code in your theme's functions.php or use a "Code Snippets" plugin.

/**
 * FIX: Serve 300x300 thumbnails in the gallery grid instead of full-resolution images.
 *
 * The browser loads images from <img src="..."> during HTML parsing, BEFORE any
 * JavaScript runs. The theme generates the HTML manually with full-res src, so
 * WordPress filters like wp_get_attachment_image_attributes never fire.
 *
 * This fix uses PHP output buffering to rewrite <img src="..."> and
 * data-original to -300x300 thumbnails before the page is sent to the browser.
 * The anchor <a href="..."> is left unchanged so the lightbox still opens the
 * full-resolution image.
 *
 * Also keeps data-src / data-lazy-src in sync (they already have the thumbnail
 * URL from the theme template).
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
// 2. Rewrite image src to use 300x300 thumbnails via output buffering.
// ---------------------------------------------------------------------------
add_action('template_redirect', function () {
    // Only apply to gallery / product archive pages.
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

        // Match every <img> whose src points to the uploads directory.
        $buffer = preg_replace_callback(
            '/<img\s[^>]*?src="([^"]*?\/uploads\/[^"]*?)\.(jpg|jpeg|png|webp)"[^>]*>/i',
            function ($m) {
                // Skip if already a thumbnail.
                if (strpos($m[1], '-300x300') !== false) {
                    return $m[0];
                }

                // Rewrite the src attribute.
                $old_url = $m[1] . '.' . $m[2];
                $new_url = $m[1] . '-300x300.' . $m[2];
                $img     = str_replace($old_url, $new_url, $m[0]);

                // Rewrite data-original if it points to the full-res upload.
                $img = preg_replace_callback(
                    '/data-original="([^"]*?\/uploads\/[^"]*?)\.(jpg|jpeg|png|webp)"/i',
                    function ($n) {
                        if (strpos($n[1], '-300x300') !== false) {
                            return $n[0];
                        }
                        return 'data-original="' . $n[1] . '-300x300.' . $n[2] . '"';
                    },
                    $img
                );

                return $img;
            },
            $buffer
        );

        return $buffer;
    });
});
