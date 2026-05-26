## Why

The current Favorites grid uses CSS `column-count`, which causes items to be distributed in a way that prioritizes column height balancing over left-to-right filling. This results in empty columns and broken layouts when the number of items (e.g., 4 or 7) doesn't perfectly divide into the column count.

## What Changes

- Replace the CSS `column-count` implementation with a CSS `grid` implementation for the `.cake-masonry-grid` class.
- Ensure items always fill columns from left to right.
- Maintain responsive behavior (3 columns on desktop, 2 on tablet, 1 on mobile).
- Align the Favorites grid visual style with the standard WooCommerce product gallery.

## Capabilities

### New Capabilities
- `grid-based-favorites-layout`: Transition the Favorites gallery from balancing columns to a standard filling grid.

### Modified Capabilities
- `favorites-heart-button`: (Requirement remains same, but implementation context in CSS changes slightly).

## Impact

- `src/features/favorites/favorite-page.css`: Major update to the grid container and item layout styles.
- Favorites page UI consistency across different item counts.
