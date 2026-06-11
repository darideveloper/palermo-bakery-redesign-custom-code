## 1. CSS Implementation

- [x] 1.1 Open `src/features/favorites/header-fav.css`
- [x] 1.2 Add a `@media (max-width: 991px)` block after the existing `.tongle::after` rule that overrides `left`, `right`, and `transform` to right-align the tooltip

## 2. Verification

- [x] 2.1 Test on a mobile viewport (≤ 767px) — confirm tooltip is right-aligned and fully visible within the viewport
- [x] 2.2 Test on a tablet viewport (768px–991px) — confirm tooltip is right-aligned with no horizontal overflow
- [x] 2.3 Test on a desktop viewport (≥ 992px) — confirm tooltip remains centered over the icon, unchanged
