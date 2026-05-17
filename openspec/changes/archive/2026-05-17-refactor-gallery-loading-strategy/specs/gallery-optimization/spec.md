## MODIFIED Requirements

### Requirement: Lazy Loader Conflict Prevention

The gallery SHALL use the browser's native `loading="lazy"` attribute for image deferral and SHALL strip the `lazy` CSS class so the theme's `jquery.lazyload.js` scroll-event handler ignores gallery images entirely. The PHP output buffer SHALL be the primary authority that determines each img's `src`, `data-original`, `data-lightbox-src`, `loading`, `decoding`, `class`, and `srcset` attributes. Client-side JavaScript SHALL only re-assign `src` defensively — when the current value indicates the PHP transform did not apply (loading spinner GIF still present, or thumbnail sentinel `?t=300` missing). In the normal PHP-buffered case those defensive branches SHALL be no-ops.

#### Scenario: PHP buffer hands off to native lazy

- **Given** an `<img class="lazy attachment-shop_catalog">` with `src="…prod_loading.gif"` and `data-original="…full-image.jpg"`
- **When** the `template_redirect` output buffer processes the page
- **Then** the resulting tag SHALL have `loading="lazy"`, `decoding="async"`, `src` pointing to the `-300x300` thumbnail URL with the `?t=300` sentinel, no `lazy` class, and no `srcset` attribute

#### Scenario: JavaScript only re-assigns src defensively

- **Given** `image-lightbox.js` is running on a page where the PHP buffer applied the sentinel
- **When** `enforceThumb(img)` is called and the current `src` already contains `?t=300`
- **Then** `enforceThumb` SHALL return immediately without writing to `src`
- **And** when `prepareCard($card)` runs and the current `src` does NOT contain `prod_loading`
- **Then** `prepareCard` SHALL leave the `src` attribute untouched (it still updates `data-original`, `class`, `loading`, `decoding`, `srcset` — which are idempotent with PHP's output)

#### Scenario: Browser defers off-screen images

- **Given** the gallery has ~300 product cards rendered in initial HTML
- **When** the page loads on iOS Safari
- **Then** only thumbnails for cards in or near the viewport SHALL be fetched
- **And** the remaining thumbnails SHALL be fetched as the user scrolls them into view

### Requirement: Lightbox Uses Full-Resolution Image

The gallery SHALL open the prettyPhoto lightbox with the full-resolution version of the image. The lightbox source URL SHALL carry the `?l=1` query-string sentinel so external URL-rewriting scripts that anchor their regex to `.jpg$`/`.png$`/`.webp$` cannot modify it.

#### Scenario: data-lightbox-src points to the full URL with sentinel

- **Given** an `<img class="…shop_catalog">` with `data-original="…full-image.jpg"`
- **When** the PHP output buffer processes the tag
- **Then** the resulting tag SHALL contain `data-lightbox-src="…full-image.jpg?l=1"` (full-size, with sentinel)
- **And** `data-original` SHALL point to `…full-image-300x300.jpg?t=300` (thumbnail, with sentinel)

#### Scenario: Lightbox click opens full resolution

- **Given** a user clicks a gallery image card on the deployed site
- **When** prettyPhoto initializes the lightbox
- **Then** the displayed image SHALL be the full-resolution URL (from `data-lightbox-src`), not the 300×300 thumbnail
- **And** the image SHALL not appear pixelated when expanded

### Requirement: Gallery Page Does Not Crash on iOS Safari

The gallery page SHALL remain interactive and stable on iOS Safari. The browser's `load` event SHALL fire within a reasonable time (no perpetual address-bar spinner), and the page SHALL NOT trigger the "A problem occurred" tab-kill dialog under normal load. The page SHALL render all visible product cards (not only the first row), and scrolling SHALL reveal additional cards with their thumbnails loading lazily.

#### Scenario: All viewport images render on first paint

- **Given** the gallery is loaded on iOS Safari at iPhone-sized viewport
- **When** the page reaches DOMContentLoaded
- **Then** every product card within the viewport SHALL render its 300×300 thumbnail
- **And** the cards SHALL NOT be blank rectangles waiting to render

#### Scenario: Address-bar spinner stops

- **Given** the gallery is loaded on iOS Safari
- **When** parsing completes and deferred scripts finish executing
- **Then** the address-bar spinner SHALL stop within a few seconds
- **And** the `load` event SHALL have fired

#### Scenario: No tab-kill under sustained use

- **Given** the gallery page is open and the user scrolls through all 300+ cards
- **When** every thumbnail has eventually been loaded by the browser's native lazy mechanism
- **Then** the tab SHALL NOT show "A problem occurred on …" (iOS Safari's renderer-killed dialog)
- **And** the page SHALL remain scrollable and responsive throughout

## ADDED Requirements

### Requirement: Thumbnail URL Sentinel

The PHP output buffer SHALL append the literal query string `?t=300` to the `src` and `data-original` of every gallery `<img class="…shop_catalog">` tag, and SHALL append `?l=1` to `data-lightbox-src`. The sentinels SHALL be fixed literals (not random or per-request) so the browser cache key remains stable across loads. The sentinel exists to defeat URL-rewriter regexes that anchor on the file extension at end-of-string; WordPress and WP Engine SHALL serve the same underlying file regardless of unknown query parameters.

#### Scenario: Rogue URL-rewriter regex is neutralized

- **Given** the rogue inline script's regex `/-\d+x\d+(\.[a-z]+)$/i`
- **When** it executes against `…cake-300x300.jpg?t=300`
- **Then** the regex SHALL NOT match (end-of-string is `=300`, not `.jpg`)
- **And** the URL SHALL be returned unchanged

#### Scenario: Same file served regardless of sentinel

- **Given** `…cake-300x300.jpg` and `…cake-300x300.jpg?t=300` are both requested
- **When** WP Engine serves each URL
- **Then** both responses SHALL deliver identical image bytes (size, dimensions, content)

### Requirement: Rogue Inline Script Removal

The PHP output buffer SHALL detect and remove inline `<script>` blocks whose body contains the literal token `const stripSuffix` or `const updateImagesAndHide`. The match SHALL be precise to those declarations (not a generic substring like `stripSuffix`) so our own gallery script — which may mention the same identifier in comments — is not removed.

#### Scenario: Rogue script removed from HTML

- **Given** the page's pre-buffer HTML contains a `<script>` block declaring `const stripSuffix = (url) => …` followed by a `MutationObserver` on `document.documentElement`
- **When** the output buffer's `<script>...</script>` callback runs
- **Then** that script block SHALL be replaced with `<!-- rogue stripSuffix script removed -->`
- **And** no other `<script>` block on the page SHALL be modified

#### Scenario: Own gallery script preserved

- **Given** the inline `image-lightbox.js` block contains the substring `stripSuffix` only in comments (not as `const stripSuffix`)
- **When** the rogue-script remover runs
- **Then** the gallery script SHALL remain intact and continue to execute on page load

### Requirement: Third-Party Load Blocker Removal

On gallery views (as defined by the `_palermo_is_gallery_view()` predicate), the theme SHALL dequeue `wc-cart-fragments` and `wc-add-to-cart`, remove the WordPress emoji actions/filters, and strip the hard-coded `<script src="…google.com/recaptcha/api.js…">` tag from the output buffer. These removals SHALL be scoped to gallery views only so other product, cart, and account pages retain their normal behavior.

#### Scenario: reCAPTCHA tag stripped on gallery

- **Given** the gallery page is requested
- **When** the buffer-strip `template_redirect` hook (priority 1) runs
- **Then** the rendered HTML SHALL contain zero `recaptcha/api.js` script tags

#### Scenario: WC cart-fragments not enqueued on gallery

- **Given** the gallery page is requested
- **When** WordPress reaches `wp_print_scripts`
- **Then** the script handle `wc-cart-fragments` SHALL be dequeued (no `?wc-ajax=get_refreshed_fragments` XHR fires that blocks the load event)
- **Note:** If WooCommerce re-enqueues the handle in a later hook, a `script_loader_tag` filter that returns `''` for the handle MAY be added as a stronger removal

#### Scenario: Emoji preloads gone on gallery

- **Given** the gallery page is requested
- **When** the page HTML is generated
- **Then** `print_emoji_detection_script` and `print_emoji_styles` SHALL NOT contribute any markup
- **And** no `s.w.org/.../emoji/.../*.svg` preload SHALL be issued by the page

#### Scenario: Non-gallery pages unaffected

- **Given** a user visits a non-gallery URL such as the cart, checkout, or a non-product page
- **When** that page is rendered
- **Then** `wc-cart-fragments`, the emoji actions, and the reCAPTCHA script SHALL be present and functional as before

### Requirement: Gallery View Predicate

The theme SHALL expose a single helper function `_palermo_is_gallery_view()` that returns true on the WooCommerce shop archive, product category archive, product tag archive, OR a WordPress page with the slug `cake-gallery`. Every gallery-only customization hook SHALL guard with this helper. Using `is_page('cake-gallery')` alone is INSUFFICIENT because `/cake-gallery/` is the WooCommerce shop archive (where `is_page()` returns false).

#### Scenario: Predicate matches the shop archive URL

- **Given** the request URL is `/cake-gallery/`
- **When** WordPress sets up the main query
- **Then** `_palermo_is_gallery_view()` SHALL return `true` because `is_shop()` returns `true`
- **And** every hook guarded by this predicate SHALL fire

#### Scenario: Predicate excludes unrelated pages

- **Given** the request is for `/about-us/` or `/cart/`
- **When** the predicate is evaluated
- **Then** it SHALL return `false`
- **And** none of the gallery-only customizations SHALL apply
