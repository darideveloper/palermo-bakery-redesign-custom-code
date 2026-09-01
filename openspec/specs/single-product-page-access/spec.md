# single-product-page-access Specification

## Purpose

This specification defines the behavior for direct access to WooCommerce single product pages on the Palermo Bakery site. It removes the previous "always redirect to the shop archive" behavior so that clients can reach, link to, and share individual product pages, while leaving the rendered single-product template to the theme's default.

## Requirements

### Requirement: Direct single-product URLs render the product page

The system SHALL render the WooCommerce single-product template when a user requests a valid product permalink directly (for example `/product/strawberry-dream/`). The page SHALL NOT be redirected to the shop archive or to any other URL.

#### Scenario: User visits a product URL directly
- **WHEN** a user navigates to a valid WooCommerce product permalink
- **THEN** the response SHALL be HTTP 200 with the rendered single-product template
- **AND** the browser SHALL remain on the product URL (no redirect)

#### Scenario: Single-product search result links to the product page
- **WHEN** a user performs a site search that returns exactly one product
- **THEN** the search result link SHALL navigate to the product page (not the shop archive)
- **AND** WooCommerce's standard single-result redirect behavior SHALL apply

### Requirement: Gallery, favorites, and other custom pages are unaffected

The system SHALL NOT change the rendering or behavior of any page other than the WooCommerce single-product template. The shop archive, category archives, tag archives, the favorites page, the order-cake page, and all other pages SHALL continue to render exactly as they do today.

#### Scenario: Shop archive still renders the gallery grid
- **WHEN** a user navigates to `/cake-gallery/`
- **THEN** the gallery grid SHALL render as before, with image-only cards, the prettyPhoto lightbox behavior, and all custom features (favorites, share, category filter) intact

#### Scenario: Category and tag archives still render
- **WHEN** a user navigates to a product category or tag archive
- **THEN** those pages SHALL render exactly as they do today (no extra redirects, no template changes)

### Requirement: No single-product-page-related redirects remain

The system SHALL NOT register any `template_redirect` hook that sends `is_product()` requests to a different URL, and SHALL NOT filter `woocommerce_redirect_single_search_result` to `__return_false`. The previous custom hook `dari_developer_disable_product_pages` and the related filter SHALL be removed from both `src/core/functions.php` and `src/core/functions_prod.php`.

#### Scenario: No is_product() redirect is registered
- **WHEN** WordPress evaluates `template_redirect` actions
- **THEN** no registered callback SHALL redirect requests where `is_product()` is true

#### Scenario: Single-result search redirect is enabled
- **WHEN** a search returns exactly one product
- **THEN** WooCommerce's default behavior of redirecting to that product's page SHALL apply (the previous `__return_false` override is removed)

### Requirement: Product pages with an empty description render without error

The system SHALL render the WooCommerce single-product template for every valid product, including products whose description (`post_content`) is empty, WITHOUT returning an HTTP 500 or a WordPress fatal-error page. The system SHALL remove any product data tab that is registered in the `woocommerce_product_tabs` filter without a valid `callback`, so the theme's tab template never invokes an invalid callback. Tabs that have a valid callback SHALL remain unchanged.

#### Scenario: Empty-description product page returns 200 and renders
- **WHEN** a user navigates to a valid product permalink whose description is empty (for example `sesame-street-smash-cake`)
- **THEN** the response SHALL be HTTP 200 (not 500)
- **AND** the single-product template SHALL render (title, gallery, summary, and the empty description area) without a WordPress fatal-error body

#### Scenario: Callback-less tab is dropped before rendering
- **WHEN** the `woocommerce_product_tabs` filter returns a tab entry that has no `callback` key, or whose `callback` is not callable
- **THEN** the system SHALL remove that tab from the array before the theme template iterates over tabs
- **AND** the theme SHALL NOT call `call_user_func` with an invalid callback

#### Scenario: Non-array tabs filter value is tolerated
- **WHEN** a callback returns a non-array value from the `woocommerce_product_tabs` filter chain
- **THEN** the system SHALL not throw a fatal error and SHALL leave the tabs behavior as-is (no invalid tab removal attempted on a non-array)

#### Scenario: Valid tabs remain unchanged
- **WHEN** a product has a description (for example `tuxedo-birthday-cake-2`) or otherwise registers tabs with valid callbacks (description, additional information, reviews)
- **THEN** those tabs SHALL render exactly as before
- **AND** the product page SHALL continue to return HTTP 200