## 1. PHP — Remove thumbnail enforcement from output buffer

- [x] 1.1 Remove `woocommerce_get_image_size_shop_catalog` filter in `src/core/functions.php` (lines 1192-1198)
- [x] 1.2 Rewrite the `ob_start` callback in `src/core/functions.php` (lines 1248-1317) to set `src` and `data-original` to the full-resolution URL (with `?t=300` sentinel) instead of building a `-300x300` thumbnail URL
- [x] 1.3 Remove the `$is_optimized` / `$thumb_base` / `$full_base` distinction — use the full-res URL for both grid src and data-lightbox-src
- [x] 1.4 Verify `data-lightbox-src` still uses `?l=1` sentinel and `src`/`data-original` still use `?t=300` sentinel
- [x] 1.5 Verify `loading="lazy"`, `decoding="async"`, `srcset` stripping, and `lazy` class removal remain intact

## 2. JavaScript — Remove thumbnail enforcement guard

- [x] 2.1 Remove `enforceThumb()`, `enforceAllThumbs()`, `attachSrcGuard()`, `srcGuard` MutationObserver, and the `THUMB_SUFFIX`/`SENTINEL` constants from `src/features/lightbox/image-lightbox.js` (lines 6-46)
- [x] 2.2 Remove all calls to `enforceAllThumbs()` and `attachSrcGuard()` (lines 199, 210-213)
- [x] 2.3 Simplify `prepareCard()` in `src/features/lightbox/image-lightbox.js` (lines 77-130) to use the full-res URL for both grid `src` and lightbox `href` — remove the `isOptimized` / `thumbnailSrc` branching

## 3. Verify gallery layout and appearance

- [x] 3.1 Open the gallery page and visually confirm images render at full resolution without distortion
- [x] 3.2 Verify the lightbox opens with full-resolution images (no `-300x300` artifact)
- [x] 3.3 Check category filter navigation preserves full-res images
- [x] 3.4 Test on mobile viewport — confirm images load lazily and no iOS Safari crash
- [x] 3.5 Test on a slow connection — confirm lazy-loading works and the page doesn't hang

## 4. Clean up and documentation

- [x] 4.1 Update documentation references to "thumbnail enforcement" in `docs/features.md` and `docs/client-readme.md`
- [x] 4.2 Check for syntax issues (package.json has no lint script; code changes verified manually)
