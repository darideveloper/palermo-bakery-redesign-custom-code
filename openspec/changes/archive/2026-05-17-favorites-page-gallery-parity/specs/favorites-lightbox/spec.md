## ADDED Requirements

### Requirement: Favorites page image links are configured for prettyPhoto
The system SHALL render each `.masonry-item` image link on the favorites page with `href` pointing to the full-resolution product image URL, a `data-rel="prettyPhoto[fav-gallery]"` attribute, a `title` attribute containing the product name, and a `data-product-id` attribute containing the WooCommerce product ID.

#### Scenario: Link attributes are present after AJAX render
- **WHEN** the favorites grid is rendered via AJAX (`ajax_render_favorite_products`)
- **THEN** each `<a>` inside `.masonry-item` SHALL have `href` equal to the WordPress `large`-size image URL, `data-rel="prettyPhoto[fav-gallery]"`, `title` equal to the product name, and `data-product-id` equal to the product ID

#### Scenario: Clicking a favorites card opens the lightbox
- **WHEN** a user clicks a cake image on the favorites page
- **THEN** the prettyPhoto lightbox SHALL open displaying the full-resolution product image

### Requirement: prettyPhoto is bound to favorites grid links after each AJAX render
The system SHALL initialize prettyPhoto on all `a[data-rel^='prettyPhoto']` links inside the favorites grid container immediately after `renderGrid()` resolves, using the same options as the cake gallery.

#### Scenario: Lightbox bound after first render
- **WHEN** `renderGrid()` resolves and injects HTML into `#favorite-cakes-list`
- **THEN** `window.palermoInitLightbox` SHALL be called with the grid container as scope, binding prettyPhoto to all unbound links

#### Scenario: Lightbox bound after re-render
- **WHEN** the favorites grid is re-rendered (e.g., after a cake is removed and the list refreshes)
- **THEN** newly injected links SHALL have prettyPhoto bound without duplicating bindings on existing links (`.pp-bound` guard)

### Requirement: Lightbox navigation is scoped to the favorites grid
The system SHALL group all favorites page lightbox images under the `prettyPhoto[fav-gallery]` group so that navigation arrows cycle only within the favorites grid, not across gallery images on other pages.

#### Scenario: Arrow navigation stays within favorites
- **WHEN** the lightbox is open on the favorites page and the user clicks the next or previous arrow
- **THEN** the lightbox navigates only between cakes in the favorites grid

### Requirement: Lightbox fav button state updates correctly during navigation on the favorites page
The system SHALL update `currentLightboxProductId` correctly when the user navigates between images inside the lightbox opened from the favorites page, so that the fav button reflects the correct state for each displayed cake.

#### Scenario: Navigate to next cake in lightbox (favorites page)
- **WHEN** the lightbox is open on the favorites page and the user clicks the next arrow
- **THEN** `getLightboxProductId()` SHALL resolve the new product ID from `link.dataset.productId` on the matching `<a>` tag
- **AND** `currentLightboxProductId` SHALL be updated to the new product ID
- **AND** the fav button SHALL display ❤️ or 🤍 matching the newly displayed cake's favorites status

#### Scenario: Navigate to previous cake in lightbox (favorites page)
- **WHEN** the lightbox is open on the favorites page and the user clicks the previous arrow
- **THEN** the fav button SHALL update to reflect the newly displayed cake's favorites status

### Requirement: All existing lightbox styles apply on the favorites page
The system SHALL apply all prettyPhoto UI customizations (close button, navigation arrows, overlay, mobile sizing) to the lightbox when opened from the favorites page, with no additional CSS required.

#### Scenario: Close button is styled correctly
- **WHEN** the lightbox opens from a favorites page card
- **THEN** the close button SHALL display as a circular element with a `×` character and a red hover state, identical to the gallery lightbox close button

#### Scenario: Mobile layout is correct
- **WHEN** the lightbox opens on a viewport narrower than 767px on the favorites page
- **THEN** the lightbox SHALL use 95% width and `max-height: 80vh`, matching the gallery mobile behavior
