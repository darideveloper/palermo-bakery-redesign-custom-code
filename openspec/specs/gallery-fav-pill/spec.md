## ADDED Requirements

### Requirement: Favorite Cakes pill appears as the first item in the gallery filter row
The system SHALL inject a "♥ Favorite Cakes" pill as the first `<li>` element inside `#woocommerce_product_categories-3 ul.product-categories` on the cake gallery page. The pill SHALL redirect the user to `/favorite-cakes` when clicked.

#### Scenario: Pill is present at page load
- **WHEN** any user (guest or logged-in) loads the cake gallery page
- **THEN** a "Favorite Cakes" pill SHALL appear as the first item in the category filter row

#### Scenario: Pill click redirects to favorites page
- **WHEN** a user clicks the "Favorite Cakes" pill
- **THEN** the browser SHALL navigate to `/favorite-cakes`

#### Scenario: Pill triggers loading spinner
- **WHEN** a user clicks the "Favorite Cakes" pill (without Ctrl/Cmd held)
- **THEN** the existing `#custom-category-loader` full-page loading overlay SHALL be activated, consistent with clicking a category filter pill

#### Scenario: Ctrl/Cmd+click opens in new tab without spinner
- **WHEN** a user Ctrl+clicks or Cmd+clicks the "Favorite Cakes" pill
- **THEN** the link SHALL open in a new tab and the loading spinner SHALL NOT be activated

### Requirement: Favorite Cakes pill matches the existing filter pill style with a heart icon
The injected pill SHALL inherit all base pill styles (padding, border-radius, border, font-size, color, transitions) from the existing `ul.product-categories li a` CSS rules. Additionally, it SHALL display a heart icon (♥) before the label text to visually distinguish it as a redirect shortcut rather than a category filter.

#### Scenario: Pill renders with heart icon
- **WHEN** the Favorite Cakes pill is rendered
- **THEN** the pill label SHALL read "♥ Favorite Cakes" (heart character followed by a space and the text)

#### Scenario: Pill hover state matches existing pills
- **WHEN** a user hovers over the Favorite Cakes pill
- **THEN** the pill SHALL display the same hover style as other category pills (background color change, scale transform, underline animation)

#### Scenario: Pill is never styled as "current/active"
- **WHEN** any gallery page is loaded (including `/favorite-cakes` itself)
- **THEN** the Favorite Cakes pill SHALL NOT receive the `current-cat` active style, since it is a redirect link, not a WooCommerce category filter
