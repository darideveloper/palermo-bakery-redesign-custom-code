## Context

On `/cake-gallery/`, the prettyPhoto lightbox shows a product title (`a.ppt`) that links to the WooCommerce product page. `fav-button.js` builds the link via `convertPptToLink()`, which resolves the `href` from the current lightbox image (`resolveLightboxPermalink()`) but takes the **title text** from the existing `.ppt` node's `textContent`.

Root cause of the stale title (confirmed live against `palermocustomcakes.com/cake-gallery/`):

- prettyPhoto (WooCommerce's `jquery.prettyPhoto.min.js`) caches the original `div.ppt` node once at init (`var $ppt = $(".ppt")`).
- On every navigation, `open()` writes the correct caption to that node via `$ppt.html(pp_titles[set_position])`.
- `convertPptToLink()` **replaces** that exact `div.ppt` with a new `a.ppt` on first open, detaching the original node.
- Result: prettyPhoto keeps writing to a detached node (invisible), while the visible `a.ppt` never receives the update. The `href` is still correct because it's resolved fresh from the image on each src change, but the text stays on the initially-opened cake's name.

This violates the existing `lightbox-title-permalink-link` spec requirement "Title link updates on lightbox navigation". The source title is already available on the matching `a.product-image` anchor's `title` attribute (set by `image-lightbox.js` from the card's `.item-title a`, and by PHP `get_the_title()` on the favorites page).

## Goals / Non-Goals

**Goals:**
- Make the `.ppt` anchor's visible **text** update to the currently displayed cake during prev/next and keyboard navigation, matching the already-correct `href`.
- Resolve the title from the **same authoritative source** as the permalink (matching anchor's `title` / `.item-title a`, with a `productId → title` map fallback) so text and link stay consistent.
- Fix only `fav-button.js`; keep the existing permalink resolution, fav/share buttons, CSS styling, and lightbox lifecycle untouched.

**Non-Goals:**
- No changes to prettyPhoto itself or to `image-lightbox.js`.
- No PHP/server changes.
- No new AJAX endpoints.
- No changes to permalink resolution logic — only the title source.

## Decisions

### Decision 1: Resolve the title from the matching `a.product-image` anchor, not from `.ppt` text

**Decision:** Add `resolveLightboxTitle(imgSrc, productId)` that mirrors `resolveLightboxPermalink()`: scan `a[data-product-permalink]` for one whose image `href` matches the current lightbox `imgSrc`; within that match, return its `title` attribute, falling back to its `.item-title a` text if the attribute is empty; if no image-src match resolves a title, fall back to the parallel `productId → title` map lookup; else return `null`. This mirrors task 1.3 and Risk 1 (never emit an empty title).

**Rationale:** The identifier that ties a card to its product is the same for both title and permalink (the YITH `data-fragment-ref`/anchor). Reusing the match gives text and link perfect alignment — they can never refer to different cakes. It also fixes both the gallery (`/cake-gallery/`) and the favorites-page ("image-src match" fallback) paths in one place.

**Alternatives considered:**
- *Read prettyPhoto's correct title from its detached node* — Not viable; the node is out of the DOM and its content is not exposed anywhere reachable after replacement.
- *Wrap the existing `.ppt` text node in an anchor instead of replacing the whole element `div` → `a`* — Would preserve prettyPhoto's `$ppt` reference, but prettyPhoto's `$ppt` points to the outer element it needs to keep as `div` (it re-sets its width); replacing `div.ppt` was an intentional design in the existing feature, and CSS already targets `a.ppt`. Sticking to the current element-lifetime avoids regressing the established styling/behavior.
- *Let prettyPhoto update a hidden text node and copy text out* — Over-engineered and fragile; the anchor's `title` attribute is already authoritative and simpler.

### Decision 2: Build a parallel `productId → title` map in `buildPermalinkMap()`

**Decision:** Inside the existing per-anchor loop of `buildPermalinkMap()` (which already runs only on `/cake-gallery/`), also record `productIdToTitle.set(fragmentRef, anchorTitle)` where `anchorTitle` is the anchor's `title` (or `.item-title a` text). Clear both maps at the start.

**Rationale:** Mirrors the existing, working map pattern; the map is the fallback when the image-src match misses (e.g. duplicate images across products), and keeping it beside the permalink map means a single scan and a single rebuild trigger (the existing `MutationObserver`s that call `buildPermalinkMap()` automatically refresh both).

**Alternatives considered:** A separate scan — Rejected; duplicates work, adds a second DOM pass, and risks the two maps diverging between rebuilds.

### Decision 3: Keep `convertPptToLink()` as the single update point

**Decision:** Only change the title-string assignment inside `convertPptToLink()` from `ppt.textContent.trim()` to `resolveLightboxTitle(imgSrc, productId) || ppt.textContent.trim()`, and adjust the idempotence guard to compare against the resolved title. All existing call sites (first open, img-src observer, child observer) keep working unchanged.

**Rationale:** The observers already call `convertPptToLink()` on every navigation; making the title source authoritative there is the minimal, targeted fix. The guard keeps the call idempotent (no DOM churn) while now correctly detecting a stale title to repair.

## Risks / Trade-offs

- [Risk] A matched anchor lacking a `title` attribute → **Mitigation**: fall back to `.item-title a` text, then to the `productId → title` map, then leave text unchanged (never an empty title).
- [Risk] Title mismatch between anchor `title` and the product name due to server-side aliasing → **Mitigation**: the anchor `title` is the same source `image-lightbox.js` uses for the `<img alt>` that prettyPhoto would have shown; it is the intended name.
- [Risk] Regression to the favorites page path → **Mitigation**: the favorites card includes both `title` and `data-product-permalink`, so the shared image-src match path works identically; manual verification on the favorites page is required.

## Migration Plan

- Edit `src/features/favorites/fav-button.js` only.
- Mirror the updated file to the live WordPress install (maintainer hand-copies per project convention).
- Verify on production: open a cake on `/cake-gallery/`, use next/prev and keyboard arrows, confirm the `a.ppt` text and `href` track the displayed cake and that clicking the title opens the correct product page.
- **Rollback:** revert `fav-button.js` to the previous version and re-copy to prod (feature is fully client-side; no data migration).

## Open Questions

- None blocking. If a product has multiple images with identical `src`, the image-src match may resolve the same title for both; this matches existing permalink behavior and is out of scope.