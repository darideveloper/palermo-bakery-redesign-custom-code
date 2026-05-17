## Context

The cake gallery (WooCommerce product grid) uses `image-lightbox.js` + `fav-button.js` to deliver a lightbox (prettyPhoto), heart-toggle buttons on cards, and a fav button injected inside the lightbox. The favorites page renders cards via AJAX (`ajax_render_favorite_products`) into `.cake-masonry-grid` and already shares the same card visual styles, but is missing three interactive features.

Key constraint: `image-lightbox.js` runs inside a jQuery IIFE with a `window.cakeGalleryScriptLoaded` guard and its `initLightbox()` function is private. `fav-button.js` runs on every page including the favorites page, so it is the right integration point for the favorites-side lightbox wiring. Custom scripts (`image-lightbox.js`, `fav-button.js`, `favorite-page.css`) are managed by the **Simple Custom CSS and JS** plugin and already load globally — no plugin configuration change is needed. prettyPhoto, however, is **not** a custom script: it ships with WooCommerce (`/wp-content/plugins/woocommerce/assets/js/prettyPhoto/jquery.prettyPhoto.min.js`) and WooCommerce only enqueues it on shop/product views. On a regular WP page with a shortcode (the favorites page), WC enqueues the prettyPhoto CSS but not the JS — so `$.fn.prettyPhoto` is undefined and `initLightbox()`'s guard silently no-ops. The fix is a PHP `wp_enqueue_scripts` hook that force-enqueues WC's bundled prettyPhoto on the favorites page.

The current live `fav-button.js` injects a **two-button container** (`#lightbox-btn-container`) into `#pp_full_res`: a share button (`#lightbox-share-btn`) on the left and the fav button (`#lightbox-fav-btn`) on the right. The duplicate-injection guard checks for `#lightbox-btn-container`, not `#lightbox-fav-btn`. All corresponding styles (`#lightbox-btn-container`, `#lightbox-share-btn`, `#lightbox-fav-btn`) already exist in `product-gallery.css` and apply globally.

The favorites grid is populated asynchronously — HTML is injected into `#favorite-cakes-list` after a `fetch()` call resolves. Any lightbox binding must happen inside the `.then()` callback, not at page load.

## Goals / Non-Goals

**Goals:**
- prettyPhoto lightbox opens on favorites page card click with full navigation, close-button, and custom fav button inside.
- Heart button (`.my-custom-fav-btn` ❤️) replaces the ✖ remove button on favorites cards; clicking it toggles favorite state and fades out the card.
- Lightbox fav button correctly identifies and toggles the product when opened from the favorites page.
- Share button visually matches the pill-style category filter, with icon and hover effects.
- Zero regressions on the gallery page.

**Non-Goals:**
- AOS entrance animations on favorites cards (separate concern).
- Thumbnail/lazy-load sentinel logic for favorites images (images are already served at `large` size from WordPress).
- Adding a category filter to the favorites page.
- Changing the shared-section (`.save-shared-btn`) behavior.

## Decisions

### Decision 1: Expose `initLightbox` globally rather than duplicating prettyPhoto options

**Choice**: Add `window.palermoInitLightbox = initLightbox;` in `image-lightbox.js` after the function definition (line ~142).

**Rationale**: The prettyPhoto options object (`PRETTYPHOTO_OPTIONS`) inside `image-lightbox.js` includes a `changepicturecallback` that resizes the content container. Duplicating this in `fav-button.js` would create drift risk. A single exported reference keeps both pages in sync with zero duplication.

**Alternative considered**: Call `$.fn.prettyPhoto` directly in `fav-button.js` with a local options object — rejected because it duplicates config and diverges over time.

### Decision 2: Move the lightbox href to the image URL in PHP (not JS post-processing)

**Choice**: In `ajax_render_favorite_products`, set `href="$image_url"` and add `data-rel`, `title`, `data-product-id` directly on the `<a>` tag in PHP.

**Rationale**: The gallery's `prepareCard()` strips `-300x300` from a lazy-loaded thumbnail to derive the full-res URL — necessary there because the gallery serves animated-GIF spinner placeholders. The favorites grid already receives `'large'` size images from `wp_get_attachment_image_url`, so no src processing is needed. PHP is the cleanest place to set the correct href since the full URL is already available.

**Alternative considered**: Post-process the rendered HTML in `fav-button.js` after `renderGrid()` (read `img.src`, set `a.href`) — rejected because it adds fragile JS string manipulation when the correct value is already available in PHP.

### Decision 3: Reuse `.my-custom-fav-btn` class for the favorites card button

**Choice**: Replace `.remove-fav-btn` in PHP with `.my-custom-fav-btn` + `data-product-id`. Update the click handler in `fav-button.js` to branch on context (`.masonry-item` vs `.product-inner`).

**Rationale**: `.my-custom-fav-btn` already has all required styles (size, shape, position, animation). Adding a new class and duplicating styles would be unnecessary. The click handler already has a branch structure; adding one more branch is minimal and explicit.

**Alternative considered**: Keep `.remove-fav-btn` and style it to look like the heart button — rejected because it misrepresents semantics (the button is a toggle, not a remove action) and doesn't allow shared state rendering via `updateUI()`.

### Decision 4: Product ID resolution via `data-product-id` on the `<a>` link — applies to both click capture AND navigation

**Choice**: Read `link.dataset.productId` from the `<a>` tag for the favorites page in two places: (a) the lightbox click listener that sets `currentLightboxProductId` on open, and (b) `getLightboxProductId(imgSrc)` which the `MutationObserver` inside `injectLightboxFavBtn()` calls on every image-src change (i.e., prev/next navigation).

**Rationale**: `getLightboxProductId` currently skips any link whose `closest(".product-inner")` is null — meaning favorites-page links always return null, and `currentLightboxProductId` is never updated during navigation. Since the `<a>` tag already carries `data-product-id`, the fix is a one-line fallback inside `getLightboxProductId`: if no `.product-inner` parent exists, return `link.dataset.productId`. This keeps navigation tracking correct with no additional data.

### Decision 5: Share button styles move to CSS file, inline styles removed

**Choice**: Strip all inline styles from the PHP `<button>`, add only `style="display:none"` (needed for JS-controlled visibility), and place all visual rules in `favorite-page.css` under `#share-favs-page-btn`.

**Rationale**: Inline styles cannot be overridden without `!important` and are harder to maintain. A CSS class/ID rule is the standard approach and easier to theme.

## Risks / Trade-offs

- **prettyPhoto not loaded on favorites page** → This was a hard prerequisite and turned out to be the main blocker during rollout. The fix is **not** in the Simple Custom CSS and JS plugin (prettyPhoto is not listed there); it is in PHP. A `wp_enqueue_scripts` action in `functions.php`, gated by `is_page('favorite-cakes') || is_page(12)`, force-enqueues `jquery.prettyPhoto.min.js` and `prettyPhoto.css` from the WooCommerce plugin URL (`plugins_url('', WC_PLUGIN_FILE) . '/assets/js/prettyPhoto/...`). The `initLightbox()` guard (`if (!$.fn.prettyPhoto) return`) silently no-ops if prettyPhoto is absent, which is why the symptom was "the lightbox just doesn't open" with no console errors.
- **AJAX render timing** → `renderGrid()` is called after localStorage/server sync which adds latency. prettyPhoto is bound inside `.then()` so the timing is correct — but if the user clicks a card before AJAX completes, no lightbox will open. This is acceptable: the grid is empty until AJAX resolves.
- **`updateUI()` on favorites cards** → The existing `updateUI()` only sets heart state for `.product-inner .my-custom-fav-btn` cards. Favorites-page heart buttons (inside `.masonry-item`) will always show ❤️ (set by PHP) and do not need `updateUI()` to manage them — they are removed from the DOM when un-favorited. No `updateUI()` change is required.
- **`injectLightboxFavBtn()` click handler must also trigger card removal** → When the lightbox fav button is clicked to un-favorite from the favorites page, `testToggleFav()` is called but the corresponding `.masonry-item` is not removed. The handler must check if `document.getElementById("fav-item-" + currentLightboxProductId)` exists and apply the same fade-out/remove as the card button click path.
- **`sharePageBtn.style.display` must match CSS** → `renderUserFavoritesGrid()` shows the button via `sharePageBtn.style.display = "inline-block"`. After the CSS change to `inline-flex`, this inline style overrides the rule and breaks the icon/text flex alignment. The JS must be updated to `"inline-flex"`.
- **Clipboard feedback must preserve span structure** → The "✅ Link Copied!" feedback (`sharePageBtn.innerHTML = "✅ Link Copied!"`) replaces the icon+text spans with plain text during the 2-second window. The feedback HTML should use the same span structure for visual consistency.
- **`.save-shared-btn` "❤️ Saved" overflow** → The current handler permanently sets button text to "❤️ Saved" inside a 40×40px circle, causing overflow. Replace with a fade-out + hide (`display: none`) after saving — consistent with how other buttons disappear in this codebase.

## Migration Plan

No database changes. No API changes. Deployment is a file update — upload modified `functions.php`, `image-lightbox.js`, `fav-button.js`, `favorite-page.css`.

**WP Engine deployment notes (learned during rollout):**
1. **Edit PHP via the WP File Manager plugin, not via the WordPress Theme File Editor.** On this site, the Theme File Editor writes to a deployment-shadowed path that does not affect the running site. The File Manager writes to the actual served file at `/wp-content/themes/snsvicky/functions.php`.
2. **Reset PHP OPcache after PHP changes.** WP Engine's "Clear All Caches" button does not always reset OPcache. Until the user's WP Engine portal access is restored, use a temporary admin-gated one-shot snippet (`?palermo_reset_opcache=1` → `opcache_reset()`) added to `functions.php`. Remove the snippet after use.
3. **Clear page cache and hard-reload in incognito** to verify.
4. JS/CSS files are inlined by the Simple Custom CSS and JS plugin — for those, update the entries in WP admin's Custom CSS & JS UI.

Rollback: revert the four files to their previous versions.
