## Context

The cake gallery at `/cake-gallery/` is a WooCommerce shop archive rendered by the SNS Vicky theme. Until now, every single product page has been hard-blocked: a `template_redirect` hook (`dari_developer_disable_product_pages` in `src/core/functions.php:1736-1742`) redirects any `is_product()` request to the shop archive, and `woocommerce_redirect_single_search_result` is forced to `__return_false` (line 1730). The single-product page is therefore unreachable for clients, search engines, and direct permalinks.

The gallery grid itself is intentionally image-only — there is no title, price, or "Add to cart" UI on the card. The product name appears only inside the prettyPhoto lightbox, as the auto-generated `.ppt` element (a `<div class="ppt">` that prettyPhoto creates from each lightbox link's `title` attribute, e.g. `Tuxedo Birthday Cake`). Visitors currently have no way to reach the product page for any specific cake.

This change re-enables product pages and turns the lightbox title into a clickable link to the corresponding product permalink. The product page itself is left untouched (theme default rendering) and the gallery grid is left untouched — only the lightbox title element changes behavior.

## Goals / Non-Goals

**Goals:**
- Re-enable direct access to WooCommerce single product pages
- Convert the prettyPhoto `.ppt` title element into a permalink link, so visitors in the lightbox can navigate to the product page
- Source the product permalink from a new `data-product-permalink` attribute on the gallery card anchor, populated by the existing PHP output buffer (no new AJAX endpoint)
- Keep all existing gallery, favorites, lightbox, category-filter, fav-button, and share-button behavior unchanged

**Non-Goals:**
- Restyling the single-product page (theme default is intentional)
- Adding or modifying any "Add to cart" / price / checkout UI
- Adding a title or any other UI to the gallery card grid (user-confirmed during planning: the grid is image-only by design; there is no title element in the grid to convert — the only title is the `.ppt` in the lightbox)
- Adding a separate "View product" button in the lightbox button row (the existing `.ppt` title becomes the link instead, keeping the lightbox button row at its current two buttons: fav and share)
- SEO / canonical URL changes
- Any change to the favorites, share, auth, or category-filter features

## Decisions

### Decision 1: Remove the redirect hooks outright, do not gate them

**Choice:** Delete the `dari_developer_disable_product_pages` function, its `template_redirect` registration, and the `woocommerce_redirect_single_search_result` filter from both `src/core/functions.php` and `src/core/functions_prod.php`.

**Rationale:** The previous "redirect everyone" behavior was a single boolean decision; re-enabling access is the opposite. There is no need for an admin-only carve-out — the user wants product pages reachable for everyone, including SEO crawlers.

**Alternatives considered:**
- *Gate by role* (`if (is_product() && !current_user_can('manage_options'))`): rejected because the requirement is for product pages to be reachable for all visitors.
- *Leave the hooks in place but disable them via a flag*: rejected because dead code is harder to reason about and the user explicitly asked for the redirect removed.

### Decision 2: Use the lightbox title, not a new button, for the permalink

**Choice:** Convert the existing `.ppt` text inside the lightbox into an anchor element pointing to the product permalink. Do not add a third button to the existing `#lightbox-btn-container` row.

**Rationale:** The user explicitly identified `.ppt` as the title element to convert. The lightbox button row already has fav and share buttons; adding a third "View product" button would be UI noise when the title text itself can carry the same affordance. The title is the most natural place for a "go to product page" action because it identifies the cake.

**Alternatives considered:**
- *Add a third button to the lightbox button row* (the original design): rejected after the user clarified the title element to convert is `.ppt` inside the lightbox, not on the card.
- *Convert the title AND add the button*: rejected as redundant.

### Decision 3: Source the permalink from a new `data-product-permalink` attribute

**Choice:** Extend the existing PHP image-rewrite output buffer at `src/core/functions.php:1799-1867` so that, after it rewrites the `<img>` attributes, a second pass inside the same `<img>` match callback adds a `data-product-permalink="<absolute URL>"` attribute to the enclosing `a.product-image` tag. The URL is built with `get_permalink($post->ID)`, where `$post->ID` is the WooCommerce product ID for the card. The product ID is sourced from the YITH wishlist element's `data-fragment-ref` inside the same card (the same source the existing fav-button JS uses). If YITH is missing or has no `data-fragment-ref` on a given card, the buffer skips that card and the rest of the page is unaffected.

**Rationale:** The output buffer is already the single authority that touches gallery image markup, so this is the natural place to add the attribute. Keeping the new pass inside the existing `<img>` callback means the two passes always agree on which image each anchor belongs to — no separate top-level regex to maintain. Client-side JS can read the attribute without any network call. The same buffer change is mirrored in `src/core/functions_prod.php`.

**Alternatives considered:**
- *New AJAX endpoint to resolve permalinks*: rejected because it's strictly slower and adds a new failure mode.
- *Read the permalink from the YITH wishlist element directly*: rejected because the YITH element doesn't currently carry the permalink, and coupling to YITH's markup adds a hidden dependency. (YITH is used here only as a product-ID lookup, which it already serves.)
- *Resolve permalinks in PHP and inline them into the page as JS globals*: rejected because `data-*` attributes are more localized and idiomatic.
- *Separate top-level regex for `<a.product-image>`*: rejected because two regexes have to be kept in sync; nesting the new pass inside the existing `<img>` match keeps them in lockstep.

### Decision 4: Convert `.ppt` after prettyPhoto renders it, hook into the existing lifecycle

**Choice:** In `src/features/favorites/fav-button.js`, after prettyPhoto has injected the `.ppt` element into the DOM, replace its text contents with an anchor element (`<a target="_blank" href="<permalink>">`) whose text is the product name. The anchor is styled with a CSS rule for `a.ppt` inside the lightbox (via `wp_add_inline_style('woocommerce_prettyPhoto_css')` in `src/core/functions.php`), because WooCommerce's `prettyPhoto.css` only targets `div.ppt` and the converted `<a>` would otherwise inherit default link styling (grey, inline, invisible on the dark background). The implementation hooks into the same MutationObserver-based lifecycle the existing fav and share buttons already use (a `MutationObserver` on the `<img>` inside `#pp_full_res` and another on `document.body` watching for `.pp_pic_holder`); the discovery that prettyPhoto events are not exposed and that `fav-button.js` is where the lightbox machinery actually lives is recorded as an implementation note in `tasks.md` 1.3.

**Rationale:** prettyPhoto creates `.ppt` lazily when the lightbox opens. Any earlier injection (e.g. on `DOMContentLoaded`) would miss it. The existing code already hooks into prettyPhoto's open/change events to inject the fav and share buttons; reusing that lifecycle keeps the title-link update consistent with the other buttons' update-on-navigation behavior. Opening in a new tab (`target="_blank"`) keeps the gallery reachable for visitors who want to compare multiple cakes without losing their place.

**Alternatives considered:**
- *Plain `<a>` (same-tab navigation)*: rejected because visitors frequently compare multiple cakes from the lightbox; same-tab navigation loses their gallery scroll position.
- *CSS-only solution*: rejected because the title needs an `href` value, not just a style change.
- *PHP-side prettyPhoto configuration to make the title an anchor*: rejected because prettyPhoto doesn't support that out of the box; manipulating the DOM in JS is the documented approach.

### Decision 5: Resolve the permalink via image-src match first, map lookup second, plain text last

**Choice:** When the lightbox opens, the JS SHALL resolve the permalink for the currently displayed cake using this fallback order:
1. **Image-src match:** compare the current lightbox image's `src` to the `src` of every `a.product-image[data-product-permalink]` on the page; use that permalink.
2. **Map lookup:** look up the current product ID in a `productId → permalink` map built during page load. The map is populated only on the main `/cake-gallery/` page (where `.product-inner` cards carry `data-product-permalink`); on other pages the map is empty and step 1 is the only path that can succeed.
3. **Plain text:** if neither path resolves a permalink, leave the `.ppt` element as plain text (do not emit an anchor with empty or `#` href).

**Rationale:** Image-src matching is the most direct signal of which card the lightbox is showing. The map is a fallback for cases where the same product is opened from a context that doesn't render `.product-inner` (e.g. the favorites page, where the markup is different). The plain-text fallback is the graceful-degradation path that prevents broken anchors.

**Alternatives considered:**
- *Click-origin `<a>` only*: rejected because prettyPhoto's callbacks don't always have a reference to the original `<a>` that fired the lightbox.
- *AJAX lookup as a third fallback*: rejected because it adds latency and a new failure mode for a UI that already has a clear degradation path.
- *Build the map on every page*: rejected because non-`/cake-gallery/` pages have no `a.product-image[data-product-permalink]` to scan, so the scan would be wasted work.

## Risks / Trade-offs

- **prettyPhoto `.ppt` element timing** → The `.ppt` element is created by prettyPhoto when the lightbox opens, not on `DOMContentLoaded`. If the JS runs before prettyPhoto fires, the conversion will be missed. **Mitigation:** hook the conversion into the same prettyPhoto events the fav and share buttons already use (`pp_open` / `pp_change`). Tasks 1.3 and the implementation step will confirm the exact hook points.

- **Selector drift on `.ppt`** → The element selector is a prettyPhoto-internal class. If the theme or prettyPhoto version is updated, the class could change. **Mitigation:** if `.ppt` is no longer the correct selector, the spec scenario for "missing permalink leaves the title as plain text" is the graceful-degradation fallback. Implementation will verify the live selector before relying on it. Additionally, WooCommerce's `prettyPhoto.css` only targets `div.ppt`; converting the element to `<a>` makes the selector stop matching, so an explicit `a.ppt` CSS rule is required. The repo source of truth is `src/features/lightbox/modal-custom.css`; the rule is loaded on prod via the "Simple Custom CSS and JS" plugin entry `2068.css`.

- **Output buffer extending into anchor rewrite is more invasive than the current image-only rewrite** → The current buffer matches and rewrites `<img>` tags; extending it to also touch the enclosing `<a>` means the buffer's regex now has to match the parent context. **Mitigation:** keep the existing `<img>` regex untouched; add a second, narrower pass that only updates the parent anchor of each matched `<img>`. If a card has no enclosing anchor, the pass is a no-op for that card.

- **Both `functions.php` and `functions_prod.php` must be edited identically** → Drift between the two files would cause prod and dev to behave differently. **Mitigation:** tasks.md lists both files in the same change step, the implementer edits both at the same time, and the verification step checks both files have identical diffs. Manual sync is consistent with the project's existing convention.

- **Re-enabling product pages exposes the theme's default single-product template to the public** → if the theme renders anything unsuitable (e.g. an "Add to cart" button conflicting with the no-commerce stance), it becomes visible to clients. **Mitigation:** this is acceptable per the user's explicit instruction ("don't do content or style changes"). The non-goals section records this as intentional.

## Migration Plan

1. Apply the change in a single PR/commit. No database migration, no data backfill.
2. The change is purely additive from a content standpoint: it removes a redirect, adds a `data-product-permalink` attribute, and adds one DOM manipulation in the lightbox lifecycle.
3. Rollback is the inverse: re-add the two redirect hooks in both PHP files; remove the `data-product-permalink` emit from the buffer; remove the `.ppt` conversion. No persisted state to roll back.
4. No feature flag is needed — the change is small, easily reverted, and the user has already approved the final state in the proposal.

## Open Questions

- **Exact MutationObserver hook points for the title conversion** — to be confirmed against the existing `fav-button.js` during implementation.
- **Whether `.ppt` is created once per lightbox open or once per image change** — affects whether the conversion needs to be re-run on prettyPhoto's "change" event. Tasks 1.3 and the implementation step will confirm.
- **Whether the favorites page renders cards with `a.product-image`** — if it does, the `data-product-permalink` lookup works there too; if not, the JS falls back to the productId → permalink map built on the gallery page.
