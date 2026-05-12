## Context

The gallery page has a three-way conflict between the PHP output buffer filter, the theme's `jquery.lazyload` plugin, and the custom `image-lightbox.js` script:

1. **PHP filter** (`fix-gallery-thumbnail-src.php` + `functions.php`) rewrites `data-original` URLs from full-res to `-300x300` thumbnails at the HTML buffer level
2. **Theme's `jquery.lazyload`** copies `data-original` → `src` when images scroll into view, gradually loading thumbnails
3. **`image-lightbox.js`** processes all images on DOM ready — currently it removes the `lazy` class and sets `src` eagerly, defeating step 2

Additionally, there is a **server cache** (WP Engine) that often serves an older version of the page where `data-original` still has the full-resolution URL. This means `image-lightbox.js` must handle BOTH scenarios:
- **Fresh page**: `data-original` = 300x300 thumbnail (PHP filter applied)
- **Cached page**: `data-original` = full-resolution URL (PHP filter bypassed)

## Goals / Non-Goals

- **Goals:**
  - Images load only when scrolled into view (preserve lazyload)
  - Grid thumbnails use 300×300 resolution
  - Lightbox shows full-resolution images
  - No iOS crash from memory pressure
- **Non-Goals:**
  - Changing the theme's lazyload plugin or configuration
  - Replacing prettyPhoto lightbox library
  - Adding server-side pagination (the gallery is intentionally one-page)

## Decisions

### Decision 1: Preserve lazyload, don't bypass it

The existing `image-lightbox.js` logic at line 82 (`$img.removeClass("lazy")`) and line 95 (`$img.attr("src", thumbnailSrc)`) explicitly bypasses the theme's lazy loading. The fix removes both lines and only updates `data-original` so lazyload copies the thumbnail URL when the image scrolls into view.

- Alternatives considered:
  - **A: Replace jquery.lazyload with IntersectionObserver** — overkill for this fix, introduces new dependency
  - **B: Add a separate `data-thumb` attribute** and handle loading ourselves — more complex, more JS, same result as just letting lazyload work
- **Chosen**: Let lazyload do its job. The only change to image attributes is `data-original` (pointing to the 300x300 thumbnail). No touch of `class`, `src`, or `loading`.

### Decision 2: PHP filter stores full URL in `data-lightbox-src`

The lightbox needs the full-resolution URL, but after the PHP filter rewrites `data-original` to the thumbnail URL, the original full URL is lost. By storing it in a `data-lightbox-src` attribute, the JS can trivially read it without URL string manipulation.

For cached pages where the PHP filter didn't run, `image-lightbox.js` falls back to stripping `-300x300` from `data-original` (after it rewrites `data-original` to the thumbnail).

### Decision 3: Handle both cached and fresh page scenarios

The `image-lightbox.js` already handles this via the `isOptimized` check at line 51. We keep this branch but correct the behavior in both paths:
- **`isOptimized = true`** (fresh page): Use `data-lightbox-src` or the original `data-original` before rewrite for the lightbox href
- **`isOptimized = false`** (cached page): Rewrite `data-original` to 300x300 but DON'T touch `class` or `src`; capture the original URL for the lightbox href

## Risks / Trade-offs

- **Risk**: The theme's `sns-woocommerce.js` may also upgrade images to full resolution after lazyload completes.
  - **Mitigation**: Verify after deployment that `data-original` stays as the thumbnail URL after scroll. If the theme script reverts it, we'll need to override it with a post-lazyload callback that re-applies the thumbnail URL.
- **Risk**: Images that don't have a `-300x300` crop will show broken images in the grid when lazyload copies `data-original`.
  - **Mitigation**: The `woocommerce_get_image_size_shop_catalog` filter sets crop to 300x300, so all new uploads have it. For legacy images, regenerate thumbnails with a plugin.

## Open Questions

- Should the duplicate PHP code (same logic in `functions.php` and `fix-gallery-thumbnail-src.php`) be deduplicated? Both files are active on the server; having two identical output buffers running sequentially makes the filter run twice, which is wasteful but idempotent.
