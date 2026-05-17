## Context

The gallery page (`/cake-gallery/`) is the WooCommerce shop archive, configured to display all ~308 products on a single page. After the previous iOS-crash fix (archived 2026-05-12) the page still froze on iOS Safari and the tab spinner never stopped. Diagnosis with WebKit Playwright and live HTML inspection revealed three interacting causes that the previous "preserve jquery.lazyload" approach could not address:

1. **`jquery.lazyload` itself** — its scroll-event handler is attached to every `<img class="lazy">` and iterates them on every scroll/touch. On iOS that's enough to make touch-scroll stutter and eventually exceed the renderer's per-tab budget.

2. **A "Simple Custom CSS and JS" plugin entry** at HTML line ~27052 (in the deployed snapshot) declares:
   ```js
   const stripSuffix = (url) => url.replace(/-\d+x\d+(\.[a-z]+)$/i, '$1');
   ```
   and runs it on every `<img>` via a `MutationObserver` on `document.documentElement`. The observer's guard (`.js-custom-loader` exists) is defused by its own `setTimeout(loader.remove, 450)`, so it re-fires every ~450 ms on every DOM mutation. Each pass calls `img.src = stripSuffix(img.src)` — assigning to `img.src` in JS bypasses native `loading="lazy"`, triggering re-fetches even when the URL didn't change.

3. **Hanging subresources** keep the `load` event from firing: WooCommerce mini-cart fragments XHR (`?wc-ajax=get_refreshed_fragments`), Google reCAPTCHA (`recaptcha/api.js` + stylesheet + font, ~460 kB combined), WordPress emoji SVG preloads, and the Cloudflare bot-challenge probe. On iOS this manifests as a forever-spinning address bar and contributes to memory pressure that triggers the "A problem occurred" tab-kill dialog.

Additionally, `is_page('cake-gallery')` returns FALSE on this URL because WordPress's main query is for the product archive (`post-type-archive-product`), not the page. The previous proposal's hooks worked only because they used OR-ed conditions (`is_shop() || ... || is_page('cake-gallery')`). My initial dequeue hooks used `is_page()` alone and silently never ran.

## Goals / Non-Goals

- **Goals:**
  - Eliminate iOS Safari freeze and tab crash on `/cake-gallery/`
  - Stop the address-bar spinner within ~3 s of page load (load event must fire)
  - Initial network: only viewport thumbnails fetch; off-screen images fetch on scroll
  - Lightbox still opens full-resolution images
  - No regression on desktop, Android, or other product archive pages
- **Non-Goals:**
  - Server-side pagination (gallery is intentionally one-page per project conventions)
  - Replacing prettyPhoto, WPBakery, or any installed plugin
  - Disabling the "Simple Custom CSS and JS" plugin entirely (cleaner to neutralize the specific rogue entry; other entries are still in use)
  - Touching the rogue script's source in the WP admin database (deleting it there is recommended but the PHP-buffer strip is the bulletproof fix)

## Decisions

### Decision 1: Replace jquery.lazyload with native `loading="lazy"`, kill the scroll-handler at the source

Strip `class="lazy"` from gallery `<img>` tags via the PHP output buffer. The `jquery.lazyload` plugin only tracks elements matching its selector (which keys on `class="lazy"`), so removing the class makes it skip our images entirely. Inject `loading="lazy" decoding="async"` so the browser handles deferred fetching.

The same transforms are also applied in `image-lightbox.js#prepareCard` as a redundant second pass. This duplication is intentional: the JS pass handles cached or pre-PHP-buffer HTML snapshots (e.g., during a partial cache invalidation window after deploy). When PHP has already done its work, the JS calls are idempotent no-ops on `class`, `loading`, `decoding`, `srcset`, and `data-original` (same values written), and `prepareCard` skips the `src` write because the `isSpinner` check is false.

- **Alternatives considered:**
  - A: Keep the `lazy` class and patch `jquery.lazyload` to use IntersectionObserver — too invasive; touches theme code
  - B: Move all lazy-loading to a custom IntersectionObserver script — duplicates what the browser already does
  - C: Only fix it in PHP and let JS be a pure observer — viable, but the cached-HTML scenario (older copies served from edge cache) would render broken images during the cache rotation window
- **Chosen:** Native `loading="lazy"` is the simplest and most efficient option (browser support on iOS Safari has been stable since 15.4). Keep `prepareCard`'s redundant attribute updates so cached HTML still renders correctly.

### Decision 2: Defeat the rogue stripSuffix regex with a query-string sentinel

The rogue regex `/-\d+x\d+(\.[a-z]+)$/i` is anchored to end-of-string. Appending `?t=300` to thumbnail URLs and `?l=1` to lightbox URLs breaks the anchor, making the regex a no-op without changing the actual image fetched (WordPress ignores unknown query params).

- **Alternatives considered:**
  - A: Strip the rogue script entirely and only do that — viable, but the sentinel is a cheap belt-and-braces defense against any future regex-based URL-rewriter (e.g., the `replaceColor` script on the order page uses the same pattern)
  - B: Re-write the rogue script via JS — fragile (the rogue script also runs on `MutationObserver`, leading to a race)
- **Chosen:** Apply BOTH the sentinel (defensive) and the script removal (definitive). The sentinel is one regex; the strip is one regex. Together they cost no measurable time and the layered defense survives future plugin updates.

### Decision 3: Strip the rogue inline script at the HTML buffer

Detect the script by its function-declaration prefix (`const stripSuffix` / `const updateImagesAndHide`) — these strings are unique to the rogue script's source and won't accidentally match comments in our own gallery script.

- **Alternative rejected:** Matching by substring `stripSuffix` alone — our own code mentions the name in comments, which would cause our gallery script to be stripped too. (This actually happened in development; fixed by using the more specific declaration prefix.)

### Decision 4: Reusable `_palermo_is_gallery_view()` predicate

`/cake-gallery/` is the WC shop archive. `is_page('cake-gallery')` returns FALSE on it. Every new gallery-only hook MUST use the same OR-ed condition that the existing image-rewrite buffer uses:

```php
function _palermo_is_gallery_view() {
    return is_shop() || is_product_category() || is_product_tag() || is_page('cake-gallery');
}
```

This prevents the silent-no-op class of bug that wasted a debugging cycle in this round.

### Decision 5: Dequeue WC cart-fragments, strip reCAPTCHA, remove emoji on gallery views only

These are all third-party requests that block the `load` event. None are needed on a gallery page:
- `wc-cart-fragments` — refreshes the mini-cart, which is hidden on the gallery
- `recaptcha/api.js` — only needed by a Zenreach email form not shown on the gallery
- WP emoji preloads — Cloudflare often stalls these

Scope each removal to `_palermo_is_gallery_view()` so other product/account pages are unaffected.

### Decision 6: Remove `content-visibility: auto` from `.block-product-inner`

In an earlier round I added `content-visibility: auto; contain-intrinsic-size: 320px 320px;` to the cards expecting iOS to skip off-screen rendering work. Instead, iOS Safari skipped rendering and never recovered — visible symptom: only the first 2 cards rendered, the rest stayed blank even on scroll. Reverting restores correct rendering.

## Risks / Trade-offs

- **Risk:** SFTP uploads of `functions.php` don't invalidate WP Engine's PHP OPcache, so deploys can silently appear to do nothing.
  - **Mitigation:** Documented operational gotcha — re-save `functions.php` via Appearance → Theme File Editor after SFTP. WordPress's `WP_Filesystem::put_contents()` calls `opcache_invalidate()` automatically on save.
- **Risk:** The query-string sentinel changes the actual URL the browser caches, so each image now has a separate cache key from any prior cached version. First load post-deploy is a full miss.
  - **Mitigation:** One-time cost; subsequent loads cache normally. With ~12 viewport thumbnails at ~7 kB each, the cold-cache penalty is ~85 kB.
- **Risk:** The rogue-script strip regex is keyed on specific function names. If the plugin entry is renamed, our strip becomes a no-op.
  - **Mitigation:** The sentinel works regardless of the strip — the rogue script's regex becomes a no-op on our URLs even if it still executes. The strip is defense in depth.
- **Trade-off:** We are bypassing the theme's `jquery.lazyload` for the gallery only. Other archive pages that use the theme's grid will still use jquery.lazyload via the same theme markup. If they ever see the same iOS issue, we extend the PHP buffer match (it already runs on all shop/category/tag pages, so this is already true for any page hitting the same buffer).

## Migration Plan

1. Apply PHP changes to `functions.php` via SFTP, then re-save via WP Admin Theme File Editor to invalidate OPcache.
2. Apply CSS change to `product-gallery.css` (no special invalidation needed).
3. Apply JS changes to `image-lightbox.js` (inlined via "Simple Custom CSS and JS" plugin — paste through plugin admin UI).
4. Purge WP Engine page cache via dashboard.
5. Verify via `curl` that:
   - `?t=300` appears ~596 times
   - `?l=1` appears ~596 times
   - `prod_loading.gif` appears 0 times
   - `class="lazy"` on `<img>` appears 0 times
   - `recaptcha/api.js` appears 0 times
   - `const stripSuffix` appears 0 times
6. Test on real iOS Safari device (page interactive, spinner stops, no tab crash).

Rollback: re-upload the previous `functions.php` / `product-gallery.css` from git history and re-save via Theme File Editor.

## Open Questions

- The `wc-cart-fragments` script is still emitted in the deployed HTML despite `wp_dequeue_script('wc-cart-fragments')` at priority 100. The script loads with `defer`, so it does not block the load event — but the resulting `?wc-ajax=get_refreshed_fragments` XHR still fires. Should we add a `script_loader_tag` filter that returns `''` for this handle as a stronger remove, or accept the deferred load as harmless? Currently accepted; revisit if iOS regresses.
- The Cloudflare bot-challenge probe (`cdn-cgi/challenge-platform/.../jsd/oneshot/...`) cannot be removed from our side. If it keeps blocking `load` on iOS even after this proposal, the remaining lever is asking WP Engine support to relax Cloudflare's bot-management settings for this domain.
