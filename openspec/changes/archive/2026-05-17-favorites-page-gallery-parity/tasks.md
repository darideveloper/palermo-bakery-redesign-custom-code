## 0. Prerequisite — Script Loading

- [x] 0.1 `image-lightbox.js` and `fav-button.js` already load globally via the Simple Custom CSS and JS plugin — no plugin config change needed.
- [x] 0.2 prettyPhoto (`jquery.prettyPhoto.min.js`) ships with WooCommerce and is only enqueued by WC on shop/product pages. Force-enqueue it on the favorites page via `wp_enqueue_scripts` in `functions.php` (handle `prettyPhoto`, URL `plugins_url('', WC_PLUGIN_FILE) . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js'`).

## 1. PHP — Favorites Grid Markup (`functions.php`)

- [x] 1.1 In `ajax_render_favorite_products`, change the `<a>` tag `href` from `get_permalink()` to `$image_url` (applies to both own and shared items in the while loop)
- [x] 1.2 Add `data-rel="prettyPhoto[fav-gallery]"` attribute to the `<a>` tag
- [x] 1.3 Add `title="<?php echo esc_attr(get_the_title()); ?>"` attribute to the `<a>` tag
- [x] 1.4 Add `data-product-id="<?php echo esc_attr(get_the_ID()); ?>"` attribute to the `<a>` tag
- [x] 1.5 Replace the `.remove-fav-btn` (✖) button with `.my-custom-fav-btn` (❤️) in the `else` branch (own favorites only), keeping `data-product-id` and `aria-label`

## 2. PHP — Share Button (`functions.php`)

- [x] 2.1 Remove all visual inline styles from `#share-favs-page-btn` (keep only `style="display:none"`)
- [x] 2.2 Replace button inner text with `<span class="share-btn-icon">📤</span><span class="share-btn-text">Share My Favorites</span>`

## 3. JS — Expose `initLightbox` globally (`image-lightbox.js`)

- [x] 3.1 Add `window.palermoInitLightbox = initLightbox;` immediately after the `initLightbox` function definition (~line 142)

## 4. JS — Bind Lightbox and Fix ID Resolution (`fav-button.js`)

- [x] 4.1 In `renderGrid()`, inside the `.then()` callback after `listContainer.innerHTML = response.data`, add: `if (window.palermoInitLightbox && typeof jQuery !== 'undefined') window.palermoInitLightbox(jQuery(listContainer));`
- [x] 4.2 In `getLightboxProductId()`, after `if (!productBlock) continue;` replace the `continue` with a fallback: if `link.dataset.productId` exists, return it; otherwise continue — so navigation on the favorites page correctly updates `currentLightboxProductId`
- [x] 4.3 In the lightbox click listener (the `capture: true` listener that sets `currentLightboxProductId`), add a branch: if `link.closest(".product-inner")` exists use `.yith-wcwl-add-to-wishlist[data-fragment-ref]`; otherwise use `link.dataset.productId`. Guard `setTimeout(injectLightboxFavBtn, 250)` so it only fires when `currentLightboxProductId` is non-null

## 5. JS — Lightbox Fav Button Click Handler for Favorites Page (`fav-button.js`)

- [x] 5.1 In `injectLightboxFavBtn()`, inside the fav button's click handler (after `testToggleFav` and `updateLightboxFavBtn`), add: find `document.getElementById("fav-item-" + currentLightboxProductId)`; if found, fade out (opacity → 0 over 0.3s), then remove from DOM and check for empty state via `renderUserFavoritesGrid()`

## 6. JS — Heart Button Click Handler for Favorites Cards (`fav-button.js`)

- [x] 6.1 In the `.my-custom-fav-btn` click handler, after the existing `.product-inner` branch, add a `.masonry-item` branch: read `btn.dataset.productId`, call `window.testToggleFav(productId)`, fade out and remove the `.masonry-item` (opacity 0 over 0.3s, then `remove()`)
- [x] 6.2 Inside the `.masonry-item` removal callback, after `remove()`, check if localStorage favorites is now empty and call `renderUserFavoritesGrid()` if so
- [x] 6.3 Delete the now-dead `.remove-fav-btn` click handler block

## 7. JS — Share Button Fixes (`fav-button.js`)

- [x] 7.1 In `renderUserFavoritesGrid()`, change `sharePageBtn.style.display = "inline-block"` to `sharePageBtn.style.display = "inline-flex"`
- [x] 7.2 In the share button click handler, update the clipboard feedback to use spans: `sharePageBtn.innerHTML = '<span class="share-btn-icon">✅</span><span class="share-btn-text">Link Copied!</span>'`

## 8. JS — Fix Save-Shared Button Feedback (`fav-button.js`)

- [x] 8.1 In the `.save-shared-btn` click handler, replace the permanent text feedback (`saveSharedBtn.innerHTML = "❤️ Saved"; saveSharedBtn.style.color = ...`) with a fade-out + hide: set `transition: opacity 0.3s ease`, `opacity: 0`, then after 300ms set `display: none`

## 9. CSS — Favorites Page Stylesheet (`favorite-page.css`)

- [x] 9.1 Remove `.remove-fav-btn` from the combined `.remove-fav-btn, .save-shared-btn` selector (keep `.save-shared-btn` styles unchanged)
- [x] 9.2 Add `#share-favs-page-btn` CSS rule: `display: inline-flex`, `align-items: center`, `gap: 10px`, `padding: 14px 32px`, `background: #333`, `color: #fff`, `border: none`, `border-radius: 50px`, `font-size: 15px`, `font-weight: 600`, `letter-spacing: 0.4px`, `cursor: pointer`, `box-shadow: 0 6px 20px rgba(0,0,0,0.15)`, `transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease`
- [x] 9.3 Add `#share-favs-page-btn:hover` rule: `background: #1a1a1a`, `transform: translateY(-2px) scale(1.03)`, `box-shadow: 0 10px 28px rgba(0,0,0,0.22)`
- [x] 9.4 Add `#share-favs-page-btn:active` rule: `transform: translateY(0) scale(0.98)`
- [x] 9.5 Add `#share-favs-page-btn .share-btn-icon` rule: `font-size: 18px`, `line-height: 1`

## 10. Verification

- [x] 10.1 Confirm `image-lightbox.js` and prettyPhoto load on the favorites page (verified via live page source: `jquery.prettyPhoto.min.js?ver=3.1.6` is enqueued with handle `wc-prettyPhoto-js`; `palermoInitLightbox` is a function in the console; `cakeGalleryScriptLoaded` is true)
- [x] 10.2 On the favorites page, click a cake card → prettyPhoto lightbox opens with full-res image, navigation arrows, share button, and fav button (confirmed by user after deploying `functions.php` via WP File Manager)
- [x] 10.3 Inside the lightbox (opened from favorites page), the fav button shows ❤️ and clicking it: removes from favorites, fades out the matching card, decrements the header counter
- [x] 10.4 Navigate between cakes in the lightbox (prev/next arrows) → fav button state updates correctly for each cake
- [x] 10.5 On a favorites page card, click the ❤️ button directly → card fades out, item removed from localStorage
- [x] 10.6 Remove the last cake via the card heart button → empty-state message displays
- [x] 10.7 Open a shared link (`?shared_favs=...`) → click `.save-shared-btn` on a cake → button fades out and hides; "❤️ Saved" text is never visible
- [x] 10.8 The share button displays as a pill with 📤 icon and correct flex alignment; hover lifts with deeper shadow
- [x] 10.9 Click share button → URL copied; button shows "✅ Link Copied!" with icon span; reverts after 2 seconds
- [x] 10.10 Open the cake gallery page → all existing behavior unchanged (lightbox, gallery heart buttons, lightbox share+fav buttons)
- [x] 10.11 Shared section `.save-shared-btn` hides on click; gallery and counter update correctly

## 11. Deployment (post-implementation, learned during rollout)

- [x] 11.1 Deploy the updated `functions.php` to `/wp-content/themes/snsvicky/functions.php` using the **WP File Manager** plugin. The WordPress Theme File Editor writes to a deployment-shadowed path on this WP Engine site and does not reach the executing copy — File Manager writes to the actual served file.
- [x] 11.2 After PHP deploys, reset PHP OPcache by visiting `/?palermo_reset_opcache=1` while logged in as admin (one-shot snippet added to `functions.php`; remove after use). WP Engine's "Clear All Caches" does not always reset OPcache; this is the reliable workaround when portal access is unavailable.
- [x] 11.3 Clear the WordPress page cache (any plugin or WP Engine cache plugin) and hard-reload `/favorite-cakes/` in incognito.
