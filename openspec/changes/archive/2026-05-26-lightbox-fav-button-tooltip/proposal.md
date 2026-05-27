## Why

The lightbox share button already shows a "Copy Link" tooltip on hover, but the favorite button next to it has no hover label. Users unfamiliar with the heart icon may not immediately understand what it does. Adding an "Add to Favorites" tooltip to the fav button will clarify its purpose and create visual consistency between the two lightbox action buttons.

## What Changes

- Add a CSS-only `::after` pseudo-element tooltip to `#lightbox-fav-btn` that displays "Add to Favorites" on hover, matching the exact styling and reveal pattern of the existing `#lightbox-share-btn::after` "Copy Link" tooltip.
- Add `position: relative !important` to `#lightbox-fav-btn` so the tooltip anchors correctly above the button.
- Update the `gallery-modal-ui` spec to include the new favorite button hover state requirement.

## Capabilities

### New Capabilities

(none)

### Modified Capabilities

- `gallery-modal-ui`: Add requirement for "Modal Favorite Button Hover State" — tooltip displaying "Add to Favorites" when hovering the fav button inside the lightbox.

## Impact

- **CSS**: `src/features/gallery/product-gallery.css` — two new rule blocks (`#lightbox-fav-btn::after` and `#lightbox-fav-btn:hover::after`) plus `position: relative !important` on `#lightbox-fav-btn`.
- **Spec**: `openspec/specs/gallery-modal-ui/spec.md` — new requirement section.
- **No JS changes** — the tooltip is purely CSS, identical in approach to the share button tooltip.