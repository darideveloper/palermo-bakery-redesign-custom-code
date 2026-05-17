## Context

The site uses prettyPhoto (jQuery plugin) as its lightbox. When a gallery image is clicked, prettyPhoto dynamically creates a `.pp_pic_holder.pp_default` overlay in `<body>` containing `#pp_full_res > img` with the full-resolution image. The existing fav system (`fav-button.js`) injects `.my-custom-fav-btn` buttons into `.item-img-info` inside `.product-inner` cards — it has no awareness of the lightbox DOM. Product IDs are stored as `data-fragment-ref` on hidden `.yith-wcwl-add-to-wishlist` elements inside each `.product-inner`.

## Goals / Non-Goals

**Goals:**
- Inject a heart button inside the prettyPhoto lightbox, centered at the bottom of the displayed image
- Button reflects the correct ❤️/🤍 state based on current localStorage favorites
- Clicking the button toggles the product's fav state (localStorage + WordPress AJAX sync if logged in)
- Button state updates correctly when the user navigates between images using lightbox prev/next arrows
- All existing gallery card fav buttons continue to work unchanged
- Button appears in all prettyPhoto lightbox instances; fully functional when triggered from a `.product-inner` gallery card

**Non-Goals:**
- Supporting other lightbox libraries (Fancybox, Magnific Popup, etc.) — only prettyPhoto is in use
- Changing `image-lightbox.js` — all fav logic stays in `fav-button.js`
- Adding fav behavior to the separate custom popup form (`custom-popup-form.js`)

## Decisions

### 1. Capture product ID at click time, not after prettyPhoto opens

**Decision:** Listen for clicks on `a[data-rel^="prettyPhoto"]` in capture phase. At click time, traverse up to `.product-inner` and read the product ID from `.yith-wcwl-add-to-wishlist[data-fragment-ref]`, storing it in `currentLightboxProductId`. Inject the button via `setTimeout(..., 250)` to wait for prettyPhoto to build its DOM.

**Alternative considered:** Read the product ID by matching `#pp_full_res img[src]` against gallery link `href` values after prettyPhoto opens. Rejected because `src` may include transformed URLs (with `?l=1` suffix) that require string manipulation and could be fragile if URL formats change.

**Why chosen:** The product card is guaranteed to be in the DOM at click time; reading `data-fragment-ref` directly is reliable and zero-cost.

### 2. Detect navigation via MutationObserver on `#pp_full_res img[src]`

**Decision:** After injecting the button, attach a `MutationObserver` to `#pp_full_res img` watching for `src` attribute changes. When src changes, match it (stripping query params) against all `a[data-rel^="prettyPhoto"]` href values in the DOM to find the new product ID.

**Alternative considered:** Override `changepicturecallback` in `image-lightbox.js` to fire a custom event. Rejected to keep fav logic entirely in `fav-button.js` and avoid coupling the two files.

**Why chosen:** MutationObserver is native, zero-dependency, and correctly fires whenever prettyPhoto swaps the image src — including keyboard navigation and arrow clicks.

### 3. Button uses `id="lightbox-fav-btn"` + class `.my-custom-fav-btn`

**Decision:** The lightbox button uses the shared `.my-custom-fav-btn` class (inherits animation, hover styles) but also `id="lightbox-fav-btn"` for stable targeting and CSS position overrides.

**Why:** `updateUI()` (gallery cards) traverses via `.product-inner` and will simply skip the lightbox button — no conflict. The lightbox button is updated by its own `updateLightboxFavBtn()` helper.

### 4. CSS: absolute positioning inside `#pp_full_res`

**Decision:** Set `#pp_full_res { position: relative }` and position the button with `bottom: 18px; left: 50%; transform: translateX(-50%)`. Override `top`/`right` from the shared `.my-custom-fav-btn` rule with `!important`. Add explicit overrides for hover (`translateX(-50%) scale(1.15)`) and a dedicated `@keyframes heartPopLightbox` that includes `translateX(-50%)` in every step to prevent the centering transform from being overridden during animation or hover.

**Why:** `#pp_full_res` is the direct image wrapper — positioning relative to it ensures the button always sits at the bottom of the image regardless of lightbox size or viewport. The transform overrides are required because CSS `transform` is a single property: a bare `scale(...)` on hover or in a keyframe completely replaces the `translateX(-50%)` centering, causing the button to jump left.

**CSS rules added to `product-gallery.css`** (where all other prettyPhoto styles live):
```css
#pp_full_res { position: relative; }

#lightbox-fav-btn {
  position: absolute !important;
  bottom: 18px !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  top: auto !important;
  right: auto !important;
  z-index: 999999 !important;
}

#lightbox-fav-btn:hover {
  transform: translateX(-50%) scale(1.15);
}

@keyframes heartPopLightbox {
  0%   { transform: translateX(-50%) scale(1); }
  50%  { transform: translateX(-50%) scale(1.3); }
  100% { transform: translateX(-50%) scale(1); }
}

#lightbox-fav-btn.is-favorited {
  animation: heartPopLightbox 0.3s ease;
}
```

## Risks / Trade-offs

- **prettyPhoto DOM delay** → The 250ms `setTimeout` before injection is a heuristic. On very slow connections prettyPhoto may not have rendered yet. Mitigation: also attach a `MutationObserver` on `document.body` (shallow `childList`) as a fallback that calls `injectLightboxFavBtn` when `.pp_pic_holder` appears.
- **Multiple observers** → Opening the lightbox many times must not stack observers. Mitigation: the injection function is idempotent (checks `document.getElementById("lightbox-fav-btn")` before creating); the `MutationObserver` on `#pp_full_res img` is created only once per lightbox open.
- **URL matching for navigation** → If prettyPhoto mutates the `src` to an unexpected format, `getLightboxProductId` may return `null`. Mitigation: if `null`, the button stays visible but in a neutral 🤍 state — it never breaks, just silently fails to update.
- **Mobile tap targets** → The button must remain tappable (≥40px) on small screens where the lightbox is full-screen. Existing `.my-custom-fav-btn` sizing (40×40px) is sufficient; no extra work needed.
- **prettyPhoto navigation method** → The `MutationObserver` targets `#pp_full_res img[src]`. This relies on prettyPhoto mutating the existing `<img>` src (confirmed behavior in prettyPhoto 3.x). If a future upgrade replaces the `<img>` element entirely, the observer becomes orphaned. Mitigation: additionally observe `#pp_full_res` for `childList` changes as a fallback.

## Migration Plan

- Changes are purely additive (new JS logic + new CSS rules)
- No server-side changes; no database migrations
- Rollback: revert the additions to `fav-button.js` and `product-gallery.css`
- No feature flag needed — the button only renders when a lightbox is open

## Open Questions

- (none — requirements are fully defined)
