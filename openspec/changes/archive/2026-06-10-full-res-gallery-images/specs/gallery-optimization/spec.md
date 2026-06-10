# gallery-optimization Specification (Delta)

## Purpose
This delta specification updates the gallery optimization strategy to serve full-resolution images in the grid instead of 300x300 thumbnails. The primary goals remain preventing memory crashes on iOS Safari, ensuring native lazy-loading, protecting URLs from rogue scripts via sentinels, and removing non-essential third-party scripts on gallery views.

## MODIFIED Requirements

### Requirement: Lazy Loader Conflict Prevention

The gallery SHALL use the browser's native `loading="lazy"` attribute for image deferral and SHALL strip the `lazy` CSS class so the theme's `jquery.lazyload.js` scroll-event handler ignores gallery images entirely. The PHP output buffer SHALL be the primary authority that determines each img's `src`, `data-original`, `data-lightbox-src`, `loading`, `decoding`, `class`, and `srcset` attributes. Client-side JavaScript SHALL only re-assign `src` defensively — when the current value indicates the PHP transform did not apply (loading spinner GIF still present). In the normal PHP-buffered case those defensive branches SHALL be no-ops.

The `src` and `data-original` attributes SHALL point to the full-resolution image URL (no `-300x300` dimension suffix). The thumbnail enforcement layer (`enforceThumb`, `enforceAllThumbs`, `attachSrcGuard`, and their MutationObserver) SHALL be removed entirely.

#### Scenario: PHP buffer hands off to native lazy with full-res

- **Given** an `<img class="lazy attachment-shop_catalog">` with `src="…prod_loading.gif"` and `data-original="…full-image.jpg"`
- **When** the `template_redirect` output buffer processes the page
- **Then** the resulting tag SHALL have `loading="lazy"`, `decoding="async"`, `src` pointing to the full-resolution URL with the `?t=300` sentinel, `data-original` pointing to the same full-resolution URL with `?t=300`, no `lazy` class, and no `srcset` attribute
- **And** the URL SHALL NOT contain a `-300x300` or any `-\d+x\d+` dimension suffix

#### Scenario: JavaScript leaves src untouched when PHP already applied

- **Given** `image-lightbox.js` is running on a page where the PHP buffer applied the sentinel
- **When** `prepareCard($card)` runs and the current `src` does NOT contain `prod_loading`
- **Then** `prepareCard` SHALL leave the `src` attribute untouched (it still updates `data-original`, `class`, `loading`, `decoding`, `srcset` — which are idempotent with PHP's output)
- **And** `prepareCard` SHALL NOT run any `enforceThumb`/`enforceAllThumbs`/`attachSrcGuard` functions

#### Scenario: No thumbnail enforcement MutationObserver runs

- **Given** a gallery page is loaded
- **When** `image-lightbox.js` initializes
- **Then** no MutationObserver SHALL be attached to gallery `<img>` elements for the purpose of reverting `src` to a thumbnail
- **And** the `enforceThumb` function SHALL NOT exist in the execution scope

#### Scenario: Browser defers off-screen images

- **Given** the gallery has ~300 product cards rendered in initial HTML
- **When** the page loads on iOS Safari
- **Then** only images for cards in or near the viewport SHALL be fetched
- **And** the remaining images SHALL be fetched as the user scrolls them into view

### Requirement: Lightbox Uses Full-Resolution Image

The gallery SHALL open the prettyPhoto lightbox with the full-resolution version of the image. The lightbox source URL SHALL carry the `?l=1` query-string sentinel so external URL-rewriting scripts that anchor their regex to `.jpg$`/`.png$`/`.webp$` cannot modify it.

The `data-original` attribute SHALL now point to the same full-resolution URL as `data-lightbox-src` (both without `-300x300` suffix). The grid `src` SHALL also point to the full-resolution URL.

#### Scenario: data-original and data-lightbox-src both point to full-res

- **Given** an `<img class="…shop_catalog">` with `data-original="…full-image.jpg"`
- **When** the PHP output buffer processes the tag
- **Then** the resulting tag SHALL contain `data-lightbox-src="…full-image.jpg?l=1"` (full-size, with sentinel)
- **And** `data-original` SHALL point to `…full-image.jpg?t=300` (full-size, with sentinel — same path as lightbox src, different sentinel)
- **And** neither URL SHALL contain a `-300x300` dimension suffix

#### Scenario: Lightbox click opens full resolution

- **Given** a user clicks a gallery image card on the deployed site
- **When** prettyPhoto initializes the lightbox
- **Then** the displayed image SHALL be the full-resolution URL (from `data-lightbox-src`), not the 300×300 thumbnail
- **And** the image SHALL not appear pixelated when expanded

### Requirement: Gallery Page Does Not Crash on iOS Safari

The gallery page SHALL remain interactive and stable on iOS Safari. The browser's `load` event SHALL fire within a reasonable time (no perpetual address-bar spinner), and the page SHALL NOT trigger the "A problem occurred" tab-kill dialog under normal load. The page SHALL render all visible product cards (not only the first row), and scrolling SHALL reveal additional cards with their images loading lazily.

#### Scenario: All viewport images render on first paint

- **Given** the gallery is loaded on iOS Safari at iPhone-sized viewport
- **When** the page reaches DOMContentLoaded
- **Then** every product card within the viewport SHALL render its full-resolution image
- **And** the cards SHALL NOT be blank rectangles waiting to render

#### Scenario: Address-bar spinner stops

- **Given** the gallery is loaded on iOS Safari
- **When** parsing completes and deferred scripts finish executing
- **Then** the address-bar spinner SHALL stop within a few seconds (may take longer than before due to larger images)
- **And** the `load` event SHALL have fired

#### Scenario: No tab-kill under sustained use

- **Given** the gallery page is open and the user scrolls through all 300+ cards
- **When** every full-resolution image has eventually been loaded by the browser's native lazy mechanism
- **Then** the tab SHALL NOT show "A problem occurred on …" (iOS Safari's renderer-killed dialog) under normal conditions
- **And** the page SHALL remain scrollable and responsive throughout

## REMOVED Requirements

### Requirement: Thumbnail URL Sentinel (dimension-specific behavior)

**Reason**: The `?t=300` sentinel query string is retained, but it is no longer paired with an enforced `-300x300` dimension suffix. The sentinel now protects the full-resolution URL instead of a thumbnail URL. The gallery no longer injects `-300x300` or any `-\d+x\d+` dimension suffix into image URLs.

**Migration**: No migration needed. The `?t=300` sentinel continues to function on the full-resolution URLs, and the `?l=1` sentinel on `data-lightbox-src` continues unchanged.

## ADDED Requirements

### Requirement: Full-Resolution Grid Images

The gallery grid SHALL display images at their original uploaded resolution instead of 300x300 thumbnails. The PHP output buffer SHALL set `src` and `data-original` to the same full-resolution URL (with `?t=300` sentinel) rather than constructing a `-300x300` thumbnail URL. The WooCommerce `shop_catalog` image size filter (forcing 300x300 crop) SHALL be removed so WordPress-generated intermediate sizes are not wasted.

#### Scenario: Grid image src matches the full resolution URL

- **Given** a product image with original file `cake-example.jpg`
- **When** the gallery page HTML is generated by the PHP output buffer
- **Then** `<img src>` SHALL be `…/cake-example.jpg?t=300`
- **And** `<img data-original>` SHALL be `…/cake-example.jpg?t=300`
- **And** neither SHALL be `…/cake-example-300x300.jpg?t=300`

#### Scenario: WooCommerce shop_catalog size filter removed

- **Given** the theme's `functions.php`
- **When** `woocommerce_get_image_size_shop_catalog` filter is evaluated
- **Then** no custom callback SHALL be registered for this filter
- **And** WooCommerce SHALL fall back to its default `shop_catalog` size configuration

### Requirement: Incremental Lightbox Initialization (unchanged, kept for clarity)

The gallery SHALL support incremental initialization of the lightbox. As new product cards are added to the DOM (via infinite scroll or background chunking), the script SHALL bind the `prettyPhoto` lightbox specifically to the newly prepared elements.

#### Scenario: Performance and iOS stability preserved with full-res images
- **Given** the incremental binding logic is active
- **When** 300+ cards are processed and bound over time with full-resolution images
- **Then** the page SHALL NOT freeze on iOS Safari
- **And** the main thread SHALL remain responsive (yielding to the browser between chunks via `requestAnimationFrame`)
