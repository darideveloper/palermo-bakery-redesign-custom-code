# lightbox-title-permalink-link Specification

## Purpose

This specification defines how the product name displayed inside the prettyPhoto lightbox (the auto-generated `.ppt` title element) becomes a clickable link to the corresponding WooCommerce product permalink. The link gives visitors a way to reach the full product page directly from the lightbox, while the rest of the lightbox UI (fav button, share button, navigation arrows) is unchanged.

## Requirements

### Requirement: Lightbox title renders as a permalink link

The system SHALL convert the prettyPhoto `.ppt` element (the auto-generated title inside the lightbox popup) into an anchor element whose `href` is the WooCommerce product permalink for the cake currently displayed, and whose `target` is `"_blank"` so the link opens in a new browser tab. The anchor text SHALL remain the product name (e.g. "Tuxedo Birthday Cake"). The anchor SHALL be styled to match the original `.ppt` appearance (white, bold, 17px, block-displayed) via a CSS rule targeting `a.ppt` inside the lightbox, because WooCommerce's `prettyPhoto.css` only targets `div.ppt` and the converted `<a>` inherits default link styling otherwise.

#### Scenario: Title is a clickable link when the lightbox opens
- **WHEN** a user opens the prettyPhoto lightbox by clicking a cake image
- **THEN** the visible title inside the lightbox SHALL be a clickable anchor element
- **AND** the anchor's `href` SHALL resolve to that product's WooCommerce permalink
- **AND** the anchor's `target` SHALL be `"_blank"`
- **AND** the anchor text SHALL match the product name

#### Scenario: Title is a link from any lightbox source on this site
- **WHEN** the lightbox opens from a card on `/cake-gallery/` OR from a card on the favorites page OR from any other context on this site where a product permalink can be resolved
- **THEN** the `.ppt` element SHALL be converted to a permalink anchor as above
- **AND** no separate "View product" button SHALL be added to the lightbox button row

#### Scenario: Title remains as plain text when permalink cannot be resolved
- **WHEN** the lightbox opens and the JS cannot resolve a permalink for the current cake through any of the available paths
- **THEN** the `.ppt` element SHALL remain as plain text
- **AND** no anchor with empty `href=""` or `href="#"` SHALL be emitted

### Requirement: Title link updates on lightbox navigation

The system SHALL update the `.ppt` anchor's `href` and text to match the newly displayed cake whenever the user navigates between images inside the lightbox using the prev/next arrows or keyboard arrows.

#### Scenario: User navigates to a different cake
- **WHEN** the user clicks the next or previous arrow inside the lightbox
- **THEN** the `.ppt` anchor's text and `href` SHALL update to reflect the newly displayed cake
- **AND** clicking the updated title SHALL open the correct product page

#### Scenario: User navigates with keyboard arrows
- **WHEN** the user presses the left or right keyboard arrow while the lightbox is open
- **THEN** the `.ppt` anchor's text and `href` SHALL update to reflect the newly displayed cake

### Requirement: Product permalink is resolved via image-src match first, then map lookup, then plain text

The system SHALL resolve the product permalink for the currently displayed cake using this fallback order:
1. **Image-src match:** the JS compares the current lightbox image's `src` to the `src` of every `a.product-image[data-product-permalink]` on the page; if a match is found, that element's `data-product-permalink` is used.
2. **Map lookup:** the JS looks up the current product ID in a `productId → permalink` map. The map is built during page load on `/cake-gallery/` only, by scanning all `a.product-image[data-product-permalink]` on the page and keying each entry by the corresponding product's YITH `data-fragment-ref`. On other pages the map is empty.
3. **Plain text:** if neither path yields a permalink, the `.ppt` element remains as plain text.

The PHP output buffer that rewrites gallery `<img>` tags SHALL populate `data-product-permalink` on the card anchor with the WordPress product permalink for the product being displayed. JS SHALL NOT introduce a new AJAX endpoint to resolve permalinks.

#### Scenario: Permalink resolved by matching the current lightbox image
- **WHEN** the lightbox is open and the JS resolves the permalink for the currently displayed cake
- **THEN** the JS SHALL first scan the page for `a.product-image[data-product-permalink]` whose `src` matches the current lightbox image's `src`
- **AND** if a match is found, the resulting `.ppt` anchor's `href` SHALL equal that element's `data-product-permalink` value

#### Scenario: Permalink resolved from the productId map as a fallback
- **WHEN** the image-src match fails to find a permalink
- **THEN** the JS SHALL look up the current product ID in the `productId → permalink` map
- **AND** the map SHALL be populated only on `/cake-gallery/`, by scanning all `a.product-image[data-product-permalink]` on the page
- **AND** the resulting `.ppt` anchor's `href` SHALL equal the looked-up permalink

#### Scenario: Map is empty on non-`/cake-gallery/` pages
- **WHEN** the page is not `/cake-gallery/` (for example the favorites page or a category archive)
- **THEN** the `productId → permalink` map SHALL be empty (no scan is performed)
- **AND** permalinks SHALL still resolve correctly when an `a.product-image[data-product-permalink]` is present on the page and matches by image-src

#### Scenario: Missing permalink leaves the title as plain text
- **WHEN** the JS cannot resolve a permalink for the current lightbox cake through any path
- **THEN** the `.ppt` element SHALL remain as plain text
- **AND** no anchor with empty `href=""` or `href="#"` SHALL be emitted

### Requirement: Existing lightbox behavior is preserved

The system SHALL NOT alter the prettyPhoto lightbox's existing behavior, including: the fav button, the share button, the prev/next arrow navigation, the close button, the keyboard arrow navigation, and the lightbox open/close lifecycle. Converting `.ppt` to an anchor SHALL be additive.

#### Scenario: Other lightbox buttons unaffected
- **WHEN** the `.ppt` is converted to an anchor
- **THEN** the fav button, share button, and the rest of the lightbox UI SHALL continue to function exactly as before
- **AND** the lightbox's open/close lifecycle SHALL be unchanged

#### Scenario: Title link click opens the product page in a new tab and the lightbox remains open
- **WHEN** the user clicks the title anchor (`<a target="_blank" href="<permalink>">`)
- **THEN** the browser SHALL open the product permalink in a new browser tab
- **AND** the original tab (with the lightbox still open) SHALL remain on the gallery
- **AND** the lightbox SHALL continue to function normally (close button, prev/next arrows, fav/share buttons) without the click interfering