## 1. Title resolution helpers in `fav-button.js`

- [x] 1.1 Add a `productIdToTitle` map declaration alongside the existing `productIdToPermalink` map (near `buildPermalinkMap()`).
- [x] 1.2 In `buildPermalinkMap()`, clear `productIdToTitle` alongside `productIdToPermalink`, and in the existing per-anchor loop also record `productIdToTitle.set(fragmentRef, anchorTitle)` where `anchorTitle` is the anchor's `title` attribute (falling back to its `.item-title a` text if the attribute is empty).
- [x] 1.3 Add `resolveLightboxTitle(imgSrc, productId)` mirroring `resolveLightboxPermalink()`: (a) image-src match against `a[data-product-permalink]` returning the matched anchor's `title` / `.item-title a` text; (b) `productIdToTitle` map lookup keyed by `String(productId)`; (c) `null` when unresolved.

## 2. Wire resolved title into `convertPptToLink()`

- [x] 2.1 In `convertPptToLink()`, change the title source from `ppt.textContent.trim()` to `resolveLightboxTitle(imgSrc, productId) || ppt.textContent.trim()`.
- [x] 2.2 Update the idempotence guard so it compares the anchor's current `textContent` against the resolved title (repairing the stale text when it differs), while keeping the existing `href` comparison.
- [x] 2.3 Keep the missing-permalink fallback path (restore to plain text) unchanged so behavior when no permalink resolves is preserved.

## 3. Verify

- [x] 3.1 Confirm no logic outside `fav-button.js` changed (git diff limited to this file).
- [x] 3.2 Live-check against `palermocustomcakes.com/cake-gallery/`: open a cake, then next/prev and keyboard arrows show the `.ppt` text AND `href` tracking the displayed cake, and the title link opens the correct product page.
- [x] 3.3 Spot-check the favorites page lightbox still shows a correct, clickable title after navigation.