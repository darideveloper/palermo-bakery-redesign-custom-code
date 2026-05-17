## ADDED Requirements

### Requirement: Save-shared button hides after a cake is saved to favorites
The system SHALL hide the `.save-shared-btn` button by fading it out and setting `display: none` after the user saves a shared cake to their favorites, instead of displaying permanent "❤️ Saved" overflow text inside the 40×40px button circle.

#### Scenario: Button fades out after saving
- **WHEN** the user clicks a `.save-shared-btn` on a shared cake card
- **AND** the cake is not already in the user's favorites list
- **THEN** the button SHALL fade its opacity to 0 over 0.3s and then be set to `display: none`
- **AND** no "Saved" text SHALL be visible on the card after the animation completes

#### Scenario: Button does not re-appear on re-render
- **WHEN** the favorites grid below is re-rendered after saving (via `renderUserFavoritesGrid()`)
- **THEN** the shared section card SHALL retain the hidden button state (button remains hidden)

#### Scenario: Already-saved cake button is not clickable again
- **WHEN** the user clicks a `.save-shared-btn` for a cake already in their favorites
- **THEN** the click handler SHALL take no action (existing `if (!favs.includes(productId))` guard), and the button SHALL remain in its current visible state unchanged
