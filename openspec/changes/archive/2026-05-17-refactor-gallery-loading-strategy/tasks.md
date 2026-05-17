## 1. Reusable gallery-view predicate

- [x] 1.1 Add `_palermo_is_gallery_view()` to `functions.php` returning `is_shop() || is_product_category() || is_product_tag() || is_page('cake-gallery')`
- [x] 1.2 Use it as the guard in every new gallery-only hook (`wp_head`, `wp_enqueue_scripts`, `template_redirect`)

## 2. PHP output buffer — sentinel + native lazy + rogue-script strip

- [x] 2.1 In the existing `template_redirect` `ob_start` buffer, add a `preg_replace_callback` over `<script>...</script>` blocks that returns an empty comment for any block whose body contains `const stripSuffix` or `const updateImagesAndHide`
- [x] 2.2 Match each `<img class="…shop_catalog…">` and append `?t=300` to both `src` and `data-original`
- [x] 2.3 Inject (or overwrite) `data-lightbox-src` so it points to the full-resolution URL suffixed with `?l=1`
- [x] 2.4 Replace `src="…prod_loading.gif"` with `src="<thumbnail-with-sentinel>"`
- [x] 2.5 Inject `loading="lazy" decoding="async"` when not already present
- [x] 2.6 Strip the `lazy` class from the `class` attribute
- [x] 2.7 Drop `srcset` to prevent the browser from upgrading to a larger size

## 3. Third-party load-event blockers

- [x] 3.1 New `wp_enqueue_scripts` hook at priority 100, guarded by `_palermo_is_gallery_view()`: `wp_dequeue_script('wc-cart-fragments')`, `wp_dequeue_script('wc-add-to-cart')`
- [x] 3.2 In the same hook, remove WordPress emoji actions/filters (`print_emoji_detection_script`, `print_emoji_styles`, `the_content_feed`/`comment_text_rss`/`wp_mail` staticize filters)
- [x] 3.3 New `template_redirect` hook at priority 1 (so it registers an OUTER buffer wrapping the image-rewrite buffer): `ob_start` that `preg_replace`s the hard-coded `<script src="…google.com/recaptcha/api.js…">` tag to an empty string

## 4. JavaScript — defensive guard + chunked init

- [x] 4.1 `image-lightbox.js`: add `THUMB_SUFFIX = "-300x300"` and `SENTINEL = "?t=300"` constants
- [x] 4.2 Add `enforceThumb(img)` that re-applies `-300x300` and the sentinel if `src` is missing both, and is a no-op when the sentinel is already present
- [x] 4.3 Add a `MutationObserver` (`srcGuard`) on each gallery `<img>`'s `src` attribute that calls `enforceThumb` on any change
- [x] 4.4 Initialize prettyPhoto exactly once via `prettyPhotoInitialized` flag — `hook="data-rel"` means new infinite-scroll cards share the same gallery
- [x] 4.5 Chunk initial card processing: first 30 cards synchronously, rest via `requestAnimationFrame` to keep iOS responsive at DOM-ready
- [x] 4.6 Call `enforceAllThumbs()` + `attachSrcGuard()` from the YITH `yith_infs_added_elem` handler so new infinite-scroll batches are protected

## 5. CSS — revert content-visibility

- [x] 5.1 Remove `content-visibility: auto` and `contain-intrinsic-size: 320px 320px` from `#sns_woo_list .block-product-inner` in `product-gallery.css` (iOS Safari renders 0 of N cards otherwise)

## 6. Deploy & verify

- [x] 6.1 Upload `functions.php` via SFTP, then re-save via WP Admin → Appearance → Theme File Editor to invalidate WP Engine OPcache (SFTP alone does NOT)
- [x] 6.2 Upload `product-gallery.css` and `image-lightbox.js` via the "Simple Custom CSS and JS" plugin admin
- [x] 6.3 Purge WP Engine page cache
- [x] 6.4 Verify via curl:
  - `?t=300` count ≈ 596
  - `?l=1` count ≈ 596
  - `prod_loading.gif` count = 0
  - `<img class="lazy"` count = 0
  - `recaptcha/api.js` count = 0
  - `const stripSuffix` count = 0
- [x] 6.5 Verify on iOS Safari: address-bar spinner stops within a few seconds, all viewport images render (not just first 2), scrolling reveals more lazy-loaded thumbnails, no "A problem occurred" dialog

