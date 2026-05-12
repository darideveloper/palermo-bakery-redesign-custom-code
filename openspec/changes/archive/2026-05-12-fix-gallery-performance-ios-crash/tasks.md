## 1. Fix PHP output buffer to preserve full-resolution URL

- [x] 1.1 In `fix-gallery-thumbnail-src.php` (and the duplicate block in `functions.php`), after rewriting `data-original` to the 300x300 URL, inject a `data-lightbox-src` attribute with the original full-resolution URL
- [x] 1.2 Verify via curl that `data-lightbox-src` appears in the raw HTML with the full URL, and `data-original` still has the 300x300 thumbnail URL

## 2. Fix `image-lightbox.js` — stop breaking lazyload

- [x] 2.1 In the `if (!isOptimized)` block (line 74), remove the `$img.removeClass("lazy")` call — keeping the `lazy` class lets the theme's lazyload control when images download
- [x] 2.2 Remove the `$img.attr("src", thumbnailSrc)` call — setting `src` eagerly bypasses lazyload and triggers immediate download; lazyload will copy `data-original` to `src` when scrolled into view
- [x] 2.3 Keep the `data-original`, `data-src`, and `data-lazy-src` updates so lazyload loads the 300x300 thumbnail version
- [x] 2.4 Verify that `class="lazy"` remains on images after the script runs and that `src` stays as the loading spinner GIF for off-screen images

## 3. Fix `image-lightbox.js` — lightbox uses full-resolution URL

- [x] 3.1 Read `data-lightbox-src` from the img tag (set by the PHP filter) for the lightbox href; fall back to stripping `-300x300` from `data-original` if `data-lightbox-src` is absent
- [x] 3.2 Set `$link.attr("href")` to the full-resolution URL, not the thumbnail URL
- [x] 3.3 Verify that clicking a gallery image opens the lightbox with the full-resolution image (2560×2560), not the 300×300 thumbnail

## 4. Validate across scenarios

- [x] 4.1 Test with a fresh (uncached) page load: lazyload loads 300x300 thumbnails as user scrolls; lightbox shows full-res on click
- [x] 4.2 Test with a cached page (full URLs in initial HTML): `image-lightbox.js` rewrites `data-original` to 300x300 but preserves `class="lazy"`; lazyload still works; lightbox uses full-res
- [x] 4.3 Test on mobile viewport / throttled network: images only load when scrolled into view (verify via Network tab)
- [x] 4.4 Test on iOS Safari (or device emulation): no crash, memory stays stable
- [x] 4.5 Test category filter + infinite scroll: new items handled correctly by all scripts

## 5. Clean up

- [x] 5.1 Run `openspec validate fix-gallery-performance-ios-crash --strict` and fix any issues
- [x] 5.2 If applicable, remove the duplicate PHP output buffer code (same logic exists in both `functions.php` and `fix-gallery-thumbnail-src.php`) to keep a single source of truth
