# Change: Replace jquery.lazyload with native lazy + sentinel pattern; eliminate iOS load-event blockers

## Why

After deploying the previous fix (`2026-05-12-fix-gallery-performance-ios-crash`) the iOS Safari freeze did NOT go away. Investigation revealed three independent failure modes the earlier proposal couldn't address by working WITH `jquery.lazyload`:

1. **`jquery.lazyload` itself is the iOS culprit.** Its scroll-event handler iterates every gallery `<img>` on every scroll/touch event. With ~600 product image tags on `/cake-gallery/`, this pegs the iOS main thread on touch-scroll. The previous spec required preserving the `lazy` class — but doing so KEEPS the offending scroll handler attached.

2. **A rogue inline script in the "Simple Custom CSS and JS" plugin** declares `const stripSuffix` and runs every ~450 ms (its `setTimeout(loader.remove, 450)` removes its own guard, causing the MutationObserver to re-fire indefinitely). It strips `-300x300` from every img's `src`, forcing full-resolution loads. The previous spec didn't know about it.

3. **`load` event never fires** because of slow third-party subresources (`?wc-ajax=get_refreshed_fragments`, Google reCAPTCHA, WordPress emoji SVG preloads, Cloudflare bot-challenge probe). On iOS this shows the address-bar spinner forever and contributes to memory pressure that produces the "A problem occurred" tab-kill dialog.

Additionally, the previous spec assumed `is_page('cake-gallery')` would match the gallery URL, but `/cake-gallery/` is the WooCommerce shop archive (body class `post-type-archive-product woocommerce-shop`), where `is_page()` returns FALSE.

## What Changes

- **MODIFIED behavior — Lazy Loader Conflict Prevention.** Instead of preserving `jquery.lazyload`, the PHP buffer now strips `class="lazy"` from gallery images and injects `loading="lazy"` + `decoding="async"`, handing lazy-loading to the browser's native implementation. This eliminates the scroll-event handler entirely.

- **ADDED — Thumbnail URL Sentinel.** The PHP buffer appends `?t=300` to the thumbnail `src` and `data-original`, and `?l=1` to `data-lightbox-src`. This breaks the end-of-string anchor in any rogue URL-rewriter regex (the rogue plugin's `/-\d+x\d+(\.[a-z]+)$/i`), making the rewriter a no-op on our gallery URLs while WordPress still serves the same file (unknown query params are ignored by WP).

- **ADDED — Rogue Inline Script Removal.** The PHP buffer runs a `preg_replace_callback` over `<script>...</script>` blocks and removes any whose body contains `const stripSuffix` or `const updateImagesAndHide` (the rogue plugin's exact function declarations).

- **ADDED — Third-Party Load Blocker Removal.** A new `wp_enqueue_scripts` hook at priority 100 dequeues `wc-cart-fragments` and `wc-add-to-cart` and removes the WordPress emoji actions/filters on gallery views. A new `template_redirect` hook with `ob_start` strips the hard-coded reCAPTCHA `<script src="…/recaptcha/api.js">` tag from the buffer.

- **ADDED — Gallery Predicate Function.** A reusable `_palermo_is_gallery_view()` helper returns `is_shop() || is_product_category() || is_product_tag() || is_page('cake-gallery')` so every new gallery-only hook uses the same condition. The previous proposal's hooks broke because they only used `is_page('cake-gallery')`, which returns FALSE on the WC shop archive.

- **REMOVED — `content-visibility: auto`** from `.block-product-inner` in `product-gallery.css`. iOS Safari has known bugs where this CSS property skips rendering in grid layouts and never recovers when scrolled into view, which surfaced as "only first 2 images visible".

- **NOT changed** — Lightbox still uses `data-lightbox-src` with the full-resolution URL (now suffixed with `?l=1`); prettyPhoto opens the full image.

## Impact

- Affected specs: `gallery-optimization` (MODIFIED 3 requirements, ADDED 4)
- Affected code:
  - `functions.php` — PHP output buffer extended; new `wp_enqueue_scripts` and `template_redirect` hooks; `_palermo_is_gallery_view()` helper
  - `image-lightbox.js` — defensive `enforceThumb` + MutationObserver guard; chunked initial card processing (`CHUNK_SIZE = 30`, rAF-driven); `prepareCard` is now idempotent and only writes `src` when the current value is the loading-spinner GIF (a no-op when PHP has already injected the thumbnail src). `prepareCard` continues to strip `class="lazy"`, set `loading="lazy"` + `decoding="async"`, drop `srcset`, and update `data-original` — these duplicate the PHP buffer's work but are kept as a second line of defense for cached/legacy HTML
  - `product-gallery.css` — removed `content-visibility: auto` and `contain-intrinsic-size` from product cards
- **Operational gotcha to document:** SFTP uploads of PHP files do NOT invalidate WP Engine's OPcache. Edits must be saved via WP Admin → Appearance → Theme File Editor (which calls `opcache_invalidate()` on save) for changes to take effect.
- No breaking changes — restores intended behavior on iOS Safari without regressing desktop or Android.
