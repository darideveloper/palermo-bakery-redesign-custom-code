## Why

The gallery fav (heart) button only appears on product cards — when a user opens the prettyPhoto lightbox to view a cake image in full size, there is no way to favorite it without closing the lightbox first. This creates friction in the user's discovery flow and leads to missed favorites.

## What Changes

- A heart button is injected inside the prettyPhoto lightbox, centered at the bottom of the displayed image
- The button reuses the existing `.my-custom-fav-btn` class and all current behaviors: localStorage toggle, WordPress AJAX sync for logged-in users, ❤️/🤍 emoji state, and `heartPopLightbox` animation (a dedicated keyframe that preserves centering)
- The button state is kept in sync when the user navigates between images inside the lightbox (next/prev arrows)
- All existing gallery card fav buttons continue to work independently alongside the lightbox button
- The button appears in all prettyPhoto lightbox instances; it is fully functional when triggered from a gallery product card

## Capabilities

### New Capabilities
- `lightbox-fav-button`: Fav button injected inside prettyPhoto lightbox, centered at the bottom of the image, with full toggle/sync/animation behavior and navigation-aware state updates

### Modified Capabilities
- (none — existing gallery card fav behavior is unchanged)

## Impact

- **`fav-button.js`**: New logic added (lightbox button injection, product ID tracking, MutationObserver for navigation)
- **`product-gallery.css`**: New CSS rules for lightbox button positioning (`#lightbox-fav-btn`, `#pp_full_res`), hover transform fix, and dedicated `heartPopLightbox` keyframe
- No changes to `image-lightbox.js`, `favorite-page.css`, `functions.php`, or any other file
- No new dependencies introduced
- Affects all pages where prettyPhoto lightbox is active (product gallery pages)
