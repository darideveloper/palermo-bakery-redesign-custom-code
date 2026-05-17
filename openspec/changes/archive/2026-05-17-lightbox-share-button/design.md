## Context

The site already has a share feature on the favorites page (`/favorite-cakes/`) that copies the user's full favorites list as a URL parameter (`?shared_favs=id1,id2,id3`). The recent implementation added a fav button to the prettyPhoto lightbox at the bottom center of the image. Now we need to add a share button next to it so users can share individual cakes directly from the lightbox.

The existing lightbox fav button implementation:
- Injects button into `#pp_full_res` (prettyPhoto's image container) via `injectLightboxFavBtn()`
- Was individually centered via `bottom: 18px; left: 50%; transform: translateX(-50%)` — now repositioned inside `#lightbox-btn-container` (see Decision 1)
- Reuses `currentLightboxProductId` variable (already set on lightbox open)
- Uses MutationObserver for navigation (arrow/keyboard)

Font Awesome is already loaded in the project, so we can use `.fa-share-nodes` (free tier).

## Goals / Non-Goals

**Goals:**
- Inject a Font Awesome share icon inside the prettyPhoto lightbox, positioned to the left of the fav button
- Both buttons centered at the bottom of the displayed image
- Clicking the share button copies a shared-favorites link (`/favorite-cakes/?shared_favs=<productId>`) to clipboard with visual feedback
- Share button state updates correctly when navigating between images in the lightbox
- Share button inherits consistent styling with the fav button (same position, box-shadow, hover effects)

**Non-Goals:**
- Adding native share dialog (Web Share API) - simpler copy-to-clipboard approach matches existing share behavior
- Supporting other lightbox libraries — only prettyPhoto is in use
- Changing the existing share functionality on the favorites page
- Adding social media integration (Facebook, Twitter, etc.)

## Decisions

### 1. Use a shared flex container to center both buttons together

**Decision:** A `#lightbox-btn-container` div is positioned absolutely at the bottom center of `#pp_full_res` (`left: 50%; transform: translateX(-50%)`). Both buttons sit inside it as a flex row (`display: flex; gap: 8px`), share on the left and fav on the right. The fav button's individual absolute positioning is reset to `position: static` inside the container.

**Alternative considered:** Keep both buttons individually absolutely positioned using different `translateX` offsets (share at `translateX(-150%)`, fav at `translateX(-50%)`). Rejected after implementation because buttons did not visually align correctly and the approach is fragile — offset values depend on fixed button widths with no natural centering relationship between them.

**Why chosen:** The flex container approach is mathematically correct and self-adjusting. Both buttons are naturally centered as a unit regardless of their sizes, and adding or removing a button in the future doesn't require recalculating offsets.

### 2. Reuse existing product ID tracking and share URL format

**Decision:** Use the existing `currentLightboxProductId` (already captured and kept up-to-date by the fav button's MutationObserver logic). Construct the share URL on click as `window.location.origin + "/favorite-cakes/?shared_favs=" + currentLightboxProductId` — the same format used by the favorites page share button.

**Alternative considered:** Read the product permalink from the gallery link `href`. Rejected because `a[data-rel^="prettyPhoto"]` links point to full-size image URLs (not product pages) — that is how `getLightboxProductId()` works (it matches `link.href` against `img.src`). Querying product permalinks via AJAX was also rejected as unnecessary complexity.

**Why chosen:** The `?shared_favs=` URL format is already implemented and fully handled by the favorites page. Using a single product ID produces a valid shared-favorites link that opens the favorites page with just that one cake rendered — no new server logic needed.

### 3. Use Font Awesome 4 share icon (`fa fa-share-alt`)

**Decision:** Use `<i class="fa fa-share-alt"></i>` for the share button icon.

**Alternative considered:** `fas fa-share-nodes` (FA6) and `fas fa-share-alt` (FA5). Both rejected after verifying the theme's actual FA version — `functions.php` loads `font-awesome.min.css` and uses `fa fa-facebook` / `fa fa-twitter` class syntax, which is Font Awesome 4. The `share-alt` icon is the correct FA4 share icon.

**Why chosen:** Matches the FA version already loaded by the theme; no additional assets needed.

### 4. Single function handles both button injection

**Decision:** Extend the existing `injectLightboxFavBtn()` function to also inject the share button, rather than creating a separate function.

**Alternative considered:** Create separate `injectLightboxShareBtn()` function. Rejected because both buttons share the same container, same product ID context, and same lifecycle (injected together on lightbox open).

**Why chosen:** Keeps code DRY — single injection point, single MutationObserver setup, easier to maintain.

### 5. Toast notification instead of button innerHTML swap for copy feedback

**Decision:** Show copy feedback as a separate `#lightbox-share-toast` div appended to `#lightbox-btn-container`, positioned above the buttons via `bottom: calc(100% + 10px)`. The toast auto-removes after 2 seconds. The share button's innerHTML is never modified.

**Alternative considered:** Swap the share button's `innerHTML` to "Link Copied!" and restore it after 2 seconds (the pattern used by the existing favorites page share button). Rejected because the share button is circular (40×40px) — the text overflows the button bounds, renders with the button's transparent-background style, and visually overlaps both buttons in an unreadable way.

**Why chosen:** The toast approach keeps the button visually unchanged during the feedback, places the message in a readable position above the button pair, and follows standard UX patterns for clipboard confirmation.

## Risks / Trade-offs

- **Share button not visible on small images** → The button is positioned at `bottom: 18px`. On very small images, the button may overlap the image content. Mitigation: The 18px offset is the same as the existing fav button, which has been acceptable.
- **Product ID not available** → If `currentLightboxProductId` is null at click time (lightbox opened outside a `.product-inner` context), the constructed URL would be malformed. Mitigation: Guard the click handler — if `!currentLightboxProductId`, do nothing or show a silent fallback.
- **Mobile tap targets** → The button must remain tappable (≥40px) on small screens. Existing button sizing (40×40px) is sufficient; no extra work needed.
- **URL copying fails in some browsers** → `navigator.clipboard.writeText()` requires secure context (HTTPS). If it fails, show fallback message. Mitigation: Add `.catch()` to show "Copy failed" message.

## Migration Plan

- Changes are purely additive (new JS logic + new CSS rules)
- No server-side changes; no database migrations
- Rollback: revert the additions to `fav-button.js` and `product-gallery.css`
- No feature flag needed — the button only renders when a lightbox is open

## Open Questions

- (none — requirements are fully defined)