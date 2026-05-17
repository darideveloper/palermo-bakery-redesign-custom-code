## Why

The favorites page shares the same visual card style as the cake gallery, but lacks the interactive features that make the gallery compelling: there is no lightbox, the top-right card button is a plain remove icon (✖) instead of the familiar heart toggle, and the share button is an unstyled dark rectangle. Users who discover the favorites page after using the gallery encounter a noticeably degraded experience. This change brings the favorites page to functional and visual parity with the gallery.

## What Changes

- **Lightbox on favorites page**: Clicking a cake card in the favorites grid now opens the same prettyPhoto lightbox used in the cake gallery, displaying the full-resolution image with navigation arrows, the custom close button, the share button, and the injected fav button at the bottom center. Lightbox navigation (prev/next) correctly updates the fav button state for each cake.
- **Heart button replaces remove button**: The ✖ remove button on each favorites card is replaced with the same `.my-custom-fav-btn` heart button (❤️) used in the gallery. Clicking it removes the cake from favorites and fades the card out — identical to the old remove behavior, but using the shared heart UI. Clicking the lightbox fav button while viewing a favorites-page cake also fades out the corresponding card.
- **Lightbox fav button works from favorites page**: The existing fav button injected inside the prettyPhoto lightbox now correctly identifies the product when the lightbox is opened from the favorites page (previously it could only resolve the product ID from the gallery's `.yith-wcwl-add-to-wishlist` element, which does not exist on the favorites page). This fix covers both initial open and navigation between images.
- **Improved share button**: The "Share My Favorites" button is restyled to match the site's pill-button aesthetic (matching the category filter), with an icon, hover lift effect, and smooth transitions — replacing the current unstyled inline-CSS implementation.
- **Fix "❤️ Saved" text overflow on shared cakes**: When a user saves a shared cake to their favorites, the `.save-shared-btn` currently displays "❤️ Saved" permanently as overflowing text inside the 40×40px circle. The button is now hidden (fade out) after saving instead.

## Capabilities

### New Capabilities

- `favorites-lightbox`: prettyPhoto lightbox is initialized and functional on the favorites page — image links point to full-res URLs, `data-rel` is set, prettyPhoto is bound after each AJAX grid render, and `getLightboxProductId()` supports `.masonry-item` context for navigation.
- `favorites-heart-button`: The heart fav button (`.my-custom-fav-btn`) replaces the remove button (`.remove-fav-btn`) on user-owned favorite cards; clicking it (or the lightbox fav button while viewing that cake) toggles the favorite state and animates the card out when removed.
- `favorites-share-button`: The "Share My Favorites" button is styled as a pill with an icon and hover interactions, consistent with the site's established button design language.
- `favorites-shared-save-feedback`: When a user saves a shared cake, the `.save-shared-btn` fades out and hides instead of showing permanent overflow text.

### Modified Capabilities

- `lightbox-fav-button`: The lightbox fav button requirement is extended — it must now correctly resolve the displayed product's ID and toggle state when the lightbox is opened from the favorites page (`.masonry-item` context), not only from the cake gallery (`.product-inner` / `.yith-wcwl-add-to-wishlist` context). This includes navigation between images and the `injectLightboxFavBtn()` click handler triggering card removal on the favorites page.

## Impact

- **`functions.php`**: `ajax_render_favorite_products` — `<a>` tag href changes from product permalink to image URL; `data-rel`, `title`, `data-product-id` attributes added; `.remove-fav-btn` replaced with `.my-custom-fav-btn`; share button inline styles stripped and icon spans added.
- **`image-lightbox.js`**: `initLightbox` exposed globally as `window.palermoInitLightbox` so `fav-button.js` can call it after AJAX renders.
- **`fav-button.js`**: `renderGrid()` calls `window.palermoInitLightbox` after injection; lightbox click listener adds `data-product-id` fallback; `.my-custom-fav-btn` click handler adds `.masonry-item` branch; dead `.remove-fav-btn` block removed.
- **`favorite-page.css`**: `.remove-fav-btn` removed from combined selector; `#share-favs-page-btn` CSS section added.
- **`fav-button.js`** (additional): `getLightboxProductId()` updated to resolve product ID from `data-product-id` on `.masonry-item` links; `injectLightboxFavBtn()` click handler triggers masonry card fade-out when on favorites page; `.save-shared-btn` handler changed to fade out and hide button after saving; `sharePageBtn.style.display` corrected to `"inline-flex"`; clipboard feedback HTML updated to use icon+text spans.
- **`functions.php` — script loading**: A new `wp_enqueue_scripts` hook is added that force-enqueues WooCommerce's bundled prettyPhoto (`jquery.prettyPhoto.min.js` + `prettyPhoto.css`) on the favorites page (page slug `favorite-cakes` or page ID `12`). WooCommerce only enqueues prettyPhoto on shop/product views by default; without this hook, `image-lightbox.js` cannot bind because `$.fn.prettyPhoto` is undefined on the favorites page. `image-lightbox.js` and `fav-button.js` already load globally via the Simple Custom CSS and JS plugin and require no additional configuration.
- **Deployment caveats (WP Engine)**: On this install, the WordPress Theme File Editor writes to a deployment-shadowed path and does not affect the running site; the deployed `functions.php` must be edited via the **WP File Manager** plugin (`/wp-content/themes/snsvicky/functions.php`). After PHP changes, PHP OPcache must be reset — WP Engine's "Clear All Caches" does not always reset OPcache. A temporary admin-only `?palermo_reset_opcache=1` snippet is the documented workaround when portal access is unavailable.
- No new dependencies, no database schema changes, no breaking changes to the gallery page.
