## Why

The ❤️/🤍 emoji characters used for the custom favorite button render inconsistently on iOS Safari — the emoji glyph is oversized, misaligned, or clipped because Apple Color Emoji ignores `font-size`/`line-height`, and Safari has a known bug with `display: flex` on `<button>` elements. A CSS-only fix failed to resolve the issue.

## What Changes

- Replace raw emoji characters (`❤️`, `🤍`) with inline SVG icons in all favorite buttons
- Add CSS rules to size the SVG icons consistently (20×20px card buttons, 28×28px lightbox)
- Remove the now-unnecessary `@supports` iOS Safari hack and `overflow: hidden` workaround
- Keep button layout (absolute positioned, 40×40px circle, top-right corner) identical

## Capabilities

### New Capabilities
- `fav-button-svg-icon`: Inline SVG heart icons used in `.my-custom-fav-btn` and `.save-shared-btn` across the favorites system (favorites page grid, gallery injection, lightbox)

### Modified Capabilities
_(none — no spec-level behavior changes, only implementation detail)_

## Impact

| Area | Impact |
|------|--------|
| `src/core/functions.php:2106-2112` | Replace `❤️` / `🤍` with inline SVG in `ajax_render_favorite_products()` |
| `src/features/favorites/fav-button.js` | Replace `innerHTML = "❤️"` / `"🤍"` with SVG in `injectLightboxFavBtn()`, `updateUI()`, `injectHeartButtons()` |
| `src/features/favorites/favorite-page.css` | Remove `@supports` + `overflow:hidden` bandaid; add `.my-custom-fav-btn svg` sizing |
| `src/features/gallery/product-gallery.css` | Add `#lightbox-fav-btn svg` sizing for the 56×56px lightbox button |
