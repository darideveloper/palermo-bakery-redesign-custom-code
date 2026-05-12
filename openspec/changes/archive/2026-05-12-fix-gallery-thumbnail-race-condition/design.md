## Context

Playwright investigation of `https://ccdev2026.wpenginepowered.com/cake-gallery/` revealed:

### Investigation Timeline

**Round 1 (initial report):**
- 574 `ERR_ABORTED` network requests
- 0 out of 297 gallery images had `-300x300` in their `src`
- `data-src` and `data-lazy-src` correctly set to thumbnails (from server HTML)
- `src` and `data-original` stubbornly stuck at full-res despite our JS setting them to thumbnail
- prettyPhoto opens but image never loads (`naturalWidth: 0`)

**Round 2 (after JS-only fixes: reorder, guard timer, IntersectionObserver, MutationObserver):**
- HTML still had `src` = full-res — the browser already started loading full-res images during parsing
- 4929 403/ERR_ABORTED requests — images were being requested, failed, our JS re-applied thumbnail, something reverted, repeat loop
- `data-src-protected` attribute was correctly applied but the damage happened before JS even ran
- iOS Safari crashes because 300+ images are queued before any JS can intervene

### Root Cause

The root cause is **not** a race condition with theme scripts reverting `src`. The root cause is:

**The browser loads images from `<img src="...">` during HTML parsing, before any JavaScript executes.**

Timeline:
1. **t=0ms**: Browser parses HTML with `<img src="full-res.jpg">` → starts loading 300+ full-res images
2. **t=4500ms**: `$(document).ready()` fires → our script changes `src` to thumbnail
3. **t=4500ms+**: Browser has already queued all full-res requests, connection pool is saturated, most get aborted

No JavaScript approach can fix this because JS runs 4+ seconds after image loading begins.

## Goals

- Prevent the browser from ever seeing full-res URLs in `<img src="...">` during initial HTML parsing
- Keep the lightbox functional with full-res images via anchor `href`
- Simplify the JavaScript to only handle things JS should handle (prettyPhoto binding, dynamic content)

## Non-Goals

- Replace prettyPhoto
- Modify WordPress theme core files
- Use Service Workers (overly complex for this scope)

## Solution

### PHP Filter (Root Cause Fix)

Use `wp_get_attachment_image_attributes` to rewrite `src` to `-300x300` thumbnail before the HTML leaves the server:

1. Filter fires when WordPress generates `<img>` tags for `shop_catalog` sized images
2. Uses `wp_get_attachment_image_src()` to get the correct thumbnail URL
3. Falls back to regex URL replacement if the Attachment API returns full-res
4. Also filter `woocommerce_get_image_size_shop_catalog` to ensure the size is 300x300

This is the only approach that works because it changes the HTML **before** the browser receives it.

### Simplified JavaScript

With the PHP filter doing the heavy lifting, the JS only needs:
1. Sync `data-original`, `data-src` to thumbnail for consistency
2. Set anchor `href` to full-res (for prettyPhoto lightbox)
3. Bind prettyPhoto to gallery links
4. Watch for new items from infinite scroll / category filter

The IntersectionObserver, MutationObserver src protection, queue management, and lightbox retry were all removed — they were attempting to fix a problem that can't be fixed client-side.

## Risks

- **Missing thumbnails**: If a `-300x300` thumbnail doesn't exist, the browser gets a 404. WordPress generates thumbnails for all uploads, but old images or images uploaded before the `shop_catalog` size existed might be missing them. The JS can still set `src` from the original as a fallback.
- **PHP filter scope**: The filter targets images with `shop_catalog` in the CSS class. If the theme changes the class or uses a different image size, the filter won't apply. The JS handles this as a fallback.
