## Context

Enabling direct product-page access exposed a fatal: ~58% of products have an empty description, and a plugin registers a `woocommerce_product_tabs` "description" tab with **no `callback` key** when `post_content` is empty. The theme template `snsvicky/woocommerce/single-product/tabs/tabs.php:44` iterates tabs and calls `call_user_func($tab['callback'], $key, $tab)`, producing a PHP `TypeError` and an HTTP 500 WordPress fatal page. Products with any description render fine.

The deployed `wp-content/themes/snsvicky/functions.php` is byte-identical to the repo canonical `src/core/functions.php`, and the deployed `function.php`/`functions.php` contain **no** product-tab filter, so the fix belongs in our canonical file. WooCommerce core only adds the `description` tab when content exists and always with a `callback` (`wc-template-functions.php:2329-2335`), so the invalid tab originates from a plugin (likely YITH WooCommerce Catalog Mode, a brochure/catalog store).

## Goals / Non-Goals

**Goals:**
- Make all empty-description product pages return HTTP 200 and render, instead of 500.
- Add the fix in the repo-canonical `src/core/functions.php` and mirror it to the live install so local and prod match.
- Tolerate defensive edge cases (non-array filter value, uncallable callback).

**Non-Goals:**
- Not editing the theme's `tabs.php` vendor template (not tracked in repo).
- Not fixing the underlying plugin that emits the callback-less tab (out of scope; the filter sidesteps it safely).
- No database, plugin-upgrade, or data changes.

## Decisions

### Decision 1: Add a `woocommerce_product_tabs` callback-guard filter in canonical `functions.php`

**Decision:** Add a filter on `woocommerce_product_tabs` with a late priority (e.g. `200`) that:
- returns early if `$tabs` is not an array;
- loops and `unset()`s any tab where `!isset($tab['callback'])` or `!is_callable($tab['callback'])`;
- returns `$tabs`.

**Rationale:** This runs after WooCommerce's default tabs (priority ~10), drops only the invalid entry, and preserves all valid tabs. It is the minimal, safe change that prevents the theme's `call_user_func` from receiving an invalid callback. Using a late priority ensures it sees the fully-assembled tab set from all plugins.

**Alternatives considered:**
- *Edit theme `tabs.php` to guard `call_user_func`* — Rejected: the template isn't in the repo canonical source/deploy flow, and editing vendor templates is fragile against theme updates.
- *Fix the emitting plugin directly* — Rejected: root cause is uncertain (uncertain plugin), riskier, and outside our tracked code. The filter is the appropriate defense-in-depth at the known choke point.

### Decision 2: Deploy via the standard mirror workflow and update the prod mirror

**Decision:** Change `src/core/functions.php` (canonical), then mirror the identical file to the live `wp-content/themes/snsvicky/functions.php` via SFTP (the maintainer's hand-copy convention). Verify byte-identical after deploy. Because the maintainer approved it here, also update the repo-side read-only prod mirror `src/core/functions_prod.php` so canonical, mirror, and live all match. (AGENTS.md normally forbids agents editing `functions_prod.php`; this change includes explicit maintainer consent to do so.)

**Rationale:** Keeps local and prod consistent (byte-identical baseline confirmed) and reuses the established deployment model in `AGENTS.md`, with an explicit one-time exception for the prod mirror.

## Risks / Trade-offs

- [Risk] A legitimate tab with a callback registered as a non-callable string could be dropped → **Mitigation**: only tabs without a valid `callback` are removed; valid tabs (`woocommerce_product_description_tab`, `comments_template`, etc.) are callable and unaffected.
- [Risk] Non-array `$tabs` could still cause issues elsewhere → **Mitigation**: the filter returns early on non-array; tabs template already only iterates if `!empty($tabs)`.
- [Risk] Deploy drift between repo and prod → **Mitigation**: post-deploy `diff` against the canonical file (baseline already proven byte-identical).
- [Risk] The failing plugin later adds a `callback` (masking a different symptom) → **Mitigation**: out of scope; this filter remains a correct guard; the plugin root cause can be separately investigated.

## Migration Plan

- Edit `src/core/functions.php` (add filter).
- Copy the updated file to the live theme `functions.php` via SFTP; confirm byte-identical.
- Update the repo-side mirror `src/core/functions_prod.php` to the identical content (maintainer-approved).
- Verify live: empty-description products (`sesame-street-smash-cake`, `watercolor-blooms-cake`, `roblox-birthday-cake`) return 200; a working product (`tuxedo-birthday-cake-2`) still returns 200.
- **Rollback:** restore the previous live `functions.php` (backup) — client-side, instant, no data migration.

## Open Questions

- Which plugin emits the callback-less tab remains unconfirmed (suspected YITH Catalog Mode). Not required to implement the fix, but noted for a future root-cause cleanup.
- Whether the same empty-description path affects product pages on other pages (upsells/related) — the tab filter covers the tab render; related/upsell loops were already functional and are unaffected.
