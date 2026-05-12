# Tasks: Optimize Gallery Image Resolution

## Implementation

- [x] Refine image transformation logic in `image-lightbox.js` to strip `-scaled` <!-- id: 0 -->
- [x] Update `initCakeGallery` to apply 300x300 suffix to both `src` and `data-original` <!-- id: 1 -->
- [x] Implement `srcset` removal to ensure thumbnail enforcement <!-- id: 2 -->
- [x] Wrap all scripts in safe jQuery IIFEs to fix `$ is not a function` errors <!-- id: 3 -->
- [x] Verify processing guards prevent double-processing or redundant URL manipulation <!-- id: 4 -->
- [x] Remove `lazy` class from images to prevent theme's `jquery.lazyload.js` from reverting `src` back to high-res <!-- id: 10 -->
- [x] Reorder operations: remove `lazy` class BEFORE changing `src`/attributes to prevent MutationObserver race conditions <!-- id: 11 -->

## Verification

- [x] **Visual Test (Grid)**: Confirm grid images appear correctly (no 404s for -scaled images) <!-- id: 5 -->
- [x] **Visual Test (Lightbox)**: Confirm lightbox still opens images in full resolution <!-- id: 6 -->
- [x] **Console Check**: Verify no `$ is not a function` or 404 errors remain <!-- id: 7 -->
- [x] **Performance Test (Mobile)**: Verify improved load stability on iOS devices <!-- id: 8 -->
- [x] **Integration Test**: Confirm Infinite Scroll and Category Filters still correctly apply the resolution optimization to new items <!-- id: 9 -->
