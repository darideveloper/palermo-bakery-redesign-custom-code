## 1. CSS — Lightbox Button Positioning and Animation

- [x] 1.1 In `product-gallery.css`, add `#pp_full_res { position: relative; }` so the button can be absolutely positioned inside it
- [x] 1.2 In `product-gallery.css`, add `#lightbox-fav-btn` rule: `position: absolute !important; bottom: 18px !important; left: 50% !important; transform: translateX(-50%) !important; top: auto !important; right: auto !important; z-index: 999999 !important;`
- [x] 1.3 In `product-gallery.css`, add the hover override: `#lightbox-fav-btn:hover { transform: translateX(-50%) scale(1.15); }` — includes `translateX(-50%)` to prevent centering from being overridden on hover
- [x] 1.4 In `product-gallery.css`, add `@keyframes heartPopLightbox { 0% { transform: translateX(-50%) scale(1); } 50% { transform: translateX(-50%) scale(1.3); } 100% { transform: translateX(-50%) scale(1); } }` — dedicated keyframe preserves centering throughout the animation
- [x] 1.5 In `product-gallery.css`, add `#lightbox-fav-btn.is-favorited { animation: heartPopLightbox 0.3s ease; }` — use the dedicated keyframe instead of the shared `heartPop`

## 2. JS — Product ID Tracking at Click Time

- [x] 2.1 In `fav-button.js`, declare `let currentLightboxProductId = null;` in the `DOMContentLoaded` scope
- [x] 2.2 In `fav-button.js`, add a capture-phase click listener on `document` that targets `a[data-rel^="prettyPhoto"]` clicks, traverses up to `.product-inner`, reads `.yith-wcwl-add-to-wishlist[data-fragment-ref]`, and stores the value in `currentLightboxProductId`

## 3. JS — Lightbox Button State Helper

- [x] 3.1 In `fav-button.js`, implement `updateLightboxFavBtn()`: reads `localStorage` for `storageKey`, checks if `currentLightboxProductId` is in the array, and sets `#lightbox-fav-btn` innerHTML to ❤️/🤍 and toggles `is-favorited` class accordingly

## 4. JS — Product ID Resolution from Image Src (Navigation)

- [x] 4.1 In `fav-button.js`, implement `getLightboxProductId(imgSrc)`: strips query params from `imgSrc`, iterates all `a[data-rel^="prettyPhoto"]` links, strips query params from each `href`, finds a match, traverses to `.product-inner > .yith-wcwl-add-to-wishlist`, and returns `dataset.fragmentRef` or `null`

## 5. JS — Lightbox Button Injection

- [x] 5.1 In `fav-button.js`, implement `injectLightboxFavBtn()`: checks `#pp_full_res` exists, checks if `#lightbox-fav-btn` already exists (if so, only calls `updateLightboxFavBtn()` and returns), otherwise creates the button element with `id="lightbox-fav-btn"`, `class="my-custom-fav-btn"`, `aria-label="Add to favorites"`, and appends it to `#pp_full_res`
- [x] 5.2 Wire the lightbox button's click handler: calls `e.preventDefault()`, `e.stopPropagation()`, `window.testToggleFav(currentLightboxProductId)`, and `updateLightboxFavBtn()`
- [x] 5.3 After injecting the button, attach a `MutationObserver` on `#pp_full_res img` watching `src` attribute changes; on change, call `getLightboxProductId(img.src)`, update `currentLightboxProductId` if a match is found, then call `updateLightboxFavBtn()`
- [x] 5.3b Also observe `#pp_full_res` with `childList: true` as a fallback in case prettyPhoto replaces the `<img>` element entirely; on any added node, re-read the new img's `src` and call `getLightboxProductId(img.src)`, update `currentLightboxProductId` if a match is found, then call `updateLightboxFavBtn()`
- [x] 5.4 At the end of `injectLightboxFavBtn()`, call `updateLightboxFavBtn()` to set the initial state

## 6. JS — Lightbox Open Detection

- [x] 6.1 In the existing capture-phase click listener (task 2.2), after setting `currentLightboxProductId`, add `setTimeout(injectLightboxFavBtn, 250)` to wait for prettyPhoto to build its DOM
- [x] 6.2 In `fav-button.js`, add a `MutationObserver` on `document.body` (shallow `childList: true, subtree: false`) as a fallback: when `.pp_pic_holder` appears in the DOM and `#lightbox-fav-btn` does not yet exist, call `injectLightboxFavBtn()`

## 7. Verification

- [x] 7.1 Open gallery page, click a cake image — verify the heart button appears centered at the bottom of the lightbox image
- [x] 7.2 Click the heart button — verify emoji flips, `heartPop` animation plays, product is saved in localStorage, gallery card heart updates
- [x] 7.3 Close and re-open the lightbox for the same cake — verify button shows ❤️ (state persisted)
- [x] 7.4 Navigate to a different cake using lightbox arrows — verify button state updates to match that cake's fav status
- [x] 7.5 Open the lightbox multiple times in a row — verify only one button is present each time (no duplication)
- [x] 7.6 Toggle fav from a gallery card while lightbox is closed — verify gallery card works normally, no regressions
- [x] 7.7 Test on mobile viewport — verify button is visible, tappable, and correctly positioned inside the full-screen lightbox
- [x] 7.8 Hover over the lightbox fav button — verify it scales without jumping position; click to favorite — verify `heartPopLightbox` animation plays without the button shifting left
