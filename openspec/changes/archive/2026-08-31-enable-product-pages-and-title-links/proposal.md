## Why

The cake gallery currently blocks all access to single WooCommerce product pages via a `template_redirect` hook that bounces any `is_product()` request to the shop archive. This was set up when the site was a pure visual portfolio, but the bakery now needs product pages reachable so clients can share direct permalinks to specific cakes and the site can be indexed as a real catalogue.

At the same time, gallery visitors currently have no way to reach the product page for a specific cake from the lightbox view. The gallery grid is image-only by design — the product name appears only inside the prettyPhoto lightbox, as the auto-generated `.ppt` element (e.g. `Tuxedo Birthday Cake`). Converting that `.ppt` element into a clickable link to the corresponding product permalink gives visitors a direct path to the full product page from the lightbox, while the click-image-to-open-lightbox behavior is preserved.

## What Changes

- **Remove** the `dari_developer_disable_product_pages` `template_redirect` hook and the `woocommerce_redirect_single_search_result` filter that prevents single-product access. Applied in both `src/core/functions.php` and the mirrored `src/core/functions_prod.php`. **BREAKING** for the previous "single-product pages are unreachable" guarantee.
- **Re-enable** direct access to WooCommerce single product pages with the theme's default rendering. No content or style changes to the single-product page itself.
- **Convert** the prettyPhoto lightbox title element (`.ppt`, the auto-generated text prettyPhoto emits for the current image) into an anchor element whose `href` is the WooCommerce product permalink for the cake currently shown and whose `target` is `"_blank"` (opens in a new tab). The anchor is styled to match the original `.ppt` appearance via a CSS rule for `a.ppt` inside the lightbox (needed because WooCommerce's `prettyPhoto.css` only targets `div.ppt`, making the converted `<a>` invisible on the dark background). If the permalink cannot be resolved through any available path, the title remains as plain text.
- **Build** a `productId → permalink` map on `/cake-gallery/` only, populated from the new `data-product-permalink` attributes on the gallery card anchors. The map is empty on other pages.
- **Update** the PHP image-rewrite output buffer in `src/core/functions.php` to emit a `data-product-permalink` attribute on the card's `a.product-image` element. The product ID for each card is read from the YITH wishlist element's `data-fragment-ref` inside the same card, and the permalink URL is built with `get_permalink($post->ID)`. Cards without a YITH element are skipped. The same buffer change is mirrored in `src/core/functions_prod.php`.
- **Update** `docs/client-readme.md` so the existing narrative (which describes the "no single-product pages, no buying chrome" stance) no longer contradicts the change. Multiple sections need targeted edits — the Cake Gallery intro, the Human-Language README, the developer code sample, the Decision Log, the Custom Lightbox Human-Language README, the Custom Lightbox Maintenance Guide, and the "Before major updates" YITH note. The implementer should keep the bakery-team tone intact and only update the technical truth.
- **Update** (optional) `openspec/project.md` so the project-level context doc reflects the new state: the gallery remains gallery-only, but single-product pages are reachable.

## Capabilities

### New Capabilities
- `lightbox-title-permalink-link`: The prettyPhoto lightbox title (`.ppt`) SHALL be rendered as an anchor element whose `href` is the WooCommerce product permalink for the cake currently shown. The link is shown on every lightbox, regardless of which page the lightbox was opened from, as long as the product permalink can be resolved.
- `single-product-page-access`: Direct requests to WooCommerce single product URLs SHALL render the theme's standard single-product template instead of redirecting to the shop archive.

### Modified Capabilities
- `gallery-optimization`: The image-rewrite output buffer in `src/core/functions.php` SHALL additionally emit `data-product-permalink` on each gallery card's `<a.product-image>` so JS can resolve the product permalink without a network call.

## Impact

**Code (edited):**
- `src/core/functions.php` — remove the two redirect hooks; extend the image-rewrite buffer to emit `data-product-permalink` on `a.product-image` (YITH-sourced product ID, `get_permalink` URL); replace lazy `.*?` YITH regex with unrolled loop + null guards; add `a.ppt` CSS via `wp_add_inline_style`
- `src/core/functions_prod.php` — mirror the same removals and buffer change
- `src/features/favorites/fav-button.js` — convert `.ppt` to `<a target="_blank">` after prettyPhoto renders it; update the anchor on every lightbox image change; build the `productId → permalink` map; resolve permalinks via image-src match first, then map lookup, then plain text
- `src/features/lightbox/modal-custom.css` — repo source of truth for the `a.ppt` CSS rule (positioning + title link styling)
- `docs/client-readme.md` — 8 targeted edits across the Cake Gallery feature group, the Custom Lightbox feature group, and the Before-major-updates section (full list in `tasks.md` Section 5)
- `openspec/project.md` — optional 2-line rewording of the project-level context

**Code (not edited):**
- The single-product WooCommerce template — renders as the theme emits
- `src/features/lightbox/image-lightbox.js` — prettyPhoto config + lightbox close-redirect + swipe-nav (unchanged)
- `src/features/gallery/product-gallery.css`
- `src/core/layout/layout-and-hidden-elements.css`
- YITH wishlist integration — used only as a product-ID source for the buffer; no other changes
- `src/features/lightbox/modal-custom.css` — repo source of truth; the rule is loaded on prod via the "Simple Custom CSS and JS" plugin entry `2068.css` (positioning + title link)

**Out of scope:**
- Styling the single-product page (theme default is intentional)
- Adding a price, "Add to cart" UI, or any purchase flow changes
- Adding a title link to the gallery card grid (the grid is image-only by design; the title lives in the lightbox)
- Adding a separate "View product" button in the lightbox (the existing `.ppt` title becomes the link instead)
- SEO/canonical URL changes
