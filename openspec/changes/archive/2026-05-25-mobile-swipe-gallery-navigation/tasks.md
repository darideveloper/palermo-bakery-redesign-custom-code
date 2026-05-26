## 1. Preparation and Setup

- [x] 1.1 Identify the best injection point in `src/features/lightbox/image-lightbox.js` for touch event listeners.
- [x] 1.2 Confirm target selectors for navigation buttons (`.pp_next`, `.pp_previous`) and interactive exclusion targets (`#lightbox-btn-container`).
- [x] 1.3 Add `touch-action: pan-y` to the lightbox container styles in `src/features/gallery/product-gallery.css`.

## 2. Core Implementation

- [x] 2.1 Implement `touchstart` listener on `document` with `useCapture: true` to capture initial touch coordinates and timestamp.
- [x] 2.2 Implement `touchend` listener on `document` with `useCapture: true` to calculate movement deltas (dX, dY).
- [x] 2.3 Implement axis detection logic (`Math.abs(dX) > Math.abs(dY)`) to distinguish horizontal swipes from vertical scrolls.
- [x] 2.4 Implement the 50px threshold check for swipe intent.
- [x] 2.5 Add safety checks to ignore swipes originating from interactive buttons using `.closest('#lightbox-btn-container')`.
- [x] 2.6 Link swipe directions to button triggers (Swipe Left -> `.pp_next`.click(), Swipe Right -> `.pp_previous`.click()).

## 3. Verification and Cleanup

- [x] 3.1 Verify swipe functionality on mobile devices (or mobile simulation).
- [x] 3.2 Ensure vertical scrolling remains smooth and unaffected.
- [x] 3.3 Confirm that swipe logic only executes when the lightbox is visible (`.pp_pic_holder` exists in DOM).
- [x] 3.4 Verify that swiping over the "Favorite" and "Share" buttons does NOT trigger navigation.
