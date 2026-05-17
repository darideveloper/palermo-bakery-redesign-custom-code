## 1. CSS — Button Container & Positioning

- [x] 1.1 In `product-gallery.css`, add `#lightbox-btn-container` rule: `position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; align-items: center; z-index: 999999;` — single centered container that holds both buttons as a flex row (share left, fav right)
- [x] 1.2 In `product-gallery.css`, override `#lightbox-fav-btn` to work inside the flex container: `position: static !important; bottom: auto !important; left: auto !important; top: auto !important; right: auto !important; transform: none !important;` — resets the previous individual absolute positioning
- [x] 1.3 In `product-gallery.css`, update `#lightbox-fav-btn:hover` to `transform: scale(1.15) !important;` and update `@keyframes heartPopLightbox` to plain `scale()` values (removing the `translateX(-50%)` that was previously needed for individual positioning)
- [x] 1.4 In `product-gallery.css`, add `#lightbox-share-btn` rule matching fav button shape and size: `border-radius: 50% !important; border: none !important; background: rgba(255, 255, 255, 0.9) !important; color: rgb(102, 102, 102) !important; font-size: 20px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: rgba(0, 0, 0, 0.4) 0px 7px 20px 1px; transition: transform 0.2s ease;`
- [x] 1.5 In `product-gallery.css`, add `#lightbox-share-btn:hover` rule: `transform: scale(1.15);` with `background` and `color` explicitly kept at the same values to prevent theme hover overrides
- [x] 1.6 In `product-gallery.css`, add `#lightbox-share-toast` rule for the copy confirmation: `position: absolute; bottom: calc(100% + 10px); left: 50%; transform: translateX(-50%); background: #000; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 13px; white-space: nowrap; pointer-events: none;` — floats above the container, centered, no overlap with buttons

## 2. JS — Button Injection

- [x] 2.1 In `fav-button.js`, replace the early-return guard in `injectLightboxFavBtn()` with a single check: `if (document.getElementById("lightbox-btn-container")) { updateLightboxFavBtn(); return; }` — simpler than checking two individual buttons
- [x] 2.2 In `fav-button.js`, create `#lightbox-btn-container` div and append to `#pp_full_res` as the shared wrapper for both buttons
- [x] 2.3 In `fav-button.js`, inject the share button (left slot): `id="lightbox-share-btn"`, `class="my-custom-lightbox-btn"` (distinct class, avoids collision with fav delegation logic), `innerHTML='<i class="fa fa-share-alt"></i>'` (FA4 — confirmed from `functions.php` using `fa fa-*` syntax), appended to `btnWrapper`
- [x] 2.4 In `fav-button.js`, define `showShareToast(message)` helper inside `injectLightboxFavBtn()`: removes any existing `#lightbox-share-toast`, creates a new `<div>` with the message, appends it to `btnWrapper`, and removes it after 2 seconds
- [x] 2.5 In `fav-button.js`, wire the share button click handler: `e.preventDefault()`, `e.stopPropagation()`, guard `!currentLightboxProductId`, construct URL as `window.location.origin + "/favorite-cakes/?shared_favs=" + currentLightboxProductId`, copy via `navigator.clipboard.writeText()`, call `showShareToast("Link Copied!")` on success and `showShareToast("Copy failed")` in `.catch()`
- [x] 2.6 In `fav-button.js`, inject the fav button (right slot): same as before but appended to `btnWrapper` instead of directly to `#pp_full_res`
- [x] 2.7 In `fav-button.js`, keep MutationObservers (`attrObserver`, `childObserver`) inside the fav button creation block — they are only registered once, preventing duplicate observers if injection is called again

## 3. JS — Navigation & Re-injection Guards

- [x] 3.1 No separate URL variable is needed — the share URL is computed on-the-fly in the click handler from `currentLightboxProductId`, which is already kept up-to-date by the existing `attrObserver` and `childObserver` MutationObserver callbacks. No changes to those callbacks are required.
- [x] 3.2 Update `lightboxBodyObserver` guard to: `!document.getElementById("lightbox-btn-container")` — simpler single check replacing the previous two-button check

## 4. Verification

- [ ] 4.1 Open gallery page, click a cake image — verify share button (left) and fav button (right) appear side by side, centered at the bottom of the lightbox image
- [ ] 4.2 Click the share button — verify a black toast reading "Link Copied!" appears above the buttons for 2 seconds, then disappears; buttons remain visually unchanged
- [ ] 4.3 Paste clipboard content — verify URL is `<origin>/favorite-cakes/?shared_favs=<productId>`
- [ ] 4.4 Navigate to a different cake using lightbox arrows — verify clicking share now copies the newly displayed cake's URL
- [ ] 4.5 Open the lightbox multiple times — verify only one button container is present (no duplication)
- [ ] 4.6 Hover over the share button — verify it scales to 1.15 without position jump; background and icon color remain unchanged
- [ ] 4.7 Test on mobile viewport — verify buttons are visible, tappable, and toast appears above them correctly
