## Why

When direct access to WooCommerce product pages was enabled, a latent defect surfaced: ~181 of 312 products (58%) have an **empty product description**, and on those pages the theme fatals with HTTP 500. A plugin adds a `woocommerce_product_tabs` "description" tab **without a `callback` key** when `post_content` is empty, and the theme template `snsvicky/woocommerce/single-product/tabs/tabs.php:44` calls `call_user_func($tab['callback'], …)`, throwing `Uncaught TypeError: call_user_func(): Argument #1 ($callback) must be a valid callback` and producing a WordPress fatal-error page. Products with any description render fine (200). This affects every empty-description product's permalink, including lightbox title links we ship to the product page.

## What Changes

- Add a `woocommerce_product_tabs` filter in `src/core/functions.php` that removes any product tab lacking a valid `callback` (an invalid "description" tab with an undefined/uncallable callback), so the theme template never executes an invalid callback.
- The filter SHALL be defensive: tolerate a non-array `$tabs` value, and only `unset()` tabs whose `callback` is missing or not callable. Valid tabs (description with content, additional information, reviews) SHALL pass through unchanged.
- Deploy the same change to the live install (`wp-content/themes/snsvicky/functions.php`) so local repo and prod behave identically. Per explicit maintainer decision, also update the repo-side prod mirror `src/core/functions_prod.php` in the same change so all three (canonical `src/core/functions.php`, mirror `src/core/functions_prod.php`, live theme file) match. This is a **fix** to the `single-product-page-access` capability: empty-description product pages must return HTTP 200 instead of 500.

**No breaking changes.** Products that already render correctly are unaffected; only the invalid callback-less tab is dropped.

## Capabilities

### New Capabilities
<!-- none -->

### Modified Capabilities
- `single-product-page-access`: The capability's "Direct single-product URLs render the product page" requirement is extended to guarantee the page renders **HTTP 200 without a fatal** for **all** valid products, including those with an **empty description**. Currently the requirement only asserts HTTP 200/rendered template, which empty-description products violate. A new requirement ("Product pages with an empty description render without error") is added with scenarios covering empty-description products, non-array tab filters, and unchanged valid tabs.

## Impact

- `src/core/functions.php` — canonical change: add the `woocommerce_product_tabs` callback-guard filter.
- Live install `wp-content/themes/snsvicky/functions.php` — mirror the canonical change (deploy step, per project convention / maintainer hand-copy; verified byte-identical baseline).
- `src/core/functions_prod.php` — repo-side prod mirror; explicitly updated in this change (maintainer approved agents editing it here) to keep all copies identical.
- No database, plugin, or theme-template changes. Root-cause plugin (emitting the callback-less tab) is intentionally left untouched; the filter safely sidesteps it.
- Verified against live: empty-description products (e.g. `sesame-street-smash-cake`, `watercolor-blooms-cake`, `roblox-birthday-cake`, `rangers-hockey-cake`) currently 500; working products (e.g. `tuxedo-birthday-cake-2`) 200.
