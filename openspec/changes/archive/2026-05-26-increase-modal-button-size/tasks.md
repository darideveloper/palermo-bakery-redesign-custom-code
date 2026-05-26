## 1. CSS Sizing Adjustments

- [x] 1.1 In `src/features/gallery/product-gallery.css`, update `#lightbox-share-btn` to have `width: 56px`, `height: 56px`, and `font-size: 28px`.
- [x] 1.2 In `src/features/gallery/product-gallery.css`, update `#lightbox-fav-btn` to have `width: 56px`, `height: 56px`, and `font-size: 28px`.

## 2. CSS Tooltip Implementation

- [x] 2.1 Add `position: relative !important;` to `#lightbox-share-btn` in `src/features/gallery/product-gallery.css`.
- [x] 2.2 Create a `#lightbox-share-btn::after` block with the tooltip styling (background, color, padding, border-radius, absolute positioning).
- [x] 2.3 Set `opacity: 0` and `visibility: hidden` by default on the tooltip.
- [x] 2.4 Add the `#lightbox-share-btn:hover::after` state to change opacity to 1 and visibility to visible.
