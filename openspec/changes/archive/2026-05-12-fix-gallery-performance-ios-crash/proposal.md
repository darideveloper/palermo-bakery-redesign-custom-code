# Change: Fix gallery performance, iOS crash, and lightbox resolution

## Why

The cake gallery page (`/cake-gallery/`) loads all 297 products on a single page with full-resolution images. The `image-lightbox.js` script removes the `lazy` CSS class from every product image and eagerly sets the `src` attribute, defeating the theme's `jquery.lazyload` plugin. All 297 full-res images then download immediately regardless of viewport position, causing:

- **iOS Safari crash** from memory pressure (mobile Safari's per-tab memory limit is ~600MB; 297 large images exceed this)
- **Extremely slow page load** on all devices (hundreds of simultaneous image requests)
- **Lightbox shows thumbnails** instead of full-resolution images (blurry when expanded)
- **PHP output buffer and JavaScript fight each other** — the PHP filter rewrites `data-original` to 300x300, then JS reverts it, then JS rewrites it again

## What Changes

- **`image-lightbox.js`**: Stop removing `class="lazy"` and stop setting `src` eagerly. Let the theme's lazyload handle image loading. Fix the lightbox `href` to point to the full-resolution URL.
- **`fix-gallery-thumbnail-src.php`** (and duplicate code in `functions.php`): Preserve the original full-resolution URL in a new `data-lightbox-src` attribute so JavaScript can use it for the lightbox without guesswork.
- **`openspec/specs/gallery-optimization/spec.md`**: Reverse the requirement that says "`lazy` class SHALL be removed" — the correct behavior is to preserve `lazy` and let lazyload manage image loading.

## Impact

- Affected specs: `gallery-optimization`
- Affected code: `image-lightbox.js`, `fix-gallery-thumbnail-src.php`, `functions.php`
- No breaking changes — all changes restore intended behavior (gallery shows thumbnails in grid, full-res in lightbox, images load on scroll)
