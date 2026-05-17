## Context

The Palermo Bakery WordPress site has two product grid implementations:

1. **WooCommerce Product Grid** (`product-gallery.css`): Main shop grid with polished visual effects
   - Cards: rounded corners, hover scale(1.05), z-index elevation
   - Images: 1:1 aspect-ratio, object-fit cover, hover scale(1.1)
   - Buttons: Custom heart positioned top-right
   - Shadow: 0 4px 15px base, 0 10px 25px on hover

2. **Favorites Grid** (`favorite-page.css`): User-saved cakes page
   - Cards: rounded corners, hover translateY(-5px)
   - Images: auto height, hover scale(1.03)
   - Buttons: Unstyled remove (✖) button
   - Shadow: missing on hover

The favorites grid HTML structure uses different class names (`.masonry-item`, `.masonry-label`, `.remove-fav-btn`) compared to WooCommerce (`.block-product-inner`, `.item-img-info`, `.my-custom-fav-btn`), but serves the same visual purpose.

## Goals / Non-Goals

**Goals:**
- Make favorites grid visually identical to WooCommerce product grid
- Maintain all existing functionality (add/remove favorites, shared cakes)
- No breaking changes to HTML structure or JavaScript behavior
- Responsive behavior maintained across all breakpoints

**Non-Goals:**
- Refactoring of HTML structure
- Adding new functionality (lightbox, category filters)
- Modifying JavaScript behavior
- Changes to WooCommerce grid (already complete)

## Decisions

**1. Modify existing `favorite-page.css` rather than create new file**

- **Rationale**: The favorites page already has its own dedicated CSS file. Adding new selectors there is cleaner than scattering styles across multiple files or duplicating code. It keeps all favorites-related styling in one maintainable location.

- **Alternative considered**: Duplicate all `product-gallery.css` styles with new selectors - rejected because it creates maintenance burden and confuses file purpose.

**2. Apply WooCommerce patterns to existing selectors**

- Rather than changing class names in HTML, we update CSS selectors to match existing HTML structure:
  - `.masonry-item` gets WooCommerce's `.block-product-inner` styles
  - `.masonry-item a` gets WooCommerce's `.item-img-info a` styles
  - `.remove-fav-btn` gets WooCommerce's `.my-custom-fav-btn` positioning

- **Rationale**: No HTML changes means lower risk and no need to modify PHP templates.

**3. Preserve distinctive favorites functionality**

- The "remove" (✖) button replaces the heart button - this is intentional since favorites need a remove action
- The masonry layout uses CSS columns (not flex/grid) - this is appropriate for variable-height masonry

- **Rationale**: Functional requirements differ; visual presentation should align while maintaining UX patterns.

## Risks / Trade-offs

**Risk: Hover effects may conflict with button positioning**
→ **Mitigation**: Use `position: relative` on `.masonry-item` container and `position: absolute` on button to ensure button stays in corner regardless of hover transform.

**Risk: Image aspect-ratio may distort images**
→ **Mitigation**: Use `object-fit: cover` to maintain aspect ratio while filling container - same approach as WooCommerce grid.

**Risk: Mobile responsiveness differences**
→ **Mitigation**: Preserve existing responsive breakpoints (2 columns at 991px, 1 column at 575px) which already match WooCommerce patterns.

## Migration Plan

1. Update `favorite-page.css` with new/enhanced styles
2. Test on staging environment:
   - Desktop: verify hover effects and button positioning
   - Mobile: verify grid responsiveness
   - Shared section: verify save button styling
3. Deploy to production (CSS-only change, no PHP/JS)
4. No rollback needed - CSS changes are non-breaking

## Open Questions

None - the change is straightforward with clear requirements from the existing WooCommerce grid implementation.