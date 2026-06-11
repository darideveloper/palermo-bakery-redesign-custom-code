## Why

The "Favorites Board" tooltip on the header heart icon uses a centered positioning strategy (`left: 50% / translateX(-50%)`) combined with `white-space: nowrap`. On tablet and mobile screens, the icon sits near the right edge of the viewport, causing the tooltip to overflow outside the visible area to the right. Desktop behavior is correct and must be preserved.

## What Changes

- On tablet and mobile breakpoints (`≤ 991px`), the tooltip `::after` pseudo-element is anchored to the **right edge** of the icon (`right: 0; transform: none`) instead of being centered.
- On desktop (`≥ 992px`), the existing centered positioning is kept unchanged.
- No changes to the tooltip text, styling, animation, or any other header element.

## Capabilities

### New Capabilities
- `header-tooltip-responsive-alignment`: Responsive alignment strategy for the header heart icon tooltip — centered on desktop, right-aligned on tablet and mobile.

### Modified Capabilities
<!-- No existing spec-level behavior changes -->

## Impact

- **File**: `src/features/favorites/header-fav.css` — add a responsive `@media` block targeting the `.tongle::after` rule.
- No JavaScript changes required.
- No breaking changes.
- No dependency changes.
