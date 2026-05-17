## MODIFIED Requirements

### Requirement: Favorites page image links are configured for prettyPhoto
The system SHALL render each `.masonry-item` image link on the favorites page with `href` pointing to the full-resolution product image URL, a `data-rel="prettyPhoto[fav-gallery]"` attribute, a `title` attribute containing the product name, and a `data-product-id` attribute containing the WooCommerce product ID.

#### Scenario: Link attributes are present after AJAX render
- **WHEN** the favorites grid is rendered via AJAX (`ajax_render_favorite_products`)
- **THEN** each `<a>` inside `.masonry-item` SHALL have `href` equal to the WordPress `full`-size image URL, `data-rel="prettyPhoto[fav-gallery]"`, `title` equal to the product name, and `data-product-id` equal to the product ID

#### Scenario: Clicking a favorites card opens the lightbox
- **WHEN** a user clicks a cake image on the favorites page
- **THEN** the prettyPhoto lightbox SHALL open displaying the full-resolution product image
