## Context

The Favorites page uses a masonry-style grid to display saved cakes. The current implementation uses CSS `column-count`, which is problematic for small item counts because it prioritizes balancing the height of columns over filling them sequentially. This leads to empty space on the right side of the container when the items aren't a multiple of 3 (or the current column count).

## Goals / Non-Goals

**Goals:**
- Fix the column balancing issue so items always populate from left-to-right.
- Maintain the visual aesthetic of the product cards.
- Ensure the grid is responsive across all devices.

**Non-Goals:**
- Changing the functionality of favoriting/unfavoriting.
- Modifying the lightbox or sharing logic.

## Decisions

- **CSS Grid Implementation**: We will switch the `.cake-masonry-grid` container to use `display: grid`.
- **Responsive Layout**:
    - Desktop (992px+): `grid-template-columns: repeat(3, 1fr)`
    - Tablet (576px-991px): `grid-template-columns: repeat(2, 1fr)`
    - Mobile (<576px): `grid-template-columns: 1fr`
- **Gap Management**: We will use `grid-gap` (or `gap`) to maintain the 20px spacing previously handled by `column-gap`.
- **Vertical Alignment**: We will use the default `align-items: stretch`. This ensures that all cards in a single row have the same height, even if their labels have different lengths, resulting in a cleaner and more professional gallery appearance.

## Risks / Trade-offs

- **Risk**: Loss of the true "masonry" (offset vertical heights) if labels vary significantly in length.
  **Mitigation**: Given the current design uses uniform image aspects, a standard grid actually looks more professional and organized than an offset masonry for this specific use case.
