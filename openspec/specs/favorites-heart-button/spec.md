## ADDED Requirements

### Requirement: Heart button replaces remove button on user-owned favorite cards
The system SHALL render a `.my-custom-fav-btn` heart button (❤️) on each user-owned favorite card instead of the `.remove-fav-btn` (✖) button. The heart button SHALL carry a `data-product-id` attribute containing the WooCommerce product ID.

#### Scenario: Heart button present on render
- **WHEN** the user's favorites grid is rendered via AJAX (`ajax_render_favorite_products` with `is_shared = false`)
- **THEN** each `.masonry-item` SHALL contain a `.my-custom-fav-btn` button showing ❤️ with `data-product-id` set to the product ID
- **AND** no `.remove-fav-btn` button SHALL be present

#### Scenario: Remove button is unchanged on shared section
- **WHEN** the shared-favorites grid is rendered via AJAX (`ajax_render_favorite_products` with `is_shared = true`)
- **THEN** each `.masonry-item` SHALL still contain a `.save-shared-btn` button (unaffected by this change)

### Requirement: Clicking the heart button on a favorites card removes it from favorites and fades the card out
The system SHALL toggle the cake's favorite state when the heart button is clicked on a `.masonry-item` card. Since the card is already in the favorites list, this SHALL always result in removal. The card SHALL fade out and be removed from the DOM.

#### Scenario: User clicks heart on a favorites card
- **WHEN** the user clicks the ❤️ heart button on a `.masonry-item` favorites card
- **THEN** the product ID SHALL be removed from the session state and synced to the server via AJAX
- **AND** the `.masonry-item` SHALL fade out (opacity → 0 over 0.3s) and then be removed from the DOM
- **AND** the header favorites counter SHALL decrement by 1

#### Scenario: Empty state after last card removed
- **WHEN** the user removes the last cake from their favorites by clicking its heart button
- **THEN** after the card is removed from the DOM, the empty-state message SHALL be displayed

### Requirement: Heart button visual style matches the gallery heart button exactly
The system SHALL apply the existing `.my-custom-fav-btn` CSS rules to the favorites-page heart button without any additional styles, producing a 40×40px circular white button positioned at the top-right corner of the card (15px offset).

#### Scenario: Button appearance on favorites page
- **WHEN** the favorites grid is displayed
- **THEN** each card's heart button SHALL appear as a 40×40px circle with `rgba(255, 255, 255, 0.9)` background, positioned 15px from the top-right corner of the card image
- **AND** the button SHALL be correctly positioned relative to the `.masonry-item` within the new CSS Grid layout

#### Scenario: Hover interaction
- **WHEN** the user hovers over a favorites card heart button
- **THEN** the button SHALL scale to 1.15× with a white solid background

#### Scenario: No `.remove-fav-btn` styles remain in use
- **WHEN** the favorites page is loaded
- **THEN** no element on the page SHALL carry the class `.remove-fav-btn`
- **AND** the `.remove-fav-btn` CSS rule SHALL be removed from the stylesheet (only `.save-shared-btn` remains in that selector)

### Requirement: Shared favorite buttons sync with user state and support toggling
The system SHALL synchronize the `.save-shared-btn` elements with the user's current favorites list on page load and allow the user to toggle (add or remove) these items. Only authenticated users can toggle shared items.

#### Scenario: Shared button state sync on load
- **WHEN** the favorites page loads a shared link and renders the shared cakes grid
- **THEN** the `.save-shared-btn` for any cake already in the user's favorites SHALL visually display as saved (e.g., ❤️ instead of 🤍)

#### Scenario: User removes a saved shared cake
- **WHEN** the authenticated user clicks a `.save-shared-btn` on a shared cake that is already in their favorites
- **THEN** the product ID SHALL be removed from the session state and synced to the server
- **AND** the `.save-shared-btn` SHALL revert to its unsaved visual state (🤍)
- **AND** the user's own favorites grid below SHALL update to reflect the removal

#### Scenario: User saves a new shared cake
- **WHEN** the authenticated user clicks a `.save-shared-btn` on a shared cake that is NOT in their favorites
- **THEN** the product ID SHALL be added to the session state and synced to the server
- **AND** the `.save-shared-btn` SHALL update to its saved visual state (❤️)
- **AND** the user's own favorites grid below SHALL update to include the new cake
