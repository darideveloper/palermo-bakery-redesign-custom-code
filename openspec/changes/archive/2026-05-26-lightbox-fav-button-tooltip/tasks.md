## 1. CSS Tooltip Implementation

- [x] 1.1 In `src/features/gallery/product-gallery.css`, change `#lightbox-fav-btn` `position: static !important` to `position: relative !important`, keeping all other properties unchanged
- [x] 1.2 In `src/features/gallery/product-gallery.css`, add a `#lightbox-fav-btn::after` block with `content: "Add to Favorites"` and styling identical to `#lightbox-share-btn::after` (absolute positioning above the button, dark background, opacity/visibility hidden by default)
- [x] 1.3 In `src/features/gallery/product-gallery.css`, add a `#lightbox-fav-btn:hover::after` block that reveals the tooltip with `opacity: 1` and `visibility: visible`, matching `#lightbox-share-btn:hover::after`

## 2. Spec Update

- [x] 2.1 In `openspec/specs/gallery-modal-ui/spec.md`, add the "Modal Favorite Button Hover State" requirement with its scenarios