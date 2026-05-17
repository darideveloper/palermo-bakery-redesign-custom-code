## 1. Refactor Lightbox Initialization

- [x] 1.1 Remove the global `prettyPhotoInitialized` flag from `image-lightbox.js` to allow multiple initialization passes.
- [x] 1.2 Rename `initPrettyPhotoOnce` to a generic `initLightbox` function.
- [x] 1.3 Update the `initLightbox` selector to `a[data-rel^='prettyPhoto']:not(.pp-bound)` to ensure we only bind to new elements.
- [x] 1.4 Add logic inside `initLightbox` to immediately add the `.pp-bound` class to the selected anchors after the `prettyPhoto()` call to prevent re-binding.

## 2. Integrate with Chunked Processing

- [x] 2.1 Update the `processCards` function to trigger `initLightbox` after the synchronous first batch is prepared.
- [x] 2.2 Modify the `step` function inside `processCardsChunked` to trigger `initLightbox` at the end of every chunk (every 30 cards), using a scoped selector to minimize DOM traversal.
- [x] 2.3 Ensure the `requestAnimationFrame` yield happens *after* the chunk is both prepared and bound to the lightbox.

## 3. Verify Infinite Scroll Support

- [ ] 3.1 Verify that `refreshGalleryDebounced` correctly triggers the updated `initLightbox` via its `processCards($scope)` call.
- [ ] 3.2 Test that clicking images appended via infinite scroll opens the `prettyPhoto` lightbox correctly.

## 4. Quality Assurance

- [ ] 4.1 Confirm that no "double-binding" occurs (clicking once should only open one lightbox).
- [ ] 4.2 Verify that the iOS performance fix (no animated GIF decoding, yielding to main thread) remains fully effective.
