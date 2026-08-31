## ADDED Requirements

### Requirement: Image anchor carries the product permalink as a data attribute

The PHP output buffer that rewrites gallery `<img>` tags SHALL also emit a `data-product-permalink` attribute on the `a.product-image` element wrapping each gallery image. The attribute value SHALL be the absolute WordPress permalink of the product being displayed on that card. The permalink URL SHALL be built by calling `get_permalink($post->ID)` where `$post->ID` is the WooCommerce product ID for the card. The product ID SHALL be sourced from the YITH wishlist element's `data-fragment-ref` attribute inside the same card (the same source the existing fav-button JS uses). If the YITH element is missing or has no `data-fragment-ref` on a given card, the buffer SHALL skip that card and the rest of the page SHALL be unaffected. This attribute exists so client-side scripts can resolve the product URL without a new AJAX endpoint.

#### Scenario: Anchor carries the permalink after buffer rewrite
- **WHEN** the `template_redirect` output buffer processes a gallery card whose image anchor is `a.product-image` and whose card contains a YITH element with a `data-fragment-ref`
- **THEN** the resulting `a.product-image` tag SHALL include a `data-product-permalink="<absolute URL>"` attribute
- **AND** the URL SHALL be the output of `get_permalink($productId)` where `$productId` is the YITH `data-fragment-ref` value
- **AND** the URL SHALL point to the WordPress product permalink, not a thumbnail or attachment URL

#### Scenario: Permalink attribute is set for every gallery card with a YITH element
- **WHEN** the gallery page is rendered with N product cards, each containing a YITH element
- **THEN** every `a.product-image` inside `#sns_woo_list` SHALL have a `data-product-permalink` attribute
- **AND** no card with a YITH element SHALL be missing the attribute

#### Scenario: Card without YITH is skipped, rest of page unaffected
- **WHEN** a gallery card does not contain a YITH wishlist element (or its `data-fragment-ref` is empty)
- **THEN** that card's `a.product-image` SHALL NOT receive the `data-product-permalink` attribute
- **AND** the buffer SHALL continue processing the remaining cards normally
- **AND** the existing image-rewrite behavior for that card (`src`, `data-original`, `data-lightbox-src`, `loading`, `decoding`, class, `srcset`) SHALL remain intact
