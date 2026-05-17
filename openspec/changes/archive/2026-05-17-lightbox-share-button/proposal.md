## Why

The recently added lightbox fav button allows users to favorite cakes directly from the prettyPhoto modal. However, users also want to share individual cake images with friends - something that was only possible from the favorites page. Adding a share button to the lightbox enables sharing any cake directly from the modal, improving the discovery flow and social sharing capabilities.

## What Changes

- A `#lightbox-btn-container` flex div is injected into `#pp_full_res`, holding the share button (left) and fav button (right) as a centered row at the bottom of the lightbox image
- A Font Awesome share icon (`fa fa-share-alt`, FA4) is injected as a fully rounded, white-background button matching the fav button's shape and size (40×40px, `border-radius: 50%`, icon color `rgb(102,102,102)`)
- Clicking the share button copies a shared favorites link (`/favorite-cakes/?shared_favs=<productId>`) to the clipboard — reusing the same URL format as the existing favorites page share feature, but scoped to a single cake
- A "Link Copied!" toast notification (black background, white text) appears above the buttons for 2 seconds after a successful copy, without affecting the button itself
- The share URL updates automatically when navigating between images in the lightbox (next/prev arrows, keyboard), since it is computed on-the-fly from `currentLightboxProductId`
- The buttons only appear when the lightbox is opened from a gallery product card (`.product-inner` context)

## Capabilities

### New Capabilities
- `lightbox-share-button`: Share button injected inside prettyPhoto lightbox, positioned to the left of the fav button, with click-to-copy shared-favorites URL behavior (`/favorite-cakes/?shared_favs=<productId>`) and navigation-aware updates

### Modified Capabilities
- (none — existing fav button behavior is unchanged; the lightbox-fav-button spec is unaffected)

## Impact

- **`fav-button.js`**: Extend `injectLightboxFavBtn()` to create `#lightbox-btn-container`, inject the share button (left) and fav button (right) into it, add `showShareToast()` helper, and simplify the re-injection guard to check for the container
- **`product-gallery.css`**: New CSS rules for `#lightbox-btn-container` (flex, centered), `#lightbox-share-btn` (40×40px rounded, white bg, gray icon), `#lightbox-share-toast` (black bg, white text, above the buttons), and updated `#lightbox-fav-btn` rules to work inside the flex container
- No changes to `favorite-page.css`, `functions.php`, or any other file
- No new dependencies (Font Awesome is already loaded in the project)
- Affects all pages where prettyPhoto lightbox is active (product gallery pages)