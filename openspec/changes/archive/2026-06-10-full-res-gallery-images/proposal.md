## Why

The gallery grid currently forces all product images to load as 300x300 thumbnails (`-300x300.jpg?t=300`), even though the `data-lightbox-src` points to the full-resolution version. This creates a visible quality gap: grid images appear soft/blurry while lightbox images look sharp. Customers browsing the grid should see the same high-quality photography they get when opening the lightbox.

## What Changes

- Remove `-300x300` suffix enforcement from ALL gallery image URLs (grid `src`, `data-original`, `data-lightbox-src`, and lightbox `<a href>`)
- Grid images will load at their original/full resolution, matching lightbox quality
- Keep the `?t=300` and `?l=1` sentinel query parameters (still needed to defeat the rogue `stripSuffix` plugin)
- Keep native lazy-loading (`loading="lazy"`, `decoding="async"`) and `srcset` stripping (still needed for iOS performance)
- Remove the JS MutationObserver guard that forcibly reverts `src` back to `-300x300`
- Remove the WooCommerce `shop_catalog` image size filter that forces 300x300 cropping

## Capabilities

### New Capabilities
- *(none — all changes modify existing capabilities)*

### Modified Capabilities
- `gallery-optimization`: Remove thumbnail enforcement — grid images should load at full resolution instead of being downgraded to 300x300. All sentinel, lazy-loading, rogue-script, and iOS stability requirements remain unchanged except for the forced dimension suffix.
- `gallery-modal-ui`: Update lightbox link generation to point to the same full-res URL used by the grid (no separate stripping logic needed since grid already has full-res).

## Impact

- `src/core/functions.php` — Remove `shop_catalog` size filter; rewrite output buffer to stop appending/stripping `-300x300`
- `src/features/lightbox/image-lightbox.js` — Remove `enforceThumb()` / `enforceAllThumbs()` / `attachSrcGuard()`; simplify `prepareCard()` to use full-res for both grid `src` and lightbox `href`
- `src/features/gallery/product-gallery.css` — Verify grid aspect-ratio styling works with varying image dimensions
- **Performance**: Grid pages will load ~20x more data per image (full-res ~200-300KB vs 300x300 thumbnail ~10-15KB). For ~300 images, this could mean 60-90MB vs 3-5MB of images on the page. Lazy-loading mitigates initial load cost but bandwidth impact per scroll remains.
