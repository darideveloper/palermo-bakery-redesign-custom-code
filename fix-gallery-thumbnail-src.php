<?php

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
// 2. Rewrite data-original to use 300x300 thumbnails via output buffering.
// ---------------------------------------------------------------------------
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
