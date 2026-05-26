## MODIFIED Requirements

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