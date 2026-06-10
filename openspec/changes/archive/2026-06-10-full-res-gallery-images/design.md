## Context

The gallery currently has a multi-layer image URL pipeline:

1. **PHP output buffer** (`functions.php:1190-1321`) intercepts every gallery `<img>` tag, forces `-300x300` thumbnails on `src`/`data-original`, injects `data-lightbox-src` with the stripped full-res URL, and adds `?t=300`/`?l=1` sentinels.
2. **JS `prepareCard()`** (`image-lightbox.js:77-130`) reads `data-original`, constructs thumbnail/lightbox URLs, sets `src` from spinner GIF to thumbnail, and binds prettyPhoto.
3. **JS `enforceThumb()`** (`image-lightbox.js:12-46`) runs a MutationObserver that catches any `src` change on grid images and forcibly reverts to `-300x300`.
4. **WooCommerce `shop_catalog` size** is locked to 300x300 via filter (`functions.php:1192-1198`).

All four layers conspire to keep grid images at 300x300. To serve full-res images in the grid, all four must be modified.

## Goals / Non-Goals

**Goals:**
- Grid `src` and `data-original` point to full-resolution images (no `-300x300` suffix)
- Lightbox continues to show full-resolution images
- `?t=300` and `?l=1` sentinels remain in place to defeat the rogue `stripSuffix` plugin
- Native lazy-loading, `decoding="async"`, and `srcset` stripping remain active
- Gallery layout (1:1 aspect ratio, grid spacing) is preserved

**Non-Goals:**
- Changing the favorites page (already renders full-size via AJAX)
- Changing the wedding cake color-swap script (separate concern)
- Modifying the sentinel mechanism itself (`?t=300` / `?l=1` stay)
- Removing lazy-loading or `srcset` stripping

## Decisions

1. **Use the original `data-lightbox-src` full URL for both grid and lightbox**
   - The PHP buffer already computes the full-res URL for `data-lightbox-src`. Instead of computing a separate thumbnail URL, the grid `src` and `data-original` will use the same full-res URL. This eliminates the `-300x300` logic entirely.
   - Alternative considered: keeping the thumbnail in the grid and only fixing lightbox. Rejected because the user wants full-res in both places.

2. **Keep the `?t=300` sentinel on grid `src` even without `-300x300`**
   - The sentinel prevents the rogue `stripSuffix` plugin from re-assigning `img.src` every 450ms. Even though there's no `-300x300` to strip now, the sentinel still breaks the plugin's regex anchor (`\.(jpg|png|webp)$`), preventing it from touching gallery images at all.
   - This also prevents other unknown plugins with similar regex-based URL rewriting from causing issues.

3. **Remove the JS `enforceThumb()` MutationObserver entirely**
   - Its sole purpose was to revert `src` back to `-300x300` if anything changed it. With no thumbnails to enforce, this code becomes counterproductive (it would revert to `-300x300` if ever triggered).
   - The script guard (`cakeGalleryScriptLoaded`) stays.

4. **Remove the `shop_catalog` size filter**
   - This filter forces WooCommerce to generate/crop at 300x300. Since we're no longer using that size, the filter is unnecessary. WordPress will fall back to `woocommerce_thumbnail` (default 300x300 anyway) or the full size depending on how the theme calls `the_post_thumbnail()`.

5. **Keep `srcset` stripping**
   - Without explicit size control, the browser might pick a `srcset` variant larger than intended. Stripping `srcset` ensures the URL we set is the only one the browser requests.

## Risks / Trade-offs

| Risk | Impact | Mitigation |
|------|--------|------------|
| **Bandwidth explosion** — full-res images are ~20x larger than 300x300 thumbnails | Slow initial load, higher data cost on mobile | Lazy-loading already active; consider adding `loading="eager"` only for above-fold images. Bandwidth increase is inherent to the requirement. |
| **iOS Safari crash** — original problem that prompted thumbnail enforcement | Browser may crash decoding many full-res images | Keep `loading="lazy"` and `decoding="async"`. The 300x300 thumbnail was one mitigation but lazy-loading and chunked processing are the primary fixes. Test on iPhone. |
| **Aspect ratio distortion** — full-res images may not be square | Grid misalignment | CSS `object-fit: cover` with `aspect-ratio: 1/1` handles non-square images. Existing CSS in `product-gallery.css` already enforces this. |
| **`?t=300` on full-res URLs causes CDN cache misses** | Slower repeat visits | `?t=300` is a cache buster by design. Consider whether to remove it from full-res URLs (the rogue plugin only matches URLs ending in `.jpg/.png`, so full-res URLs with `?l=1` already end in `1` not `.jpg` — the sentinel may be redundant on `src` if we keep `?l=1`). |
