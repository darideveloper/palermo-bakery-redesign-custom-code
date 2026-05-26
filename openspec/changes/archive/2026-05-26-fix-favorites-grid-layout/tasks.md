## 1. Grid Refactor

- [x] 1.1 Update `.cake-masonry-grid` in `src/features/favorites/favorite-page.css` to use `display: grid`.
- [x] 1.2 Remove `column-count` and `column-gap` from `.cake-masonry-grid`.
- [x] 1.3 Add `grid-template-columns: repeat(3, 1fr)` and `gap: 20px` to `.cake-masonry-grid` for desktop.
- [x] 1.4 Update responsive breakpoints for `.cake-masonry-grid` to use `grid-template-columns: repeat(2, 1fr)` for tablet and `grid-template-columns: 1fr` for mobile.

## 2. Card Styling Alignment

- [x] 2.1 Remove `margin-bottom: 20px` and `break-inside: avoid` from `.masonry-item`.
- [x] 2.2 Add `height: 100%` and `display: flex; flex-direction: column;` to `.masonry-item` to ensure it stretches to full grid cell height and allows labels to align correctly.
- [x] 2.3 Add `flex-grow: 1;` to `.masonry-label` (if needed) to ensure the white background fills the bottom of the card in a stretched row.
- [x] 2.4 Verify card image `aspect-ratio` and hover effects remain consistent with the new grid layout.
- [x] 2.5 Check heart button positioning within the grid items to ensure it remains at the top-right corner.

## 3. Verification

- [x] 3.1 Verify the layout for 4 items correctly fills the first row and starts the second row in the first column.
- [x] 3.2 Verify the layout for 7 items fills two complete rows and starts the third row.
- [x] 3.3 Verify responsiveness by checking the layout at different viewport widths.
