## Why

When a user opens a cake lightbox on `/cake-gallery/` and navigates between cakes with the prev/next (or keyboard) arrows, the title link's **`href` updates to the newly displayed cake but the title **text** stays stale** (e.g. still reads "She Said Yes! Engagement Cake" while showing a different cake). This makes the link look broken and misrepresents the displayed product. The root cause is a detached-DOM race: prettyPhoto caches the original `div.ppt` node once at init and writes the correct caption to it on every navigation, while `fav-button.js` replaced that very node with an `a.ppt` and then reads the stale visible text as its title source.

## What Changes

- Introduce a `resolveLightboxTitle()` helper that resolves the cake name from the **same authoritative source as the permalink** (the matching `a.product-image` anchor's `title` / `.item-title a` text, with a `productId → title` map fallback), instead of reading the stale `.ppt` text.
- Extend `buildPermalinkMap()` in `fav-button.js` to also build a `productId → title` map from the same anchors, mirroring the existing permalink-map logic.
- Update `convertPptToLink()` to use the resolved title as the source of truth for the anchor text, so the caption always matches the currently displayed cake across arrow/keyboard navigation.
- Fixing this aligns the implementation with the existing `lightbox-title-permalink-link` spec's "Title link updates on lightbox navigation" requirement, which was previously unmet.

**No breaking changes.** The existing permalink resolution, fav/share buttons, styling, and lightbox lifecycle are untouched. No PHP change is required.

## Capabilities

### New Capabilities
<!-- none -->

### Modified Capabilities
- `lightbox-title-permalink-link`: The "Title link updates on lightbox navigation" requirement currently states the `.ppt` anchor text and `href` must both update on navigation, but the text does not update in practice. This change fixes the title-text source so the requirement is actually satisfied. The spec will add explicit scenarios asserting that the title text updates to match the displayed cake (resolved from the same source as the permalink), and that it does not fall back to the stale `.ppt` text.

## Impact

- `src/features/favorites/fav-button.js` — primary change: title resolution + map + conversion logic.
- Behavior verified live against `palermocustomcakes.com/cake-gallery/` (title currently stale after arrow navigation).
- No change to `src/core/functions.php`, `image-lightbox.js`, or CSS. No data or API changes.