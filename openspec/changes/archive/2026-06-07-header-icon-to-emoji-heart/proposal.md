## Why

The header favorites shortcut currently displays a Font Awesome birthday cake icon (`\f1fd`). Visitors familiar with the gallery and lightbox experience see heart emoji (`❤️`/`🤍`) for the favorites toggle, creating an inconsistency between the header indicator and the on-page favorites UI. Aligning the header icon to the same emoji heart creates a cohesive visual language across the site.

## What Changes

- Replace the header `.mini-wishlist .tongle:before` Font Awesome cake glyph (`\f1fd`) with a filled red heart emoji (`❤️`)
- Add `color: #e74c3c` so the heart is always red (not toggled)
- No JavaScript changes — the header icon is a pure CSS `::before` element and is never toggled by JS

## Capabilities

### New Capabilities

- `header-fav-icon`: the header favorites shortcut icon styling — currently a CSS-only override of the YITH Wishlist `.mini-wishlist .tongle:before` pseudo-element

### Modified Capabilities

_(none — no spec-level requirement changes)_

## Impact

- **File modified**: `src/features/favorites/header-fav.css` — one `content` value and one `color` declaration
- **No breaking changes**: the icon swaps to a heart; the count badge is unaffected; all gallery/lightbox favorites behaviour remains identical
